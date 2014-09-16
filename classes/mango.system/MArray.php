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
	
	import('mango.system.exceptions.*');
	
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
	class MArray extends MObject {
		
		const OBJECT_NOT_FOUND = -1;
		
		const ORDER_ASCENDING = 0;
		const ORDER_DESCENDING = 1;
		
		//
		// ************************************************************
		//
		
		protected $array;
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function __construct($array = array()) {
			parent::__construct();
			
			$this->array = $array;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function lowerBound() {
			return 0;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function upperBound() {
			return $this->count() - 1;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function count() {
			return count($this->array);
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return mixed
		 */
		public function objectAtIndex($index) {
			MAssertTypes('int', $index);
			
			if ($index >= $this->lowerBound() && $index <= $this->upperBound()) {
				return $this->array[$index];
			} else {
				throw new MIndexOutOfBoundsException($index, $this->lowerBound(), $this->upperBound());
			}
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function indexOfObject($object) {
			for ($i = 0; $i < $this->count(); $i++) {
				$current = $this->array[$i];
				if ($object instanceof MObject && $current instanceof MObject) {
					if ($object->equals($current)) {
						return $i;
					}
				} else {
					if ($current == $object) {
						return $i;
					}
				}
			}
			return MArray::OBJECT_NOT_FOUND;
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function objectExists($object) {
			return ($this->indexOfObject($object) != MArray::OBJECT_NOT_FOUND);
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function containsObject($object) {
			return $this->objectExists($object);
		}
		
		/**
		 * 
		 *
		 * @return mixed
		 */
		public function lastObject() {
			if ($this->count() > 0) {
				return $this->objectAtIndex($this->upperBound());
			} else {
				return null;
			}
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function isLastObject($object) {
			return $this->indexOfObject($object) == $this->upperBound();
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function componentsJoinedByString(MString $separator) {
			return new MString(implode($separator->stringValue(), $this->array));
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function subarrayWithRange(MRange $range) {
			return new MArray(array_slice($this->array, $range->location(), $range->length()));
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function subarrayFromIndex($index) {
			MAssertTypes('int', $index);
			return $this->subarrayWithRange(MRangeMake($index, $this->upperBound()));
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function subarrayToIndex($index) {
			MAssertTypes('int', $index);
			return $this->subarrayWithRange(MRangeMake(0, $index));
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function sortedArray($order = MArray::ORDER_ASCENDING, $sortFlags = SORT_REGULAR) {
			MAssertTypes('int', $order);
			$sortedArray = $this->array;
			if ($order == MArray::ORDER_ASCENDING) {
				sort($sortedArray, $sortFlags);
			} else {
				rsort($sortedArray, $sortFlags);
			}
			return new MArray($sortedArray);
		}
		
		/**
		 *
		 *
		 * @return MArray
		 */
		public function sortedArrayUsingMethod(callback $method, $order = MArray::ORDER_ASCENDING) {
			$array = $this->toArray();
			usort($array, function ($a, $b) {
				$result = $a->{$method}($b);
				if (MArray::ORDER_DESCENDING) {
					if ($result == MMangoObject::ORDERED_ASCENDING) {
						$result = MMangoObject::ORDERED_DESCENDING;
					} else if ($result == MMangoObject::ORDERED_DESCENDING) {
						$result = MMangoObject::ORDERED_ASCENDING;
					}
				}
				return $result;
			});
			return new MArray($array);
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function arrayByAppendingArray(MArray $array) {
			return A(array_merge($this->array, $array->array));
		}
		
		/**
		 * 
		 *
		 * @return array
		 */
		public function toArray() {
			return $this->array;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function toJSON() {
			return S(json_encode($this->array));
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
			return MString::stringWithFormat("MArray[%d]:\n%s", $this->count(), var_export($this->array, true));
		}
		
	}

?>