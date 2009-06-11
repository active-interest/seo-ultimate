<?php
/*
Plugin Name: SEO Ultimate
Plugin URI: http://www.seodesignsolutions.com/wordpress-seo/
Description: This all-in-one SEO plugin can rewrite title tags, edit meta data, add noindex to pages, and insert canonical tags (with many more features coming soon).
Version: 0.3
Author: SEO Design Solutions
Author URI: http://www.seodesignsolutions.com/
Text Domain: seo-ultimate
*/

/**
 * The main SEO Ultimate plugin file.
 * @package SeoUltimate
 * @version 0.3
 * @link http://www.seodesignsolutions.com/wordpress-seo/ SEO Ultimate Homepage
 */

/*
Copyright © 2009 John Lamansky

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/********** CONSTANTS **********/

define("SU_PLUGIN_NAME", "SEO Ultimate");
define("SU_PLUGIN_URI", "http://www.seodesignsolutions.com/wordpress-seo/");
define("SU_VERSION", "0.3");
define("SU_AUTHOR", "SEO Design Solutions");
define("SU_AUTHOR_URI", "http://www.seodesignsolutions.com/");
define("SU_USER_AGENT", "SeoUltimate/0.3");

define('SU_MODULE_ENABLED', 10);
define('SU_MODULE_SILENCED', 5);
define('SU_MODULE_HIDDEN', 0);
define('SU_MODULE_DISABLED', -10);

define('SU_RESULT_OK', 1);
define('SU_RESULT_WARNING', 0);
define('SU_RESULT_ERROR', -1);


/********** INCLUDES **********/

require('class.seo-ultimate.php');
require('class.su-module.php');
require('class.su-widget.php');
require('functions.php');


/********** PLUGIN FILE LOAD HANDLER **********/

//If we're running WordPress, then initialize the main class defined above.
//Or, show a blank page on direct load.

global $seo_ultimate;
if (defined('ABSPATH'))
	$seo_ultimate = new SEO_Ultimate();
else {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

?>