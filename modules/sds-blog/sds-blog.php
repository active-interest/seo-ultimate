<?php
/**
 * SEO Design Solutions Whitepapers Module
 * 
 * @version 1.0.6
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_SdsBlog extends SU_Module {
	
	function get_module_title() { return __('Whitepapers', 'seo-ultimate'); }
	function get_page_title() { return __('SEO Design Solutions Whitepapers', 'seo-ultimate'); }	
	function has_menu_count() { return true; }
	function get_menu_count() { return $this->get_unread_count(); }
	
	function __construct() {
		add_filter('su_settings_export_array', array(&$this, 'filter_export_array'));
	}
	
	function init() {
		$this->cron('load_blog_rss', 'hourly');
	}
	
	function get_default_settings() {
		//Don't notify about new items when the plugin is just installed
		return array('lastread' => time());
	}
	
	function filter_export_array($settings) {
		unset($settings[$this->get_module_key()]['rssitems']);
		return $settings;
	}
	
	function load_blog_rss() {
		$rss = suwp::load_rss('http://feeds.seodesignsolutions.com/SeoDesignSolutionsBlog', SU_USER_AGENT);
		if ($rss) $this->update_setting('rssitems', $rss->items);
	}
	
	function admin_page_contents() {
		global $seo_ultimate;
		echo "<a href='http://www.seodesignsolutions.com'><img src='{$seo_ultimate->plugin_dir_url}plugin/images/sds-logo.png' alt='".__("SEO Design Solutions", 'seo-ultimate')."' id='sds-logo' /></a>";
		echo "<p>".__("Search engine optimization articles from the company behind the SEO Ultimate plugin.", 'seo-ultimate')."</p>\n";
		echo "<div class='rss-widget'>\n";
		
		add_filter('http_headers_useragent', 'su_get_user_agent');
		wp_widget_rss_output( 'http://feeds.seodesignsolutions.com/SeoDesignSolutionsBlog', array('show_summary' => 1, 'show_date' => 1) );
		remove_filter('http_headers_useragent', 'su_get_user_agent');
		
		echo "</div>\n";
		$this->update_setting('lastread', time());
	}
	
	function get_unread_count() {
		
		$rss = $this->get_setting('rssitems');
		
		if ($rss) {
			$lastread = $this->get_setting('lastread');
		
			$new = 0;
			foreach ($rss as $item) {
				if ($this->get_feed_item_date($item) > $lastread) $new++;
			}
			
			return $new;
			
		} else {
			return 0;
		}
	}
	
	function get_feed_item_date($item) {
		
		//Is there an Atom date? If so, parse it.
		if ($atom_date = $item['issued'])
			$date = parse_w3cdtf($atom_date);
		
		//Or is there an RSS2 date? If so, parse it.
		elseif ($rss_2_date = $item['pubdate'])
			$date = strtotime($rss_2_date);
		
		//Or is there an RSS1 date? If so, parse it.
		elseif ($rss_1_date = $item['dc']['date'])
			$date = parse_w3cdtf($rss_1_date);
			
		else $date = null;
		
		//Return a UNIX timestamp.
		if ($date) return $date; else return 0;
	}
	
	function admin_help() {
		return "<p>".__("The articles below are loaded from the SEO Design Solutions website. Click on an article&#8217s title to read it.", 'seo-ultimate')."</p>";
	}
	
}

} elseif ($_GET['css'] == 'admin') {
	header('Content-type: text/css');
?>

#su-sds-blog .rss-widget {
	background-color: white;
	border: 1px solid black;
	padding: 2em;
	margin: 2em 0;
	border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
}

#su-sds-blog a.rsswidget {
	font-size: 13px;
	font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
	line-height: 1.7em;
}

#su-sds-blog li a:visited {
	color: purple;
}

#su-sds-blog span.rss-date {
	margin-left: 3px;
}

#su-sds-blog li {
	padding-bottom: 1em;
}

#su-sds-blog img#sds-logo {
	float: right;
	border: 1px solid black;
	padding: 1em;
	background-color: white;
	border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
}

<?php
}
?>