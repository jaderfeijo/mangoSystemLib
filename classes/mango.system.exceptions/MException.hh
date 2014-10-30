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
class MException extends Exception implements MMangoObject {
	
	const int GeneralExceptionCode = 0;
	const int NaNExceptionCode = 1;
	const int IndexOutOfBoundsExceptionCode = 2;
	const int StreamExceptionCode = 3;
	const int StreamClosedExceptionCode = 4;
	const int EndOfStreamExceptionCode = 5;
	const int SeekNotSupportedExceptionCode = 6;
	const int InvalidDataTypeExceptionCode = 7;
	const int NumberOutOfBoundsExceptionCode = 8;
	const int ModelParseErrorExceptionCode = 9;
	const int PersistentStoreExceptionCode = 10;
	const int ManagedObjectExceptionCode = 11;
	const int MethodNotSupportedExceptionCode = 12;
	const int BadRequestExceptionCode = 13;
	const int InvalidOperationExceptionCode = 14;
	const int EntityNotFoundExceptionCode = 15;
	const int ParseErrorExceptionCode = 16;
	const int FieldUndefinedExceptionCode = 17;
	const int ParameterUndefinedExceptionCode = 18;
	const int InputOutputExceptionCode = 19;
	const int FileNotFoundExceptionCode = 20;
	const int UndefinedKeyExceptionCode = 21;
	const int ObjectNotFoundExceptionCode = 22;
	const int ApplicationInstanceAlreadyCreatedExceptionCode = 23;
	const int ResponseAlreadySentExceptionCode = 24;
	
	//
	// ************************************************************
	//
	
	protected MString $_description;
	protected int $_code;
	protected ?Exception $_previous;
	
	public function __construct(?MString $description = null, int $code = MException::GeneralExceptionCode, ?Exception $previous = null) {
		parent::__construct();
		
		$this->_description = ($description !== null ? $description : S("Unknown Exception"));
		$this->_code = $code;
		$this->_previous = $previous;
	}
	
	/******************** Exception ********************/
	
	public function message() : string {
		return $this->description()->stringValue();
	}
	
	/******************** Properties ********************/
	
	public function description() : MString {
		return $this->_description;
	}
	
	public function code() : int {
		return $this->_code;
	}
	
	public function previous() : ?Exception {
		return $this->_previous;
	}
	
	/******************** MMangoObject ********************/
	
	public function equals(MMangoObject $object) : bool {
		return ($this == $object);
	}
	
	public function compare(MMangoObject $object) : MComparisonResult {
		if ($object instanceof MException) {
			if ($this->code() < $object->code()) {
				return MComparisonResult::Ascending;
			} else if ($this->code() > $object->code()) {
				return MComparisonResult::Descending;
			} else {
				return MComparisonResult::Same;
			}
		} else {
			return MComparisonResult::Descending;
		}
	}
	
	public function className() : MString {
		return new MString(get_class($this));
	}
	
	public function packageName() : MString {
		return S(__DIR__)->componentsSeparatedByString(S("/"))->lastObject();
	}
	
	public function fullClassName() : MString {
		return Sf("%s.%s", $this->packageName(), $this->className());
	}
	
	public function isKindOfClass(MString $class) : bool {
		return is_a($this, $class->stringValue());
	}
	
	public function toString() : MString {
		return MString::stringWithFormat("%s[%s]: \"%s\"\n%s", $this->className(), $this->code(), $this->description(), $this->previous());
	}
	
	public function __toString() : string {
		return $this->toString()->stringValue();
	}
	
	public function hash() : int {
		return 0;
	}
	
}

