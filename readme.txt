=== SEO Ultimate ===
Contributors: SEO Design Solutions
Tags: seo, google, yahoo, bing, search engines, admin, post, page, modules, title, meta, robots, noindex, nofollow, canonical, 404, robots.txt, htaccess, slugs, url, anchor, more, link, excerpt, permalink, links, autolinks, categories, uninstallable
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 2.3

This all-in-one SEO plugin gives you control over titles, noindex/nofollow, meta tags, slugs, canonical tags, "more" links, 404 errors, and more.

== Description ==

= Recent Releases =

* Version 2.3 adds per-post noindex/nofollow toggles
* Version 2.2 adds a links-per-post limiter for Deeplink Juggernaut
* Version 2.1 adds a 404 Monitor overhaul and other improvements

= Features =

SEO Ultimate is an all-in-one [SEO](http://www.seodesignsolutions.com/) plugin with these powerful features:

* **Title Rewriter**
	* Out-of-the-box functionality puts your post titles at the beginning of the `<title>` tag where they belong.
	* Easily override the entire `<title>` tag contents for any individual post, page, category, or post tag on your blog.
	* Customize your homepage's `<title>` tag.
	* Format the `<title>` tags of posts, pages, categories, tags, archives, search results, and more!

* **Noindex Manager** -- UPDATED in Version 2.3
	* Add the `<meta name="robots" content="noindex,follow" />` tag to archives, comment feeds, the login page, and more.
	* Set meta robots tags (index/noindex and follow/nofollow) for each individual post/page.
	* Avoid duplicate content issues with the recommended settings.

* **Meta Editor**
	* Edit the `<meta>` description/keyword tags for posts, pages, and the homepage.
	* Influence search engine result snippets with the meta description editing functionality.
	* Enter verification codes in the provided fields to access search engine webmaster tools.
	* Give instructions to search engine spiders if desired (`noodp`, `noydir`, and `noarchive`).

* **Canonicalizer**
	* Point search engines to preferred content access points with `<link rel="canonical" />` tags.
	* Go beyond the basic canonical tag functionality of WordPress 2.9+ with SEO Ultimate's support for category/tag/date/author archives.
	* Redirect requests for non-existent pagination with a simple checkbox.

* **404 Monitor** -- UPDATED in Version 2.1
	* Improve the visiting experience of users and spiders by keeping tabs on "page not found" errors. (Use a redirection plugin to point dead-end URLs to your content.)
	* Find out what URLs are referring visitors to 404 errors.

* **Linkbox Inserter**
	* Encourage natural linkbuilding activity by adding textboxes to the end of your posts/pages that contain automatically-generated link HTML.

* **File Editor**
	* Implement advanced SEO strategies with the `.htaccess` editor.
	* Give instructions to search engines via the `robots.txt` editor.

* **Slug Optimizer**
	* Increase in-URL keyword potency by removing "filler words" (like "the," "with," "and," etc.) from post/page URLs.

* **Competition Researcher**
	* Investigate multiple keywords or URLs with quick access to search engine tools. Competition Researcher does this without illicit scraping/automation methods.
	* Find out how many webpages are competing for the keywords you specify.
	* Choose to analyze the keyword relevance in competing webpages' titles, body content, URLs, or anchor text.
	* Find out how many pages of a competing website are in Google's index.
	* Access competitors' incoming links profile.
	* Find out what external websites your competitors are linking to.

* **More Link Customizer**
	* Optimize your posts' "read more" links by including the posts' keyword-rich titles in the anchor text.
	* Override the "read more" link on a per-post basis.
	* Include `<strong>` or `<em>` tags in the anchor text if so desired.

* **Internal Relevance Researcher** -- NEW in Version 1.4
	* Determine which of your webpages Google most strongly associates with the keywords you specify.
	* Use the information to determine ideal targets for incoming links or ideal sources of outgoing links.

* **Deeplink Juggernaut** -- UPDATED in Version 2.2
	* Automatically link phrases in your posts/pages to given URLs.
	* Use the power of anchor text to boost your internal ranking paradigm.
	* Control the maximum number of autolinks added to each post/page.

* **Settings Manager**
	* Export your SEO Ultimate settings to a file and re-import later if desired.
	* Move SEO Ultimate settings between blogs using the export/import functionality.
	* Reset all settings back to "factory defaults" if something goes wrong.
	
* **Additional features**
	* Lets you import post meta from All in One SEO Pack.
	* Displays admin notices if blog privacy settings are configured to block search engines.
	* Supports [WordPress plugin translation](http://urbangiraffe.com/articles/translating-wordpress-themes-and-plugins/). POT file is included in the zip file.
	* SEO Ultimate documentation is seamlessly integrated into the contextual help system of WordPress 2.7+ and is accessible via the dropdowns in the upper-right-hand corner of the admin screen. In-depth info, explanations, and FAQ are just a click away.
	* Unlike certain other SEO plugins, SEO Ultimate sports a clean, simple, aesthetically-pleasing interface, with no ads or donation nags.
	* SEO Ultimate cleanly integrates itself into WordPress without plastering its name all over the interface.
	* If you choose to delete SEO Ultimate from within the WordPress plugin manager, SEO Ultimate will remove all its settings from your database.
	* Includes icon integration with the WordPress 2.7+ menu and the Ozh Admin Drop Down Menu plugin.
	* Uses WordPress plugin security features like nonces, etc.
	
* **Features Coming Soon**
	* Automatic XHTML validation checking
	* Nofollow options
	* Title tag editing for attachments
	* ...And much, much more! Install SEO Ultimate today and use WordPress's automatic plugin updater to get new features as they're released.

[**Download**](http://downloads.wordpress.org/plugin/seo-ultimate.zip) **your free copy of SEO Ultimate today.**

[youtube http://www.youtube.com/watch?v=IE_10_nwe0c]

== Installation ==

To install the plugin automatically:

1. Go to the [SEO Ultimate homepage](http://www.seodesignsolutions.com/wordpress-seo/)
2. In the "Auto Installer" box on the right, enter your blog's URL and click "Launch Installer."
3. Click "Install Now," then click "Activate this plugin."

That's it! Now go to the new "SEO" menu and explore the modules of the SEO Ultimate plugin.


To install the plugin manually:

1. Download and unzip the plugin.
2. Upload the `seo-ultimate` directory to `/wp-content/plugins/`.
3. Activate the plugin through the 'Plugins' menu in WordPress.



== 404 Monitor ==

= Overview =

* **What it does:** The 404 Monitor keeps track of non-existent URLs that generated 404 errors. 404 errors are when a search engine or visitor comes to a URL on your site but nothing exists at that URL.

* **Why it helps:** The 404 Monitor helps you spot 404 errors; then you can take steps to correct them to reduce link-juice loss from broken links.

* **How to use it:** Check the 404 Monitor occasionally for errors. (A numeric bubble will appear next to the "404 Monitor" item on the menu if there are any newly-logged URLs that you haven't seen yet. These new URLs will also be highlighted green in the table.) If a 404 error's referring URL is located on your site, try locating and fixing the broken URL. If moved content was previously located at the requested URL, try using a redirection plugin to point the old URL to the new one.

If there are no 404 errors in the log, this is good and means there's no action required on your part.

= Actions Help =

You can perform the following actions on each entry in the log:

* The "View" button will open the URL in a new window. This is useful for testing whether or not a redirect is working.
* The "Google Cache" button will open Google's archived version of the URL in a new window. This is useful for determining what content, if any, used to be located at that URL.
* Once you've taken care of a 404 error, you can click the "Remove" button to remove it from the list. The URL will reappear on the list if it triggers a 404 error in the future.

= Troubleshooting =

404 Monitor doesn't appear to work? Take these notes into consideration:

* The 404 Monitor doesn't record 404 errors generated by logged-in users.
* In order for the 404 Monitor to track 404 errors, you must have "Pretty Permalinks" enabled under `Settings > Permalinks`.
* Some parts of your website may not be under WordPress's control; the 404 Monitor can't track 404 errors on non-WordPress website areas.



== Canonicalizer ==

= Overview =

* **What it does:** Canonicalizer improves on two WordPress features to minimize possible exact-content duplication penalties. The `<link rel="canonical" />` tags setting improves on the canonical tags feature of WordPress 2.9 and above by encompassing much more of your site than just your posts and Pages.

	The nonexistent pagination redirect feature fills a gap in WordPress's built-in canonicalization functionality: for example, if a URL request is made for page 6 of a category archive, and that category doesn't have a page 6, then by default, depending on the context, WordPress will display a blank page, or it will display the content of the closest page number available, without issuing a 404 error or a 301 redirect (thus creating two or more identical webpages). This duplicate-content situation can happen when you, for example, remove many posts from a category, thus reducing the amount of pagination needed in the category's archive. The Canonicalizer's feature fixes that behavior by issuing 301 redirects to page 1 of the paginated section in question.

* **Why it helps:** These features will point Google to the correct URL for your homepage and each of your posts, Pages, categories, tags, date archives, and author archives. That way, if Google comes across an alternate URL by which one of those items can be accessed, it will be able to find the correct URL and won't penalize you for having two identical pages on your site.

* **How to use it:** Just check both checkboxes and click Save Changes. SEO Ultimate will do the rest.



== Competition Researcher ==

= Overview =

* **What it does:** The Competition Researcher opens Google search results in iframes based on the parameters you specify. The Competition Researcher does _not_ scrape/crawl Google's search results or use other illicit automated methods; it just opens the Google search results in your browser.

	The Competition Researcher lets you find out the following information:
	* How many webpages are competing for the keywords you specify.
	* The keyword relevance in competing webpages' titles, body content, or anchor text.
	* How many pages of a competing website are in Google's index.
	* The incoming links profile of competing websites.
	* The external websites that your competitors are linking to.

* **Why it helps:** The Competition Researcher gives you quick access to specially-constructed search queries. You can study the search results to glean information about the general competition for specific keywords or information about specific competitors' websites. Knowledge of the competition is an essential component of any SEO strategy.

* **How to use it:** Choose a tool based on the information you'd like to obtain, enter the keywords or competitors' domain names that you'd like to research, select options if desired, and then click Submit. The results will open in a new window.



== File Editor ==

= Overview =

* **What it does:** The File Editor module lets you edit two important SEO-related files: robots.txt and .htaccess.

* **Why it helps:** You can use the [robots.txt file](http://www.robotstxt.org/robotstxt.html) to give instructions to search engine spiders. You can use the [.htaccess file](http://httpd.apache.org/docs/2.2/howto/htaccess.html) to implement advanced SEO strategies (URL rewriting, regex redirects, etc.). SEO Ultimate makes editing these files easier than ever.

* **How to use it:** Edit the files as desired, then click Save Changes. If you create a custom robots.txt file, be sure to enable it with the checkbox.

= FAQ =

* **Will my robots.txt edits remain if I disable the File Editor?**
	No. On a WordPress blog, the robots.txt file is dynamically generated just like your posts and Pages. If you disable the File Editor module or the entire SEO Ultimate plugin, the File Editor won't be able to insert your custom code into the robots.txt file anymore.

* **Will my .htaccess edits remain if I disable the File Editor?**
	Yes. The .htaccess file is static. Your edits will remain even if you disable SEO Ultimate or its File Editor module.

= Troubleshooting =

* **Why do I get a "500 Server Error" after using the File Editor?**
	You may have inserted code into your .htaccess file that your web server can't understand. As the File Editor warns, incorrectly editing your .htaccess file can disable your entire website in this way. To restore your site, you'll need to use an FTP client (or your web host's File Manager) to edit or rename your .htaccess file. If you need help, please contact your web host.

* **Where did my .htaccess edits go?**
	The .htaccess file is static, so SEO Ultimate doesn't have total control over it. It's possible that WordPress, another plugin, or other software may overwrite your .htaccess file. If you have a backup of your blog's files, you can try recovering your edits from there.



== Internal Relevance Researcher ==

= Overview =

* **What it does:** The Internal Relevance Researcher (IRR) opens Google search results in iframes based on the keywords you specify. For each keyword, IRR queries Google in this format: `site:example.com keyword`. IRR does _not_ scrape/crawl Google's search results or use other illicit automated methods; it just opens the Google search results in your browser.

* **Why it helps:** Internal Relevance Researcher lets you determine which of your webpages Google most strongly associates with the keywords you specify. You can ascertain this by observing which of your pages rank the highest for each keyword. You can then use this information to determine ideal targets for incoming links or ideal sources of outgoing links.

* **How to use it:** Enter the keywords you'd like to research, select options if desired, and then click Submit. The results will open in a new window.



== Linkbox Inserter ==

= Overview =

* **What it does:** Linkbox Inserter can add linkboxes to your posts/pages.

* **Why it helps:** Linkboxes contain HTML code that visitors can use to link to your site. This is a great way to encourage SEO-beneficial linking activity.

* **How to use it:** Use the checkboxes to enable the Linkbox Inserter in various areas of your site. Customize the HTML if desired. Click "Save Changes" when finished.


= Settings Help =

Here's information on the various settings:

* **Display linkboxes...**
	
	* **At the end of posts** -- Adds the linkbox HTML to the end of all posts (whether they're displayed on the blog homepage, in archives, or by themselves).
	
	* **At the end of pages** -- Adds the linkbox HTML to the end of all Pages.
	
	* **When called by the su_linkbox hook** -- For more fine-tuned control over where linkboxes appear, enable this option and add `<?php do_action('su_linkbox'); ?>` to your theme. You can also add an ID parameter to display the linkbox of a particular post/page; for example: `<?php do_action('su_linkbox', 123); ?>`
	
	* **HTML** -- The HTML that will be outputted to display the linkboxes. The HTML field supports these variables:
		
		* {id} -- The ID of the current post/page, or the ID passed to the action hook call.
		* {url} -- The permalink URL of the post/page.
		* {title} -- The title of the post/page.



== Meta Editor ==

= Overview =

* **What it does:** Meta Editor lets you customize a wide variety of settings known as "meta data."

* **Why it helps:** Using meta data, you can convey information to search engines, such as what text you want displayed by your site in search results, what your site is about, whether they can cache your site, etc.

* **How to use it:** Adjust the settings as desired, and then click Save Changes. You can refer to the "Settings Help" tab for information on the settings available. You can also customize the meta data of an individual post or page by using the textboxes that Meta Editor adds to the post/page editors.

= Settings Help =

Here's information on the various settings:

* **Blog Homepage Meta Description** -- When your blog homepage appears in search results, it'll have a title and a description. When you insert content into the description field below, the Meta Editor will add code to your blog homepage (the `<meta name="description" />` tag) that asks search engines to use what you've entered as the homepage's search results description.

* **Blog Homepage Meta Keywords** -- Here you can enter keywords that describe the overall subject matter of your entire blog. Use commas to separate keywords. Your keywords will be put in the `<meta name="keywords" />` tag on your blog homepage.

* **Default Values**
	
	* **Use this blog's tagline as the default homepage description.** -- If this box is checked and if the Blog Homepage Meta Description field is empty, Meta Editor will use your blog's tagline as the meta description. You can edit the blog's tagline under `Settings > General`.
	
* **Spider Instructions**
	
	* **Don't use this site's Open Directory / Yahoo! Directory description in search results.** -- If your site is listed in the [Open Directory (DMOZ)](http://www.dmoz.org/) or the [Yahoo! Directory](http://dir.yahoo.com/), some search engines may use your directory listing as the meta description. These boxes tell search engines not to do that and will give you full control over your meta descriptions. These settings have no effect if your site isn't listed in the Open Directory or Yahoo! Directory respectively.

	* **Don't cache or archive this site.** -- When you check this box, Meta Editor will ask search engines (Google, Yahoo!, Bing, etc.) and archivers (Archive.org, etc.) to _not_ make cached or archived "copies" of your site.

* **Verification Codes** -- This section lets you enter in verification codes for the webmaster portals of the 3 leading search engines.

* **Custom `<head>` HTML** -- Just enter in raw HTML code here, and it'll be entered into the `<head>` tag across your entire site.

= FAQ =

* **How do I edit the meta tags of my homepage?**
	If you are using a "blog homepage" (the default option of showing your blog posts on your homepage), go to `SEO > Meta Editor` and use the Blog Homepage fields.
  
	If you have configured your `Settings > Reading` section to use a "frontpage" (i.e. a Page as your homepage), just edit that Page under `Pages > Edit` and use the "Description" and "Keywords" fields in the "SEO Settings" box.

= Troubleshooting =

* **What do I do if my site has multiple meta tags?**
	First, try removing your theme's built-in meta tags if it has them. Go to `Appearance > Editor` and edit `header.php`. Delete or comment-out any `<meta>` tags.
  
	If the problem persists, try disabling other SEO plugins that may be generating meta tags.
  
	Troubleshooting tip: Go to `Settings > SEO Ultimate` and enable the "Insert comments around HTML code insertions" option. This will mark SEO Ultimate's meta tags with comments, allowing you to see which meta tags are generated by SEO Ultimate and which aren't.



== Module Manager ==

= Options =

The Module Manager lets you customize the visibility and accessibility of each module; here are the options available:

* **Enabled** -- The default option. The module will be fully enabled and accessible.
* **Silenced** -- The module will be enabled and accessible, but it won't be allowed to display numeric bubble alerts on the menu.
* **Hidden** -- The module's functionality will be enabled, but the module won't be visible on the SEO menu. You will still be able to access the module's admin page by clicking on its title in the Module Manager table.
* **Disabled** -- The module will be completely disabled and inaccessible.

= FAQ =

* **What are modules?**
	SEO Ultimate's features are divided into groups called "modules." SEO Ultimate's "Module Manager" lets you enable or disable each of these groups of features. This way, you can pick-and-choose which SEO Ultimate features you want.

* **Can I access a module again after I've hidden it?**
	Yes. Just go to the Module Manager and click the module's title to open its admin page. If you'd like to put the module back in the "SEO" menu, just re-enable the module in the Module Manager and click "Save Changes."

* **How do I disable the number bubbles on the "SEO" menu?**
	Just go to the Module Manager and select the "Silenced" option for any modules generating number bubbles. Then click "Save Changes."



== More Link Customizer ==

= Overview =

* **What it does:** More Link Customizer lets you modify the anchor text of your posts' ["more" links](http://codex.wordpress.org/Customizing_the_Read_More).

* **Why it helps:** On the typical WordPress setup, the "more link" always has the same anchor text (e.g. "Read more of this entry"). Since internal anchor text conveys web page topicality to search engines, the "read more" phrase isn't a desirable anchor phrase. More Link Customizer lets you replace the boilerplate text with a new anchor that, by default, integrates your post titles (which will ideally be keyword-oriented).

* **How to use it:** On this page you can set the anchor text you'd like to use by default. The `{post}` variable will be replaced with the post's title. HTML and encoded entities are supported. If instead you decide that you'd like to use the default anchor text specified by your currently-active theme, just erase the contents of the textbox. The anchor text can be overridden on a per-post basis via the "More Link Text" box in the "SEO Settings" section of the WordPress post editor.

= FAQ =

* **Why is the More Link Customizer an improvement over WordPress's built-in functionality?**
	Although WordPress does allow basic [custom "more" anchors](http://codex.wordpress.org/Customizing_the_Read_More#Having_a_custom_text_for_each_post), the SEO Ultimate approach has several benefits:

	* More Link Customizer (MLC) lets you set a custom default anchor text. WordPress, on the other hand, leaves this up to the currently-active theme.
	* MLC lets you dynamically incorporate the post's title into the anchor text.
	* MLC lets you include HTML tags in your anchor, whereas WordPress strips these out.
	* MLC's functionality is much more prominent than WordPress's unintuitive, barely-documented approach.
	* Unlike WordPress's method, MLC doesn't require you to utilize the HTML editor.

	If you've already specified custom anchors via WordPress's method, SEO Ultimate will import those anchors automatically into the More Link Customizer.



== Noindex Manager ==

= Overview =

* **What it does:** Noindex Manager lets you prohibit the search engine spiders from indexing certain pages on your blog using the `<meta name="robots" content="noindex" />` tag.

* **Why it helps:** This module lets you "noindex" pages that contain unimportant content (e.g. the login page), or pages that mostly contain duplicate content.

* **How to use it:** Adjust the settings as desired, and then click Save Changes. You can refer to the "Settings Help" tab for information on the settings available.

= Settings Help =

Here's information on the various settings:

* **Administration back-end pages** -- Tells spiders not to index the administration area (the part you're in now), in the unlikely event a spider somehow gains access to the administration. Recommended.

* **Author archives** -- Tells spiders not to index author archives. Useful if your blog only has one author.

* **Blog search pages** -- Tells spiders not to index the result pages of WordPress's blog search function. Recommended.

* **Category archives** -- Tells spiders not to index category archives. Recommended only if you don't use categories.

* **Comment feeds** -- Tells spiders not to index the RSS feeds that exist for every post's comments. (These comment feeds are totally separate from your normal blog feeds.)

* **Comment subpages** -- Tells spiders not to index posts' comment subpages.

* **Date-based archives** -- Tells spiders not to index day/month/year archives. Recommended, since these pages have little keyword value.

* **Subpages of the homepage** -- Tells spiders not to index the homepage's subpages (page 2, page 3, etc). Recommended.

* **Tag archives** -- Tells spiders not to index tag archives. Recommended only if you don't use tags.

* **User login/registration pages** -- Tells spiders not to index WordPress's user login and registration pages. Recommended.



== Slug Optimizer ==

= Overview =

* **What it does:** Slug Optimizer removes common words from the portion of a post's or Page's URL that is based on its title. (This portion is also known as the "slug.")

* **Why it helps:** Slug Optimizer increases keyword potency because there are fewer words in your URLs competing for relevance.

* **How to use it:** Slug Optimizer goes to work when you're editing a post or Page, with no action required on your part. If needed, you can use the textbox on the Slug Optimizer admin page to customize which words are removed.

= FAQ =

* **What's a slug?**
	The slug of a post or page is the portion of its URL that is based on its title.
	
	When you edit a post or Page in WordPress, the slug is the yellow-highlighted portion of the Permalink beneath the Title textbox.

* **Does the Slug Optimizer change my existing URLs?**
	No. Slug Optimizer will not relocate your content by changing existing URLs. Slug Optimizer only takes effect on new posts and pages.

* **How do I see Slug Optimizer in action?**
	1. Create a new post/Page in WordPress.
	2. Type in a title containing some common words.
	3. Click outside the Title box. WordPress will insert a URL labeled "Permalink" below the Title textbox. The Slug Optimizer will have removed the common words from the URL.

* **What if I want to include a common word in my slug?**
	When editing the post or page in question, just click the "Edit" button next to the permalink and change the slug as desired. The Slug Optimizer won't remove words from a manually-edited slug.

* **How do I revert back to the optimized slug after making changes?**
	When editing the post or page in question, just click the "Edit" button next to the permalink; a "Save" button will appear in its place. Next erase the contents of the textbox, and then click the aforementioned "Save" button.

= Troubleshooting =

* **Why didn't the Slug Optimizer remove common words from my slug?**
	It's possible that every word in your post title is in the list of words to remove. In this case, Slug Optimizer doesn't remove the words, because if it did, you'd end up with a blank slug.



== Title Rewriter ==

= Overview =

* **What it does:** Title Rewriter helps you customize the contents of your website's `<title>` tags. The tag contents are displayed in web browser title bars and in search engine result pages.

* **Why it helps:** Proper title rewriting ensures that the keywords in your post/Page titles have greater prominence for search engine spiders and users. This is an important foundation for WordPress SEO.

* **How to use it:** Title Rewriter enables recommended settings automatically, so you shouldn't need to change anything. If you do wish to edit the rewriting formats, you can do so using the textboxes below (the "Settings & Variables" tab includes additional information on this). You also have the option of overriding the `<title>` tag of an individual post or page by using the textboxes under the "Post" and "Page" tabs below, or by using the "Title Tag" textbox that Title Rewriter adds to the post/page editors.

= Settings & Variables =

Various variables, surrounded in {curly brackets}, are provided for use in the title formats. All settings support the {blog} variable, which is replaced with the name of the blog, and the {tagline} variable, which is replaced with the blog tagline as set under `Settings > General`.

Here's information on each of the settings and its supported variables:

* **Blog Homepage Title** -- Displays on the main blog posts page.

* **Post Title Format** -- Displays on single-post pages. Supports these variables:

	* {post} -- The post's title.
	* {category} -- The title of the post category with the lowest ID number.
	* {categories} -- A natural-language list of the post's categories (e.g. "Category A, Category B, and Category C").
	* {tags} -- A natural-language list of the post's tags (e.g. "Tag A, Tag B, and Tag C").
	* {author} -- The Display Name of the post's author.
	* {author\_username}, {author\_firstname}, {author\_lastname}, {author\_nickname} -- The username, first name, last name, and nickname of the post's author, respectively, as set in his or her profile.

* **Page Title Format** -- Displays on WordPress Pages. The {page} variable is replaced with the Page's title. Also supports the same author variables as the Post Title Format.

* **Category Title Format** -- Displays on category archives. The {category} variable is replaced with the name of the category, and {category\_description} is replaced with its description.

* **Tag Title Format** -- Displays on tag archives. The {tag} variable is replaced with the name of the tag, and {tag\_description} is replaced with its description.

* **Day Archive Title Format** -- Displays on day archives. Supports these variables:

	* {day} -- The day number, with ordinal suffix, e.g. 23rd
	* {daynum} -- The two-digit day number, e.g. 23
	* {month} -- The name of the month, e.g. April
	* {monthnum} -- The two-digit number of the month, e.g. 04
	* {year} -- The year, e.g. 2009
	
* **Month Archive Title Format** -- Displays on month archives. Supports {month}, {monthnum}, and {year}.

* **Year Archive Title Format** -- Displays on year archives. Supports the {year} variable.

* **Author Archive Title Format** -- Displays on author archives. Supports the same author variables as the Post Title Format box, i.e. {author}, {author\_username}, {author\_firstname}, {author\_lastname}, and {author\_nickname}.

* **Search Title Format** -- Displays on the result pages for WordPress's blog search function. The {query} variable is replaced with the search query as-is. The {ucwords} variable returns the search query with the first letter of each word capitalized.

* **404 Title Format** -- Displays whenever a URL doesn't go anywhere. Supports this variable:

	* {url_words} -- The words used in the error-generating URL. The first letter of each word will be capitalized.

* **Pagination Title Format** -- Displays whenever the visitor is on a subpage (page 2, page 3, etc). Supports these variables:

	* {title} -- The title that would normally be displayed on page 1.
	* {num} -- The current page number (2, 3, etc).
	* {max} -- The total number of subpages available. Would usually be used like this: Page {num} of {max}

= FAQ =

* **Does the Title Rewriter edit my post/page titles?**
	No. The Title Rewriter edits the `<title>` tags of your site, not your post/page titles.

* **What's the difference between the "title" and the "title tag" of a post/page?**
	The "title" is the title of your post or page, and is displayed on your site and in your RSS feed. The title is also used in your `<title>` tag by default; however, you can override the value of just the `<title>` tag by using the "Title Tag" field in the "SEO Settings" box.

= Troubleshooting =

* **Why isn't the Title Rewriter changing my `<title>` tags?**
	Try disabling other SEO plugins, as they may be conflicting with SEO Ultimate. Also, check to make sure your theme is [plugin-friendly](http://wordpress.jdwebdev.com/blog/theme-plugin-hooks/).


== Plugin Settings ==

= Overview =

The Settings module lets you manage settings related to the SEO Ultimate plugin as a whole.

= Global Settings Help =

Here's information on some of the settings:

* **Enable nofollow'd attribution link** -- If enabled, the plugin will display an attribution link on your site.

* **Notify me about unnecessary active plugins** -- If enabled, SEO Ultimate will add notices to your "Plugins" administration page if you have any other plugins installed whose functionality SEO Ultimate replaces.

* **Insert comments around HTML code insertions** -- If enabled, SEO Ultimate will use HTML comments to identify all code it inserts into your `<head>` tag. This is useful if you&#8217;re trying to figure out whether or not SEO Ultimate is inserting a certain piece of header code.

= FAQ =

* **Why doesn't the settings exporter include all my data in an export?** -- The settings export/import system is designed to facilitate moving settings between sites. It is NOT a replacement for keeping your database backed up. The settings exporter doesn't include data that is specific to your site. For example, logged 404 errors are not included because those 404 errors only apply to your site, not another site. Also, post/page titles/meta are not included because the site into which you import the file could have totally different posts/pages located under the same ID numbers.
	
	If you're moving a site to a different server or restoring a crashed site, you should do so with database backup/restore.


== Frequently Asked Questions ==

= General FAQ =

* **Why "SEO Ultimate" instead of "Ultimate SEO"?**
	Because "SEO Ultimate" works better as a brand name.

* **Where in WordPress does the plugin add itself?**
	SEO Ultimate puts all its admin pages under a new "SEO" top-level menu. The only exception is the plugin settings page, which goes under `Settings > SEO Ultimate`.

* **Where's the documentation?**
	SEO Ultimate's documentation is built into the plugin itself. Whenever you're viewing an SEO Ultimate page in your WordPress admin, you can click the "Help" tab in the upper-right-hand corner to view documentation for the area you're viewing.

* **How do I uninstall SEO Ultimate?**
	1. Go to the `Settings > SEO Ultimate` admin page and click the "Uninstall" tab.
	2. Click the "Uninstall Now" button and click "Yes" to confirm. SEO Ultimate's files and database entries will be deleted.

* **Will all my settings be deleted if I delete SEO Ultimate in the Plugins manager?**
	No. Your settings will be retained unless you uninstall SEO Ultimate under `Settings > SEO Ultimate > Uninstall`.

* **Where is the Plugin Settings page?**
	The plugin settings page is located under `Settings > SEO Ultimate`.

= "SEO Settings" box =

* **Where is the SEO Settings box located?**
	The SEO Settings box is located on WordPress's post/page editor underneath the content area.

* **How do I disable the "SEO Settings" box in the post/page editors?**
	Open the editor, click the "Screen Options" tab in the upper-right-hand corner, and uncheck the "SEO Settings" checkbox. Note that the box's visibility is a per-user preference.

* **Why did some of the textboxes disappear from the "SEO Settings" box?**
	The "SEO Settings" fields are added by your modules. The "Title Tag" field is added by the Title Rewriter module, the "Description" and "Keywords" fields are added by the Meta Editor module, etc. If you disable a module using the Module Manager, its fields in the "SEO Settings" box will be disabled too. You can re-enable the field in question by re-enabling the corresponding module.


= Module FAQ =

Frequently asked questions, documentation, and troubleshooting tips for SEO Ultimate's modules can be found on the [Other Notes](http://wordpress.org/extend/plugins/seo-ultimate/other_notes/) tab.



== Screenshots ==

1. The Module Manager lets you enable/disable SEO Ultimate features
2. The 404 Monitor log with "Screen Options" dropdown visible
3. The Canonicalizer module helps avoid duplicate content issues 
4. The Competition Researcher module
5. The Deeplink Juggernaut module
6. The File Editor module lets you edit your robots.txt and .htaccess files
7. The Internal Relevance Researcher module
8. The Linkbox Inserter module encourages natural linkbuilding activity
9. The Meta Editor module
10. The Noindex Manager module
11. The "Default Formats" tab of the Title Rewriter module
12. The "Pages" tab of the Title Rewriter module lets you edit Pages' <title> tags
13. The "Categories" tab of the Title Rewriter module lets you edit categories' <title> tags
14. The "SEO Settings" box, which is visible on post & page editors
15. The SEO Ultimate menu


== Changelog ==

= Version 2.3 (May 26, 2010) =
* Feature: Meta robots tags (index/noindex and follow/nofollow) can now be set for each post or page via the "SEO Settings" box
* Behavior Change: Since the Noindex Manager's advertised functionality is controlling the "noindex" attribute only, its behavior has been changed to output "noindex,follow" where it previously outputted "noindex,nofollow"

= Version 2.2 (May 24, 2010) =
* Feature: Deeplink Juggernaut now has a links-per-post limiter option
* Bugfix: The current tab is now maintained when submitting a tabbed form twice in a row
* Bugfix: When a module page reloads after submitting a tabbed form, the screen no longer jumps part-way down the page

= Version 2.1.1 (May 19, 2010) =
* Bugfix: Fixed "get_table_name" fatal error that appeared when upgrading certain configurations
* Bugfix: Restored missing success/error messages for import/reset functions

= Version 2.1 (May 18, 2010) =
* Improvement: Major 404 Monitor upgrade, featuring a new space-saving interface redesign
* Improvement: 404 Monitor now stores its 404 log in wp_options instead of its own database table
* Improvement: 404 Monitor now ignores apple-touch-icon.png 404s
* Improvement: Plugin now silently ignores a missing readme.txt instead of giving error
* Improvement: CSS and JavaScript now exist in separate, static files instead of being outputted by PHP files
* Improvement: SEO Ultimate settings now remain when plugin files are deleted; settings can now be deleted through new "Uninstall" function under `Settings > SEO Ultimate > Uninstall`
* Improvement: Database usage for the Whitepapers module reduced more than 90%
* Improvement: Users can now tab from a post's HTML editor directly into the "SEO Settings" fields
* Improvement: Removed blank admin CSS/JS file references
* Improvement: Added list of active modules to SEO Ultimate's plugin page listing
* Improvement: Added an "Uninstall" link to SEO Ultimate's plugin page listing
* Improvement: Update info notices now also visible under `Tools > Upgrade`
* Improvement: Added some missing documentation
* Improvement: Added/updated screenshots
* Improvement: Removed unused code
* Improvement: Added blank index.php files to module directories to prevent indexing/snooping of directory listings
* Feature: You can now hide 404 Monitor columns with the new "Screen Options" dropdown
* Bugfix: Removed duplicate excerpt ellipses from Whitepapers module
* Known Issue: If you had previously disabled 404 Monitor in version 2.0 or earlier, it will re-enable itself when upgrading to version 2.1 or later. The workaround is to re-disable 404 Monitor from the Module Manager after upgrading.

= Version 2.0 (April 29, 2010) =
* Feature: Title Rewriter can now edit the title tags of post tag archives

= Version 1.9 (April 3, 2010) =
* Feature: Title Rewriter can now edit the title tags of category archives

= Version 1.8.3 (March 30, 2010) =
* Bugfix: Fixed bug that caused disabled attribution link to display under certain circumstances

= Version 1.8.2 (March 29, 2010) =
* Bugfix: Fixed front-end Deeplink Juggernaut error

= Version 1.8.1 (March 27, 2010) =
* Bugfix: Fixed back-end Deeplink Juggernaut error

= Version 1.8 (March 27, 2010) =
* Feature: Added Deeplink Juggernaut beta module

= Version 1.7.3 (March 11, 2010) =
* Bugfix: Fixed variable name conflict introduced in 1.7.1 that disabled WordPress's plugin/theme editors

= Version 1.7.2 (March 6, 2010) =
* Bugfix: Fixed blank-admin-area bug in WordPress 3.0 alpha

= Version 1.7.1 (February 27, 2010) =
* Bugfix: Fixed conflict with Flexibility theme
* Bugfix: Comment administration no longer alters SEO Ultimate menu bubble counters
* Bugfix: SEO Ultimate menu icon is no longer accidentally added to other plugins' menus
* Bugfix: Disabling visitor logging now disables all related code as well
* Bugfix: Module Manager: Fixed invalid HTML IDs
* Bugfix: Module Manager: Module titles are now consistent between enabled and disabled states
* Bugfix: Module Manager: The "Silenced" option no longer disappears when all modules that support it are disabled
* Bugfix: Module Manager: The "Plugin Settings" module link no longer breaks when re-enabling that module
* Improvement: Added blank index.php files to additional plugin directories

= Version 1.7 (February 20, 2010) =
* Feature: Displays admin notices if blog privacy settings are configured to block search engines

= Version 1.6 (January 30, 2010) =
* Feature: Added All in One SEO Pack importer module

= Version 1.5.3 (January 27, 2010) =
* Bugfix: Fixed "get_parent_module_key" fatal error that appeared under limited circumstances
* Bugfix: Fixed "load_rss" fatal error that appeared under some circumstances
* Bugfix: Fixed broken image in the Whitepapers module

= Version 1.5.2 (January 25, 2010) =
* Bugfix: Uninstallation now works when the plugin is deactivated

= Version 1.5.1 (January 23, 2010) =
* Bugfix: Stopped the included Markdown library from "helpfully" functioning as a WordPress plugin
* Bugfix: Fixed error that appeared above changelog notices

= Version 1.5 (January 23, 2010) =
* Major under-the-hood changes and improvements
* Feature: Added new {url_words} title format variable to Title Rewriter
* Bugfix: Fixed broken link in the "SEO Settings" contextual help dropdown
* Improvement: Module documentation now loaded directly from the readme file (eliminates duplication)
* Improvement: Much more documentation now available from within the plugin
* Improvement: Module Manager now only shows the "Silenced" option for applicable modules
* Improvement: Cleaned root folder (now includes only the readme, screenshots, plugin file, POT file, and blank index.php)
* Improvement: Reduced database usage when saving post meta

= Version 1.4.1 (January 11, 2010) =
* Compatibility: Meta Editor now supports the new Google Webmaster Tools verification code

= Version 1.4 (December 16, 2009) =
* Feature: Added the Internal Relevance Researcher
* Bugfix: Title Rewriter no longer rewrites XML `<title>` tags in feeds
* Improvement: Copied all documentation to the readme.txt file

= Version 1.3 (November 13, 2009) =
* Feature: Added the More Link Customizer module
* Bugfix: Postmeta fields now handle HTML entities properly
* Improvement: Made minor tweaks to the Competition Researcher

= Version 1.2 (October 31, 2009) =
* Feature: Added the Competition Researcher module

= Version 1.1.2 (October 9, 2009) =
* Compatibility: Added PHP4 support

= Version 1.1.1 (October 8, 2009) =
* Bugfix: Fixed tab rendering bug

= Version 1.1 (October 7, 2009) =
* Feature: You can now mass-edit post/page titles from the Title Rewriter module
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
* Improvement: Canonicalizer now removes the duplicate canonical tags produced by WordPress 2.9-rare
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
