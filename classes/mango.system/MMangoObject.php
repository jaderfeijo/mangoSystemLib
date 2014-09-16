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
	
	package('mango.system');
	
	/**
	 * Low level interface to which all Mango Objects must conform
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system
	 *
	 */
	interface MMangoObject {
		
		const ORDERED_DESCENDING = -1;
		const ORDERED_SAME = 0;
		const ORDERED_ASCENDING = 1;
		
		//
		// ************************************************************
		//
		
		/**
		 * Compares this object to the specified object
		 *
		 * This method compares wether this object is equal to the object
		 * specified by $object
		 *
		 * @param MMangoObject $object The object to compare this object to
		 *
		 * @return bool True if the objects are equal, false otherwise
		 */
		public function equals(MMangoObject $object);
		
		/**
		 * Compares this object to the specified object and returns
		 * a value specifying whether the object is smaller than, equal to
		 * or greater than the object it is being called on
		 *
		 * @param MMangoObject $object An object to compare this object to
		 *
		 * @see MMangoObject::ORDERED_DESCENDING
		 * @see MMangoObject::ORDERED_SAME
		 * @see MMangoObject::ORDERED_ASCENDING
		 *
		 * @returns	int An integer containing a constant which identifies
		 * how this object compares to the specified object
		 */
		public function compare(MMangoObject $object);
		
		/**
		 * Returns the class name of the current object
		 *
		 * @return MString The objects' class name
		 */
		public function className();
		
		/**
		 * Returns the package name of the current object
		 *
		 * @return MString The objects' package name
		 */
		public function packageName();
		
		/**
		 * Returns the fully qualified class name of the current object
		 *
		 * @return MString The objects' fully qualified class name
		 */
		public function fullClassName();
		
		/**
		 * Returns a boolean indicating whether this object is of the
		 * specified class or has the class as one of its parents
		 *
		 * @return bool A boolean value idicating whether this object
		 * is of the specified class or inherits from it
		 */
		public function isKindOfClass(MString $class);
		
		/**
		 * Returns a String representation of the current object
		 *
		 * @return MString A String representation of the current object
		 */
		public function toString();
		
		/**
		 * @internal
		 *
		 * Allow any mango object to be used as a PHP string
		 *
		 * @return string A string representation of the object
		 */
		public function __toString();
		
		/**
		 * Returns a unique integer representing a hash of the contents of this object
		 *
		 * @return int An integer hash representing this object and it's contents
		 */
		public function hash();
		
	}

?>