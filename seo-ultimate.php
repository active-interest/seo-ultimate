<?php
/*
Plugin Name: SEO Ultimate
Plugin URI: http://www.seodesignsolutions.com/wordpress-seo/
Description: This all-in-one SEO plugin gives you control over titles, noindex, meta data, slugs, canonical tags, "more" links, 404 error tracking, and more.
Version: 1.5.1
Author: SEO Design Solutions
Author URI: http://www.seodesignsolutions.com/
Text Domain: seo-ultimate
*/

/**
 * The main SEO Ultimate plugin file.
 * @package SeoUltimate
 * @version 1.5.1
 * @link http://www.seodesignsolutions.com/wordpress-seo/ SEO Ultimate Homepage
 */

/*
Copyright (c) 2009 John Lamansky

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

//Reading plugin info from constants is faster than trying to parse it from the header above.
define("SU_PLUGIN_NAME", "SEO Ultimate");
define("SU_PLUGIN_URI", "http://www.seodesignsolutions.com/wordpress-seo/");
define("SU_VERSION", "1.5.1");
define("SU_AUTHOR", "SEO Design Solutions");
define("SU_AUTHOR_URI", "http://www.seodesignsolutions.com/");
define("SU_USER_AGENT", "SeoUltimate/1.5.1");

/********** INCLUDES **********/

//Libraries
include 'includes/jlfunctions/jlfunctions.php';
include 'includes/jlwp/jlwp.php';
if (!class_exists('Markdown'))
	include_once 'includes/markdown/markdown.php';

//Plugin files
include 'plugin/su-constants.php';
include 'plugin/su-functions.php';
include 'plugin/class.seo-ultimate.php';
include 'plugin/class.su-hitset.php';

//Module files
include 'modules/class.su-module.php';
include 'modules/class.su-importmodule.php';


/********** PLUGIN FILE LOAD HANDLER **********/

//If we're running WordPress, then initialize the main class loaded above.
//Or, show a blank page on direct load.

global $seo_ultimate;
if (defined('ABSPATH'))
	$seo_ultimate =& new SEO_Ultimate(__FILE__);
else {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

?>