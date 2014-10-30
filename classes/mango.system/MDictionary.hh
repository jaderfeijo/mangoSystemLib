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
class MDictionary<Tk, Tv> extends MValue {
	
	public static function parseString(MString $dictString) : MDictionary<MString, MString> {
		$arr = $dictString->componentsSeparatedByString(S("|"));
		$dict = new MMutableDictionary(null);
		foreach ($arr->toVector() as $dictKeyPair) {
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
	
	protected Map<Tk, Tv> $_map;
	
	public function __construct(?KeyedTraversable<Tk, Tv> $kt = null) {
		parent::__construct();

		$this->_map = new Map(null);

		if ($kt !== null) {
			foreach ($kt as $k => $v) {
				$this->_map->set($k, $v);
			}
		}
	}
	
	/******************** Properties ********************/
	
	public function count() : int {
		return $this->_map->count();
	}

	public function keys() : MArray<Tk> {
		return MArray::withObjectsFromVector($this->_map->keys());
	}
	
	public function objects() : MArray<Tv> {
		return MArray::withObjectsFromVector($this->_map->values());
	}
	
	public function keyedTraversable() : KeyedTraversable<Tk, Tv> {
		return $this->toMap();
	}

	/******************** Methods ********************/

	public function hasObjectForKey(Tk $key) : bool {
		return $this->_map->contains($key);
	}
	
	public function objectForKey(Tk $key) : Tv {
		$object = $this->_map->get($key);
		if ($object === null) {
			throw new MUndefinedKeyException($key);
		}
		return $object;
	}
	
	public function toMap() : ConstMap<Tk, Tv> {
		return $this->_map;
	}

	public function toArray() : array {
		return $this->toMap()->toArray();
	}

	public function toJSON() : MString {
		return S(json_encode($this->toArray()));
	}

	/******************** MObject Methods ********************/
	
	public function equals(MMangoObject $object) : bool {
		if ($object instanceof MDictionary) {
			if ($this->count() == $object->count()) {
				foreach ($this->traversable() as $k => $o) {
					if ($object->objectForKey($k) != $o) {
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
		if ($object instanceof MDictionary) {
			if ($this->count() < $object->count()) {
				return MComparisonResult::Ascending;
			} else if ($this->count() > $object->count()) {
				return MComparisonResult::Descending;
			} else {
				return MComparisonResult::Same;
			}
		} else {
			throw new MException(S("Invalid object type"));
		}
	}
	
	public function toString() : MString {
		$string = new MMutableString();
		$keys = $this->allKeys();
		foreach ($keys->toVector() as $key) {
			$string->appendFormat("%s:%s", urlencode((string)$key), urlencode((string)$this->objectForKey($key)));
			if (!$keys->isLastObject($key)) {
				$string->appendString(S("|"));
			}
		}
		return $string;
	}
	
}

