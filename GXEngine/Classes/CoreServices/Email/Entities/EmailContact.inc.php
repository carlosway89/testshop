<?php
/* --------------------------------------------------------------
   EmailContact.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailContactInterface');

/**
 * Class EmailContact
 *
 * Represents a contact (sender/recipient) that participates in a Email entity.
 *
 * @category System
 * @package Email
 * @subpackage Entities
 */
class EmailContact implements EmailContactInterface
{
	/**
	 * @var EmailAddressInterface
	 */
	protected $emailAddress;

	/**
	 * @var ContactTypeInterface
	 */
	protected $contactType;
	
	/**
	 * @var ContactNameInterface
	 */
	protected $contactName;


	/**
	 * Constructor
	 *
	 * The user will have to
	 *
	 * @param EmailAddressInterface $emailAddress (optional)
	 * @param ContactNameInterface  $contactName (optional)
	 * @param ContactTypeInterface $contactType (optional)
	 */
	public function __construct(EmailAddressInterface $emailAddress = null,
	                            ContactTypeInterface $contactType = null,
	                            ContactNameInterface $contactName = null)
	{
		$this->emailAddress = $emailAddress;
		$this->contactType = $contactType;
		$this->contactName = $contactName;
	}

	/**
	 * Return contact information as string.
	 *
	 * Example Output: 'John Doe <email@address.com>'
	 *
	 * @return string
	 */
	public function __toString()
	{
		$result = array();

		if($this->contactName !== null)
		{
			$result[] = (string)$this->contactName;
		}

		if($this->emailAddress !== null)
		{
			$result[] = '<' . (string)$this->emailAddress . '>';
		}

		return implode(' ', $result);
	}

	/**
	 * EmailAddress Getter
	 *
	 * @return string
	 */
	public function getEmailAddress()
	{
		return (string)$this->emailAddress;
	}

	/**
	 * EmailAddress Setter
	 *
	 * @param EmailAddressInterface $emailAddress
	 */
	public function setEmailAddress(EmailAddressInterface $emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}

	/**
	 * ContactName Getter
	 *
	 * @return string
	 */
	public function getContactName()
	{
		return (string)$this->contactName;
	}

	/**
	 * ContactName Setter
	 *
	 * @param ContactNameInterface $contactName
	 */
	public function setContactName(ContactNameInterface $contactName)
	{
		$this->contactName = $contactName;
	}

	/**
	 * ContactType Getter
	 *
	 * @return ContactTypeInterface
	 */
	public function getContactType()
	{
		return (string)$this->contactType;
	}

	/**
	 * ContactType Setter
	 *
	 * @param ContactTypeInterface $contactType
	 */
	public function setContactType(ContactTypeInterface $contactType)
	{
		$this->contactType = $contactType;
	}
}