=== Article Directory ===
Contributors: Dimox
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=H7X3M4JL8N7Q4
Tags: categories, articles, directory, list
Requires at least: 2.9
Tested up to: 3.0.4
Stable tag: 1.3

Displays the list of categories like in article directory and allows authors to publish articles and change their profile bypassing the admin area.

== Description ==

Displays the structured list of categories (like in article directory), which can be easily customized with CSS. Also allows authors to publish articles and change their profile bypassing the admin interface. See the demo at [Articlesss.com/demo/](http://articlesss.com/demo/ "Article Directory").

**Standard features for categories list:**

* Showing an amount of articles in parent and child categories.
* Showing a category description in link title.
* Showing the empty categories.
* Using hierarchy for subcategories.
* Excluding a specified categories.

**Special features for categories list:**

* Simple and handy design using CSS. The structure of categories is the multilevel list, which can be altered easily with CSS, as you need. The parent category have the `<div>` container to mark it out with CSS as a parent.
* Parent category is showing amount of articles in subcategories. The character also include the number of articles in this parent category.
* Showing a specified amount of child categories (2nd level).
* Showing a categories in specified amount of columns.
* Hiding all subcategories.
* Adding an icons for each category in the list using CSS.

**Additional features:**

* Authors can add articles and change their profile bypassing the admin interface.
* Displaying the "Terms of article publication" on article submission page.
* Excluding the child categories articles from the parent categories archive pages.
* Ability to get an article source code.


== Installation ==

**Attention:** if you are using the plugin simultaneously with [Article Directory WordPress Theme](http://articlesss.com/article-directory-wordpress-theme/ "Article Directory WordPress Theme"), miss the 5, 6, 7 and 8.1 points of installation:

1. Copy the **article-directory** folder in WordPress plugins directory (`/wp-content/plugins/`).
2. Activate the plugin through admin interface.
3. Activate the **"Membership (Anyone can register)"** option on the **"General Settings"** page of admin area.
4. On the same page select "Author" or "Contributor" in the option **"New User Default Role"**.
5. Add the following code in the `index.php` file of your theme (or in another file in a place, where you want to display the categories list):

	`<?php if (function_exists('article_directory')) article_directory(); ?>`

6. Add the following code in a place of your theme (for example, in sidebar.php), where you want to see the authorization form:

	`<?php if (function_exists('article_directory_authorization_form')) article_directory_authorization_form(); ?>`

7. Add the following code in the **header.php** file before the `</head>` tag:

	`<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-content/plugins/article-directory/author-panel.css" type="text/css" media="screen" />`
	`<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-content/plugins/article-directory/categories.css" type="text/css" media="screen" />`

8. Select a prefered interface for authors in corresponding option at the <strong>"Settings &rarr; Article Directory"</strong> page of admin area. If you have selected the **"Author panel"**, then follow these subpoints:

	1. Create in directory with your theme a new PHP file, for example **author-panel.php** and paste the following code in it:

		`<?php`
		`/*`
		`Template Name: Author panel`
		`*/`
		`if (function_exists('article_directory_author_panel')) article_directory_author_panel();`
		`?>`

	2. Create in admin area a new page and select the template **"Author panel"** in page attributes. Through this page authors will add new articles and change their profile. Access in admin area for authors will be forbidden.

	3. Specify ID of this page in corresponding option at the **"Settings &rarr; Article Directory"** page of admin area.

9. That's all. If it's necessary, you may customize other options of plugin.


The plugin also lets you to display the list with the links to categories RSS feeds. To do this, you must:

* Create a [new page template](http://codex.wordpress.org/Pages#Creating_Your_Own_Page_Templates).
* Add the following code:

	`<?php $rssfeeds=true; ?>`
	`<?php if (function_exists('article_directory')) article_directory(); ?>`

* Create a new page in the admin interface and select the created template in page attributes.


== Frequently Asked Questions ==

You can find them [here](http://articlesss.com/article-directory-wordpress-plugin/#faq).


== Changelog ==

Version history and list of changes you can see [here](http://articlesss.com/article-directory-wordpress-plugin/#version-history).