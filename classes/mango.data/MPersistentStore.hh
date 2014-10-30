<?hh

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
 * @todo Add support database engines other than just MySQL
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
 */
class MPersistentStore extends MObject {
	
	protected MString $_url;
	protected ?MDictionary $_options;
	protected MString $_username;
	protected MString $_password;
	protected ?MPersistentStoreDelegate $_delegate;
	protected ?MPersistentStoreCoordinator $_persistentStoreCoordinator;
	protected ?PDO $_connection;
	
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
	public function __construct(MString $url, ?MDictionary $options = null) {
		parent::__construct();
		$this->_url = $url;
		$this->_options = $options;
		$this->_username = S("");
		$this->_password = S("");
		$this->_delegate = null;
		$this->_persistentStoreCoordinator = null;
		$this->_connection = null;
	}
	
	/******************** Protected ********************/
	
	/**
	 * @internal
	 *
	 * @return PDO
	 */
	protected function connection() : PDO {
		$this->_connection = MInit($this->_connection, function () : PDO {
			$pdo = new PDO(
				$this->url()->stringValue(),
				$this->username()->stringValue(),
				$this->password()->stringValue(),
				array(PDO::ATTR_PERSISTENT => true)
			);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $pdo;
		});
		return $this->_connection;
	}
	
