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
	import('mango.system.exceptions.*');
	
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
	class MFileInputStream extends MInputStream {
		
		//
		// ************************************************************
		//
		
		protected $file;
		protected $handle;
		
		/**
		 * 
		 *
		 * @return MFileInputStream
		 */
		public function __construct(MFile $file) {
			parent::__construct();
			
			$this->file = $file;
			$this->handle = fopen($file->path()->stringValue(), 'r');
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return MFile
		 */
		public function file() {
			return $this->file;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function fileSize() {
			return filesize($this->handle);
		}
		
		/******************** MStream Methods ********************/
		
		/**
		 * 
		 */
		public function isSeekSupported() {
			return true;
		}
		
		/**
		 * 
		 */
		public function position() {
			if (!$this->isClosed()) {
				return ftell($this->handle);
			} else {
				throw new MStreamClosedException($this);
			}
		}
		
		/**
		 * 
		 */
		public function isClosed() {
			return is_null($this->handle);
		}
		
		/**
		 * 
		 */
		public function seek($position) {
			MAssertTypes('int', $position);
			
			if ($position < 0) {
				throw new MStreamException($this, S("The position specified is invalid. Needs to be a positive number"));
			}
			
			if (!$this->isClosed()) {
				if ($position < $this->fileSize()) {
					fseek($this->handle, $position);
				} else {
					throw new MEndOfStreamException($this);
				}
			} else {
				throw new MStreamClosedException($this);
			}
		}
		
		/**
		 * 
		 */
		public function close() {
			if (!$this->isClosed()) {
				if (fclose($this->handle)) {
					unset($this->handle);
					return true;
				}
				return false;
			} else {
				throw new MStreamClosedException($this);
			}
		}
		
		/******************** MInputStream Methods ********************/
		
		/**
		 * 
		 */
		public function available() {
			if (!$this->isClosed()) {
				return $this->fileSize() - $this->position();
			} else {
				throw new MStreamClosedException($this);
			}
		}
		
		/**
		 * 
		 */
		public function read($length) {
			MAssertTypes('int', $length);
			
			if (!$this->isClosed()) {
				if ($this->available() > 0) {
					$data = fread($this->handle, $length);
					if ($data !== false) {
						return $data;
					} else {
						throw new MStreamException($this, S("An error occurred while trying to read the file!"));
					}
				} else {
					throw new MEndOfStreamException($this);
				}
			} else {
				throw new MStreamClosedException($this);
			}
		}
		
		/**
		 * 
		 */
		public function skip($count) {
			MAssertTypes('int', $count);
			
			if (!$this->isClosed()) {
				$newPosition = $this->position() + $count;
				if ($newPosition >= $this->fileSize()) $newPosition = $this->fileSize() - 1;
				$this->seek($newPosition);
			} else {
				throw new MStreamClosedException($this);
			}
		}
		
		/**
		 * 
		 */
		public function reset() {
			$this->seek(0);
		}
		
	}

?>