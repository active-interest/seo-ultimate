<?php
/**
 * Meta Editor Module
 * 
 * @version 1.0.3
 * @since 0.3
 */

if (class_exists('SU_Module')) {

class SU_Meta extends SU_Module {

	function get_menu_title() { return __('Meta Editor', 'seo-ultimate'); }
	
	function init() {
		add_filter('su_meta_robots', array($this, 'meta_robots'));
		add_action('su_head', array($this, 'head_tag_output'));
		add_filter('su_postmeta_help', array($this, 'postmeta_help'), 20);
	}
	
	function get_default_settings() {
		return array(
			'home_description_tagline_default' => true
		);
	}
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->textareas(array(
			  'home_description' => __("Blog Homepage Meta Description", 'seo-ultimate')
			, 'home_keywords' => __("Blog Homepage Meta Keywords", 'seo-ultimate')
		), 3);
		$this->checkboxes(array(
				  'home_description_tagline_default' => __("Use this blog&#8217s tagline as the default homepage description.", 'seo-ultimate')
			), __("Default Values", 'seo-ultimate'));
		$this->checkboxes(array(
				  'noodp' => __("Don&#8217t use this site&#8217s Open Directory description in search results.", 'seo-ultimate')
				, 'noydir' => __("Don&#8217t use this site&#8217s Yahoo! Directory description in search results.", 'seo-ultimate')
				, 'noarchive' => __("Don&#8217t cache or archive this site.", 'seo-ultimate')
			), __("Spider Instructions", 'seo-ultimate'));
		$this->textboxes(array(
				  'google_verify' => __("Google Webmaster Tools:", 'seo-ultimate')
				, 'yahoo_verify' => __("Yahoo! Site Explorer:", 'seo-ultimate')
				, 'microsoft_verify' => __("Bing Webmaster Center:", 'seo-ultimate')
			), array(), __("Verification Codes", 'seo-ultimate'));
		$this->textarea('custom_html', __("Custom &lt;head&gt; HTML", 'seo-ultimate'));
		$this->admin_form_end();
	}
	
	function postmeta_fields($fields) {
		$id = "_su_description";
		$value = attribute_escape($this->get_postmeta('description'));
		
		$fields['20|description|keywords'] =
			  "<tr class='textarea'>\n<th scope='row'><label for='$id'>".__("Description:", 'seo-ultimate')."</label></th>\n"
			. "<td><textarea name='$id' id='$id' type='text' class='regular-text' cols='60' rows='3'"
			. " onkeyup=\"javascript:document.getElementById('su_meta_description_charcount').innerHTML = document.getElementById('_su_description').value.length\">$value</textarea>"
			. "<br />".sprintf(__("You&#8217;ve entered %s characters. Most search engines use up to 160.", 'seo-ultimate'), "<strong id='su_meta_description_charcount'>".strlen($value)."</strong>")
			. "</td>\n</tr>\n"
			. $this->get_postmeta_textbox('keywords', __('Keywords:<br /><em>(separate with commas)</em>', 'seo-ultimate'))
		;
		
		return $fields;
	}
	
	//Add the appropriate commands to the meta robots array
	function meta_robots($commands) {
		
		$tags = array('noodp', 'noydir', 'noarchive');
		
		foreach ($tags as $tag) {
			if ($this->get_setting($tag)) $commands[] = $tag;
		}
		
		return $commands;
	}
	
	function head_tag_output() {
		
		$desc = false;
		$kw = false;
		
		//If we're viewing the homepage, look for homepage meta data.
		if (is_home()) {
			$desc = $this->get_setting('home_description');
			if (!$desc && $this->get_setting('home_description_tagline_default')) $desc = get_bloginfo('description');
			$kw = $this->get_setting('home_keywords');
		
		//If we're viewing a post or page, look for its meta data.
		} elseif (is_singular()) {
			$desc = $this->get_postmeta('description');
			$kw = $this->get_postmeta('keywords');	
		}
		
		//Do we have a description? If so, output it.
		if ($desc) {
			$desc = su_esc_attr($desc);
			echo "\t<meta name=\"description\" content=\"$desc\" />\n";
		}
		
		//Do we have keywords? If so, output them.
		if ($kw) {
			$kw = su_esc_attr($kw);
			echo "\t<meta name=\"keywords\" content=\"$kw\" />\n";
		}
		
		//Supported meta tags and their names
		$verify = array(
			  'google' => 'verify-v1'
			, 'yahoo' => 'y_key'
			, 'microsoft' => 'msvalidate.01'
		);
		
		//Do we have verification tags? If so, output them.
		foreach ($verify as $site => $name) {
			if ($value = $this->get_setting($site.'_verify')) {
				$value = su_esc_attr($value);
				echo "\t<meta name=\"$name\" content=\"$value\" />\n";
			}
		}
		
		//Display custom code if provided
		if ($custom = $this->get_setting('custom_html')) {
			
			//Does the plugin user want us to surround code insertions with comments? If so, mark the custom code as such.
			$mark_code = $this->get_setting('mark_code', false, 'settings');
			$desc = __('Custom Header Code', 'seo-ultimate');
			
			echo "\n";
			if ($mark_code) echo "\t<!-- $desc -->\n";
			echo $custom;
			if ($mark_code) echo "\n\t<!-- /$desc -->";
			echo "\n\n";
		}
		
	}
	
	function admin_dropdowns() {
		return array(
			  'overview' => __('Overview', 'seo-ultimate')
			, 'settings' => __('Settings Help', 'seo-ultimate')
		);
	}
	
	function admin_dropdown_overview() {
		return __("
<ul>
	<li><p><strong>What it does:</strong> Meta Editor lets you customize a wide variety of settings known as &#8220;meta data.&#8221;</p></li>
	<li><p><strong>Why it helps:</strong> Using meta data, you can convey information to search engines, such as what text you want displayed by your site in search results, what your site is about, whether they can cache your site, etc.</p></li>
	<li><p><strong>How to use it:</strong> Adjust the settings as desired, and then click Save Changes. You can refer to the &#8220;Settings Help&#8221; tab for information on the settings available. You can also customize the meta data of an individual post or page by using the textboxes that Meta Editor adds to the post/page editors.</p></li>
</ul>
", 'seo-ultimate');
	}
	
	function admin_dropdown_settings() {
		return __("
<p>Here&#8217;s information on the various settings:</p>
<ul>
	<li><p><strong>Blog Homepage Meta Description</strong> &mdash; When your blog homepage appears in search results, it&#8217;ll have a title and a description. 
		When you insert content into the description field below, the Meta Editor will add code to your blog homepage (the <code>&lt;meta&nbsp;name=&quot;description&quot;&nbsp;/&gt;</code> tag)
		that asks search engines to use what you&#8217;ve entered as the homepage&#8217;s search results description.</p></li>
	<li><p><strong>Blog Homepage Meta Keywords</strong> &mdash; Here you can enter keywords that describe the overall subject matter of your entire blog. Use commas to separate keywords. 
		Your keywords will be put in the <code>&lt;meta&nbsp;name=&quot;keywords&quot;&nbsp;/&gt;</code> tag on your blog homepage.</p></li>
	<li><p><strong>Default Values</strong></p>
		<ul>
			<li><p><strong>Use this blog&#8217;s tagline as the default homepage description.</strong> &mdash; 
				If this box is checked and if the Blog Homepage Meta Description field is empty, 
				Meta Editor will use your blog&#8217;s <a href="options-general.php" target="_blank">tagline</a> as the meta description.</p></li>
		</ul>
	</li>
	<li><p><strong>Spider Instructions</strong></p>
		<ul>
			<li><p><strong>Don&#8217;t use this site&#8217;s Open Directory / Yahoo! Directory description in search results.</strong> &mdash; 
				If your site is listed in the <a href='http://www.dmoz.org/' target='_blank'>Open Directory (DMOZ)</a> or 
				the <a href='http://dir.yahoo.com/' target='_blank'>Yahoo! Directory</a>, 
				some search engines may use your directory listing as the meta description. 
				These boxes tell search engines not to do that and will give you full control over your meta descriptions. 
				These settings have no effect if your site isn&#8217;t listed in the Open Directory or Yahoo! Directory respectively.</p></li>
			<li><p><strong>Don&#8217;t cache or archive this site.</strong> &mdash; 
				When you check this box, Meta Editor will ask search engines (Google, Yahoo!, Bing, etc.) and archivers (Archive.org, etc.) 
				to <em>not</em> make cached or archived &#8220;copies&#8221; of your site.</p></li>
		</ul>
	</li>
	<li><p><strong>Verification Codes</strong> &mdash; This section lets you enter in verification codes for the webmaster portals of the 3 leading search engines.</p></li>
	<li><p><strong>Custom &lt;head&gt; HTML</strong> &mdash; Just enter in raw HTML code here, and it&#8217;ll be entered into the &lt;head&gt; tag across your entire site.</p></li>
</ul>
", 'seo-ultimate');
	}
	
	function postmeta_help($help) {
		$help[] = __("<strong>Description:</strong> &mdash; The value of the meta description tag. The description will often appear underneath the title in search engine results. ".
			"Writing an accurate, attention-grabbing description for every post is important to ensuring a good search results clickthrough rate.", 'seo-ultimate');
		$help[] = __("<strong>Keywords:</strong> &mdash; The value of the meta keywords tag. The keywords list gives search engines a hint as to what this post/page is about. ".
			"Be sure to separate keywords with commas, like so: <samp>one,two,three</samp>.", 'seo-ultimate');
		return $help;
	}

}

}
?>