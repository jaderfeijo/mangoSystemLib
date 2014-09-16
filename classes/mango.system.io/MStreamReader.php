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
	class MStreamReader extends MObject {
		
		const CHUNK_SIZE = 8192; // 8k
		
		//
		// ************************************************************
		//
		
		protected $stream;
		protected $buffer;
		
		/**
		 * 
		 *
		 * @return MStreamReader
		 */
		public function __construct(MInputStream $stream) {
			parent::__construct();
			
			$this->stream = $stream;
			$this->buffer = new MMutableData();
		}
		
		/******************** Private ********************/
		
		/**
		 * @internal
		 *
		 * @return int
		 */
		private function _rebuffer($length = 1) {
			MAssertTypes('int', $length);
			
			if (!$this->endOfFile()) {
				if ($buffer->length() < $length) {
					$data = $this->stream->read(CHUNK_SIZE);
					$this->buffer->appendBytes($data);
				}
			}
			return $this->buffer->length();
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return MInputStream
		 */
		public function stream() {
			return $this->stream;
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function endOfFile() {
			return (($this->buffer->length() + $this->stream->available()) <= 0);
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function readCharacter() {
			if ($this->_rebuffer() > 0) {
				return new MString($this->buffer->consumeByte());
			}
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function readWord() {
			$word = new MMutableString();
			while (!$this->endOfFile()) {
				$char = $this->readCharacter();
				if ($char->rangeOfCharacterFromSet(MString::WORD_DELIMITER_CHARACTER) != MRange::RANGE_NOT_FOUND) {
					break;
				} else {
					$word->appendString($char);
				}
			}
			return $word->toString();
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function readLine() {
			$line = new MMutableString();
			while (!$this->endOfFile()) {
				$char = $this->readCharacter();
				if ($char->rangeOfCharacterFromSet(MString::LINE_DELIMITER_CHARACTER) != MRange::RANGE_NOT_FOUND) {
					break;
				} else {
					$word->appendString($char);
				}
			}
			return $word->toString();
		}
		
	}

?>