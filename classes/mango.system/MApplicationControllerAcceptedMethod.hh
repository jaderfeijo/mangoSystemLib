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
 * This class contains information about a HTTP accepted method
 *
 * It is used by MApplicationController to describe the accepted methods
 * for a view controller
 *
 * @author Jader Feijo <jader@movinpixel.com>
 */
class MApplicationControllerAcceptedMethod extends MObject {
	
	protected MString $_method;
	protected MMutableArray $_contentTypes;
	protected MMutableArray $_fields;
	
	/**
	 * Creates a new MApplicationControllerAcceptedMethod instance with the specified
	 * method.
	 *
	 * @param MString $method A string containing the HTTP method this accepted method
	 * represents
	 *
	 * @return MApplicationControllerAcceptedMethod The newly created acceted method
	 * instance
	 */
	public function __construct(MString $method) {
		parent::__construct();
		
		$this->_method = $method;
		$this->_contentTypes = new MMutableArray();
		$this->_fields = new MMutableArray();
	}
	
	/******************** Properties ********************/
	
	/**
	 * The HTTP method this accepted method represents
	 *
	 * @return MString
	 */
	public function method() : MString {
		return $this->_method;
	}
	
	/**
	 * Returns an array containing all accepted content types
	 *
	 * @return MArray An array containing all accepted content
	 * types
	 */
	public function contentTypes() : MArray {
		return (MArray)$this->_contentTypes;
	}
	
	/**
	 * Returns an array containing instances of MApplicationControllerField
	 * which describes the accepted fields for this controller
	 *
	 * @see MApplicationControllerField 
	 *
	 * @return MArray Returns an array containing all the fields accepted
	 * by this method
	 */
	public function fields() : MArray {
		return (MArray)$this->_fields;
	}
	
	/******************** Methods ********************/
	
	/**
	 * Adds the specified content type to the list of content types
	 * accepted by this method
	 *
	 * @param MString A string containing a content type to add
	 *
	 * @return void
	 */
	public function addContentType(MString $contentType) : void {
		$this->_contentTypes->addObject($contentType);
	}
	
	/**
	 * Removes the specified content type from the list of content
	 * types acceted by this method
	 *
	 * @param MString A string containing the content type to remove
	 *
	 * @return void
	 */
	public function removeContentType(MString $contentType) : void {
		$this->_contentTypes->removeObject($contentType);
	}
	
	/**
	 * Removes all content types accepted by this method
	 *
	 * @return void
	 */
	public function removeAllContentTypes() : void {
		$this->_contentTypes->removeAllObjects();
	}
	
	/**
	 * Adds the specified field to this method
	 *
	 * @param MApplicationControllerField $field The field instance
	 * to add
	 *
	 * @return void
	 */
	public function addField(MApplicationControllerField $field) : void {
		$this->_fields->addObject($field);
	}
	
	/**
	 * Removes the specified field from this method
	 *
	 * @param MApplicationControllerField $field The field instance
	 * to remove
	 *
	 * @return void
	 */
	public function removeField(MApplicationControllerField $field) : void {
		$this->_fields->removeObject($this);
	}
	
	/**
	 * Removes all fields from this method
	 *
	 * @see MApplicationControllerField
	 *
	 * @return void
	 */
	public function removeAllFields() : void {
		$this->_fields->removeAllObjects();
	}
	
	/**
	 * Returns a field accepted by this method which matches the specified
	 * name.
	 *
	 * This method iterates through all of the accepted MApplicationControllerField
	 * instances accepted by this class instance, looks for one that matches the
	 * specified name and returns it. If no field matching the specified name is found,
	 * this method returns null
	 *
	 * @param MString $name The field name to retrieve
	 * 
	 * @return MApplicationControllerField A field instance matching the specified name
	 * or null of none is found
	 */
	public function fieldWithName(MString $name) : ?MApplicationControllerField {
		foreach ($this->fields()->toArray() as $field) {
			if ($field->name()->equals($name)) {
				return $field;
			}
		}
		return null;
	}
	
}

