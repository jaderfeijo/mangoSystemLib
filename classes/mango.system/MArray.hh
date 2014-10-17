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

enum MArrayOrder : int {
	Ascending = 0;
	Descending = 1;
}

/**
 * 
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MArray extends MObject {
	
	const int ObjectNotFound = -1;
	
	//
	// ************************************************************
	//
	
	protected array $_array;
	
	public function __construct(array $array = array()) {
		parent::__construct();
		
		$this->_array = $array;
	}
	
	/******************** Properties ********************/
	
	public function lowerBound() : int {
		return 0;
	}
	
	public function upperBound() : int {
		return $this->count() - 1;
	}
	
	public function count() : int {
		return count($this->_array);
	}
	
	/******************** Methods ********************/
	
	public function objectAtIndex(int $index) : object {
		if ($index >= $this->lowerBound() && $index <= $this->upperBound()) {
			return $this->_array[$index];
		} else {
			throw new MIndexOutOfBoundsException($index, $this->lowerBound(), $this->upperBound());
		}
	}
	
	public function indexOfObject(object $object) : int {
		for ($i = 0; $i < $this->count(); $i++) {
			$current = $this->_array[$i];
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
	
	public function containsObject(object $object) : bool {
		return ($this->indexOfObject($object) != MArray::ObjectNotFound);
	}
	
	public function lastObject() : ?object {
		if ($this->count() > 0) {
			return $this->objectAtIndex($this->upperBound());
		} else {
			return null;
		}
	}
	
	public function isLastObject(object $object) : void {
		return $this->indexOfObject($object) == $this->upperBound();
	}
	
	public function componentsJoinedByString(MString $separator) : MString {
		return new MString(implode($separator->stringValue(), $this->_array));
	}
	
	public function subarrayWithRange(MRange $range) : MArray {
		return new MArray(array_slice($this->_array, $range->location(), $range->length()));
	}
	
	public function subarrayFromIndex(int $index) : MArray {
		return $this->subarrayWithRange(MRangeMake($index, $this->upperBound()));
	}
	
	public function subarrayToIndex(int $index) : MArray {
		return $this->subarrayWithRange(MRangeMake(0, $index));
	}
	
	public function sortedArray(MArrayOrder $order = MArrayOrder::Ascending, $sortFlags = SORT_REGULAR) : MArray {
		$sortedArray = $this->_array;
		if ($order == MArrayOrder::Ascending) {
			sort($sortedArray, $sortFlags);
		} else {
			rsort($sortedArray, $sortFlags);
		}
		return new MArray($sortedArray);
	}
	
	public function sortedArrayUsingMethod(callback $method, MArrayOrder $order = MArrayOrder::Ascending) : MArray {
		$array = $this->toArray();
		usort($array, function ($a, $b) {
			$result = $a->{$method}($b);
			if (MArray::ORDER_DESCENDING) {
				if ($result == MComparisonResult::Ascending) {
					$result = MComparisonResult::Descending;
				} else if ($result == MComparisonResult::Descending) {
					$result = MComparisonResult::Ascending;
				}
			}
			return $result;
		});
		return new MArray($array);
	}
	
	public function arrayByAppendingArray(MArray $array) : MArray {
		return A(array_merge($this->_array, $array->_array));
	}
	
	public function toArray() : array {
		return $this->_array;
	}
	
	public function toJSON() : MString {
		return S(json_encode($this->_array));
	}
	
	/******************** MObject Methods ********************/
	
	public function compare(MMangoObject $object) : MComparisonResult {
		return N($this->count())->compare(N($object->count()));
	}
	
	public function toString() : MString {
		return MString::stringWithFormat("MArray[%d]:\n%s", $this->count(), var_export($this->_array, true));
	}
	
}

