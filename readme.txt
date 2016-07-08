=== Tabs Widget for Page Builder ===
Contributors: capuderg, cyman
Tags: tabs, widget, Page Builder by SiteOrigin, SiteOrigin, Bootstrap, ProteusThemes
Requires at least: 4.0.0
Tested up to: 4.5.3
Stable tag: 1.2.1
License: GPLv3 or later

Adds a "Tabs for Page Builder" widget, which can be used in Page Builder by SiteOrigin editor.

== Description ==

Page Builder by SiteOrigin editor is great, but it lacks a "tabs widget", so we created one.

The "Tabs for Page Builder" widget allows you to add multiple tabs with a tab name and tab content. The tab content is using the Page Builder editor, so you can build your own layout inside the tab content (add widgets, set columns, ...).

The front-end display is the same as the tabs in Bootstrap framework.

[More details about this plugin can be found in this post](http://gregorcapuder.com/tabs-widget-for-page-builder/).

Note: the front-end design is "bare bones", so only the default Bootstrap CSS is used. So you should style it to your own liking with custom CSS. Maybe we will add some custom skins in the future, but I can't make any promises.

== Installation ==

Upload the Tabs Widget for Page Builder plugin to your WordPress site, Activate it, and that's it.

Once you activate it, the "Tabs Widget for Page Builder" widget will be available in your Page Builder editor.

== Changelog ==

= 1.2.1 =
*8 July 2016*

* Added a filter, that enables older Twitter Bootstrap tabs layout.

= 1.2.0 =
*8 April 2016*

* Tab settings are now sortable

= 1.1.2 =
*27 March 2016*

* Improved the the front-end tab ids reg-ex, to remove special characters

= 1.1.1 =
*25 March 2016*

* Fixed issue with front-end tab ids if the tab titles were not alphanumeric (Cyrillic  characters, for example)

= 1.1.0 =
*20 March 2016*

* tabs widget now also works in WordPress sidebars (not only in Page Builder editor),
* tabs widget now works inside another tabs widget,
* front-end IDs now use tab titles (no more random strings)

= 1.0.0 =
*Release Date - 19 January 2016*

* Initial version of the plugin

== Frequently Asked Questions ==

= The tabs widget is not working properly? =

If for example the first tab always remains active, than this is an indicator that your theme (or another plugin) is using an older version of Twitter Bootstrap. In this plugin we use the Bootstrap version 4.x, so some HTML is different from the older versions (v3.x, v2.x). You can solve this by adding the bellow code to your theme *functions.php* file:

`add_filter( 'pt-tabs/use_older_bootstrap_layout', '__return_true' );`

Also make sure that you have the latest version of this Tabs widget plugin.

== Screenshots ==

1. Front-end display of the widget.
2. Front-end display of the widget in the theme Beauty by ProteusThemes.
3. Widget back-end.