	/**
	 * @internal
	 *
	 * @return MString
	 */
	protected function databaseVersion() : ?MString {
		try {
			$result = $this->connection()->query("SELECT `value` FROM `Z_METADATA` WHERE `key` = 'version'");
			if (count($result) > 0) {
				$object = $result->fetchObject();
				if ($object !== null) {
					return S($object->value);
				} else {
					return null;
				}
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}
	}
	
	/**
	 * @internal
	 *
	 * @return MString
	 */
	protected function createTableQuery(MString $tableName, MArray<MString> $fields, ?MString $primaryKey = null) : MString {
		$tableQuery = new MMutableString();
		$tableQuery->appendLine(Sf("CREATE TABLE `%s` (", $tableName));
		
		$tableQuery->appendString($fields->componentsJoinedByString(S(",\n")));
		
		if ($primaryKey !== null) {	
			if ($primaryKey->isEmpty()) {
				$tableQuery->appendFormat(",\nPRIMARY KEY (`%s`)", $primaryKey);
			}
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
	protected function unpackData(MDictionary<MString, MValue> $data, MEntityDescription $entity) : MDictionary<string, mixed> {
		$unpackedData = new MMutableDictionary();
		foreach ($data->keyedTraversable() as $key => $value) {
			$unboxedValue = null;
			$property = $entity->propertyWithName($key);
			if ($property !== null) {
				if ($property->type() == MPropertyType::String) {
					if ($value instanceof MString) {
						$unboxedValue = $value->stringValue();
					}
				} else if ($property->type() == MPropertyType::Integer) {
					if ($value instanceof MInteger) {
						$unboxedValue = $value->intValue();
					}
				} else if ($property->type() == MPropertyType::Float) {
					if ($value instanceof MFloat) {
						$unboxedValue = $value->floatValue();
					}
				} else if ($property->type() == MPropertyType::Boolean) {
					if ($value instanceof MBoolean) {
						$unboxedValue = $value->boolValue();
					}
				} else if ($property->type() == MPropertyType::Date) {
					if ($value instanceof MDate) {
						$unboxedValue = $value->timestamp();
					}
				} else if ($property->type() == MPropertyType::Binary) {
					if ($value instanceof MData) {
						$unboxedValue = $value->getBytes();
					}
				}
			}
			if ($unboxedValue !== null) {
				$unpackedData->setObjectForKey($key->stringValue(), $unboxedValue);
			} else {
				throw new MPersistentStoreException(Sf("Failed to unpack data for property '%s'!", $key));
			}
		}
		return $unpackedData;
	}
	
	/**
	 * @internal
	 *
	 * @return void
	 */
	protected function saveManagedObject(MManagedObject $object) : void {
		if ($object->hasChanges()) {
			// Inform the object that it is about to be saved
			$object->_willStartSaving();
			
			// Save object's updated data
			if ($object->_hasUpdatedData()) {
				$data = $this->unpackData($object->_updatedData(), $object->entity());
				
				if ($object->objectID() == MManagedObject::UnknownID) {
					$statements = A(array_fill(0, $data->count(), S("?")));
					
					$query = Sf(
						"INSERT INTO `%s` (`%s`) VALUES (%s);",
						$object->entity()->plural(),
						$data->keys()->componentsJoinedByString(S("`, `")),
						$statements->componentsJoinedByString(S(", "))
					);
					
					$statement = $this->connection()->prepare($query->stringValue());
					if (!$statement->execute($data->objects()->toArray())) {
						throw new MPersistentStoreException(Sf("Could not save entity [%s]", $object->entity()->name()));
					}
					
					$object->_setObjectID((int)$this->connection()->lastInsertId());
				} else {
					$statements = new MMutableArray();
					foreach ($data->keys()->toArray() as $field) {
						$statements->addObject(Sf("`%s` = ?", $field));
					}
					
					$query = Sf(
						"UPDATE `%s` SET %s WHERE `objectID` = %s;",
						$object->entity()->plural(),
						$statements->componentsJoinedByString(S(", ")),
						$object->objectID()
					);
					
					$statement = $this->connection()->prepare($query->stringValue());
					if (!$statement->execute($data->objects()->toArray())) {
						throw new MPersistentStoreException(Sf("Could not save entity [%s]", $object->entity()->name()));
					}
				}
			}
			
			// Save object's inserted relationships
			if ($object->_hasInsertedRelationships()) {
				foreach ($object->_insertedRelationships()->keyedTraversable() as $relationshipName => $relationshipObjects) {
					if (!$object->_isSavingInsertedObjectsForRelationshipName($relationshipName)) {
						$relationship = $object->entity()->relationshipWithName($relationshipName);

						if ($relationship !== null) {
							foreach ($relationshipObjects->traversable() as $relationshipObject) {
								$inverseRelationship = $relationship->inverseRelationship();
								if ($inverseRelationship !== null) {
									$relationshipObject->_willSaveInsertedObjectsForRelationshipName($inverseRelationship->name());
								}
								
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
								
								if ($inverseRelationship !== null) {
									$relationshipObject->_didSaveInsertedObjectsForRelationshipName($inverseRelationship->name());
								}
							}
						}
					}
				}
			}
			
			// Delete object's removed relationships
			if ($object->_hasRemovedRelationships()) {
				foreach ($object->_removedRelationships()->keyedTraversable() as $relationshipName => $relationshipObjects) {
					if (!$object->_isSavingRemovedObjectsForRelationshipName($relationshipName)) {
						$relationship = $object->entity()->relationshipWithName($relationshipName);
						
						if ($relationship !== null) {
							foreach ($relationshipObjects->traversable() as $relationshipObject) {
								$inverseRelationship = $relationship->inverseRelationship();
								if ($inverseRelationship !== null) {
									$relationshipObject->_willSaveRemovedObjectsForRelationshipName($inverseRelationship->name());
								}
								
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
							
								if ($inverseRelationship !== null) {
									$relationshipObject->_didSaveRemovedObjectsForRelationshipName($inverseRelationship->name());
								}
							}
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
	protected function deleteManagedObject(MManagedObject $object) : void {
		$object->discardChanges();
		
		// Remove all object's relationships
		foreach ($object->entity()->relationships()->traversable() as $relationship) {
			$relationshipObjects = $object->objectForAttribute($relationship);
			if ($relationshipObjects instanceof MArray) {
				foreach ($relationshipObjects->traversable() as $relationshipObject) {
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
	 * @return MArray<MManagedObject>
	 */
	protected function executeFaultRequest(MFaultRequest $request) : MArray<MManagedObject> {
		$failedFaults = new MMutableArray();
		
		foreach ($request->faults()->traversable() as $fault) {
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
			if ($row !== null) {
				unset($row['objectID']);
				
				// Set the object's data
				foreach ($row as $key => $value) {
					MExec($fault->entity()->attributeWithName(S($key)), $attribute ==> {
						if ($attribute instanceof MEntityDescriptionProperty) {
							if ($attribute->type() == MPropertyType::String) {
								$data->setObjectForKey(S($key), S($value));
							} else if ($attribute->type() == MPropertyType::Integer) {
								$data->setObjectForKey(S($key), I($value));
							} else if ($attribute->type() == MPropertyType::Float) {
								$data->setObjectForKey(S($key), F($value));
							} else if ($attribute->type() == MPropertyType::Boolean) {
								$data->setObjectForKey(S($key), B($value));
							} else if ($attribute->type() == MPropertyType::Date) {
								$data->setObjectForKey(S($key), MDate::parse($value));
							} else if ($attribute->type() == MPropertyType::Binary) {
								$data->setObjectForKey(S($key), new MData($value));
							}
						} else {
							throw new MPersistentStoreException(S("Attribute appears to be a relationship, expecting a property!"));
						}
					}, function () : void {
						throw new MPersistentStoreException(S("Databse structure appears to be corrupted or is incompatible with this model version!"));
					});
				}
				
				$fault->_setData($data);
				
				foreach ($fault->entity()->relationships()->traversable() as $relationship) {
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
						MExec($this->persistentStoreCoordinator(), $persistentStoreCoordinator ==> {
							MExec($persistentStoreCoordinator->model()->entityWithName($relationship->type()), $entity ==> {
								MExec($this->fetchObjectWithObjectID($entity, (int)$relationshipRow[$relationship->inverseColumnName()->intValue()]), $relationshipObject ==> {
									$relationshipObjects->addObject($relationshipObject);
								}, () ==> {
									throw new MPersistentStoreException(Sf("Object with ID [%s] is missing from the data store and could not be initialized!", $relationshipRow['objectID']));
								});
							}, () ==> {
								throw new MPersistentStoreException(Sf("Could not find entity named '%s'", $relationship->type()));
							});
						}, () ==> {
							throw new MPersistentStoreException(S("Persistent Store Coordinator not defined!"));
						});

						$relationships->setObjectForKey($relationship->name(), $relationshipObjects);
					}
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
	protected function executeFetchRequest(MFetchRequest $request) : MArray<MManagedObject> {
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
	protected function executeSaveRequest(MSaveRequest $request) : MArray {
		$affectedObjects = new MMutableArray();
		
		// Save new objects
		$insertManagedObjects = $request->insertManagedObjects();
		if ($insertManagedObjects !== null) {
			foreach ($insertManagedObjects->traversable() as $object) {
				$this->saveManagedObject($object);
				$affectedObjects->addObject($object);
			}
		}
		
		// Save updated objects
		$updateManagedObjects = $request->updateManagedObjects();
		if ($updateManagedObjects != null) {
			foreach ($updateManagedObjects->traversable() as $object) {
				$this->saveManagedObject($object);
				$affectedObjects->addObject($object);
			}
		}
		
		// Remove deleted objects
		$deleteManagedObjects = $request->deleteManagedObjects();
		if ($deleteManagedObjects !== null) {
			foreach ($deleteManagedObjects->traversable() as $object) {
				$this->deleteManagedObject($object);
				$affectedObjects->addObject($object);
			}
		}
		
		return $affectedObjects;
	}
	
	/**
	 * @internal
	 *
	 * @return void
	 */
	protected function createDatabaseStructure() : void {		
		MExec($this->persistentStoreCoordinator(), $persistentStoreCoordinator ==> {
			$this->connection()->beginTransaction();

			$model = $persistentStoreCoordinator->model();

			$tables = new MMutableDictionary();
			$relationshipTables = new MMutableDictionary();
			
			foreach ($model->entities()->traversable() as $entity) {
				$fields = new MMutableArray();
				$fields->addObject(S("`objectID` INT NOT NULL AUTO_INCREMENT"));
				
				foreach ($entity->attributes()->traversable() as $attribute) {
					if ($attribute instanceof MEntityDescriptionProperty) {
						$type = "MEDIUMTEXT";
						if ($attribute->type() == MPropertyType::Integer) {
							$type = "INT";
						} else if ($attribute->type() == MPropertyType::Float) {
							$type = "FLOAT";
						} else if ($attribute->type() == MPropertyType::Boolean) {
							$type = "TINYINT";
						} else if ($attribute->type() == MPropertyType::Date) {
							$type = "INT";
						} else if ($attribute->type() == MPropertyType::Binary) {
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
			
			foreach ($tables->keyedTraversable() as $tableName => $table) {
				$tableQuery = $this->createTableQuery($tableName, $table, S("objectID"));
				$this->connection()->exec($tableQuery->stringValue());
			}
	
			foreach ($relationshipTables->keyedTraversable() as $tableName => $fields) {
				$tableQuery = $this->createTableQuery($tableName, $fields, S("objectID"));
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
			$statement->execute(array($persistentStoreCoordinator->model()->version()->stringValue()));
			
			$success = $this->connection()->commit();
			
			if (!$success) {
				throw new MPersistentStoreException(S("Could not create database structure!"));
			}
		
			MExec($this->delegate(), $delegate ==> {
				$delegate->didCreatePersistentStore($this);
			});
		}, () ==> {
			throw new MPersistentStoreException(S("Undefined persistent store coordinator!"));
		});
	}
	
	/**
	 * @internal
	 *
	 * @return void
	 */
	protected function performMigration() : void {
		throw new MPersistentStoreException(S("Persistent Store Migration not supported!"));
	}
	
	/**
	 * @internal
	 *
	 * @return void
	 */
	protected function ensureDatabaseConsistency() : void {
		$dbVersion = $this->databaseVersion();
		if ($dbVersion !== null) {
			MExec($this->persistentStoreCoordinator(), $persistentStoreCoordinator ==> {
				if (!$dbVersion->equals($persistentStoreCoordinator->model()->version())) {
					$this->performMigration();
				}
			}, () ==> {
				throw new MPersistentStoreException(S("Undefined persistent store coordinator!"));
			});
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
	public function url() : MString {
		return $this->_url;
	}
	
	/**
	 * Returns a Dictionary containing the options used to connect to the
	 * Persistent Store
	 *
	 * @return MDictionary The Dictionary containing the options used to connect
	 * to the Persistent Store
	 */
	public function options() : ?MDictionary {
		return $this->_options;
	}
	
	/**
	 * Sets the username used to login to the Persistent Store
	 *
	 * @param MString $username The username to use when connecting to
	 * the Persistent Store
	 *
	 * @return void
	 */
	public function setUsername(MString $username) : void {
		$this->_username = $username;
	}
	
	/**
	 * Returns the username used to login to the Persistent Store
	 *
	 * @return MString The username used to login to the Persistent Store
	 */
	public function username() : MString {
		return $this->_username;
	}
	
	/**
	 * Sets the password to be used to login to the Persistent Store
	 *
	 * @param MString $password The password to use when connecting to
	 * the Persistent Store
	 *
	 * @return void
	 */
	public function setPassword(MString $password) : void {
		$this->_password = $password;
	}
	
	/**
	 * Returns the password used to login to the Persistent Store
	 *
	 * @return MString The password used to login to the Persistent Store
	 */
	public function password() : MString {
		return $this->_password;
	}
	
	/**
	 * Sets the delegate for this Persistent Store
	 *
	 * @param MPersistentStoreDelegate $delegate The new delegate for this Persistent
	 * Store
	 *
	 * @return void
	 */
	public function setDelegate(MPersistentStoreDelegate $delegate) : void {
		$this->_delegate = $delegate;
	}
	
	/**
	 * Returns the delegate for this Persistent Store
	 *
	 * @return ?MPersistentStoreDelegate The delegate for this Persistent Store
	 */
	public function delegate() : ?MPersistentStoreDelegate {
		return $this->_delegate;
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
	public function setPersistentStoreCoordinator(?MPersistentStoreCoordinator $persistentStoreCoordinator) : void {
		$this->_persistentStoreCoordinator = $persistentStoreCoordinator;
	}
	
	/**
	 * Returns the Persistent Store Coordinator this Persistent Store belongs to
	 *
	 * @return ?MPersistentStoreCoordinator The Persistent Store Coordinator this
	 * Persistent Store belongs to
	 */
	public function persistentStoreCoordinator() : ?MPersistentStoreCoordinator {
		return $this->_persistentStoreCoordinator;
	}
	
	/******************** Methods ********************/
	
	/**
	 * This method executes a request in the Persistent Store and returns the
	 * results as an Array
	 *
	 * @param MPersistentStoreRequest $request The Request to perform
	 *
	 * @return MArray<MManagedObject> An Array containing the results of the request
	 */
	public function executeRequest(MPersistentStoreRequest $request) : MArray<MManagedObject> {
		$this->ensureDatabaseConsistency();
		if ($request instanceof MFetchRequest) {
			return $this->executeFetchRequest($request);
		} else if ($request instanceof MFaultRequest) {
			return $this->executeFaultRequest($request);
		} else if ($request instanceof MSaveRequest) {
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
	public function fetchObjectWithObjectID(MEntityDescription $entity, int $objectID) : ?MManagedObject {
		$object = null;

		$query = Sf(
			"SELECT `objectID` FROM `%s` WHERE `objectID` = ?",
			$entity->plural()
		);
		
		$statement = $this->connection()->prepare($query->stringValue());
		$statement->bindParam(1, $objectID);
		$statement->execute();
		
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		if ($row != null) {
			MExec($this->persistentStoreCoordinator(), $persistentStoreCoordinator ==> {
				MExec($persistentStoreCoordinator->managedObjectContext(), $managedObjectContext ==> {
					$object = $managedObjectContext->newObjectForEntity($entity, (int)$row['objectID']);
				}, () ==> {
					throw new MPersistentStoreException(S("Undefined managed object context!"));
				});
			}, () ==> {
				throw new MPersistentStoreException(S("Undefined persistent store coordinator!"));
			});
		}

		return $object;
	}
	
}

