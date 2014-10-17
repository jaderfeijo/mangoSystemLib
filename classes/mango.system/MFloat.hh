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
class MFloat extends MNumber {

	protected float $_float;

	public function __construct(float $float) {
		$this->_float = $float;
	}

	/******************** Properties ********************/

	public function floatValue() : float {
		return $this->_float;
	}

	/******************** MObject ********************/

	public function equals(MMangoObject $object) : bool {
		return ($this->floatValue() == ((MFloat)$object)->floatValue());
	}

	public function compare(MMangoObject $object) : MComparisonResult {
		if ($this->floatValue() < ((MFloat)$object)->floatValue()) {
			return MComparisonResult::Ascending;
		} else if ($this->floatValue() > ((MFloat)$object)->floatValue()) {
			return MComparisonResult::Descending;
		} else {
			return MComparisonResult::Same;
		}
	}

	public function toString() : MString {
		return new MString((string)$this->floatValue());
	}

	public function hash() : string {
		return hexdec(md5($this->floatValue()));
	}

}

