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
	
	/**
	 * Provides a description of an entity and its properties and relationships
	 *
	 * This class stores all the information necessary to describe an Entity
	 * with all of its properties and relationships
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
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
		public static function entityWithName(MString $name, MManagedObjectContext $context) {
			return $context->persistentStoreCoordinator()->model()->entityWithName($name);
		}
		
		//
		// ************************************************************
		//
		
		protected $name;
		protected $plural;
		protected $entityClassName;
		protected $attributes;
		
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
			
			$this->name = $name;
			$this->plural = $plural;
			$this->entityClassName = $entityClassName;
			$this->attributes = new MMutableArray();
		}
		
		/******************** Properties ********************/
		
		/**
		 * Sets the entity's name
		 *
		 * @param MString $name The new entity name
		 *
		 * @return void
		 */
		public function setName(MString $name) {
			$this->name = $name;
		}
		
		/**
		 * Returns the entity name
		 *
		 * @return MString The entity name
		 */
		public function name() {
			return $this->name;
		}
		
		/**
		 * Sets the entity's plural name
		 *
		 * @param MString $plural The new pluralised name
		 *
		 * @return void
		 */
		public function setPlural(MString $plural) {
			$this->plural = $plural;
		}
		
		/**
		 * Returns the entity's pluralised name
		 *
		 * @return MString The pluralised name
		 */
		public function plural() {
			return $this->plural;
		}
		
		/**
		 * Sets the entity's class name
		 *
		 * @param MString $className The fully qualified class name for this entity
		 *
		 * @return void
		 */
		public function setEntityClassName(MString $entityClassName) {
			$this->entityClassName = $entityClassName;
		}
		
		/**
		 * Returns the fully qualified class name for this entity
		 *
		 * @return MString The fully qualified class name for this entity
		 */
		public function entityClassName() {
			return $this->entityClassName;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Adds an attribute to this entity
		 *
		 * @param MEntityDescriptionAttribute $attribute The attribute to add to this entity
		 *
		 * @return void
		 */
		public function addAttribute(MEntityDescriptionAttribute $attribute) {
			$this->attributes->addObject($attribute);
		}
		
		/**
		 * Removes an attribute from this entity
		 *
		 * @param MEntityDescriptionAttribute $attribute The attribute to remove from this entity
		 *
		 * @return void
		 */
		public function removeAttribute(MEntityDescriptionAttribute $attribute) {
			$this->attributes->removeObject($attribute);
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
		 * @return MArray An array containing all the attributes in this entity
		 */
		public function attributes() {
			return $this->attributes;
		}
		
		/**
		 * Returns an Array of all the properties in this entity
		 *
		 * The Array returned contains instances of the MEntityDescriptionProperty
		 * class which describes each property contained inside this entity
		 *
		 * @see MEntityDescriptionProperty
		 *
		 * @return MArray An array containing all the properties in this entity
		 */
		public function properties() {
			$properties = new MMutableArray();
			foreach ($this->attributes()->toArray() as $attribute) {
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
		 * @return MArray An array containing all the relationships in this entity
		 */
		public function relationships() {
			$relationships = new MMutableArray();
			foreach ($this->attributes()->toArray() as $attribute) {
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
		public function attributeWithName(MString $name) {
			foreach ($this->attributes()->toArray() as $attribute) {
				if ($attribute->name()->equals($name)) {
					return $attribute;
				} else if ($attribute instanceof MEntityDescriptionRelationship) {
					if ($attribute->singular()->equals($name)) {
						return $attribute;
					}
				}
			}
			return null;
		}
		
		/******************** MObject Methods ********************/
		
		/**
		 * 
		 */
		public function equals(MMangoObject $object) {
			return ($this->name()->equals($object->name()) && $this->entityClassName()->equals($object->entityClassName()));
		}
		
		/**
		 * 
		 */
		public function toString() {
			return Sf("%s<%s>", $this->name(), $this->entityClassName());
		}
		
	}

?>