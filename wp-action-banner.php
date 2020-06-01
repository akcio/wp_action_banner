<?php
/*
Plugin Name: Action Banner
Plugin Script: wp-action-banner.php
Description: Allows you to create custom banners, which consist of an image, text, a link, and a call to action.  Custom banners are easily output via shortcodes.
Version: 0.2.3
Author: Разработчики занаклейкой.рф
Text Domain: plugin-action-banner
*/

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );



class WP_Action_Banner{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init_texdomain'));

        add_action('wp_enqueue_scripts', array($this, 'register_styles'), 10);
        add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 10);
        add_action('admin_enqueue_scripts', array($this, 'register_admin_styles'), 10);
        add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'), 10);
    }

    public function init_texdomain() {
        $mo_file_path = dirname(__FILE__) . '/languages/action-banner-'. determine_locale() . '.mo';
        load_textdomain( 'plugin-action-banner', $mo_file_path );
        load_plugin_textdomain('plugin-action-banner', false, dirname(plugin_basename(__FILE__)).'/languages/' );
    }

    public function register_styles() {
        wp_register_style('banner-styles', plugin_dir_url( __FILE__ ).'assets/css/ab-styles.css');
        wp_enqueue_style('banner-styles');
    }

    public function register_scripts() {
        wp_register_script( 'banner-scripts', plugin_dir_url( __FILE__ ).'assets/js/ab-scripts.js' );  
        wp_enqueue_script( 'banner-scripts' );  
    }

    public function register_admin_styles() {
        wp_register_style('banner-admin-style', plugin_dir_url( __FILE__ ).'assets/css/ab-admin-styles.css');
        wp_enqueue_style('banner-admin-style');
    }

    public function register_admin_scripts() {
        wp_register_script( 'banner-admin-script', plugin_dir_url( __FILE__ ).'assets/js/ab-admin-scripts.js' ); 
        wp_enqueue_script( 'banner-admin-script' );  
    }
}

new WP_Action_Banner();

