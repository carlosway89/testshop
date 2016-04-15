<?php
/* --------------------------------------------------------------
   EmailAddress.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailAddressInterface');

/**
 * Class EmailAddress
 * 
 * Represents an email address (both email address and displayed name).
 * 
 * @category System
 * @package Email
 * @subpackage ValueObjects
 */
class EmailAddress implements EmailAddressInterface
{
   /**
    * Defines the max length of the database field. 
    */
   const MAX_LENGTH = 128;
   
   
   /**
    * @var string
    */
   protected $emailAddress;

   /**
    * Constructor
    *
    * Executes the validation checks for the email address. 
    * 
    * @param string $p_emailAddress
    */
   public function __construct($p_emailAddress)
   {
      if(!is_string($p_emailAddress) || empty($p_emailAddress))
      {
         throw new InvalidArgumentException('Invalid argument provided (expected string email address) $p_emailAddress: ' 
                                            . print_r($p_emailAddress, true));
      }
      
      if(strlen(trim($p_emailAddress)) > self::MAX_LENGTH)
      {
         throw new InvalidArgumentException('Argument exceeded the maximum database field length (' 
                                            . self::MAX_LENGTH . '):' . $p_emailAddress); 
      } 
      
      if(!filter_var($p_emailAddress, FILTER_VALIDATE_EMAIL))
      {
         throw new InvalidArgumentException('Invalid email address provided $p_emailAddress: ' . $p_emailAddress); 
      }
      
      $this->emailAddress = $p_emailAddress;
   }

   /**
    * Get the email address as a string.
    * 
    * @return string
    */
   public function __toString()
   {
      return $this->emailAddress; 
   }
}