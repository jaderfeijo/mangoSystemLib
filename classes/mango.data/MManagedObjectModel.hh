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
 * This class loads and manages the data necessary to describe a Data Model for use with
 * a Managed Object Context and a Persistent Store
 *
 * A Data Model or Managed Object Model consists of a series of Entities each containing
 * properties and relationships to each other.
 *
 * To the Persistent Store, each Entity is translated into a Table. To the Mango environment
 * each Entity is translated into an Object, namely an instance of MManagedObject
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MManagedObjectModel extends MObject {
	
	protected MFile $_modelFile;
	protected MString $_version;
	protected MMutableArray<MEntityDescription> $_entities;
	
	/**
	 * Constructs a new Model by loading data from a model file
	 *
	 * @param MFile $file The file containing information about this model to be loaded
	 * @param MString $version A String used to identify a specific version of the model
	 * to use, if null is specified, the default version specified inside the model file
	 * is used
	 *
	 * @return MManagedObjectModel The loaded Model instance
	 */
	public function __construct(MFile $modelFile, ?MString $version = null) {
		parent::__construct();
		
		$this->_modelFile = $modelFile;
		$this->_entities = new MMutableArray();
		
		$xmlModels = simplexml_load_file($modelFile->path()->stringValue());
		$this->_version = S((string)$xmlModels->'current-version');

		if ($version !== null) {
			$this->_version = $version;
		}

		$model = null;
		foreach ($xmlModels->model as $m) {
			if (S((string)$m['version'])->equals($this->version())) {
				$model = $m;
			}
		}
		
		if ($model !== null) {
			$relationshipsToLink = new MMutableDictionary();
			
			foreach ($model->entity as $entity) {
				$newEntity = new MEntityDescription(S((string)$entity['name']), S((string)$entity['plural']), S((string)$entity['class']));
				
				foreach ($entity->property as $property) {
					$newProperty = new MEntityDescriptionProperty($newEntity, S((string)$property['name']));
					
					if ((string)$property['type'] !== null) {
						$newProperty->setType(MPropertyTypeFromString(S($property['type'])));
					}
					if ((string)$property['defaultValue'] !== null) {
						$defaultValue = null;
						if ($newProperty->type() == MPropertyType::String) {
							$defaultValue = new MString((string)$property['defaultValue']);
						} else if ($newProperty->type() == MPropertyType::Integer) {
							$defaultValue = I(S($property['defaultValue'])->intValue());
						} else if ($newProperty->type() == MPropertyType::Float) {
							$defaultValue = F(S($property['defaultValue'])->floatValue());
						} else if ($newProperty->type() == MPropertyType::Boolean) {
							$defaultValue = B(S($property['defaultValue'])->boolValue());
						} else if ($newProperty->type() == MPropertyType::Date) {
							$defaultValue = MDate::parseString(S($property['defaultValue']));
						} else if ($newProperty->type() == MPropertyType::Binary) {
							// BinaryType's defaultValue is ignored, always null
						} else {
							throw new MModelParseErrorException($this->modelFile(), Sf("Invalid data type '%s'", $newProperty->type()));
						}
						$newProperty->setDefaultValue($defaultValue);
					}
				}
				
				foreach ($entity->relationship as $relationship) {
					$newRelationship = new MEntityDescriptionRelationship($newEntity, S((string)$relationship['name']), S((string)$relationship['type']));
					$newRelationship->setSingular(S((string)$relationship['singular']));
					
					if (strtolower((string)$relationship['to']) == "many") {
						$newRelationship->setTo(MRelationshipTo::Many);
					} else {
						$newRelationship->setTo(MRelationshipTo::One);
					}
					if ($relationship['inverse'] != null) {
						$relationshipsToLink->setObjectForKey($newRelationship, S((string)$relationship['inverse']));
					}
				}
					
				$this->addEntity($newEntity);
			}
			
			// Link relationships to their respective inverse relationships
			foreach ($relationshipsToLink->keyedTraversable() as $relationship => $inverse) {
				$inverseEntity = $this->entityWithName($relationship->type());
				if ($inverseEntity !== null) {
					$inverseRelationship = $inverseEntity->relationshipWithName($inverse);
					if ($inverseRelationship !== null) {
						$relationship->setInverseRelationship($inverseRelationship);
					} else {
						throw new MModelParseErrorException($this->modelFile(), Sf("Could not find the relationship named '%s' in entity '%s'", $inverse, $relationship->type()));
					}
				} else {
					throw new MModelParseErrorException($this->modelFile(), Sf("[%s->%s] Inverse relationship's entity named '%s' not defined in this model version", $relationship->entity()->name(), $relationship->name(), $relationship->type()));
				}
			}
		} else {
			throw new MModelVersionNotFoundException($this->modelFile());
		}
	}
	
	/******************** Protected ********************/
	
	/**
	 * @internal
	 *
	 * Used internally to add parsed entities to this Data Model
	 *
	 * @return void
	 */
	protected function addEntity(MEntityDescription $entity) : void {
		$this->_entities->addObject($entity);
	}
	
	/**
	 * @internal
	 *
	 * Used internally to remove entities from this Data Model
	 *
	 * @return void
	 */
	protected function removeEntity(MEntityDescription $entity) : void {
		$this->_entities->removeObject($entity);
	}
	
	/******************** Properties ********************/
	
	/**
	 * Returns an Array containing all the entities in this model
	 *
	 * @return MArray<MEntityDescription> An Array containing all the entities in this model
	 */
	public function entities() : MArray<MEntityDescription> {
		return $this->_entities;
	}
	
	/**
	 * Returns the file that describes this model and from which this model was loaded
	 *
	 * @return MFile The file that describes this model
	 */
	public function modelFile() : MFile {
		return $this->_modelFile;
	}
	
	/**
	 * Returns a String that identifies the version of the Model this Managed Object Model
	 * represents
	 *
	 * @return MString A String identifying the version this Model is using
	 */
	public function version() : MString {
		return $this->_version;
	}
	
	/******************** Methods ********************/
	
	/**
	 * Returns the entity with the specified name
	 *
	 * @param MString $name The name of the entity to return
	 *
	 * @return ?MEntityDescription The entity with the name that was specified
	 */
	public function entityWithName(MString $name) : ?MEntityDescription {
		foreach ($this->entities()->traversable() as $entity) {
			if ($entity->name()->equals($name) || $entity->plural()->equals($name)) {
				return $entity;
			}
		}
		return null;
	}
	
	/**
	 * Returns an instance of MEntityDescriptionAttribute matching
	 * the specified path
	 *
	 * The path is a string which should be in the following format
	 *
	 * "EntityName.attributeName"
	 *
	 * @param MString $path The path of the attribute you wish to retrieve
	 *
	 * @return MEntityDescriptionAttribute The attribute matching the specified
	 * path, or null if no matching attribute is found
	 */
	public function attributeWithPath(MString $path) : ?MEntityDescriptionAttribute {
		$pathArray = $path->componentsSeparatedByString(S("."));
		if ($pathArray->count() == 2) {
			$entityName = $pathArray->objectAtIndex(0);
			$attributeName = $pathArray->objectAtIndex(1);
			$entity = $this->entityWithName($entityName);
			if ($entity !== null) {
				return $entity->attributeWithName($attributeName);
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	/**
	 * Returns a new instance of MManagedObjectModel this instance's model file and the
	 * the specified version
	 *
	 * @param MString $version A String identifying the version of the model to load
	 *
	 * @return MManagedObjectModel A new instance of MManagedObjectModel that represents
	 * the version specified by $version
	 */
	public function modelForVersion(MString $version) : MManagedObjectModel {
		return new MManagedObjectModel($this->modelFile(), $version);
	}
}

