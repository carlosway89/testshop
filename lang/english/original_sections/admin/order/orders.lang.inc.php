<?php
/* --------------------------------------------------------------
	orders.lang.inc.php 2015-10-05
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'ADMIN_INVOICE_PDF_NAME' => 'Rechnung_{ORDER_ID}_{INVOICE_ID}_{DATE}',
	'ADMIN_PACKINGSLIP_PDF_NAME' => 'Lieferschein_{ORDER_ID}_{DATE}',
	'BUTTON_DHL_LABEL' => 'DHL label',
	'BUTTON_EKOMI_SEND_MAIL' => 'Send eKomi-e-mail',
	'BUTTON_GM_CANCEL' => 'Cancel',
	'BUTTON_HERMES' => 'Hermes Versand',
	'BUTTON_MULTI_CANCEL' => 'Cancel',
	'BUTTON_MULTI_CHANGE_ORDER_STATUS' => 'Change status',
	'BUTTON_MULTI_DELETE' => 'Delete',
	'BUTTON_PP_RELOAD' => 'reload',
	'EKOMI_ALREADY_SEND_MAIL_ERROR' => 'The eKomi-e-mail was not sent, because the mail was already sent in the past.',
	'EKOMI_SEND_MAIL_ERROR' => 'The eKomi-e-mail was not sent, because a failure occurred. Look into the ekomi-errors-logfile in the export-directory for further information.',
	'EKOMI_SEND_MAIL_SUCCESS' => 'The eKomi-e-mail was successfully sent.',
	'EMAIL_SEPARATOR' => '------------------------------------------------------',
	'EMAIL_TEXT_COMMENTS_UPDATE' => "Comments on your order:\n\n%s\n\n",
	'EMAIL_TEXT_DATE_ORDERED' => 'Date Ordered:',
	'EMAIL_TEXT_INVOICE_URL' => 'Detailed Invoice:',
	'EMAIL_TEXT_ORDER_NUMBER' => 'Order Number:',
	'EMAIL_TEXT_STATUS_UPDATE' => "Your order has been updated to the following status.\n\nNew status: %s\n\nPlease reply to this email if you have any questions.\n",
	'EMAIL_TEXT_SUBJECT' => 'Order Update',
	'ENTRY_BILLING_ADDRESS' => 'Billing Address:',
	'ENTRY_CITY' => 'City:',
	'ENTRY_COUNTRY' => 'Country:',
	'ENTRY_CREDIT_CARD_CVV' => 'Security Code (CVV)):',
	'ENTRY_CREDIT_CARD_EXPIRES' => 'Credit Card Expires:',
	'ENTRY_CREDIT_CARD_NUMBER' => 'Credit Card Number:',
	'ENTRY_CREDIT_CARD_OWNER' => 'Credit Card Owner:',
	'ENTRY_CREDIT_CARD_TYPE' => 'Credit Card Type:',
	'ENTRY_CUSTOMER' => 'Customer:',
	'ENTRY_CUSTOMERS_GROUP' => 'Customer Group:',
	'ENTRY_CUSTOMERS_VAT_ID' => 'VAT No.:',
	'ENTRY_DATE_LAST_UPDATED' => 'Date Last Updated:',
	'ENTRY_DATE_PURCHASED' => 'Date Purchased:',
	'ENTRY_DELIVERY_TO' => 'Delivery To:',
	'ENTRY_EMAIL_ADDRESS' => 'Email Address:',
	'ENTRY_FIRST_ORDER' => 'First order',
	'ENTRY_LAST_ORDER' => 'Last order',
	'ENTRY_NEW_ORDER_STATUS' => 'New Order Status',
	'ENTRY_NOTIFY_COMMENTS' => 'Append Comments:',
	'ENTRY_NOTIFY_CUSTOMER' => 'Notify Customer:',
	'ENTRY_OPEN_CUSTOMER' => 'Open customer',
	'ENTRY_ORDER_INFORMATION' => 'Order information',
	'ENTRY_ORDER_SUM_TOTAL' => 'Order sum total', # nach besserer übersetzung fragen
	'ENTRY_ORDER_TOTAL' => 'Order total', # nach besserer übersetzung fragen
	'ENTRY_PAYMENT_METHOD' => 'Payment Method:',
	'ENTRY_POST_CODE' => 'Postcode (ZIP):',
	'ENTRY_PRINTABLE' => 'Print Invoice',
	'ENTRY_SEND_PARCEL_TRACKING_CODES' => 'Send tracking codes',
	'ENTRY_SHIP_TO' => 'SHIP TO:',
	'ENTRY_SHIPPING' => 'Shipping:',
	'ENTRY_SHIPPING_ADDRESS' => 'Shipping Address:',
	'ENTRY_SOLD_TO' => 'SOLD TO:',
	'ENTRY_STATE' => 'State:',
	'ENTRY_STATUS' => 'Status:',
	'ENTRY_STREET_ADDRESS' => 'Street Address:',
	'ENTRY_SUB_TOTAL' => 'Subtotal:',
	'ENTRY_SUBURB' => 'Suburb:',
	'ENTRY_TAX' => 'Tax:',
	'ENTRY_TELEPHONE' => 'Telephone:',
	'ENTRY_TOTAL' => 'Total:',
	'ERROR_ORDER_DOES_NOT_EXIST' => 'Order does not exist.',
	'GM_MAIL' => 'Email:',
	'GM_ORDERS_EDIT_CLOCK' => ' o\'clock',
	'GM_ORDERS_NUMBER' => 'Order No.: ',
	'GM_PRODUCTS' => 'Product(s)',
	'GM_SEND_ORDER_STATUS_MONO' => 'The order marked did not receive an order acceptance.',
	'GM_SEND_ORDER_STATUS_STEREO' => 'The order marked did not receive an order acceptance.',
	'HEADING_GM_STATUS' => 'Change Order Status',
	'HEADING_GX_CUSTOMIZER' => 'GX-Customizer Set',
	'HEADING_SUB_TITLE' => 'Customers',
	'HEADING_TITLE' => 'Orders',
	'HEADING_TITLE_SEARCH' => 'Order ID / invoice code:',
	'HEADING_TITLE_SEARCH_INVOICE' => 'Invoice ID:',
	'HEADING_TITLE_STATUS' => 'Status',
	'INVOICE_CREATED' => 'Invoices',
	'MAILBEEZ_CONVERSATIONS' => 'MailBeez - Conversations',
	'MAILBEEZ_NOTIFICATIONS' => 'MailBeez - Notifications',
	'MAILBEEZ_OVERVIEW' => 'MailBeez - Customer Insight',
	'NO_INVOICE_CREATED' => 'No invoices created.',
	'NO_PACKINGSLIP_CREATED' => 'No packing slips created.',
	'ORDER_HEADING_TITLE' => 'Order',
	'PACKINGSLIP_CREATED' => 'Packing slips',
	'SUCCESS_ORDER_UPDATED' => 'Success: Order has been updated successfully.',
	'TABLE_HEADING_ABANDONMENT_WITHDRAWAL' => 'Abandonment of the withdrawal right',
	'TABLE_HEADING_ACTION' => 'Action',
	'TABLE_HEADING_AFTERBUY' => 'Afterbuy',
	'TABLE_HEADING_COMMENTS' => 'Comments',
	'TABLE_HEADING_CUSTOMER_NOTIFIED' => 'Customer Notified',
	'TABLE_HEADING_CUSTOMERS' => 'Customers',
	'TABLE_HEADING_DATE_ADDED' => 'Date Added',
	'TABLE_HEADING_DATE_PURCHASED' => 'Date Purchased',
	'TABLE_HEADING_DISCOUNT' => 'Discount',
	'TABLE_HEADING_GM_STATUS' => 'Status',
	'TABLE_HEADING_GROSS' => 'Gross',
	'TABLE_HEADING_NET' => 'Net',
	'TABLE_HEADING_ORDER_TOTAL' => 'Order Total',
	'TABLE_HEADING_PAYMENT_METHOD' => 'Payment Method',
	'TABLE_HEADING_PAYPAL' => 'Paypal',
	'TABLE_HEADING_PRICE_EXCLUDING_TAX' => 'Price (excl)',
	'TABLE_HEADING_PRICE_INCLUDING_TAX' => 'Price (incl)',
	'TABLE_HEADING_PRODUCTS' => 'Products',
	'TABLE_HEADING_PRODUCTS_MODEL' => 'Model',
	'TABLE_HEADING_QUANTITY' => 'Qty',
	'TABLE_HEADING_STATUS' => 'Status',
	'TABLE_HEADING_TAX' => 'Tax',
	'TABLE_HEADING_TOTAL' => 'Total',
	'TABLE_HEADING_TOTAL_EXCLUDING_TAX' => 'Total (excl)',
	'TABLE_HEADING_TOTAL_INCLUDING_TAX' => 'Total',
	'TABLE_HEADING_WITHDRAWAL' => 'Withdrawal',
	'TABLE_HEADING_WITHDRAWAL_ID' => 'Withdrawal-ID',
	'TEXT_ABANDONMENT_DOWNLOAD' => 'Abandonment of the withdrawal right for download articles:',
	'TEXT_ABANDONMENT_SERVICE' => 'Abandonment of the withdrawal right for services:',
	'TEXT_ADD_WITHDRAWAL' => 'Add withdrawal',
	'TEXT_ADDRESS' => 'Address',
	'TEXT_ALL_ORDERS' => 'All Orders',
	'TEXT_AMOUNT' => 'Sum',
	'TEXT_BANK' => 'Bank Collection',
	'TEXT_BANK_BIC' => 'BIC:',
	'TEXT_BANK_BLZ' => 'Bank Code:',
	'TEXT_BANK_ERROR_1' => 'Account number and bank code are not compatible!<br />Please try again!',
	'TEXT_BANK_ERROR_2' => 'Sorry, we are unable to proof this account number!',
	'TEXT_BANK_ERROR_3' => 'Account number not proofable! Method of verify not implemented',
	'TEXT_BANK_ERROR_4' => 'Account number technically not proofable!<br />Please try again!',
	'TEXT_BANK_ERROR_5' => 'Bank code not found!<br />Please try again.!',
	'TEXT_BANK_ERROR_8' => 'No match for your bank code or bank code not provided!',
	'TEXT_BANK_ERROR_9' => 'No account number provided!',
	'TEXT_BANK_ERRORCODE' => 'Error code:',
	'TEXT_BANK_FAX' => 'Collect authorization will be approved by fax',
	'TEXT_BANK_IBAN' => 'IBAN:',
	'TEXT_BANK_NAME' => 'Bank:',
	'TEXT_BANK_NUMBER' => 'Account Number:',
	'TEXT_BANK_OWNER' => 'Account Holder:',
	'TEXT_BANK_PRZ' => 'Method of Verify:',
	'TEXT_BANK_STATUS' => 'Verify Status:',
	'TEXT_CONFIRMATION_NOT_SENT' => 'No email confirmation sent',
	'TEXT_CREATE_WITHDRAWAL' => 'Create withdrawal',
	'TEXT_DATE' => 'Date',
	'TEXT_DATE_ORDER_CREATED' => 'Date Created:',
	'TEXT_DATE_ORDER_LAST_MODIFIED' => 'Last Modified:',
	'TEXT_EDIT' => 'Edit',
	'TEXT_GM_STATUS' => 'Change Status',
	'TEXT_INFO_DELETE_INTRO' => 'Are you sure you want to delete this order?',
	'TEXT_INFO_HEADING_DELETE_ORDER' => 'Delete Order %s',
	'TEXT_INFO_HEADING_MULTI_CANCEL_ORDER' => 'Cancel Orders',
	'TEXT_INFO_HEADING_MULTI_DELETE_ORDER' => 'Delete Orders',
	'TEXT_INFO_MULTI_CANCEL_INTRO' => 'Are you sure you want to cancel this orders?',
	'TEXT_INFO_MULTI_DELETE_INTRO' => 'Are you sure you want to delete this orders?',
	'TEXT_INFO_PAYMENT_METHOD' => 'Payment Method:',
	'TEXT_INFO_REACTIVATEARTICLE' => 'Reset article status',
	'TEXT_INFO_RESHIPP' => 'Delivery status recalculate',
	'TEXT_INFO_RESTOCK_PRODUCT_QUANTITY' => 'Restock product quantity',
	'TEXT_MARKED_ELEMENTS' => 'Active Elements',
	'TEXT_NO_ORDER_HISTORY' => 'No Order History Available',
	'TEXT_NO_WITHDRAWALS' => 'No existing withdrawals',
	'TEXT_NO_WITHDRAWALS_LIST' => 'None in the list',
	'TEXT_ORDER_STATUS_HISTORY' => 'Order status history',
	'TEXT_PPNOTIFICATION_ERROR' => 'There is a connection problem with PayPal.<br />The payment information can not be loaded.<br />Please try again later.',
	'TEXT_PPNOTIFICATION_LOADING' => 'The PayPal payment information are loading.<br />The charging process stops after 60 seconds.<br />Please note, in this case, the notice.',
	'TEXT_PRODUCT_ATTRIBUTES' => 'Product Attributes',
	'TEXT_PRODUCT_PROPERTIES' => 'Product Properties',
	'TEXT_SELECTED_ORDERS' => 'Selected Orders',
	'TEXT_SHOW' => 'Show',
	'TEXT_SHOW_WITHDRAWAL' => 'Show withdrawal',
	'TEXT_TOTAL' => 'Total',
	'TEXT_VALIDATING' => 'Not validated',
	'TEXT_WITHDRAWAL' => 'Withdrawals',
	'TITLE_BANK_INFO' => 'Bank Transfer',
	'TITLE_CC_INFO' => 'Credit Card Info',
	'TITLE_CUSTOMER_ID' => 'Customer ID:',
	'TITLE_GIFT_MAIL' => 'Email Coupon',
	'TITLE_INVOICE' => 'Invoice',
	'TITLE_INVOICE_MAIL' => 'Email Invoice',
	'TITLE_ORDER' => 'Show Order',
	'TITLE_ORDER_CONFIRMATION' => 'Order Confirmation',
	'TITLE_ORDERS_BILLING_CODE' => 'Invoice Code',
	'TITLE_PACKINGS_BILLING_CODE' => 'Packing Slip Billing Code',
	'TITLE_PACKINGSLIP' => 'Packing Slip',
	'TITLE_RECREATE_ORDER' => 'Recreate Order Acceptance',
	'TITLE_SEND_ORDER' => 'Send Order Acceptance',
	'TITLE_SEND_ORDER_CONFIRMATION' => 'Send Order Confirmation',
	'TITLE_SEPA_INFO' => 'SEPA',
	'WARNING_ORDER_NOT_UPDATED' => 'Warning: Nothing to change. The order was not updated.'
);