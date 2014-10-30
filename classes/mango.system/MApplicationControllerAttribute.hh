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
 * Abstract class which forms the base for all application controller attributes
 *
 * @see MApplicationControllerField
 * @see MApplicationControllerParameter
 *
 * @author Jader Feijo <jader@movinpixel.com>
 */
abstract class MApplicationControllerAttribute extends MObject {
	
	protected MString $_name;
	protected MApplicationControllerAttributeType $_type;
	protected bool $_required;
	
	/**
	 * This method should be overriden by subclasses of this class to perform
	 * extra initialization as necessary
	 *
	 * This method initializes a new instance of a subclass of MApplicationControllerAttribute
	 * with the specified name, type and required parameters
	 *
	 * @param MString $name The name of this attribute
	 * @param int $type A constant representing the type of this attribute
	 * Possible values are:
	 * 	MApplicationControllerAttributeType::String
	 *  MApplicationControllerAttributeType::Integer
	 *  MApplicationControllerAttributeType::Float
	 *  MApplicationControllerAttributeType::Boolean
	 *  MApplicationControllerAttributeType::Date
	 *  MApplicationControllerAttributeType::Binary
	 *  MApplicationControllerAttributeType::Array
	 *  MApplicationControllerAttributeType::Dictionary
	 *
	 * @param bool $required Wether or not this attribute is required
	 */
	public function __construct(MString $name, MApplicationControllerAttributeType $type, bool $required = true) {
		parent::__construct();

		$this->_name = $name;
		$this->_type = $type;
		$this->_required = $required;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Returns the name of this attribute
	 *
	 * @return MString A string containing the name of this attribute
	 */
	public function name() : MString {
		return $this->_name;
	}
	
	/**
	 * Retuns an constant representing the type of this attribute
	 * Possible values are:
	 * 	MApplicationControllerAttributeType::String
	 *  MApplicationControllerAttributeType::Integer
	 *  MApplicationControllerAttributeType::Float
	 *  MApplicationControllerAttributeType::Boolean
	 *  MApplicationControllerAttributeType::Date
	 *  MApplicationControllerAttributeType::Binary
	 *  MApplicationControllerAttributeType::Array
	 *  MApplicationControllerAttributeType::Dictionary
	 *
	 * @return MApplicationControllerAttributeType A constant representing the type of this attribute
	 */
	public function type() : MApplicationControllerAttributeType {
		return $this->_type;
	}
	
	/**
	 * Returns a boolean which indicates wether this attribute is required
	 * or not
	 *
	 * @return bool A boolean indicating wether this attribute is required
	 * or not
	 */
	public function required() : bool {
		return $this->_required;
	}
	
	/******************** Methods ********************/
	
	/**
	 * Returns a string containing the expected data type for this attribute
	 *
	 * @return MString A string containing the expected data type for this attribute
	 */
	public function expectedDataType() : MString {
			if ($this->type() == MApplicationControllerAttributeType::String) {
			return S("String");
			} else if ($this->type() == MApplicationControllerAttributeType::Integer) {
			return S("Integer");
		} else if ($this->type() == MApplicationControllerAttributeType::Float) {
			return S("Float");
		} else if ($this->type() == MApplicationControllerAttributeType::Boolean) {
			return S("Boolean");
		} else if ($this->type() == MApplicationControllerAttributeType::Date) {
			return S("Date");
		} else if ($this->type() == MApplicationControllerAttributeType::Binary) {
			return S("Binary");
		} else if ($this->type() == MApplicationControllerAttributeType::Array) {
			return S("Array");
		} else if ($this->type() == MApplicationControllerAttributeType::Dictionary) {
			return S("Dictionary"); 
		} else {
			return S("Unknown");
		}
	}
	
	/******************** MObject ********************/
	
	/**
	 *
	 */
	public function toString() : MString {
		return Sf("%s(%s)", $this->className(), $this->name());
	}
	
}

