<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Coerce a stored field value to a safe string for display/export.
 */
function bwsbfe_stringify_field_value( $raw ) {
    if ( is_array( $raw ) ) {
        if ( isset( $raw['ID'] ) ) {
            return (string) intval( $raw['ID'] );
        }
        return '';
    }

    if ( is_object( $raw ) ) {
        return '';
    }

    if ( ! is_string( $raw ) && ! is_numeric( $raw ) ) {
        return '';
    }

    return (string) $raw;
}

/**
 * Read a scalar ACF/meta value for the bulk sheet (raw, unformatted).
 */
function bwsbfe_read_field_value( $post_id, $field_name ) {
    $raw = get_post_meta( $post_id, $field_name, true );

    if ( ( $raw === '' || $raw === false ) && function_exists( 'get_field' ) ) {
        $raw = get_field( $field_name, $post_id, false );
    }

    return bwsbfe_stringify_field_value( $raw );
}

/**
 * Resolve attachment ID from an image field value.
 */
function bwsbfe_read_image_attachment_id( $post_id, $field_name ) {
    $raw = get_post_meta( $post_id, $field_name, true );

    if ( ( $raw === '' || $raw === false ) && function_exists( 'get_field' ) ) {
        $raw = get_field( $field_name, $post_id, false );
    }

    if ( is_array( $raw ) ) {
        return intval( $raw['ID'] ?? 0 );
    }

    return intval( $raw );
}

/**
 * Load all pages for a project in saved order.
 */
function bwsbfe_get_project_pages( $page_ids ) {
    $page_ids = array_values( array_filter( array_map( 'intval', (array) $page_ids ) ) );
    if ( empty( $page_ids ) ) {
        return [];
    }

    $pages = get_posts( [
        'post_type'           => 'page',
        'post_status'         => [ 'publish', 'draft', 'pending', 'private', 'future' ],
        'post__in'            => $page_ids,
        'orderby'             => 'post__in',
        'posts_per_page'      => count( $page_ids ),
        'ignore_sticky_posts' => true,
    ] );

    if ( count( $pages ) >= count( $page_ids ) ) {
        return $pages;
    }

    $by_id = [];
    foreach ( $pages as $page ) {
        $by_id[ $page->ID ] = $page;
    }

    foreach ( $page_ids as $page_id ) {
        if ( isset( $by_id[ $page_id ] ) ) {
            continue;
        }
        $post = get_post( $page_id );
        if ( $post && 'page' === $post->post_type ) {
            $by_id[ $page_id ] = $post;
        }
    }

    $ordered = [];
    foreach ( $page_ids as $page_id ) {
        if ( isset( $by_id[ $page_id ] ) ) {
            $ordered[] = $by_id[ $page_id ];
        }
    }

    return $ordered;
}

/**
 * Find a post by exact title (replaces deprecated get_page_by_title).
 *
 * @param string $title     Post title.
 * @param string $post_type Post type slug.
 * @return WP_Post|null
 */
function bwsbfe_find_post_by_title( $title, $post_type = 'page' ) {
    global $wpdb;

    $title = sanitize_text_field( $title );
    if ( $title === '' ) {
        return null;
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Exact title lookup; replaces deprecated get_page_by_title().
    $post_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1",
            $title,
            $post_type
        )
    );

    return $post_id ? get_post( (int) $post_id ) : null;
}

/**
 * Sanitize a field value while preserving safe HTML where appropriate.
 */
function bwsbfe_sanitize_field_value( $field_name, $value ) {
    $value = is_string( $value ) ? $value : (string) $value;

    if ( function_exists( 'acf_get_field' ) ) {
        $field_obj = acf_get_field( $field_name );
        if ( $field_obj && ! empty( $field_obj['type'] ) ) {
            if ( in_array( $field_obj['type'], [ 'wysiwyg', 'textarea' ], true ) ) {
                return wp_kses_post( $value );
            }
            if ( $field_obj['type'] === 'url' ) {
                return esc_url_raw( $value );
            }
            return sanitize_text_field( $value );
        }
    }

    if ( preg_match( '/<[^>]+>/', $value ) ) {
        return wp_kses_post( $value );
    }

    return sanitize_textarea_field( $value );
}

/**
 * Map ACF field definition to editor type.
 */
function bwsbfe_map_field_type( $acf_type ) {
    if ( in_array( $acf_type, [ 'image', 'file' ], true ) ) {
        return 'image';
    }
    if ( in_array( $acf_type, [ 'textarea', 'wysiwyg' ], true ) ) {
        return 'textarea';
    }
    return 'text';
}

