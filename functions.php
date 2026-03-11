<?php

/**
 * SPIPHOTO FSE – Theme Functions
 *
 * @package SpiphotoFSE
 * @version 1.0.0
 */

declare(strict_types=1);

namespace SpiphotoFSE;

// Security.
defined('ABSPATH') || exit;

// ─────────────────────────────────────────────
// 1. THEME SETUP
// ─────────────────────────────────────────────
function setup(): void {
    load_theme_textdomain('spiphoto-fse', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', [
        'comment-form', 'comment-list', 'gallery', 'caption',
        'style', 'script', 'navigation-widgets',
    ]);
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo', [
        'height'      => 96,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('post-formats', [
        'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat',
    ]);
    add_theme_support('custom-spacing');
    add_theme_support('custom-line-height');
    add_theme_support('appearance-tools');
    add_theme_support('border');

    // Image sizes.
    add_image_size('spiphoto-card',     780, 440, true);
    add_image_size('spiphoto-hero',    1440, 810, true);
    add_image_size('spiphoto-thumb',   300, 225, true);
    add_image_size('spiphoto-square',  600, 600, true);

    // Nav menus.
    register_nav_menus([
        'primary'   => __('Primary Navigation', 'spiphoto-fse'),
        'secondary' => __('Secondary Navigation', 'spiphoto-fse'),
        'footer'    => __('Footer Navigation', 'spiphoto-fse'),
        'social'    => __('Social Links', 'spiphoto-fse'),
    ]);
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

// ─────────────────────────────────────────────
// ANTI-FLASH THÈME JOUR/NUIT
// ─────────────────────────────────────────────
function theme_antiflash(): void {
    echo '<script>
(function(){
  var t=localStorage.getItem("spiphoto-theme");
  var p=window.matchMedia&&window.matchMedia("(prefers-color-scheme: light)").matches?"light":"dark";
  document.documentElement.setAttribute("data-theme",t||p);
})();
</script>';
}
add_action('wp_head', __NAMESPACE__ . '\\theme_antiflash', 1);

// ─────────────────────────────────────────────
// 2. ENQUEUE SCRIPTS & STYLES
// ─────────────────────────────────────────────
function enqueue_assets(): void {
    $ver = wp_get_theme()->get('Version');

    // Google Fonts (preconnect first).
    wp_enqueue_style(
        'spiphoto-fse-fonts-preconnect',
        'https://fonts.googleapis.com',
        [],
        null
    );
    wp_enqueue_style(
        'spiphoto-fse-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..900;1,9..40,300..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Source+Serif+4:ital,opsz,wght@0,8..60,300..900;1,8..60,300..900&display=swap',
        ['spiphoto-fse-fonts-preconnect'],
        null
    );

    // Main stylesheet.
    wp_enqueue_style(
        'spiphoto-fse-style',
        get_template_directory_uri() . '/style.css',
        ['spiphoto-fse-fonts'],
        $ver
    );

    // Main JS (deferred).
    wp_enqueue_script(
        'spiphoto-fse-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        $ver,
        ['strategy' => 'defer', 'in_footer' => true]
    );

    // Pass data to JS.
    wp_localize_script('spiphoto-fse-main', 'spiphotoFSE', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('spiphoto_fse'),
        'homeUrl'   => home_url('/'),
        'i18n'      => [
            'searchPlaceholder' => __('Search articles…', 'spiphoto-fse'),
            'copyLink'          => __('Copy Link', 'spiphoto-fse'),
            'linkCopied'        => __('Copied!', 'spiphoto-fse'),
        ],
    ]);

    // Comments script.
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets');

// Editor styles.
function enqueue_editor_assets(): void {
    add_editor_style([
        'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..900;1,9..40,300..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Source+Serif+4:ital,opsz,wght@0,8..60,300..900;1,8..60,300..900&display=swap',
        'style.css',
    ]);
}
add_action('after_setup_theme', __NAMESPACE__ . '\\enqueue_editor_assets');

