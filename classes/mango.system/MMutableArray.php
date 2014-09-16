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
	class MMutableArray extends MArray {
		
		//
		// ************************************************************
		//
		
		/**
		 * 
		 *
		 * @return MMutableArray
		 */
		public function __construct(MArray $array = null) {
			if (!is_null($array)) {
				parent::__construct($array->array);
			} else {
				parent::__construct();
			}
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function addObject($object) {
			$this->array[] = $object;
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function addObjects($objects) {
			if ($objects instanceof MArray) {
				foreach ($objects->toArray() as $object) {
					$this->addObject($object);
				}
			} else {
				foreach ($objects as $object) {
					$this->addObject($object);
				}
			}
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function removeObject($object) {
			for ($i = 0; $i < $this->count(); $i++) {
				if ($this->array[$i] == $object) {
					$this->removeObjectAtIndex($i);
					return true;
				}
			}
			return false;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function removeObjectAtIndex($index) {
			MAssertTypes('int', $index);
			if (N($index)->isWithinBounds(N($this->lowerBound()), N($this->upperBound()))) {
				array_splice($this->array, $index, 1);
			} else {
				throw new MIndexOutOfBoundsException($index, $this->lowerBound(), $this->upperBound());
			}
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function removeAllObjects() {
			unset($this->array);
			$this->array = array();
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function appendArray(MArray $array) {
			$this->array = array_merge($this->array, $array->array);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function subtractArray(MArray $array) {
			foreach ($array as $search) {
				foreach ($this->array as $key => $value) {
					if ($search instanceof MObject) {
						if ($search->equals($value)) {
							$this->removeObjectAtIndex($key);
							break;
						}
					} else {
						if ($search == $value) {
							$this->removeObjectAtIndex($key);
							break;
						}
					}
				}
			}
		}
		
	}

?>