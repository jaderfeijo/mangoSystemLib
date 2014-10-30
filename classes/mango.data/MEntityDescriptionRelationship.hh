<?hh

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
 * Class that describes an entity's relationship
 *
 * This class holds and manages the information necessary to describe
 * an entity's relationship
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MEntityDescriptionRelationship extends MEntityDescriptionAttribute {
	
	protected MString $_type;
	protected MRelationshipTo $_to;
	protected ?MEntityDescriptionRelationship $_inverseRelationship;
	protected ?MString $_singular;
	
	public function __construct(MEntityDescription $entity, MString $name, MString $type) {
		parent::__construct($entity, $name);
		
		$this->_type = $type;
		$this->_to = MRelationshipTo::One;
		$this->_inverseRelationship = null;
		$this->_singular = null;
	}
	
	/******************** Properties ********************/
	
	/**
	 * Returns the type of this relationship
	 *
	 * @return MString The type of this relationship
	 */
	public function type() : MString {
		return $this->_type;
	}
	
	/**
	 * Sets the target type of this relationship
	 *
	 * Use this method to set the target type of this relationship.
	 * This can be one of the following possible values:
	 * MRelationshipTo::One -> Relationship links to a single object of the target entity
	 * MRelationshipTo::Many -> Relationship links to a collection of the target entity
	 *
	 * @see MRelationshipTo::One
	 * @see MRelationshipTo::Many
	 * @see MEntityDescriptionRelationship::to()
	 *
	 * @param MRelationshipTo $to A constant defining the target type of this relationship
	 *
	 * @return void
	 */
	public function setTo(MRelationshipTo $to) : void {
		$this->_to = $to;
	}
	
	/**
	 * Returns the target type of this relationship
	 *
	 * This method returns the target type of this relationship.
	 * The possible values are:
	 * MRelationshipTo::One -> Relationship links to a single object of the target entity
	 * MRelationshipTo::Many -> Relationship links to a collection of the target entity
	 *
	 * @see MRelationshipTo::One
	 * @see MRelationshipTo::Many
	 * @see MEntityDescriptionRelationship::setTo()
	 *
	 * @return MRelationshipTo A constant defining the target type of this relationship
	 */
	public function to() : MRelationshipTo {
		return $this->_to;
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
	 * @param ?MEntityDescriptionRelationship $inverseRelationship The inverse relationship object for this
	 * relationship
	 *
	 * @return void
	 */
	public function setInverseRelationship(?MEntityDescriptionRelationship $inverseRelationship = null) : void {
		$this->_inverseRelationship = $inverseRelationship;
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
	 * @return ?MEntityDescriptionRelationship The inverse relationship object for this relationship
	 */
	public function inverseRelationship() : ?MEntityDescriptionRelationship {
		return $this->_inverseRelationship;
	}
	
	/**
	 * Sets the singular name of this relationship
	 *
	 * The singular name is only used in ToMany relationships
	 *
	 * @param ?MString $singular The singular name of this relationship
	 *
	 * @return void
	 */
	public function setSingular(?MString $singular = null) : void {
		$this->_singular = $singular;
	}
	
	/**
	 * Returns the singular name of this relationship
	 *
	 * @return ?MString The singular name of this relationship
	 */
	public function singular() : ?MString {
		return $this->_singular;
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
	 * MRelationshipKind::OneToOne
	 * MRelationshipKind::OneToMany
	 * MRelationshipKind::ManyToMany
	 *
	 * Calling this method on this end of the relationship or in the inverse
	 * end will yield the same results. If you wish to determine what the target
	 * type is for this end of the relationship, please use
	 * MEntityDescriptionRelationship::to()
	 *
	 * @see MEntityDescriptionRelationship::to()
	 * @see MRelationshipKind::OneToOne
	 * @see MRelationshipKind::OneToMany
	 * @see MRelationshipKind::ManyToMany
	 *
	 * @return MRelationshipKind A constant defining the type of this relationship
	 */
	public function relationshipType() : MRelationshipKind {
		$inverseRelationship = $this->inverseRelationship();
		if ($this->to() == MRelationshipTo::One) {
			if ($inverseRelationship !== null) {
				if ($inverseRelationship->to() == MRelationshipTo::One) {
					return MRelationshipKind::OneToOne;
				} else if ($inverseRelationship->to() == MRelationshipTo::Many) {
					return MRelationshipKind::OneToMany;
				}
			} else {
				return MRelationshipKind::OneToNone;
			}
		} else if ($this->to() == MRelationshipTo::Many) {
			if ($inverseRelationship !== null) {
				if ($inverseRelationship->to() == MRelationshipTo::One) {
					return MRelationshipKind::OneToMany;
				} else if ($inverseRelationship->to() == MRelationshipTo::Many) {
					return MRelationshipKind::ManyToMany;
				}
			} else {
				return MRelationshipKind::ManyToNone;
			}
		}
		return MRelationshipKind::Unknown;
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
	public function tableName() : MString {
		$names = null;
		$inverseRelationship = $this->inverseRelationship();
		if ($inverseRelationship !== null) {
			$names = A(array(
				$this->entity()->name(),
				$inverseRelationship->entity()->name(),
				$this->name(),
				$inverseRelationship->name()
			))->sortedArray(MArrayOrder::Ascending, SORT_STRING);
		} else {
			$name = A(array(
				$this->entity()->name(),
				$this->name()
			))->sortedArray(MArrayOrder::Ascending, SORT_STRING);
		}
		if ($names === null) {
			throw new MPersistentStoreException(Sf("Failed to create table name for relationship '%s'", $this->name()));
		}
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
	public function columnName() : MString {
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
	public function inverseColumnName() : MString {
		$inverseRelationship = $this->inverseRelationship();
		if ($inverseRelationship !== null) {
			return $inverseRelationship->columnName();
		} else {
			return S(md5($this->type()))->substringToIndex(16)->toLower();
		}
	}
	
	/******************** MEntityDescriptionAttribute ********************/
	
	public function typeClassName() : MString {
		return $this->type();
	}
	
}

