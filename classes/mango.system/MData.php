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
	 * 
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system
	 *
	 */
	class MData extends MObject {
		
		/**
		 *
		 *
		 * @return MData
		 */
		public static function parseBase64String(MString $base64String) {
			return new MData(base64_decode($base64String->stringValue()));
		}
		
		/**
		 * 
		 *
		 * @return MData
		 */
		public static function dataWithBytes($bytes) {
			return new MData($bytes);
		}
		
		//
		// ************************************************************
		//
		
		protected $bytes;
		
		/**
		 * 
		 *
		 * @return MData
		 */
		public function __construct($bytes) {
			MAssertTypes('string', $bytes);
			parent::__construct();
			
			if (!is_string($bytes)) throw new MInvalidDataTypeException(S("string"), S(gettype($bytes)));
			
			$this->bytes = $bytes;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function length() {
			return new strlen($this->bytes);
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return string
		 */
		public function getBytes(MRange $range = null) {
			if (is_null($range)) {
				return $this->bytes;
			} else {
				return substr($this->bytes, $range->location(), $range->length());
			}
		}
		
		/**
		 * 
		 *
		 * @return string
		 */
		public function firstByte() {
			if ($this->length() > 0) {
				$bytes = $this->getBytes(MRange::rangeWithLength(1));
				return $bytes[0];
			} else {
				throw new MIndexOutOfBoundsException();
			}
		}
		
		/**
		 * 
		 *
		 * @return MData
		 */
		public function dataWithinRange(MRange $range) {
			return new MData($this->getBytes($range));
		}
		
		/**
		 * 
		 *
		 * @return MData
		 */
		public function dataByAppendingData(MData $data) {
			return new MData($this->getBytes() . $data->getBytes());
		}
		
		/**
		 * 
		 *
		 * @return MData
		 */
		public function dataByAppendingBytes($bytes) {
			return new MData($this->getBytes() . $bytes);
		}
		
		public function base64EncodedString() {
			return S(base64_encode($this->bytes));
		}
		
		/******************** MObject Methods ********************/
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			return N($this->length())->compare(N($object->length()));
		}
		
		/**
		 * 
		 */
		public function toString() {
			return $this->base64EncodedString();
		}
		
		/**
		 * 
		 */
		public function hash() {
			hexdec(md5($this->bytes));
		}
	}

?>