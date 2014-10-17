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
	
	import('mango.system.*');
	import('mango.system.exceptions.*');
	
	/**
	 * Class that describes an entity's relationship
	 *
	 * This class holds and manages the information necessary to describe
	 * an entity's relationship
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.data
	 *
	 */
	class MEntityDescriptionRelationship extends MEntityDescriptionAttribute {
		
		const ToOne = 0;
		const ToMany = 1;
		
		const UnknownType = 0;
		const OneToOneType = 1;
		const OneToManyType = 2;
		const ManyToManyType = 3;
		
		//
		// ************************************************************
		//
		
		protected $type;
		protected $to;
		protected $inverseRelationship;
		protected $singular;
		
		/**
		 * 
		 */
		public function __construct(MEntityDescription $entity, MString $name, MString $type) {
			parent::__construct($entity, $name);
			
			$this->type = $type;
			$this->to = MEntityDescriptionRelationship::ToOne;
			$this->inverseRelationship = null;
			$this->singular = null;
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the type of this relationship
		 *
		 * @return MString The type of this relationship
		 */
		public function type() {
			return $this->type;
		}
		
		/**
		 * Sets the target type of this relationship
		 *
		 * Use this method to set the target type of this relationship.
		 * This can be one of the following possible values:
		 * MEntityDescriptionRelationship::ToOne -> Relationship links to a single object of the target entity
		 * MEntityDescriptionRelationship::ToMany -> Relationship links to a collection of the target entity
		 *
		 * @see MEntityDescriptionRelationship::ToOne
		 * @see MEntityDescriptionRelationship::ToMany
		 * @see MEntityDescriptionRelationship::to()
		 *
		 * @param int $to A constant defining the target type of this relationship
		 *
		 * @return void
		 */
		public function setTo($to) {
			MAssertTypes('int', $to);
			
			$this->to = $to;
		}
		
		/**
		 * Returns the target type of this relationship
		 *
		 * This method returns the target type of this relationship.
		 * The possible values are:
		 * MEntityDescriptionRelationship::ToOne -> Relationship links to a single object of the target entity
		 * MEntityDescriptionRelationship::ToMany -> Relationship links to a collection of the target entity
		 *
		 * @see MEntityDescriptionRelationship::ToOne
		 * @see MEntityDescriptionRelationship::ToMany
		 * @see MEntityDescriptionRelationship::setTo()
		 *
		 * @return int A constant defining the target type of this relationship
		 */
		public function to() {
			return $this->to;
		}
		
		/**
		 * Sets the inverse relationship for this relationship
		 *
		 * Use this method to set the inverse relationship for this relationship.
		 * The inverse relationship is the other end of this relationship, in
		 * other words, the relationship that links to this one on the target side
		 * of this relationship, namely the target entity.
		 *
		 * @see MEntityDescriptionRelationship::inverseRelationship()
		 * @see MEntityDescription
		 *
		 * @param MEntityDescriptionRelationship $inverseRelationship The inverse relationship object for this
		 * relationship
		 *
		 * @return void
		 */
		public function setInverseRelationship(MEntityDescriptionRelationship $inverseRelationship = null) {
			$this->inverseRelationship = $inverseRelationship;
		}
		
		/**
		 * Returns the inverse relationship for this relationship
		 *
		 * This method returns the inverse relationship object that describes the
		 * other end (inverse) of this relationship on the target entity.
		 *
		 * @see MEntityDescriptionRelationship::setInverseRelationship()
		 * @see MEntityDescription
		 *
		 * @return MEntityDescriptionRelationship The inverse relationship object for this relationship
		 */
		public function inverseRelationship() {
			return $this->inverseRelationship;
		}
		
		/**
		 * Sets the singular name of this relationship
		 *
		 * The singular name is only used in ToMany relationships
		 *
		 * @param MString $singular The singular name of this relationship
		 *
		 * @return void
		 */
		public function setSingular(MString $singular = null) {
			$this->singular = $singular;
		}
		
		/**
		 * Returns the singular name of this relationship
		 *
		 * @return MString The singular name of this relationship
		 */
		public function singular() {
			return $this->singular;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Returns a constant that defines the type of this relationship
		 * 
		 * This method returns a constant that defines the type of this relationship
		 * based on this relationships 'to' type and the inverse relationship's 'to'
		 * type.
		 *
		 * Possible values are:
		 * MEntityDescriptionRelationship::OneToOneType
		 * MEntityDescriptionRelationship::OneToManyType
		 * MEntityDescriptionRelationship::ManyToManyType
		 *
		 * Calling this method on this end of the relationship or in the inverse
		 * end will yield the same results. If you wish to determine what the target
		 * type is for this end of the relationship, please use
		 * MEntityDescriptionRelationship::to()
		 *
		 * @see MEntityDescriptionRelationship::to()
		 * @see MEntityDescriptionRelationship::OneToOneType
		 * @see MEntityDescriptionRelationship::OneToManyType
		 * @see MEntityDescriptionRelationship::ManyToManyType
		 *
		 * @return int A constant defining the type of this relationship
		 */
		public function relationshipType() {
			if ($this->inverseRelationship() != null) {
				if ($this->to() == MEntityDescriptionRelationship::ToOne) {
					if ($this->inverseRelationship()->to() == MEntityDescriptionRelationship::ToOne) {
						return MEntityDescriptionRelationship::OneToOneType;
					} else if ($this->inverseRelationship()->to() == MEntityDescriptionRelationship::ToMany) {
						return MEntityDescriptionRelationship::OneToManyType;
					}
				} else if ($this->to() == MEntityDescriptionRelationship::ToMany) {
					if ($this->inverseRelationship()->to() == MEntityDescriptionRelationship::ToOne) {
						return MEntityDescriptionRelationship::OneToManyType;
					} else if ($this->inverseRelationship()->to() == MEntityDescriptionRelationship::ToMany) {
						return MEntityDescriptionRelationship::ManyToManyType;
					}
				}
			}
			return MEntityDescriptionRelationship::UnknownType;
		}
		
		/******************** Internal ********************/
		
		/**
		 * @internal
		 *
		 * Returns the table name for this relationship
		 *
		 * This method returns the low level table name to be used with the database.
		 * This method should be used only internally to determine the details of the
		 * data storage. The table name is the result of the entity name combined with
		 * the inverse relationship's entity name, combined with both, this relationship's
		 * name and the inverse relationship's name. The resulting string is then hashed
		 * and an MD5 hash is used. This ensures that no two relationships have the same
		 * table name.
		 *
		 * @return MString A String containg the table name for this relationship
		 */
		public function tableName() {
			$names = A(
				$this->entity()->name(),
				$this->inverseRelationship()->entity()->name(),
				$this->name(),
				$this->inverseRelationship()->name()
			)->sortedArray(MArray::ORDER_ASCENDING, SORT_STRING);
			
			return Sf("Z_%s", S(md5($names->componentsJoinedByString(S(""))))->substringToIndex(16)->toUpper());
		}
		
		/**
		 * @internal
		 *
		 * Returns the column name for this end of the relationship
		 *
		 * This method returns the low level column name to be used with the database for
		 * this end of the relationship.
		 * This method should be used only internally to determine the details of the
		 * data storage. The column name is the result of this side's entity name combined
		 * with the relationship's name to create an MD5 hash. This ensures that no two
		 * relationships have the same column name.
		 *
		 * @return MString A String containg the column name for this relationship
		 */
		public function columnName() {
			$names = A(
				$this->entity()->name(),
				$this->name()
			);
			
			return S(md5($names->componentsJoinedByString(S(""))))->substringToIndex(16)->toLower();
		}
		
		/**
		 * @internal
		 *
		 * Returns the inverse relationship's column name
		 *
		 * This method returns the column name for the inverse side of this relationship.
		 * If this relationship does not have a corresponding inverse relationship, the
		 * column name is generated based on this relationship's type.
		 *
		 * @return MString A String containing the column name for the inverse side of this relationship
		 */
		public function inverseColumnName() {
			if ($this->inverseRelationship()) {
				return $this->inverseRelationship()->columnName();
			} else {
				return S(md5($this->type()))->substringToIndex(16)->toLower();
			}
		}
		
		/******************** MEntityDescriptionAttribute ********************/
		
		/**
		 * 
		 */
		public function typeClassName() {
			return $this->type();
		}
		
	}

?>