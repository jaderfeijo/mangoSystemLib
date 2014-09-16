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
	
	import('mango.system.exceptions.*');
	
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
	class MNumber extends MObject {
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public static function parse($number) {
			$num = $number;
			if ($number instanceof MString) {
				$num = (string)$number;
			}
			
			if (is_numeric($num)) {
				if (is_float($num)) {
					return MNumber::parseFloat($num);
				} else if (is_int($num)) {
					return MNumber::parseInt($num);
				} else if (is_string($num)) {
					return MNumber::parseInt(intval($num));
				}
			} else if (is_bool($num)) {
				return MNumber::parseBool($num);
			} else if (is_string($num)) {
				return MNumber::parseInt($num);
			}
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public static function parseInt($integer) {
			$int = $integer;
			if ($int instanceof MString) {
				$int = (string)$int;
			}
			
			return new MNumber((int)$int);
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public static function parseFloat($float) {
			$f = $float;
			if ($f instanceof MString) {
				$f = (string)$float;
			}
			
			return new MNumber((float)$f);
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public static function parseBool($boolean) {
			$bool = $boolean;
			if ($bool instanceof MString) {
				$bool = (string)$bool;
			}
			
			if (is_bool($bool)) {
				return new MNumber((int)$bool);
			} else if (is_numeric($bool)) {
				return new MNumber((int)$bool);
			} else if (is_string($bool)) {
				if (strtolower($bool) == "on" || strtolower($bool) == "true" || strtolower($bool) == "yes") {
					return new MNumber(1);
				} else {
					return new MNumber(0);
				}
			} else {
				return false;
			}
		}
		
		//
		// ************************************************************
		//
		
		protected $number;
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function __construct($number = 0) {
			parent::__construct();
			
			if (!is_numeric($number)) throw new MNaNException($number);
			$this->number = $number;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function intValue() {
			return (int)$this->number;
		}
		
		/**
		 * 
		 *
		 * @return float
		 */
		public function floatValue() {
			return (float)$this->number;
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function boolValue() {
			return (bool)$this->number;
		}
		
		/**
		 * 
		 *
		 * @return number
		 */
		public function value() {
			return $this->number;
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isEven() {
			return ($this->value() % 2 == 0);
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isOdd() {
			return !$this->isEven();
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isGreaterThan(MNumber $number) {
			return ($this->value() > $number->value());
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isLessThan(MNumber $number) {
			return ($this->value() < $number->value());
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isGreaterThanOrEqualTo(MNumber $number) {
			return ($this->value() >= $number->value());
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isLessThanOrEqualTo($number) {
			return ($this->value() <= $number->value());
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isWithinBounds(MNumber $lowerBound, MNumber $upperBound) {
			return ($this->isGreaterThanOrEqualTo($lowerBound) && $this->isLessThanOrEqualTo($upperBound));
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function numberBySumming(MNumber $number) {
			return new MNumber($this->value() + $number->value());
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function numberBySubtracting(MNumber $number) {
			if (!is_numeric($number)) throw new MNaNException($number);
			return new MNumber($this->value() - $number);
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function numberByIncrementing() {
			return new MNumber($this->value() + 1);
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function numberByDecrementing() {
			return new MNumber($this->value() - 1);
		}
		
		/******************** MObject ********************/
		
		/**
		 *
		 */
		public function equals(MMangoObject $object) {
			return ($this->number == $object->number);
		}
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			if ($this->isGreaterThan($object)) {
				return MMangoObject::ORDERED_DESCENDING;
			} else if ($this->equals($object)) {
				return MMangoObject::ORDERED_SAME;
			} else if ($this->isLessThan($object)) {
				return MMangoObject::ORDERED_ASCENDING;
			} else {
				return MMangoObject::ORDERED_SAME;
			}
		}
		
		/**
		 * 
		 */
		public function toString() {
			return new MString((string)$this->number);
		}
		
		/**
		 * 
		 */
		public function hash() {
			return hexdec(md5($this->number));
		}
		
	}

?>