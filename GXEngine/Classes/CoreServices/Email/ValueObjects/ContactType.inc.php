<?php
/* --------------------------------------------------------------
   ContactType.inc.php 2015-02-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('ContactTypeInterface');

/**
 * Class ContactType
 * 
 * @category System
 * @package Email
 * @subpackage ValueObjects
 */
class ContactType implements ContactTypeInterface
{
   /**
    * @var string
    */
   protected $type;
   
   /** 
    * Type Constants 
    */
   const SENDER = 'sender'; 
   const RECIPIENT = 'recipient';
   const REPLY_TO = 'reply_to';
   const BCC = 'bcc';
   const CC = 'cc';

   /**
    * Class Constructor
    * 
    * @param string $p_type Must be one of the given values.  
    */
   public function __construct($p_type)
   {
      if(!is_string($p_type) || empty($p_type) 
            || ($p_type != self::SENDER && $p_type != self::RECIPIENT && $p_type != self::BCC 
                && $p_type != self::CC && $p_type != self::REPLY_TO))
      {
         throw new InvalidArgumentException('Invalid contact type provided (string constant expected): ' . print_r($p_type, true));
      }
      $this->type = $p_type;
   }
   
   /**
    * Get Contact Type as String. 
    * 
    * @return string
    */
   public function __toString()
   {
      return $this->type;
   }
}