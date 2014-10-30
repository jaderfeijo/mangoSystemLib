<?hh // strict

/*
 * Copyright (c) 2011 Movinpixel Ltd. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. Neither the name of the company nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY MOVINPIXEL AND ITS CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL MOVINPIXEL OR ITS CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */
	
/**
 * This class provides a View that is capable of rendering a Managed Object
 * as an XML element
 *
 * The functionality of this class is complemented by the MXMLEntityCollectionView
 * class.
 *
 * By using both classes you are able to create an XML representation of a
 * collection of Managed Objects
 *
 * The results can then be returned to the client, or stored in their serialised
 * form
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MXMLManagedObjectView extends MXMLElementView {
	
	protected MManagedObject $_managedObject;
	protected MMutableDictionary<MString, MXMLManagedObjectViewDataProvider> $_dynamicFields;
	protected MMutableDictionary<MString, MXMLElementView> $_dynamicFieldViews;
	
	/**
	 * Contructs a new XML Managed Object View that represents the object specified in
	 * XML format
	 *
	 * @param MManagedObject $managedObject The Managed Object to be represented as
	 * an XML Element View
	 *
	 * @return MXMLManagedObjectView The new XML Element View instance
	 */
	public function __construct(MManagedObject $managedObject) {
		parent::__construct($managedObject->entity()->name());
		
		$this->_managedObject = $managedObject;
		$this->_dynamicFields = new MMutableDictionary();
		$this->_dynamicFieldViews = new MMutableDictionary();
		
		$this->setValueForProperty(S("objectID"), S((string)$this->managedObject()->objectID()));
		if (!$managedObject->isFault()) {
			foreach ($managedObject->entity()->attributes()->toArray() as $attribute) {
				if ($attribute instanceof MEntityDescriptionProperty) {
					$object = $managedObject->objectForAttribute($attribute);
					$propertyElement = new MXMLElementView($attribute->name(), ($object ? $object->toString() : S("")));
					$propertyElement->setValueForProperty(S("type"), S($attribute->type()));
					$this->addSubview($propertyElement);
				} else if ($attribute instanceof MEntityDescriptionRelationship) {
					$relationshipElement = new MXMLElementView($attribute->name());
					$relationshipElement->setValueForProperty(S("type"), $attribute->type());
					$relationshipValues = $managedObject->objectForAttribute($attribute);
					if (!$relationshipValues instanceof MArray) {
						$relationshipValues = A(array($relationshipValues));
					}
					foreach ($relationshipValues->toArray() as $object) {
						$objectElement = new MXMLElementView($attribute->singular());
						$objectElement->setValueForProperty(S("objectID"), Sf("%d", $object->objectID()));
						$relationshipElement->addSubview($objectElement);
					}
				} else {
					throw new MManagedObjectException($managedObject, S("Unsupported Attribute Type"));
				}
			}
		}
	}
	
	/******************** Methods ********************/
	
	/**
	 * 
	 * @param MString $fieldName
	 * @param MXMLManagedObjectViewDataProvider $dataProvider
	 * 
	 * @return void
	 */
	public function addDynamicField(MString $fieldName, MXMLManagedObjectViewDataProvider $dataProvider) : void {
		$this->_dynamicFields->setObjectForKey($fieldName, $dataProvider);
		$dataType = $dataProvider->dataTypeForFieldWithName($this->managedObject(), $fieldName);
		$data = $dataProvider->dataForFieldWithName($this->managedObject(), $fieldName);
		$fieldElement = new MXMLElementView($fieldName, ($data ? $data->toString() : S("")));
		$fieldElement->setValueForProperty(S("type"), $dataType);
		$this->addSubview($fieldElement);
		$this->_dynamicFieldViews->setObjectForKey($fieldName, $fieldElement);
	}
	
	/**
	 * 
	 * @param MString $fieldName
	 * 
	 * @return void
	 */
	public function removeDynamicField(MString $fieldName) : void {
		$view = $this->_dynamicFieldViews->objectForKey($fieldName);
		if ($view !== null) {
			$this->removeSubview($view);
			$this->_dynamicFieldViews->removeObjectForKey($fieldName);
			$this->_dynamicFields->removeObjectForKey($fieldName);
		}
	}
	
	/******************** Properties ********************/
	
	/**
	 * 
	 * Returns the Managed Object this XML Managed Object View represents
	 *
	 * @return MManagedObject The Managed Object this XML View represents
	 */
	public function managedObject() : MManagedObject {
		return $this->_managedObject;
	}
	
	/**
	 * 
	 * @return MDictionary
	 */
	public function dynamicFields() : MDictionary<MString, MXMLManagedObjectViewDataProvider> {
		return $this->_dynamicFields;
	}
}

