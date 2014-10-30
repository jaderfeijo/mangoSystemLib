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
 * This exception is thrown when an error occours whilst parsing a Mango Data
 * Model
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MModelParseErrorException extends MException {
	
	protected MFile $_modelFile;
	
	/**
	 * Creates a new model parse error exception instance
	 *
	 * @param MFile $modelFile The file that caused the parse error
	 * @param MString|null $description The description of the error
	 * @param MException|null $previous The previous exception that cause this error to
	 * happen
	 *
	 * @return MModelParseErrorException The new exception instance
	 */
	public function __construct(MFile $modelFile, ?MString $description = null, ?MException $previous = null) {
		$d = $description;
		if ($d === null) {
			$d = Sf("Model Parse Error on file [%s]", $modelFile->path()->stringValue());
		} else {
			$d = $d->stringByAppendingString(Sf("\nModel file: [%s]", $modelFile->path()->stringValue()));
		}
		
		parent::__construct(
			$d,
			MException::ModelParseErrorExceptionCode,
			$previous
		);
		
		$this->_modelFile = $modelFile;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Returns the Model File that caused this exception to be thrown
	 *
	 * @return MFile The Model file that caused the exception to be thrown
	 */
	public function modelFile() : MFile {
		return $this->_modelFile;
	}
	
}

