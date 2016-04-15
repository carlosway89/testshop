<?php
/* --------------------------------------------------------------
   SampleExtender.inc.php 2015-10-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SampleExtender extends SampleExtender_parent
{
    function proceed()
    {
        parent::proceed();

        $this->v_output_buffer['single_action'] = '<a href="' . xtc_href_link('gm_send_order.php', 'oID=0&type=recreate_order') . '" target="_blank">TEST</a>';
        $this->v_output_buffer['multi_action'] = '<a data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="multi_delete" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')) . 'oID=0&action=delete') . '">MULTI-TEST</a>';
    }
}