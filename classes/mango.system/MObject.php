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
	 
	package('mango.system');
	
	/**
	 * Provides the basic functionally of a Mango object
	 *
	 * This class is the top level class from which all Mango
	 * objects are derived. It provides the basic functionally needed
	 * by every Mango Framework object.
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 * 
	 * @package mango.system
	 *
	 */
	class MObject implements MMangoObject {
		
		/**
		 * Returns the class name of the class in which this method is called on
		 *
		 * @example MString::typeClass() // returns S("MString")
		 *
		 * @return MString
		 */
		public static function typeClass() {
			return S(get_called_class());
		}
		
		/**
		 * Creates a new instance of the class specified
		 *
		 * @example $mString = MObject::newInstanceOfClass(S("mango.system.MString"));
		 * // $mString is a new instance of MString
		 *
		 * @param MString $className The fully qualified class name you wish to create an instance of
		 *
		 * @return MObject|mixed A new instance of the class specified by $className
		 */
		public static function newInstanceOfClass(MString $className) {
			return MObject::newInstanceOfClassWithParameters($className);
		}
		
		/**
		 * Creates a new instance of the class specified using the specified parameters
		 *
		 * @example $mString = MObject::newInstanceOfClass(S("mango.system.MString"), A("my string"));
		 * // $mString is a new instance of MString with the value of S("my string")
		 *
		 * @param MString $className The fully qualified class name you wish to create an intance of
		 * @param MArray $params The list of arguments to pass to the class' constructor
		 *
		 * @return MObject|mixed A new instance of the class specified by $className initialized with the
		 * parameters specified by $params
		 */
		public static function newInstanceOfClassWithParameters(MString $className, MArray $params = null) {
			$reflectionClass = MObject::reflectionClass($className);
			if ($params != null) {
				return $reflectionClass->newInstanceArgs($params->toArray());
			} else {
				return $reflectionClass->newInstance();
			}
		}
		
		/**
		 * Returns a new instance of ReflectionClass for the class specified
		 *
		 * @param MString $className The fully qualified class name to create the ReflectionClass
		 * instance for
		 *
		 * @return ReflectionClass A new instance of ReflectionClass representing the class
		 * specified by $className
		 */
		public static function reflectionClass(MString $className) {
			$class = MClassFromPackageString($className->stringValue());
			$package = MPackageFromPackageString($className->stringValue());
			
			if ($package != "") {
				import($className);
			}
			
			return new ReflectionClass($class);
		}
		
		//
		// ************************************************************
		//
		
		/**
		 * Creates a new instance of MObject
		 *
		 * @return MObject A new instance of MObject
		 */
		public function __construct() {
			//
		}
		
		/******************** MMangoObject ********************/
		
		/**
		 * 
		 */
		public function equals(MMangoObject $object) {
			return ($this === $object);
		}
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			if ($this > $object) {
				return MMangoObject::ORDERED_DESCENDING;
			} else if ($this == $object) {
				return MMangoObject::ORDERED_SAME;
			} else if ($this < $object) {
				return MMangoObject::ORDERED_ASCENDING;
			} else {
				return MMangoObject::ORDERED_SAME;
			}
		}
		
		/**
		 * 
		 */
		public function hash() {
			return hexdec(md5(var_export($this, true)));
		}
		
		/**
		 * 
		 */
		public function className() {
			return S(get_class($this));
		}
		
		/**
		 *
		 */
		public function packageName() {
			return S(__DIR__)->componentsSeparatedByString(S("/"))->lastObject();
		}
		
		/**
		 *
		 */
		public function fullClassName() {
			return Sf("%s.%s", $this->packageName(), $this->className());
		}
		
		/**
		 *
		 */
		public function isKindOfClass(MString $class) {
			return is_a($this, $class->stringValue());
		}
		
		/**
		 * 
		 */
		public function toString() {
			return $this->className();
		}
		
		/**
		 * @internal
		 *
		 * Returns a string representation of the current object
		 *
		 * @return string A string representation of the current object
		 */
		public function __toString() {
			return $this->toString()->stringValue();
		}
		
	}

?>