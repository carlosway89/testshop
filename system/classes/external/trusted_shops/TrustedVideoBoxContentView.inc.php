<?php
/* --------------------------------------------------------------
   TrustedVideoBoxContentView.inc.php 2014-07-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class TrustedVideoBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_gm_trusted_shops_video.html');
		$this->set_caching_enabled(false);
	}


	public function prepare_data()
	{
		$this->build_html = false;
		$t_html_output = '';
		$t_videobox_enabled = gm_get_conf('GM_TS_VIDEOBOX_ENABLED') == 1;
		$t_edit_mode = $_SESSION['style_edit_mode'] == 'edit';

		if($t_videobox_enabled || $t_edit_mode) {
			$service = new GMTSService();
			$tsid = $service->findRatingID($_SESSION['language_code']);
			if($tsid != false) {
				$video_url = 'https://www.trustedshops.de/shop/certificate.php?shop_id='.$tsid.'#play';
				$teaser_img_url = GM_HTTP_SERVER.DIR_WS_CATALOG.'images/trusted_video_teaser.gif';
				$t_box_content = '<a href="'.$video_url.'" target="_new"><img src="'.$teaser_img_url.'"></a>';
				$this->set_content_data('CONTENT', $t_box_content);
				$this->build_html = true;
			}
		}
	}
}