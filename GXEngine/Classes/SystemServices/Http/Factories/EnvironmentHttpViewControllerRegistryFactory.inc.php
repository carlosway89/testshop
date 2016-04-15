<?php
/* --------------------------------------------------------------
   EnvironmentHttpContextFactory.inc.php 2015-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractHttpContextFactory');

/**
 * Class EnvironmentHttpViewControllerRegistryFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 * @extends    AbstractHttpContextFactory
 */
class EnvironmentHttpViewControllerRegistryFactory extends AbstractHttpContextFactory
{

	/**
	 * Create new registry object.
	 *
	 * @return HttpViewControllerRegistryInterface
	 */
	public function create()
	{
		$registry = MainFactory::create('HttpViewControllerRegistry');
		$this->_addAvailableControllers($registry);

		return $registry;
	}


	/**
	 * Add new available controller to the registry.
	 *
	 * @param \HttpViewControllerRegistryInterface $registry
	 *
	 * @todo Scan classes for implemented interface
	 */
	protected function _addAvailableControllers(HttpViewControllerRegistryInterface $registry)
	{
		$registry->set('Logoff', 'LogoffController');
		$registry->set('Sample', 'SampleController');
		$registry->set('Emails', 'EmailsController');
		$registry->set('PrintOrder', 'PrintOrderController');
		$registry->set('PopupContent', 'PopupContentController');
		$registry->set('PopupImage', 'PopupImageController');
		$registry->set('PrintProductInfo', 'PrintProductInfoController');
		$registry->set('PopupCouponHelp', 'PopupCouponHelpController');
		$registry->set('Download', 'DownloadController');
		$registry->set('AccountHistory', 'AccountHistoryController');
		$registry->set('CheckoutSuccess', 'CheckoutSuccessController');
		$registry->set('GmOpensearch', 'GmOpensearchController');
		$registry->set('PopupSearchHelp', 'PopupSearchHelpController');
		$registry->set('Wishlist', 'WishlistController');
		$registry->set('ProductsNew', 'ProductsNewController');
		$registry->set('ProductReviewsWrite', 'ProductReviewsWriteController');
		$registry->set('Withdrawal', 'WithdrawalController');
		$registry->set('Specials', 'SpecialsController');
		$registry->set('WhosOnline', 'WhosOnlineController');
		$registry->set('ClearCache', 'ClearCacheController');
		$registry->set('CreateRegistree', 'CreateRegistreeController');
		$registry->set('CreateGuest', 'CreateGuestController');
		$registry->set('Backup', 'BackupController');
		$registry->set('AddressBook', 'AddressBookController');
		$registry->set('AddressBookProcess', 'AddressBookProcessController');
		$registry->set('AdvancedSearch', 'AdvancedSearchController');
		$registry->set('AdvancedSearchResult', 'AdvancedSearchResultController');
		$registry->set('ShoppingCart', 'ShoppingCartController');
		$registry->set('ShowProductThumbs', 'ShowProductThumbsController');
		$registry->set('DisplayVvcodes', 'DisplayVvcodesController');
		$registry->set('GvSend', 'GvSendController');
		$registry->set('GvRedeem', 'GvRedeemController');
		$registry->set('GmCssMonitor', 'GmCssMonitorController');
		$registry->set('Newsletter', 'NewsletterController');
		$registry->set('GmAccountDelete', 'GmAccountDeleteController');
		$registry->set('GmPriceOffer', 'GmPriceOfferController');
		$registry->set('ShopContent', 'ShopContentController');
		$registry->set('AccountHistoryInfo', 'AccountHistoryController');
		$registry->set('ProductsReviewsWrite', 'ProductsReviewsWriteController');
		$registry->set('Countries', 'CountriesController');
		$registry->set('ProductInfo', 'ProductInfoController');
		$registry->set('BannerManager', 'BannerManagerController');
		$registry->set('BannerStatistic', 'BannerStatisticController');
		$registry->set('LightboxConfiguration', 'LightboxConfigurationController');
		$registry->set('LogoManager', 'LogoManagerController');
		$registry->set('TeaserSlider', 'TeaserSliderController');
		$registry->set('SeoBoostConfiguration', 'SeoBoostConfigurationController');
		$registry->set('SeoMetaConfiguration', 'SeoMetaConfigurationController');
		$registry->set('RobotsDownload', 'RobotsDownloadController');
		$registry->set('SitemapGenerator', 'SitemapGeneratorController');
		$registry->set('GmAnalytics', 'GmAnalyticsController');
		$registry->set('CustomerExport', 'CustomerExportController');
		$registry->set('CustomerGroups', 'CustomerGroupsController');
		$registry->set('Dashboard', 'DashboardController');
		$registry->set('UserConfiguration', 'UserConfigurationController');
		$registry->set('ShopKey', 'ShopKeyController');
		$registry->set('ImageProcessing', 'ImageProcessingController');
		$registry->set('EmbeddedModule', 'EmbeddedModuleController');
		$registry->set('ModuleCenter', 'ModuleCenterController');
		$registry->set('JanolawModuleCenterModule', 'JanolawModuleCenterModuleController');
		$registry->set('AffiliPrintModuleCenterModule', 'AffiliPrintModuleCenterModuleController');
		$registry->set('EcondaModuleCenterModule', 'EcondaModuleCenterModuleController');
		$registry->set('EkomiModuleCenterModule', 'EkomiModuleCenterModuleController');
		$registry->set('FindologicModuleCenterModule', 'FindologicModuleCenterModuleController');
		$registry->set('HermesModuleCenterModule', 'HermesModuleCenterModuleController');
		$registry->set('IloxxModuleCenterModule', 'IloxxModuleCenterModuleController');
		$registry->set('IntrashipModuleCenterModule', 'IntrashipModuleCenterModuleController');
		$registry->set('ItRechtModuleCenterModule', 'ItRechtModuleCenterModuleController');
		$registry->set('LettrModuleCenterModule', 'LettrModuleCenterModuleController');
		$registry->set('MagnalisterModuleCenterModule', 'MagnalisterModuleCenterModuleController');
		$registry->set('MailbeezModuleCenterModule', 'MailbeezModuleCenterModuleController');
		$registry->set('MediafinanzModuleCenterModule', 'MediafinanzModuleCenterModuleController');
		$registry->set('ProtectedShopsModuleCenterModule', 'ProtectedShopsModuleCenterModuleController');
		$registry->set('ShopgateModuleCenterModule', 'ShopgateModuleCenterModuleController');
		$registry->set('TrustedShopsModuleCenterModule', 'TrustedShopsModuleCenterModuleController');
		$registry->set('YategoModuleCenterModule', 'YategoModuleCenterModuleController');
		$registry->set('YoochooseModuleCenterModule', 'YoochooseModuleCenterModuleController');
		$registry->set('PayOneModuleCenterModule', 'PayOneModuleCenterModuleController');
		$registry->set('AmazonAdvPaymentsModuleCenterModule', 'AmazonAdvPaymentsModuleCenterModuleController');
		$registry->set('KlarnaModuleCenterModule', 'KlarnaModuleCenterModuleController');
		$registry->set('HeidelpayModuleCenterModule', 'HeidelpayModuleCenterModuleController');
		$registry->set('PaypalNGModuleCenterModule', 'PaypalNGModuleCenterModuleController');
		$registry->set('AfterbuyModuleCenterModule', 'AfterbuyModuleCenterModuleController');
		$registry->set('BrickfoxModuleCenterModule', 'BrickfoxModuleCenterModuleController');
		$registry->set('GoogleAdwordConversionModuleCenterModule', 'GoogleAdwordConversionModuleCenterModuleController');
		$registry->set('SkrillModuleCenterModule', 'SkrillModuleCenterModuleController');
		$registry->set('ProductAttributesModuleCenterModule', 'ProductAttributesModuleCenterModuleController');
		$registry->set('Shipcloud', 'ShipcloudController');
		$registry->set('ShipcloudModuleCenterModule', 'ShipcloudModuleCenterModuleController');
		$registry->set('DynamicShopMessages', 'DynamicShopMessagesController');
		$registry->set('OrderTooltip', 'OrderTooltipController');
	}
}
