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
	
	import('mango.data.exceptions.*');
	import('mango.system.*');
	
	/**
	 * 
	 * @todo Add support database engines other than just MySQL
	 *
	 */
	
	/**
	 * This class represents a Persistent Store in which to persist your
	 * Managed Objects
	 *
	 * A Persistent Store connects to a Managed Object Context by using a
	 * Persistent Store Coordinator which coordinates a collection of
	 * Persistent Stores
	 *
	 * @see MManagedObject
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
	class MPersistentStore extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $url;
		protected $options;
		protected $username;
		protected $password;
		protected $delegate;
		protected $persistentStoreCoordinator;
		protected $connection;
		
		/**
		 * Creates a new instance of the MPersistentStore class which represents a 
		 * Persistent Store identified by the Persistent Store url
		 *
		 * At the moment only MySQL is supported as a Persistent Store. In the future
		 * support for other database mechanisms will be added
		 *
		 * The $url parameter currently uses the same format as PHP's PDO.
		 *
		 * @see http://php.net/manual/en/book.pdo.php
		 *
		 * @param MString $url A url which identifies your Persistent Store's location
		 * This URL uses the same format used by PHP's PDO to connect to a database
		 * @param MDictionary $options A Dictionary of options used to connect to the
		 * Persistent Store
		 *
		 * @return MPersistentStore The newly created Persistent Store instance
		 */
		public function __construct(MString $url, MDictionary $options = null) {
			parent::__construct();
			
			$this->url = $url;
			$this->options = $options;
			$this->username = S("");
			$this->password = S("");
			$this->delegate = null;
			$this->persistentStoreCoordinator = null;
			$this->connection = null;
		}
		
		/******************** Protected ********************/
		
		/**
		 * @internal
		 *
		 * @return PDO
		 */
		protected function connection() {
			if ($this->connection == null) {
				$this->connection = new PDO(
					$this->url()->stringValue(),
					$this->username()->stringValue(),
					$this->password()->stringValue(),
					array(PDO::ATTR_PERSISTENT => true)
				);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			return $this->connection;
		}
		
		/**
		 * @internal
		 *
		 * @return MString
		 */
		protected function databaseVersion() {
			try {
				$result = $this->connection()->query("SELECT `value` FROM `Z_METADATA` WHERE `key` = 'version'");
				if (count($result) > 0) {
					$object = $result->fetchObject();
					if (!is_null($object)) {
						return S($object->value);
					} else {
						return null;
					}
				} else {
					return null;
				}
			} catch (PDOException $e) {
				return null;
			}
		}
		
		/**
		 * @internal
		 *
		 * @return MString
		 */
		protected function createTableQuery(MString $tableName, MArray $fields, MString $primaryKey = null) {
			$tableQuery = new MMutableString();
			$tableQuery->appendLine(Sf("CREATE TABLE `%s` (", $tableName));
			
			$tableQuery->appendString($fields->componentsJoinedByString(S(",\n")));
			
			if (!empty($primaryKey)) {
				$tableQuery->appendFormat(",\nPRIMARY KEY (`%s`)", $primaryKey);
			}
			
			$tableQuery->appendLine(S("\n);"));
			$tableQuery->appendLine(S(""));
			
			return $tableQuery;
		}
		
		/**
		 * @internal
		 *
		 * @return MDictionary
		 */
		protected function unpackData(MDictionary $data, MEntityDescription $entity) {
			$unpackedData = new MMutableDictionary();
			foreach ($data->allKeys()->toArray() as $key) {
				$value = $data->objectForKey($key);
				$unboxedValue = null;
				if (!is_null($value)) {
					$property = $entity->attributeWithName($key);
					if ($property->type() == MEntityDescriptionProperty::StringType) {
						$unboxedValue = $value->stringValue();
					} else if ($property->type() == MEntityDescriptionProperty::IntegerType) {
						$unboxedValue = $value->intValue();
					} else if ($property->type() == MEntityDescriptionProperty::FloatType) {
						$unboxedValue = $value->floatValue();
					} else if ($property->type() == MEntityDescriptionProperty::BooleanType) {
						$unboxedValue = $value->boolValue();
					} else if ($property->type() == MEntityDescriptionProperty::DateType) {
						$unboxedValue = $value->timestamp();
					} else if ($property->type() == MEntityDescriptionProperty::BinaryType) {
						$unboxedValue = $value->getBytes();
					}
				}
				$unpackedData->setObjectForKey($key->stringValue(), $unboxedValue);
			}
			return $unpackedData;
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function saveManagedObject(MManagedObject $object) {
			if ($object->hasChanges()) {
				// Inform the object that it is about to be saved
				$object->_willStartSaving();
				
				// Save object's updated data
				if ($object->_hasUpdatedData()) {
					$data = $this->unpackData($object->_updatedData(), $object->entity());
					
					if ($object->objectID() == MManagedObject::UNKNOWN_ID) {
						$statements = A(array_fill(0, $data->count(), S("?")));
						
						$query = Sf(
							"INSERT INTO `%s` (`%s`) VALUES (%s);",
							$object->entity()->plural(),
							$data->allKeys()->componentsJoinedByString(S("`, `")),
							$statements->componentsJoinedByString(S(", "))
						);
						
						$statement = $this->connection()->prepare($query->stringValue());
						if (!$statement->execute($data->allObjects()->toArray())) {
							throw new MPersistentStoreException(Sf("Could not save entity [%s]", $object->entity()->name()));
						}
						
						$object->_setObjectID((int)$this->connection()->lastInsertId());
					} else {
						$statements = new MMutableArray();
						foreach ($data->allKeys()->toArray() as $field) {
							$statements->addObject(Sf("`%s` = ?", $field));
						}
						
						$query = Sf(
							"UPDATE `%s` SET %s WHERE `objectID` = %s;",
							$object->entity()->plural(),
							$statements->componentsJoinedByString(S(", ")),
							$object->objectID()
						);
						
						$statement = $this->connection()->prepare($query->stringValue());
						if (!$statement->execute($data->allObjects()->toArray())) {
							throw new MPersistentStoreException(Sf("Could not save entity [%s]", $object->entity()->name()));
						}
					}
				}
				
				// Save object's inserted relationships
				if ($object->_hasInsertedRelationships()) {
					foreach ($object->_insertedRelationships()->allKeys()->toArray() as $relationshipName) {
						if (!$object->_isSavingInsertedObjectsForRelationshipName($relationshipName)) {
							$relationshipObjects = $object->_insertedRelationships()->objectForKey($relationshipName);
							$relationship = $object->entity()->attributeWithName($relationshipName);

							foreach ($relationshipObjects->toArray() as $relationshipObject) {
								$relationshipObject->_willSaveInsertedObjectsForRelationshipName($relationship->inverseRelationship()->name());
								
								$this->saveManagedObject($relationshipObject);
								
								$query = Sf(
									"INSERT INTO `%s` (`%s`, `%s`) VALUES (%s);",
									$relationship->tableName(),
									$relationship->columnName(),
									$relationship->inverseColumnName(),
									S("?, ?")
								);
								
								$statement = $this->connection()->prepare($query->stringValue());
								if (!$statement->execute(array($object->objectID(), $relationshipObject->objectID()))) {
									throw new MPersistentStoreException(Sf("Could not save relationship [%s] on entity [%s]", $relationshipName, $object->entity()->name()));
								}
								
								$relationshipObject->_didSaveInsertedObjectsForRelationshipName($relationship->inverseRelationship()->name());
							}
						}
					}
				}
				
				// Delete object's removed relationships
				if ($object->_hasRemovedRelationships()) {
					foreach ($object->_removedRelationships()->allKeys()->toArray() as $relationshipName) {
						if (!$object->_isSavingRemovedObjectsForRelationshipName($relationshipName)) {
							$relationshipObjects = $object->_removedRelationships()->objectForKey($relationshipName);
							$relationship = $object->entity()->attributeWithName($relationshipName);
							
							foreach ($relationshipObjects->toArray() as $relationshipObject) {
								$relationshipObject->_willSaveRemovedObjectsForRelationshipName($relationship->inverseRelationship()->name());
								
								$this->saveManagedObject($relationshipObject);
								
								$query = Sf(
									"DELETE FROM `%s` WHERE `%s` = ?;",
									$relationship->tableName(),
									$relationship->inverseColumnName()
								);
								
								$statement = $this->connection()->prepare($query->stringValue());
								if (!$statement->execute(array($relationshipObject->objectID()))) {
									throw new MPersistentStoreException(Sf("Could not remove relationship [%s] on entity [%s]", $relationshipName, $object->entity()->name()));
								}
								
								$relationshipObject->_didSaveRemovedObjectsForRelationshipName($relationship->inverseRelationship()->name());
							}
						}
					}
				}
				
				// Tell the object to persit it's changes
				$object->persistChanges();
				
				// Inform the object that it has finished being saved
				$object->_didFinishSaving();
			}
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function deleteManagedObject(MManagedObject $object) {
			$object->discardChanges();
			
			// Remove all object's relationships
			foreach ($object->entity()->relationships() as $relationship) {
				$relationshipObjects = $object->objectForAttribute($relationship);
				if ($relationshipObjects) {
					foreach ($relationshipObjects->toArray() as $relationshipObject) {
						$object->removeObjectFromRelationship($relationship, $relationshipObject);
					}
				}
			}
			
			$this->saveManagedObject($object);
			
			// Remove object from the database
			$query = Sf(
				"DELETE FROM `%s` WHERE `objectID` = ?",
				$object->entity()->plural()
			);
			
			$statement = $this->connection()->prepare($query->stringValue());
			if (!$statement->execute(array($object->objectID()))) {
				throw new MPersistentStoreException(Sf("Could not delete entity [%s]", $object->entity()->name()));
			}
		}
		
		/**
		 * @internal
		 *
		 * @return MArray
		 */
		protected function executeFaultRequest(MFaultRequest $request) {
			$failedFaults = new MMutableArray();
		
			foreach ($request->faults()->toArray() as $fault) {
				$data = new MMutableDictionary();
				$relationships = new MMutableDictionary();
				
				$query = Sf(
					"SELECT * FROM `%s` WHERE `objectID` = :objectID LIMIT 1",
					$fault->entity()->plural()
				);
				
				$id = $fault->objectID();
				
				$statement = $this->connection()->prepare($query->stringValue());
				$statement->bindParam(':objectID', $id, PDO::PARAM_INT);
				$statement->execute();
				
				$row = $statement->fetch(PDO::FETCH_ASSOC);
				if ($row != null) {
					unset($row['objectID']);
					
					// Set the object's data
					foreach ($row as $key => $value) {
						$property = $fault->entity()->attributeWithName(S($key));
						if ($property) {
							$boxedValue = null;
							if ($value != null) {
								if ($property->type() == MEntityDescriptionProperty::StringType) {
									$boxedValue = new MString($value);
								} else if ($property->type() == MEntityDescriptionProperty::IntegerType) {
									$boxedValue = new MNumber($value);
								} else if ($property->type() == MEntityDescriptionProperty::FloatType) {
									$boxedValue = new MNumber($value);
								} else if ($property->type() == MEntityDescriptionProperty::BooleanType) {
									$boxedValue = new MNumber($value);
								} else if ($property->type() == MEntityDescriptionProperty::DateType) {
									$boxedValue = MDate::parse($value);
								} else if ($property->type() == MEntityDescriptionProperty::BinaryType) {
									$boxedValue = new MData($value);
								}
							}
							$data->setObjectForKey(S($key), $boxedValue);
						} else {
							throw new MPersistentStoreException(S("Database structure incompatible with this model version!"));
						}
					}
					
					$fault->_setData($data);
					
					foreach ($fault->entity()->relationships()->toArray() as $relationship) {
						$relationshipQuery = Sf(
							"SELECT * FROM `%s` WHERE `%s` = ?;",
							$relationship->tableName(),
							$relationship->columnName()
						);
						
						$objectID = $fault->objectID();
						$statement = $this->connection()->prepare($relationshipQuery->stringValue());
						$statement->bindParam(1, $objectID);
						$statement->execute();

						$relationshipObjects = new MMutableArray();
						foreach ($statement->fetchAll() as $relationshipRow) {
							$relationshipObject = $this->fetchObjectWithObjectID($this->persistentStoreCoordinator()->model()->entityWithName($relationship->type()), (int)$relationshipRow[$relationship->inverseColumnName()->stringValue()]);
							if ($relationshipObject) {
								$relationshipObjects->addObject($relationshipObject);
							} else {
								MLog("Warning: Object with ID [%s] is missing from the data store and could not be initialized!", $relationshipRow['objectID']);
							}
						}
						$relationships->setObjectForKey($relationship->name(), $relationshipObjects);
					}
					
					$fault->_setRelationships($relationships);
					
				} else {
					$failedFaults->addObject($fault);
				}
			}
			
			return $failedFaults;
		}
		
		/**
		 * @internal
		 *
		 * @return MArray
		 */
		protected function executeFetchRequest(MFetchRequest $request) {
			$results = new MMutableArray();
			$query = new MMutableString();
			
			$query->appendString(Sf(
				"SELECT `objectID` FROM `%s`",
				$request->entity()->plural()
			));
			if ($request->predicate() != null) {
				$query->appendString(Sf(
					" WHERE %s",
					$request->predicate()
				));
			}
			$query->appendString(S(";"));
			
			foreach ($this->connection()->query($query->stringValue()) as $row) {
				$object = $request->context()->newObjectForEntity($request->entity(), (int)$row['objectID']);
				$results->addObject($object);
			}
			
			return $results;
		}
		
		/**
		 * @internal
		 *
		 * @return MArray
		 */
		protected function executeSaveRequest(MSaveRequest $request) {
			$affectedObjects = new MMutableArray();
			
			// Save new objects
			foreach ($request->insertManagedObjects()->toArray() as $object) {
				$this->saveManagedObject($object);
				$affectedObjects->addObject($object);
			}
			
			// Save updated objects
			foreach ($request->updateManagedObjects()->toArray() as $object) {
				$this->saveManagedObject($object);
				$affectedObjects->addObject($object);
			}
			
			// Remove deleted objects
			foreach ($request->deleteManagedObjects()->toArray() as $object) {
				$this->deleteManagedObject($object);
				$affectedObjects->addObject($object);
			}
			
			return $affectedObjects;
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function createDatabaseStructure() {
			$this->connection()->beginTransaction();
			
			$model = $this->persistentStoreCoordinator()->model();
			
			$tables = new MMutableDictionary();
			$relationshipTables = new MMutableDictionary();
			
			foreach ($model->entities()->toArray() as $entity) {
				$fields = new MMutableArray();
				$fields->addObject(S("`objectID` INT NOT NULL AUTO_INCREMENT"));
				
				foreach ($entity->attributes()->toArray() as $attribute) {
					if ($attribute instanceof MEntityDescriptionProperty) {
						$type = "MEDIUMTEXT";
						if ($attribute->type() == MEntityDescriptionProperty::IntegerType) {
							$type = "INT";
						} else if ($attribute->type() == MEntityDescriptionProperty::FloatType) {
							$type = "FLOAT";
						} else if ($attribute->type() == MEntityDescriptionProperty::BooleanType) {
							$type = "TINYINT";
						} else if ($attribute->type() == MEntityDescriptionProperty::DateType) {
							$type = "INT";
						} else if ($attribute->type() == MEntityDescriptionProperty::BinaryType) {
							$type = "LONGBLOB";
						}
						
						$fields->addObject(Sf(
							"`%s` %s",
							$attribute->name(),
							$type
						));
					} else if ($attribute instanceof MEntityDescriptionRelationship) {
						$tableName = $attribute->tableName();
						
						if ($relationshipTables->objectForKey($tableName) == null) {
							$relationshipFields = new MMutableArray();
							$relationshipFields->addObject(S("`objectID` INT NOT NULL AUTO_INCREMENT"));
							$relationshipFields->addObject(Sf(
								"`%s` INT NOT NULL",
								$attribute->columnName()
							));
							$relationshipFields->addObject(Sf(
								"`%s` INT NOT NULL",
								$attribute->inverseColumnName()
							));
							
							$relationshipTables->setObjectForKey($tableName, $relationshipFields);
						}
					}
				}
				
				$tableName = $entity->plural();
				if ($tables->objectForKey($tableName) == null) {
					$tables->setObjectForKey($tableName, $fields);
				} else {
					throw new MPersistentStoreException(Sf("Duplicate entity name ('%s')!", $entity->name()));
				}
			}
			
			foreach ($tables->allKeys()->toArray() as $tableName) {
				$tableQuery = $this->createTableQuery($tableName, $tables->objectForKey($tableName), S("objectID"));
				$this->connection()->exec($tableQuery->stringValue());
			}
			foreach ($relationshipTables->allKeys()->toArray() as $tableName) {
				$tableQuery = $this->createTableQuery($tableName, $relationshipTables->objectForKey($tableName), S("objectID"));
				$this->connection()->exec($tableQuery->stringValue());
			}
			
			$tableQuery = $this->createTableQuery(S("Z_METADATA"), A(
				S("`id` INT NOT NULL AUTO_INCREMENT"),
				S("`key` MEDIUMTEXT"),
				S("`value` MEDIUMTEXT")),
				S("id")
			);
			$this->connection()->exec($tableQuery);
			
			$statement = $this->connection()->prepare("INSERT INTO `Z_METADATA` (`key`, `value`) VALUES ('version', ?)");
			$statement->execute(array($this->persistentStoreCoordinator()->model()->version()->stringValue()));
			
			$success = $this->connection()->commit();
			
			if (!$success) {
				throw new MPersistentStoreException(S("Could not create database structure!"));
			}
			
			if ($this->delegate() != null) {
				$this->delegate()->didCreatePersistentStore($this);
			}
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function performMigration() {
			throw new MPersistentStoreException(S("Persistent Store Migration not supported!"));
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function ensureDatabaseConsistency() {
			$dbVersion = $this->databaseVersion();
			if ($dbVersion != null) {
				if (!$dbVersion->equals($this->persistentStoreCoordinator()->model()->version())) {
					$this->performMigration();
				}
			} else {
				$this->createDatabaseStructure();
			}
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the URL String used to connect to the Persistent Store
		 *
		 * @return MString The URL connection String for this Persistent Store
		 */
		public function url() {
			return $this->url;
		}
		
		/**
		 * Returns a Dictionary containing the options used to connect to the
		 * Persistent Store
		 *
		 * @return MDictionary The Dictionary containing the options used to connect
		 * to the Persistent Store
		 */
		public function options() {
			return $this->options;
		}
		
		/**
		 * Sets the username used to login to the Persistent Store
		 *
		 * @param MString $username The username to use when connecting to
		 * the Persistent Store
		 *
		 * @return void
		 */
		public function setUsername(MString $username) {
			$this->username = $username;
		}
		
		/**
		 * Returns the username used to login to the Persistent Store
		 *
		 * @return MString The username used to login to the Persistent Store
		 */
		public function username() {
			return $this->username;
		}
		
		/**
		 * Sets the password to be used to login to the Persistent Store
		 *
		 * @param MString $password The password to use when connecting to
		 * the Persistent Store
		 *
		 * @return void
		 */
		public function setPassword(MString $password) {
			$this->password = $password;
		}
		
		/**
		 * Returns the password used to login to the Persistent Store
		 *
		 * @return MString The password used to login to the Persistent Store
		 */
		public function password() {
			return $this->password;
		}
		
		/**
		 * Sets the delegate for this Persistent Store
		 *
		 * @param MPersistentStoreDelegate $delegate The new delegate for this Persistent
		 * Store
		 *
		 * @return void
		 */
		public function setDelegate(MPersistentStoreDelegate $delegate) {
			$this->delegate = $delegate;
		}
		
		/**
		 * Returns the delegate for this Persistent Store
		 *
		 * @return MPersistentStoreDelegate The delegate for this Persistent Store
		 */
		public function delegate() {
			return $this->delegate;
		}
		
		/**
		 * @internal
		 *
		 * Used internally to set the Persistent Store Coordinator this Persistent Store belongs to
		 *
		 * @param MPersistentStoreCoordinator $persistentStoreCoordinator The Persistent Store
		 * Coordinator to link this Persistent Store to
		 *
		 * @return void
		 */
		public function setPersistentStoreCoordinator(MPersistentStoreCoordinator $persistentStoreCoordinator) {
			$this->persistentStoreCoordinator = $persistentStoreCoordinator;
		}
		
		/**
		 * Returns the Persistent Store Coordinator this Persistent Store belongs to
		 *
		 * @return MPersistentStoreCoordinator The Persistent Store Coordinator this
		 * Persistent Store belongs to
		 */
		public function persistentStoreCoordinator() {
			return $this->persistentStoreCoordinator;
		}
		
		/******************** Methods ********************/
		
		/**
		 * This method executes a request in the Persistent Store and returns the
		 * results as an Array
		 *
		 * @param MPersistentStoreRequest $request The Request to perform
		 *
		 * @return MArray An Array containing the results of the request
		 */
		public function executeRequest(MPersistentStoreRequest $request) {
			$this->ensureDatabaseConsistency();
			if ($request->requestType() == MPersistentStoreRequest::FetchRequestType) {
				return $this->executeFetchRequest($request);
			} else if ($request->requestType() == MPersistentStoreRequest::FaultRequestType) {
				return $this->executeFaultRequest($request);
			} else if ($request->requestType() == MPersistentStoreRequest::SaveRequestType) {
				return $this->executeSaveRequest($request);
			} else {
				throw new MPersistentStoreException(S("Invalid request type"));
			}
		}
		
		/**
		 * Fetches an Object from the Persistent Store
		 *
		 * @param MEntityDescription $entity The entity of the Object you wish to fetch
		 * @param int $objectID The object ID of the Object to be fetched
		 *
		 * @return MManagedObject The retreived Managed Object instance
		 */
		public function fetchObjectWithObjectID(MEntityDescription $entity, $objectID) {
			MAssertTypes('MEntityDescription', $entity, 'int', $objectID);
			
			$query = Sf(
				"SELECT `objectID` FROM `%s` WHERE `objectID` = ?",
				$entity->plural()
			);
			
			$statement = $this->connection()->prepare($query->stringValue());
			$statement->bindParam(1, $objectID);
			$statement->execute();
			
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			if ($row != null) {
				$object = $this->persistentStoreCoordinator()->managedObjectContext()->newObjectForEntity($entity, (int)$row['objectID']);
				return $object;
			} else {
				return null;
			}
		}
		
	}

?>