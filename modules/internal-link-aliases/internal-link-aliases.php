<?php

class SU_InternalLinkAliases extends SU_Module {
	
	function get_default_settings() {
		return array(
			  'alias_dir' => 'go'
		);
	}
	
	function init() {
		add_filter('su_custom_update_postmeta-aliases', array(&$this, 'save_post_aliases'), 10, 4);
		add_filter('the_content', array(&$this, 'apply_aliases'), 9); //Run before wp_texturize etc.
		add_action('template_redirect', array(&$this, 'redirect_aliases'), 0);
		add_action('do_robotstxt', array(&$this, 'block_aliases_dir'));
		add_action('su_do_robotstxt', array(&$this, 'block_aliases_dir'));
		add_filter('su_get_setting-internal-link-aliases-alias_dir', array(&$this, 'filter_alias_dir'));
	}
	
	function get_module_title() { return __('Link Mask Generator', 'seo-ultimate'); }
	function get_menu_title() { return false; }
	
	function filter_alias_dir($alias_dir) {
		return trim(sustr::preg_filter('a-zA-Z0-9_/', $alias_dir), '/');
	}
	
	function postmeta_fields($fields) {
		
		$post = get_post(suwp::get_post_id());
		if (!$post) return;
		$content = $post->post_content;
		
		$alias_dir = $this->get_setting('alias_dir', 'go');
		
		if ($content && preg_match_all('@ href=["\']([^"\']+)["\']@', $content, $matches)) {
			$urls = $matches[1];
			
			$html = "<tr valign='top'>\n<th scope='row'>".__('Link Masks', 'seo-ultimate')."</th>\n<td>\n";
			
			$html .= "<table class='widefat'><thead>\n";
			$headers = array(__('URL', 'seo-ultimate'), '', __('Mask URL', 'seo-ultimate'));
			foreach ($headers as $header) $html .= "<th>$header</th>\n";
			$html .= "</thead>\n<tbody>";
			
			$aliases = $this->get_setting('aliases', array());
			$post_aliases = array();
			foreach ($aliases as $alias) {
				if (in_array($post->ID, $alias['posts']))
					$post_aliases[$alias['from']] = $alias['to'];
			}
			
			foreach ($urls as $url) {
				$a_url = esc_attr($url);
				$ht_url = esc_html(sustr::truncate($url, 30));
				$a_alias = esc_attr($post_aliases[$url]);
				$html .= "<tr><td><a href='$a_url' title='$a_url' target='_blank'>$ht_url</a></td>\n<td>&rArr;</td><td>/$alias_dir/<input type='text' name='_su_aliases[$a_url]' value='$a_alias' /></td></tr>\n";
			}
			
			$html .= "</tbody>\n</table>\n";
			
			$html .= '<p><small>' . __('You can stop search engines from following a link by typing in a mask for its URL.', 'seo-ultimate') . "</small></p>\n";
			
			$html .= "</td>\n</tr>\n";
			
			$fields['70|aliases'] = $html;
		}
		
		return $fields;
	}
	
	function save_post_aliases($false, $saved_aliases, $metakey, $post) {
		if ($post->post_type == 'revision' || !is_array($saved_aliases)) return true;
		
		$aliases = $this->get_setting('aliases', array());
		
		$posts = array($post->ID);
		foreach ($saved_aliases as $from => $to) {
			$id = md5($from . serialize($posts));
			$aliases[$id] = compact('from', 'to', 'posts');
		}
		
		$this->update_setting('aliases', $aliases);
		
		return true;
	}
	
	function apply_aliases($content) {
		$id = suwp::get_post_id();
		$aliases = $this->get_setting('aliases', array());
		$aliases = array_reverse($aliases, true); //Just in case we have duplicate aliases, make sure the most recent ones are applied first
		$alias_dir = $this->get_setting('alias_dir', 'go');
		
		foreach ($aliases as $alias) {
			$to = $alias['to'];
			
			if (in_array($id, $alias['posts']) && $to) {
				$from = $alias['from'];
				$to = get_bloginfo('url') . "/$alias_dir/$to/";
				$content = str_replace(array(" href='$from'", " href=\"$from\""), array(" href='$to'", " href=\"$to\""), $content);
			}
		}
		return $content;
	}
	
	function redirect_aliases() {
		$aliases = $this->get_setting('aliases', array());
		$alias_dir = $this->get_setting('alias_dir', 'go');
		
		foreach ($aliases as $alias)
			if ($to = $alias['to'])
				if (suurl::current() == get_bloginfo('url') . "/$alias_dir/$to/")
					wp_redirect($alias['from']);
	}
	
	function block_aliases_dir() {
		echo '# ';
		_e('Added by Link Alias Generator (LAG) module', 'seo-ultimate');
		echo "\n";
		
		$urlinfo = parse_url(get_bloginfo('url'));
		$path = $urlinfo['path'];
		echo "User-agent: *\n";
		
		$alias_dir = $this->get_setting('alias_dir', 'go');
		echo "Disallow: $path/$alias_dir/\n";
		
		echo '# ';
		_e('End LAG', 'seo-ultimate');
		echo "\n\n";
	}

}