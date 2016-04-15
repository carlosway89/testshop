<?php
/* --------------------------------------------------------------
  checkout_page_confirm.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
/**
    Shop System Plugins - Terms of use

    This terms of use regulates warranty and liability between Wirecard
    Central Eastern Europe (subsequently referred to as WDCEE) and it's
    contractual partners (subsequently referred to as customer or customers)
    which are related to the use of plugins provided by WDCEE.

    The Plugin is provided by WDCEE free of charge for it's customers and
    must be used for the purpose of WDCEE's payment platform integration
    only. It explicitly is not part of the general contract between WDCEE
    and it's customer. The plugin has successfully been tested under
    specific circumstances which are defined as the shopsystem's standard
    configuration (vendor's delivery state). The Customer is responsible for
    testing the plugin's functionality before putting it into production
    enviroment.
    The customer uses the plugin at own risk. WDCEE does not guarantee it's
    full functionality neither does WDCEE assume liability for any
    disadvantage related to the use of this plugin. By installing the plugin
    into the shopsystem the customer agrees to the terms of use. Please do
    not use this plugin if you do not agree to the terms of use!

	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]

*/
// set order-status 1 (pending)
define('MODULE_PAYMENT_WCP_ORDER_STATUS_PENDING', 1);
// set order-status 2 (processing)
define('MODULE_PAYMENT_WCP_ORDER_STATUS_SUCCESS', 2);
// set order-status 99 (canceled)
define('MODULE_PAYMENT_WCP_ORDER_STATUS_FAILED', 99);
chdir('../../');

