=== Cherry Sidebars ===

Contributors: TemplateMonster 2002
Tags: sidebar, sidebar manager, cherry framework, custom sidebars, widget area, group widgets, page custom sidebar, post custom sidebar, custom sidebar, dynamic sidebar
Requires at least: 4.5
Tested up to: 5.1.0
Stable tag: 1.1.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Cherry Sidebars allows to create and manage your own dynamic sidebars.

== Description ==

Cherry Sidebars is a lightweight plugin for creating multiple widget areas, and outputting them on posts or pages of your choosing. Now you don't have to limit yourself to one widget area, instead you can create as many as you like. Group your widgets into multiple areas, and pick which one you want to display for a certain post or page.
The plugin can be useful for certain posts or pages, where you want your content to be different, for example you need to add a banner ad to your popular post, or a bio section to the 'About' page. Cherry Sidebars would be a perfect tool for this task.

== Installation ==

1. Upload "Cherry Sidebars" folder to the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Navigate to the "Widgets" section to start customizing (Please notice! Some themes that do not support widgets may not have such page)

== Screenshots ==

1. An example of two custom sidebars with the widgets.
2. The form for creating a sidebar. Simply specify name and description for your new sidebar.
3. This is the sidebar manager panel that allows you to assign one of the sidebars to the current page.

== Configuration ==

= Creating a new sidebar =
1. Navigate to "Widgets" section (Please notice! Some themes that do not support widgets may not have such page).
2. Click on "Create a new sidebar" button.
3. Fill in all required form fields and click the "Create Sidebar" button.
4. After that you will be able to drag'n'drop your widgets into the new sidebar.

= Managing sidebars on pages =
* Open your page or post in editing mode.
* In the right bottom corner of the page you will see sidebar manager panel that allows you to assign one of the sidebars to the current page.
* For instance you can display widgets from the sidebar you've created in Primary Sidebar area.

== Changelog ==

= 1.1.3 =

* UPD: Cherry Framework

= 1.1.2 =

* UPD: cherry-framework update

= 1.1.1 =

* FIX: Prevent random placing sidebaras on migration to child

= 1.1.0 =

* ADD: Allow copy cutom sidebars into child theme on activation (add into parent theme functions.php add_theme_support( 'cherry_migrate_sidebars' );)
* FIX: Prevent errors on adding empty sidebar to page

= 1.0.5 =

* UPD: Admin panel interface
* UPD: Cherry Framework up to 1.3.1
* FIX: Cherry framework include
* FIX: error in php < 5.2

= 1.0.4 =

* FIX: Display sidebars in the widget list
* FIX: List sidebars on pages and posts

= 1.0.3 =

* UPD: Update Cherry Framework up to 1.1.1

= 1.0.2 =

* FIX: Custom sidebars option name

= 1.0.1 =

* FIX: sidebar_widgets filter should check WP_Query (WooCommerce Ajax navigation)
* FIX: assets initialization
* FIX: remove duplicates from allowed post types list

= 1.0.0 =

* Initial release
