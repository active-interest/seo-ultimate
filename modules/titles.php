<?php
/**
 * Title Rewriter Module
 * 
 * @version 2.0
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Titles extends SU_Module {

	function get_menu_title() { return __('Title Rewriter', 'seo-ultimate'); }
	
	function init() {
		add_action('template_redirect', array(&$this, 'before_header'), 0);
		add_action('wp_head', array(&$this, 'after_header'), 1000);
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 10);
		$this->admin_page_tabs_init();
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
		$this->admin_form_start(false, false);
		$this->admin_page_tabs(array(
			  __('Default Formats', 'seo-ultimate') => 'admin_page_formats_tab'
			, __('Posts', 'seo-ultimate') => 'admin_page_posts_tab'
			, __('Pages', 'seo-ultimate') => 'admin_page_pages_tab'
		));
		$this->admin_form_end(false, false);
	}
	
	function admin_page_formats_tab() {
		echo "<table class='form-table'>\n";
		$this->textboxes($this->get_supported_settings(), $this->get_default_settings());
		echo "</table>";
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
		if ($this->should_rewrite_title()) ob_start(array(&$this, 'change_title_tag'));
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
		$post_id = 0;
		$post_title = '';
		if (is_singular()) {
			$post = $wp_query->get_queried_object();
			$post_title = strip_tags( apply_filters( 'single_post_title', $post->post_title ) );
			$post_id = $post->ID;
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
		
		//Load category titles
		$cat_title = $cat_titles = $cat_desc = '';
		if (is_category()) {
			$cat_title = single_cat_title('', false);
			$cat_desc = category_description();
		} elseif (count($categories = get_the_category())) {
			$cat_titles = su_lang_implode($categories, 'name');
			usort($categories, '_usort_terms_by_ID');
			$cat_title = $categories[0]->name;
			$cat_desc = category_description($categories[0]->term_id);
		}
		
		//Load tag titles
		$tag_title = $tag_desc = '';
		if (is_tag()) {
			$tag_title = single_tag_title('', false);
			$tag_desc = tag_description();
		}
		
		//Load author titles
		if (is_author()) {
			$author_obj = $wp_query->get_queried_object();
		} elseif (is_singular()) {
			global $authordata;
			$author_obj = $authordata;
		} else {
			$author_obj = null;
		}
		if ($author_obj)
			$author = array(
				  'username' => $author_obj->user_login
				, 'name' => $author_obj->display_name
				, 'firstname' => get_the_author_meta('first_name', $author_obj->ID)
				, 'lastname' => get_the_author_meta('last_name',  $author_obj->ID)
				, 'nickname' => get_the_author_meta('nickname',   $author_obj->ID)
			);
		else
			$author = array();
		
		$variables = array(
			  '{blog}' => get_bloginfo('name')
			, '{tagline}' => get_bloginfo('description')
			, '{post}' => $post_title
			, '{page}' => $post_title
			, '{category}' => $cat_title
			, '{categories}' => $cat_titles
			, '{category_description}' => $cat_desc
			, '{tag}' => $tag_title
			, '{tag_description}' => $tag_desc
			, '{tags}' => su_lang_implode(get_the_tags($post_id), 'name', true)
			, '{daynum}' => $daynum
			, '{day}' => $day
			, '{monthnum}' => $monthnum
			, '{month}' => $month
			, '{year}' => $year
			, '{author}' => $author['name']
			, '{author_name}' => $author['name']
			, '{author_username}' => $author['username']
			, '{author_firstname}' => $author['firstname']
			, '{author_lastname}' => $author['lastname']
			, '{author_nickname}' => $author['nickname']
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
	
	function admin_page_posts_tab() {
		$this->title_editing_table('post', __('Post'), 'get_posts');
	}
	
	function admin_page_pages_tab() {
		$this->title_editing_table('page', __('Page'), 'get_pages');
	}
	
	function get_id_from_settings_key($key) {
		$matches = array();
		if (preg_match('/([a-z]+)_([0-9]+)_([a-z]+)/', $key, $matches))
			return (int)$matches[2];
		
		return false;
	}
	
	function get_singular_title($value, $key) {
		if ($id = $this->get_id_from_settings_key($key))
			return $this->get_postmeta('title', $id);
		
		return $value;
	}
	
	function save_singular_title($unused, $value, $key) {
		if ($id = $this->get_id_from_settings_key($key)) {
			update_post_meta($id, '_su_title', $value);
			return true;
		}
		
		return false;
	}
	
	function title_editing_table($object_type, $object_type_label, $function,
			$get_value_callback = 'get_singular_title', $save_value_callback = 'save_singular_title',
			$num_varname = 'numberposts', $offset_varname = 'offset', $id_varname = 'ID', $title_varname = 'post_title', $edit_link_function = 'get_edit_post_link') {
	
		$mk = $this->get_module_key();
	
		add_filter("su_get_setting-$mk", array(&$this, $get_value_callback), 10, 2);
		add_filter("su_custom_update_setting-$mk", array(&$this, $save_value_callback), 10, 3);
	
		$headers = array( __('ID'), $object_type_label, __('Title Tag', 'seo-ultimate') );
	
		echo <<<STR
<table class="widefat fullwidth" cellspacing="0">
	<thead><tr>
		<!--<th scope="col" class="$object_type-id">{$headers[0]}</th>-->
		<th scope="col" class="$object_type-title">{$headers[1]}</th>
		<th scope="col" class="$object_type-title-tag">{$headers[2]}</th>
	</tr></thead>
	<tbody>

STR;
		
		/*if (strlen($num_varname) && strlen($offset_varname))
			$args = "$num_varname=20&$offset_varname=0";
		else
			$args = '';*/
		$args = "$num_varname=1000";
		
		$objects = $function($args);
		$pagination_total = ceil(count($function()) / 2);
		
		foreach ($objects as $object) {
			$id = $object->$id_varname;
			$editlink = $edit_link_function($id);
			$title = $object->$title_varname;
			
			$this->textbox("{$object_type}_{$id}_title", "<a href='$editlink'>$title</a>");
		}
		
		echo "\t</tbody>\n</table>\n";
	
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
		You also have the option of overriding the <code>&lt;title&gt;</code> tag of an individual post or page by using the textboxes under the &#8220;Post&#8221; and &#8220;Page&#8221; tabs below, or by using the &#8220;Title Tag&#8221; textbox that Title Rewriter adds to the post/page editors.</p></li>
