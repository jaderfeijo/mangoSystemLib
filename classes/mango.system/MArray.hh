<?hh

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
class MArray<T> extends MValue {
	
	const int ObjectNotFound = -1;

	public static function withArray<Tv>(array $objects) : MArray<Tv> {
		$array = new MMutableArray();
		foreach ($objects as $object) {
			$array->addObject($object);
		}
		return $array;
	}

	public static function withObjectsFromArray<Tv>(MArray<Tv> $objects) : MArray<Tv> {
		return MArray::withObjects($objects->traversable());
	}

	public static function withObjects<Tv>(Traversable<Tv> $objects) : MArray<Tv> {
		$array = new MMutableArray();
		foreach ($objects as $object) {
			$array->addObject($object);
		}
		return $array;
	}

	//
	// ************************************************************
	//
	
	protected Vector<T> $_vector;
	
	public function __construct(?Traversable<T> $t = null) {
		parent::__construct();

		$this->_vector = new Vector($t);

		if ($t !== null) {
			foreach ($t as $o) {
				$this->_vector->add($o);
			}
		}
	}
	
	/******************** Properties ********************/
	
	public function count() : int {
		return $this->_vector->count();
	}

	public function lowerBound() : int {
		return 0;
	}

	public function upperBound() : int {
		return $this->count() - 1;
	}

	public function isEmpty() : bool {
		return ($this->count() <= 0);
	}
	
	public function traversable() : Traversable<T> {
		return $this->toVector();
	}

	/******************** Methods ********************/
	
	public function objectAtIndex(int $index) : T {
		if ($index < $this->lowerBound() || $index > $this->upperBound()) {
			throw new MIndexOutOfBoundsException($index, $this->lowerBound(), $this->upperBound());
		}
		return $this->_vector->at($index);
	}
	
	public function indexOfObject(T $object) : int {
		for ($i = 0; $i < $this->count(); $i++) {
			$current = $this->objectAtIndex($i);
			if ($object instanceof MMangoObject && $current instanceof MMangoObject) {
				if ($object->equals($current)) {
					return $i;
				}
			} else {
				if ($current == $object) {
					return $i;
				}
			}
		}
		return MArray::ObjectNotFound;
	}

	public function containsObject(T $object) : bool {
		return ($this->indexOfObject($object) != MArray::ObjectNotFound);
	}
	
	public function lastObject() : T {
		return $this->objectAtIndex($this->upperBound());
	}
	
	public function isLastObject(T $object) : bool {
		return ($this->indexOfObject($object) == $this->upperBound());
	}
	
	public function componentsJoinedByString(MString $separator) : MString {
		return new MString(implode($separator->stringValue(), $this->toArray()));
	}
	
	public function subarrayWithRange(MRange $range) : MArray<T> {
		return MArray::withObjectsFromArray(array_slice($this->toArray(), $range->location(), $range->length()));
	}
	
	public function subarrayFromIndex(int $index) : MArray<T> {
		return $this->subarrayWithRange(MRangeMake($index, $this->upperBound()));
	}
	
	public function subarrayToIndex(int $index) : MArray<T> {
		return $this->subarrayWithRange(MRangeMake(0, $index));
	}
	
	public function sortedArray(MArrayOrder $order = MArrayOrder::Ascending, int $sortFlags = SORT_REGULAR) : MArray<T> {
		$sortedArray = $this->toArray();
		if ($order == MArrayOrder::Ascending) {
			sort($sortedArray, $sortFlags);
		} else {
			rsort($sortedArray, $sortFlags);
		}
		return MArray::withObjectsFromArray($sortedArray);
	}
	
	public function arrayByAppendingArray(MArray<T> $array) : MArray<T> {
		$newArray = new MMutableArray();
		$newArray->addObjects($this);
		$newArray->addObjects($array);
		return $newArray;
	}
	
	public function toJSON() : MString {
		return S(json_encode($this->toArray()));
	}
	
	public function toArray() : array {
		return $this->toVector()->toArray();
	}

	public function toVector() : ConstVector<T> {
		return $this->_vector;
	}

	/******************** MObject Methods ********************/
	
	public function equals(MMangoObject $object) : bool {
		if ($object instanceof MArray) {
			if ($this->count() == $object->count()) {
				for ($i = 0; $i < $this->count(); $i++) {
					$obj1 = $this->objectAtIndex($i);
					$obj2 = $this->objectAtIndex($i);
					if ($obj1 instanceof MMangoObject && $obj2 instanceof MMangoObject) {
						if (!$obj1->equals($obj2)) {
							return false;
						}
					} else {
						if ($obj1 !== $obj2) {
							return false;
						}
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function compare(MMangoObject $object) : MComparisonResult {
		if ($object instanceof MArray) {
			if ($this->count() < $object->count()) {
				return MComparisonResult::Ascending;
			} else if ($this->count() > $object->count()) {
				return MComparisonResult::Descending;
			} else {
				return MComparisonResult::Same;
			}
		} else {
			return MComparisonResult::Descending;
		}
	}
	
	public function toString() : MString {
		return Sf("MArray[%d]:\n%s", $this->count(), var_export($this->_vector, true));
	}
	
}

