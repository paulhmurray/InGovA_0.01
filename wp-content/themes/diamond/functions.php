<?php
/**
 * Diamond functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress

 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Diamond 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

/**
 * Diamond only works in WordPress 4.1 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'diamond_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Diamond 1.0
 */
function diamond_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on diamond, use a find and replace
	 * to change 'diamond' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'diamond', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

    $args = array(
        'default-color' => '#f1f1f1',
    );
    add_theme_support( 'custom-background', $args );

    $args = array(
        'uploads'       => true,
    );
    add_theme_support( 'custom-header', $args );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	//set_post_thumbnail_size( 234.797, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Sidebar Menu',      'diamond' ),
		'second'  => __( 'Header Menu', 'diamond' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 */
	add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css' ) );
}
endif; // diamond_setup
add_action( 'after_setup_theme', 'diamond_setup' );

/**
 * Register widget area.
 *
 * @since Diamond 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function diamond_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Widget Area', 'diamond' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'diamond' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Widget right', 'diamond' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'diamond' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'diamond_widgets_init' );


function diamond_load_fonts() {
    wp_register_style('diamond-googleFonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700');
    wp_enqueue_style( 'diamond-googleFonts');
}
add_action('wp_enqueue_scripts', 'diamond_load_fonts');

/**
 * JavaScript Detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Diamond 1.1
 */
function diamond_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'diamond_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 *
 * @since Diamond 1.0
 */
function diamond_scripts() {

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'diamond-genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.2' );

	// Add font-awesome, used in the main stylesheet.
	wp_enqueue_style( 'diamond-font-awesome', get_template_directory_uri() . '/font-awesome/font-awesome.css', array(), '3.2' );

    wp_enqueue_script( 'diamond-menu', get_template_directory_uri() . '/js/menu.js', array('jquery'), '20151014', false );
    wp_enqueue_script( 'diamond-js', get_template_directory_uri() . '/js/diamond-js.js', array(), '20150418', true );

	// Load our main stylesheet.
	wp_enqueue_style( 'diamond-style', get_stylesheet_uri() );

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'diamond-ie', get_template_directory_uri() . '/css/ie.css', array( 'diamond-style' ), '20141010' );
	wp_style_add_data( 'diamond-ie', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'diamond-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'diamond-style' ), '20141010' );
	wp_style_add_data( 'diamond-ie7', 'conditional', 'lt IE 8' );

	wp_enqueue_script( 'diamond-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20141010', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'diamond-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20141010' );
	}

	wp_enqueue_script( 'diamond-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'diamond-script', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . __( 'expand child menu', 'diamond' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . __( 'collapse child menu', 'diamond' ) . '</span>',
	) );

    // Load grid home posts javascript
    wp_enqueue_script( 'diamond-mansory-grid', get_template_directory_uri() . '/js/Packery-grid.js', array( 'jquery' ), '20162201', true );
}
add_action( 'wp_enqueue_scripts', 'diamond_scripts' );

/**
 * Display descriptions in main navigation.
 *
 * @since Diamond 1.0
 *
 * @param string  $item_output The menu item output.
 * @param WP_Post $item        Menu item object.
 * @param int     $depth       Depth of the menu.
 * @param array   $args        wp_nav_menu() arguments.
 * @return string Menu item with possible description.
 */
function diamond_nav_description( $item_output, $item, $depth, $args ) {
	if ( 'primary' == $args->theme_location && $item->description ) {
		$item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'diamond_nav_description', 10, 4 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Diamond 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function diamond_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'diamond_search_form_modify' );

/**
 * Implement the Custom Header feature.
 *
 * @since Diamond 1.0
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 *
 * @since Diamond 1.0
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 *
 * @since Diamond 1.0
 */
require get_template_directory() . '/inc/customizer.php';

function diamond_excerpt($limit) {
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt)/*.'<div id="teaser-more"><a href="'. get_permalink( get_the_ID() ) .'">Continue Reading</a></div>'*/;
    } else {
    $excerpt = implode(" ",$excerpt);
    }
    $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
    return $excerpt;
}

function diamond_admin_notice() {
    ?>
    <div id="message" class="updated notice is-dismissible">
        <p><a href="<?php echo esc_url( __( 'http://www.mhthemes.com/support/documentation-diamond/', 'diamond' ) ); ?>" target="_blank"><?php _e( 'Diamond Documentation', 'diamond' ); ?></a></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'diamond_admin_notice' );