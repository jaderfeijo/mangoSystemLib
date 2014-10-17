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
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MMutableArray extends MArray {
	
	//
	// ************************************************************
	//
	
	public function __construct(?MArray $array = null) {
		if ($array !== null) {
			parent::__construct($array->_array);
		} else {
			parent::__construct();
		}
	}
	
	/******************** Methods ********************/
	
	public function addObject(object $object) : void {
		$this->_array[] = $object;
	}
	
	public function addObjects(array $objects) : void {
		foreach ($objects as $object) {
			$this->addObject($object);
		}
	}

	public function addObjectsFromArray(MArray $objects) : void {
		foreach ($objects->toArray() as $object) {
			$this->addObject($object);
		}
	}
	
	public function removeObject($object) : bool {
		for ($i = 0; $i < $this->count(); $i++) {
			if ($this->_array[$i] == $object) {
				$this->removeObjectAtIndex($i);
				return true;
			}
		}
		return false;
	}
	
	public function removeObjectAtIndex(int $index) : void {
		if ($index >= $this->lowerBound() && $index <= $this->upperBound()) {
			array_splice($this->_array, $index, 1);
		} else {
			throw new MIndexOutOfBoundsException($index, $this->lowerBound(), $this->upperBound());
		}
	}
	
	public function removeAllObjects() : void {
		unset($this->_array);
		$this->_array = array();
	}
	
	public function appendArray(MArray $array) : void {
		$this->_array = array_merge($this->_array, $array->_array);
	}
	
	public function subtractArray(MArray $array) : void {
		foreach ($array->toArray() as $search) {
			foreach ($this->_array as $key => $value) {
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

