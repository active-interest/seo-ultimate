<?php
/**
 * Canonicalizer Module
 * 
 * @version 1.0
 * @since 0.3
 */

if (class_exists('SU_Module')) {

class SU_Canonical extends SU_Module {

	function get_menu_title() { return __('Canonicalizer', 'seo-ultimate'); }
	
	function init() {
		if ($this->get_setting('link_rel_canonical'))
			add_action('su_head', array($this, 'link_rel_canonical_tag'));
	}
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->checkboxes(array(
				  'link_rel_canonical' => __("Generate <code>&lt;link rel=&quot;canonical&quot; /&gt;</code> tags.", 'seo-ultimate')
			));
		
		$this->admin_form_end();
	}
	
	function link_rel_canonical_tag() {
		if ($url = $this->get_canonical_url()) {
			$url = attribute_escape($url);
			echo "\t<link rel=\"canonical\" href=\"$url\" />\n";
		}
	}
	
	/**
	 * Returns the canonical URL to put in the link-rel-canonical tag.
	 * 
	 * This function is modified from the GPL-licensed {@link http://wordpress.org/extend/plugins/canonical/ Canonical URLs} plugin,
	 * which in turn was heavily based on the {@link http://svn.fucoder.com/fucoder/permalink-redirect/ Permalink Redirect} plugin.
	 */
	function get_canonical_url() {
		global $wp_query, $wp_rewrite;
		
		if ($wp_query->is_404 || $wp_query->is_search) return false;
		
		$haspost = count($wp_query->posts) > 0;
		
		if (get_query_var('m')) {
			// Handling special case with '?m=yyyymmddHHMMSS'
			// Since there is no code for producing the archive links for
			// is_time, we will give up and not try to produce a link.
			$m = preg_replace('/[^0-9]/', '', get_query_var('m'));
			switch (strlen($m)) {
				case 4: // Yearly
					$link = get_year_link($m);
					break;
				case 6: // Monthly
					$link = get_month_link(substr($m, 0, 4), substr($m, 4, 2));
					break;
				case 8: // Daily
					$link = get_day_link(substr($m, 0, 4), substr($m, 4, 2),
										 substr($m, 6, 2));
					break;
				default:
					return false;
			}
		
		} elseif (($wp_query->is_single || $wp_query->is_page) && $haspost) {
			$post = $wp_query->posts[0];
			$link = get_permalink($post->ID);
			// WP2.2: In Wordpress 2.2+ is_home() returns false and is_page() 
			// returns true if front page is a static page.
			if ($wp_query->is_page && ('page' == get_option('show_on_front')) && 
					$post->ID == get_option('page_on_front'))
				$link = trailingslashit($link);
		
		} elseif ($wp_query->is_author && $haspost) {
			$author = get_userdata(get_query_var('author'));
			if ($author === false) return false;
			$link = get_author_link(false, $author->ID, $author->user_nicename);
			
		} elseif ($wp_query->is_category && $haspost) {
			$link = get_category_link(get_query_var('cat'));
			
		} else if ($wp_query->is_tag  && $haspost) {
			$tag = get_term_by('slug',get_query_var('tag'),'post_tag');
			if (!empty($tag->term_id)) $link = get_tag_link($tag->term_id);
		
		} elseif ($wp_query->is_day && $haspost) {
			$link = get_day_link(get_query_var('year'),
								 get_query_var('monthnum'),
								 get_query_var('day'));
		
		} elseif ($wp_query->is_month && $haspost) {
			$link = get_month_link(get_query_var('year'),
								   get_query_var('monthnum'));
		
		} elseif ($wp_query->is_year && $haspost) {
			$link = get_year_link(get_query_var('year'));
		
		} elseif ($wp_query->is_home) {
			if ((get_option('show_on_front') == 'page') && ($pageid = get_option('page_for_posts')))
				$link = trailingslashit(get_permalink($pageid));
			else
				$link = trailingslashit(get_option('home'));
		} elseif ($wp_query->is_search) {
		
		} else
			return false;
		
		//Handle pagination
		$page = get_query_var('paged');
		if ($page && $page > 1) {
			if ($wp_rewrite->using_permalinks()) {
				$link = trailingslashit($link) ."page/$page";
				$link = user_trailingslashit($link, 'paged');
			} else {
				$link = add_query_arg( 'paged', $page, $link );
			}
		}
		
		return $link;
	}
	
	function admin_help() {
		return __(<<<STR
<p>The Canonicalizer helps you avoid duplicate content penalties on your website.</p>
<p>The <strong>Generate <code>&lt;link rel=&quot;canonical&quot; /&gt;</code> tags</strong> option, when enabled, 
will insert code that points Google to the correct URL for your homepage and each of your posts, Pages, categories, tags, date archives, and author archives. 
That way, if Google comes across an alternate URL by which one of those items can be accessed, it will be able to find the correct URL 
and won&#8217;t penalize you for having two identical pages on your site.</p>
STR
, 'seo-ultimate');
	}
}

}
?>