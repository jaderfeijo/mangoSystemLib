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
 * @todo Add more information about Predicates
 */

/**
 * This class holds the information necessary to perform a Fetch Request
 *
 * A Fetch Request is a request that is sent to the data store in order
 * to fetch objects with properties matching certain criteria. The criteria
 * is defined by the predicate.
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MFetchRequest extends MPersistentStoreRequest {
		
	protected MEntityDescription $_entity;
	protected ?MString $_predicate;
	
	/**
	 * Creates a new instance of the MFetchRequest class which defines
	 * a request to be sent to the data store
	 *
	 * @param MEntityDescription $entity The entity you wish to fetch
	 * @param MManagedObjectContext $context The context in which this 
	 * request will be performed
	 *
	 * @return MFetchRequest The newly created Fetch Request instance
	 */
	public function __construct(MEntityDescription $entity, MManagedObjectContext $context) {
		parent::__construct($context);
		$this->_entity = $entity;
		$this->_predicate = null;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Sets the entity to be fetched by this Fetch Request
	 *
	 * @see MFetchRequest::entity()
	 *
	 * @param MEntityDescription $entity The entity to be fetched
	 *
	 * @return void
	 */
	public function setEntity(MEntityDescription $entity) : void {
		$this->_entity = $entity;
	}
	
	/**
	 * Returns the entity to be fetched by this fetch request
	 *
	 * @see MFetchRequest::setEntity()
	 *
	 * @return MEntityDescription The entity to be fetched
	 */
	public function entity() : MEntityDescription {
		return $this->_entity;
	}
	
	/**
	 * Sets the predicate to use for this fetch request
	 *
	 * The predicate limits the returned objects to those with properties
	 * matching the criteria of the predicate.
	 *
	 * @see MEntityDescription::predicate()
	 *
	 * @param MString|null $predicate The predicate to be used with this
	 * request
	 *
	 * @return void
	 */
	public function setPredicate(?MString $predicate = null) : void {
		$this->_predicate = $predicate;
	}
	
	/**
	 * Returns the predicate being used with this fetch request
	 *
	 * @see MEntityDescription::setPredicate()
	 *
	 * @return MString The predicate being used with this fetch
	 * request
	 */
	public function predicate() : ?MString {
		return $this->_predicate;
	}
	
}

