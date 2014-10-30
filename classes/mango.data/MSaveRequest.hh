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
 * Class that represents a Save Request to be performed in a Persistent Store
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MSaveRequest extends MPersistentStoreRequest {
	
	protected ?MArray<MManagedObject> $_insertManagedObjects;
	protected ?MArray<MManagedObject> $_updateManagedObjects;
	protected ?MArray<MManagedObject> $_deleteManagedObjects;
	
	/**
	 * Creates a new Save Request with the specified context
	 *
	 * @param MManagedObjectContext $context The context in which this
	 * Save Request will be performed
	 *
	 * @return MSaveRequest The newly created Save Request
	 */
	public function __construct(MManagedObjectContext $context) {
		parent::__construct($context);
		
		$this->_insertManagedObjects = null;
		$this->_updateManagedObjects = null;
		$this->_deleteManagedObjects = null;
	}
	
	/******************** Methods ********************/
	
	/**
	 * Sets the Managed Objects that are to be inserted
	 *
	 * @param MArray<MManagedObject> $insertManagedObjects An Array containing all
	 * Managed Objects that should be inserted into the Data Store
	 *
	 * @return void
	 */
	public function setInsertManagedObjects(?MArray<MManagedObject> $insertManagedObjects) : void {
		$this->_insertManagedObjects = $insertManagedObjects;
	}
	
	/**
	 * Returns the Managed Objects that are to be inserted
	 *
	 * @return MArray<MManagedObject> An Array containing all Managed Objects
	 * that are to be inserted into the Data Store
	 */
	public function insertManagedObjects() : ?MArray<MManagedObject> {
		return $this->_insertManagedObjects;
	}
	
	/**
	 * Sets the Managed Objects that are to updated
	 *
	 * @param MArray<MManagedObject> $updateManagedObjects An Array containing all
	 * Managed Objects that should be updated in the Data Store
	 *
	 * @return void
	 */
	public function setUpdateManagedObjects(?MArray<MManagedObject> $updateManagedObjects) : void {
		$this->_updateManagedObjects = $updateManagedObjects;
	}
	
	/**
	 * Returns the Managed Objects that are to be updated
	 *
	 * @return MArray<MManagedObject> An Array containing all Managed Objects
	 * that are to be updated in the Data Store
	 */
	public function updateManagedObjects() : ?MArray<MManagedObject> {
		return $this->_updateManagedObjects;
	}
	
	/**
	 * Sets the Managed Objects that are to be deleted
	 *
	 * @param MArray<MManagedObject> $deleteManagedObjects An Array containing all
	 * Managed Objects that should be deleted from the Data Store
	 *
	 * @return void
	 */
	public function setDeleteManagedObjects(?MArray<MManagedObject> $deleteManagedObjects) : void {
		$this->_deleteManagedObjects = $deleteManagedObjects;
	}
	
	/**
	 * Returns the Managed Objects that are to be deleted
	 *
	 * @return MArray<MManagedObject> An Array containing all Managed Objects
	 * that are to be deleted from the Data Store
	 */
	public function deleteManagedObjects() : ?MArray<MManagedObject> {
		return $this->_deleteManagedObjects;
	}
	
}