// ─────────────────────────────────────────────
// 3. WIDGETS
// ─────────────────────────────────────────────
function register_widgets(): void {
    $defaults = [
        'before_widget' => '<div class="sidebar-widget %2$s" id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ];

    register_sidebar(array_merge($defaults, [
        'name' => __('Primary Sidebar', 'spiphoto-fse'),
        'id'   => 'sidebar-primary',
        'description' => __('Widgets shown in the primary sidebar.', 'spiphoto-fse'),
    ]));

    register_sidebar(array_merge($defaults, [
        'name' => __('Footer Column 1', 'spiphoto-fse'),
        'id'   => 'footer-col-1',
    ]));
    register_sidebar(array_merge($defaults, [
        'name' => __('Footer Column 2', 'spiphoto-fse'),
        'id'   => 'footer-col-2',
    ]));
    register_sidebar(array_merge($defaults, [
        'name' => __('Footer Column 3', 'spiphoto-fse'),
        'id'   => 'footer-col-3',
    ]));

    register_sidebar(array_merge($defaults, [
        'name'        => __('Before Post Content', 'spiphoto-fse'),
        'id'          => 'before-post',
        'description' => __('Displayed above single post content.', 'spiphoto-fse'),
    ]));
}
add_action('widgets_init', __NAMESPACE__ . '\\register_widgets');

// ─────────────────────────────────────────────
// 4. READING TIME
// ─────────────────────────────────────────────
function reading_time(int $post_id = 0): string {
    $post_id   = $post_id ?: get_the_ID();
    $content   = get_post_field('post_content', $post_id);
    $word_count = str_word_count(wp_strip_all_tags($content));
    $minutes   = max(1, (int) round($word_count / 200));

    return sprintf(
        /* translators: %d: minutes */
        _n('%d min read', '%d min read', $minutes, 'spiphoto-fse'),
        $minutes
    );
}

// ─────────────────────────────────────────────
// 5. BREADCRUMBS (no plugin dependency)
// ─────────────────────────────────────────────
function breadcrumbs(): void {
    if (is_front_page()) return;

    $separator = '<span class="breadcrumb-sep" aria-hidden="true">›</span>';
    $output    = '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'spiphoto-fse') . '">';
    $output   .= '<ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">';

    $pos = 1;

    // Home.
    $output .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    $output .= '<a href="' . esc_url(home_url('/')) . '" itemprop="item"><span itemprop="name">' . __('Home', 'spiphoto-fse') . '</span></a>';
    $output .= '<meta itemprop="position" content="' . $pos++ . '">';
    $output .= '</li>';

    if (is_category()) {
        $output .= $separator;
        $output .= '<li class="breadcrumb-item current" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        $output .= '<span itemprop="name">' . single_cat_title('', false) . '</span>';
        $output .= '<meta itemprop="position" content="' . $pos . '">';
        $output .= '</li>';
    } elseif (is_tag()) {
        $output .= $separator;
        $output .= '<li class="breadcrumb-item current" aria-current="page">';
        $output .= '<span>' . single_tag_title('', false) . '</span>';
        $output .= '</li>';
    } elseif (is_search()) {
        $output .= $separator;
        $output .= '<li class="breadcrumb-item current" aria-current="page">';
        $output .= '<span>' . sprintf(__('Search: %s', 'spiphoto-fse'), get_search_query()) . '</span>';
        $output .= '</li>';
    } elseif (is_singular()) {
        $post = get_post();

        if ('post' === get_post_type()) {
            $cats = get_the_category($post->ID);
            if ($cats) {
                $output .= $separator;
                $output .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                $output .= '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '" itemprop="item"><span itemprop="name">' . esc_html($cats[0]->name) . '</span></a>';
                $output .= '<meta itemprop="position" content="' . $pos++ . '">';
                $output .= '</li>';
            }
        }

        $output .= $separator;
        $output .= '<li class="breadcrumb-item current" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        $output .= '<span itemprop="name">' . esc_html(get_the_title($post->ID)) . '</span>';
        $output .= '<meta itemprop="position" content="' . $pos . '">';
        $output .= '</li>';
    }

    $output .= '</ol></nav>';
    echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// ─────────────────────────────────────────────
// 6. CUSTOM BLOCK STYLES
// ─────────────────────────────────────────────
function register_block_styles(): void {
    $styles = [
        ['core/button',    'outline',      __('Outline',      'spiphoto-fse')],
        ['core/button',    'ghost',        __('Ghost',        'spiphoto-fse')],
        ['core/image',     'rounded',      __('Rounded',      'spiphoto-fse')],
        ['core/image',     'shadow',       __('Shadow',       'spiphoto-fse')],
        ['core/image',     'frame',        __('Frame',        'spiphoto-fse')],
        ['core/group',     'card',         __('Card',         'spiphoto-fse')],
        ['core/group',     'glass',        __('Glass',        'spiphoto-fse')],
        ['core/separator', 'dots',         __('Dots',         'spiphoto-fse')],
        ['core/heading',   'section-label',__('Section Label','spiphoto-fse')],
        ['core/quote',     'pull-quote',   __('Pull Quote',   'spiphoto-fse')],
        ['core/list',      'check-list',   __('Check List',   'spiphoto-fse')],
    ];

    foreach ($styles as [$block, $name, $label]) {
        register_block_style($block, ['name' => $name, 'label' => $label]);
    }
}
add_action('init', __NAMESPACE__ . '\\register_block_styles');

// ─────────────────────────────────────────────
// 7. CUSTOM BLOCK PATTERNS
// ─────────────────────────────────────────────
function register_pattern_categories(): void {
    register_block_pattern_category('spiphoto-fse-headers',  ['label' => __('SPIPHOTO – En-têtes',  'spiphoto-fse')]);
    register_block_pattern_category('spiphoto-fse-sections', ['label' => __('SPIPHOTO – Sections', 'spiphoto-fse')]);
    register_block_pattern_category('spiphoto-fse-posts',    ['label' => __('SPIPHOTO – Articles',    'spiphoto-fse')]);
    register_block_pattern_category('spiphoto-fse-cta',      ['label' => __('SPIPHOTO – CTA',      'spiphoto-fse')]);
}
add_action('init', __NAMESPACE__ . '\\register_pattern_categories');

// ─────────────────────────────────────────────
// 8. CUSTOM BLOCK VARIATIONS
// ─────────────────────────────────────────────
function enqueue_block_variations(): void {
    wp_enqueue_script(
        'spiphoto-fse-variations',
        get_template_directory_uri() . '/assets/js/block-variations.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_variations');

// ─────────────────────────────────────────────
// 9. READING PROGRESS (inject HTML)
// ─────────────────────────────────────────────
function inject_ui_elements(): void {
    // Back to top + reading progress (only on front end).
    if (is_admin()) return;
    echo '<div class="reading-progress-bar" id="readingProgress" role="progressbar" aria-label="' . esc_attr__('Reading progress', 'spiphoto-fse') . '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>';
    echo '<button class="back-to-top" id="backToTop" aria-label="' . esc_attr__('Back to top', 'spiphoto-fse') . '">↑</button>';
    echo '<div class="search-overlay" id="searchOverlay" role="dialog" aria-modal="true" aria-label="' . esc_attr__('Search', 'spiphoto-fse') . '">
        <div class="search-overlay-inner">
            ' . get_search_form(false) . '
        </div>
        <button class="search-overlay-close" id="searchOverlayClose" aria-label="' . esc_attr__('Close search', 'spiphoto-fse') . '">✕</button>
    </div>';
}
add_action('wp_footer', __NAMESPACE__ . '\\inject_ui_elements');

// ─────────────────────────────────────────────
// 10. PERFORMANCE
// ─────────────────────────────────────────────

// Remove emoji scripts (unnecessary).
remove_action('wp_head',             'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles',     'print_emoji_styles');
remove_action('admin_print_styles',  'print_emoji_styles');

// Remove oEmbed.
remove_action('wp_head', 'wp_oembed_add_discovery_links');

// Remove wlwmanifest.
remove_action('wp_head', 'wlwmanifest_link');

// Remove RSD.
remove_action('wp_head', 'rsd_link');

// Resource hints.
function resource_hints(array $hints, string $relation_type): array {
    if ('preconnect' === $relation_type) {
        $hints[] = ['href' => 'https://fonts.googleapis.com'];
        $hints[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
    }
    return $hints;
}
add_filter('wp_resource_hints', __NAMESPACE__ . '\\resource_hints', 10, 2);

// ─────────────────────────────────────────────
// 11. STRUCTURED DATA (JSON-LD)
// ─────────────────────────────────────────────
function output_schema(): void {
    if (!is_singular('post')) return;

    $post     = get_post();
    $author   = get_the_author_meta('display_name', $post->post_author);
    $pub_date = get_the_date('c', $post);
    $mod_date = get_the_modified_date('c', $post);
    $img_src  = get_the_post_thumbnail_url($post->ID, 'spiphoto-hero');

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => get_the_title($post->ID),
        'author'           => ['@type' => 'Person', 'name' => $author],
        'datePublished'    => $pub_date,
        'dateModified'     => $mod_date,
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'logo'  => ['@type' => 'ImageObject', 'url' => get_site_icon_url()],
        ],
        'description'      => wp_strip_all_tags(get_the_excerpt($post->ID)),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => get_permalink($post->ID)],
    ];

    if ($img_src) {
        $schema['image'] = $img_src;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', __NAMESPACE__ . '\\output_schema');

// ─────────────────────────────────────────────
// 12. EXCERPT LENGTH
// ─────────────────────────────────────────────
function excerpt_length(): int {
    return 25;
}
add_filter('excerpt_length', __NAMESPACE__ . '\\excerpt_length');

function excerpt_more(string $more): string {
    return '…';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

// ─────────────────────────────────────────────
// 13. SECURITY HEADERS (via wp_headers filter)
// ─────────────────────────────────────────────
function security_headers(array $headers): array {
    $headers['X-Content-Type-Options'] = 'nosniff';
    $headers['X-Frame-Options']        = 'SAMEORIGIN';
    $headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
    $headers['Permissions-Policy']     = 'camera=(), microphone=(), geolocation=()';
    return $headers;
}
add_filter('wp_headers', __NAMESPACE__ . '\\security_headers');

// ─────────────────────────────────────────────
// 14. AJAX: LOAD MORE POSTS
// ─────────────────────────────────────────────
function ajax_load_more(): void {
    check_ajax_referer('spiphoto_fse', 'nonce');

    $page     = absint($_POST['page'] ?? 1);
    $cat      = sanitize_text_field($_POST['category'] ?? '');
    $per_page = absint($_POST['perPage'] ?? 6);

    $args = [
        'post_type'      => 'post',
        'paged'          => $page,
        'posts_per_page' => $per_page,
        'post_status'    => 'publish',
    ];
    if ($cat) $args['category_name'] = $cat;

    $query = new \WP_Query($args);

    if (!$query->have_posts()) {
        wp_send_json_error(['message' => 'No more posts'], 404);
    }

    ob_start();
    while ($query->have_posts()) {
        $query->the_post();
        // Render minimal card markup – customise to match your block patterns.
        get_template_part('template-parts/content', get_post_type());
    }
    wp_reset_postdata();

    wp_send_json_success([
        'html'     => ob_get_clean(),
        'hasMore'  => $query->max_num_pages > $page,
        'maxPages' => $query->max_num_pages,
    ]);
}
add_action('wp_ajax_spiphoto_load_more',        __NAMESPACE__ . '\\ajax_load_more');
add_action('wp_ajax_nopriv_spiphoto_load_more', __NAMESPACE__ . '\\ajax_load_more');
