<?php
/* --------------------------------------------------------------
   gm_gprint_admin_header.php 2009-11-13 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php  
if(strstr($_SERVER['PHP_SELF'], 'gm_gprint.php') == 'gm_gprint.php')
{ 
?>
	<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/ajaxfileupload/ajaxfileupload.js"></script>
<?php  
} 
?>