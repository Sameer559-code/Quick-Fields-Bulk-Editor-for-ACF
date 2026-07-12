<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_bwsbfe_save_cell', function () {
    check_ajax_referer( 'bwsbfe_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $pid   = intval( $_POST['page_id'] ?? 0 );
    $field = sanitize_key( $_POST['field_name'] ?? '' );
    if ( ! $pid || ! $field ) {
        wp_send_json_error( 'Missing data' );
    }
    if ( ! bwsbfe_user_can_edit_sheet_page( $pid ) ) {
        wp_send_json_error( 'Unauthorized' );
    }
    if ( ! bwsbfe_verify_acf_field( $field, 'text' ) && ! bwsbfe_verify_acf_field( $field, 'textarea' ) ) {
        wp_send_json_error( 'Invalid field' );
    }

    $raw_value = filter_input( INPUT_POST, 'value' );
    if ( ! is_string( $raw_value ) ) {
        $raw_value = '';
    } else {
        $raw_value = wp_unslash( $raw_value );
    }
    $val = bwsbfe_sanitize_field_value( $field, $raw_value );
    update_post_meta( $pid, $field, $val );
    if ( function_exists( 'update_field' ) ) {
        $field_obj = acf_get_field( $field );
        if ( $field_obj ) {
            update_field( $field_obj['key'], $val, $pid );
        }
    }
    wp_send_json_success();
} );

add_action( 'wp_ajax_bwsbfe_save_project', function () {
    check_ajax_referer( 'bwsbfe_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $page_ids = [];
    if ( isset( $_POST['page_ids'] ) ) {
        $raw_ids = sanitize_text_field( wp_unslash( $_POST['page_ids'] ) );
        if ( $raw_ids !== '' ) {
            $page_ids = array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) );
        }
    }
    // Only allow real WordPress pages (exclude attachments, templates, etc.).
    $page_ids = array_values( array_filter( $page_ids, function ( $id ) {
        return get_post_type( $id ) === 'page';
    } ) );

    $id = bwsbfe_save_project( [
        'id'          => intval( $_POST['id'] ?? 0 ),
        'name'        => sanitize_text_field( wp_unslash( $_POST['name'] ?? 'Untitled Project' ) ),
        'description' => sanitize_textarea_field( wp_unslash( $_POST['description'] ?? '' ) ),
        'page_ids'    => $page_ids,
    ] );
    wp_send_json_success( [ 'id' => $id ] );
} );

add_action( 'wp_ajax_bwsbfe_delete_project', function () {
    check_ajax_referer( 'bwsbfe_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }
    bwsbfe_delete_project( intval( $_POST['id'] ?? 0 ) );
    wp_send_json_success();
} );

add_action( 'wp_ajax_bwsbfe_clear_fields', function () {
    check_ajax_referer( 'bwsbfe_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $project_id = intval( $_POST['project_id'] ?? 0 );
    if ( ! $project_id ) {
        wp_send_json_error( 'Missing project ID' );
    }

    $result = bwsbfe_clear_project_fields( $project_id, true );
    if ( $result['success'] ) {
        wp_send_json_success( $result );
    }
    wp_send_json_error( $result['message'] ?? 'Clear failed' );
} );
