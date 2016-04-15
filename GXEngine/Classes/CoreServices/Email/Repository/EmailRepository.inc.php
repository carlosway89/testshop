<?php
/* --------------------------------------------------------------
   EmailRepository.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailRepositoryInterface');

/**
 * Class EmailRepository
 *
 * Handles the database operations that concern the email records of the
 * database. It provides a layer for more complicated methods that use the
 * writer, reader and deleter.
 *
 * @category   System
 * @package    Email
 * @subpackage Repository
 */
class EmailRepository implements EmailRepositoryInterface
{
	/**
	 * @var EmailWriterInterface
	 */
	protected $writer;

	/**
	 * @var EmailReaderInterface
	 */
	protected $reader;

	/**
	 * @var EmailDeleterInterface
	 */
	protected $deleter;


	/**
	 * Class Constructor
	 *
	 * @param EmailWriterInterface  $writer Used for database writing operations
	 * @param EmailReaderInterface  $reader
	 * @param EmailDeleterInterface $deleter
	 */
	public function __construct(EmailWriterInterface $writer,
	                            EmailReaderInterface $reader,
	                            EmailDeleterInterface $deleter)
	{
		$this->writer  = $writer;
		$this->reader  = $reader;
		$this->deleter = $deleter;
	}


	/**
	 * Write an email record into the database.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function write(EmailInterface $email)
	{
		$this->writer->write($email);
	}


	/**
	 * Get an email record by ID.
	 *
	 * @param IdInterface $id Database ID of the  record to be returned.
	 *
	 * @return EmailInterface Returns the email object that matches the ID.
	 * @throws UnexpectedValueException If record does not exist.
	 */
	public function getById(IdInterface $id)
	{
		$collection = $this->reader->get(array('email_id' => (int)(string)$id));

		if($collection->count() === 0)
		{
			throw new UnexpectedValueException('No email record matches the provided $id: ' . $id);
		}

		return $collection->getItem(0);
	}


	/**
	 * Find Email by ID
	 *
	 * This method will try to find the email record that matches provided ID and
	 * will return NULL on when the record does not exist.
	 *
	 * @param IdInterface $id Email record id to be found.
	 *
	 * @return EmailInterface|null Returns email object or null on failure.
	 */
	public function findById(IdInterface $id)
	{
		$collection = $this->reader->get(array('email_id' => (int)(string)$id));

		if($collection->count() > 0)
		{
			return $collection->getItem(0);
		}
		else
		{
			return null;
		}
	}


	/**
	 * Get a collection of pending emails.
	 *
	 * @return EmailCollection Returns the pending email objects.
	 */
	public function getPending()
	{
		return $this->reader->get(array('is_pending' => true));
	}


	/**
	 * Get a collection of sent emails.
	 *
	 * @return EmailCollection Returns the sent email objects.
	 */
	public function getSent()
	{
		return $this->reader->get(array('is_pending' => false));
	}


	/**
	 * Get all email records from the database.
	 *
	 * @return EmailCollection
	 */
	public function getAll()
	{
		return $this->reader->get();
	}


	/**
	 * Remove email record from the database.
	 *
	 * This method will remove ALL the email information, from the tables that
	 * contain information about the specified email.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function delete(EmailInterface $email)
	{
		$this->deleter->delete($email);
	}


	/**
	 * Filter email records with provided keyword string.
	 *
	 * @param string $p_keyword String to be used for filtering the email records.
	 * @param array  $limit     (optional) Array that contains LIMIT and OFFSET value
	 *                          e.g. array( 'limit' => 10, 'offset' => 5 )
	 * @param array  $order     (optional) Contains arrays with column, direction pairs
	 *                          e.g. array( 'column' => 'direction' )
	 *
	 * @return EmailCollection Returns a collection containing the email records.
	 */
	public function filter($p_keyword, array $limit = array(), array $order = array())
	{
		return $this->reader->filter($p_keyword, $limit, $order);
	}


	/**
	 * Get the current count of the email records in the database.
	 *
	 * @param string $p_filterKeyword (optional) If provided the records will be filtered.
	 * 
	 * @return int Returns the row number of the email table.
	 */
	public function getRecordCount($p_filterKeyword = '')
	{
		return $this->reader->getRecordCount($p_filterKeyword);
	}
}