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
	class MDate extends MObject {
		
		// General
		const ShortFormat = 0;
		const FullFormat = 1;
		
		// Hours
		const TwentyFourHourFormat = 0;
		const TwelveHourFormat = 1;
		
		//
		// ************************************************************
		//
		
		/**
		 * 
		 *
		 * @return MDate
		 */
		public static function parseString(MString $string) {
			return MDate::parse($string->stringValue());
		}
		
		/**
		 * 
		 *
		 * @return MDate
		 */
		public static function parse($str) {
			MAssertTypes('string', $str);
			
			$timestamp = null;
			
			if (is_numeric($str)) {
				$timestamp = (int)$str;
			} else {
				$timestamp = strtotime($str);
			}
			
			return new MDate($timestamp);
		}
		
		//
		// ************************************************************
		//
		
		protected $timestamp;
		protected $timeZone;
		
		/**
		 * 
		 *
		 * @return MDate
		 */
		public function __construct($timestamp = null, $timeZone = null) {
			MAssertTypes('int', $timestamp, 'string', $timeZone);
			parent::__construct();
			
			$this->timestamp = (is_null($timestamp) ? time() : $timestamp);
			$this->timeZone = (empty($timeZone) ? date_default_timezone_get() : $timeZone);
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function timestamp() {
			return $this->timestamp;
		}
		
		/******************** Time Zones ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setTimeZone($timeZone) {
			MAssertTypes('string', $timeZone);
			
			$this->timeZone = $timeZone;
		}
		
		/**
		 * 
		 *
		 * @return string
		 */
		public function timeZone() {
			return $this->timeZone;
		}
		
		/******************** Seconds ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function seconds() {
			return idate('s', $this->timestamp());
		}
		
		/******************** Minutes ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function minutes() {
			return idate('i', $this->timestamp());
		}
		
		/******************** Hours ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function hours($format = MDate::TwentyFourHourFormat) {
			MAssertTypes('int', $format);
			
			if ($format == MDate::TwelveHourFormat) {
				return idate('h', $this->timestamp());
			} else {
				return idate('H', $this->timestamp());
			}
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function meridiem() {
			return new MString(date('A', $this->timestamp()));
		}
		
		/******************** Days ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function day() {
			return idate('d', $this->timestamp());
		}
		
		/******************** Months ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function month() {
			return idate('m', $this->timestamp());
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function monthString($format = MDate::FullFormat) {
			MAssertTypes('int', $format);
			
			if ($format == MDate::ShortFormat) {
				return new MString(date('M', $this->timestamp()));
			} else {
				return new MString(date('F', $this->timestamp()));
			}
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function numberOfDaysInMonth() {
			return idate('t', $this->timestamp());
		}
		
		/******************** Years ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function year() {
			return idate('Y', $this->timestamp());
		}
		
		/******************** Weeks ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function week() {
			return idate('W', $this->timestamp());
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function dayOfTheWeek() {
			return idate('N', $this->timestamp());
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function dayOfTheWeekString($format = MDate::FullFormat) {
			MAssertTypes('int', $format);
			
			if ($format == MDate::ShortFormat) {
				return new MString(date('D', $this->timestamp()));
			} else {
				return new MString(date('l', $this->timestamp()));
			}
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return MDate
		 */
		public function dateByAddingDate(MDate $date) {
			return new MDate($this->timestamp() + $date->timestamp());
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function descriptionWithFormat($format) {
			return new MString(date($format, $this->timestamp()));
		}
		
		/**
		 * 
		 * @return bool
		 */
		public function isPast() {
			return (time() > $this->timestamp);
		}
		
		/**
		 * 
		 * @return bool
		 */
		public function isPresent() {
			return (time() == $this->timestamp);
		}
		
		/**
		 * 
		 * @return bool
		 */
		public function isFuture() {
			return (time() < $this->timestamp);
		}
		
		/******************** MObject ********************/
		
		/**
		 *
		 */
		public function compare(MMangoObject $object) {
			return N($this->timestamp())->compare(N($object->timestamp()));
		}
		
		/**
		 * 
		 */
		public function toString() {
			return S((string)$this->timestamp);
		}
		
		/**
		 * 
		 */
		public function hash() {
			return hexdec(md5($this->toString()));
		}
	}

?>