<?php
/*
Plugin Name: Article Directory
Plugin URI: http://articlesss.com/article-directory-wordpress-plugin/
Description: Displays the structured list of categories (like in article directory), which can be easily customized with CSS. Also allows authors to publish articles and change their profile bypassing the admin interface.
Version: 1.3
Author: Dimox
Author URI: http://dimox.net/
*/



function artdir_get_version() {
	return '1.3';
}


if (strstr($_SERVER['REQUEST_URI'], 'article-directory.php') && isset($_GET['ver'])) echo artdir_get_version();
if (!function_exists('add_action')) exit;



function artdir_textdomain() {
	load_plugin_textdomain('article-directory', 'wp-content/plugins/article-directory/languages');
	require_once(ABSPATH . '/wp-admin/includes/plugin.php');
	register_setting('article_directory', 'article_directory', 'artdir_validate');
}
add_action('init', 'artdir_textdomain');



function artdir_plugin_description($string) {
	if (trim($string) == 'Displays the structured list of categories (like in article directory), which can be easily customized with CSS. Also allows authors to publish articles and change their profile bypassing the admin interface.')
	$string = __('Displays the structured list of categories (like in article directory), which can be easily customized with CSS. Also allows authors to publish articles and change their profile bypassing the admin interface. See the demo at <a href="http://articlesss.com/demo/">articlesss.com</a>. <strong>Attention!</strong> If you deactivate the plugin its settings will be removed from the database.', 'article-directory');
	return $string;
}
add_filter('pre_kses', 'artdir_plugin_description');



function artdir_default_options() {
	$def_options['exclude_cats'] = 0;
	$def_options['show_parent_count'] = 1;
	$def_options['show_child_count'] = 1;
	$def_options['hide_empty'] = 0;
	$def_options['desc_for_parent_title'] = 1;
	$def_options['desc_for_child_title'] = 1;
	$def_options['child_hierarchical'] = 1;
	$def_options['column_count'] = 3;
	$def_options['sort_by'] = 0;
	$def_options['sort_direction'] = 0;
	$def_options['no_child_alert'] = 1;
	$def_options['show_child'] = 1;
	$def_options['maximum_child'] = 0;
	$def_options['author_interface'] = 0;
	$def_options['author_panel_id'] = '';
	$def_options['article_status'] = 0;
	$def_options['minimum_symbols'] = 700;
	$def_options['maximum_links'] = 3;
	$def_options['show_editor'] = 1;
	$def_options['default_editor'] = 'html';
	$def_options['sel_only_one_cat'] = 1;
	$def_options['show_tags'] = 0;
	$def_options['allow_new_tags'] = 0;
	$def_options['publish_terms_text'] = '';
	$def_options['kinderloss'] = 1;
	$def_options['show_article_code'] = 0;
	return $def_options;
}



