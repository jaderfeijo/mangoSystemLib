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
	 * Abstract class that holds the basic information about an entity's attribute
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	abstract class MEntityDescriptionAttribute extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $entity;
		protected $name;
		
		/******************** Abstract Methods ********************/
		
		/**
		 * Returns the type class name of this attribute
		 *
		 * @return MString
		 */
		public abstract function typeClassName();
		
		/******************** Methods ********************/
		
		/**
		 * Initializes a new instance of an MEntityDescriptionAttribute class
		 *
		 * @param MEntityDescription $entity The entity that this attribute belongs to
		 * @param MString $name The attribute name
		 *
		 * @return MEntityDescriptionAttribute A new instance of the MEntityDescriptionAttribute class
		 */
		public function __construct(MEntityDescription $entity, MString $name) {
			parent::__construct();
			
			$this->entity = $entity;
			$this->name = $name;
			
			$this->entity->addAttribute($this);
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the entity description to which this attribute belongs
		 *
		 * @return MEntityDescription The entity description to which this attribute belongs
		 */
		public function entity() {
			return $this->entity;
		}
		
		/**
		 * Sets the name of this attribute
		 *
		 * @param MString $name The new name for this attribute
		 *
		 * @return void
		 */
		public function setName(MString $name) {
			$this->name = $name;
		}
		
		/**
		 * Returns the name of this entity
		 *
		 * @return MString The name of this entity
		 */
		public function name() {
			return $this->name;
		}
		
		/******************** MObject ********************/
		
		/**
		 * 
		 */
		public function equals(MMangoObject $object) {
			return ($this->entity()->equals($object->entity()) && $this->name()->equals($object->name()));
		}
		
		/**
		 * 
		 */
		public function toString() {
			return Sf("%s->%s", $this->entity()->name(), $this->name());
		}
		
	}

?>