function debug_msg($msg)
{

   $fh = fopen('wirecard_checkout_page_notify_debug.txt', 'a');
   fwrite($fh, date('r'). ". ". $msg."\n");
   //file_put_contents('wirecard_checkout_page_notify_debug.txt', date('r').". ".$msg . "\n", FILE_APPEND);
   fclose($fh);
}
debug_msg('called script from '.$_SERVER['REMOTE_ADDR']);
$returnMessage = null;
if ($_POST)
{
     include ('includes/application_top.php');
     include ('includes/modules/payment/wcp.php');
     $languageArray = Array('language' => htmlentities($_POST['confirmLanguage']),
                           'language_id' => htmlentities($_POST['confirmLanguageId']));

	 $coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
	 $coo_lang_file_master->init_from_lang_file('lang/'.$languageArray['language'].'/modules/payment/wcp.php');
     debug_msg("Finished Initialization of the confirm_callback.php script" );
     debug_msg("Received this POST: " . print_r($_POST, 1));
     $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
     $q = xtc_db_query("INSERT INTO ".TABLE_PAYMENT_WCP."
       (orders_id, response, created_at)
       VALUES
       ('".$order_id."','".  xtc_db_input(serialize($_POST))."', NOW())");
     if(!$q)
     {
         $returnMessage = 'Transactiontable update failed.';
     }
     debug_msg('Payment Table updated='.$q);
     if(isset($_POST['responseFingerprintOrder']) && isset($_POST['responseFingerprint']))
     {
         $responseFingerprintOrder = explode(',', $_POST['responseFingerprintOrder']);
         $responseFingerprintSeed  = '';
         $c = strtoupper($_POST['paymentCode']);
         if(get_magic_quotes_gpc() || get_magic_quotes_runtime())
         {
             $stipslashes = true;
         }
         else
         {
             $stipslashes = false;
         }
         //calculating fingerprint;
         foreach($responseFingerprintOrder as $k)
         {
             if($stipslashes)
             {
                 $responseFingerprintSeed .= (strtoupper($k) == 'SECRET' ? wcp_core::constant("MODULE_PAYMENT_{$c}_PRESHARED_KEY") : stripslashes($_POST[$k]));
             }
             else
             {
                 $responseFingerprintSeed .= (strtoupper($k) == 'SECRET' ? wcp_core::constant("MODULE_PAYMENT_{$c}_PRESHARED_KEY") : $_POST[$k]);
             }
         }

         $calculated_fingerprint = md5($responseFingerprintSeed);
         if($calculated_fingerprint == $_POST['responseFingerprint'])
         {
             debug_msg('Fingerprint is OK');

             switch ($_POST['paymentState'])
             {
                 case 'SUCCESS':
                     $order_status = MODULE_PAYMENT_WCP_ORDER_STATUS_SUCCESS;
                     break;

                 case 'PENDING':
                     $order_status = MODULE_PAYMENT_WCP_ORDER_STATUS_PENDING;
                     break;

                 default:
                     $order_status = MODULE_PAYMENT_WCP_ORDER_STATUS_FAILED;

             }
             debug_msg('Callback Process');
             $q = xtc_db_query('UPDATE ' . TABLE_ORDERS . ' SET orders_status=\'' . xtc_db_input($order_status) . '\' WHERE orders_id=\'' . (int)$order_id.'\';');
             if(!$q)
             {
                 $returnMessage = 'Orderstatus update failed.';
             }
             debug_msg('Order-Status updated='.$q);
             $avsStatusCode = isset($_POST['avsResponseCode']) ? $_POST['avsResponseCode'] : '';
             $avsStatusMessage = isset($_POST['avsResponseMessage']) ? $_POST['avsResponseMessage'] : '';
             if($avsStatusCode != '' && $avsStatusMessage != '')
             {
                 $avsStatus = 'AVS Result: ' . $avsStatusCode . ' - ' . $avsStatusMessage;
             }
             else
             {
                 $avsStatus = '';
             }
             debug_msg($avsStatus);
             $q = xtc_db_query('INSERT INTO '.TABLE_ORDERS_STATUS_HISTORY.'
             (orders_id,  orders_status_id, date_added, customer_notified, comments)
             VALUES
               (' . (int)$order_id . ', ' . (int)$order_status . ', NOW(), "0", "' . xtc_db_input($avsStatus) . '")');
             if(!$q)
             {
                 $returnMessage = 'Statushistory update failed';
             }
             debug_msg('Order-Status-History updated='.$q);
             $mail = create_status_mail_for_order($order_id, $languageArray, CONFIRM_MAIL_COMMENT_SUCCESS);
             if(!$mail)
             {
                 $returnMessage = 'Can\'t send confirmation mail.';
             }
             else
             {
                 debug_msg('Customer has been notified about status change.');
             }
         }
         else
         {
             $returnMessage = 'Fingerprint validation failed.';
             debug_msg('Invalid Responsefingerprint.');
             debug_msg('calc-fingerprint: ' .$calculated_fingerprint);
             debug_msg('response-fingerprint: '. $_POST['responseFingerprint']);
         }
     }
     else
     {
         debug_msg('No fingerprint found.');
         if(isset($_POST['paymentState']) && $_POST['paymentState'] == 'CANCEL')
         {
             debug_msg('Order is Canceled');
             $order_status = MODULE_PAYMENT_WCP_ORDER_STATUS_FAILED;

             debug_msg('Callback Process');
             $q = xtc_db_query("UPDATE ".TABLE_ORDERS."
               SET orders_status='" . xtc_db_input($order_status) . "',
                 gm_cancel_date='".date('Y-m-d H:i:s')."'
               WHERE orders_id='" . (int)$order_id . "'");
             if(!$q)
             {
                 $returnMessage = 'Orderstatus update failed.';
             }
             debug_msg('Order-Status updated='.$q);
             $q = xtc_db_query("INSERT INTO ".TABLE_ORDERS_STATUS_HISTORY."
               (orders_id,  orders_status_id, date_added, customer_notified, comments)
               VALUES
               ('" . (int)$order_id . "', '" . (int)$order_status . "', NOW(), '0', '')");
             if(!$q)
             {
                 $returnMessage = 'Statushistory update failed';
             }
             debug_msg('Order-Status-History updated='.$q);

             $mail = create_status_mail_for_order($order_id, $languageArray, CONFIRM_MAIL_COMMENT_CANCEL);
             if(!$mail)
             {
                 $returnMessage = 'Can\'t send confirmation mail.';
             }
             else
             {
                 debug_msg('Customer has been notified about status change.');
             }

             // restock order
             $restocked = xtc_remove_order($order_id, true);
             if($restocked)
             {
                 debug_msg('Order Restocked');
             }
         }
         elseif(isset($_POST['paymentState']) && $_POST['paymentState'] == 'FAILURE')
         {
             $message = isset($_POST['message']) ? htmlentities($_POST['message']) : '';
             debug_msg('Order Failed: '.$message);
             $order_status = MODULE_PAYMENT_WCP_ORDER_STATUS_FAILED;
             debug_msg('Callback Process');
             $q = xtc_db_query("UPDATE ".TABLE_ORDERS."
               SET orders_status='" . (int)$order_status . "',
                 gm_cancel_date='".date('Y-m-d H:i:s')."'
               WHERE orders_id='" . (int)$order_id . "'");
             if(!$q)
             {
                 $returnMessage = 'Orderstatus update failed.';
             }
             debug_msg('Order-Status updated='.$q);
             $q = xtc_db_query("INSERT INTO ".TABLE_ORDERS_STATUS_HISTORY."
               (orders_id,  orders_status_id, date_added, customer_notified, comments)
               VALUES
               ('" . (int)$order_id ."', '" . (int)$order_status . "', NOW(), '0', '" . xtc_db_input($message) . "')");
             if(!$q)
             {
                 $returnMessage = 'Statushistory update failed';
             }
             debug_msg('Order-Status-History updated='.$q);
             $mail = create_status_mail_for_order($order_id, $languageArray, CONFIRM_MAIL_COMMENT_FAILURE);
             if(!$mail)
             {
                 $returnMessage = 'Can\'t send confirmation mail.';
             }
             else
             {
                 debug_msg('Customer has been notified about status change.');
             }
             // restock order
             $restocked = xtc_remove_order($order_id, true);
             if($restocked)
             {
                 debug_msg('Order Restocked');
             }
         }
         elseif(isset($_POST['paymentState']) && $_POST['paymentState'] == 'SUCCESS')
         {
             $returnMessage = 'Mandatory fields not used.';
         }
     }

	 xtc_db_close();
}
else
{
    $returnMessage = 'Not a POST request';
}
echo _wirecardCheckoutPageConfirmResponse($returnMessage);
debug_msg("-- script reached eof - executed without errors --\n");

function create_status_mail_for_order($oID, $language, $comment)
{
    $_SESSION['language_id'] = $language['language_id'];
    require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
    require_once (DIR_FS_CATALOG . 'gm/classes/GMLogoManager.php');
    if (file_exists(DIR_WS_CLASSES . 'Smarty_2.6.14/Smarty.class.php'))
        require_once (DIR_WS_CLASSES . 'Smarty_2.6.14/Smarty.class.php');
    else
        require_once (DIR_WS_CLASSES . 'Smarty/Smarty.class.php');

    require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');

    $smarty = new Smarty();

    // assign language to template for caching
    $smarty->assign('language', $language['language']);
    $smarty->caching = false;

    // set dirs manual
    $smarty->template_dir = DIR_FS_CATALOG.'templates';
    $smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
    $smarty->config_dir = DIR_FS_CATALOG.'lang';

    $smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
    $smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

    $gm_logo_mail = new GMLogoManager('gm_logo_mail');
    if($gm_logo_mail->logo_use == '1')
    {
        $smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
    }
    require_once (DIR_WS_CLASSES.'order.php');
    $order = new order($oID);
    $orderDate = xtc_date_long($order->info['date_purchased']);
    $smarty->assign('ORDER_DATE', $orderDate);
    $consumerName = $order->customer['name'];
    $smarty->assign('NAME', $consumerName);
    $consumerMail = $order->customer['email_address'];
    $smarty->assign('ORDER_NR', $oID);
    $orderStatus = $order->info['orders_status'];
    $smarty->assign('ORDER_STATUS', $orderStatus);
    $mailNotify = $comment;
    $smarty->assign('NOTIFY_COMMENTS', $mailNotify);
    $mailSubject = CONFIRM_MAIL_SUBJECT . $oID .', ' . $orderDate . ' ' . $consumerName;
    $shopBillingMail = EMAIL_BILLING_ADDRESS;
    $shopBillingName = EMAIL_BILLING_NAME;
    $shopReplyMail = EMAIL_BILLING_REPLY_ADDRESS;
    $shopReplyName = EMAIL_BILLING_REPLY_ADDRESS_NAME;

	$htmlBody = fetch_email_template($smarty, 'change_order_mail', 'html', 'admin/');
	$textBody = fetch_email_template($smarty, 'change_order_mail', 'txt', 'admin/');

    $return = xtc_php_mail($shopBillingMail, $shopBillingName, $consumerMail, $consumerName, '', $shopReplyMail, $shopReplyName, '', '', $mailSubject, $htmlBody, $textBody);
    return $return;
}

// from admin/includes/functions/general.php
function xtc_remove_order($order_id, $restock = false)
{
    if ($restock == 'on')
    {
        // BOF GM_MOD:
        $order_query = xtc_db_query("
                                    SELECT
                                        op.orders_products_id,
                                        op.products_id,
                                        op.products_quantity,
                                        o.date_purchased
                                    FROM " .
                                        TABLE_ORDERS_PRODUCTS . " op
                                    LEFT JOIN  " .
                                        TABLE_ORDERS . " o
                                    ON
                                        op.orders_id = o.orders_id
                                    WHERE
                                        op.orders_id = '" . xtc_db_input($order_id) . "'
        ");

        while ($order = xtc_db_fetch_array($order_query))
        {
            /* BOF SPECIALS RESTOCK */
            $t_query = xtc_db_query("
                                    SELECT
                                        specials_date_added
                                    AS
                                        date
                                    FROM " .
                                        TABLE_SPECIALS . "
                                    WHERE
                                        specials_date_added < '" .  $order['date_purchased']    . "'
                                    AND
                                        products_id         = '" .  $order['products_id']       . "'
            ");

            if((int)xtc_db_num_rows($t_query) > 0)
            {
                xtc_db_query("
                                UPDATE " .
                                    TABLE_SPECIALS . "
                                SET
                                    specials_quantity = specials_quantity + " . $order['products_quantity'] . ",
                                    status = 1
                                WHERE
                                    products_id = '" . $order['products_id'] . "'
                ");
            }
            /* EOF SPECIALS RESTOCK */

            xtc_db_query("
                            UPDATE " .
                                TABLE_PRODUCTS . "
                            SET
                                products_quantity = products_quantity + ".$order['products_quantity'].",
                                products_ordered = products_ordered - ".$order['products_quantity'].",
                                products_status = 1
                            WHERE
                                products_id = '".$order['products_id']."'
            ");

            // BOF GM_MOD
            if(ATTRIBUTE_STOCK_CHECK == 'true')
            {
                $gm_get_orders_attributes = xtc_db_query("
                                                        SELECT
                                                            products_options,
                                                            products_options_values
                                                        FROM
                                                            orders_products_attributes
                                                        WHERE
                                                            orders_id = '" . xtc_db_input($order_id) . "'
                                                        AND
                                                            orders_products_id = '" . $order['orders_products_id'] . "'
                ");

                while($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes))
                {
                    $gm_get_attributes_id = xtc_db_query("
                                                        SELECT
                                                            pa.products_attributes_id
                                                        FROM
                                                            products_options_values pov,
                                                            products_options po,
                                                            products_attributes pa
                                                        WHERE
                                                            po.products_options_name = '" . $gm_orders_attributes['products_options'] . "'
                                                            AND po.products_options_id = pa.options_id
                                                            AND pov.products_options_values_id = pa.options_values_id
                                                            AND pov.products_options_values_name = '" . $gm_orders_attributes['products_options_values'] . "'
                                                            AND pa.products_id = '" . $order['products_id'] . "'
                                                        LIMIT 1
                    ");

                    if(xtc_db_num_rows($gm_get_attributes_id) == 1)
                    {
                        $gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

                        xtc_db_query("
                                        UPDATE
                                            products_attributes
                                        SET
                                            attributes_stock = attributes_stock + ".$order['products_quantity']."
                                        WHERE
                                            products_attributes_id = '" . $gm_attributes_id['products_attributes_id'] . "'
                        ");
                    }
                }
            }
            // EOF GM_MOD
        }
        return true;
    }
    else
    {
        return false;
    }
}

function _wirecardCheckoutPageConfirmResponse($message = null)
{
    if($message != null)
    {
        debug_msg($message);
        $value = 'result="NOK" message="' . $message . '" ';
    }
    else
    {
        $value = 'result="OK"';
    }
    return '<QPAY-CONFIRMATION-RESPONSE ' . $value . ' />';
}
