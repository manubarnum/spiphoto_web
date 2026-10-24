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

    // Polices : servies localement depuis assets/fonts/ via theme.json (RGPD).

    // Main stylesheet.
    wp_enqueue_style(
        'spiphoto-fse-style',
        get_template_directory_uri() . '/style.css',
        [],
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
    add_editor_style(['style.css']);
}
add_action('after_setup_theme', __NAMESPACE__ . '\\enqueue_editor_assets');

// Section 3 (WIDGETS) retiree le 21/10/2026 : theme FSE, les zones de widgets
// classiques ne sont pas utilisables. Les 5 sidebars enregistrees etaient vides.

// ─────────────────────────────────────────────
// 3. NAVIGATION CONDITIONNELLE (membres connectes)
// ─────────────────────────────────────────────
// Remplace le menu de la topbar par LoggedMenu pour un utilisateur connecte.
// Remplace Conditional Menus, inoperant sur le bloc Navigation (il ne passe
// pas par wp_nav_menu()). Les IDs sont ceux des objets wp_navigation.
const NAV_MEMBRES = 64675; // LoggedMenu

function swap_navigation_for_members(array $block): array {
    if ('core/navigation' !== ($block['blockName'] ?? '')) {
        return $block;
    }
    $class = $block['attrs']['className'] ?? '';
    if (!str_contains($class, 'topbar-navigation')) {
        return $block;
    }
    if (is_user_logged_in()) {
        $block['attrs']['ref'] = NAV_MEMBRES;
    }
    return $block;
}
add_filter('render_block_data', __NAMESPACE__ . '\\swap_navigation_for_members');

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
// Retires le 24/10/2026 : image 'rounded' et separator 'dots' doublonnaient les
// styles natifs de WordPress (memes noms, CSS fourni par le coeur) ; image
// 'frame', group 'glass' et heading 'section-label' n'avaient aucun CSS.
// Chaque style ci-dessous a ses regles dans style.css (section STYLES DE BLOC).
function register_block_styles(): void {
    $styles = [
        ['core/button',    'outline',      __('Outline',      'spiphoto-fse')],
        ['core/button',    'ghost',        __('Ghost',        'spiphoto-fse')],
        ['core/image',     'shadow',       __('Shadow',       'spiphoto-fse')],
        ['core/group',     'card',         __('Card',         'spiphoto-fse')],
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
// Retire le 21/10/2026 : assets/js/block-variations.js n'a jamais existe,
// l'enqueue provoquait un 404 a chaque ouverture de l'editeur.
// Pour reactiver : creer le fichier puis restaurer l'enqueue ci-dessous.

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
// Resource hints Google Fonts retires le 21/10/2026 : polices servies localement.

// Preload des deux polices critiques pour le LCP (latin uniquement).
// L'attribut crossorigin est obligatoire : les requetes de polices sont en mode
// CORS, sans lui le navigateur telechargerait le fichier deux fois.
function preload_critical_fonts(): void {
    $fonts = [
        'playfair-normal-latin.woff2',
        'source-serif-normal-latin.woff2',
    ];
    foreach ($fonts as $font) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url(get_template_directory_uri() . '/assets/fonts/' . $font)
        );
    }
}
add_action('wp_head', __NAMESPACE__ . '\\preload_critical_fonts', 2);

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

// Section 14 (AJAX load more) retiree le 21/10/2026 : aucun bouton
// [data-action="load-more"] n'existait dans les templates ni en base,
// et template-parts/ etait absent. Utiliser le bloc Requete + Pagination.
