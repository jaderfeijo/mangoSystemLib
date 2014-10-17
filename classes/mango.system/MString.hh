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

enum MStringDelimiters : MString {
	WordDelimiterCharacters : S(" .,\n\t");
	LineDelimiterCharacters : S("\n");
}

/**
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MString extends MObject {
	
	/******************** Static Methods ********************/
	
	public static function stringWithFormat() : MString {
		$args = func_get_args();
		return new MString(call_user_func_array('sprintf', $args));
	}
	
	public static function stringWithRepeatingString(MString $string, int $times) : MString {
		return S(str_repeat($string->stringValue(), $times));
	}
	
	//
	// ************************************************************
	//
	
	protected string $_string;
	
	public function __construct(string $string = "") {
		parent::__construct();
		$this->_string = $string;
	}
	
	/******************** Methods ********************/
	
	public function endsWith(MString $suffix) : bool {
		if ($suffix->length() > $this->length()) {
			return false;
		} else {
			$ending = $this->substringFromIndex($this->length() - $suffix->length());
			return $ending->equals($suffix);
		}
	}
	
	public function startsWith(MString $prefix) : bool {
		if ($prefix->length() > $this->length()) {
			return false;
		} else {
			$start = $this->substringToIndex($prefix->length() - 1);
			return $start->equals($prefix);
		}
	}
	
	public function isEmpty() : bool {
		return empty($this->_string);
	}
	
	public function stringValue() : string {
		return $this->_string;
	}

	public function intValue() : int {
		return intval($this->stringValue());
	}

	public function floatValue() : float {
		return floatval($this->stringValue());
	}

	public function boolValue() : bool {
		return boolval($this->stringValue());
	}
	
	public function length() : int {
		return strlen($this->stringValue());
	}
	
	public function toLower() : MString {
		return new MString(strtolower($this->stringValue()));
	}
	
	public function toUpper() : MString {
		return new MString(strtoupper($this->stringValue()));
	}
	
	public function stringByAppendingString(MString $string) : MString {
		return new MString($this->stringValue() . $string->stringValue());
	}
	
	public function stringByAppendingLine(MString $line) : MString {
		return new MString($this->stringValue() . $line->stringValue() . "\n");
	}
	
	public function stringByAppendingPathComponent(MString $component) : MString {
		$pathSeparator = S("");
		if (!$this->endsWith(S("/"))) {
			$pathSeparator = S("/");
		}
		return $this->stringByAppendingString(Sf("%s%s", $pathSeparator, $component));
	}
	
	public function stringByReplacingOccurrencesOfString(MString $find, MString $replace) : MString {
		return new MString(str_replace($find->stringValue(), $replace->stringValue(), $this->stringValue()));
	}
	
	public function stringByTrimmingCharactersInSet(MString $set) : MString {
		return new MString(trim($this->stringValue(), $set->stringValue()));
	}
	
	public function stringByTrimmingEmptySpaces() : MString {
		return $this->stringByTrimmingCharactersInSet(S(" "));
	}
	
	public function stringByEncodingHTMLEntities() : MString {
		return new MString(htmlspecialchars($this->stringValue()));
	}
	
	public function urlEncodedString() : MString {
		return new MString(urlencode($this->stringValue()));
	}
	
	public function urlDecodedString() : MString {
		return new MString(urldecode($this->stringValue()));
	}
	
	public function componentsSeparatedByString(MString $separator, bool $allowEmptyStrings = true) : MArray {
		$stringArray = new MMutableArray();
		$array = explode($separator->stringValue(), $this->stringValue());
		foreach($array as $str) {
			if ($allowEmptyStrings || !empty($str)) {
				$stringArray->addObject(S($str));
			}
		}
		return $stringArray;
	}
	
	public function substringFromIndex(int $index) : MString {
		return $this->substringWithRange(MRangeMake($index, $this->length() - $index));
	}
	
	public function substringWithRange(MRange $range) : MString {
		if ($range->location() < 0) {
			throw new MIndexOutOfBoundsException($range->location(), 0, $this->length());
		}
		if (($range->location() + $range->length()) > $this->length()) {
			throw new MIndexOutOfBoundsException($range->location() + $range->length(), 0, $this->length());
		}
		return new MString(substr($this->stringValue(), $range->location(), $range->length()));
	}
	
	public function substringToIndex(int $index) : MString {
		return $this->substringWithRange(MRangeMake(0, $index + 1));
	}
	
	public function characterAtIndex(int $index) : MString {
		return $this->substringWithRange(MRangeMake($index, 1));
	}
	
	public function rangeOfString(MString $string) : MRange {
		$pos = strpos($this->stringValue(), $string->stringValue());
		if ($pos !== false) {
			return MRangeMake($pos, $string->length());
		} else {
			return MRange::NotFound();
		}
	}
	
	public function rangeOfCharacterFromSet(MString $set) : MRange {
		$e = new MStringEnumerator($set);
		while ($e->moveNext()) {
			$char = $e->currentObject();
			$range = $this->rangeOfString($char);
			if ($range != MRange::NotFound()) {
				return $range;
			}
		}
		return MRange::NotFound();
	}
	
	/******************** MObject ********************/
	
	public function equals(MMangoObject $string) : bool {
		return ($this->toString()->stringValue() == $string->toString()->stringValue());
	}
	
	public function compare(MMangoObject $object) : MComparisonResult {
		$cmp = strcmp($this->stringValue(), $object->stringValue());
		if ($cmp < 0) {
			return MComparisonResult::Ascending;
		} else if ($cmd > 0) {
			return MComparisonResult::Descending;
		} else {
			return MComparisonResult::Same;
		}
	}
	
	public function toString() : MString {
		return $this;
	}
	
	public function hash() : string {
		return hexdec(md5($this->stringValue()));
	}
	
}

