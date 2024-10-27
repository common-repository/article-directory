<?php

if ( is_user_logged_in() ) {

	add_action('wp_head', 'artdir_jquery', 8);

	// убираем магические кавычки
	function stripslashes_array($array) { return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array); }
	if (get_magic_quotes_gpc()) { $_POST = stripslashes_array($_POST); }

	global $current_user;
	get_currentuserinfo();

	$options = get_option('article_directory');
	$uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$apl = get_permalink($options['author_panel_id']);
	$profile = '';
	if (get_option('permalink_structure') == '') {
		$profile = '&profile';
	} else {
		$profile = '?profile';
	}

	$authorNav  = "\r\n\r\n" . '<h2>' . get_the_title() . '</h2>' . "\r\n\r\n";
	$authorNav .= '<ul id="authorNav">';
	$authorNav .= $uri == $apl ? '<li class="current"><a href="' . $apl .'">' . __('Submit article', 'article-directory') . '</a></li>' : '<li><a href="' . $apl .'">' . __('Submit article', 'article-directory') . '</a></li>';
	$authorNav .= (strpos($uri, $apl . $profile) !== false ) ? '<li class="current"><a href="' . $apl . $profile . '">' . __('My profile', 'article-directory') . ' (' . $current_user->display_name . ')</a></li>' : '<li><a href="' . $apl . $profile . '">' . __('My profile', 'article-directory') . ' (' . $current_user->display_name . ')</a></li>';
	$authorNav .= '<li><a href="' . wp_logout_url(get_bloginfo('wpurl')) .'">' . __('Log out') . '</a></li>';
	$authorNav .= '</ul>' . "\r\n\r\n";

	// профиль
	if (isset($_GET['profile']) || isset($_GET['updated'])) {

		$error = '';

		if ($_POST['action'] == 'updateProfile') {

			require_once( ABSPATH . WPINC . '/registration.php');

			global $demo;

			if (!$demo) {
				if(isset($_POST['display_name'])) {
					wp_update_user(array('ID' => $current_user->ID, 'display_name' => $_POST['display_name']));
				}
			}

			if(empty($_POST['user_email'])) {
				$error .= '<div>' . __('<strong>ERROR</strong>: Please enter an e-mail address.') . '</div>';
			} elseif (!is_email($_POST['user_email'])) {
				$error .= '<div>' . __('<strong>ERROR</strong>: The e-mail address isn&#8217;t correct.') . '</div>';
			} else {
				if (!$demo) {
					wp_update_user(array('ID' => $current_user->ID, 'user_email' => $_POST['user_email']));
				}
			}

			if (!$demo) {
				if(isset($_POST['url'])) {
					wp_update_user(array('ID' => $current_user->ID, 'user_url' => $_POST['url']));
				}
			}

			if (!$demo) {
				if(isset($_POST['description'])) {
					wp_update_user(array('ID' => $current_user->ID, 'description' => strip_tags($_POST['description'])));
				}
			}

			if (empty($_POST['pass1']) && !empty($_POST['pass2']) or !empty($_POST['pass1']) && empty($_POST['pass2']) or !empty($_POST['pass1']) && !empty($_POST['pass2'])) {
				if ($_POST['pass1'] != $_POST['pass2']) {
					$error .= '<div>' . __('<strong>ERROR</strong>: Please enter the same password in the two password fields.') . '</div>';
				} else {
					if (!$demo) {
						wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'])));
					}
				}
			}

			if (!$error) {
				if (strpos($uri, '&updated') === false ) {
					wp_redirect($uri . '&updated');
				} else {
					wp_redirect($uri);
				}
				exit;
			} else {
				$error = $error;
			}

		}

		get_header();
		echo $authorNav;

		if (isset($_GET['updated'])) {
			if (!$error) echo '<div class="success"><strong>' . __('User updated.') . '</strong></div>';
		}
		if ($error) echo '<div class="error">' . $error . '</div>';

?>
	<form action="" method="post" id="updateProfile">

		<table class="form-table">

			<tr>
				<th><?php _e('Registration date', 'article-directory'); ?></th>
				<td><?php echo esc_attr($current_user->user_registered); ?></td>
			</tr>

			<tr>
				<th><label for="user_login"><?php _e('Username'); ?></label></th>
				<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr($current_user->user_login); ?>" disabled="disabled" class="disabled" /> <div class="description"><?php _e('Usernames cannot be changed.'); ?></div></td>
			</tr>

			<tr>
				<th><label for="display_name"><?php _e('Display name publicly as'); ?></label></th>
				<td><input type="text" name="display_name" id="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" /></td>
			</tr>

			<tr>
				<th><label for="user_email"><?php _e('E-mail'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
				<td><input type="text" name="user_email" id="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" /></td>
			</tr>

			<tr>
				<th><label for="url"><?php _e('Website'); ?></label></th>
				<td><input type="text" name="url" id="url" value="<?php echo esc_attr($current_user->user_url) ?>" /></td>
			</tr>

			<tr>
				<th><label for="description"><?php _e('About Yourself'); ?></label></th>
				<td><textarea name="description" id="description" rows="5" cols="30"><?php echo esc_html($current_user->description); ?></textarea></td>
			</tr>

			<tr id="password">
				<th><label for="pass1"><?php _e('New Password'); ?></label></th>
				<td><input type="password" name="pass1" id="pass1" value="" autocomplete="off" /> <div class="description"><?php _e("If you would like to change the password type a new one. Otherwise leave this blank."); ?></div>
					<input type="password" name="pass2" id="pass2" value="" autocomplete="off" /> <div class="description"><?php _e("Type your new password again."); ?></div>
					<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
					<div class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></div>
				</td>
			</tr>

<script type='text/javascript'>
/* <![CDATA[ */
var pwsL10n = {
	empty: "<?php echo esc_js( __( 'Strength indicator' ) ); ?>",
	short: "<?php echo esc_js( __( 'Very weak' ) ); ?>",
	bad: "<?php echo esc_js( __( 'Weak' ) ); ?>",
	good: "<?php echo esc_js( _x( 'Medium', 'password strength' ) ); ?>",
	strong: "<?php echo esc_js( __( 'Strong' ) ); ?>",
	mismatch: "<?php echo esc_js( __( 'Mismatch' ) ); ?>"
};
try{convertEntities(pwsL10n);}catch(e){};
/* ]]> */
</script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/load-scripts.php?c=1&amp;load=user-profile,password-strength-meter"></script>

		</table>
		<input type="hidden" name="action" value="updateProfile" />
		<input type="submit" value="<?php esc_attr_e('Update Profile'); ?>" name="submit" id="submit" />
	</form>

<?php
	} // конец профиля
	else
	{ // добавление статьи

		$error = '';

		if ($_POST['submit']) {
			global $user_ID;

			$tags = $_POST['tags'];
			$newtags = explode(',',$_POST['newtags']);
			foreach ($newtags as $tag) {
				wp_insert_term(trim($tag), 'tag');
				$tags[] = trim($tag);
			}

			$post_status = 'pending';
			if ( $options['article_status'] == '1' ) $post_status = 'publish';

			$new_post = array(
				'post_title' => wp_filter_nohtml_kses($_POST['title']),
				'post_content' => $_POST['post'],
				'post_status' => $post_status,
				'post_author' => $user_ID,
				'post_type' => 'post',
				'post_category' => $_POST['cats'],
				'tags_input' => implode(',',$tags)
			);

			if (empty($_POST['title'])) {
				$error .= '<div><strong>' . __('ERROR', 'article-directory') . '</strong>: ' . __('Specify the article title.', 'article-directory') . '</div>';
			}
			if (empty($_POST['post'])) {
				$error .= '<div><strong>' . __('ERROR', 'article-directory') . '</strong>: ' . __('Specify the article text.', 'article-directory') . '</div>';
			}
			if (empty($_POST['cats']) || $_POST['cats'][0] == -1) {
				$error .= '<div><strong>' . __('ERROR', 'article-directory') . '</strong>: ' . __('Select a category.', 'article-directory') . '</div>';
			}
			if (strlen($_POST['post']) < $options['minimum_symbols']) {
				$error .= '<div><strong>' . __('ERROR', 'article-directory') . '</strong>: ' . __('The minimum number of characters allowed in the article', 'article-directory') . ': <strong>' . $options['minimum_symbols'] . '</strong></div>';
			}
			preg_match_all('#href\s*=\s*("|\')?(.*?)("|\'|\s|> )#si', $_POST['post'], $matches);
			if (count($matches[0]) > $options['maximum_links']) {
				$error .= '<div><strong>' . __('ERROR', 'article-directory') . '</strong>: ' . __('The maximum number of links allowed in the article', 'article-directory') . ': <strong>' . $options['maximum_links'] . '</strong></div>';
			}

			$submitted = '';
			if (get_option('permalink_structure') == '') {
				$submitted = '&submitted';
			} else {
				$submitted = '?submitted';
			}

			if (!$error) {
				global $demo;
				if (!$demo) {
					$post_id = wp_insert_post($new_post);
				}
				if (strpos($uri, $submitted) === false ) {
					wp_redirect($uri . $submitted);
				} else {
					wp_redirect($uri);
				}
				exit;
			} else {
				$error = $error;
			}

		}

		get_header();
		echo $authorNav;

		if (isset($_GET['submitted'])) {
			if (!$error) {
				if ( $options['article_status'] == '1' ) {
			 		echo '<div class="success"><strong>' . __('Thank you! Your article has been published.', 'article-directory') . '</strong></div>';
				} else {
			 		echo '<div class="success"><strong>' . __('Thank you! Your article has been added and is awaiting review by the administrator.', 'article-directory') . '</strong></div>';
				}
			}
		}
		if ($error) echo '<div class="error">' . $error . '</div>';

		if (!empty($options['publish_terms_text'])) {
?>
	<strong><?php _e('Terms of article publication', 'article-directory'); ?></strong>
	<div id="publishTerms"><?php echo $options['publish_terms_text']; ?></div>
<?php } ?>

	<form action="" method="post" id="articleSubmit">
		<div id="post_title"><label><?php _e('Article title', 'article-directory'); ?></label><input type="text" name="title" value="<?php echo $_POST['title'] ?>" /></div>

		<div id="post_content">
			<label><?php _e('Article text', 'article-directory'); ?></label>
			<script src="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/load-scripts.php?c=1&amp;load=utils,editor"></script>
<?php if ($options['show_editor'] == '1') { ?>
			<script>window.onload = function() { document.getElementById('quicktags').style.display = '<?php if ($options["default_editor"] == "html") echo "block"; else echo "none"; ?>'; }</script>
<?php
		wp_print_scripts('quicktags');
		require_once(ABSPATH . '/wp-admin/includes/post.php');
		function richedit() { return true; }
		add_filter('user_can_richedit', 'richedit');
		wp_tiny_mce( false, array( 'height' => '370' ) );
		if ($options['default_editor'] == 'html') {
			add_filter( 'wp_default_editor', create_function('', 'return "html";') );
		} else {
			add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );
		}
		the_editor($_POST['post'], 'post' , '', false);
	} else {
		echo '<style type="text/css">#quicktags {display:none}</style>';
		the_editor($_POST['post'], 'post' , '', false);
	}
?>
		</div>

		<div id="cats">
<?php if ($options['sel_only_one_cat'] == 1) { ?>
			<label><?php _e('Category', 'article-directory'); ?></label>
<?php	wp_dropdown_categories('show_option_none=--->&taxonomy=category&hide_empty=0&name=cats[]&orderby=name&hierarchical=1'); ?>
<?php } else { ?>
			<label><?php _e('Categories'); ?></label>
			<div class="description"><?php _e('Press Ctrl + click to select more than one', 'article-directory'); ?></div>
<?php
	$categories = wp_dropdown_categories("taxonomy=category&hide_empty=0&orderby=name&hierarchical=1&echo=0");
	preg_match_all('/\s*<option class="(\S*)" value="(\S*)">(.*)<\/option>\s*/', $categories, $matches, PREG_SET_ORDER);
	echo '<select name="cats[]" size="7" multiple="multiple">';
	foreach ($matches as $match) {
		echo '<option value="'.$match[2].'">'.$match[3].'</option>';
	}
	echo "</select>\n";
}
?>
		</div>

<?php if ($options['show_tags'] == 1 || $options['allow_new_tags'] == 1) { ?>
		<div id="tags">
<?php } ?>
<?php
if ($options['show_tags'] == 1) {
	$tags = get_tags('get=all');
	if (!empty($tags)) { ?>
			<label><?php _e('Tags', 'article-directory'); ?></label>
			<div class="description"><?php _e('Press Ctrl + click to select more than one', 'article-directory'); ?></div>
<?php
		echo '<select name="tags[]" size="7" multiple="multiple">';
		foreach ($tags as $tag) {
?>
			<option value="<?php echo $tag->name ?>"><?php echo $tag->name ?></option>
<?php
		}
		echo "</select>\n";
	}
}
?>
<?php if ($options['allow_new_tags'] == 1) { ?>
			<label><?php _e('New tags', 'article-directory'); ?></label>
			<div id="newtags"><input type="text" name="newtags" size="16" value="<?php echo $_POST['newtags']; ?>" /></div>
<?php } ?>
<?php if ($options['show_tags'] == 1 || $options['allow_new_tags'] == 1) { ?>
		</div>
<?php } ?>

		<input type="submit" name="submit" value="<?php _e('Submit'); ?>" id="submit" />
	</form>

<?php
	} // конец добавления статьи

	get_sidebar();
	get_footer();

} else {

	get_header();
	if (have_posts()) : while (have_posts()) : the_post();
		echo '<h2>' . get_the_title() . '</h2>';
		the_content();
	endwhile; endif;
	get_sidebar();
	get_footer();

}
?>