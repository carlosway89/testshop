<?php
/* -----------------------------------------------------------------------------------------
   $Id: econda.php 899 2006-07-29 02:40:57Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2006 xt:Commerce
   -----------------------------------------------------------------------------------------

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
   
   class econda {
      var $logger;
      var $do_debug_logging = false;

      function econda() {
         $this->logger = new FileLog('econda-debug', $this->do_debug_logging );
         $this->log('econda: '.'instantiated');
      }

      function log($text) {
         $out = date('Y-m-d H:i:s').' | '.$text ."\n";
         $this->logger->write($out);
      }   
   	
   	function _loginUser() {
         $this->log('econda: '.'_loginUser');
   		$_SESSION['login_success'] = 1;
   	}

      function _loginFailUserUnknown()
      {
         $this->log('econda: '.'_loginFailUserUnknown');
         $_SESSION['login_success'] = -1;
      }
   	
      function _loginFailPasswordInvalid()
      {
         $this->log('econda: '.'_loginFailPasswordInvalid');
         $_SESSION['login_success'] = -2;
      }

      function _userRegistered()
      {
         $this->log('econda: userRegistered');
         $_SESSION['econda_user_registered'] = 1;
      }

   	function _emptyCart() {
         $this->log('econda: '.'_emptyCart');
   		//$_SESSION['econda_cart'] = array();
   	}
   	
   	function _delArticle($pID,$qty,$old_qty) {
         $this->log('econda: '.'_delArticle '. implode(', ', func_get_args()));
   		$_SESSION['econda_cart'][] = array(
            'todo' => 'del',
            'id' => xtc_db_input($pID),
            'cart_qty' => xtc_remove_non_numeric($qty),
            'old_qty' => $old_qty
         );  		
         $this->log("econda_cart is now:\n".print_r($_SESSION['econda_cart'], true));
   	}
   	
   	function _updateProduct($pID,$qty,$old_qty) {
         $this->log('econda: '.'_updateProduct '. implode(', ', func_get_args()));
   		$_SESSION['econda_cart'][] = array(
            'todo' => 'update',
            'id' => xtc_db_input($pID),
            'cart_qty' => xtc_remove_non_numeric($qty),
            'old_qty' => $old_qty
         );					
         $this->log("econda_cart is now:\n".print_r($_SESSION['econda_cart'], true));
   	}
   	
   	function _addProduct($pID,$qty,$old_qty) {
         $this->log('econda: '.'_addProduct '. implode(', ', func_get_args()));
   		$_SESSION['econda_cart'][] = array(
            'todo' => 'add',
            'id' => xtc_db_input($pID),
            'cart_qty' => xtc_remove_non_numeric($qty),
            'old_qty' => $old_qty
         );
         $this->log("econda_cart is now:\n".print_r($_SESSION['econda_cart'], true));
   	}
   	
     	
   }
   
