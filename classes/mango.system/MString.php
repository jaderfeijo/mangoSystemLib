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
	class MString extends MObject {
		
		public static $WORD_DELIMITER_CHARACTERS;
		public static $LINE_DELIMITED_CHARACTERS;
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		static function _init() {
			self::$WORD_DELIMITER_CHARACTERS = S(" .,\n\t");
			self::$LINE_DELIMITED_CHARACTERS = S("\n");
		}
		
		/******************** Static Methods ********************/
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public static function _stringWithFormat(array $args) {
			return new MString(call_user_func_array('sprintf', $args));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public static function stringWithFormat() {
			$args = func_get_args();
			return MString::_stringWithFormat($args);
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public static function stringWithRepeatingString(MString $string, $times) {
			MAssertTypes('MString', $string, 'int', $times);	
			return S(str_repeat($string->stringValue(), $times));
		}
		
		//
		// ************************************************************
		//
		
		protected $string;
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function __construct($string = "") {
			MAssertTypes('string', $string);
			parent::__construct();
			
			$this->string = $string;
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function endsWith(MString $suffix) {
			if ($suffix->length() > $this->length()) {
				return false;
			} else {
				$ending = $this->substringFromIndex($this->length() - $suffix->length());
				return $ending->equals($suffix);
			}
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function startsWith(MString $prefix) {
			if ($prefix->length() > $this->length()) {
				return false;
			} else {
				$start = $this->substringToIndex($prefix->length() - 1);
				return $start->equals($prefix);
			}
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isEmpty() {
			return empty($this->string);
		}
		
		/**
		 * 
		 *
		 * @return string
		 */
		public function stringValue() {
			return $this->string;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function length() {
			return strlen($this->stringValue());
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function toLower() {
			return new MString(strtolower($this->stringValue()));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function toUpper() {
			return new MString(strtoupper($this->stringValue()));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function stringByAppendingString(MString $string) {
			return new MString($this->stringValue() . $string->stringValue());
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function stringByAppendingLine(MString $line) {
			return new MString($this->stringValue() . $line->stringValue() . "\n");
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function stringByAppendingPathComponent(MString $component) {
			$pathSeparator = S("");
			if (!$this->endsWith(S("/"))) {
				$pathSeparator = S("/");
			}
			return $this->stringByAppendingString(Sf("%s%s", $pathSeparator, $component));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function stringByReplacingOccurrencesOfString(MString $find, MString $replace) {
			return new MString(str_replace($find->stringValue(), $replace->stringValue(), $this->stringValue()));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function stringByTrimmingCharactersInSet(MString $set) {
			return new MString(trim($this->stringValue(), $set->stringValue()));
		}
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function stringByTrimmingEmptySpaces() {
			return $this->stringByTrimmingCharactersInSet(S(" "));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function stringByEncodingHTMLEntities() {
			return new MString(htmlspecialchars($this->stringValue()));
		}
		
		/**
		 * 
		 * @return MString
		 */
		public function urlEncodedString() {
			return new MString(urlencode($this->stringValue()));
		}
		
		/**
		 * 
		 * @return MString
		 */
		public function urlDecodedString() {
			return new MString(urldecode($this->stringValue()));
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function componentsSeparatedByString(MString $separator, $allowEmptyStrings = true) {
			MAssertTypes('MString', $separator, 'bool', $allowEmptyStrings);
			
			$stringArray = new MMutableArray();
			$array = explode($separator->stringValue(), $this->stringValue());
			foreach($array as $str) {
				if ($allowEmptyStrings || !empty($str)) {
					$stringArray->addObject(S($str));
				}
			}
			return $stringArray;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function substringFromIndex($index) {
			MAssertTypes('int', $index);
			
			return $this->substringWithRange(MRangeMake($index, $this->length() - $index));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function substringWithRange(MRange $range) {
			if ($range->location() < 0) {
				throw new MIndexOutOfBoundsException($range->location(), 0, $this->length());
			}
			if (($range->location() + $range->length()) > $this->length()) {
				throw new MIndexOutOfBoundsException($range->location() + $range->length(), 0, $this->length());
			}
			return new MString(substr($this->stringValue(), $range->location(), $range->length()));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function substringToIndex($index) {
			MAssertTypes('int', $index);
			
			return $this->substringWithRange(MRangeMake(0, $index + 1));
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function characterAtIndex($index) {
			return $this->substringWithRange(MRangeMake($index, 1));
		}
		
		/**
		 * 
		 *
		 * @return MRange
		 */
		public function rangeOfString(MString $string) {
			$pos = strpos($this->stringValue(), $string->stringValue());
			if ($pos !== false) {
				return MRangeMake($pos, $string->length());
			} else {
				return MRange::RANGE_NOT_FOUND;
			}
		}
		
		/**
		 * 
		 *
		 * @return MRange
		 */
		public function rangeOfCharacterFromSet(MString $set) {
			$e = new MStringEnumerator($set);
			while ($e->moveNext()) {
				$char = $e->currentObject();
				$range = $this->rangeOfString($char);
				if ($range != MRange::RANGE_NOT_FOUND) {
					return $range;
				}
			}
			return MRange::RANGE_NOT_FOUND;
		}
		
		/******************** MObject ********************/
		
		/**
		 * 
		 */
		public function equals(MMangoObject $string) {
			return ($this->toString()->stringValue() == $string->toString()->stringValue());
		}
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			return strcmp($this->stringValue(), $object->stringValue());
		}
		
		/**
		 * 
		 */
		public function toString() {
			return $this;
		}
		
		/**
		 * 
		 */
		public function hash() {
			return hexdec(md5($this->stringValue()));
		}
		
	}
	
	MString::_init();

?>