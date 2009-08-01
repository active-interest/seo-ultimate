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
		//If the canonical tags are enabled, then hook them into the front-end header.
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
		//Display the canonical tag if a canonical URL is available
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
		
		//404s and search results don't have canonical URLs
		if ($wp_query->is_404 || $wp_query->is_search) return false;
		
		//Are there posts in the current Loop?
		$haspost = count($wp_query->posts) > 0;
		
		//Handling special case with '?m=yyyymmddHHMMSS'.
		if (get_query_var('m')) {
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
					//Since there is no code for producing canonical archive links for is_time, we will give up and not try to produce a link.
					return false;
			}
		
		//Posts and pages
		} elseif (($wp_query->is_single || $wp_query->is_page) && $haspost) {
			$post = $wp_query->posts[0];
			$link = get_permalink($post->ID);
			if (is_front_page()) $link = trailingslashit($link);
			
		//Author archives
		} elseif ($wp_query->is_author && $haspost) {
			$author = get_userdata(get_query_var('author'));
			if ($author === false) return false;
			$link = get_author_link(false, $author->ID, $author->user_nicename);
			
		//Category archives
		} elseif ($wp_query->is_category && $haspost) {
			$link = get_category_link(get_query_var('cat'));
			
		//Tag archives
		} else if ($wp_query->is_tag  && $haspost) {
			$tag = get_term_by('slug',get_query_var('tag'),'post_tag');
			if (!empty($tag->term_id)) $link = get_tag_link($tag->term_id);
		
		//Day archives
		} elseif ($wp_query->is_day && $haspost) {
			$link = get_day_link(get_query_var('year'),
								 get_query_var('monthnum'),
								 get_query_var('day'));
		
		//Month archives
		} elseif ($wp_query->is_month && $haspost) {
			$link = get_month_link(get_query_var('year'),
								   get_query_var('monthnum'));
		
		//Year archives
		} elseif ($wp_query->is_year && $haspost) {
			$link = get_year_link(get_query_var('year'));
		
		//Homepage
		} elseif ($wp_query->is_home) {
			if ((get_option('show_on_front') == 'page') && ($pageid = get_option('page_for_posts')))
				$link = trailingslashit(get_permalink($pageid));
			else
				$link = trailingslashit(get_option('home'));
			
		//Other
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
		
		//Return the canonical URL
		return $link;
	}
	
	function admin_dropdowns() {
		return array(
			  'overview' => __('Overview', 'seo-ultimate')
		);
	}
	
	function admin_dropdown_overview() {
		return __("
<ul>
	<li><p><strong>What it does:</strong> Canonicalizer inserts <code>&lt;link rel=&quot;canonical&quot; /&gt;</code> tags to minimize possible exact-content duplication penalties.</p></li>
	<li><p><strong>Why it helps:</strong> These tags will point Google to the correct URL for your homepage and each of your posts, Pages, categories, tags, date archives, and author archives. 
That way, if Google comes across an alternate URL by which one of those items can be accessed, it will be able to find the correct URL 
and won&#8217;t penalize you for having two identical pages on your site.</p></li>
	<li><p><strong>How to use it:</strong> Just check the checkbox and click Save Changes. SEO Ultimate will do the rest.</p></li>
</ul>
", 'seo-ultimate');
	}
}

}
?>