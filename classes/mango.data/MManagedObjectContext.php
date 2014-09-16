<?php

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
	
	package('mango.data');
	
	import('mango.system.*');
	import('mango.system.io.*');
	import('mango.system.exceptions.*');
	
	/**
	 *
	 * @todo When retrieving objects from the persistent store, make sure that objects
	 * scheduled for deletion are removed from the result set
	 *
	 * @todo Implement caching of Managed Objects
	 *
	 */
	
	/**
	 * This class creates a context in which Managed Objects can exist. It handles
	 * the creation, retrieval, caching and persisting of Managed Objects. Every
	 * Managed Object needs to belong to a context. You can have more than
	 * one context in your application, you should never mix objects from one
	 * context with objects from another context.
	 *
	 * The Managed Object Context is like a bridge that sits between the objects
	 * and the Persistent Store. It takes care of the upper layer of managinging
	 * the objects and it feeds information to both, the objects layer and the
	 * Persistent Store layer about the state of the objects
	 *
	 * @see MManagedObject
	 * @see MPersistentStore
	 * @see MPersistentStoreCoordinator
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MManagedObjectContext extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $persistentStoreCoordinator;
		protected $managedObjects;
		protected $deletedObjects;
		
		/**
		 * Creates a new MManagedObjectContext object that is connected to a Persistent Store Coordinator
		 *
		 * @param MPersistentStoreCoordinator $persistentStoreCoordinator The Persistent Store Coordinator
		 * this context is connected to
		 *
		 * @return MManagedObjectContext The new Managed Object Context
		 */
		public function __construct(MPersistentStoreCoordinator $persistentStoreCoordinator) {
			parent::__construct();
			
			$this->persistentStoreCoordinator = $persistentStoreCoordinator;
			$this->managedObjects = new MMutableArray();
			$this->deletedObjects = new MMutableArray();
			
			$this->persistentStoreCoordinator()->setManagedObjectContext($this);
		}
		
		/******************** Private ********************/
		
		/**
		 * @return MManagedObject
		 */
		private function _parseObjectFromXML(SimpleXMLElement $xmlObject, callable $callback = null) {
			$entityName = S($xmlObject->getName());
			$entity = $this->persistentStoreCoordinator()->model()->entityWithName($entityName);
			if ($entity) {
				$object = $this->newObjectForEntity($entity);
				if ($object) {
					foreach ($xmlObject->children() as $xmlAttribute) {
						$attribute = $entity->attributeWithName(S($xmlAttribute->getName()));
						if ($attribute instanceof MEntityDescriptionProperty) {
							if ($attribute->type() == MEntityDescriptionProperty::StringType) {
								$object->setObjectForAttribute($attribute, S((string)$xmlAttribute));
							} else if ($attribute->type() == MEntityDescriptionProperty::IntegerType) {
								$object->setObjectForAttribute($attribute, N((int)$xmlAttribute));
							} else if ($attribute->type() == MEntityDescriptionProperty::FloatType) {
								$object->setObjectForAttribute($attribute, N((float)$xmlAttribute));
							} else if ($attribute->type() == MEntityDescriptionProperty::BooleanType) {
								$object->setObjectForAttribute($attribute, N((bool)$xmlAttribute));
							} else if ($attribute->type() == MEntityDescriptionProperty::DateType) {
								$object->setObjectForAttribute($attribute, MDate::parse((string)$xmlAttribute));
							} else if ($attribute->type() == MEntityDescriptionProperty::BinaryType) {
								$object->setObjectForAttribute($attribute, MData::dataWithBytes((string)$xmlAttribute));
							} else {
								throw new MInvalidDataTypeException(S("StringType|IntegerType|FloatType|BooleanType|DateType|BinaryType"), S($attribute->type()));
							}
						} else if ($attribute instanceof MEntityDescriptionRelationship) {
							foreach ($xmlAttribute->children() as $xmlRelationshipObject) {
								$object->addObjectToRelationship($attribute, $this->_parseObjectFromXML($xmlRelationshipObject));
							}
						} else {
							throw new MManagedObjectException($object, Sf("Attribute type '%s' not supported", $attribute->className()));
						}
					}
					
					if (!is_null($callback)) $callback($object);
					
					return $object;
				} else {
					throw new MManagedObjectException($object, S("Could not create object"));
				}
			} else {
				throw new MEntityNotFoundException($entityName);
			}
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the Persistent Store Coordinator this context is connected to 
		 *
		 * @return MPersistentStoreCoordinator The Persistent Store Coordinator
		 * this context is connected to
		 */
		public function persistentStoreCoordinator() {
			return $this->persistentStoreCoordinator;
		}
		
		/**
		 * Returns an Array of all the Managed Objects currently existing in this context
		 *
		 * @return MArray An Array containing the Managed Objects within this context
		 */
		public function managedObjects() {
			return $this->managedObjects;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Returns an Array containing all the Managed Objects that have been removed within
		 * this context
		 *
		 * This method returns an Array of all objects within this context that have been
		 * scheduled for deletion and will be removed once this context is saved
		 *
		 * @see MManagedObjectContext::save()
		 *
		 * @return MArray An Array containing the objects that are scheduled for deletion
		 * in this context
		 */
		public function deletedObjects() {
			return $this->deletedObjects;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Parses one or more Managed Objects from inside the specified XML file
		 * and inserts them into this Managed Object Context
		 *
		 * @param MFile $file An XML file containing the objects to be parsed
		 * @param callable $callback A callback function which will be called every time a new
		 * object is parsed and added into the Managed Object Context. The method signature for the
		 * callback is callback(MManagedObject $object);
		 *
		 * @return MArray An Array containing the parsed objects
		 */
		public function parseObjectsFromFile(MFile $file, callable $callback = null) {
			if (!$file->exists()) throw new MFileNotFoundException($file->path());
			
			$xml = simplexml_load_file($file->path()->stringValue());
			return $this->parseObjectsFromXML($xml, $callback);
		}
		
		/**
		 * Parses one or more Managed Objects from inside the specified XML data
		 * and inserts them into this Managed Object Context
		 *
		 * @param MData $data A data object containing the XML representation of the objects to be parsed
		 * @param callable $callback A callback function which will be called every time a new
		 * object is parsed and added into the Managed Object Context. The method signature for the
		 * callback is callback(MManagedObject $object);
		 *
		 *
		 * @return MArray An Array containing the parsed objects
		 */
		public function parseObjectsFromData(MData $data, callable $callback = null) {
			$xml = simplexml_load_string($data->getBytes());
			return $this->parseObjectsFromXML($xml, $callback);
		}
		
		/**
		 * Parses one or more Managed Objects from inside the specified XML string
		 * and inserts them into this Managed Object Context
		 *
		 * @param MString $string A string containing the XML representation of the objects to be parsed
		 * @param callable $callback A callback function which will be called every time a new
		 * object is parsed and added into the Managed Object Context. The method signature for the
		 * callback is callback(MManagedObject $object);
		 *
		 * @return MArray An Array containing the parsed objects
		 */
		public function parseObjectsFromString(MString $string, callable $callback = null) {
			$xml = simplexml_load_string($string->stringValue());
			return $this->parseObjectsFromXML($xml, $callback);
		}
		
		/**
		 * Parses one or more Managed Objects from inside the specified SimpleXMLElement object
		 * and inserts them into this Managed Object Context
		 *
		 * @param SimpleXMLElement $xml A SimpleXMLElement object containing the objects to be parsed
		 * @param callable $callback A callback function which will be called every time a new
		 * object is parsed and added into the Managed Object Context. The method signature for the
		 * callback is callback(MManagedObject $object);
		 *
		 * @return MArray An Array containing the parsed objects
		 */
		public function parseObjectsFromXML(SimpleXMLElement $xml, callable $callback = null) {
			$objects = new MMutableArray();
			foreach ($xml->children() as $xmlObject) {
				$objects->addObject($this->_parseObjectFromXML($xmlObject, $callback));
			}
			return $objects;
		}
		
		/**
		 * Returns a Managed Object that matches the entity type and the id specified
		 *
		 * This method fetches an object from the Persistent Store that is of the
		 * entity type specified by $entity and with the id specified in $id
		 *
		 * @param MEntityDescription $entity The entity type of the object to fetch
		 * @param int $id The ID of the object to be fetched
		 *
		 * @return MManagedObject The fetched MManagedObject
		 */
		public function objectWithObjectID(MEntityDescription $entity, $objectID) {
			MAssertTypes('int', $id);
			
			$request = new MFetchRequest($entity, $this);
			$request->setPredicate(Sf("objectID = '%d'", $objectID));
			$data = $this->executeFetchRequest($request);
			
			return $data->lastObject();
		}
		
		/**
		 * Returns a Managed Object whose property matches the specified object value
		 *
		 * This method fetches an object from the Persistent Store that contains the
		 * specified property and whose value for that property matches the value
		 * specified in $value
		 *
		 * @param MEntityDescriptionProperty $property The property whose value you
		 * wish to match against
		 * @param MObject $value The value to be matched
		 *
		 * @return MManagedObject The fetched MManagedObject
		 */
		public function objectWith(MEntityDescriptionProperty $property, MObject $value = null) {
			$request = new MFetchRequest($property->entity(), $this);
			if ($value) {
				$request->setPredicate(Sf("%s = '%s'", $property->name()->stringValue(), $value->toString()->stringValue()));
			} else {
				$request->setPredicate(Sf("%s = null", $property->name()->stringValue()));
			}
			$data = $this->executeFetchRequest($request);
			
			return $data->lastObject();
		}
		
		/**
		 * Returns a Managed Object whose property path matches the specified object
		 * value
		 *
		 * This method fetches an object from the Persistent Store that contains the
		 * specified property and whose value for that property matches the value
		 * specified in $value
		 *
		 * @param MString $attributePath The attribute path whose value you
		 * wish to match against
		 * @param MObject $value The value to be matched
		 *
		 * @return MManagedObject The fetched MManagedObject
		 */
		public function objectWithAttributePath(MString $attributePath, MObject $value = null) {
			return $this->objectWith($this->persistentStoreCoordinator()->model()->attributeWithPath($attributePath), $value);
		}
		
		/**
		 * Creates a new Managed Object instance with the class type specified by the entity
		 *
		 * This creates a new instance of an MManagedObject subclass specified in entity,
		 * inserts it into this context and returns it.
		 *
		 * @param MEntityDescription $entity The entity to create a new instance of
		 * @param int $objectID An optional ID to use for the object. This should only
		 * be used when manullay constructing a representation of the object from the
		 * data store.
		 *
		 * @return MManagedObject The new MManagedObject subclass instance
		 */
		public function newObjectForEntity(MEntityDescription $entity, $objectID = MManagedObject::UNKNOWN_ID) {
			MAssertTypes('int', $objectID);
			return MObject::newInstanceOfClassWithParameters(
				$entity->entityClassName(),
				A(array($entity, $this, $objectID))
			);
		}
		
		/**
		 * Executes a Fetch Request into this context's Persistent Store
		 *
		 * This method executes the specified Fetch Request into the Persistent Store.
		 * The returned Array contains all the objects matching the Fetch Request's
		 * predicate criteria
		 *
		 * @see MFetchRequest
		 * @see MPersistentStore
		 *
		 * @param MFetchRequest $request The Request to be executed in this context
		 *
		 * @return MArray An Array containing the Managed Objects that matched
		 * the specified request
		 */
		public function executeFetchRequest(MFetchRequest $request) {
			return $this->persistentStoreCoordinator()->executeRequest($request);
		}
		
		/**
		 * Inserts a Managed Object into this context
		 *
		 * This method is called automatically every time you create a new MManagedObject
		 *
		 * @param MManagedObject $object The Managed Object to insert into this context
		 *
		 * @return void
		 */
		public function insertObject(MManagedObject $object) {
			$this->managedObjects->addObject($object);
		}
		
		/**
		 * Schedule a Managed Object for deletion
		 *
		 * The Managed Object is deleted once this context is saved
		 *
		 * @see MManagedObjectContext::save()
		 *
		 * @return void
		 */
		public function deleteObject(MManagedObject $object) {
			$this->deletedObjects->addObject($object);
			$this->managedObjects->removeObject($object);
		}
		
		/**
		 * Returns whether or not this context has unsaved changes
		 *
		 * @return bool Whether or not this context has unsaved changes
		 */
		public function hasChanges() {
			if ($this->deletedObjects()->count() > 0) {
				return true;
			} else {
				foreach ($this->managedObjects()->toArray() as $object) {
					if ($object->hasChanges()) {
						return true;
					}
				}
				return false;
			}
		}
		
		/**
		 * Saves any unsaved changes in this context and it's Managed Objects into the Persistent Store
		 *
		 * @see MManagedObjectContext::hasChanges()
		 *
		 * @return bool Whether or not the save was successful
		 */
		public function save() {
			if ($this->hasChanges()) {
				$saveRequest = new MSaveRequest($this);
				
				$insertedObjects = new MMutableArray();
				$updatedObjects = new MMutableArray();
				
				foreach ($this->managedObjects()->toArray() as $object) {
					if ($object->hasChanges()) {
						if ($object->objectID() == MManagedObject::UNKNOWN_ID) {
							$insertedObjects->addObject($object);
						} else {
							$updatedObjects->addObject($object);
						}
					}
				}
				
				$saveRequest->setInsertManagedObjects($insertedObjects);
				$saveRequest->setUpdateManagedObjects($updatedObjects);
				$saveRequest->setDeleteManagedObjects($this->deletedObjects());
				
				$affectedObjects = $this->persistentStoreCoordinator()->executeRequest($saveRequest);
				
				foreach ($affectedObjects->toArray() as $object) {
					if (!$this->deletedObjects->removeObject($object)) {
						$object->persistChanges();
					}
				}
				
				return true;
			} else {
				return false;
			}
		}
		
	}

?>