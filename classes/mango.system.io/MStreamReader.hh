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
class MStreamReader extends MObject {
	
	const int ChunkSize = 8192; // 8k
	
	protected MInputStream $_stream;
	protected MMutableData $_buffer;
	
	public function __construct(MInputStream $stream) {
		parent::__construct();
		$this->_stream = $stream;
		$this->_buffer = new MMutableData(null);
	}
	
	/******************** Private ********************/
	
	private function _rebuffer(int $length = 1) : int {
		if (!$this->endOfFile()) {
			if ($this->_buffer->length() < $length) {
				$data = $this->_stream->read(MStreamReader::ChunkSize);
				$this->_buffer->appendData($data);
			}
		}
		return $this->_buffer->length();
	}
	
	/******************** Properties ********************/
	
	public function stream() : MInputStream {
		return $this->_stream;
	}
	
	public function endOfFile() : bool {
		return (($this->_buffer->length() + $this->_stream->available()) <= 0);
	}
	
	/******************** Methods ********************/
	
	public function readCharacter() : MString {
		$this->_rebuffer();
		return new MString($this->_buffer->consumeByte());
	}
	
	public function readWord() : MString {
		$word = new MMutableString();
		while (!$this->endOfFile()) {
			$char = $this->readCharacter();
			if ($char->rangeOfCharacterFromSet(MString::WordDelimiterCharacters) != MRange::NotFound()) {
				break;
			} else {
				$word->appendString($char);
			}
		}
		return $word->toString();
	}
	
	public function readLine() : MString {
		$line = new MMutableString();
		while (!$this->endOfFile()) {
			$char = $this->readCharacter();
			if ($char->rangeOfCharacterFromSet(MString::LineDelimiterCharacters) != MRange::NotFound()) {
				break;
			} else {
				$line->appendString($char);
			}
		}
		return $line->toString();
	}
	
}

