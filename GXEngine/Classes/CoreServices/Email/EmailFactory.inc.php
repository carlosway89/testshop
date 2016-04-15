<?php
/* --------------------------------------------------------------
   EmailFactory.inc.php 2015-07-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailFactoryInterface');

/**
 * Class EmailFactory
 *
 * @category System
 * @package  Email
 */
class EmailFactory implements EmailFactoryInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * Class Constructor
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Create Email Object
	 *
	 * @param IdInterface                   $id           (optional)
	 * @param EmailSubjectInterface         $subject      (optional)
	 * @param EmailContentInterface         $contentPlain (optional)
	 * @param EmailContentInterface         $contentHtml  (optional)
	 * @param bool                          $p_isPending  (optional)
	 * @param ContactCollectionInterface    $contacts     (optional)
	 * @param AttachmentCollectionInterface $attachments  (optional)
	 *
	 * @return Email Returns an email object.
	 */
	public function createEmail(IdInterface $id = null,
	                            EmailSubjectInterface $subject = null,
	                            EmailContentInterface $contentHtml = null,
	                            EmailContentInterface $contentPlain = null,
	                            $p_isPending = true,
	                            ContactCollectionInterface $contacts = null,
	                            AttachmentCollectionInterface $attachments = null)

	{
		if(!is_bool($p_isPending))
		{
			throw new InvalidArgumentException('Invalid $p_isPending argument given (bool expected): '
			                                   . print_r($p_isPending, true));
		}

		$email = MainFactory::create('Email');

		// Set email information.
		if($id !== null)
		{
			$email->setId($id);
		}

		if($subject !== null)
		{
			$email->setSubject($subject);
		}

		if($contentPlain !== null)
		{
			$email->setContentPlain($contentPlain);
		}

		if($contentHtml !== null)
		{
			$email->setContentHtml($contentHtml);
		}

		$email->setPending($p_isPending);

		// Set email contacts.
		if($contacts !== null)
		{
			foreach($contacts->getArray() as $contact)
			{
				switch($contact->getContactType())
				{
					case ContactType::SENDER:
						$email->setSender($contact);
						break;
					case ContactType::RECIPIENT:
						$email->setRecipient($contact);
						break;
					case ContactType::REPLY_TO:
						$email->setReplyTo($contact);
						break;
					case ContactType::BCC:
						$email->getBcc()->add($contact);
						break;
					case ContactType::CC:
						$email->getCc()->add($contact);
						break;
					default:
						throw new UnexpectedValueException('Unexpected contact type: ' . $contact->getContactType());
				}
			}
		}

		// Set email attachments collection.
		if($attachments !== null)
		{
			$email->setAttachments($attachments);
		}

		return $email;
	}


	/**
	 * Create EmailContact Object
	 *
	 * @param EmailAddressInterface $emailAddress Email address of the contact.
	 * @param ContactTypeInterface  $contactType  Contact type (see ContactType class definition).
	 * @param ContactNameInterface  $contactName  (optional) Contact display name.
	 *
	 * @return EmailContact Returns an email contact object.
	 */
	public function createContact(EmailAddressInterface $emailAddress,
	                              ContactTypeInterface $contactType,
	                              ContactNameInterface $contactName = null)
	{
		return MainFactory::create('EmailContact', $emailAddress, $contactType, $contactName);
	}


	/**
	 * Create EmailAttachment Object
	 *
	 * @param AttachmentPathInterface $path Valid path of the attachment (on the server).
	 * @param AttachmentNameInterface $name (optional) Display name for the attachment.
	 *
	 * @return EmailAttachment Returns an email attachment object.
	 */
	public function createAttachment(AttachmentPathInterface $path, AttachmentNameInterface $name = null)
	{
		return MainFactory::create('EmailAttachment', $path, $name);
	}


	/**
	 * Create MailerAdapter Object
	 *
	 * @return MailerAdapter Returns a mailer adapter object.
	 */
	public function createMailerAdapter()
	{
		$mailer = $this->createMailer();

		return MainFactory::create('MailerAdapter', $mailer);
	}


	/**
	 * Create PHPMailer Object.
	 *
	 * @return PHPMailer Returns a mailer object.
	 */
	public function createMailer()
	{
		$mailer            = new PHPMailer(true);
		$mailer->SMTPDebug = 0; // Disable debug output.

		// Set PHPMailer CharSet
		if(isset($_SESSION['language_charset']))
		{
			$mailer->CharSet = $_SESSION['language_charset'];
		}
		else
		{
			// @todo Replace the following section with the LanguageService when it is ready.
			$query           = 'SELECT language_charset FROM ' . TABLE_LANGUAGES . ' WHERE code = "' . DEFAULT_LANGUAGE
			                   . '"';
			$result          = xtc_db_query($query);
			$language        = xtc_db_fetch_array($result);
			$mailer->CharSet = $language['language_charset'];
		}

		// Set PHPMailer Language
		if($_SESSION['language'] === 'german')
		{
			$mailer->setLanguage('de', DIR_WS_CLASSES);
		}
		else
		{
			$mailer->setLanguage('en', DIR_WS_CLASSES);
		}

		// Set PHPMailer Protocol
		switch(EMAIL_TRANSPORT)
		{
			case 'smtp':
				$mailer->IsSMTP();
				$mailer->SMTPKeepAlive = true; // set mailer to use SMTP
				$mailer->SMTPAuth      = SMTP_AUTH; // turn on SMTP authentication true/false
				$mailer->Username      = SMTP_USERNAME; // SMTP username
				$mailer->Password      = SMTP_PASSWORD; // SMTP password
				$mailer->Host          = SMTP_MAIN_SERVER . ';'
				                         . SMTP_Backup_Server; // specify main and backup server "smtp1.example.com;smtp2.example.com"
				if(SMTP_ENCRYPTION == 'ssl' || SMTP_ENCRYPTION == 'tls')
				{
					$mailer->SMTPSecure = SMTP_ENCRYPTION;
				}
				break;

			case 'sendmail':
				$mailer->IsSendmail();
				$mailer->Sendmail = SENDMAIL_PATH;
				break;

			case 'mail':
				$mailer->IsMail();
				break;
		}

		return $mailer;
	}


	/**
	 * Create EmailService Object
	 *
	 * @return EmailService
	 */
	public function createService()
	{
		return MainFactory::create('EmailService', $this->createRepository(), $this, $this->createMailerAdapter(),
		                           $this->createAttachmentsHandler());
	}


	/**
	 * Create EmailRepository Object
	 *
	 * @return EmailRepository
	 */
	public function createRepository()
	{
		return MainFactory::create('EmailRepository', $this->createWriter(), $this->createReader(),
		                           $this->createDeleter());
	}


	/**
	 * Create EmailWriter Object
	 *
	 * @return EmailWriter
	 */
	public function createWriter()
	{
		return MainFactory::create('EmailWriter', $this->_getDbConnection());
	}


	/**
	 * Create EmailReader Object
	 *
	 * @return EmailReader
	 */
	public function createReader()
	{
		return MainFactory::create('EmailReader', $this->_getDbConnection(), $this);
	}


	/**
	 * Create EmailDeleter Object
	 *
	 * @return EmailDeleter
	 */
	public function createDeleter()
	{
		return MainFactory::create('EmailDeleter', $this->_getDbConnection());
	}


	/**
	 * Create AttachmentsHandler Object
	 *
	 * @param string $p_uploadsDirPath (optional) You can specify a custom uploads directory path if you do not want
	 *                                 the default "uploads" directory. The path must contain a "tmp" and an
	 *                                 "attachments" directory otherwise the AttachmentsHandler class will not work
	 *                                 properly.
	 *
	 * @return AttachmentsHandler
	 */
	public function createAttachmentsHandler($p_uploadsDirPath = null)
	{
		$uploadsDirPath = (!empty($p_uploadsDirPath)) ? $p_uploadsDirPath : DIR_FS_CATALOG . 'uploads';

		return MainFactory::create('AttachmentsHandler', $uploadsDirPath);
	}


	/**
	 * Get Database Object
	 *
	 * @return CI_DB_query_builder
	 */
	protected function _getDbConnection()
	{
		return $this->db;
	}
}
