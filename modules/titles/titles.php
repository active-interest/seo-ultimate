<?php
/**
 * Title Rewriter Module
 * 
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Titles extends SU_Module {
	
	function get_module_title() { return __('Title Rewriter', 'seo-ultimate'); }
	
	function init() {
		add_action('template_redirect', array(&$this, 'before_header'), 0);
		add_action('wp_head', array(&$this, 'after_header'), 1000);
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 10);
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
	
	function admin_page_formats_tab() {
		echo "<table class='form-table'>\n";
		$this->textboxes($this->get_supported_settings(), $this->get_default_settings());
		echo "</table>";
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
		return (!is_feed() && strlen(strval($this->get_title_format())) > 0);
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
		
		//Custom taxonomy title?
		if ((is_category() || is_tag() || is_tax()) && $tax_title = $this->get_taxonomy_title('', $wp_query->get_queried_object_id()))
			return htmlspecialchars($tax_title);
		
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
			, '{url_words}' => $this->get_url_words($_SERVER['REQUEST_URI'])
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
	
	function get_url_words($url) {
		
		//Remove any extensions (.html, .php, etc)
		$url = preg_replace('|\\.[a-zA-Z]{1,4}$|', ' ', $url);
		
		//Turn slashes to >>
		$url = str_replace('/', ' &raquo; ', $url);
		
		//Remove word separators
		$url = str_replace(array('.', '/', '-'), ' ', $url);
		
		//Capitalize the first letter of every word
		$url = explode(' ', $url);
		$url = array_map('trim', $url);
		$url = array_map('ucwords', $url);
		$url = implode(' ', $url);
		$url = trim($url);
		
		return $url;
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
	
	function get_taxonomy_title($value, $key) {
		return $this->get_title_from_settings('taxonomy', $value, $key);
	}
	
	function save_taxonomy_title($unused, $value, $key) {
		return $this->save_title_to_settings('taxonomy', $value, $key);
	}
	
	function get_title_from_settings($type, $value, $key) {
		if (is_int($key))
			$id = $key;
		else
			$id = $this->get_id_from_settings_key($key);
		
		if ($id) {
			$titles = $this->get_setting($type.'_titles', array());
			return $titles[$id];
		}
		
		return $value;
	}
	
	function save_title_to_settings($type, $value, $key) {
		if (is_int($key))
			$id = $key;
		else
			$id = $this->get_id_from_settings_key($key);
		
		if ($id) {
			$titles = $this->get_setting($type.'_titles', array());
			$titles[$id] = $value;
			$this->update_setting($type.'_titles', $titles);
		}
		
		return false;
	}
	
	function title_editing_table($object_type, $function, $args, $func_set = 'singular',
			$id_varname = 'ID', $title_varname = 'post_title', $edit_link_function = 'get_edit_post_link') {
		
		$mk = $this->get_module_key();
		
		add_filter("su_get_setting-$mk", array(&$this, "get_{$func_set}_title"), 10, 2);
		add_filter("su_custom_update_setting-$mk", array(&$this, "save_{$func_set}_title"), 10, 3);
		
		$headers = array( __('ID'), __('Name'), __('Title Tag', 'seo-ultimate') );
		
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
		//$args = "$num_varname=1000";
		$args = (array)$args;
		foreach ($args as $arg_key => $arg) {
			if (is_string($arg))
				$args[$arg_key] = sprintf($arg, 1000, 0);
		}
		
		$objects = call_user_func_array($function, $args);
		//$pagination_total = ceil(count($function()) / 2);
		
		foreach ($objects as $object) {
			$id = $object->$id_varname;
			$editlink = call_user_func($edit_link_function, $id, $object_type);			
			$title = $object->$title_varname;
			
			if ($editlink) $label = "<a href='$editlink' target='_blank'>$title</a>"; else $label = $title;
			$this->textbox("{$object_type}_{$id}_title", $label);
		}
		
		echo "\t</tbody>\n</table>\n";
	
	}
	
	function get_object_subtype_tabs($type, $keys, $labels, $callback) {
		
		$labels = apply_filters("su_{$type}_tabs", $labels);
		
		$types = array();
		foreach ($keys as $key) {
			
			$label = $labels[$key];
			
			if (!$label) {
				//Rudimentary English pluralization; would turn "post" to "Posts"
				//Can be internationalized later on
				$label = ucwords($key);
				if (sustr::endswith($label, 's'))
					$label .= 'es';
				else
					$label .= 's';
				
				$label = __($label, 'seo-ultimate');
			}
			
			$types[$key] = $label;
		}
		
		$tabs = array();
		foreach ($types as $key => $label) {
			$tabs[$label] = array($callback, $key);
		}
		
		return $tabs;
	}
	
	function postmeta_fields($fields) {
		$fields['10|title'] = $this->get_postmeta_textbox('title', __('Title Tag:', 'seo-ultimate'));
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __("<strong>Title Tag</strong> &mdash; The exact contents of the &lt;title&gt; tag. The title appears in visitors' title bars and in search engine result titles. ".
			"If this box is left blank, then the <a href='admin.php?page=su-titles' target='_blank'>default post/page titles</a> are used.", 'seo-ultimate');
		return $help;
	}
}

}
?>