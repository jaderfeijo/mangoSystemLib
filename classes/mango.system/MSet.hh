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
class MSet<T> extends MValue {

	public static function withObjectsFromArray<Tv>(array $objects) : MSet<Tv> {
		$set = new MMutableSet();
		foreach ($objects as $object) {
			$set->addObject($object);
		}
		return $set;
	}

	public static function withObjects<Tv>(Traversable<Tv> $objects) : MSet<Tv> {
		$set = new MMutableSet();
		foreach ($objects as $object) {
			$set->addObject($object);
		}
		return $set;
	}
	
	//
	// ************************************************************
	//
	
	protected Set<T> $_set;
	
	public function __construct(?Traversable<T> $t = null) {
		parent::__construct();

		$this->_set = new Set(null);

		if ($t !== null) {
			foreach ($t as $o) {
				$this->_set->add($o);
			}
		}
	}

	/******************** Properties ********************/

	public function count() : int {
		return $this->_set->count();
	}

	public function traversable() : Traversable<T> {
		return $this->toSet();
	}

	/******************** Methods ********************/
	
	public function containsObject(T $object) : bool {
		return $this->_set->contains($object);
	}

	public function anyObject() : T {
		foreach ($this->_set as $o) {
			return $o;
		}
		throw new MObjectNotFoundException();
	}

	public function toArray() : MArray<T> {
		return new MArray($this->toSet());
	}

	public function toSet() : ConstSet<T> {
		return $this->_set;
	}
	
	/******************** MObject ********************/
	
	public function equals(MMangoObject $object) : bool {
		if ($object instanceof MSet) {
			if ($this->count() == $object->count()) {
				foreach ($this->traversable() as $v) {
					if (!$object->containsObject($v)) {
						return false;
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
		if ($object instanceof MSet) {
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
		return Sf("MSet[%d]:\n%s", $this->count(), var_export($this->_set, true));
	}
	
}

