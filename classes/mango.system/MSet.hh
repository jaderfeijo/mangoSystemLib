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
class MSet extends MObject {
	
	const ObjectNotFound = -1;
	
	//
	// ************************************************************
	//
	
	protected array $_set;
	
	public function __construct(?MArray $array = null) {
		parent::__construct();
		$this->_set = ($array !== null ? $array->toArray() : array());
	}
	
	/******************** Protected ********************/
	
	protected function _indexOfObject(object $object) : int {
		for ($i = 0; $i < $this->count(); $i++) {
			$current = $this->_set[$i];
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
		return MSet::ObjectNotFound;
	}
	
	/******************** Properties ********************/
	
	public function count() : int {
		return count($this->_set);
	}
	
	/******************** Methods ********************/
	
	public function objectExists(object $object) : bool {
		return ($this->_indexOfObject($object) != MSet::ObjectNotFound);
	}
	
	public function containsObject(object $object) : bool {
		return $this->objectExists($object);
	}
	
	public function member(object $object) : ?MObject {
		$index = $this->_indexOfObject($object);
		if ($index != MSet::ObjectNotFound) {
			return $this->_set[$index];
		} else {
			return null;
		}
	}
	
	public function anyObject() : ?MObject {
		if ($this->count() > 0) {
			return $this->_set[0];
		} else {
			return null;
		}
	}
	
	public function sortedArray(MArrayOrder $order = MArrayOrder::Ascending, int $sortFlags = SORT_REGULAR) : MArray {
		$sortedArray = $this->_set;
		if ($order == MArrayOrder::Ascending) {
			sort($sortedArray, $sortFlags);
		} else {
			rsort($sortedArray, $sortFlags);
		}
		return new MArray($sortedArray);
	}
	
	public function toArray() : array {
		return $this->_set;
	}
	
	/******************** MObject ********************/
	
	public function compare(MMangoObject $object) : MComparisonResult {
		if ($this->count() < $object->count()) {
			return MComparisonResult::Ascending;
		} else if ($this->count() > $object->count()) {
			return MComparisonResult::Descending;
		} else {
			return MComparisonResult::Same;
		}
	}
	
}

