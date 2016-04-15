=== Plugin Name ===
Contributors: toxnico
Donate link: http://example.com/
Tags: security, login
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin prevents unwanted people to access your login page, and it can also disable XMLRPC it you don't need it.

== Description ==

Did you ever feel like your website or blog login page is ridiculously fragile and reachable, and could be easily broken in by an intruder?

Personally I hate to think of hundreds of people playing with my door lock hundreds of times a day. It's the same with my blog login page.

On Wordpress, there are two main potential vectors of bruteforce intrusion:
*  http://my-site.com/wp-login.php, which is the login page
*  http://my-site.com/xmlrpc.php, which is an API for interacting with third party applications.

This plugin adds one security layer in front of your login page, and by the way you can also disable XML-RPC with a simple checkbox if you don't need it (XML-RPC is a *WIDELY* used vector of attacks).

The idea is simple: you choose a pair of words, and when you want to access your login page, you just provide them in the URL like this: http://my-site.com/wp-login.php?word1=word2. That's all!
If you try to access your login page without this pair of words, you get a configurable error message, where you can insult the attacker as much as you want ;)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wplogindoor` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Wp Login Door screen to configure the plugin
1. Enjoy your new door :)

== Frequently Asked Questions ==

= What if I lose my pair of words? =

You can disable the plugin from your FTP server.
Then login as usual, reactivate the plugin, and check your word pair.

= Is that all ? =

Yes!

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* First release!



///////////////////////////
Code Canyon
///////////////////////////


I've been developing software for 15 years in several domains and languages, and as a Wordpress blog owner, I have some needs and I develop my own plugins. I discovered CodeCanyon recently and decided to package my hand crafted plugins correctly to distribute them!