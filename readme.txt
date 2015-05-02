=== Plugin Name ===
Contributors: aryanduntley, dunar21
Plugin Name: WPR Admin Amplify
Donate link: http://worldpressrevolution.com/wpr_myplugins/wpr-admin-amplify/
Plugin URI: http://worldpressrevolution.com/wpr_myplugins/wpr-admin-amplify/
Author URI: http://worldpressrevolution.com/ 
Tags: membership, restricted content, protected content, custom post type, custom options page, custom fields, custom taxonomy, custom categories, custom post meta, custom forms,
Requires at least: 3.0.1
Tested up to: 4.2.1
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This provides an admin UI, based solely on wordpress' custom post type API, that allows you to create Custom Post Types, Custom Post Meta, User Categories, Membership/Restricted/Protected content, Custom Admin Pages/Options, Custom Taxonomies.  Soon, I will provide a UI for creating forms.

== Description ==
This plugin allows for general users to create custom admin settings pages, custom post types, custom taxonomies and custom post meta (custom fields ui).  It allows for user categories (how you use them is up to you) as well as membership / restricted content based solely on a custom membership taxonomy.  In order to use the membership/restricted content all you have to do is assign the membership taxonomy (after you create some terms just like post categories) to a post, page, menu item, media item, custom post type, ect...  Then that content is restricted unless a user has been assigned either that same membership category (term) or a parent membership category (parent term).  The membership/restricted content taxonomy is hierarchical so you can assign childred to the membership level and provide a user with parent priviledges which will allow them access to all child restricted content.  Media is also able to be restricted.  You can assign a media item the membership category (term) as well as click a checkbox to make it private.  When making it private, the media is sent to a protected folder that is inaccessible directly (by way of a direct url) or through the site.  In order to view the media or have access to it, a user must be logged in and have the correct privileges. Please note that when images are moved to the protected folder, all generated associated sizes are removed and only the main image is preserved.  You are able to restore the protected media back to the general access uploads folder.

#### DOCUMENTATION
There is documentation in regards to use and hooks in the plugin itself under the associated tabs.  In the future, I will provide more thorough documentation on my website.

#### BETA
This plugin is currently in beta.  I am releasing it for testing and feedback.  Eventually, I will begin supporting this plugin, making updates and improving the code and functionality.  After a while, I will add features and create a "Pro" version so that I am able to put real time into this (as long as it is useful enough that many people are using it and it begins to generate income).

$metafieldtypes = array("radio"=>"Radio Button", "select"=>"Select Box", "multiselect"=>"Multi Select", "multicheck"=>"Multi Checkboxes", "sidebars"=>"Sidebar List", "text"=>"Text Field", "noneditable"=>"Non-editable textd", "check"=>"Check Box", "multilist"=>"Multilist", "advancedlist"=>"Advanced List", "textarea"=>"Text Area", "textwys"=>"WYSIWYG Textarea", "datepicker"=>"Date Picker", "timepicker"=>"Time Picker", "separator"=>"Section Separator", "file"=>"File Chooser", "calltoaction"=>"Javascript Call To Action");

---
 
> #### Meta Fields
> The currently available meta filds are:
> * Radio Button
> * Select Box
> * Check Box
> * Text Area
> * WYSIWYG Textarea
> * File Chooser
> * Multi Select
> * Multi Checkboxes
> * Sidebar List
> * Text Field
> * Non-editable Text
> * Date Picker
> * Time Picker
> * Multilist (A dynamic list of text inputs)
> * Advanced List (A dynamic multidemensional list of text inputs)
> * Section Separator
> * Javascript Call To Action (documentation provided in plugin under Meta Fields Tab)
>
 
---


Plugin site: [WorldpressRevolution](http://worldpressrevolution.com/wpr_myplugins/wpr-admin-amplify/ "Aryan Duntley's Worldpress Revolution wordpress tutorials")


== Installation ==



1. Upload `wpr-general-posts` folder to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress



== Screenshots ==



Pending



== Changelog ==

= 0.0.1 =

* Initial release.



