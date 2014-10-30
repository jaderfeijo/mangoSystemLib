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
 * Provides a description of an entity and its properties and relationships
 *
 * This class stores all the information necessary to describe an Entity
 * with all of its properties and relationships
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MEntityDescription extends MObject {
	
	/******************** Static Methods ********************/
	
	/**
	 * Returns the entity instance for the given name inside the specified context
	 *
	 * This static method searches the specified context for an entity with the given
	 * name and returns it.
	 *
	 * @see MManagedObjectContext
	 *
	 * @static
	 *
	 * @param MString $name The name of the entity you wish to retrieve from the context
	 * @param MManagedObjectContext $context The context to search for the entity
	 *
	 * @return MEntityDescription
	 */
	public static function entityWithName(MString $name, MManagedObjectContext $context) : ?MEntityDescription {
		return $context->persistentStoreCoordinator()->model()->entityWithName($name);
	}
	
	//
	// ************************************************************
	//
	
	protected MString $_name;
	protected MString $_plural;
	protected MString $_entityClassName;
	protected MMutableArray<MEntityDescriptionAttribute> $_attributes;
	
	/**
	 * Creates a new instance of an MEntityDescription object
	 *
	 * @example new MEntityDescription(S("Customer"), S("Customers"), S("com.myapp.Customer"));
	 *
	 * @see S()
	 *
	 * @param MString $name The entity name
	 * @param MString $plural The entity's pluralised name
	 * @param MString $entityClassName The fully qualified class name for the entity
	 *
	 * @return MEntityDescription The newly created instance of MEntityDescription
	 */
	public function __construct(MString $name, MString $plural, MString $entityClassName) {
		parent::__construct();
		
		$this->_name = $name;
		$this->_plural = $plural;
		$this->_entityClassName = $entityClassName;
		$this->_attributes = new MMutableArray();
	}
	
	/******************** Properties ********************/
	
	/**
	 * Sets the entity's name
	 *
	 * @param MString $name The new entity name
	 *
	 * @return void
	 */
	public function setName(MString $name) : void {
		$this->_name = $name;
	}
	
	/**
	 * Returns the entity name
	 *
	 * @return MString The entity name
	 */
	public function name() : MString {
		return $this->_name;
	}
	
	/**
	 * Sets the entity's plural name
	 *
	 * @param MString $plural The new pluralised name
	 *
	 * @return void
	 */
	public function setPlural(MString $plural) : void {
		$this->_plural = $plural;
	}
	
	/**
	 * Returns the entity's pluralised name
	 *
	 * @return MString The pluralised name
	 */
	public function plural() : MString {
		return $this->_plural;
	}
	
	/**
	 * Sets the entity's class name
	 *
	 * @param MString $className The fully qualified class name for this entity
	 *
	 * @return void
	 */
	public function setEntityClassName(MString $entityClassName) : void {
		$this->_entityClassName = $entityClassName;
	}
	
	/**
	 * Returns the fully qualified class name for this entity
	 *
	 * @return MString The fully qualified class name for this entity
	 */
	public function entityClassName() : MString {
		return $this->_entityClassName;
	}
	
	/******************** Methods ********************/
	
	/**
	 * Adds an attribute to this entity
	 *
	 * @param MEntityDescriptionAttribute $attribute The attribute to add to this entity
	 *
	 * @return void
	 */
	public function addAttribute(MEntityDescriptionAttribute $attribute) : void {
		$this->_attributes->addObject($attribute);
	}
	
	/**
	 * Removes an attribute from this entity
	 *
	 * @param MEntityDescriptionAttribute $attribute The attribute to remove from this entity
	 *
	 * @return void
	 */
	public function removeAttribute(MEntityDescriptionAttribute $attribute) : void {
		$this->_attributes->removeObject($attribute);
	}
	
	/**
	 * Returns an Array of all the attributes in this entity
	 *
	 * The Array returned contains instances of the MEntityDescriptionAttribute
	 * class which describe each attribute (properties and relationships) contained
	 * inside this entity
	 *
	 * @see MEntityDescriptionAttribute
	 *
	 * @return MArray<MEntityDescriptionAttribute> An array containing all the attributes in this entity
	 */
	public function attributes() : MArray<MEntityDescriptionAttribute> {
		return $this->_attributes;
	}
	
	/**
	 * Returns an Array of all the properties in this entity
	 *
	 * The Array returned contains instances of the MEntityDescriptionProperty
	 * class which describes each property contained inside this entity
	 *
	 * @see MEntityDescriptionProperty
	 *
	 * @return MArray<MEntityDescriptionProperty> An array containing all the properties in this entity
	 */
	public function properties() : MArray<MEntityDescriptionProperty> {
		$properties = new MMutableArray();
		foreach ($this->attributes()->toVector() as $attribute) {
			if ($attribute instanceof MEntityDescriptionProperty) {
				$properties->addObject($attribute);
			}
		}
		return $properties;
	}
	
	/**
	 * Returns an Array of all the relationships in this entity
	 *
	 * The Array returned contains instances of the MEntityDescriptionRelationship
	 * class which describes each relationship contained inside this entity
	 *
	 * @see MEntityDescriptionRelationship
	 *
	 * @return MArray<MEntityDescriptionRelationship> An array containing all the relationships in this entity
	 */
	public function relationships() : MArray<MEntityDescriptionRelationship> {
		$relationships = new MMutableArray();
		foreach ($this->attributes()->traversable() as $attribute) {
			if ($attribute instanceof MEntityDescriptionRelationship) {
				$relationships->addObject($attribute);
			}
		}
		return $relationships;
	}
	
	/**
	 * Returns an attribute with the specified name
	 *
	 * @param MString $name The name for the attribute you want to retrieve
	 *
	 * @return MEntityDescriptionAttribute The attribute with the name specified
	 */
	public function attributeWithName(MString $name) : ?MEntityDescriptionAttribute {
		foreach ($this->attributes()->traversable() as $attribute) {
			if ($attribute->name()->equals($name)) {
				return $attribute;
			} else if ($attribute instanceof MEntityDescriptionRelationship) {
				$singular = $attribute->singular();
				if ($singular !== null) {
					if ($singular->equals($name)) {
						return $attribute;
					}
				}
			}
		}
		return null;
	}
	
	/**
	 * Returns a property with the specified name
	 *
	 * @param MString $name The name of the property you want to retrieve
	 *
	 * @return MEntityDescriptionProperty The property with the name specified.
	 */
	public function propertyWithName(MString $name) : ?MEntityDescriptionProperty {
		$property = $this->attributeWithName($name);
		if ($property instanceof MEntityDescriptionProperty) {
			return $property;
		} else {
			return null;
		}
	}

	/**
	 * Returns a relationship with the specified name
	 *
	 * @param MString $name The name of the relationship you want to retrieve
	 *
	 * @return MEntityDescriptionRelationship The relationship with the name specified.
	 */
	public function relationshipWithName(MString $name) : ?MEntityDescriptionRelationship {
		$relationship = $this->attributeWithName($name);
		if ($relationship instanceof MEntityDescriptionRelationship) {
			return $relationship;
		} else {
			return null;
		}
	}

	/******************** MObject Methods ********************/
	
	public function equals(MMangoObject $object) : bool {
		if ($object instanceof MEntityDescription) {
			return ($this->name()->equals($object->name()) && $this->entityClassName()->equals($object->entityClassName()));
		} else {
			return false;
		}
	}
	
	public function toString() : MString {
		return Sf("%s<%s>", $this->name(), $this->entityClassName());
	}
	
}

