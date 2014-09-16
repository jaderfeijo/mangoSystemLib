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
	
	package('mango.system.exceptions');
	
	import('mango.system.*');
	
	/**
	 * 
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system.exceptions
	 *
	 */
	class MIndexOutOfBoundsException extends MException {
		
		//
		// ************************************************************
		//
		
		protected $index;
		protected $lowerBound;
		protected $upperBound;
		
		/**
		 * 
		 *
		 * @return MIndexOutOfBoundsException
		 */
		public function __construct($index = 0, $lowerBound = 0, $upperBound = 0, MException $previous = null) {
			MAssertTypes('int', $index, 'int', $lowerBound, 'int', $upperBound);
			parent::__construct(
				Sf("Index [%d] out of bounds [%d -> %d]", $index, $lowerBound, $upperBound),
				MException::NUMBER_OUT_OF_RANGE_EXCEPTION_CODE,
				$previous
			);
			
			$this->index = $index;
			$this->lowerBound = $lowerBound;
			$this->upperBound = $upperBound;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function index() {
			return $this->index;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function lowerBound() {
			return $this->lowerBound;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function upperBound() {
			return $this->upperBound;
		}
		
	}

?>