if (!function_exists('init_action_banners')) {
    function init_action_banners()
    {
        register_post_type('action_banner', array(
            'labels' => array(
                'name' => __('Banners', 'plugin-action-banner'), // The main name of the post type
                'singular_name' => __('Banner', 'plugin-action-banner'), // Additional name for post of type Book
                'add_new' => __('Add new', 'plugin-action-banner'),
                'add_new_item' => __('Add new banner', 'plugin-action-banner'),
                'edit_item' => __('Edit banner', 'plugin-action-banner'),
                'new_item' => __('New banner', 'plugin-action-banner'),
                'view_item' => __('View banner', 'plugin-action-banner'),
                'search_items' => __('Find banner', 'plugin-action-banner'),
                'not_found' => __('Banners not found', 'plugin-action-banner'),
                'not_found_in_trash' => __('Not found banners in trash', 'plugin-action-banner'),
                'parent_item_colon' => '',
                'menu_name' => __('Action banners', 'plugin-action-banner')
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('thumbnail', 'title')
        ));
    }
}

add_action('init', 'init_action_banners');

add_action("admin_init", "admin_init_action_banners");

if (!function_exists('admin_init_action_banners')) {
    function admin_init_action_banners()
    {
        add_meta_box("slides-meta", __("Slides", 'plugin-action-banner'), "slides_meta", "action_banner", "normal", "low");
    }
}

add_action("manage_posts_custom_column",  "action_banner_custom_columns");
add_filter("manage_edit-action_banner_columns", "action_banner_edit_columns");

if (!function_exists('action_banner_edit_columns')) {
    function action_banner_edit_columns($columns)
    {
        $columns["shortcode"] = __("Shortcode", 'plugin-action-banner');

        return $columns;
    }
}

if (!function_exists('action_banner_custom_columns')) {
    function action_banner_custom_columns($column)
    {
        global $post;
        $post_type = get_post_type($post->ID);
        if ($post_type != 'action_banner') {
            return;
        }
        switch ($column) {
            case "shortcode":
                echo '[action_banner id="' . $post->ID . '"]';
                break;
        }
    }
}

if (!function_exists('slides_meta')) {
    function slides_meta()
    {
        global $post;
        $custom = get_post_custom($post->ID);
        $slides = $custom["slides"][0];
        $main_height = $custom['main_height'][0];
        $relative_height = $custom['relative_height'][0];
        $timeout = $custom['slide_timeout'][0];
        $btn_color = $custom['btn_color'][0];
        $btn_hover_color = $custom['btn_hover_color'][0];
        if (empty($slides)) {
            $slides = Array('items' => Array());
        } else {
            $slides = json_decode($slides, true);
        }
        if (empty($main_height)) {
            $main_height = 400;
        }
        if (empty($relative_height)) {
            $relative_height = 300;
        }
        if (empty($timeout)) {
            $timeout = 5000;
        }
        if (empty($btn_color)) {
            $btn_color = '#149DDE';
        }
        if (empty($btn_hover_color)) {
            $btn_hover_color = '#00ACEE';
        }
        ?>
        <table class="form-table">
            <tr>
                <th>
                    <label><?php echo __('Shortcode', 'plugin-action-banner')?></label>
                </th>
                <td>
                    <input class="regular-text code" type="text" readonly value="[action_banner id=&quot;<?php echo get_the_ID();?>&quot;]">
                    <p class="description"><?php echo __('You can use this shortcode to insert banner.', 'plugin-action-banner')?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Main height', 'plugin-action-banner');?></label>
                </th>
                <td>
                    <input class="regular-text" type="number" name="main_height" value="<?php echo $main_height;?>"/>
                    <p class="description"><?php echo __('Height for large screens in pixels.', 'plugin-action-banner')?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Relative height', 'plugin-action-banner');?></label>
                </th>
                <td>
                    <input class="regular-text" type="number" name="relative_height" value="<?php echo $relative_height;?>"/>
                    <p class="description"><?php echo __('Height for small screens in pixels.', 'plugin-action-banner')?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Slide timeout', 'plugin-action-banner');?></label>
                </th>
                <td>
                    <input class="regular-text" type="number" name="slide_timeout" value="<?php echo $timeout;?>"/>
                    <p class="description"><?php echo __('Slide timeout in milliseconds.', 'plugin-action-banner')?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="btnColor"><?php echo __('Button color', 'plugin-action-banner');?></label>
                </th>
                <td>
                    <input type="color" id="btnColor" name="btn_color" value="<?php echo $btn_color; ?>" />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="btnHoverColor"><?php echo __('Button hover color', 'plugin-action-banner');?></label>
                </th>
                <td>
                    <input type="color" id="btnHoverColor" name="btn_hover_color" value="<?php echo $btn_hover_color; ?>" />
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Slides', 'plugin-action-banner');?></label>
                </th>
                <td>
                    <input id="slides-input" type="hidden" name="slides" value=""/>
                    <select id="select-slide" class="regular-text">
                        <option value="-1"><?php echo __('Select slide', 'plugin-action-banner');?></option>
                        <?php $iter = 'a';?>
                        <?php foreach ($slides['items'] as $key => $slide): ?>
                            <option value="<?php echo $key;?>"><?php echo $slide['title']?></option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <button id="add-slide" class="button"><?php echo __('Add slide', 'plugin-action-banner'); ?></button>
                    <button id="remove-slide" class="button" style="display: none;"><?php echo __('Remove slide', 'plugin-action-banner');?></button>
                    <button id="slide-up" class="button" style="display: none;"><?php echo __('Move up', 'plugin-action-banner'); ?></button>
                    <button id="slide-down" class="button" style="display: none;"><?php echo __('Move down', 'plugin-action-banner');?></button>
                </td>
            </tr>
        </table>
        <div id="slide-params" class="frame-container" style="display: none">
            <table id="slide-params-form" class="form-table">
                <tr>
                    <th>
                        <label><?php echo __('Slide title', 'plugin-action-banner');?></label>
                    </th>
                    <td>
                        <input class="regular-text" type="text" id="slide-title"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php echo __('Slide text', 'plugin-action-banner');?></label>
                    </th>
                    <td>
                        <textarea class="regular-text" id="slide-text"></textarea>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php echo __('Slide image', 'plugin-action-banner');?></label>
                    </th>
                    <td>
                        <input class="regular-text" type="text" id="slide-image"/><br>
                        <button id="add-slide-image" class="button"><?php echo __('From media library', 'plugin-action-banner'); ?></button>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php echo __('Horizontal alignment', 'plugin-action-banner');?></label>
                    </th>
                    <td>
                        <label>
                            <input type="radio" id="leftAlign" checked name="horizontal_align" value="left"/>
                            <span for="leftAlign"><?php echo __('Left', 'plugin-action-banner')?></span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" id="centerAlign" name="horizontal_align" value="center"/>
                            <span for="centerAlign"><?php echo __('Center', 'plugin-action-banner')?></span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" id="rightAlign" name="horizontal_align" value="right"/>
                            <span for="rightAlign"><?php echo __('Right', 'plugin-action-banner')?></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php echo __('Text color', 'plugin-action-banner');?></label>
                    </th>
                    <td>
                        <label>
                            <input type="radio" id="lightColor" checked name="text_color" value="light"/>
                            <span for="lightColor"><?php echo __('Light', 'plugin-action-banner')?></span>
                        <br>
                        <label>
                            <input type="radio" id="darkColor" name="text_color" value="dark"/>
                            <span for="darkColor"><?php echo __('Dark', 'plugin-action-banner')?></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php echo __('Buttons', 'plugin-action-banner');?></label>
                    </th>
                    <td>
                        <select id="select-button" class="regular-text">
                            <option value="-1"><?php echo __('Select buton', 'plugin-action-banner');?></option>
                        </select>
                        <br>
                        <button id="add-button" class="button"><?php echo __('Add button', 'plugin-action-banner'); ?></button>
                        <button id="remove-button" class="button" style="display:none"><?php echo __('Remove button', 'plugin-action-banner');?></button>
                    </td>
                </tr>
            </table>

            <div id="slide-buttons"></div>

            <div id="button-params" class="frame-container" style="display: none">
                <table id="buttons-param-table" class="form-table">
                    <tr>
                        <th>
                            <label><?php echo __('Button name', 'plugin-action-banner');?></label>
                        </th>
                        <td>
                            <input class="regular-text" type="text" id="button-key"/>
                            <p class="description"><?php echo __('Button label.', 'plugin-action-banner')?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label><?php echo __('Button link', 'plugin-action-banner');?></label>
                        </th>
                        <td>
                            <input class="regular-text" type="text" id="button-value"/>
                            <p class="description"><?php echo __('Full or relative link.', 'plugin-action-banner')?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>


        <script>
            jQuery(document).ready(function($) {
                setSlides(<?php echo json_encode($slides['items']);?>);
            });
        </script>
        <?php
    }
}

if ( ! function_exists( 'sanitize_text_or_array_field' ) ) {
    /**
     * Recursive sanitation for text or array
     *
     * @param $array_or_string (array|string)
     * @return mixed
     * @since  0.1
     */
    function sanitize_text_or_array_field($array_or_string)
    {
        if (is_string($array_or_string)) {
            $array_or_string = sanitize_text_field($array_or_string);
        } elseif (is_array($array_or_string)) {
            foreach ($array_or_string as $key => &$value) {
                if (is_array($value)) {
                    $value = sanitize_text_or_array_field($value);
                } else {
                    $value = sanitize_text_field($value);
                }
            }
        }

        return $array_or_string;
    }
}

if (!function_exists('save_action_stickers_meta')) {
    function save_action_stickers_meta($post_id)
    {
        $post_type = get_post_type( $post_id );
        if ($post_type != 'action_banner') {
            return;
        }

        if (!empty($_POST['main_height'])) {
            update_post_meta(
                $post_id,
                'main_height',
                (int)$_POST['main_height']
            );
        }

        if (!empty($_POST['relative_height'])) {
            update_post_meta(
                $post_id,
                'relative_height',
                (int)$_POST['relative_height']
            );
        }

        if (!empty($_POST['slide_timeout'])) {
            update_post_meta(
                $post_id,
                'slide_timeout',
                (int)$_POST['slide_timeout']
            );
        }

        if (!empty($_POST['btn_color'])) {
            update_post_meta(
                $post_id,
                'btn_color',
                $_POST['btn_color']
            );
        }

        if (!empty($_POST['btn_hover_color'])) {
            update_post_meta(
                $post_id,
                'btn_hover_color',
                $_POST['btn_hover_color']
            );
        }

        if (!empty($_POST['slides'])) {
            $json_encoded = str_replace('\\"', '"', $_POST['slides']);


            update_post_meta(
                $post_id,
                'slides',
                wp_json_encode(json_decode($json_encoded, true), JSON_UNESCAPED_UNICODE)
            );
        }
    }
}

add_action('save_post', 'save_action_stickers_meta');

if (!function_exists('action_banner_shortcode')) {
    function action_banner_shortcode($atts)
    {
        global $post;

        $rg = (object) shortcode_atts( [
            'id' => null
        ], $atts );


        if( ! $post = get_post( $rg->id ) )
            return '';

        $custom = get_post_custom($post->ID);
        $slides = $custom["slides"][0];
        if (empty($slides)) {
            $slides = Array('items' => Array());
        } else {
            $slides = json_decode($slides, true);
        }

        $main_height = $custom['main_height'][0];
        $relative_height = $custom['relative_height'][0];
        $timeout = $custom['slide_timeout'][0];
        $button_color = $custom['btn_color'][0];
        $button_hover_color = $custom['btn_hover_color'][0];

        $out = '<style>
                    .action-banner.banner-' . $rg->id . '{height:' . $main_height . 'px}
                    @media (max-width : 767px) {
                        .action-banner.banner-' . $rg->id . '{height:' . $relative_height .'px}
                    }
                    .action-banner.banner-' . $rg->id . ' div.ab-slide div.ab-wrapper div.ab-buttons button{background-color:' . $button_color . '}
                    .action-banner.banner-' . $rg->id . ' div.ab-slide div.ab-wrapper div.ab-buttons button:hover{background-color:' . $button_hover_color . '}
                </style>
                <script>
                    var abSlideTimeout = ' . $timeout . ';
                </script>
                <div class="action-banner banner-' . $rg->id . '">';
        foreach ($slides['items'] as $slide) {
            $img = $slide['image'];
            $header = $slide['title'];
            $text = $slide['text'];
            $buttons = $slide['buttons'];

            $label_color = $slide['text_color']; // 'dark' or 'light'
            $horizontal_alignment = $slide['h_align']; // 'left', 'center' or 'right'

            $out .= '
            <div class="ab-slide" style="background-image: url(' . $img . '); display: none">
                <div class="ab-blackout">
                	<div class="ab-wrapper ';
                    switch ($label_color) {
                        case 'dark':
                            $out .= 'dark';
                            break;
                        case 'light':
                            $out .= 'light';
                            break;
                        default:
                            $out .= 'dark';
                            break;
                    }
                    $out .= ' ';
                    switch ($horizontal_alignment) {
                        case 'left':
                            $out .= 'left';
                            break;
                        case 'center':
                            $out .= 'center';
                            break;
                        case 'right':
                            $out .= 'right';
                            break;
                        default:
                            $out .= 'center';
                            break;
                    }
                    $out .= '">
                        <div class="ab-header">' . $header . '</div>
                        <div class="ab-text">' . $text . '</div>
                        <div class="ab-buttons">';
                        foreach ($buttons as $name => $link) {
                            $out .= '<button onclick="window.open(\'' . $link . '\',\'_blank\')">' . $name . '</button>&nbsp;';
                        }
                        $out .= '
                        </div>
                    </div>
                </div>
            </div>';
        }
        $out .= '</div>';
        return $out;
    }
}

add_shortcode('action_banner', 'action_banner_shortcode');