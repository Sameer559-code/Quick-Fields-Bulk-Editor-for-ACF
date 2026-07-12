# Quickfields Bulk Editor for ACF

Bulk edit Advanced Custom Fields across many WordPress pages in a spreadsheet-style sheet with autosave.

**Requires:** [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (Free or Pro)  
**Author:** [samsyntax](https://samsyntax.com)  
**License:** GPLv2 or later  
**Pro add-on:** [Quickfield Pro](https://samsyntax.com/quickfield)

This plugin is not affiliated with or endorsed by Advanced Custom Fields.

## Features

- Create unlimited projects
- Select WordPress pages per project
- Spreadsheet-style bulk editing for text and textarea fields
- Tabbed interface organized by ACF field group
- Read-only preview of image field values
- Clear text fields across all pages in a project
- Autosave as you type

## Requirements

| Requirement | Version |
|-------------|---------|
| WordPress   | 5.8+    |
| PHP         | 7.4+    |
| ACF         | Free or Pro |

## Installation

1. Download or clone this repository into `/wp-content/plugins/`.
2. Activate **Quickfields Bulk Editor for ACF** under **Plugins**.
3. Ensure **Advanced Custom Fields** is installed and active.
4. Open **Quickfields Bulk Editor** in the admin menu to create your first project.

## Usage

1. Create a **project** and assign the WordPress pages you want to edit together.
2. Open the **sheet view** — pages as rows, ACF fields as columns.
3. Edit text and textarea fields; changes autosave as you type.
4. Use field-group tabs to switch between ACF groups on those pages.

## Supported field types

| Type     | Support              |
|----------|----------------------|
| Text     | Editable             |
| Textarea | Editable             |
| Image    | Read-only preview    |

Repeaters, flexible content, relationships, and other complex field types are not supported in this version.

## FAQ

**Does this work without ACF?**  
No. The plugin detects and edits fields registered through Advanced Custom Fields.

**Is this an official ACF plugin?**  
No. Quickfields is developed by samsyntax and is not affiliated with or endorsed by Advanced Custom Fields.

**Does deleting a project delete page content?**  
No. Deleting a project only removes the project grouping. Page content stays unchanged.

**Who can access the bulk editor?**  
Users with the `manage_options` capability (typically administrators).

## Pro add-on

The free plugin fully supports bulk text editing. CSV import/export, in-sheet image editing, and clearing image fields are available in the separate [Quickfields Pro](https://samsyntax.com/quickfield) add-on — not locked inside this plugin.

## Changelog

### 3.0.5

- Renamed to Quickfields Bulk Editor for ACF
- Project page picker lists WordPress pages only
- Clarified Pro add-on messaging
- Activation hook registered from main plugin file

See [`readme.txt`](readme.txt) for the full WordPress.org changelog.

## Support

- Website: [samsyntax.com](https://samsyntax.com)
- Pro / donate: [Quickfield Pro](https://samsyntax.com/quickfield)

## License

GPLv2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for details.
