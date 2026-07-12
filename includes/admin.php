<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function bwsbfe_pro_add_on_url() {
    return apply_filters( 'bwsbfe_pro_add_on_url', 'https://samsyntax.com/products/quickfields' );
}

/**
 * Whether to show the Pro add-on promo banner (hide when separate Pro plugin is active).
 */
function bwsbfe_should_show_pro_banner() {
    return (bool) apply_filters( 'bwsbfe_show_pro_banner', true );
}

/**
 * ACF-style promo banner for the separate Quickfields Pro add-on (wp.org compliant — links only).
 */
function bwsbfe_render_pro_banner() {
    if ( ! bwsbfe_should_show_pro_banner() ) {
        return;
    }

    $pro_url = bwsbfe_pro_add_on_url();
    $features = [
        [ 'icon' => 'dashicons-download',       'label' => __( 'CSV Export', 'quickfields-bulk-editor-for-acf' ) ],
        [ 'icon' => 'dashicons-upload',       'label' => __( 'CSV Import', 'quickfields-bulk-editor-for-acf' ) ],
        [ 'icon' => 'dashicons-format-image', 'label' => __( 'Image Upload', 'quickfields-bulk-editor-for-acf' ) ],
        [ 'icon' => 'dashicons-update',       'label' => __( 'Image Replace', 'quickfields-bulk-editor-for-acf' ) ],
        [ 'icon' => 'dashicons-trash',        'label' => __( 'Clear Including Images', 'quickfields-bulk-editor-for-acf' ) ],
        [ 'icon' => 'dashicons-table-col-after', 'label' => __( 'Excel Workflow', 'quickfields-bulk-editor-for-acf' ) ],
    ];
    ?>
    <div class="bwsbfe-pro-hero bwsbfe-pro-hero--bottom">
        <div class="bwsbfe-pro-hero__inner">
            <div class="bwsbfe-pro-hero__copy">
                <h2 class="bwsbfe-pro-hero__title">
                    <?php esc_html_e( 'Get More with the Separate Quickfields Pro Add-on', 'quickfields-bulk-editor-for-acf' ); ?>
                    <span class="bwsbfe-pro-hero__badge">PRO</span>
                </h2>
                <p class="bwsbfe-pro-hero__lead">
                    <?php esc_html_e( 'This free plugin fully supports bulk text editing. CSV import/export, in-sheet image editing, and clearing image fields are available in a separate Pro add-on sold on our website — not locked inside this plugin.', 'quickfields-bulk-editor-for-acf' ); ?>
                </p>
                <div class="bwsbfe-pro-hero__actions">
                    <a href="<?php echo esc_url( $pro_url ); ?>" class="bwsbfe-pro-hero__btn bwsbfe-pro-hero__btn--ghost" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Learn More', 'quickfields-bulk-editor-for-acf' ); ?>
                        <span class="bwsbfe-pro-hero__arrow" aria-hidden="true">↗</span>
                    </a>
                    <a href="<?php echo esc_url( $pro_url ); ?>" class="bwsbfe-pro-hero__btn bwsbfe-pro-hero__btn--primary" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'View Pricing & Upgrade', 'quickfields-bulk-editor-for-acf' ); ?>
                        <span class="bwsbfe-pro-hero__arrow" aria-hidden="true">↗</span>
                    </a>
                </div>
            </div>
            <div class="bwsbfe-pro-hero__grid" role="list">
                <?php foreach ( $features as $feature ) : ?>
                <div class="bwsbfe-pro-hero__tile" role="listitem">
                    <span class="bwsbfe-pro-hero__tile-icon dashicons <?php echo esc_attr( $feature['icon'] ); ?>" aria-hidden="true"></span>
                    <span class="bwsbfe-pro-hero__tile-label"><?php echo esc_html( $feature['label'] ); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bwsbfe-pro-hero__foot">
            <span><?php esc_html_e( 'Built for WordPress developers by', 'quickfields-bulk-editor-for-acf' ); ?> <strong>samsyntax</strong></span>
            <a href="<?php echo esc_url( $pro_url ); ?>" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e( 'More from Quickfields', 'quickfields-bulk-editor-for-acf' ); ?> ↗
            </a>
        </div>
    </div>
    <?php
}

