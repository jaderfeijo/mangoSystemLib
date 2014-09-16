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
	class MDictionary extends MObject {
		
		/**
		 * 
		 * @param MString $dictString
		 * 
		 * @return MDictionary
		 */
		public static function parseString(MString $dictString) {
			$arr = $dictString->componentsSeparatedByString(S("|"));
			$dict = new MMutableDictionary();
			foreach ($arr->toArray() as $dictKeyPair) {
				$dictKeyPairArr = $dictKeyPair->componentsSeparatedByString(S(":"));
				$key = $dictKeyPairArr->objectAtIndex(0)->urlDecodedString();
				$value = $dictKeyPairArr->objectAtIndex(1)->urlDecodedString();
				$dict->setObjectForKey($key, $value);
			}
			return $dict;
		}
		
		//
		// ************************************************************
		//
		
		protected $keys;
		protected $values;
		
		/**
		 * 
		 *
		 * @return MDictionary
		 */
		public function __construct($dictionary = array()) {
			parent::__construct();
			
			$this->keys = new MMutableArray(A(array_keys($dictionary)));
			$this->values = new MMutableArray(A(array_values($dictionary)));
			
			$args = func_get_args();
			if (N(count($args))->isEven()) {
				for ($i = 0; $i < count($args); $i++) {
					$key = $args[$i];
					$object = $args[++$i];
					$this->keys->addObject($key);
					$this->values->addObject($object);
				}
			} else {
				throw new MException(S("Uneven number of keys and objects specified"));
			}
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function allKeys() {
			return $this->keys;
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function allObjects() {
			return $this->values;
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function allValues() {
			return $this->allObjects();
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function hasObjectForKey($key) {
			return $this->keys->objectExists($key);
		}
		
		/**
		 * 
		 *
		 * @return mixed
		 */
		public function objectForKey($key) {
			$index = $this->keys->indexOfObject($key);
			if ($index != MArray::OBJECT_NOT_FOUND) {
				return $this->values->objectAtIndex($index);
			} else {
				return null;
			}
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function count() {
			return $this->keys->count();
		}
		
		/**
		 * @return array
		 */
		public function toArray() {
			$array = array();
			
			foreach ($this->allKeys()->toArray() as $key) {
				$object = $this->objectForKey($key);
				
				$k = (string)$key;
				$v = null;
				
				if ($object instanceof MDictionary) {
					$v = $object->toArray();
				} else if ($object instanceof MArray) {
					$v = $object->toArray();
				} else if ($object instanceof MNumber) {
					$v = $object->value();
				} else {
					$v = (string)$object;
				}
				
				$array[$k] = $v;
			}
			
			return $array;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function toJSON() {
			return S(json_encode($this->toArray()));
		}
		
		/******************** MObject Methods ********************/
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			return N($this->count())->compare(N($object->count()));
		}
		
		/**
		 * 
		 */
		public function toString() {
			$string = new MMutableString();
			foreach ($this->allKeys()->toArray() as $key) {
				$string->appendFormat("%s:%s", urlencode((string)$key), urlencode((string)$this->objectForKey($key)));
				if (!$this->allKeys()->isLastObject($key)) {
					$string->appendString(S("|"));
				}
			}
			return $string;
		}
		
		/**
		 * 
		 */
		public function hash() {
			return hexdec(md5($this->toString()));
		}
		
	}

?>