<?php

/**
 * Theme setup.
 */

namespace App;

use App\Services\ModernImageHandler;
use function Roots\bundle;
use Illuminate\Support\Facades\Blade;



    /**
     * Register the theme assets.
     *
     * @return void
     */
    add_action('wp_enqueue_scripts', function () {
        bundle('app')->enqueue();
    }, 100);

    add_action('admin_enqueue_scripts', function () {
        bundle('admin-media-uploader')->enqueue();
    }, 100);

    /**
     * Register the theme assets with the block editor.
     *
     * @return void
     */
    add_action('enqueue_block_editor_assets', function () {
        bundle('editor')->enqueue();
    }, 100);

    /**
     * Register the initial theme setup.
     *
     * @return void
     */
    add_action('after_setup_theme', function () {
        /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
    }, 20);

	// Add theme support for custom logo
    add_theme_support('custom-logo', [
        'height'      => 48,
        'width'       => 120,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
	
	// Add custom header image support
    add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
        $wp_customize->add_section('header_image_section', [
            'title'    => __('Header Background', 'sage'),
            'priority' => 30,
        ]);

        $wp_customize->add_setting('header_background_image', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw'
        ]);

        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'header_background_image', [
            'label'    => __('Header Background Image', 'sage'),
            'section'  => 'header_image_section',
            'settings' => 'header_background_image'
        ]));

        $wp_customize->add_setting('header_image');
        $wp_customize->add_control(new \WP_Customize_Image_Control(
            $wp_customize,
            'header_image',
            [
                'label' => __('Header Image', 'sage'),
                'section' => 'title_tagline'
            ]
        ));
    });


    /**
     * Register the theme sidebars.
     *
     * @return void
     */
    add_action('widgets_init', function () {
        $config = [
            'before_widget' => '<section class="widget %1$s %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        ];

        register_sidebar([
            'name' => __('Primary', 'sage'),
            'id' => 'sidebar-primary',
        ] + $config);

        register_sidebar([
            'name' => __('Footer', 'sage'),
            'id' => 'sidebar-footer',
        ] + $config);
    });

    add_action('after_setup_theme', function() {
        new ModernImageHandler();
    });

    
    Blade::directive('modernPicture', function ($expression) {
        require_once get_theme_file_path('/app/moder-images.php');
        return "<?php echo \\App\\modern_picture($expression); ?>";
    });