function bwsbfe_render_image_cell( $wpid, $thumb, $field_name = '' ) {
    $custom = apply_filters( 'bwsbfe_image_cell_html', null, $wpid, $thumb, $field_name );
    if ( null !== $custom ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered HTML from Pro add-on.
        echo $custom;
        return;
    }
    ?>
    <div class="bwsbfe-img-wrap bwsbfe-img-wrap--readonly">
        <div class="bwsbfe-thumb <?php echo esc_attr( $thumb ? 'has-img' : '' ); ?>">
            <?php echo $thumb ? '<img src="' . esc_url( $thumb ) . '" alt="" />' : '<span>' . esc_html__( 'No image', 'quickfields-bulk-editor-for-acf' ) . '</span>'; ?>
        </div>
        <p class="bwsbfe-img-hint"><?php esc_html_e( 'Read-only preview. Edit images in the post editor.', 'quickfields-bulk-editor-for-acf' ); ?></p>
    </div>
    <?php
}

function bwsbfe_render_brand_footer() {
    ?>
    <p class="bwsbfe-brand-footer">
        <?php
        printf(
            /* translators: %s: author website URL */
            wp_kses_post( __( 'Quickfields by samsyntax — <a href="%s" target="_blank" rel="noopener noreferrer">Learn more</a>', 'quickfields-bulk-editor-for-acf' ) ),
            esc_url( 'https://samsyntax.com/products/quickfields' )
        );
        ?>
    </p>
    <?php
}

add_action( 'admin_menu', function () {
    add_menu_page(
        __( 'Quickfields Bulk Editor', 'quickfields-bulk-editor-for-acf' ),
        __( 'Quickfields Bulk Editor', 'quickfields-bulk-editor-for-acf' ),
        'manage_options',
        'bwsbfe',
        'bwsbfe_router',
        'dashicons-table-col-before',
        80
    );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'toplevel_page_bwsbfe' ) {
        return;
    }
    wp_enqueue_style( 'bwsbfe-style', BWSBFE_URL . 'includes/style.css', [ 'dashicons' ], BWSBFE_VERSION );
    wp_enqueue_script( 'bwsbfe-script', BWSBFE_URL . 'includes/editor.js', [ 'jquery' ], BWSBFE_VERSION, true );
    wp_localize_script( 'bwsbfe-script', 'bwsbfeData', apply_filters( 'bwsbfe_script_data', [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'bwsbfe_nonce' ),
        'baseUrl'   => admin_url( 'admin.php?page=bwsbfe' ),
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin screen context for JS.
        'projectId' => isset( $_GET['project'] ) ? intval( $_GET['project'] ) : 0,
    ] ) );
} );

function bwsbfe_router() {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen routing only.
    $view = sanitize_key( wp_unslash( $_GET['view'] ?? 'projects' ) );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen routing only.
    $pid  = isset( $_GET['project'] ) ? intval( $_GET['project'] ) : 0;
    if ( $view === 'sheet' && $pid ) {
        $project = bwsbfe_get_project( $pid );
        if ( $project ) {
            bwsbfe_render_sheet( $project );
            return;
        }
    }
    if ( $view === 'new' || ( $view === 'edit' && $pid ) ) {
        bwsbfe_render_project_form( $pid ? bwsbfe_get_project( $pid ) : null );
        return;
    }
    bwsbfe_render_projects_list();
}

function bwsbfe_render_projects_list() {
    $projects = bwsbfe_get_projects();
    ?>
    <div class="wrap bwsbfe-wrap">
        <div class="bwsbfe-header">
            <div>
                <h1><?php esc_html_e( 'Quickfields Bulk Editor for ACF', 'quickfields-bulk-editor-for-acf' ); ?></h1>
                <p><?php esc_html_e( 'Create projects to bulk-edit ACF text and textarea fields across multiple pages.', 'quickfields-bulk-editor-for-acf' ); ?></p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe&view=new' ) ); ?>" class="bwsbfe-btn-primary"><?php esc_html_e( '+ New Project', 'quickfields-bulk-editor-for-acf' ); ?></a>
        </div>

        <?php if ( empty( $projects ) ) : ?>
        <div class="bwsbfe-empty">
            <div class="bwsbfe-empty-icon">📂</div>
            <h2><?php esc_html_e( 'No projects yet', 'quickfields-bulk-editor-for-acf' ); ?></h2>
            <p><?php esc_html_e( 'Create a project to start editing page content in bulk.', 'quickfields-bulk-editor-for-acf' ); ?></p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe&view=new' ) ); ?>" class="bwsbfe-btn-primary"><?php esc_html_e( '+ Create First Project', 'quickfields-bulk-editor-for-acf' ); ?></a>
        </div>
        <?php else : ?>
        <div class="bwsbfe-project-grid">
            <?php foreach ( $projects as $p ) :
                $count = count( bwsbfe_project_page_ids( $p ) );
                ?>
            <div class="bwsbfe-project-card">
                <div class="bwsbfe-project-card-top">
                    <div class="bwsbfe-project-icon">📋</div>
                    <div class="bwsbfe-project-info">
                        <h3><?php echo esc_html( $p->name ); ?></h3>
                        <?php if ( $p->description ) : ?><p><?php echo esc_html( $p->description ); ?></p><?php endif; ?>
                        <span class="bwsbfe-page-count">
                            <?php
                            printf(
                                esc_html( _n( '%d page', '%d pages', $count, 'quickfields-bulk-editor-for-acf' ) ),
                                (int) $count
                            );
                            ?>
                        </span>
                    </div>
                </div>
                <div class="bwsbfe-project-card-foot">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe&view=sheet&project=' . intval( $p->id ) ) ); ?>" class="bwsbfe-btn-primary bwsbfe-btn-sm"><?php esc_html_e( 'Open Sheet →', 'quickfields-bulk-editor-for-acf' ); ?></a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe&view=edit&project=' . intval( $p->id ) ) ); ?>" class="bwsbfe-btn-ghost bwsbfe-btn-sm"><?php esc_html_e( 'Edit', 'quickfields-bulk-editor-for-acf' ); ?></a>
                    <button type="button" class="bwsbfe-btn-danger bwsbfe-btn-sm bwsbfe-delete-project" data-id="<?php echo esc_attr( $p->id ); ?>"><?php esc_html_e( 'Delete', 'quickfields-bulk-editor-for-acf' ); ?></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php bwsbfe_render_pro_banner(); ?>
        <?php bwsbfe_render_brand_footer(); ?>
    </div>
    <?php
}

