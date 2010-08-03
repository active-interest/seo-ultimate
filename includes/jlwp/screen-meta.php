<?php
/********** DROPDOWN CODE **********/

//Special thanks to the Drafts Dropdown plugin for the abstracted code
//http://alexking.org/projects/wordpress

if (!function_exists('screen_meta_html')) {

function screen_meta_html($meta) {
	extract($meta);
	if (function_exists($content)) {
		$content = $content();
	}
	echo '
<div id="screen-meta-'.$key.'-wrap" class="screen-meta-wrap hidden">
	<div class="screen-meta-content">'.$content.'</div>
</div>
<div id="screen-meta-'.$key.'-link-wrap" class="hide-if-no-js screen-meta-toggle cf">
<a href="#screen-meta-'.$key.'-wrap" id="screen-meta-'.$key.'-link" class="show-settings">'.$label.'</a>
</div>
	';
}

}

if (!function_exists('screen_meta_output')) {

function screen_meta_output() {
	global $screen_meta;
/*
expected format:
$screen_meta = array(
	array(
		'key' => 'drafts',
		'label' => 'Drafts',
		'content' => 'screen_meta_drafts_content' // can be content or function name
	)
);
*/
	if (!$screen_meta) $screen_meta = array();
	$screen_meta = apply_filters('screen_meta', $screen_meta);
	if (!$screen_meta) return;
	echo '<div id="screen-meta-extra-content">';
	foreach ($screen_meta as $meta) {
		screen_meta_html($meta);
	}
	echo '</div>';
?>
<style type="text/css">
.screen-meta-toggle {
	float: right;
<?php if (version_compare($wp_version, '3.0', '<')) { ?>
	background: transparent url( <?php bloginfo('wpurl'); ?>/wp-admin/images/screen-options-left.gif ) no-repeat 0 0;
<?php } else { ?>
	background: #e3e3e3;
<?php } ?>
	font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
	height: 22px;
	padding: 0;
	margin: 0 6px 0 0;
	
	-moz-border-radius-bottomleft: 3px;
	-moz-border-radius-bottomright: 3px;
	-webkit-border-bottom-left-radius: 3px;
	-webkit-border-bottom-right-radius: 3px;
}

.screen-meta-wrap h5 {
	margin: 8px 0;
	font-size: 13px;
}
.screen-meta-wrap {
	border-style: none solid solid;
	border-top: 0 none;
	border-width: 0 1px 1px;
	margin: 0 15px;
	padding: 8px 12px 12px;
	-moz-border-radius: 0 0 0 4px;
	-webkit-border-bottom-left-radius: 4px;
	-khtml-border-bottom-left-radius: 4px;
	border-bottom-left-radius: 4px;
}
</style>
<script type="text/javascript">
jQuery(function($) {

// These hacks not needed if adopted into core
// move tabs into place
	$('#screen-meta-extra-content .screen-meta-toggle.cf').each(function() {
		$('#screen-meta-links').append($(this));
	});
// Move content into place
	$('#screen-meta-extra-content .screen-meta-wrap').each(function() {
		$('#screen-meta-links').before($(this));
	});
// end hacks

// simplified generic code to handle all screen meta tabs
	$('#screen-meta-links a.show-settings').unbind().click(function() {
		var link = $(this);
		$(link.attr('href')).slideToggle('fast', function() {
			if (link.hasClass('screen-meta-shown')) {
				link.css({'backgroundImage':'url("images/screen-options-right.gif")'}).removeClass('screen-meta-shown');
				$('.screen-meta-toggle').css('visibility', 'visible');
			}
			else {
				$('.screen-meta-toggle').css('visibility', 'hidden');
				link.css({'backgroundImage':'url("images/screen-options-right-up.gif")'}).addClass('screen-meta-shown').parent().css('visibility', 'visible');
			}
		});
		return false;
	});
	
	var copy = $('#contextual-help-wrap');
	$('.screen-meta-wrap').css({
		'background-color': copy.css('background-color'),
		'border-color': copy.css('border-bottom-color')
	});
	
});
</script>

<?php
}
add_action('admin_footer', 'screen_meta_output');

}
?>