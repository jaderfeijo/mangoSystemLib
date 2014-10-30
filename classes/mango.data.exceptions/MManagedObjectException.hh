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
 * This exception forms the base for all exceptions that happen inside Managed
 * Objects
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MManagedObjectException extends MException {
	
	/**
	 * Creates a new instance of MManagedObjectException
	 *
	 * @param MManagedObject $object The Managed Object that cause the exception to be raised
	 * @param ?MString $object The description of the error
	 * @param ?MException $previous The previous exception that caused this exception to
	 * be raised
	 *
	 * @return MManagedObjectException The new exception instance
	 */
	public function __construct(MManagedObject $object, ?MString $description = null, ?MException $previous = null) {
		if ($description == null) $description = S("Unknown Managed Object Error");
		$description = Sf("%s [%s]", $description, $object);
	
		parent::__construct(
			$description,
			MException::ManagedObjectExceptionCode,
			$previous
		);
	}
	
}