function bwsbfe_render_project_form( $project = null ) {
    $editing   = ! is_null( $project );
    $sel_ids   = $editing ? bwsbfe_project_page_ids( $project ) : [];
    $all_pages = get_posts( [
        'post_type'              => 'page',
        'post_status'            => [ 'publish', 'draft', 'pending', 'private', 'future' ],
        'posts_per_page'         => -1,
        'orderby'                => 'title',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ] );
    ?>
    <div class="wrap bwsbfe-wrap">
        <div class="bwsbfe-header">
            <div>
                <h1><?php echo esc_html( $editing ? __( 'Edit Project', 'quickfields-bulk-editor-for-acf' ) : __( 'New Project', 'quickfields-bulk-editor-for-acf' ) ); ?></h1>
                <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe' ) ); ?>" class="bwsbfe-back"><?php esc_html_e( '← Back to Projects', 'quickfields-bulk-editor-for-acf' ); ?></a></p>
            </div>
        </div>
        <div class="bwsbfe-form-wrap">
            <div class="bwsbfe-form-card">
                <div class="bwsbfe-form-group">
                    <label><?php esc_html_e( 'Project Name', 'quickfields-bulk-editor-for-acf' ); ?> <span class="req">*</span></label>
                    <input type="text" id="bwsbfe-proj-name" value="<?php echo esc_attr( $editing ? $project->name : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Service Pages', 'quickfields-bulk-editor-for-acf' ); ?>" />
                </div>
                <div class="bwsbfe-form-group">
                    <label><?php esc_html_e( 'Description', 'quickfields-bulk-editor-for-acf' ); ?> <span class="opt">(<?php esc_html_e( 'optional', 'quickfields-bulk-editor-for-acf' ); ?>)</span></label>
                    <textarea id="bwsbfe-proj-desc" rows="2"><?php echo esc_textarea( $editing ? $project->description : '' ); ?></textarea>
                </div>
                <div class="bwsbfe-form-group">
                    <label><?php esc_html_e( 'Select Pages', 'quickfields-bulk-editor-for-acf' ); ?> <span class="req">*</span></label>
                    <p class="bwsbfe-hint"><?php esc_html_e( 'The sheet will show ACF fields automatically based on the pages selected.', 'quickfields-bulk-editor-for-acf' ); ?></p>
                    <input type="text" id="bwsbfe-page-search" placeholder="<?php esc_attr_e( 'Search pages…', 'quickfields-bulk-editor-for-acf' ); ?>" class="bwsbfe-page-search" />
                    <div class="bwsbfe-page-list" id="bwsbfe-page-list">
                        <?php foreach ( $all_pages as $pg ) : ?>
                        <label class="bwsbfe-page-item <?php echo esc_attr( in_array( $pg->ID, $sel_ids, true ) ? 'checked' : '' ); ?>">
                            <input type="checkbox" value="<?php echo esc_attr( $pg->ID ); ?>" <?php checked( in_array( $pg->ID, $sel_ids, true ) ); ?> />
                            <span class="bwsbfe-page-title"><?php echo esc_html( $pg->post_title ); ?></span>
                            <span class="bwsbfe-page-status <?php echo esc_attr( $pg->post_status ); ?>"><?php echo esc_html( $pg->post_status ); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="bwsbfe-page-sel-count">
                        <span id="bwsbfe-sel-count"><?php echo esc_html( (string) count( $sel_ids ) ); ?></span>
                        <?php esc_html_e( 'pages selected', 'quickfields-bulk-editor-for-acf' ); ?>
                    </div>
                </div>
                <div class="bwsbfe-form-actions">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe' ) ); ?>" class="bwsbfe-btn-ghost"><?php esc_html_e( 'Cancel', 'quickfields-bulk-editor-for-acf' ); ?></a>
                    <button type="button" class="bwsbfe-btn-primary" id="bwsbfe-save-project" data-id="<?php echo esc_attr( $editing ? $project->id : 0 ); ?>">
                        <?php echo esc_html( $editing ? __( 'Save Changes', 'quickfields-bulk-editor-for-acf' ) : __( 'Create Project', 'quickfields-bulk-editor-for-acf' ) ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php bwsbfe_render_pro_banner(); ?>
        <?php bwsbfe_render_brand_footer(); ?>
    </div>
    <?php
}

function bwsbfe_render_sheet( $project ) {
    if ( function_exists( 'wp_raise_memory_limit' ) ) {
        wp_raise_memory_limit( 'admin' );
    }

    $page_ids     = bwsbfe_project_page_ids( $project );
    $field_groups = bwsbfe_get_page_fields( $page_ids );
    $tab_keys     = array_keys( $field_groups );
    $pages        = bwsbfe_get_project_pages( $page_ids );

    if ( empty( $field_groups ) ) {
        echo '<div class="wrap bwsbfe-wrap"><div class="bwsbfe-empty"><div class="bwsbfe-empty-icon">⚠️</div><h2>' . esc_html__( 'No ACF fields detected', 'quickfields-bulk-editor-for-acf' ) . '</h2><p>' . esc_html__( 'Make sure Advanced Custom Fields is active and field groups are assigned to your pages.', 'quickfields-bulk-editor-for-acf' ) . '</p></div></div>';
        return;
    }
    ?>
    <div class="wrap bwsbfe-wrap">
        <div class="bwsbfe-header">
            <div>
                <h1>📋 <?php echo esc_html( $project->name ); ?></h1>
                <p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe' ) ); ?>" class="bwsbfe-back"><?php esc_html_e( '← Projects', 'quickfields-bulk-editor-for-acf' ); ?></a>
                    &nbsp;·&nbsp;
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe&view=edit&project=' . intval( $project->id ) ) ); ?>" class="bwsbfe-back"><?php esc_html_e( 'Edit project', 'quickfields-bulk-editor-for-acf' ); ?></a>
                    &nbsp;·&nbsp;
                    <?php
                    printf(
                        esc_html__( '%1$d pages · %2$d field groups', 'quickfields-bulk-editor-for-acf' ),
                        count( $page_ids ),
                        count( $tab_keys )
                    );
                    ?>
                </p>
            </div>
            <div class="bwsbfe-header-right">
                <div class="bwsbfe-status" id="bwsbfe-status"><span class="bwsbfe-dot"></span> <?php esc_html_e( 'Ready', 'quickfields-bulk-editor-for-acf' ); ?></div>
                <?php do_action( 'bwsbfe_sheet_toolbar', $project ); ?>
                <button type="button" class="bwsbfe-btn-danger" id="bwsbfe-clear-fields"><?php esc_html_e( 'Clear Text Fields', 'quickfields-bulk-editor-for-acf' ); ?></button>
            </div>
        </div>

        <?php if ( count( $pages ) < count( $page_ids ) ) : ?>
        <div class="bwsbfe-modal-note bwsbfe-notice-inline">
            <?php
            printf(
                esc_html__( 'Loaded %1$d of %2$d pages in this project. Some pages may have been deleted or are unavailable.', 'quickfields-bulk-editor-for-acf' ),
                count( $pages ),
                count( $page_ids )
            );
            ?>
        </div>
        <?php endif; ?>

        <?php if ( empty( $pages ) ) : ?>
        <div class="bwsbfe-empty">
            <div class="bwsbfe-empty-icon">📄</div>
            <h2><?php esc_html_e( 'No pages in this project', 'quickfields-bulk-editor-for-acf' ); ?></h2>
            <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=bwsbfe&view=edit&project=' . intval( $project->id ) ) ); ?>"><?php esc_html_e( 'Edit project', 'quickfields-bulk-editor-for-acf' ); ?></a></p>
        </div>
        <?php else : ?>

        <div class="bwsbfe-overlay" id="bwsbfe-clear-overlay" style="display:none;">
            <div class="bwsbfe-modal">
                <div class="bwsbfe-modal-head">
                    <h2><?php esc_html_e( 'Clear Text Fields', 'quickfields-bulk-editor-for-acf' ); ?></h2>
                    <button type="button" id="bwsbfe-clear-modal-close">✕</button>
                </div>
                <p class="bwsbfe-modal-note"><?php esc_html_e( 'Empty all text and textarea fields for every page in this project. Image fields are not changed. This cannot be undone.', 'quickfields-bulk-editor-for-acf' ); ?></p>
                <div class="bwsbfe-modal-foot">
                    <button type="button" class="bwsbfe-btn-ghost" id="bwsbfe-clear-modal-cancel"><?php esc_html_e( 'Cancel', 'quickfields-bulk-editor-for-acf' ); ?></button>
                    <button type="button" class="bwsbfe-btn-danger" id="bwsbfe-do-clear"><?php esc_html_e( 'Clear Text Fields', 'quickfields-bulk-editor-for-acf' ); ?></button>
                </div>
            </div>
        </div>

        <?php do_action( 'bwsbfe_sheet_modals', $project ); ?>

        <div class="bwsbfe-tabs">
            <?php foreach ( $tab_keys as $i => $group_name ) : ?>
            <button type="button" class="bwsbfe-tab <?php echo esc_attr( 0 === $i ? 'active' : '' ); ?>" data-tab="<?php echo esc_attr( sanitize_title( $group_name ) ); ?>">
                <?php echo esc_html( $group_name ); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ( $field_groups as $group_name => $fields ) :
            $tab_id = sanitize_title( $group_name );
            $first  = ( $group_name === $tab_keys[0] );
            ?>
        <div class="bwsbfe-panel <?php echo esc_attr( $first ? 'active' : '' ); ?>" data-tab="<?php echo esc_attr( $tab_id ); ?>">
            <div class="bwsbfe-scroll">
                <table class="bwsbfe-table">
                    <thead>
                        <tr>
                            <th class="bwsbfe-th-page"><?php esc_html_e( 'Page', 'quickfields-bulk-editor-for-acf' ); ?></th>
                            <?php foreach ( $fields as $fk => $fd ) : ?>
                            <th>
                                <?php echo esc_html( $fd['label'] ); ?>
                                <?php if ( 'image' === $fd['type'] ) : ?>
                                <span class="bwsbfe-badge"><?php esc_html_e( 'IMG', 'quickfields-bulk-editor-for-acf' ); ?></span>
                                <?php endif; ?>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $pages as $pg ) :
                            $wpid = $pg->ID;
                            ?>
                        <tr>
                            <td class="bwsbfe-td-page">
                                <a href="<?php echo esc_url( get_edit_post_link( $wpid ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $pg->post_title ); ?></a>
                                <a href="<?php echo esc_url( get_permalink( $wpid ) ); ?>" target="_blank" rel="noopener noreferrer" class="bwsbfe-view-link">↗</a>
                            </td>
                            <?php foreach ( $fields as $fk => $fd ) : ?>
                            <td class="bwsbfe-td">
                                <?php if ( 'image' === $fd['type'] ) :
                                    $att_id = bwsbfe_read_image_attachment_id( $wpid, $fk );
                                    $thumb  = $att_id ? wp_get_attachment_image_url( $att_id, 'thumbnail' ) : '';
                                    bwsbfe_render_image_cell( $wpid, $thumb, $fk );
                                elseif ( 'textarea' === $fd['type'] ) :
                                    $raw = bwsbfe_read_field_value( $wpid, $fk );
                                    ?>
                                <textarea class="bwsbfe-input" data-pid="<?php echo esc_attr( $wpid ); ?>" data-field="<?php echo esc_attr( $fk ); ?>" rows="3"><?php echo esc_textarea( $raw ); ?></textarea>
                                <?php else :
                                    $raw = bwsbfe_read_field_value( $wpid, $fk );
                                    ?>
                                <input type="text" class="bwsbfe-input" data-pid="<?php echo esc_attr( $wpid ); ?>" data-field="<?php echo esc_attr( $fk ); ?>" value="<?php echo esc_attr( $raw ); ?>" />
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>

        <?php endif; ?>
        <?php bwsbfe_render_pro_banner(); ?>
        <?php bwsbfe_render_brand_footer(); ?>
    </div>
    <?php
}

