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
	class MSet extends MObject {
		
		const OBJECT_NOT_FOUND = -1;
		
		const ORDER_ASCENDING = 0;
		const ORDER_DESCENDING = 1;
		
		//
		// ************************************************************
		//
		
		protected $set;
		
		/**
		 * 
		 *
		 * @return MSet
		 */
		public function __construct(MArray $array = null) {
			parent::__construct();
			
			$this->set = ($array ? $array->toArray() : array());
		}
		
		/******************** Protected ********************/
		
		/**
		 * @internal
		 *
		 * @return int
		 */
		public function _indexOfObject($object) {
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
			return MSet::OBJECT_NOT_FOUND;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function count() {
			return count($this->set);
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function objectExists($object) {
			return ($this->_indexOfObject($object) != MSet::OBJECT_NOT_FOUND);
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
		 * @return MObject
		 */
		public function member($object) {
			$index = $this->_indexOfObject($object);
			if ($index != MSet::OBJECT_NOT_FOUND) {
				return $this->set[$index];
			} else {
				return null;
			}
		}
		
		/**
		 * 
		 *
		 * @return MObject
		 */
		public function anyObject() {
			if ($this->count() > 0) {
				return $this->set[0];
			} else {
				return null;
			}
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function sortedArray($order = MSet::ORDER_ASCENDING, $sortFlags = SORT_REGULAR) {
			MAssertTypes('int', $order);
			$sortedArray = $this->set;
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
		 * @return array
		 */
		public function toArray() {
			return $this->set;
		}
		
		/******************** MObject ********************/
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			return N($this->count())->compare(N($object->count()));
		}
		
	}

?>