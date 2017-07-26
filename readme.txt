=== LMB^Box WordPress Plugin API ===
Contributors: lmbbox
Donate link: 
Tags: plugin, plugins, development, api, framework
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 0.2

A WordPress Plugin API class that allows plugin developers to have a standard management class for their plugin.

== Description ==

A WordPress Plugin API class that allows plugin developers to have a standard management class for their plugin. All options and menu pages are handled through the plugin api class.
I have included a test plugin to demonstrate usage of the API. More documentation will come ...

== Installation ==

1. Upload `lmbbox-wordpress-plugin-api` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. N/A

== Change Log ==

= 0.2 =

Changed class public options variable to private __options variable.
Renamed all protected functions and variables from __* to _*. All private functions and variables are __*. Added _add_hook, _remove_hook functions, and _setup_hooks. Updated lmbbox-test.php plugin.
Added Widget handling. Add _get_options function. Updated _update_options function to fix checkbox option updating.

= 0.1 =

Inital Version
