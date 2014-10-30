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
 * Class that describes an entity's property
 *
 * This class holds and manages the information necessary to describe
 * an entity's property
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MEntityDescriptionProperty extends MEntityDescriptionAttribute {
	
	protected MPropertyType $_type;
	protected ?MValue $_defaultValue;
	
	public function __construct(MEntityDescription $entity, MString $name) {
		parent::__construct($entity, $name);
		$this->_type = MPropertyType::String;
		$this->_defaultValue = null;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Sets the data type for this property
	 *
	 * This method sets the type of this entity's property. Below is a list of
	 * supported property types you can use
	 * MPropertyType::String
	 * MPropertyType::Integer
	 * MPropertyType::Float
	 * MPropertyType::Boolean
	 * MPropertyType::Date
	 * MPropertyType::Binary
	 *
	 * @see MPropertyType::String
	 * @see MPropertyType::Integer
	 * @see MPropertyType::Float
	 * @see MPropertyType::Boolean
	 * @see MPropertyType::Date
	 * @see MPropertyType::Binary
	 * 
	 * @param MPropertyType $type The property data type
	 *
	 * @return void
	 */
	public function setType(MPropertyType $type) : void {
		$this->_type = $type;
	}
	
	/**
	 * Returns the data type for this property
	 *
	 * This method returns the data type of this entity's property, Below is a list of
	 * possible values
	 * MPropertyType::String
	 * MPropertyType::Integer
	 * MPropertyType::Float
	 * MPropertyType::Boolean
	 * MPropertyType::Date
	 * MPropertyType::Binary
	 *
	 * @see MPropertyType::String
	 * @see MPropertyType::Integer
	 * @see MPropertyType::Float
	 * @see MPropertyType::Boolean
	 * @see MPropertyType::Date
	 * @see MPropertyType::Binary
	 *
	 * @return MPropertyType The property data type
	 */
	public function type() : MPropertyType {
		return $this->_type;
	}
	
	/**
	 * Sets the default value of this property
	 *
	 * Use this method to set the default value of this property. This has to
	 * be of the correct object type for the data type specified for this
	 * property. For example, if the data type is MPropertyType::String
	 * you should pass an instance of MString, if it's an Integer or Float,
	 * it should be an instance of MNumber and so on. Below is a full list of
	 * the data types and their corresponding objects
	 * 
	 * MPropertyType::String -> MString
	 * MPropertyType::Integer -> MNumber
	 * MPropertyType::Float -> MNumber
	 * MPropertyType::Boolean -> MNumber
	 * MPropertyType::Date -> MDate
	 * MPropertyType::Binary -> MData
	 *
	 * @see MPropertyType::String
	 * @see MPropertyType::Integer
	 * @see MPropertyType::Float
	 * @see MPropertyType::Boolean
	 * @see MPropertyType::Date
	 * @see MPropertyType::Binary
	 *
	 * @param ?MValue $defaultValue The default value for this property
	 *
	 * @return void
	 */
	public function setDefaultValue(?MValue $defaultValue) : void {
		$this->_defaultValue = $defaultValue;
	}
	
	/**
	 * Returns the default value of this property
	 *
	 * This method returns the default value for this entity's property
	 *
	 * @return ?MValue The default value of this property
	 */
	public function defaultValue() : ?MValue {
		return $this->_defaultValue;
	}
	
	/******************** MEntityDescriptionAttribute ********************/
	
	public function typeClassName() : MString {
		if ($this->type() == MPropertyType::String) {
			return MString::className();
		} else if ($this->type() == MPropertyType::Integer) {
			return MNumber::className();
		} else if ($this->type() == MPropertyType::Float) {
			return MNumber::className();
		} else if ($this->type() == MPropertyType::Boolean) {
			return MNumber::className();
		} else if ($this->type() == MPropertyType::Date) {
			return MDate::className();
		} else if ($this->type() == MPropertyType::Binary) {
			return MData::className();
		} else {
			return S("");
		}
	}
	
}

