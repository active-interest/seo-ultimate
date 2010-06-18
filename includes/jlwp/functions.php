<?php

class suwp {

	/**
	 * Determines the ID of the current post.
	 * Works in the admin as well as the front-end.
	 * 
	 * @return int|false The ID of the current post, or false on failure.
	 */
	function get_post_id() {
		if (is_admin())
			return intval($_REQUEST['post']);
		elseif (in_the_loop())
			return intval(get_the_ID());
		elseif (is_singular()) {
			global $wp_query;
			return $wp_query->get_queried_object_id();
		}
		
		return false;
	}
	
	function get_any_posts($args = null) {
		$args['post_type'] = implode(',', suwp::get_post_type_names());
		return get_posts($args);
	}
	
	function get_post_type_names() {
		if (function_exists('get_post_types')) {
			if ($types = get_post_types(array('public' => true), 'names'))
				return $types;
			else
				return array('post', 'page', 'attachment');
		}
		
		return array();
	}
	
	function get_taxonomies() {
		global $wp_taxonomies;
		$taxonomies = array();
		foreach ($wp_taxonomies as $key => $taxonomy)
			if (in_array('post', (array)$taxonomy->object_type))
				$taxonomies[$key] = $taxonomy;
		return $taxonomies;
	}
	
	/**
	 * Loads a webpage and returns its HTML as a string.
	 * 
	 * @param string $url The URL of the webpage to load.
	 * @param string $ua The user agent to use.
	 * @return string The HTML of the URL.
	 */
	function load_webpage($url, $ua) {
		
		$options = array();
		$options['headers'] = array(
			'User-Agent' => $ua
		);
		
		$response = wp_remote_request($url, $options);
		
		if ( is_wp_error( $response ) ) return false;
		if ( 200 != $response['response']['code'] ) return false;
		
		return trim( $response['body'] );
	}
	
	/**
	 * Loads an RSS feed and returns it as an object.
	 * 
	 * @param string $url The URL of the RSS feed to load.
	 * @param callback $ua The user agent to use.
	 * @return object $rss The RSS object.
	 */
	function load_rss($url, $ua) {
		$uafunc = create_function('', "return '$ua';");
		add_filter('http_headers_useragent', $uafunc);
		require_once (ABSPATH . WPINC . '/rss.php');
		$rss = fetch_rss($url);
		remove_filter('http_headers_useragent', $uafunc);
		return $rss;
	}
	
	/**
	 * @return string
	 */
	function add_backup_url($text) {
		$anchor = __('backup your database', 'seo-ultimate');
		return str_replace($anchor, '<a href="'.suwp::get_backup_url().'" target="_blank">'.$anchor.'</a>', $text);
	}
	
	/**
	 * @return string
	 */
	function get_backup_url() {
		if (is_plugin_active('wp-db-backup/wp-db-backup.php'))
			return admin_url('tools.php?page=wp-db-backup');
		else
			return 'http://codex.wordpress.org/Backing_Up_Your_Database';
	}
	
	function get_edit_term_link($id, $taxonomy) {
		if ($taxonomy == 'category')
			return admin_url("categories.php?action=edit&amp;cat_ID=$id");
		else
			return get_edit_tag_link($id, $taxonomy);
	}
	
	function get_all_the_terms($id = 0) {
		
		$id = (int)$id;
		
		if ($id) {
			$post = get_post($id);
			if (!$post) return false;
		} else {
			if (!in_the_loop()) return false;
			global $post;
			$id = (int)$post->ID;
		}
		
		$taxonomies = get_object_taxonomies($post);
		$terms = array();
		
		foreach ($taxonomies as $taxonomy) {
			$newterms = get_the_terms($id, $taxonomy);
			if ($newterms) $terms = array_merge($terms, $newterms);
		}
		
		return $terms;
	}
	
	function remove_instance_action($tag, $class, $function, $priority=10) {
		return suwp::remove_instance_filter($tag, $class, $function, $priority);
	}
	
	function remove_instance_filter($tag, $class, $function, $priority=10) {
		if (isset($GLOBALS['wp_filter'][$tag][$priority]) && count($GLOBALS['wp_filter'][$tag][$priority])) {
			foreach ($GLOBALS['wp_filter'][$tag][$priority] as $key => $x) {
				if (sustr::startswith($key, $class.$function)) {
					unset($GLOBALS['wp_filter'][$tag][$priority][$key]);
					if ( empty($GLOBALS['wp_filter'][$tag][$priority]) )
						unset($GLOBALS['wp_filter'][$tag][$priority]);
					unset($GLOBALS['merged_filters'][$tag]);
					return true;
				}
			}
		}
		
		return false;
	}
}

?>