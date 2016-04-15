<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */


class ShopgateTools
{
    
    /**
     * @param bool $asArray
     *
     * @return array|string
     */
    public static function getGambioVersion($asArray = true)
    {
        
        // Get Gambio GX version
        if (file_exists(dirname(__FILE__) . '/../../release_info.php')) {
            include(dirname(__FILE__) . '/../../release_info.php');
            
            if (isset($gx_version) && mb_substr($gx_version, 0, 2) == 'v2') {
                // only on version 2.xxxx
                $debugFile = DIR_FS_CATALOG . 'system/core/Debugger.inc.php';
                if (file_exists($debugFile)) {
                    require_once $debugFile;
                    $GLOBALS['coo_debugger'] = new Debugger();
                }
            }
        }
        
        $gambioGXVersion = array(
            'main_version' => '1',
            'sub_version'  => '0',
            'revision'     => '0',
        );
        
        $gxVersionFileDestination = '/' . trim(DIR_FS_CATALOG, '/') . '/release_info.php';
        if (file_exists($gxVersionFileDestination)) {
            require_once $gxVersionFileDestination;
            if (preg_match(
                '/(?P<main_version>[1-9]+).(?P<sub_version>[0-9]+).(?P<revision>[0-9]+)/', $gx_version, $matches
            )) {
                $gambioGXVersion = array(
                    'main_version' => $matches['main_version'],
                    'sub_version'  => $matches['sub_version'],
                    'revision'     => $matches['revision'],
                );
            }
        }
        
        return ($asArray) ? $gambioGXVersion : implode(".", array_keys($gambioGXVersion));
    }
    
    /**
     * @param $version
     *
     * @return bool
     */
    public static function isGambioVersionLowerThan($version)
    {
        return (version_compare($version, self::getGambioVersion(false)) >= 0) ? true : false;
    }
} 
