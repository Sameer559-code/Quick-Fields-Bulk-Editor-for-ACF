<?php
/**
 * Plugin Name: Quickfields Bulk Editor for ACF
 * Description: Project-based bulk editor for Advanced Custom Fields — spreadsheet-style text editing with autosave across many pages.
 * Version: 3.0.5
 * Author: samsyntax
 * Author URI: https://samsyntax.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quickfields-bulk-editor-for-acf
 * Requires Plugins: advanced-custom-fields
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BWSBFE_PATH', plugin_dir_path( __FILE__ ) );
define( 'BWSBFE_URL', plugin_dir_url( __FILE__ ) );
define( 'BWSBFE_VERSION', '3.0.5' );

require_once BWSBFE_PATH . 'includes/db.php';
require_once BWSBFE_PATH . 'includes/fields.php';
require_once BWSBFE_PATH . 'includes/ajax.php';
require_once BWSBFE_PATH . 'includes/admin.php';

register_activation_hook( __FILE__, 'bwsbfe_create_tables' );
