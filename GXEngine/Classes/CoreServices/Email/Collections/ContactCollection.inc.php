<?php
/* --------------------------------------------------------------
   ContactCollection.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');
MainFactory::load_class('ContactCollectionInterface');

/**
 * Class ContactCollection
 * 
 * Handles collection of EmailContact objects.
 * 
 * @category System
 * @package Email
 * @subpackage Collections
 */
class ContactCollection extends AbstractCollection implements ContactCollectionInterface
{
   /**
    * Add a new contact into the collection.
    * 
    * @param EmailContactInterface $contact
    */
   public function add(EmailContactInterface $contact)
   {
      $this->_add($contact);   
   }

   /**
    * Remove a contact from collection. 
    * 
    * @param EmailContactInterface $contact
    *
    * @throws Exception If contact cannot be found.
    */
   public function remove(EmailContactInterface $contact)
   {
      $index = array_search($contact, $this->collectionContentArray);

      if($index === false)
      {
         throw new Exception('Could not remove contact because it does not exist in collection.');
      }

      unset($this->collectionContentArray[$index]);
   }

   /**
    * Remove all contacts of collection. 
    */
   public function clear()
   {
      $this->collectionContentArray = array();
   }

   /**
    * Get the type of te collection items.
    * 
    * @return string    
    */
   protected function _getValidType()
   {
      return 'EmailContactInterface';
   }
}