<?hh

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
class MFileOutputStream extends MOutputStream {
	
	protected MFile $_file;
	protected resource $_handle;
	
	public function __construct(MFile $file) {
		parent::__construct();
		$this->_file = $file;
		$this->_handle = fopen($file->path()->stringValue(), 'a');	
	}
	
	/******************** Properties ********************/
	
	public function file() : MFile {
		return $this->_file;
	}
			
	/******************** MStream Methods ********************/
	
	public function isSeekSupported() : bool {
		return true;
	}
	
	public function position() : int {
		if ($this->isClosed()) {
			throw new MStreamClosedException($this);
		}
		return ftell($this->_handle);
	}
	
	public function isClosed() : bool {
		return ($this->_handle === null);
	}
	
	public function seek(int $position) : void {
		if ($this->isClosed()) {
			throw new MStreamClosedException($this);
		}
		fseek($this->_handle, $position);
	}
	
	public function close() : bool {
		if ($this->isClosed()) {
			throw new MStreamClosedException($this);
		}

		if ($this->flush()) {
			if (fclose($this->_handle)) {
				unset($this->_handle);
				return true;
			}
		}

		return false;
	}
	
	/******************** MOutputStream Methods ********************/
	
	public function write(string $data) : int {
		if ($this->isClosed()) {
			throw new MStreamClosedException($this);
		}

		$written = fwrite($this->_handle, $data);
		if ($written === false) {
			throw new MStreamException($this, S("An error occurred while trying to write to the file."));
		}

		return $written;
	}
	
	public function flush() : bool {
		if ($this->isClosed()) {
			throw new MStreamClosedException($this);
		}
		return fflush($this->_handle);
	}
	
}

