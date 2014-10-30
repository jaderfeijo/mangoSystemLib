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
 * Abstract class that forms the base for all Persistent Store Requests
 *
 * @see MFaultRequest
 * @see MFetchRequest
 * @see MSaveRequest
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
abstract class MPersistentStoreRequest extends MObject {
	
	protected MManagedObjectContext $_context;
	
	/**
	 * Constructs a new MPersistentStoreRequest instance. This method should be called
	 * by all subclasses of MPersistentStoreRequest
	 *
	 * @param MManagedObjectContext $context The context in which this Persistent Store
	 * Request will be performed
	 *
	 * @return MPersistentStoreRequest The newly created instance of MPersistentStoreRequest
	 */
	public function __construct(MManagedObjectContext $context) {
		parent::__construct();
		$this->_context = $context;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Sets the context where this request will be performed in
	 *
	 * @param MMangedObjectContext $context The context to perform this
	 * request in
	 *
	 * @return void
	 */
	public function setContext(MManagedObjectContext $context) : void {
		$this->_context = $context;
	}
	
	/**
	 * Returns the context where this request will be performed in
	 *
	 * @return MManagedObjectContext The context this request will be
	 * performed in
	 */
	public function context() : MManagedObjectContext {
		return $this->_context;
	}
	
}

