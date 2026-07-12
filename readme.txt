=== Quickfields Bulk Editor for ACF ===

Contributors: kilersam559

Donate link: https://samsyntax.com/quickfield

Tags: custom-fields, bulk-edit, advanced-custom-fields, spreadsheet, editor

Requires at least: 5.8

Tested up to: 7.0

Requires PHP: 7.4

Stable tag: 3.0.5

License: GPLv2 or later

License URI: https://www.gnu.org/licenses/gpl-2.0.html



Bulk edit Advanced Custom Fields across many pages in a spreadsheet-style sheet with autosave.



== Description ==



**Quickfields Bulk Editor for ACF** helps you update Advanced Custom Fields (ACF) content across many WordPress pages without opening each page one by one.



Built by [samsyntax](https://samsyntax.com). Pro add-on: [Quickfield Pro](https://samsyntax.com/quickfield). This plugin is not affiliated with or endorsed by Advanced Custom Fields.



Group related pages into **projects**, open a **sheet view** (pages as rows, ACF fields as columns), and bulk-edit text and textarea fields with autosave. Ideal for service pages, location pages, and any site where many pages share the same ACF structure.



= Features =



* Create unlimited projects

* Select WordPress pages per project

* Spreadsheet-style bulk editing for text and textarea fields

* Tabbed interface organized by ACF field group

* Read-only preview of image field values

* Clear text fields across all pages in a project

* Autosave as you type



= Requirements =



* WordPress 5.8 or higher

* [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (ACF Free or Pro)



== Installation ==



1. Upload the plugin folder to `/wp-content/plugins/` or install via the WordPress Plugins screen.

2. Activate **Quickfields Bulk Editor for ACF** through the **Plugins** menu.

3. Ensure **Advanced Custom Fields** is installed and active.

4. Go to **Quickfields Bulk Editor** in the admin menu to create your first project.



== Frequently Asked Questions ==



= Does this work without Advanced Custom Fields? =



No. The plugin detects and edits fields registered through Advanced Custom Fields.



= Is this an official Advanced Custom Fields plugin? =



No. Quickfields is developed by samsyntax and is not affiliated with or endorsed by Advanced Custom Fields.



= Which ACF field types are supported? =



The sheet supports editing text and textarea fields. Image fields are shown as read-only previews. Repeaters, flexible content, relationships, and other complex field types are not supported in the current version.



= Does deleting a project delete my page content? =



No. Deleting a project only removes the project grouping. All page content remains unchanged.



= Who can access the bulk editor? =



Only users with the `manage_options` capability (typically administrators).



== Screenshots ==



1. Projects list — create and manage bulk-editing projects

2. Sheet view — edit ACF text fields across pages in one table



== Changelog ==



= 3.0.5 =

* Renamed to Quickfields Bulk Editor for ACF (accurate free-plugin naming)

* Project page picker lists WordPress pages only

* Clarified Pro add-on messaging (separate plugin, not locked features)

* Activation hook registered from main plugin file



= 3.0.4 =

* Contributors set to WordPress.org username kilersam559



= 3.0.3 =

* Author and site updated to Sam Syntax (samsyntax.com)



= 3.0.2 =

* WordPress.org review compliance updates



= 3.0.1 =

* Renamed display name for ACF trademark compliance



= 3.0.0 =

* WordPress.org compliance: fully functional free bulk text/textarea editor

* Removed license-locked features from wp.org build



== Upgrade Notice ==



= 3.0.5 =

Renamed to Quickfields Bulk Editor for ACF. Project picker shows pages only.