/**
 * Collect ACF field groups (and fields) across one or more pages.
 */
function bwsbfe_collect_field_groups( $page_ids = [] ) {
    if ( ! function_exists( 'acf_get_field_groups' ) ) {
        return [];
    }

    $groups          = [];
    $seen_group_keys = [];

    if ( empty( $page_ids ) ) {
        $scan_ids = [ null ];
    } else {
        // One representative page is enough — field groups are location-rule based.
        $scan_ids = [ (int) reset( $page_ids ) ];
    }

    foreach ( $scan_ids as $post_id ) {
        $args = $post_id ? [ 'post_id' => $post_id ] : [];

        foreach ( acf_get_field_groups( $args ) as $group ) {
            if ( isset( $seen_group_keys[ $group['key'] ] ) ) {
                continue;
            }
            $seen_group_keys[ $group['key'] ] = true;

            $fields = acf_get_fields( $group['key'] );
            if ( empty( $fields ) ) {
                continue;
            }

            $group_fields = [];
            foreach ( $fields as $field ) {
                $group_fields[ $field['name'] ] = [
                    'label' => $field['label'],
                    'type'  => bwsbfe_map_field_type( $field['type'] ),
                    'key'   => $field['key'],
                ];
            }

            if ( ! empty( $group_fields ) ) {
                $title = $group['title'];
                if ( isset( $groups[ $title ] ) ) {
                    $groups[ $title ] = array_merge( $groups[ $title ], $group_fields );
                } else {
                    $groups[ $title ] = $group_fields;
                }
            }
        }
    }

    return $groups;
}

/**
 * Dynamically detect ACF field groups assigned to pages
 * and return fields grouped by their ACF group label.
 */
function bwsbfe_get_page_fields( $page_ids = [] ) {
    return bwsbfe_collect_field_groups( $page_ids );
}

/**
 * Verify the current user may edit a post in the sheet.
 */
function bwsbfe_user_can_edit_sheet_page( $page_id ) {
    $page_id = intval( $page_id );
    return $page_id > 0 && current_user_can( 'edit_post', $page_id );
}

/**
 * Verify a field name is a registered ACF field (optional type: text, textarea, image).
 */
function bwsbfe_verify_acf_field( $field_name, $expected_type = null ) {
    if ( ! function_exists( 'acf_get_field' ) ) {
        return false;
    }

    $field_obj = acf_get_field( $field_name );
    if ( ! is_array( $field_obj ) || empty( $field_obj['name'] ) ) {
        return false;
    }

    if ( $expected_type ) {
        return bwsbfe_map_field_type( $field_obj['type'] ?? '' ) === $expected_type;
    }

    return true;
}

/**
 * Clear ACF field values for every page in a project.
 *
 * @param int  $project_id
 * @param bool $exclude_images When true, image/file fields are left unchanged.
 */
function bwsbfe_clear_project_fields( $project_id, $exclude_images = false ) {
    $project = bwsbfe_get_project( $project_id );
    if ( ! $project ) {
        return [ 'success' => false, 'message' => 'Project not found.' ];
    }

    $page_ids = bwsbfe_project_page_ids( $project );
    if ( empty( $page_ids ) ) {
        return [ 'success' => false, 'message' => 'No pages in this project.' ];
    }

    $field_groups = bwsbfe_get_page_fields( $page_ids );
    if ( empty( $field_groups ) ) {
        return [ 'success' => false, 'message' => 'No ACF fields detected for this project.' ];
    }

    $cleared = 0;

    foreach ( $page_ids as $page_id ) {
        foreach ( $field_groups as $fields ) {
            foreach ( $fields as $field_name => $field_def ) {
                if ( $exclude_images && ( $field_def['type'] ?? '' ) === 'image' ) {
                    continue;
                }

                delete_post_meta( $page_id, $field_name );
                delete_post_meta( $page_id, '_' . $field_name );

                if ( function_exists( 'update_field' ) && ! empty( $field_def['key'] ) ) {
                    update_field( $field_def['key'], '', $page_id );
                }

                $cleared++;
            }
        }
    }

    $scope = $exclude_images ? 'text field' : 'field';

    return [
        'success' => true,
        'message' => sprintf(
            'Cleared %d %s value%s across %d page%s.',
            $cleared,
            $scope,
            $cleared !== 1 ? 's' : '',
            count( $page_ids ),
            count( $page_ids ) !== 1 ? 's' : ''
        ),
    ];
}
