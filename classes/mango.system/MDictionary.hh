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
 * 
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MDictionary extends MObject {
	
	public static function parseString(MString $dictString) MDictionary {
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
	
	protected MMutableArray $_keys;
	protected MMutableArray $_values;
	
	public function __construct(array $dictionary = array()) {
		parent::__construct();
		
		$this->_keys = new MMutableArray(A(array_keys($dictionary)));
		$this->_values = new MMutableArray(A(array_values($dictionary)));
		
		$args = func_get_args();
		if (N(count($args))->isEven()) {
			for ($i = 0; $i < count($args); $i++) {
				$key = $args[$i];
				$object = $args[++$i];
				$this->_keys->addObject($key);
				$this->_values->addObject($object);
			}
		} else {
			throw new MException(S("Uneven number of keys and objects specified"));
		}
	}
	
	/******************** Properties ********************/
	
	public function allKeys() : MArray {
		return (MArray)$this->_keys;
	}
	
	public function allObjects() : MArray {
		return (MArray)$this->_values;
	}
	
	public function allValues() : MArray {
		return $this->allObjects();
	}
	
	public function hasObjectForKey(object $key) : bool {
		return $this->_keys->objectExists($key);
	}
	
	public function objectForKey(object $key) : ?object {
		$index = $this->_keys->indexOfObject($key);
		if ($index != MArray::ObjectNotFound) {
			return $this->_values->objectAtIndex($index);
		} else {
			return null;
		}
	}
	
	public function count() : int {
		return $this->_keys->count();
	}
	
	public function toArray() : array {
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
	
	public function toJSON() : MString {
		return S(json_encode($this->toArray()));
	}
	
	/******************** MObject Methods ********************/
	
	public function compare(MMangoObject $object) : MComparisonResult {
		return N($this->count())->compare(N($object->count()));
	}
	
	public function toString() : MString {
		$string = new MMutableString();
		foreach ($this->allKeys()->toArray() as $key) {
			$string->appendFormat("%s:%s", urlencode((string)$key), urlencode((string)$this->objectForKey($key)));
			if (!$this->allKeys()->isLastObject($key)) {
				$string->appendString(S("|"));
			}
		}
		return (MString)$string;
	}
	
	public function hash() : string {
		return hexdec(md5($this->toString()));
	}
	
}