function artdir_validate($input) {
	$def_options = artdir_default_options();
	$input['exclude_cats']          = (preg_match("/^(\d+,)*\d+$/", $input['exclude_cats']) ? $input['exclude_cats'] : $def_options['exclude_cats']);
	$input['show_parent_count']     = ($input['show_parent_count'] == 1 ? 1 : 0);
	$input['show_child_count']      = ($input['show_child_count'] == 1 ? 1 : 0);
	$input['hide_empty']            = ($input['hide_empty'] == 1 ? 1 : 0);
	$input['desc_for_parent_title'] = ($input['desc_for_parent_title'] == 1 ? 1 : 0);
	$input['desc_for_child_title']  = ($input['desc_for_child_title'] == 1 ? 1 : 0);
	$input['child_hierarchical']    = ($input['child_hierarchical'] == 1 ? 1 : 0);
	$input['column_count']          = (is_numeric($input['column_count']) && $input['column_count'] > 0 ? $input['column_count'] : $def_options['column_count']);
	$input['sort_by']               = ($input['sort_by'] == 1 ? 1 : 0);
	$input['sort_direction']        = ($input['sort_direction'] == 1 ? 1 : 0);
	$input['no_child_alert']        = ($input['no_child_alert'] == 1 ? 1 : 0);
	$input['show_child']            = ($input['show_child'] == 1 ? 1 : 0);
	$input['maximum_child']         = (is_numeric($input['maximum_child']) && $input['maximum_child'] > 0 ? $input['maximum_child'] : $def_options['maximum_child']);
	$input['author_interface']      = ($input['author_interface'] == 1 ? 1 : 0);
	$input['author_panel_id']       = (!empty($input['author_panel_id']) && $input['author_panel_id'] > 0 && is_numeric($input['author_panel_id']) ? $input['author_panel_id'] : '');
	$input['article_status']        = ($input['article_status'] == 1 ? 1 : 0);
	$input['minimum_symbols']       = (!empty($input['minimum_symbols']) && $input['minimum_symbols'] > 0 && is_numeric($input['minimum_symbols']) ? $input['minimum_symbols'] : $def_options['minimum_symbols']);
	$input['maximum_links']         = (!empty($input['maximum_links']) && $input['maximum_links'] >= 0 && is_numeric($input['maximum_links']) ? $input['maximum_links'] : $def_options['maximum_links']);
	$input['show_editor']           = ($input['show_editor'] == 1 ? 1 : 0);
	$input['default_editor']        = ($input['default_editor'] == 'tinymce' ? 'tinymce' : $def_options['default_editor']);
	$input['sel_only_one_cat']      = ($input['sel_only_one_cat'] == 1 ? 1 : 0);
	$input['show_tags']             = ($input['show_tags'] == 1 ? 1 : 0);
	$input['allow_new_tags']        = ($input['allow_new_tags'] == 1 ? 1 : 0);
	$input['publish_terms_text']    = (!empty($input['publish_terms_text']) ? $input['publish_terms_text'] : '');
	$input['kinderloss']            = ($input['kinderloss'] == 1 ? 1 : 0);
	$input['show_article_code']     = ($input['show_article_code'] == 1 ? 1 : 0);
	if (isset($_POST['artdirReset'])) 	{
		$input = artdir_default_options();
	}
  return $input;
}



function artdir_uninstall() {
	delete_option('article_directory');
}
register_deactivation_hook( __FILE__, 'artdir_uninstall');



