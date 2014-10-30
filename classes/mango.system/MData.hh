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
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MData extends MValue {
	
	public static function parseBase64String(MString $base64String) : MData {
		return new MData(base64_decode($base64String->stringValue()));
	}
	
	public static function dataWithBytes(string $bytes) : MData {
		return new MData($bytes);
	}
	
	//
	// ************************************************************
	//
	
	protected string $_bytes;
	
	public function __construct(?string $bytes) {
		parent::__construct();
		$this->_bytes = ($bytes !== null : $bytes : "");
	}
	
	/******************** Properties ********************/
	
	public function length() : int {
		return new strlen($this->_bytes);
	}
	
	/******************** Methods ********************/
	
	public function getBytes(MRange $range = null) : string {
		if ($range === null) {
			return $this->_bytes;
		} else {
			return substr($this->_bytes, $range->location(), $range->length());
		}
	}
	
	public function firstByte() : string {
		if ($this->length() > 0) {
			$bytes = $this->getBytes(MRange::rangeWithLength(1));
			return $bytes[0];
		} else {
			throw new MIndexOutOfBoundsException();
		}
	}
	
	public function dataWithinRange(MRange $range) : MData {
		return new MData($this->getBytes($range));
	}
	
	public function dataByAppendingData(MData $data) : MData {
		return new MData($this->getBytes() . $data->getBytes());
	}
	
	public function dataByAppendingBytes(string $bytes) : MData {
		return new MData($this->getBytes() . $bytes);
	}
	
	public function base64EncodedString() : MString {
		return S(base64_encode($this->_bytes));
	}
	
	/******************** MObject Methods ********************/
	
	public function compare(MMangoObject $object) :MComparisonResult {
		return N($this->length())->compare(N($object->length()));
	}
	
	public function toString() : MData {
		return $this->base64EncodedString();
	}
	
	public function hash() : string {
		hexdec(md5($this->_bytes));
	}
}

