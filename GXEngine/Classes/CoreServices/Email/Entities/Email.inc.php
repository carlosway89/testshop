<?php
/* --------------------------------------------------------------
   Email.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailInterface');

/**
 * Class Email
 *
 * This class represents the database entity of an email.
 *
 * @category System
 * @package Email
 * @subpackage Entities
 */
class Email implements EmailInterface
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var EmailContactInterface
	 */
	protected $sender;

	/**
	 * @var EmailContactInterface
	 */
	protected $recipient;

	/**
	 * @var EmailContactInterface
	 */
	protected $replyTo;

	/**
	 * @var EmailContactInterface
	 */
	protected $subject;

	/**
	 * @var EmailContentInterface
	 */
	protected $contentPlain;

	/**
	 * @var EmaiContentInterface
	 */
	protected $contentHtml;

	/**
	 * @var AttachmentCollectionInterface
	 */
	protected $attachments;

	/**
	 * @var ContactCollectionInterface
	 */
	protected $bcc;

	/**
	 * @var ContactCollectionInterface
	 */
	protected $cc;

	/**
	 * @var bool
	 */
	protected $isPending;

	/**
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * @var DateTime
	 */
	protected $sentDate; 

	/**
	 * Class Constructor
	 *
	 * All parameters are optional and can be set after the creation of the Email
	 * object. All class properties will have "null" as default value.
	 *
	 * @param EmailContactInterface $sender (optional)
	 * @param EmailContactInterface $recipient (optional)
	 * @param EmailSubjectInterface $subject (optional)
	 * @param EmailContentInterface $contentHtml (optional)
	 * @param EmailContentInterface $contentPlain (optional)
	 */
	public function __construct(EmailContactInterface $sender = null,
	                            EmailContactInterface $recipient = null,
	                            EmailSubjectInterface $subject = null,
	                            EmailContentInterface $contentHtml = null,
	                            EmailContentInterface $contentPlain = null)
	{
		// Required Email Properties 
		$this->sender = $sender;
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->contentHtml = $contentHtml;
		$this->contentPlain = $contentPlain;
		
		// Optional Email Properties
		$this->id = null;
		$this->bcc = MainFactory::create('ContactCollection');
		$this->cc = MainFactory::create('ContactCollection');
		$this->attachments = MainFactory::create('AttachmentCollection');
		$this->isPending = true;
		$this->creationDate = new DateTime(); // Current datetime as default value. 
		$this->sentDate = null; 
	}

	/**
	 * Id Setter
	 *
	 * @param IdInterface $id
	 */
	public function setId(IdInterface $id)
	{
		$this->id = (int)(string)$id;
	}

	/**
	 * Id Getter
	 *
	 * @return IdInterface
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * From Setter
	 *
	 * @param EmailContactInterface $sender
	 */
	public function setSender(EmailContactInterface $sender)
	{
		$this->sender = $sender;
	}

	/**
	 * From Getter
	 *
	 * @return EmailContactInterface
	 */
	public function getSender()
	{
		return $this->sender;
	}

	/**
	 * Recipient Setter
	 *
	 * @param EmailContactInterface $recipient
	 */
	public function setRecipient(EmailContactInterface $recipient)
	{
		$this->recipient = $recipient;
	}

	/**
	 * Recipient Getter
	 *
	 * @return EmailContactInterface
	 */
	public function getRecipient()
	{
		return $this->recipient;
	}

	/**
	 * Reply To Setter
	 *
	 * @param EmailContactInterface $recipient
	 */
	public function setReplyTo(EmailContactInterface $recipient)
	{
		$this->replyTo = $recipient;
	}

	/**
	 * Reply To Getter
	 *
	 * @return EmailContactInterface
	 */
	public function getReplyTo()
	{
		return $this->replyTo;
	}

	/**
	 * Subject Setter
	 *
	 * @param EmailSubjectInterface $subject
	 */
	public function setSubject(EmailSubjectInterface $subject)
	{
		$this->subject = $subject;
	}

	/**
	 * Subject Getter
	 *
	 * @return EmailSubjectInterface
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Content Plain Setter
	 *
	 * @param EmailContentInterface $contentPlain
	 */
	public function setContentPlain(EmailContentInterface $contentPlain)
	{
		$this->contentPlain = $contentPlain;
	}

	/**
	 * Content Plain Getter
	 *
	 * @return EmailContentInterface
	 */
	public function getContentPlain()
	{
		return $this->contentPlain;
	}

	/**
	 * Content HTML Setter
	 *
	 * @param EmailContentInterface $contentHtml
	 */
	public function setContentHtml(EmailContentInterface $contentHtml)
	{
		$this->contentHtml = $contentHtml;
	}

	/**
	 * Content HTML Getter
	 *
	 * @return EmailContentInterface
	 */
	public function getContentHtml()
	{
		return $this->contentHtml;
	}

	/**
	 * Attachments Setter
	 *
	 * @param AttachmentCollectionInterface $attachments
	 */
	public function setAttachments(AttachmentCollectionInterface $attachments)
	{
		$this->attachments = $attachments;
	}

	/**
	 * Attachments Getter
	 *
	 * @return AttachmentCollectionInterface
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}

	/**
	 * BCC Setter
	 *
	 * @param ContactCollectionInterface $bcc
	 */
	public function setBcc(ContactCollectionInterface $bcc)
	{
		$this->bcc = $bcc;
	}

	/**
	 * BCC Getter
	 *
	 * @return ContactCollectionInterface
	 */
	public function getBcc()
	{
		return $this->bcc;
	}

	/**
	 * CC Setter
	 *
	 * @param ContactCollectionInterface $cc
	 */
	public function setCc(ContactCollectionInterface $cc)
	{
		$this->cc = $cc;
	}

	/**
	 * CC Getter
	 *
	 * @return ContactCollectionInterface
	 */
	public function getCc()
	{
		return $this->cc;
	}

	/**
	 * IsPending Setter
	 *
	 * @param bool $p_isPending
	 */
	public function setPending($p_isPending)
	{
		if(!is_bool($p_isPending))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected bool) $p_isPending: '
			                                   . print_r($p_isPending, true));
		}

		$this->isPending = (bool)$p_isPending;
	}

	/**
	 * IsPending Getter
	 *
	 * @return bool
	 */
	public function isPending()
	{
		return $this->isPending;
	}


	/**
	 * Creation Date Setter
	 * 
	 * @param DateTime $creationDate
	 */
	public function setCreationDate(DateTime $creationDate)
	{
		$this->creationDate = $creationDate; 
	}


	/**
	 * Creation Date Getter
	 * 
	 * @return DateTime
	 */
	public function getCreationDate()
	{
		return $this->creationDate; 
	}


	/**
	 * Sent Date Setter
	 * 
	 * @param DateTime $sentDate
	 */
	public function setSentDate(DateTime $sentDate)
	{
		$this->sentDate = $sentDate;
	}


	/**
	 * Sent Date Getter
	 * 
	 * @return DateTime
	 */
	public function getSentDate()
	{
		return $this->sentDate;
	}
}