<?php
/**
 * Title Rewriter Module
 * 
 * @version 1.0.5
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Titles extends SU_Module {

	function get_menu_title() { return __('Title Rewriter', 'seo-ultimate'); }
	
	function init() {
		add_action('template_redirect', array($this, 'before_header'), 0);
		add_action('wp_head', array($this, 'after_header'), 1000);
		add_filter('su_postmeta_help', array($this, 'postmeta_help'), 10);
	}
	
	function get_default_settings() {
	
		//We internationalize even non-text formats (like "{post} | {blog}") to allow RTL languages to switch the order of the variables
		return array(
			  'title_home' => __('{blog}', 'seo-ultimate')
			, 'title_single' => __('{post} | {blog}', 'seo-ultimate')
			, 'title_page' => __('{page} | {blog}', 'seo-ultimate')
			, 'title_category' => __('{category} | {blog}', 'seo-ultimate')
			, 'title_tag' => __('{tag} | {blog}', 'seo-ultimate')
			, 'title_day' => __('Archives for {month} {day}, {year} | {blog}', 'seo-ultimate')
			, 'title_month' => __('Archives for {month} {year} | {blog}', 'seo-ultimate')
			, 'title_year' => __('Archives for {year} | {blog}', 'seo-ultimate')
			, 'title_author' => __('Posts by {author} | {blog}', 'seo-ultimate')
			, 'title_search' => __('Search Results for {query} | {blog}', 'seo-ultimate')
			, 'title_404' => __('404 Not Found | {blog}', 'seo-ultimate')
			, 'title_paged' => __('{title} - Page {num}', 'seo-ultimate')
		);
	}
	
	function get_supported_settings() {
		return array(
			  'title_home' => __('Blog Homepage Title', 'seo-ultimate')
			, 'title_single' => __('Post Title Format', 'seo-ultimate')
			, 'title_page' => __('Page Title Format', 'seo-ultimate')
			, 'title_category' => __('Category Title Format', 'seo-ultimate')
			, 'title_tag' => __('Tag Title Format', 'seo-ultimate')
			, 'title_day' => __('Day Archive Title Format', 'seo-ultimate')
			, 'title_month' => __('Month Archive Title Format', 'seo-ultimate')
			, 'title_year' => __('Year Archive Title Format', 'seo-ultimate')
			, 'title_author' => __('Author Archive Title Format', 'seo-ultimate')
			, 'title_search' => __('Search Title Format', 'seo-ultimate')
			, 'title_404' => __('404 Title Format', 'seo-ultimate')
			, 'title_paged' => __('Pagination Title Format', 'seo-ultimate')
		);
	}
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->textboxes($this->get_supported_settings(), $this->get_default_settings());
		$this->admin_form_end();
	}
	
	function postmeta_fields($fields) {
		$fields['10|title'] = $this->get_postmeta_textbox('title', __('Title Tag:', 'seo-ultimate'));
		return $fields;
	}
	
	function get_title_format() {
		if ($key = $this->get_current_page_type())
			return $this->get_setting("title_$key");
		
		return false;
	}
	
	function get_current_page_type() {
		$pagetypes = $this->get_supported_settings();
		unset($pagetypes['title_paged']);
		
		foreach ($pagetypes as $key => $title) {
			$key = str_replace('title_', '', $key);
			if (call_user_func("is_$key")) return $key;
		}
		
		return false;
	}
	
	function should_rewrite_title() {
		return (strlen(strval($this->get_title_format())) > 0);
	}
	
	function before_header() {
		if ($this->should_rewrite_title()) ob_start(array($this, 'change_title_tag'));
	}

	function after_header() {
		if ($this->should_rewrite_title()) {
			
			$handlers = ob_list_handlers();
			if (count($handlers) > 0 && strcasecmp($handlers[count($handlers)-1], 'SU_Titles::change_title_tag') == 0)
				ob_end_flush();
			else
				su_debug_log(__FILE__, __CLASS__, __FUNCTION__, __LINE__, "Other ob_list_handlers found:\n".print_r($handlers, true));
		}
	}
	
	function change_title_tag($head) {
		
		$title = $this->get_title();
		if (!$title) return $head;
		
		//Replace the old title with the new and return
		return eregi_replace('<title>[^<]+</title>', '<title>'.$title.'</title>', $head);
	}
	
	function get_title() {
		if (!$this->should_rewrite_title()) return '';
		
		global $wp_query, $wp_locale;
		
		$format = $this->get_title_format();
		
		//Custom post/page title?
		if ($post_title = $this->get_postmeta('title'))
			return htmlspecialchars($this->get_title_paged($post_title));
		
		//Load post/page titles
		$post_title = '';
		if (is_singular()) {
			$post = $wp_query->get_queried_object();
			$post_title = strip_tags( apply_filters( 'single_post_title', $post->post_title ) );
		}
		
		//Load date-based archive titles
		if ($m = get_query_var('m')) {
			$year = substr($m, 0, 4);
			$monthnum = intval(substr($m, 4, 2));
			$daynum = intval(substr($m, 6, 2));
		} else {
			$year = get_query_var('year');
			$monthnum = get_query_var('monthnum');
			$daynum = get_query_var('day');
		}
		$month = $wp_locale->get_month($monthnum);
		$monthnum = zeroise($monthnum, 2);
		$day = date('jS', mktime(12,0,0,$monthnum,$daynum,$year));
		$daynum = zeroise($daynum, 2);
		
		//Load author archive titles
		$author_name = '';
		if (is_author()) {
			$author = $wp_query->get_queried_object();
			$author_name = $author->display_name;
		}
		
		$variables = array(
			  '{blog}' => get_bloginfo('name')
			, '{post}' => $post_title
			, '{page}' => $post_title
			, '{category}' => single_cat_title('', false)
			, '{tag}' => single_tag_title('', false)
			, '{daynum}' => $daynum
			, '{day}' => $day
			, '{monthnum}' => $monthnum
			, '{month}' => $month
			, '{year}' => $year
			, '{author}' => $author_name
			, '{query}' => attribute_escape(get_search_query())
			, '{ucquery}' => attribute_escape(ucwords(get_search_query()))
		);
		
		$title = str_replace(array_keys($variables), array_values($variables), htmlspecialchars($format));
		
		return $this->get_title_paged($title);
	}
	
	function get_title_paged($title) {
		
		global $wp_query, $numpages;
		
		if (is_paged() || get_query_var('page')) {
			
			if (is_paged()) {
				$num = absint(get_query_var('paged'));
				$max = absint($wp_query->max_num_pages);
			} else {
				$num = absint(get_query_var('page'));
				
				if (is_singular()) {
					$post = $wp_query->get_queried_object();
					$max = count(explode('<!--nextpage-->', $post->post_content));
				} else
					$max = '';
			}
			
			return str_replace(
				array('{title}', '{num}', '{max}'),
				array( $title, $num, $max ),
				$this->get_setting('title_paged'));
		} else
			return $title;
	}
	
	function admin_dropdowns() {
		return array(
			  'overview' => __('Overview', 'seo-ultimate')
			, 'settings' => __('Settings & Variables', 'seo-ultimate')
		);
	}
	
	function admin_dropdown_overview() {
		return __("
<ul>
	<li><p><strong>What it does:</strong> Title Rewriter helps you customize the contents of your website&#8217;s <code>&lt;title&gt;</code> tags.
		The tag contents are displayed in web browser title bars and in search engine result pages.</p></li>
	<li><p><strong>Why it helps:</strong> Proper title rewriting ensures that the keywords in your post/Page titles have greater prominence for search engine spiders and users.
		This is an important foundation for WordPress SEO.</p></li>
	<li><p><strong>How to use it:</strong> Title Rewriter enables recommended settings automatically, so you shouldn&#8217;t need to change anything.
		If you do wish to edit the rewriting formats, you can do so using the textboxes below (the &#8220;Settings Help&#8221; tab includes additional information on this).
		You also have the option of overriding the <code>&lt;title&gt;</code> tag of an individual post or page by using the &#8220;Title Tag&#8221; textbox that Title Rewriter adds to the post/page editors.</p></li>
</ul>
", 'seo-ultimate');
	}
	
	function admin_dropdown_settings() {
		return __("
<p>Various variables, surrounded in {curly brackets}, are provided for use in the title formats.
All settings support the {blog} variable, which is replaced with the name of the blog.</p>
<p>Here&#8217;s information on each of the settings and its supported variables:</p>
<ul>
	<li><p><strong>Blog Homepage Title</strong> &mdash; Displays on the main blog posts page.</p></li>
	<li><p><strong>Post Title Format</strong> &mdash; Displays on single-post pages. The {post} variable is replaced with the post&#8217;s title.</p></li>
	<li><p><strong>Page Title Format</strong> &mdash; Displays on WordPress Pages. The {page} variable is replaced with the Page&#8217;s title.</p></li>
	<li><p><strong>Category Title Format</strong> &mdash; Displays on category archives. The {category} variable is replaced with the name of the category.</p></li>
	<li><p><strong>Tag Title Format</strong> &mdash; Displays on tag archives. The {tag} variable is replaced with the name of the tag.</p></li>
	<li><p><strong>Day Archive Title Format</strong> &mdash; Displays on day archives. Supports these variables:</p>
		<ul>
			<li>{day} &mdash; The day number, with ordinal suffix, e.g. 23rd</li>
			<li>{daynum} &mdash; The two-digit day number, e.g. 23</li>
			<li>{month} &mdash; The name of the month, e.g. April</li>
			<li>{monthnum} &mdash; The two-digit number of the month, e.g. 04</li>
			<li>{year} &mdash; The year, e.g. 2009</li>
		</ul></li>
	<li><p><strong>Month Archive Title Format</strong> &mdash; Displays on month archives. Supports {month}, {monthnum}, and {year}.</p></li>
	<li><p><strong>Year Archive Title Format</strong> &mdash; Displays on year archives. Supports the {year} variable.</p></li>
	<li><p><strong>Author Archive Title Format</strong> &mdash; Displays on author archives. The {author} variable is replaced with the author&#8217;s Display Name.</p></li>
	<li><p><strong>Search Title Format</strong> &mdash; Displays on the result pages for WordPress&#8217;s blog search function.
		The {query} variable is replaced with the search query as-is. The {ucwords} variable returns the search query with the first letter of each word capitalized.</p></li>
	<li><p><strong>404 Title Format</strong> &mdash; Displays whenever a URL doesn&#8217;t go anywhere.</p></li>
	<li><p><strong>Pagination Title Format</strong> &mdash; Displays whenever the visitor is on a subpage (page 2, page 3, etc). Supports these variables:</p>
		<ul>
			<li>{title} &mdash; The title that would normally be displayed on page 1.</li>
			<li>{num} &mdash; The current page number (2, 3, etc).</li>
			<li>{max} &mdash; The total number of subpages available. Would usually be used like this: Page {num} of {max}</li>
		</ul></li>
</ul>
", 'seo-ultimate');
	}
	
	function postmeta_help($help) {
		$help[] = __("<strong>Title Tag</strong> &mdash; The exact contents of the &lt;title&gt; tag. The title appears in visitors' title bars and in search engine result titles. ".
			"If this box is left blank, then the <a href='admin.php?page=titles' target='_blank'>default post/page titles</a> are used.", 'seo-ultimate');
		return $help;
	}

}

}
?>