</ul>
", 'seo-ultimate');
	}
	
	function admin_dropdown_settings() {
		return __("
<p>Various variables, surrounded in {curly brackets}, are provided for use in the title formats.
All settings support the {blog} variable, which is replaced with the name of the blog, 
and the {tagline} variable, which is replaced with the blog tagline as set under <a href='options-general.php' target='_blank'>General&nbsp;Settings</a>.</p>
<p>Here&#8217;s information on each of the settings and its supported variables:</p>
<ul>
	<li><p><strong>Blog Homepage Title</strong> &mdash; Displays on the main blog posts page.</p></li>
	<li><p><strong>Post Title Format</strong> &mdash; Displays on single-post pages. Supports these variables:</p>
		<ul>
			<li>{post} &mdash; The post&#8217;s title.</li>
			<li>{category} &mdash; The title of the post category with the lowest ID number.</li>
			<li>{categories} &mdash; A natural-language list of the post&#8217;s categories (e.g. &#8220;Category A, Category B, and Category C&#8221;).</li>
			<li>{tags} &mdash; A natural-language list of the post&#8217;s tags (e.g. &#8220;Tag A, Tag B, and Tag C&#8221;).</li>
			<li>{author} &mdash; The Display Name of the post&#8217;s author.</li>
			<li>{author_username}, {author_firstname}, {author_lastname}, {author_nickname} &mdash; The username, first name, last name, and nickname of the post&#8217;s author, respectively, as set in his or her profile.</li>
		</ul>
	<li><p><strong>Page Title Format</strong> &mdash; Displays on WordPress Pages. The {page} variable is replaced with the Page&#8217;s title. Also supports the same author variables as the Post Title Format.</p></li>
	<li><p><strong>Category Title Format</strong> &mdash; Displays on category archives. The {category} variable is replaced with the name of the category, and {category_description} is replaced with its description.</p></li>
	<li><p><strong>Tag Title Format</strong> &mdash; Displays on tag archives. The {tag} variable is replaced with the name of the tag, and {tag_description} is replaced with its description.</p></li>
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
	<li><p><strong>Author Archive Title Format</strong> &mdash; Displays on author archives. Supports the same author variables as the Post Title Format box, 
		i.e. {author}, {author_username}, {author_firstname}, {author_lastname}, and {author_nickname}.</p></li>
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

} elseif ($_GET['css'] == 'admin') {
	header('Content-type: text/css');
?>

#su-titles table.widefat {
	width: auto;
}

#su-titles table.widefat td input.regular-text {
	width: 400px;
}

<?php
}
?>