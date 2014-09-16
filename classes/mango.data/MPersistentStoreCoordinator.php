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
	 * 
	 * @todo Verify integrity of affected objects across persistent stores
	 * @todo Implement algorithm to fetch data from the fastest available database
	 *
	 */
	
	/**
	 * This class connects to a Managed Object Context and coordinates operations
	 * between a collection of different Persistent Stores
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MPersistentStoreCoordinator extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $model;
		protected $persistentStores;
		protected $managedObjectContext;
		
		/**
		 * Creates a new Persistent Store Coordinator instance
		 *
		 * @param MManagedObjectMode $mode The model to use for across the
		 * different Persistent Stores
		 *
		 * @return MPersistentStoreCoordinator
		 */
		public function __construct(MManagedObjectModel $model) {
			parent::__construct();
			
			$this->model = $model;
			$this->persistentStores = new MMutableSet();
			$this->managedObjectContext = null;
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the Managed Object Model used by this Persistent Store
		 * Coordinator
		 *
		 * @return MManagedObjectModel The Model used by this Persistent Store
		 * Coordinator
		 */
		public function model() {
			return $this->model;
		}
		
		/**
		 * Adds a Persistent Store to this Persistent Store Coordinator's
		 * collection of Stores
		 *
		 * @param MPersistentStore $store The Persistent Store instance to
		 * add
		 *
		 * @return void
		 */
		public function addPersistentStore(MPersistentStore $store) {
			if ($this->persistentStores->addObject($store)) {
				$store->setPersistentStoreCoordinator($this);
			}
		}
		
		/**
		 * Removes a Persistent Store from this Persistent Store Coordinator's 
		 * collection of Stores
		 *
		 * @param MPersistentStore $store The Persistent Store instance to
		 * remove
		 *
		 * @return void
		 */
		public function removePersistentStore(MPersistentStore $store) {
			if ($this->persistentStores->removeObject($store)) {
				$store->setPersitentStoreCoordinator(null);
			}
		}
		
		/**
		 * Returns a collection of all the Persistent Stores managed by this
		 * Persistent Store Coordinator
		 *
		 * @return MArray An Array containing all the Persistent Stores in this
		 * Coordinator
		 */
		public function persistentStores() {
			return $this->persistentStores;
		}
		
		/**
		 * Sets the context this Persistent Store Coordinator belongs to
		 *
		 * @param MManagedObjectContext $context The context this Persistent
		 * Store Coordinator belongs to
		 *
		 * @return void
		 */
		public function setManagedObjectContext(MManagedObjectContext $context) {
			$this->managedObjectContext = $context;
		}
		
		/**
		 * Returns the Managed Object Context this Persistent Store Coordinator
		 * belongs to
		 *
		 * @return MManagedObjectContext The Managed Object Context this Persistent
		 * Store Coordinator belongs to
		 */
		public function managedObjectContext() {
			return $this->managedObjectContext;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Execute a request across all persistent stores and returns the results
		 * if any
		 *
		 * @param MPersistentStoreRequest $request The request to be peformed
		 *
		 * @return MArray|null An Array containing the results for the request
		 * or null if the request yields no results
		 */
		public function executeRequest(MPersistentStoreRequest $request) {
			if ($this->persistentStores()->count() > 0) {
				if ($request->requestType() == MPersistentStoreRequest::SaveRequestType) {
					$objects = new MArray();
					foreach ($this->persistentStores()->toArray() as $persistentStore) {
						$objects = $persistentStore->executeRequest($request);
					}
					return $objects;
				} else if ($request->requestType() == MPersistentStoreRequest::FetchRequestType) {
					return $this->persistentStores()->anyObject()->executeRequest($request);
				} else if ($request->requestType() == MPersistentStoreRequest::FaultRequestType) {
					return $this->persistentStores()->anyObject()->executeRequest($request);
				} else {
					throw new MPersistentStoreException(Sf("Unsupported request type (%s)", $request->requestType()));
				}
			} else {
				throw new MPersistentStoreException(S("No persistent stores defined!"));
			}
		}
		
	}

?>