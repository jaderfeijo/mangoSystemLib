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
class MIndexOutOfBoundsException extends MException {
	
	protected int $_index;
	protected int $_lowerBound;
	protected int $_upperBound;
	
	public function __construct(int $index = 0, int $lowerBound = 0, int $upperBound = 0, ?MException $previous = null) {
		parent::__construct(
			Sf("Index [%d] out of bounds [%d -> %d]", $index, $lowerBound, $upperBound),
			MException::NumberOutOfBoundsExceptionCode,
			$previous
		);
		
		$this->_index = $index;
		$this->_lowerBound = $lowerBound;
		$this->_upperBound = $upperBound;
	}
	
	/******************** Properties ********************/
	
	public function index() : int {
		return $this->_index;
	}
	
	public function lowerBound() : int {
		return $this->_lowerBound;
	}
	
	public function upperBound() : int {
		return $this->_upperBound;
	}
	
}

