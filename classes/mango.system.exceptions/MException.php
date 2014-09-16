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
	
	package('mango.system.exceptions');
	
	import('mango.system.*');
	
	/**
	 * 
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system.exceptions
	 *
	 */
	class MException extends Exception implements MMangoObject {
		
		const GENERAL_EXCEPTION = 0;
		const NAN_EXCEPTION_CODE = 1;
		const INDEX_OUT_OF_BOUNDS_EXCEPTION_CODE = 2;
		const STREAM_EXCEPTION_CODE = 3;
		const STREAM_CLOSED_EXCEPTION_CODE = 4;
		const END_OF_STREAM_EXCEPTION_CODE = 5;
		const SEEK_NOT_SUPPORTED_EXCEPTION_CODE = 6;
		const INVALID_DATA_TYPE_EXCEPTION_CODE = 7;
		const NUMBER_OUT_OF_RANGE_EXCEPTION_CODE = 8;
		const MODEL_PARSE_ERROR_EXCEPTION_CODE = 9;
		const PERSISTENT_STORE_EXCEPTION_CODE = 10;
		const MANAGED_OBJECT_EXCEPTION_CODE = 11;
		const METHOD_NOT_SUPPORTED_EXCEPTION_CODE = 12;
		const BAD_REQUEST_EXCEPTION_CODE = 13;
		const INVALID_OPERATION_EXCEPTION_CODE = 14;
		const ENTITY_NOT_FOUND_EXCEPTION_CODE = 15;
		const PARSE_ERROR_EXCEPTION_CODE = 16;
		const FIELD_UNDEFINED_EXCEPTION = 17;
		const PARAMETER_UNDEFINED_EXCEPTION = 18;
		const INPUT_OUTPUT_EXCEPTION = 19;
		const FILE_NOT_FOUND_EXCEPTION = 20;
		
		//
		// ************************************************************
		//
		
		protected $description;
		protected $code;
		protected $previous;
		
		/**
		 * 
		 *
		 * @return MException
		 */
		public function __construct(MString $description = null, $code = MException::GENERAL_EXCEPTION, MException $previous = null) {
			parent::__construct();
			
			if ($description == null) $description = S("Unknown Exception");
			
			$this->description = $description;
			$this->code = $code;
			$this->previous = $previous;
		}
		
		/******************** Exception ********************/
		
		/**
		 * 
		 *
		 * @return string
		 */
		public function message() {
			return $this->description()->stringValue();
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function description() {
			return $this->description;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function code() {
			return $this->code;
		}
		
		/**
		 * 
		 *
		 * @return MException
		 */
		public function previousException() {
			return $this->previous;
		}
		
		/******************** MMangoObject ********************/
		
		/**
		 * 
		 */
		public function equals(MMangoObject $object) {
			return ($this == $object);
		}
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			return N($this->code())->compare(N($object->code()));
		}
		
		/**
		 * 
		 */
		public function className() {
			return new MString(get_class($this));
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
			return MString::stringWithFormat("%s[%s]: \"%s\"\n%s", $this->className(), $this->code(), $this->description(), $this->previousException());
		}
		
		/**
		 * 
		 */
		public function __toString() {
			return $this->toString()->stringValue();
		}
		
		/**
		 * 
		 */
		public function hash() {
			return 0;
		}
		
	}

?>