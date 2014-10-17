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

enum MDateFormat : int {
	Short = 0;
	Full = 1;
}

enum MHourFormat : int {
	TwentyFourHour : 0;
	TwelveHour : 1;
}

/**
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MDate extends MObject {
	
	//
	// ************************************************************
	//
	
	public static function parseString(MString $string) : MDate {
		return MDate::parse($string->stringValue());
	}
	
	public static function parse(string $str) : MDate {
		$timestamp = null;
		
		if (is_numeric($str)) {
			$timestamp = parseInt($str);
		} else {
			$timestamp = strtotime($str);
		}
		
		return new MDate($timestamp);
	}
	
	//
	// ************************************************************
	//
	
	protected int $_timestamp;
	protected string $_timeZone;
	
	/**
	 * 
	 *
	 * @return MDate
	 */
	public function __construct(?int $timestamp = null, ?string $timeZone = null) {
		parent::__construct();
		
		$this->_timestamp = ($timestamp === null ? time() : $timestamp);
		$this->_timeZone = ($timeZone === null ? date_default_timezone_get() : $timeZone);
	}
	
	/******************** Properties ********************/
	
	public function timestamp() : int {
		return $this->_timestamp;
	}
	
	/******************** Time Zones ********************/
	
	public function setTimeZone(string $timeZone) : void {
		$this->_timeZone = $timeZone;
	}
	
	public function timeZone() : string {
		return $this->_timeZone;
	}
	
	/******************** Seconds ********************/
	
	public function seconds() : int {
		return idate('s', $this->timestamp());
	}
	
	/******************** Minutes ********************/
	
	public function minutes() : int {
		return idate('i', $this->timestamp());
	}
	
	/******************** Hours ********************/
	
	public function hours(MHourFormat $format = MHourFormat::TwentyFourHour) : int {
		if ($format == MHourFormat::TwelveHour) {
			return idate('h', $this->timestamp());
		} else {
			return idate('H', $this->timestamp());
		}
	}
	
	public function meridiem() : MString {
		return new MString(date('A', $this->timestamp()));
	}
	
	/******************** Days ********************/
	
	public function day() : int {
		return idate('d', $this->timestamp());
	}
	
	/******************** Months ********************/
	
	public function month() : int {
		return idate('m', $this->timestamp());
	}
	
	public function monthString(MDateFormat $format = MDateFormat::Full) : MString {
		if ($format == MDateFormat::Short) {
			return new MString(date('M', $this->timestamp()));
		} else {
			return new MString(date('F', $this->timestamp()));
		}
	}
	
	public function numberOfDaysInMonth() : int {
		return idate('t', $this->timestamp());
	}
	
	/******************** Years ********************/
	
	public function year() : int {
		return idate('Y', $this->timestamp());
	}
	
	/******************** Weeks ********************/
	
	public function week() : int {
		return idate('W', $this->timestamp());
	}
	
	public function dayOfTheWeek() : int {
		return idate('N', $this->timestamp());
	}
	
	public function dayOfTheWeekString(MDateFormat $format = MDateFormat::Full) : void {
		if ($format == MDateFormat::Short) {
			return new MString(date('D', $this->timestamp()));
		} else {
			return new MString(date('l', $this->timestamp()));
		}
	}
	
	/******************** Methods ********************/
	
	public function dateByAddingDate(MDate $date) : MDate {
		return new MDate($this->timestamp() + $date->timestamp());
	}
	
	public function descriptionWithFormat(string $format) : MString {
		return new MString(date($format, $this->timestamp()));
	}
	
	public function isPast() : bool {
		return (time() > $this->timestamp());
	}
	
	public function isPresent() : bool {
		return (time() == $this->timestamp());
	}
	
	public function isFuture() : bool {
		return (time() < $this->timestamp());
	}
	
	/******************** MObject ********************/
	
	public function compare(MMangoObject $object) : MComparisonResult {
		return N($this->timestamp())->compare(N($object->timestamp()));
	}
	
	public function toString() : MString {
		return S((string)$this->timestamp());
	}
	
	public function hash() : string {
		return hexdec(md5($this->toString()));
	}
}

