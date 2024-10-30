=== Best Post Page ===
Contributors: mfisher5kavika
Donate link: http://kelohe.com/bestofpage/
Tags: best, posts
Requires at least: 2.3
Tested up to: 2.9
Stable tag: 1.3

Best Post Page is a WordPress plugin, that utilizes optimization algorithms to chose the best posts from your blog.

== Description ==

Best Post Page is a WordPress plugin, that utilizes optimization algorithms to chose the best posts based 
on criteria such as views and comments. This plugin automatically generates a page called 'Best of Posts' that 
displays the top 10 posts from your blog. The list of the best posts is automatically recalculated. 
All you have to do is download and activate the plugin.

v1.2 - Added admin page for configuration of page.   
v1.3 - Greatly improved performance when using WP-ShortStats.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `bestpost.php` to the `/wp-content/plugins/` directory.  
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use admin page to configure the number of posts and the learning rate.


== Frequently Asked Questions ==

= What else needs to be installed? =

WP-ShortStats *should* be installed before this plugin can be activated.  This plugin tries to use the statistics from WP-SS to optimize the posts.

= Are there any tags that need to be placed in posts? =

No, just install the plugin and the page will automatically display the 'best of posts'.

= Do I need to create the 'best of posts' page? =

No, just install the plugin and the page will be created automatically.

= What happens to the 'best of posts' page if I deactivate the plugin? =

The 'best of posts' page will be deleted.  Nothing will be lost, the data is dynamically created so just reactivate the plugin and the 'best of posts' page will be back.

= Are their any API calls made? =

Yes, the plugin calls an API with data about your posts. The API service uses an optimization algorithm to determine the best 10 posts that should be displayed. This list is sent back to the page to be displayed. This is done server side so the client (browser) is not part of the API call.

= Can I change the title or content of the 'Best of Posts' page? =

Yes, you can change the title or the content (default content = 'This is a list of the best posts.') but you cannot change the permalink (what shows in the URL when you are on the page http://yourblog/best-of-posts/)

== Screenshots ==

1. This is a screenshot of a blog with the "Best of Posts" page on the right side under Pages. 
2. This is a screenshot of the actual "Best of Posts" page with the best 10 posts listed as permalinks to the posts.


