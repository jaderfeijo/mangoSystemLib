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
	import('mango.system.xml.*');
	import('mango.system.*');
	
	/**
	 * This class provides a View that allows you to represent a collection of Managed
	 * Objects as XML for a certain entity type
	 * 
	 * This class is used to return a collection of Managed Objects back to the client
	 * as an XML document
	 *
	 * This class acts as a container for MXMLManagedObjectView objects
	 *
	 * @see MXMLManagedObjectView
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MXMLEntityCollectionView extends MXMLElementView {
		
		//
		// ************************************************************
		//
		
		protected $entity;
		protected $fireFaults;
		protected $managedObjects;
		protected $dynamicFields;
		
		/**
		 * Creates a new XML Entity Collection view instance with the specified
		 * entity type
		 *
		 * @param MEntityDescription $entity The entity this collection will represent
		 *
		 * @return MXMLEntityCollectionView The newly created instance
		 */
		public function __construct(MEntityDescription $entity) {
			parent::__construct($entity->plural());
			
			$this->entity = $entity;
			$this->fireFaults = false;
			$this->managedObjects = new MMutableDictionary();
			$this->dynamicFields = new MMutableDictionary();
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the Entity this XML Entity Collection represents
		 *
		 * @return MEntityDescription The Entity this collection represents
		 */
		public function entity() {
			return $this->entity;
		}
		
		/**
		 * Sets weather or not this view should fire faults when a managed object is added
		 * to it.
		 * 
		 * If fireFaults is set to false (default), only the object's ID is represented in the
		 * resulting collection XML.
		 * If fireFaults is set to true, the object's ID as well as it's data is represented in
		 * the resulting collection XML.
		 * 
		 * @param bool $fireFaults Weather or not this view should fire faults when adding managed
		 * objects to it
		 */
		public function setFireFaults($fireFaults) {
			MAssertTypes('bool', $fireFaults);
			$this->fireFaults = $fireFaults;
		}
		
		/**
		 * Returns weather or not this view fires faults when a managed object is added
		 * to it.
		 * 
		 * @see setFireFaults()
		 * 
		 * @returns bool Weather or not this view fires faults when managed object is added
		 */
		public function fireFaults() {
			return $this->fireFaults;
		}
		
		/**
		 * Adds a Managed Object to the entity collection
		 *
		 * @param MManagedObject $object The object to be added to the collection
		 *
		 * @return void
		 */
		public function addManagedObject(MManagedObject $object) {
			if ($object->entity()->equals($this->entity())) {
				if ($this->fireFaults()) {
					$object->fireFault();
				}
				
				$elementView = new MXMLManagedObjectView($object);
				
				foreach ($this->dynamicFields->allKeys()->toArray() as $fieldName) {
					$dataProvider = $this->dynamicFields->objectForKey($fieldName);
					$elementView->addDynamicField($fieldName, $dataProvider);
				}
				
				$this->managedObjects->setObjectForKey($object, $elementView);
				$this->addSubview($elementView);
			} else {
				throw new MManagedObjectException($object, S("Object's entity is different from collections' entity"));
			}
		}
		
		/**
		 * Removes a Managed Object from the entity collection
		 *
		 * @param MManagedObject $object The object to be removed from the collection
		 *
		 * @return void
		 */
		public function removeManagedObject(MManagedObject $object) {
			if ($object->entity()->equals($this->entity())) {
				$elementView = $this->managedObjects->objectForKey($object);
				$this->managedObjects->removeObjectForKey($object);
				$this->removeSubview($elementView);
			} else {
				throw new MManagedObjectException($object, S("Object's entity is different from collections' entity"));
			}
		}
		
		/**
		 * Removes all Managed Objects from this collection of entities
		 *
		 * @return void
		 */
		public function removeAllManagedObjects() {
			$this->managedObjects->removeAllObjects();
		}
		
		/**
		 * Returns an Array containing all Managed Objects in this collection
		 *
		 * @return MArray An Array with all the Managed Objects in this collection
		 */
		public function managedObjects() {
			return $this->managedObjects->allKeys();
		}
		
		/**
		 * 
		 * @param MString $fieldName
		 * @param MXMLManagedObjectViewDataProvider $dataProvider
		 * 
		 * @return void
		 */
		public function addDynamicField(MString $fieldName, MXMLManagedObjectViewDataProvider $dataProvider) {
			$this->dynamicFields->setObjectForKey($fieldName, $dataProvider);
		}
		
		/**
		 * 
		 * @param MString $fieldName
		 * @param MXMLManagedObjectViewDataProvider $dataProvider
		 * 
		 * @return void
		 */
		public function removeDynamicField(MString $fieldName) {
			$this->dynamicFields->removeObjectForKey($fieldName);
		}
		
	}

?>