function article_directory($echo = TRUE) {

	$options = get_option('article_directory');

	$exclude_cat            = array($options['exclude_cats']);
	$show_parent_count      = $options['show_parent_count'];
	$show_child_count       = $options['show_child_count'];
	$hide_empty             = $options['hide_empty'];
	$desc_for_parent_title  = $options['desc_for_parent_title'];
	$desc_for_child_title   = $options['desc_for_child_title'];
	$child_hierarchical     = $options['child_hierarchical'];
	$column_count           = $options['column_count'];
	$sort_by                = $options['sort_by'];
	$sort_direction         = $options['sort_direction'];
	$no_child_alert         = $options['no_child_alert'];
	$show_child             = $options['show_child'];
	$maximum_child          = $options['maximum_child'];

	global $wpdb;
	$cal_tree = array();
	if (!$column_count) $column_count = 1;

	global $rssfeeds;
  $feed = '';
	if ($rssfeeds) {
		$feed = 'RSS';
		$show_parent_count = 0;
		$show_child_count = 0;
	}

	if ($sort_by == 0) $order_by = $orderby = 'name';
	elseif ($sort_by == 1) { $order_by = 'term_order'; $orderby = 'term_group'; }


	$parent_cats = $wpdb->get_results("SELECT *
	FROM " . $wpdb->term_taxonomy . " term_taxonomy
	LEFT JOIN " . $wpdb->terms . " terms
	ON terms.term_id = term_taxonomy.term_id
	WHERE term_taxonomy.taxonomy = 'category' AND term_taxonomy.parent = 0 " .
	( count($exclude_cat) ? ' AND terms.term_id NOT IN (' . implode(',', $exclude_cat) . ') ' : '' )
	. " ORDER BY terms." . $order_by);

	foreach ($parent_cats as $parent) {

		$summ = "SELECT SUM(count) FROM " . $wpdb->term_taxonomy . " WHERE taxonomy = 'category' AND parent = " . $parent->term_id;

		$child_summ = mysql_result(mysql_query($summ),0); //считаем кол-во статей в подрубрике 1-го уровня

		$catid = $wpdb->get_var("SELECT term_ID FROM " . $wpdb->term_taxonomy . " WHERE taxonomy = 'category' AND parent = " . $parent->term_id); //определяем ID подрубрики 1-го уровня

		$sub_child_summ = (int)$catid ? $wpdb->get_var("SELECT SUM(count) FROM " . $wpdb->term_taxonomy . " WHERE taxonomy = 'category' AND parent = " . $catid) : 0; //считаем кол-во статей в подрубрике 2-го уровня

		$cat_name = get_the_category_by_ID($parent->term_id);

 		$descr = sprintf(__("View all posts filed under %s"), $cat_name);

		if ($desc_for_parent_title == 1) {
			if (empty($parent->description)) {
				$descr = $descr;
			} else {
				$descr = $parent->description;
			}
		}

		$child_summ += $parent->count;  //прибавляем к сумме родительской рубрики сумму в подрубрике 1-го уровня
		$child_summ += $sub_child_summ; //прибавляем к сумме родительской рубрики сумму в подрубрике 2-го уровня

		if ($show_parent_count == 1) {
			$parent_count = ' (' . $child_summ . ')';
		} else {
			$parent_count = '';
		}

		$cal_tree[] = array(
			'cat' => array(
			'href'  => get_category_link($parent->term_id),
			'title' => $descr,
			'name'  => $cat_name,
			'count' => $parent_count
		),
		'cats'=> wp_list_categories( ( count($exclude_cat) ? 'exclude=' . implode(',', $exclude_cat) : '' ) . '&orderby=' . $orderby . '&show_count=' . $show_child_count . '&hide_empty=' . $hide_empty . '&use_desc_for_title=' . $desc_for_child_title . '&child_of=' . $parent->term_id . '&title_li=&hierarchical=' . $child_hierarchical . '&echo=0&feed=' . $feed)
		);

	}


	$_tree = array();
	$count = count($cal_tree);
	if ($sort_direction) {
		$line_count = ceil( $count / $column_count );
		$limit      = $count - $line_count * $column_count % $count;
		for ($i = 0; $i < $count; $i++) {
			$index = floor($i / $line_count) + ($limit && $i > $limit ? 1 : 0);
			if (!isset($_tree[$index])) { $_tree[$index] = array(); }
			$_tree[$index][] = &$cal_tree[$i];
		}
	}
	else {
		for ($i = 0; $i < $count; $i++) {
			$index = $i % $column_count;
			if (!isset($_tree[$index])) { $_tree[$index] = array(); }
			$_tree[$index][] = &$cal_tree[$i];
		}
	}


	if (count($_tree)) {

		$write = '
<div id="categories">';

		for ($j = 0, $count = count($_tree); $j < $count; $j++) {

			// вывод столбца
			$write .= '
		<ul class="column">';

			// вывод рубрик для столбца
			for ($i = 0, $icount = count($_tree[$j]); $i < $icount; $i++) {

				$catcount = $i + 11;
				if ($j == 1) $catcount = $i + 21;
				if ($j == 2) $catcount = $i + 31;
				if ($j == 3) $catcount = $i + 41;
				if ($j == 4) $catcount = $i + 51;

				if ($rssfeeds) {

					$write .= '

			<li id="cat-'. $catcount .'"><div><a href="' . esc_html($_tree[$j][$i]['cat']['href']) . '" title="' . esc_html($_tree[$j][$i]['cat']['title']) . '">' . esc_html($_tree[$j][$i]['cat']['name']) . '</a> (<a href="' . esc_html($_tree[$j][$i]['cat']['href']) . '/feed/" title="' . esc_html($_tree[$j][$i]['cat']['title']) . '">RSS</a>)</div>';

				} else {

					$write .= '

			<li id="cat-'. $catcount .'"><div><a href="' . esc_html($_tree[$j][$i]['cat']['href']) . '" title="' . esc_html($_tree[$j][$i]['cat']['title']) . '">' . esc_html($_tree[$j][$i]['cat']['name']) . '</a>' . $_tree[$j][$i]['cat']['count'] . '</div>';

				}

				// see wp-includes/category-template.php::276
				// $output .= '<li>' . __("No categories") . '</li>';
				$nocats = '<li>' . __("No categories") . '</li>';

				if ($no_child_alert == 1) $nocats = '';

				if ($_tree[$j][$i]['cats'] != $nocats && $show_child == 1) {

				$write .= '
			<ul class="sub-categories">';
					if ($maximum_child) {
						for ($s = 0, $strlen = strlen($_tree[$j][$i]['cats']), $counter = $maximum_child+1, $slevel = 0; $s < $strlen; $s++) {
							if (!$slevel && substr($_tree[$j][$i]['cats'], $s, 3) == '<li' && !(--$counter)) break;
							else if (substr($_tree[$j][$i]['cats'], $s, 3) == '<ul') $slevel++;
							else if ($slevel && substr($_tree[$j][$i]['cats'], $s-4, 4) == '/ul>') $slevel--;
							else if (!$slevel) $write .= substr($_tree[$j][$i]['cats'], $s, 1);
						}
						$licount = substr_count($_tree[$j][$i]['cats'], '<li');
						if ( ($licount > $maximum_child) && ($_tree[$j][$i]['cats'] != '<li>' . __("No categories") . '</li>') ) {
							$write .= '<li>...</li>';
						}
					}
					else $write .= $_tree[$j][$i]['cats'];

					$write .= '
			</ul>';

				}
				$write .= '
		</li>';

			}

			// печать одного столбца
			$write .= '
	</ul><!-- .column -->' . "\r\n";

		}

$write .= '
</div><!-- #categories -->' . "\r\n";

if ( $echo == true )
	echo $write;
else
	return $write;

	}

}



