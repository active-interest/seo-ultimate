=== SEO Ultimate ===
Contributors: SEO Design Solutions
Tags: seo, title, meta, noindex, canonical, 404, robots.txt, htaccess, slugs, url, google, yahoo, bing, search engines, admin, post, page, modules
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 1.1.1

This all-in-one SEO plugin can handle titles, noindex, meta data, slugs, canonical tags, 404 error tracking, and more (with many more features coming soon).

== Description ==

SEO Ultimate is an all-in-one [SEO](http://www.seodesignsolutions.com/) plugin with these features:

* **Title Rewriter** - Lets you format the `<title>` tags of posts, pages, categories, tags, archives, search results, the blog homepage, and more. Also includes a mass-editor for post/page <title> tags.

* **Noindex Manager** - Lets you add the `noindex` meta robots instruction to archives, comment feeds, the login page, and more.

* **Meta Editor** - Lets you edit the meta descriptions/keywords for your posts, pages, and homepage. Also lets you enter verification meta codes and give code instructions to search engine spiders.

* **Canonicalizer** - Inserts `<link rel="canonical" />` tags for your homepage and each of your posts, Pages, categories, tags, date archives, and author archives.

* **404 Monitor** - Logs 404 errors generated on your blog.

* **Linkbox Inserter** - Encourages linkbuilding activity by inserting textboxes containing link HTML.

* **File Editor** - Lets you edit two important SEO-related files: robots.txt and .htaccess

* **Slug Optimizer** - Removes common words from post/Page slugs to increase in-URL keyword potency.

We have many more features that we're working on finetuning before release. If you install the plugin now, you can have these new features delivered to you on a regular basis via WordPress's automatic plugin upgrader.

SEO Ultimate was developed with WordPress plugin "best practices" in mind:

* Integration with the contextual help system of WordPress 2.7+
* Internationalization support
* Nonce security
* An uninstall routine
* Icon support for the new WordPress 2.7+ menu
* Settings import/export/reset functionality


== Installation ==

To install the plugin automatically:

1. Login to your WordPress admin
2. Go to Plugins > Add New
3. Type `seo ultimate` in the search box
4. Click the "Install" link
5. Click "Install Now"
6. Click "Activate this plugin"
7. Go to the new "SEO" menu to start using.


To install the plugin manually:

1. Download and unzip the plugin.
2. Upload the `seo-ultimate` directory to `/wp-content/plugins/`.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to the new "SEO" menu to start using.


== Frequently Asked Questions ==

= Where in WordPress does the plugin add itself? =

SEO Ultimate puts all its admin pages under a new "SEO" top-level menu. The only exception is the plugin settings page, which goes under `Settings > SEO Ultimate`.

= Where's the documentation? =

SEO Ultimate's documentation is built into the plugin itself. Whenever you're viewing an SEO Ultimate page in your WordPress admin, you can click the "Help" tab in the upper-right-hand corner to view documentation for the area you're viewing.

= What are modules? =

SEO Ultimate's features are divided into groups called "modules." SEO Ultimate's "Module Manager" lets you enable or disable each of these groups of features. This way, you can pick-and-choose which SEO Ultimate features you want.

= Can I access a module again after I've hidden it? =

Yes. Just go to the Module Manager and click the module's title to open its admin page. If you'd like to put the module back in the "SEO" menu, just re-enable the module in the Module Manager and click "Save Changes."

= How do I disable the number bubbles on the "SEO" menu? =

Just go to the Module Manager and select the "Silenced" option for any modules generating number bubbles. Then click "Save Changes."

= Why doesn't the settings exporter include all my data in an export? =

The settings export/import system is designed to facilitate moving settings between sites. It is NOT a replacement for keeping your database backed up. The settings exporter doesn't include data that is specific to your site. For example, logged 404 errors are not included because those 404 errors only apply to your site, not another site. Also, post/page titles/meta are not included because the site into which you import the file could have totally different posts/pages located under the same ID numbers.

If you're moving a site to a different server or restoring a crashed site, you should do so with database backup/restore.

= Why do I get a "500 Server Error" after using the File Editor? =

You may have inserted code into your .htaccess file that your web server can't understand. As the File Editor warns, incorrectly editing your .htaccess file can disable your entire website in this way. To restore your site, you'll need to use an FTP client (or your web host's File Manager) to edit or rename your .htaccess file. If you need help, please contact your web host.

= Will my robots.txt edits remain if I disable the File Editor? =

No. On a WordPress blog, the robots.txt file is dynamically generated just like your posts and Pages. If you disable the File Editor module or the entire SEO Ultimate plugin, the File Editor won't be able to insert your custom code into the robots.txt file anymore.

= Will my .htaccess edits remain if I disable the File Editor? =

Yes. The .htaccess file is static. Your edits will remain even if you disable SEO Ultimate or its File Editor module.

= Where did my .htaccess edits go? =

The .htaccess file is static, so SEO Ultimate doesn't have total control over it. It's possible that WordPress, another plugin, or other software may overwrite your .htaccess file. If you have a backup of your blog's files, you can try recovering your edits from there.

= What do I do if my site has multiple meta tags? =

First, try removing your theme's built-in meta tags if it has them. Go to `Appearance > Editor` and edit `header.php`. Delete or comment-out any `<meta>` tags.

If the problem persists, try disabling other SEO plugins that may be generating meta tags.

Troubleshooting tip: Go to `Settings > SEO Ultimate` and enable the "Insert comments around HTML code insertions" option. This will mark SEO Ultimate's meta tags with comments, allowing you to see which meta tags are generated by SEO Ultimate and which aren't.

= How do I edit the meta tags of my homepage? =

If you are using a "blog homepage" (the default option of showing your blog posts on your homepage), go to `SEO > Meta Editor` and use the Blog Homepage fields.

If you have configured your `Settings > Reading` section to use a "frontpage" (i.e. a Page as your homepage), just edit that Page under `Pages > Edit` and use the "Description" and "Keywords" fields in the "SEO Settings" box.

= Does the Title Rewriter edit my post/page titles? =

No. The Title Rewriter edits the `<title>` tags of your site, not your post/page titles.

= What's the difference between the "title" and the "title tag" of a post/page? =

The "title" is the title of your post or page, and is displayed on your site and in your RSS feed. The title is also used in your `<title>` tag by default; however, you can override the value of just the `<title>` tag by using the "Title Tag" field in the "SEO Settings" box.

= What's a slug? =

The slug of a post or page is the portion of its URL that is based on its title.

When you edit a post or Page in WordPress, the slug is the yellow-highlighted portion of the Permalink beneath the Title textbox.

= Does the Slug Optimizer change my existing URLs? =

No. Slug Optimizer will not relocate your content by changing existing URLs. Slug Optimizer only takes effect on new posts and pages.

= How do I see Slug Optimizer in action? =

1. Create a new post/Page in WordPress.
2. Type in a title containing some common words.
3. Click outside the Title box. WordPress will insert a URL labeled "Permalink" below the Title textbox. The Slug Optimizer will have removed the common words from the URL.

= Why didn't the Slug Optimizer remove common words from my slug? =

It's possible that every word in your post title is in the list of words to remove. In this case, Slug Optimizer doesn't remove the words, because if it did, you'd end up with a blank slug.

= What if I want to include a common word in my slug? =

When editing the post or page in question, just click the "Edit" button next to the permalink and change the slug as desired.

= How do I revert back to the optimized slug after making changes? =

When editing the post or page in question, just click the "Edit" button next to the permalink; a "Save" button will appear in its place. Next erase the contents of the textbox, and then click the aforementioned "Save" button.

= How do I remove the attribution link? =

Because of the tremendous effort put into this plugin, we ask that you please leave the link enabled. If you must disable it, you can do so under `Settings > SEO Ultimate`.

= Why isn't the Title Rewriter or the Meta Editor working? =

Try disabling other SEO plugins, as they may be conflicting with SEO Ultimate. Also, check to make sure your theme is [plugin-friendly](http://wordpress.jdwebdev.com/blog/theme-plugin-hooks/).

= How do I disable the "SEO Settings" box in the post/page editors? =

Open the editor, click the "Screen Options" tab in the upper-right-hand corner, and uncheck the "SEO Settings" checkbox.

= Why did some of the textboxes disappear from the "SEO Settings" box? =

The "SEO Settings" fields are added by your modules. The "Title Tag" field is added by the Title Rewriter module, the "Description" and "Keywords" fields are added by the Meta Editor module, etc. If you disable a module using the Module Manager, its fields in the "SEO Settings" box will be disabled too. You can re-enable the field in question by re-enabling the corresponding module.

= How do I uninstall SEO Ultimate? =

1. Go to the `Plugins` admin page.
2. Deactivate the plugin if it's activated.
3. Click the plugin's "Delete" link.
4. Click the "Yes, Delete these files" button. SEO Ultimate's files and database entries will be deleted.

= Will all my settings be deleted if I delete SEO Ultimate in the Plugins manager? =

Yes. WordPress plugins are supposed to delete their settings during the uninstallation process.

== Screenshots ==

1. The Title Rewriter module
2. The Noindex Manager module
3. The Meta Editor module
4. The "SEO Settings" post/page meta box
5. The Linkbox Inserter module
6. The File Editor module

== Changelog ==

= Version 1.1.1 (October 8, 2009) =
* Bugfix: Fixed tab rendering bug

= Version 1.1 (October 7, 2009) =
* Feature: You can now mass-edit post/page titles from the Title Rewriter module
* Bugfix: Fixed a variety of bugs that only appear on PHP4 setups
* Bugfix: Fixed logo background color in the Whitepapers module
* Improvement: Title Rewriter now supports 10 additional title format variables
* Improvement: Added internationalization support for admin menu notice numbers
* Improvement: Certain third-party plugin notices are now removed from SEO Ultimate's admin pages

= Version 1.0 (September 21, 2009) =
* Feature: Canonicalizer can now redirect requests for nonexistent pagination
* Feature: Visitor logging can now be disabled completely from the Plugin Settings page
* Feature: Logged visitor information can now be automatically deleted after a certain number of days
* Feature: Added icon support for the Ozh Admin Drop Down Menu plugin
* Bugfix: 404 Monitor notification count now consistent with new errors shown
* Improvement: Canonicalizer now removes the duplicate canonical tags produced by the WordPress 2.9 Trunk
* Improvement: Inline changelogs now won't display if the Changelogger plugin is activated
* Improvement: SEO Ultimate now selectively logs visitors based on which modules are enabled

= Version 0.9.3 (August 1, 2009) =
* Bugfix: Optimized slugs save with post
* Bugfix: Slug Optimizer now treats words as case-insensitive
* Bugfix: Slug Optimizer now handles words with apostrophes

= Version 0.9.1 (August 1, 2009) =
* Bugfix: Fixed PHP parse errors

= Version 0.9 (August 1, 2009) =
* Feature: Added the Slug Optimizer module
* Feature: Noindex Manager now supports noindexing comment subpages
* Bugfix: 404 Monitor's numeric notice now only includes new 404s
* Bugfix: Linkbox Inserter now respects the "more" tag
* Bugfix: Missing strings added to the POT file
* Improvement: 404 Monitor now shows the referring URL for all 404 errors
* Improvement: Reduced the number of database queries the plugin makes
* Improvement: CSS and JavaScript are now only loaded when appropriate
* Improvement: Added additional built-in documentation
* Improvement: Divided built-in help into multiple tabs to reduce dropdown height
* Improvement: Miscellaneous code efficiency improvements
* Improvement: Many additional code comments added

= Version 0.8 (July 22, 2009) =
* Feature: Added robots.txt editor (new File Editor module)
* Feature: Added .htaccess editor (new File Editor module)
* Bugfix: 404 Monitor no longer uses the unreliable get_browser() function
* Bugfix: 404 Monitor now ignores favicon requests
* Bugfix: Fixed conflict with the WP Table Reloaded plugin
* Bugfix: Fixed bug that caused Module Manager to appear blank on certain configurations
* Bugfix: Fixed bug that caused multiple drafts to be saved per post
* Bugfix: Post meta box no longer leaves behind empty postmeta database rows
* Bugfix: Added missing Module Manager help
* Bugfix: Fixed settings double-serialization bug
* Bugfix: Fixed error that appeared when re-enabling disabled modules
* Bugfix: Newlines and tabs now removed from HTML attributes
* Improvement: SEO Ultimate now stores its wp_options data in 1 entry instead of 4
* Improvement: The settings read/write process has been streamlined
* Improvement: Drastically expanded the readme.txt FAQ section
* Improvement: Plugin's directories now return 403 codes
* Improvement: Settings importer now retains the settings of modules added after the export

= Version 0.7 (July 16, 2009) =
* Feature: Added the Module Manager
* Feature: Modules can optionally display numeric notices in the menu

= Version 0.6 (July 2, 2009) =
* Feature: Added the Linkbox Inserter module
* Bugfix: Fixed plugin notices bug

= Version 0.5 (June 25, 2009) =
* Feature: Added settings exporter
* Feature: Added settings importer
* Feature: Added button that restores default settings
* Bugfix: Fixed bug that decoded HTML entities in textboxes
* Bugfix: Added internationalization support to some overlooked strings
* Compatibility: Restores support for the WordPress 2.7 branch

= Version 0.4 (June 18, 2009) =
* Added the 404 Monitor module

= Version 0.3 (June 11, 2009) =
* Added the Canonicalizer module
* Added alerts of possible plugin conflicts
* Fixed a WordPress 2.8 compatibility issue
* SEO Ultimate now requires WordPress 2.8 or above

= Version 0.2 (June 4, 2009) =
* Added the Meta Editor module
* Fixed a double-escaping bug in the Title Rewriter
* Fixed a bug that caused the Modules list to display twice on some installations

= Version 0.1.1 (May 28, 2009) =
* Fixed a bug that surfaced when other SEO plugins were installed
* Fixed a bug that appeared on certain PHP setups

= Version 0.1 (May 22, 2009) =
* Initial release
