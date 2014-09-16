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
	import('mango.data.exceptions.*');
	
	/**
	 *
	 * @todo Add documentation for the __call() method
	 * @todo Implement JSON support
	 *
	 */
	
	/**
	 * This class stores and manages the data retrieved from the data store
	 * into an object called a Managed Object
	 *
	 * Managed Objects are always a sub-class of MManagedObject. They are
	 * objects that are dynamically built based on the data stored inside
	 * the data store and a Managed Object Model. The object's description
	 * of it's properties and relationships with other Managed Objects is
	 * managed by a class called MEntityDescription. That describes all the
	 * information about the entity a Managed Object conforms to.
	 *
	 * You should create a subclass of MManagedObject for each one of your
	 * entities. The class name is usually the same as the entity's name.
	 *
	 * Once you've defined the model and loaded it into the system, the
	 * system will create MEntityDescription instances for each one of
	 * the entities you defined in your model. When you perform a fetch
	 * request to the database for example, it will dynamically load the
	 * custom class you created for your entity which extends MManagedObject.
	 * The MManagedObject class will then manage all the data for the object
	 * and take care of things such as faulting and relationships.
	 *
	 * It also handles the dynamic creation of accessor methods for each one
	 * of your properties and relationships dynamically according to what
	 * you defined in your model, amongst other things.
	 *
	 * @see MManagedObjectModel
	 * @see MEntityDescription
	 * @see MManagedObjectContext
	 * @see MPersistentStoreCoordinator
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MManagedObject extends MObject {
		
		const UNKNOWN_ID = -1;
		
		/******************** Static Methods ********************/
		
		/**
		 * When called in a subclass of MManagedObject, this method returns the entity description object
		 * that class represents
		 *
		 * This method uses the subclass' name to search the provided MManagedObjectContext for a matching
		 * entity description for the class in which the method is called on.
		 *
		 * @param MManagedObjectContext $context The context in which to perform the search
		 *
		 * @return MEntityDescription The MEntityDescription instance that corresponds to this class
		 */
		public static function entityDescription(MManagedObjectContext $context) {
			return $context->persistentStoreCoordinator()->model()->entityWithName(parent::typeClass());
		}
		
		//
		// ************************************************************
		//
		
		protected $entity;
		protected $context;
		protected $objectID;
		
		protected $data;
		protected $updatedData;
		protected $relationships;
		protected $insertedRelationships;
		protected $removedRelationships;
		protected $isSaving;
		protected $savingInsertedRelationships;
		protected $savingRemovedRelationships;
		
		/**
		 * Creates a new instance of the MManagedObject class and inserts it into the context
		 *
		 * @param MEntityDescription $entity The entity that represents this Managed Object
		 * @param MManagedObjectContext $context The context to insert this new Managed Object into
		 *
		 * @return MManagedObject The newly created Managed Object
		 */
		public function __construct(MEntityDescription $entity, MManagedObjectContext $context, $objectID = MManagedObject::UNKNOWN_ID) {
			MAssertTypes('int', $objectID);
			parent::__construct();
			
			$this->entity = $entity;
			$this->context = $context;
			$this->objectID = $objectID;
			
			$this->data = new MMutableDictionary();
			$this->updatedData = new MMutableDictionary();
			$this->relationships = new MMutableDictionary();
			$this->insertedRelationships = new MMutableDictionary();
			$this->removedRelationships = new MMutableDictionary();
			$this->isSaving = false;
			$this->savingInsertedRelationships = new MMutableArray();
			$this->savingRemovedRelationships = new MMutableArray();
			
			$this->initDefaultValues();
			$this->context()->insertObject($this);
			$this->didCreateObject();
		}
		
		/******************** Protected ********************/
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function initDefaultValues() {
			foreach ($this->entity->attributes() as $attribute) {
				if ($attribute instanceof MEntityDescriptionProperty) {
					if ($attribute->defaultValue() != null) {
						$this->updatedData->setObjectForKey($attribute->name(), $attribute->defaultValue());
					}
				}
			}
		}
		
		/******************** Magic ********************/
		
		/**
		 * @internal
		 *
		 * @return mixed
		 */
		function __call($method, $arguments) {
			$keyName = S($method);
			$operation = S("");
			if ($keyName->startsWith(S("set"))) {
				$keyName = $keyName->substringWithRange(MRangeMake(3, 1))->toLower()->stringByAppendingString($keyName->substringFromIndex(4));
				$operation = S("set");
			} else if ($keyName->startsWith(S("add"))) {
				$keyName = $keyName->substringWithRange(MRangeMake(3, 1))->toLower()->stringByAppendingString($keyName->substringFromIndex(4));
				$operation = S("add");
			} else if ($keyName->startsWith(S("remove"))) {
				$keyName = $keyName->substringWithRange(MRangeMake(6, 1))->toLower()->stringByAppendingString($keyName->substringFromIndex(7));
				$operation = S("remove");
			}
			
			$attribute = $this->entity()->attributeWithName($keyName);
			
			if ($attribute) {
				if ($operation->equals(S("set"))) {
					$this->setObjectForAttribute($attribute, $arguments[0]);
				} else if ($operation->equals(S("add"))) {
					$this->addObjectToRelationship($attribute, $arguments[0]);
				} else if ($operation->equals(S("remove"))) {
					$this->removeObjectFromRelationship($attribute, $arguments[0]);
				} else {
					return $this->objectForAttribute($attribute);
				}
			} else {
				throw new MManagedObjectException($this); // Unknown Exception
			}
		}
		
		/******************** Internal ********************/
		
		/**
		 * @internal
		 * 
		 * Sets the object ID for this Managed Object
		 *
		 * @see MManagedObject::objectID()
		 *
		 * @param int $objectID The object ID for this Managed Object
		 *
		 * @return void
		 */
		public function _setObjectID($objectID) {
			MAssertTypes('int', $objectID);
			$this->objectID = $objectID;
		}
		
		/**
		 * @internal
		 *
		 * Used interanally to set the data for this object
		 *
		 * @param MMutableDictionary $data The data for this object
		 *
		 * @return void
		 */
		public function _setData(MMutableDictionary $data) {
			$this->data = $data;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to return the data for this object
		 *
		 * @return MDictionary A dictionary containing the data for this object
		 */
		public function _data() {
			return $this->data;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to return the updated data for this object
		 *
		 * This is the data that has been modified and not yet saved to
		 * the data store
		 *
		 * @return MDictionary A dictionary containing the data that has
		 * been updated in this object
		 */
		public function _updatedData() {
			return $this->updatedData;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to set the relationships for this object
		 *
		 * @param MMutableDictionary $relationship A Mutable Dictionary
		 * containing the relationships for this object
		 *
		 * @return void
		 */
		public function _setRelationships(MMutableDictionary $relationships) {
			$this->relationships = $relationships;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to return the relationships for this object
		 *
		 * @return MDictionary A dictionary containing the relationships
		 * for this object
		 */
		public function _relationships() {
			return $this->relationships;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to return which relationships were inserted
		 *
		 * @return MDictionary A Dictionary containing the relationships
		 * that have been inserted into this object
		 */
		public function _insertedRelationships() {
			return $this->insertedRelationships;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to return which relationships were removed
		 *
		 * @return MDictionary A Dictionary containing the relationships
		 * that have been removed from this object
		 */
		public function _removedRelationships() {
			return $this->removedRelationships;
		}
		
		/**
		 * @internal
		 * 
		 * Returns weather or not this object has unsaved updated data
		 * 
		 * @return bool Weather or not this object has unsaved updated data
		 */
		public function _hasUpdatedData() {
			return ($this->updatedData->count() > 0);
		}
		
		/**
		 * @internal
		 * 
		 * Returns weather or not this object has unsaved inserted relationships
		 * 
		 * @return bool Weather or not this object has unsaved inserted relationships
		 */
		public function _hasInsertedRelationships() {
			return ($this->insertedRelationships->count() > 0);
		}
		
		/**
		 * @internal
		 * 
		 * Returns weather or not this object has unsaved removed relationships
		 * 
		 * @return bool Weather or not this object has unsaved removed relationships
		 */
		public function _hasRemovedRelationships() {
			return ($this->removedRelationships->count() > 0);
		}
		
		/**
		 * @internal
		 * 
		 * Called by the Persistent Store to inform this object that it is about to be saved
		 * 
		 * @return void
		 */
		public function _willStartSaving() {
			$this->isSaving = true;
		}
		
		/**
		 * @internal
		 * 
		 * Returns weather or not this object is currently being saved by the Persistent Store
		 * 
		 * @return bool Weather or not this object is currently being saved by the Persistent Store
		 */
		public function _isSaving() {
			return $this->isSaving;
		}
		
		/**
		 * @internal
		 * 
		 * Called by the Persistent Store to inform this object that it has been saved
		 * 
		 * @return void
		 */
		public function _didFinishSaving() {
			$this->isSaving = false;
		}
		
		/**
		 * @internal
		 * 
		 * Called by the Persistent Store to inform this object that the inserted objects for the
		 * specified relationship are about to be saved on the other end of the relationship and do
		 * not need to be saved on this end again
		 * 
		 * @param MString $relationshipName The name of the relationship which is about to be
		 * saved
		 * 
		 * @return void
		 */
		public function _willSaveInsertedObjectsForRelationshipName(MString $relationshipName) {
			$this->savingInsertedRelationships->addObject($relationshipName);
		}
		
		/**
		 * @internal
		 * 
		 * Returns the current save status of the inserted objects for the specified relationship
		 * 
		 * @param MString $relationshipName The name of the relationship you wish to check the
		 * save status for
		 * 
		 * @return bool Weather or not the specified relationship's inserted objects are currently
		 * being saved
		 */
		public function _isSavingInsertedObjectsForRelationshipName(MString $relationshipName) {
			return $this->savingInsertedRelationships->containsObject($relationshipName);
		}
		
		/**
		 * @internal
		 * 
		 * Called by the Persistent Store to inform this object that the inserted objects for the
		 * specified relationship have been saved on the other end of the relationship and do not
		 * need to be saved on this end again
		 * 
		 * @param MString $relationshipName The name of the relationship which no longer requires
		 * saving
		 * 
		 * @return void
		 */
		public function _didSaveInsertedObjectsForRelationshipName(MString $relationshipName) {
			if ($this->insertedRelationships->hasObjectForKey($relationshipName)) {
				// Merge the inserted objects with the current objects for this relationship
				$insertedObjects = $this->insertedRelationships->objectForKey($relationshipName);
				if ($this->relationships->hasObjectForKey($relationshipName)) {
					$this->relationships->objectForKey($relationshipName)->appendArray($insertedObjects);
				} else {
					$this->relationships->setObjectForKey($relationshipName, $insertedObjects);
				}
				
				// Clear the insertedRelationships dictionary entry for this relationship
				$this->insertedRelationships->removeObjectForKey($relationshipName);
			}
			$this->savingInsertedRelationships->removeObject($relationshipName);
		}
		
		/**
		 * @internal
		 * 
		 * Called by the Persistent Store to inform this object that the removed objects for the
		 * specified relationship are about to be saved on the other end of the relationship and do
		 * not need to be saved on this end again
		 * 
		 * @param MString $relationshipName The name of the relationship which is about to be
		 * saved
		 * 
		 * @return void
		 */
		public function _willSaveRemovedObjectsForRelationshipName(MString $relationshipName) {
			$this->savingRemovedRelationships->addObject($relationshipName);
		}
		
		/**
		 * @internal
		 * 
		 * Returns the current save status of the inserted objects for the specified relationship
		 * 
		 * @param MString $relationshipName The name of the relationship you wish to check the
		 * save status for
		 * 
		 * @return bool Weather or not the specified relationship's removed objects are currently
		 * being saved
		 */
		public function _isSavingRemovedObjectsForRelationshipName(MString $relationshipName) {
			return $this->savingRemovedRelationships->containsObject($relationshipName);
		}
		
		/**
		 * @internal
		 * 
		 * Called by the Persistent Store to inform this object that the removed objects for the
		 * specified relationship have been saved on the other end of the relationship and do not
		 * need to be saved on this end again
		 * 
		 * @param MString $relationshipName The name of the relationship which no longer requires
		 * saving
		 * 
		 * @return void
		 */
		public function _didSaveRemovedObjectsForRelationshipName(MString $relationshipName) {
			if ($this->removedRelationships->hasObjectForKey($relationshipName)) {
				// Subtract the removed objects from the current objects for this relationship
				$removedObjects = $this->removedRelationships->objectForKey($relationshipName);
				if ($this->relationships->hasObjectForKey($relationshipName)) {
					$this->relationships->objectForKey($relationshipName)->subtractArray($removedObjects);
				}
				
				// Clear the removedRelationships dictionary entry for this relationship
				$this->removedRelationships->removeObjectForKey($relationshipName);
			}
			$this->savingRemovedRelationships->removeObject($relationshipName);
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns this Managed Object's entity
		 *
		 * @return MEntityDescription The entity this Managed Object represents
		 */
		public function entity() {
			return $this->entity;
		}
		
		/**
		 * Returns the context this Managed Object belongs to
		 *
		 * @return MManagedObjectContext The context this Managed Object belongs to
		 */
		public function context() {
			return $this->context;
		}
		
		/**
		 * Returns the object ID for this Managed Object
		 *
		 * This method returns the unique object ID which identified this object in it's
		 * Persistent Store
		 *
		 * @return int This Managed Object's ID
		 */
		public function objectID() {
			return $this->objectID;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Subclasses of MManagedObject should override this method.
		 *
		 * This method is called by the constructor every time a new instance of MManagedObject
		 * is created. This gives a chance for subclasses of MManagedObject to do any initialization
		 * they may need.
		 *
		 * Subclasses of MManagedObject should override this method instead of __construct if they
		 * need to do any custom initialization
		 *
		 * @return void
		 */
		public function didCreateObject() {
			// the default implementation of this method doesn't do anything
		}
		
		/**
		 * Returns the Object which represents the value for an attribute of this object
		 *
		 * Attributes are the properties and relationships of this object. This method
		 * returns an object or a collection of objects, in the case of a relationship,
		 * which represent the value of that attribute
		 *
		 * @see MManagedObject::setObjectForAttribute()
		 * @see MEntityDescriptionAttribute
		 * @see MEntityDescriptionProperty
		 * @see MEntityDescriptionRelationship
		 *
		 * @param MEntityDescriptionAttribute $attribute The attribute which you'd like to
		 * retrieve the value for
		 *
		 * @return MObject|MArray An object or an Array containing a collection of Objects
		 * that represent the value of the requested attribute
		 */
		public function objectForAttribute(MEntityDescriptionAttribute $attribute) {
			$object = null;
			
			$this->fireFault();
			
			if ($attribute instanceof MEntityDescriptionProperty) {
				$object = $this->updatedData->objectForKey($attribute->name());
				if ($object == null) {
					$object = $this->data->objectForKey($attribute->name());
				}
			} else if ($attribute instanceof MEntityDescriptionRelationship) {
				$objects = new MMutableArray();
				
				if (($arr = $this->relationships->objectForKey($attribute->name())) != null) {
					$objects->appendArray($arr);
				}
				
				if (($arr = $this->insertedRelationships->objectForKey($attribute->name())) != null) {
					$objects->appendArray($arr);
				}
				
				if (($arr = $this->removedRelationships->objectForKey($attribute->name())) != null) {
					$objects->subtractArray($arr);
				}
				
				if ($attribute->to() == MEntityDescriptionRelationship::ToOne) {
					$object = $objects->lastObject();
				} else {
					$object = $objects;
				}
			} else {
				throw new MManagedObjectException($this, S("Unknown attribute type!"));
			}
			
			return $object;
		}
		
		/**
		 * Sets the object which represents the value for a certain attribute of this object
		 *
		 * Attributes are the properties and relationships of this object. This method sets
		 * the object which represents the value of that attribute.
		 *
		 * You cannot use this method to set the value of a ToMany relationship, for that
		 * you should use MManagedObject::addObjectToRelationship and
		 * MManagedObject::removeObjectFromRelationship
		 *
		 * @see MManagedObject::addObjectToRelationship
		 * @see MManagedObject::removeObjectFromRelationship
		 *
		 * @param MEntityDescriptionAttribute $attribute The attribute which you'd like to set
		 * the value of
		 * @param MObject $object The Object representing the value for that attribute
		 *
		 * @return void
		 */
		public function setObjectForAttribute(MEntityDescriptionAttribute $attribute, MObject $object = null) {
			$this->fireFault();
			
			if ($attribute instanceof MEntityDescriptionProperty) {
				if ($object) {
					if ($attribute->type() == MEntityDescriptionProperty::StringType) {
						if (!$object instanceof MString) {
							throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", str($object->className()), str(MString::className())));
						}
					} else if ($attribute->type() == MEntityDescriptionProperty::IntegerType) {
						if (!$object instanceof MNumber) {
							throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", str($object->className()), str(MNumber::className())));
						}
					} else if ($attribute->type() == MEntityDescriptionProperty::FloatType) {
						if (!$object instanceof MNumber) {
							throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", str($object->className()), str(MNumber::className())));
						}
					} else if ($attribute->type() == MEntityDescriptionProperty::BooleanType) {
						if (!$object instanceof MNumber) {
							throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", str($object->className()), str(MNumber::className())));
						}
					} else if ($attribute->type() == MEntityDescriptionProperty::DateType) {
						if (!$object instanceof MDate) {
							throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", str($object->className()), str(MDate::className())));
						}
					} else if ($attribute->type() == MEntityDescriptionProperty::BinaryType) {
						if (!$object instanceof MData) {
							throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", str($object->className()), str(MData::className())));
						}
					} else {
						throw new MInvalidManagedObjectOperationException($this, Sf("Unsupported type [%s]!", $attribute->type()));
					}
				}
				$this->updatedData->setObjectForKey($attribute->name(), $object);
			} else if ($attribute instanceof MEntityDescriptionRelationship) {
				if ($attribute->to() == MEntityDescriptionRelationship::ToMany) {
					throw new MInvalidManagedObjectOperationException($this, S("Could not set a ToMany relationship, please use add/remove"));
				}
				if (($oldArr = $this->relationships->objectForKey($attribute->name())) != null) {
					if ($oldArr->lastObject()) {
						$this->removeObjectFromRelationship($attribute, $oldArr->lastObject());
					}
				}
				$this->addObjectToRelationship($attribute, $object);
			} else {
				throw new MManagedObjectException($this, S("Unknown attribute type!"));
			}
		}
		
		/**
		 * Adds an Object to the specified relationship attribute of this Managed Object
		 *
		 * @param MEntityDescriptionRelationship $relationship The relationship you wish to add
		 * an object to
		 * @param MManagedObject $object The Managed Object to add to the relationship
		 * @param bool $updateInverseRelationship Weather or not the inverse relationship should also be updated
		 *
		 * @return void
		 */
		public function addObjectToRelationship(MEntityDescriptionRelationship $relationship, MManagedObject $object, $updateInverseRelationship = true) {
			MAssertTypes('bool', $updateInverseRelationship);
			
			$this->fireFault();
			
			if ($relationship->typeClassName() != $object->className()) {
				throw new MInvalidManagedObjectOperationException($this, Sf("Invalid type [%s], expected [%s]!", $object->className(), $relationship->typeClassName()));
			}
			
			// Add object on this side of the relationship
			$arr = $this->insertedRelationships->objectForKey($relationship->name());
			if (!$arr) {
				$arr = new MMutableArray();
				$this->insertedRelationships->setObjectForKey($relationship->name(), $arr);
			}
			$arr->addObject($object);
			
			// Update the inverse side of the relationship if necessary
			if ($updateInverseRelationship) {
				$object->addObjectToRelationship($relationship->inverseRelationship(), $this, false);
			}
		}
		
		/**
		 * Removes an Object from the specified relationship attribute of this Managed Object
		 *
		 * @param MEntityDescriptionRelationship $relationship The relationship you wish to remove
		 * an object from
		 * @param MManagedObject $object The Managed Object to remove from the relationship
		 * @param bool $updateInverseRelationship Weather or not the inverse relationship should also be updated
		 *
		 * @return void
		 */
		public function removeObjectFromRelationship(MEntityDescriptionRelationship $relationship, MManagedObject $object, $updateInverseRelationship = true) {
			MAssertTypes('bool', $updateInverseRelationship);
			
			$this->fireFault();
			
			// Remove object from this side of the relationship
			$arr = $this->removedRelationships->objectForKey($relationship->name());
			if (!$arr) {
				$arr = new MMutableArray();
				$this->removedRelationships->setObjectForKey($relationship->name(), $arr);
			}
			$arr->addObject($object);
			
			// Update the inverse side of the relationship if necessary
			if ($updateInverseRelationship) {
				$object->removeObjectFromRelationship($relationship->inverseRelationship(), $this, false);
			}
		}
		
		/**
		 * Returns whether or not this object is a fault
		 *
		 * Fault is the state of a Managed Object when it is immediately
		 * retrieved from the Persistent Store via a Fetch Request. At this
		 * state the object contains no data, only the ID which identifies
		 * it in the Persitent Store is set
		 *
		 * The data is only retrieved from the Persistent Store once a property
		 * is accessed. After that, the Object is no longer a fault
		 *
		 * @see MManagedObject::fireFault()
		 *
		 * @return bool Whether or not this object is in a fault state
		 */
		public function isFault() {
			if ($this->objectID() != MManagedObject::UNKNOWN_ID && $this->data->count() <= 0 && $this->relationships->count() <= 0) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Fires the fault fetching process in this object
		 *
		 * If this object is a fault, this method fires it and retreives
		 * the data from Persistent Store therefore taking this object
		 * out of the fault state
		 *
		 * @see MManagedObject::isFault()
		 *
		 * @return void
		 */
		public function fireFault() {
			if ($this->isFault()) {
				$request = new MFaultRequest($this->context());
				$request->addFault($this);
				if ($this->context()->persistentStoreCoordinator()->executeRequest($request)->count() > 0) {
					throw new MManagedObjectException($this, S("Error while fetching data for fault!"));
				}
			}
		}
		
		/**
		 * Returns weather or not this object has unsaved changes
		 *
		 * @return bool Weather or not this object has unsaved changes
		 */
		public function hasChanges() {
			return (!$this->_isSaving() && ($this->_hasUpdatedData() || $this->_hasInsertedRelationships() || $this->_hasRemovedRelationships()));
		}
		
		/**
		 * This method is used to persist all of it's changes internally
		 * after a save is performed to the Persistent Store
		 *
		 * @return void
		 */
		public function persistChanges() {
			foreach ($this->updatedData->allKeys()->toArray() as $key) {
				$this->data->setObjectForKey($key, $this->updatedData->objectForKey($key));
			}
			foreach ($this->insertedRelationships->allKeys()->toArray() as $key) {
				if (!$this->relationships->hasObjectForKey($key)) {
					$this->relationships->setObjectForKey($key, new MMutableArray());
				}
				$this->relationships->objectForKey($key)->appendArray($this->insertedRelationships->objectForKey($key));
			}
			foreach ($this->removedRelationships->allKeys()->toArray() as $key) {
				if ($this->relationships->hasObjectForKey($key)) {
					$this->relationships->objectForKey($key)->subtractArray($this->removedRelationships->objectForKey($key));
				}
			}
			$this->discardChanges();
		}
		
		/**
		 * Discards any changes made to this object and reverts it to it's
		 * original state
		 *
		 * @return void
		 */
		public function discardChanges() {
			$this->updatedData->removeAllObjects();
			$this->insertedRelationships->removeAllObjects();
			$this->removedRelationships->removeAllObjects();
		}
		
		/******************** MObject Methods ********************/
		
		/**
		 * 
		 */
		public function equals(MMangoObject $object) {
			return $this->hash() == $object->hash();
		}
		
		/**
		 * 
		 */
		public function hash() {
			$str = new MMutableString();
			$str->appendString($this->entity()->toString());
			if ($this->objectID() != MManagedObject::UNKNOWN_ID) {
				$str->appendString($this->_data()->toString());
			}
			if ($this->hasChanges()) {
				$str->appendString($this->_updatedData()->toString());
			}
			return $str->hash();
		}
		
		/**
		 * 
		 */
		public function toString() {
			return Sf("[MManagedObject=>%s <%s>]", $this->className(), $this->hash());
		}
		
	}

?>