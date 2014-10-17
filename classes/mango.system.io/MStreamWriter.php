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
	
	package('mango.system.io');
	
	import('mango.system.*');
	
	/**
	 * 
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system.io
	 *
	 */
	class MStreamWriter extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $stream;
		
		/**
		 * 
		 *
		 * @return MStreamWriter
		 */
		public function __construct(MOutputStream $stream) {
			parent::__construct();
			
			$this->stream = $stream;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return MOutputStream
		 */
		public function stream() {
			return $this->stream;
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isClosed() {
			return $this->stream->isClosed();
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function writeString(MString $string) {
			$this->stream->write($string->stringValue());
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function writeWord(MString $word) {
			$string = new MMutableString($word);
			if (!$word->hasSuffix(S(" "))) {
				$string->appendString(S(" "));
			}
			$this->writeString($string);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function writeLine(MString $line) {
			$string = new MMutableString($line);
			if (!$line->endsWith(S("\n"))) {
				$string->appendString(S("\n"));
			}
			$this->writeString($string);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function close() {
			$this->stream->close();
		}
		
	}

?>