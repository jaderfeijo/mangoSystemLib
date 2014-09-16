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
	 * Class that describes an entity's property
	 *
	 * This class holds and manages the information necessary to describe
	 * an entity's property
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MEntityDescriptionProperty extends MEntityDescriptionAttribute {
		
		const StringType = 'String';
		const IntegerType = 'Integer';
		const FloatType = 'Float';
		const BooleanType = 'Boolean';
		const DateType = 'Date';
		const BinaryType = 'Binary';
		
		//
		// ************************************************************
		//
		
		protected $type;
		protected $defaultValue;
		
		/**
		 *
		 */
		public function __construct(MEntityDescription $entity, MString $name) {
			parent::__construct($entity, $name);
			
			$this->type = MEntityDescriptionProperty::StringType;
			$this->defaultValue = null;
		}
		
		/******************** Properties ********************/
		
		/**
		 * Sets the data type for this property
		 *
		 * This method sets the type of this entity's property. Below is a list of
		 * supported property types you can use
		 * MEntityDescriptionProperty::StringType
		 * MEntityDescriptionProperty::IntegerType
		 * MEntityDescriptionProperty::FloatType
		 * MEntityDescriptionProperty::BooleanType
		 * MEntityDescriptionProperty::DateType
		 * MEntityDescriptionProperty::BinaryType
		 *
		 * @see MEntityDescriptionProperty::StringType
		 * @see MEntityDescriptionProperty::IntegerType
		 * @see MEntityDescriptionProperty::FloatType
		 * @see MEntityDescriptionProperty::BooleanType
		 * @see MEntityDescriptionProperty::DateType
		 * @see MEntityDescriptionProperty::BinaryType
		 * 
		 * @param string $type A string defining the property data type
		 *
		 * @return void
		 */
		public function setType($type) {
			MAssertTypes('string', $type);
			
			$this->type = $type;
		}
		
		/**
		 * Returns the data type for this property
		 *
		 * This method returns the data type of this entity's property, Below is a list of
		 * possible values
		 * MEntityDescriptionProperty::StringType
		 * MEntityDescriptionProperty::IntegerType
		 * MEntityDescriptionProperty::FloatType
		 * MEntityDescriptionProperty::BooleanType
		 * MEntityDescriptionProperty::DateType
		 * MEntityDescriptionProperty::BinaryType
		 *
		 * @see MEntityDescriptionProperty::StringType
		 * @see MEntityDescriptionProperty::IntegerType
		 * @see MEntityDescriptionProperty::FloatType
		 * @see MEntityDescriptionProperty::BooleanType
		 * @see MEntityDescriptionProperty::DateType
		 * @see MEntityDescriptionProperty::BinaryType
		 *
		 * @return string A string defining the property data type
		 */
		public function type() {
			return $this->type;
		}
		
		/**
		 * Sets the default value of this property
		 *
		 * Use this method to set the default value of this property. This has to
		 * be of the correct object type for the data type specified for this
		 * property. For example, if the data type is MEntityDescription::StringType
		 * you should pass an instance of MString, if it's an IntegerType or FloatType,
		 * it should be an instance of MNumber and so on. Below is a full list of
		 * the data types and their corresponding objects
		 * 
		 * MEntityDescriptionProperty::StringType -> MString
		 * MEntityDescriptionProperty::IntegerType -> MNumber
		 * MEntityDescriptionProperty::FloatType -> MNumber
		 * MEntityDescriptionProperty::BooleanType -> MNumber
		 * MEntityDescriptionProperty::DateType -> MDate
		 * MEntityDescriptionProperty::BinaryType -> MData
		 *
		 * @see MEntityDescriptionProperty::StringType
		 * @see MEntityDescriptionProperty::IntegerType
		 * @see MEntityDescriptionProperty::FloatType
		 * @see MEntityDescriptionProperty::BooleanType
		 * @see MEntityDescriptionProperty::DateType
		 * @see MEntityDescriptionProperty::BinaryType
		 *
		 * @return void
		 */
		public function setDefaultValue(MObject $defaultValue) {
			$this->defaultValue = $defaultValue;
		}
		
		/**
		 * Returns the default value of this property
		 *
		 * This method returns the default value for this entity's property
		 *
		 * @return MObject|mixed The default valye of this property
		 */
		public function defaultValue() {
			return $this->defaultValue;
		}
		
		/******************** MEntityDescriptionAttribute ********************/
		
		/**
		 * 
		 */
		public function typeClassName() {
			if ($this->type() == MEntityDescriptionProperty::StringType) {
				return MString::className();
			} else if ($this->type() == MEntityDescriptionProperty::IntegerType) {
				return MNumber::className();
			} else if ($this->type() == MEntityDescriptionProperty::FloatType) {
				return MNumber::className();
			} else if ($this->type() == MEntityDescriptionProperty::BooleanType) {
				return MNumber::className();
			} else if ($this->type() == MEntityDescriptionProperty::DateType) {
				return MDate::className();
			} else if ($this->type() == MEntityDescriptionProperty::BinaryType) {
				return MData::className();
			} else {
				return S("");
			}
		}
		
	}

?>