function artdir_options_page() {
	add_options_page('Article Directory', 'Article Directory', 8, __FILE__, 'artdir_options');
	if (!get_option("article_directory")) {
		$options = artdir_default_options();
		add_option("article_directory", $options) ;
	}
}
add_action('admin_menu', 'artdir_options_page');



function artdir_options() {
	$options = get_option('article_directory');
	$error = false;
	if ( empty($options['author_panel_id']) && $options['author_interface'] == '0' ) {
		echo '<div id="message" class="error"><p><strong style="color:#C00">' .  __('Attention', 'article-directory') . '!</strong> ' .  __('The option "<em><strong>ID of author panel page</strong></em>" must be filled obligatory. Otherwise, the authors are unable to add articles.', 'article-directory') . '</p></div>';
	}
	if ( empty($options['author_panel_id']) ) {
		$error = ' style="border: 1px solid #C00; background: #FFEBE8;"';
	}
?>

<div class="wrap">

	<h2><?php _e('Article Directory Options', 'article-directory'); ?></h2>

	<form method="post" action="options.php">
		<?php settings_fields('article_directory'); ?>
		<?php $options = get_option('article_directory'); ?>

		<div id="poststuff" class="ui-sortable">

			<p><input type="submit" class="button-primary" value="<?php _e('Update Options', 'article-directory') ?>" style="font-weight:bold;" /><br><br></p>

			<div class="postbox">

		    <h3><?php _e('Categories List Options', 'article-directory'); ?></h3>

				<div class="inside">

					<table class="form-table">

					 	<tr valign="top">
							<td scope="row" colspan="3"><strong style="color: #090"><span style="color: #F00">(!)</span> <?php _e('This options is only for the list of categories, which displays on the home page (or another page, where you have inserted the <code>article_directory()</code> function).', 'article-directory'); ?></strong></td>
			      </tr>

					 	<tr valign="top">
							<td scope="row"><label for="column_count"><?php _e('The number of columns for parent categories list', 'article-directory'); ?>:</label></td>
							<td>
								<input name="article_directory[column_count]" type="text" id="column_count" value="<?php echo $options['column_count']; ?>" size="4" maxlength="2" />
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Sort the parent categories list', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[sort_by]">
									<option value="0"<?php selected('0', $options['sort_by']); ?>><?php _e('By name', 'article-directory'); ?></option>
									<option value="1"<?php selected('1', $options['sort_by']); ?>><?php _e('By your choice', 'article-directory'); ?></option>
								</select>
							</td>
							<td><?php _e('For sorting by your choice you need to install <a href="http://wordpress.org/extend/plugins/my-category-order/">My Category Order</a> plugin.', 'article-directory'); ?></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Sort direction of parent categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[sort_direction]">
									<option value="1"<?php selected('1', $options['sort_direction']); ?>><?php _e('From top to down', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['sort_direction']); ?>><?php _e('From left to right', 'article-directory'); ?></option>
								</select>
							</td>
							<td><?php _e('At sorting "From left to right" the list is built more rationally.', 'article-directory'); ?></td>
			      </tr>

					 	<tr valign="top">
							<td scope="row"><label><?php _e('Show the number of articles in parent categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[show_parent_count]">
									<option value="1"<?php selected('1', $options['show_parent_count']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['show_parent_count']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show description in the title of parent categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[desc_for_parent_title]">
									<option value="1"<?php selected('1', $options['desc_for_parent_title']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['desc_for_parent_title']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show the "No categories", if category don\'t contain subcategories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[no_child_alert]">
									<option value="1"<?php selected('1', $options['no_child_alert']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['no_child_alert']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr>
							<td style="padding: 0">&nbsp;</td>
						</tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show the child categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[show_child]">
									<option value="1"<?php selected('1', $options['show_child']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['show_child']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show the number of articles in child categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[show_child_count]">
									<option value="1"<?php selected('1', $options['show_child_count']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['show_child_count']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label for="maximum_child"><?php _e('The number of child categories to show', 'article-directory'); ?>:</label></td>
							<td>
								<input name="article_directory[maximum_child]" type="text" id="maximum_child" value="<?php echo $options['maximum_child']; ?>" size="4" maxlength="2" />
							</td>
							<td><?php _e('<code>0</code> - all child categories will be displayed. If the number other than zero, level 3 child categories not shown.<br /> Specify <code>99</code>, if you not want to show subcategories of 3rd and above level.', 'article-directory'); ?></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show description in the title of child categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[desc_for_child_title]">
									<option value="1"<?php selected('1', $options['desc_for_child_title']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['desc_for_child_title']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Use hierarchy for child categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[child_hierarchical]">
									<option value="1"<?php selected('1', $options['child_hierarchical']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['child_hierarchical']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Hide empty categories', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[hide_empty]">
									<option value="1"<?php selected('1', $options['hide_empty']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['hide_empty']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr>
							<td style="padding: 0">&nbsp;</td>
						</tr>

					 	<tr valign="top">
							<td scope="row" style="width: 360px"><label for="exclude_cats"><?php _e('Comma separated IDs of categories, which should be excluded', 'article-directory'); ?>:</label></td>
							<td width="130">
								<input name="article_directory[exclude_cats]" type="text" id="exclude_cats" value="<?php echo $options['exclude_cats']; ?>" size="15" />
							</td>
							<td><?php _e('Еxample: <code>1,3,7</code>. <code>0</code> - all categories will be displayed.', 'article-directory'); ?></td>
			      </tr>

			    </table>

				</div><!-- .inside -->

			</div><!-- .postbox -->

			<div class="postbox">

		    <h3><?php _e('"Submit Article" page options', 'article-directory'); ?></h3>

				<div class="inside">

					<table class="form-table">

						<tr valign="top">
							<td scope="row" style="width: 360px"><label><?php _e('Interface for authors', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[author_interface]" id="author_interface">
									<option value="1"<?php selected('1', $options['author_interface']); ?>><?php _e('WordPress admin area', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['author_interface']); ?>><?php _e('Author panel', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

					</table>

<script type="text/javascript">
(function($) {
	$(function() {
		if ( $('#author_interface').val() == '1' ) $('#author_panel_options').hide();
		$('#author_interface').change(function() { $('#author_panel_options, #message').toggle(); })
	})
})(jQuery)
</script>

					<table class="form-table" id="author_panel_options">

<noscript>
					 	<tr valign="top">
							<td scope="row" colspan="3"><div style="margin: -10px 0 0"><strong style="color: #FF4D00"><?php _e('The following options are works only if you have selected the interface "Author panel".', 'article-directory'); ?></strong></div></td>
			      </tr>
</noscript>

						<tr valign="top">
							<td scope="row" style="width: 360px"><label for="author_panel_id"><?php _e('ID of author panel page', 'article-directory'); ?>:</label></td>
							<td width="160">
							  <input<?php echo $error; ?> name="article_directory[author_panel_id]" type="text" id="author_panel_id" value="<?php echo $options['author_panel_id']; ?>" size="5" maxlength="6" />
							</td>
							<td><?php _e('<strong>Mandatory option.</strong> More about it read in the instructions for installing the plugin.', 'article-directory'); ?> <?php _e('<a href="http://articlesss.com/article-directory-wordpress-plugin/#faq5" target="_blank">How to find this ID.</a>', 'article-directory'); ?></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Assign the following status to submitted article', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[article_status]">
									<option value="1"<?php selected('1', $options['article_status']); ?>><?php _e('Published'); ?></option>
									<option value="0"<?php selected('0', $options['article_status']); ?>><?php _e('Pending Review'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label for="minimum_symbols"><?php _e('The minimum number of characters allowed in article', 'article-directory'); ?>:</label></td>
							<td>
							  <input name="article_directory[minimum_symbols]" type="text" id="minimum_symbols" value="<?php echo $options['minimum_symbols']; ?>" size="5" maxlength="4" />
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label for="maximum_links"><?php _e('The maximum number of links allowed in article', 'article-directory'); ?>:</label></td>
							<td>
							  <input name="article_directory[maximum_links]" type="text" id="maximum_links" value="<?php echo $options['maximum_links']; ?>" size="5" maxlength="2" />
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show text editor', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[show_editor]" id="show_editor">
									<option value="1"<?php selected('1', $options['show_editor']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['show_editor']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

<script type="text/javascript">
(function($) {
	$(function() {
		if ( $('#show_editor').val() == '0' ) $('#show_editor_options').hide();
		$('#show_editor').change(function() { $('#show_editor_options').toggle(); })
	})
})(jQuery)
</script>

						<tr valign="top" id="show_editor_options">
							<td scope="row"><label><?php _e('Default text editor', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[default_editor]">
									<option value="html"<?php selected('html', $options['default_editor']); ?>><?php _e('HTML editor', 'article-directory'); ?></option>
									<option value="tinymce"<?php selected('tinymce', $options['default_editor']); ?>><?php _e('Visual editor', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Allow to choose only one category', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[sel_only_one_cat]">
									<option value="1"<?php selected('1', $options['sel_only_one_cat']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['sel_only_one_cat']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td><?php _e('Recommended to publish an article in only one category for the prevention of duplicate content. This option would avoid the publication in more than one category.', 'article-directory'); ?></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Show list of tags', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[show_tags]">
									<option value="1"<?php selected('1', $options['show_tags']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['show_tags']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label><?php _e('Allow to add new tags', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[allow_new_tags]">
									<option value="1"<?php selected('1', $options['allow_new_tags']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['allow_new_tags']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row"><label for="publish_terms_text"><?php _e('Terms of article publication', 'article-directory') ?>:</label></td>
							<td colspan="2">
							  <table width="100%" style="border-collapse: collapse;">
			      			<tr valign="top">
			      				<td style="border:none; padding: 0 10px 0 0"><textarea style="font: 11px/13px Arial, Tahoma, Arial; width: 400px; height: 200px;" name="article_directory[publish_terms_text]" id="publish_terms_text"><?php echo $options['publish_terms_text']; ?></textarea></td>
			      				<td style="border:none; padding: 0"><?php _e('The terms appear before the article submission form. You can use html tags for text formatting, for example, <code>&lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;a&gt;</code>. Leave this field blank, if you don\'t want to show the terms.', 'article-directory'); ?></td>
			      			</tr>
			      		</table>
							</td>
			      </tr>

			    </table>

				</div><!-- .inside -->

			</div><!-- .postbox -->

			<div class="postbox">

		    <h3><?php _e('Other Options', 'article-directory'); ?></h3>

				<div class="inside">

					<table class="form-table">

						<tr valign="top">
							<td scope="row" style="width: 360px"><label><?php _e('Exclude the child categories articles from the parent categories pages', 'article-directory'); ?>:</label></td>
							<td width="40">
								<select name="article_directory[kinderloss]">
									<option value="1"<?php selected('1', $options['kinderloss']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['kinderloss']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td></td>
			      </tr>

						<tr valign="top">
							<td scope="row" style="width: 360px"><label><?php _e('Show article source code', 'article-directory'); ?>:</label></td>
							<td>
								<select name="article_directory[show_article_code]">
									<option value="1"<?php selected('1', $options['show_article_code']); ?>><?php _e('Yes', 'article-directory'); ?></option>
									<option value="0"<?php selected('0', $options['show_article_code']); ?>><?php _e('No', 'article-directory'); ?></option>
								</select>
							</td>
							<td><?php _e('Appears on article page.', 'article-directory'); ?></td>
			      </tr>

			    </table>

				</div><!-- .inside -->

			</div><!-- .postbox -->

			<p><input type="submit" class="button-primary" value="<?php _e('Update Options', 'article-directory') ?>" style="font-weight:bold;" /><br><br></p>
	    <p><input type="submit" name="artdirReset" class="button-primary" value=" <?php _e('Reset Defaults', 'article-directory') ?> " /><br><br></p>

			<div class="postbox">

		    <h3><?php _e('Copyright', 'article-directory'); ?></h3>

				<div class="inside">

					<p>&copy; 2008-<?php echo date('Y'); ?> <a href="<?php _e('http://dimox.net', 'article-directory') ?>">Dimox</a> | <a href="<?php _e('http://articlesss.com/article-directory-wordpress-plugin/', 'article-directory') ?>">Article Directory</a> | <?php _e('version', 'article-directory') ?> <?php echo artdir_get_version() ?></p>

				</div><!-- .inside -->

			</div><!-- .postbox -->

		</div><!-- #poststuff -->

	</form>

</div><!-- .wrap -->

<?php

}



$options = get_option('article_directory');



if ($options['kinderloss'] == 1) {

	//thanks to "Kinderlose" plugin - http://guff.szub.net/kinderlose
	function kinderloss_where($where) {
		if ( is_category() ) {
			global $wp_query;
			$where = preg_replace('/.term_id IN \(\'(.*)\'\)/', '.term_id IN (\'' . $wp_query->query_vars['cat'] . '\') AND post_type = \'post\' AND post_status = \'publish\'', $where);
		}

		return $where;
	}

	add_filter('posts_where', 'kinderloss_where');
}



if ($options['author_interface'] == 0) {

	function artdir_restrict_admin_area() {
		if (strpos($_SERVER['SCRIPT_NAME'], 'wp-admin')) {
			if (!current_user_can('level_7')) {
				$options = get_option('article_directory');
				require_once( ABSPATH . WPINC . '/pluggable.php');
				if ( is_user_logged_in() ) {
					wp_redirect(get_permalink($options['author_panel_id']));
	?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<title><?php _e('WordPress &rsaquo; Error'); ?></title>
</head>
<body style="background:#F9F9F9">
	<div style="background:#FFF;color:#333;font:12px/18px 'Lucida Grande',Verdana,Arial,'Bitstream Vera Sans',sans-serif;margin:50px auto;width:700px;padding:18px 32px;-moz-border-radius:11px;-webkit-border-radius:11px;border-radius:11px;border:1px solid #DFDFDF">
		<h1 style="font-size: 14px"><?php _e('Error', 'article-directory'); ?></h1>
		<p><?php _e('Unfortunately, you can not get into the author panel, because the site admin does not <a href="http://articlesss.com/article-directory-wordpress-plugin/#installation" target="_blank">set it up</a>.', 'article-directory') ?></p>
		<p>&raquo; <a href="<?php echo wp_logout_url(get_bloginfo('wpurl')); ?>"><?php _e('Log out') ?></a></p>
	</div>
</body>
</html>
	<?php
					die();
				}
			}
		}
	}
	add_action('init', 'artdir_restrict_admin_area');

}



function artdir_jquery() {
	wp_deregister_script('jquery');
	wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"), false, '1.4.2');
	wp_enqueue_script('jquery');
}



if ($options['show_article_code'] == 1) {

	function artdir_get_article_code($text) {
		$rn = "\r\n\r\n";
		$get_article_code = '
<script type="text/javascript">
(function($) {
$(function() {
	$("#getArticleCode").css({opacity: 0}).hide();
	$("#getArticleSource").toggle(
		function() { $("#getArticleCode").animate({opacity: 1}, 300).show(); return false; },
		function() { $("#getArticleCode").animate({opacity: 0}).hide();	return false;	}
	);
	$("#htmlVersion").text("<h1>' . get_the_title() . '</h1>" + "\r\n" + $("#artdirPost").html() + "<p>' . __('Source') . ': <a href=\"' . get_permalink() . '\">' . get_permalink() . '</a></p>");
	$("#textVersion").text("' . get_the_title() . '" + "\r\n\r\n" + $("#artdirPost").text() + "\r\n" + "' . __('Source') . ': ' . get_permalink() . '");
	$("#getArticleCode textarea, #getArticleCode input").click(function() { $(this).select() });
})
})(jQuery)
</script>
<p><a href="#" id="getArticleSource">'.__('Article Source', 'article-directory').'</a></p>
<div id="getArticleCode" style="display:none">
	<label>' . __('HTML Version', 'article-directory') . ':</label>
	<textarea id="htmlVersion" rows="15" cols="50"></textarea>
	<label>' . __('Text Version', 'article-directory') . ':</label>
	<textarea id="textVersion" rows="15" cols="50"></textarea>
	<label>' . __('Article Url', 'article-directory') . ':</label>
	<input type="text" value="' . get_permalink() . '" />
</div>
		';
		if (is_single()) {
			return '<div id="artdirPost">' . $text . '</div>' . $get_article_code;
		} else {
			return $text;
		}
	}
	add_filter('the_content', 'artdir_get_article_code');

	if (is_single()) {
		add_action('wp_head', 'artdir_jquery', 8);
	}
}



function article_directory_author_panel() {
	include(ABSPATH . 'wp-content/plugins/article-directory/author-panel.php');
}



function article_directory_authorization_form() {
	if (!current_user_can('level_0')) { ?>
        <div class="section">
	        <h3><?php _e('Authorization', 'article-directory'); ?></h3>
		      <form name="loginform" id="authoriz" action="<?php bloginfo('wpurl'); ?>/wp-login.php" method="post">
						<div>
			        <label for="login"><?php _e('Username', 'article-directory'); ?>:</label>
			        <input type="text" name="log" value="" id="login" />
						</div>
						<div>
			        <label for="pass"><?php _e('Password'); ?>:</label>
			        <input type="password" name="pwd" value="" id="pass" />
						</div>
						<div>
		        	<span id="remember"><label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" value="forever" /><?php _e('Remember Me'); ?></label></span>
		        	<input type="submit" name="submit" value="<?php _e('Log In'); ?>" id="enter" />
						</div>
		        <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
		        <div id="lost"><?php wp_register('', ''); ?> | <a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password?'); ?></a></div>
		      </form>
				</div><!-- .section -->
	<?php } else { ?>
        <div class="section">
	        <h3><?php _e('Management', 'article-directory'); ?></h3>
	        <ul>
		<?php if (current_user_can('level_7')) { ?>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/post-new.php"><?php _e('Submit article', 'article-directory'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/edit.php"><?php _e('Posts'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/edit-comments.php"><?php _e('Comments'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/plugins.php"><?php _e('Plugins'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/users.php"><?php _e('Users'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/options-general.php"><?php _e('Options'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/profile.php"><?php _e('Profile'); ?></a></li>
	          <li><a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>"><?php _e('Log out'); ?></a></li>
		<?php } else { ?>
			<?php $options = get_option('article_directory'); ?>
			<?php if ($options['author_interface'] == '0') { ?>
			<?php
				$profile = '';
				if (get_option('permalink_structure') == '') $profile = '&profile';
					else $profile = '?profile';
			?>
            <li><a href="<?php echo get_permalink($options['author_panel_id']); ?>"><?php _e('Submit article', 'article-directory'); ?></a></li>
            <li><a href="<?php echo get_permalink($options['author_panel_id']) . $profile; ?>"><?php _e('My profile', 'article-directory'); ?></a></li>
			<?php } else { ?>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/post-new.php"><?php _e('Submit article', 'article-directory'); ?></a></li>
            <li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/profile.php"><?php _e('Profile'); ?></a></li>
			<?php } ?>
	          <li><a href="<?php echo wp_logout_url(get_bloginfo('wpurl')); ?>"><?php _e('Log out'); ?></a></li>
		<?php } ?>
	        </ul>
				</div><!-- .section -->
<?php }
}

?>