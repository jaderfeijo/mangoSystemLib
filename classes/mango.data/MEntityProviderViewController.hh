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
 * @todo Implement support for JSON. Currently not supported
 */

/**
 * View Controller class that handles the serving of entities to the client
 *
 * This class provides an easy way to serve APIs to your clients based on
 * the entities you've already created. The APIs are RESTful and in most
 * cases, allowing access to your entity via a HTTP RESTful API requires
 * little work.
 *
 * This class handles all CRUD operations for you directly, to use it
 * simply extend it and configure it to use with the entity you wish to
 * serve.
 *
 * Since this class extends from MViewController, it can be used anywhere in
 * the system where a view controller is used.
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MEntityProviderViewController extends MViewController {
	
	protected MEntityDescription $_entity;
	protected MManagedObjectContext $_context;
	protected int $_objectID;
	protected ?MString $_predicate;
	protected MEntityProviderOutputType $_outputType;
	
	/**
	 * Creates a new MEntityProviderViewController instance
	 *
	 * Creates a new instance of the MEntityProviderViewController class which
	 * you can use to serve your entities directly to your clients using
	 * RESTful standards
	 *
	 * @param MEntityDescription $entity The entity you wish to create an interface for
	 * @param MManagedObjectContext $context The Managed Object Context to connect this
	 * entity provider to
	 *
	 * @return MEntityProviderViewController A new instance of the MEntityProviderViewController class
	 */
	public function __construct(MEntityDescription $entity, MManagedObjectContext $context) {
		parent::__construct();
		
		$this->_entity = $entity;
		$this->_context = $context;
		$this->_objectID = MManagedObject::UnknownID;
		$this->_predicate = null;
		$this->_outputType = MEntityProviderOutputType::Xml;
	}
	
	/******************** Protected ********************/
	
	/**
	 * @internal
	 *
	 * Updates a Managed Object with data coming from a POST or PUT request
	 *
	 * @param MManagedObject $object The managed object instance to be updated
	 *
	 * @return void
	 */
	protected function _updateObjectWithInputData(MManagedObject $object) : void {
		foreach ($object->entity()->properties()->toArray() as $property) {
			$object->setObjectForAttribute($property, $this->valueForFieldNamed($property->name()));
		}
	}
	
	/**
	 * @internal
	 *
	 * Creates a new Managed Object and populates it with the data from a POST or PUT request
	 *
	 * @param MEntityDescription $entity The entity which defines the new object type to be created
	 *
	 * @return MManagedObject A new instance of MManagedObject created with the data from
	 * a POST or PUT request
	 */
	protected function _createNewObjectWithInputData(MEntityDescription $entity) : MManagedObject {
		$object = $this->context()->newObjectForEntity($entity);
		$this->_updateObjectWithInputData($object);
		return $object;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Returns the entity this entity provider is connected to
	 *
	 * @return MEntityDescription The entity description instance used by this entity provider
	 */
	public function entity() : MEntityDescription {
		return $this->_entity;
	}
	
	/**
	 * Returns the context used by the entity provider to retrieve and save entities
	 *
	 * @return MManagedObjectContext The managed object context instance used by this entity provider
	 */
	public function context() : MManagedObjectContext {
		return $this->_context;
	}
	
	/**
	 * Sets the object ID for the object that is to be handled by this entity
	 *
	 * Use this method to set the object ID of the object you wish this entity provider
	 * to handle. This will put the entity provider in a 'single' object mode where
	 * whatever the read-write operation is requested will be directed to the object
	 * with the specified object ID.
	 * 
	 * Setting this to 'null' has the effect of putting this entity provider into
	 * 'many' mode which means that whatever read-write operations requested to thie
	 * entity provider will affect all objects of the specified entity type.
	 *
	 * A common way to use this is to configure your MViewController to take
	 * one parameter, namely the ID of the object you wish to work with and then
	 * pass that parameter to the MEntityViewController.
	 *
	 * @example
	 * class MyEntityViewController extends MEntityProviderViewController {
	 *	public function __construct(MString $id) {
	 *			parent::__construct(MEntityDescription::entityWithName(S("MyEntity"), MAppDelegate()->managedObjectContext()));
	 *			$this->setObjectID($id->intValue());
	 *		}
	 *	}
	 *
	 * You can also make the object ID parameter optional, meaning if an object ID is specified
	 * your entity provider will affect only one object with the requested action(s),
	 * otherwise, it affects all objects in the collection.
	 *
	 * @see MEntityProviderViewController::objectID()
	 *
	 * @param int $objectID The object ID of the object to be handled by this entity provider
	 *
	 * @return void
	 */
	public function setObjectID(int $objectID = MManagedObject::UnknownID) : void {
		$this->_objectID = $objectID;
	}
	
	/**
	 * Returns the object ID of the object being handled by this entity provider
	 *
	 * @see MEntityProviderViewController::setObjectID()
	 *
	 * @return int The object ID of the object being handled by this entity provider
	 */
	public function objectID() : int {
		return $this->_objectID;
	}
	
	/**
	 * Sets the predicate for this entity provider
	 *
	 * This method sets the predicate which limits the objects affected / retreived by this entity
	 * provider to only those matching the predicate's criteria
	 *
	 * @see MEntityProviderViewController::predicate()
	 *
	 * @param MString $predicate The predicate to be used with this entity provider
	 *
	 * @return void
	 */
	public function setPredicate(?MString $predicate = null) : void {
		$this->_predicate = $predicate;
	}
	
	/**
	 * Returns the predicate for this entity provider
	 *
	 * @see MEntityProviderViewController::setPredicate()
	 *
	 * @return MString This entity provider's predicate
	 */
	public function predicate() : ?MString {
		return $this->_predicate;
	}
	
	/**
	 * Sets the output type for this entity provider
	 *
	 * This method sets the output type to be used by this entity provider
	 * when outputting data back to clients. Possible values are:
	 * MEntityProviderViewController::OUTPUT_TYPE_XML
	 * MEntityProviderViewController::OUTPUT_TYPE_JSON
	 *
	 * @see MEntityProviderViewController::OUTPUT_TYPE_XML
	 * @see MEntityProviderViewController::OUTPUT_TYPE_JSON
	 *
	 * @param int $type The output type constant that defines the output
	 * type to be used by this entity provider
	 *
	 * @return void
	 */
	public function setOutputType(MEntityProviderOutputType $type) : void {
		if ($type == MEntityProviderOutputType::Xml) {
			$this->_outputType = $type;
		} else {
			throw new MException(Sf("Output Type %d not supported!", $type));
		}
	}
	
	/******************** Override ********************/
	
	/**
	 * Override this method to determine whether or not this entity provider should returns
	 * the objects passed to this method
	 *
	 * This method gets called before returning the specified objects to the client. If
	 * this function returns 'false', then the objects are not returned and the server
	 * returns with a 403 Forbidden response code.
	 *
	 * You can use this method to limit access to certain objects on a user basis for
	 * example. If users have different access levels in your application you may
	 * want to limit access to certain objects for certain users. Overriding this
	 * method gives you a chance to do so before the objects are returned to the
	 * client.
	 *
	 * @param MArray $objects The objects that are about to be passed to the client
	 *
	 * @return bool Whether or not the objects inside $objects should be returned to
	 * the client
	 */
	public function shouldReturnObjects(MArray<MManagedObject> $objects) : bool {
		return true;
	}
	
	/**
	 * Override this method to determine whether or not this entity provider should update
	 * the object passed to this method
	 *
	 * This method gets called before updating the specified object with the data provided
	 * by the client. If this function returns 'false', then the object is not updated
	 * and the server returns a 403 Forbidden response code.
	 *
	 * You can use this method to limit access to certain objects on a user basis for
	 * example. If users have different access levels in your application you may
	 * want to limit access to certain objects for certain users. Overriding this
	 * method gives you a chance to do so before the objects are updated by the
	 * client.
	 * 
	 * @param MManagedObject $object The object about to be updated
	 *
	 * @return bool Whether or not the object should be updated
	 */
	public function shouldUpdateObject(MManagedObject $object) : bool {
		return true;
	}
	
	/**
	 * Override this method to determine whether or not this entity provider should
	 * insert the object passed to this method
	 *
	 * This method gets called before inserting the specified object with the data
	 * provided by the client. If this function returns 'false', then the object is not
	 * inserted and the server returns a 403 Forbidden response code.
	 *
	 * You can use this method to limit access to certain objects on a user basis for
	 * example. If users have different access levels in your application you may
	 * want to limit access to certain objects for certain users. Overriding this
	 * method gives you a chance to do so before the objects are updated by the
	 * client.
	 *
	 * @param MManagedObject $object The object about to be inserted
	 *
	 * @return bool Whether or not the object should be inserted
	 */
	public function shouldInsertObject(MManagedObject $object) : bool {
		return true;
	}
	
	/**
	 * Override this method to determine whether or not this entity provider should
	 * delete the object passed to this method
	 *
	 * This method gets called before deleting the specified object. If this function
	 * returns 'false', then the object is not deleted and the server returns a 403
	 * Forbidden response code.
	 *
	 * You can use this method to limit access to certain objects on a user basis for
	 * example. If users have different access levels in your application you may
	 * want to limit access to certain objects for certain users. Overriding this
	 * method gives you a chance to do so before the objects are updated by the
	 * client.
	 *
	 * @param MManagedObject $object The object about to be deleted
	 *
	 * @return bool Whether or not the object should be deleted
	 */
	public function shouldDeleteObject(MManagedObject $object) : bool {
		return true;
	}
	
	/******************** MViewController ********************/
	
	/**
	 * 
	 */
	public function loadView() : void {
		parent::loadView();
		
		if (MHTTPRequest()->method() == MHTTPRequestMethod::GET)
		{
			$objects = null;
			
			if ($this->objectID()) {
				$object = $this->context()->objectWithObjectID($this->entity(), $this->objectID());
				if ($object) {
					$arr = A($object);
					if ($this->shouldReturnObjects($arr)) {
						$object->fireFault();
						$objects = $arr;
					} else {
						$this->setResponseCode(MHTTPResponseCode::Forbidden);
					}
				} else {
					$this->setResponseCode(MHTTPResponseCode::NotFound);
				}
			} else {
				$request = new MFetchRequest($this->entity(), $this->context());
				if ($this->predicate()) {
					$request->setPredicate($this->predicate());
				}
				$arr = $this->context()->executeFetchRequest($request);
				if ($arr->count() > 0) {
					if ($this->shouldReturnObjects($arr)) {
						$objects = $arr;
					} else {
						$this->setResponseCode(MHTTPResponseCode::Forbidden);
					}
				} else {
					$this->setResponseCode(MHTTPResponseCode::NotFound);
				}
			}
			
			if ($objects) {
				$xml = new MXMLDocumentView();
				
				$objectsXML = new MXMLEntityCollectionView($this->entity());
				foreach ($objects->toArray() as $object) {
					$objectsXML->addManagedObject($object);
				}
				$xml->addSubview($objectsXML);
				
				$this->setView($xml);
			}
		}
		else if (MHTTPRequest()->method() == MHTTPRequestMethod::PUT)
		{
			if ($this->objectID()) {
				$object = $this->context()->objectWithObjectID($this->entity(), $this->objectID());
				if ($object) {
					if ($this->shouldUpdateObject($object)) {
						$this->_updateObjectWithInputData($object);
						$this->context()->save();
					} else {
						$this->setResponseCode(MHTTPResponseCode::Forbidden);
					}
				} else {
					$this->setResponseCode(MHTTPResponseCode::NotFound);
				}
			} else {
				$this->setResponseCode(MHTTPResponseCode::Forbidden);
			}
		}
		else if (MHTTPRequest()->method() == MHTTPRequestMethod::POST)
		{
			if ($this->objectID()) {
				$this->setResponseCode(MHTTPResponseCode::Forbidden);
			} else {
				$object = $this->_createNewObjectWithInputData($this->entity());
				if ($object) {
					if ($this->shouldInsertObject($object)) {
						$this->context()->save();
						$this->setView(new MPlainTextView(Sf("%s/%s", $this->address(), N($object->objectID())->toString())));
					} else {
						$this->context()->deleteObject($object);
						$this->setResponseCode(MHTTPResponseCode::Forbidden);
					}
				} else {
					$this->setResponseCode(MHTTPResponseCode::BadRequest);
				}
			}
		}
		else if (MHTTPRequest()->method() == MHTTPRequestMethod::DELETE)
		{
			if ($this->objectID()) {
				$object = $this->context()->objectWithObjectID($this->entity(), $this->objectID());
				if ($object) {
					if ($this->shouldDeleteObject($object)) {
						$this->context()->deleteObject($object);
						$this->context()->save();
					} else {
						$this->setResponseCode(MHTTPResponseCode::Forbidden);
					}
				} else {
					$this->setResponseCode(MHTTPResponseCode::NotFound);
				}
			} else {
				$this->setResponseCode(MHTTPResponseCode::Forbidden);
			}
		}
	}
	
}

