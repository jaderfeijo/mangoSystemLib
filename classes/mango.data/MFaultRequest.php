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
	
	package('mango.data');
	
	import('mango.data.exceptions.*');
	import('mango.system.*');
	
	/**
	 * This class holds the information necessary to perform
	 * a Fault Request to the data store.
	 *
	 * A Fault Request is a request to populate the data into a faulted
	 * object to the data store. When you first fetch an object from the
	 * data store, it is what is called a Fault, that is, it only contains
	 * the data necessary to identify where it's data is located in the
	 * data store, namely, it only contains it's ID. Once you try to
	 * access the object's properties for the first time, the system creates
	 * an MFaultRequest instance which requests the data for that particular
	 * object before it can be used.
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MFaultRequest extends MPersistentStoreRequest {
		
		//
		// ************************************************************
		//
		
		protected $faults;
		
		/**
		 * Creates a new instance of the MFaultRequest class
		 *
		 * This method creates a new instance of the MFaultRequest class and takes
		 * the context where the fault operation is to be performed.
		 *
		 * @param MManagedObjectContext $context The context where the fault operation
		 * is to be performed.
		 *
		 * @return MFaultRequest The newly created MFaultRequest instance
		 */
		public function __construct(MManagedObjectContext $context) {
			parent::__construct(MPersistentStoreRequest::FaultRequestType, $context);
			
			$this->faults = new MMutableArray();
		}
		
		/******************** Properties ********************/
		
		/**
		 * Adds a fault object to the request
		 *
		 * Use this method to add fault objects to your request. This way
		 * you can fetch them all at once and save in performance compared
		 * to fetching them one by one.
		 *
		 * @see MFaultRequest::removeFault()
		 * @see MFaultRequest::faults()
		 *
		 * @param MManagedObject $fault The fault Managed Object to add
		 *
		 * @return void
		 */
		public function addFault(MManagedObject $fault) {
			if (!$fault->isFault()) {
				throw new MPersistentStoreException(S("Managed Object is not a valid fault"));
			}
			$this->faults->addObject($fault);
		}
		
		/**
		 * Removes a fault object from the request
		 *
		 * @see MFaultRequest::addFault()
		 * @see MFaultRequest::faults()
		 *
		 * @param MManagedObject $fault The fault Managed Object to remove
		 *
		 * @return void
		 */
		public function removeFault(MManagedObject $fault) {
			$this->faults->removeObject($fault);
		}
		
		/**
		 * Returns an Array containing all the fault objects in this request
		 *
		 * @see MFaultRequest::addFault()
		 * @see MFaultRequest::removeFault()
		 *
		 * @return MArray An Array containg all the faults in this request
		 */
		public function faults() {
			return $this->faults;
		}
		
	}

?>