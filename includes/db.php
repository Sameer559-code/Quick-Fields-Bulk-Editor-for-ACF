<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function bwsbfe_create_tables() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();
    $t = $wpdb->prefix . 'bwsbfe_projects';
    $sql = "CREATE TABLE IF NOT EXISTS $t (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        description text,
        page_ids longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

function bwsbfe_projects_table() {
    global $wpdb;
    return $wpdb->prefix . 'bwsbfe_projects';
}

function bwsbfe_get_projects() {
    global $wpdb;
    bwsbfe_create_tables();
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom projects table.
    return $wpdb->get_results(
        $wpdb->prepare( 'SELECT * FROM %i ORDER BY updated_at DESC', bwsbfe_projects_table() )
    );
}

function bwsbfe_get_project( $id ) {
    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom projects table.
    return $wpdb->get_row(
        $wpdb->prepare( 'SELECT * FROM %i WHERE id = %d', bwsbfe_projects_table(), $id )
    );
}

function bwsbfe_save_project( $data ) {
    global $wpdb;
    $table = bwsbfe_projects_table();
    bwsbfe_create_tables();
    if ( ! empty( $data['id'] ) ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom projects table update.
        $wpdb->update( $table, [
            'name'        => sanitize_text_field( $data['name'] ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'page_ids'    => maybe_serialize( $data['page_ids'] ?? [] ),
            'updated_at'  => current_time( 'mysql' ),
        ], [ 'id' => intval( $data['id'] ) ] );
        return intval( $data['id'] );
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom projects table insert.
    $wpdb->insert( $table, [
        'name'        => sanitize_text_field( $data['name'] ),
        'description' => sanitize_textarea_field( $data['description'] ?? '' ),
        'page_ids'    => maybe_serialize( $data['page_ids'] ?? [] ),
        'created_at'  => current_time( 'mysql' ),
        'updated_at'  => current_time( 'mysql' ),
    ] );
    return $wpdb->insert_id;
}

function bwsbfe_delete_project( $id ) {
    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom projects table delete.
    $wpdb->delete( bwsbfe_projects_table(), [ 'id' => intval( $id ) ] );
}

function bwsbfe_project_page_ids( $project ) {
    $ids = maybe_unserialize( $project->page_ids );
    return is_array( $ids ) ? array_map( 'intval', $ids ) : [];
}
