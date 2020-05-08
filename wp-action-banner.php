<?php
/*
Plugin Name: Action Banner
Plugin Script: wp-action-banner.php
Description: Allows you to create custom banners, which consist of an image, text, a link, and a call to action.  Custom banners are easily output via shortcodes.
Version: 0.1.85
Author: Разработчики занаклейкой.рф
Text Domain: plugin-action-banner
*/

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );



class WP_Action_Banner{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init_texdomain'));
    }

    public function init_texdomain() {
        $mo_file_path = dirname(__FILE__) . '/languages/action-banner-'. determine_locale() . '.mo';
        load_textdomain( 'action-banner', $mo_file_path );
        load_plugin_textdomain('action-banner', false, dirname(plugin_basename(__FILE__)).'/languages/' );
    }
}

new WP_Action_Banner();


add_action("manage_posts_custom_column",  "portfolio_custom_columns");
add_filter("manage_edit-portfolio_columns", "portfolio_edit_columns");

function portfolio_edit_columns($columns){
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Portfolio Title",
        "description" => "Description",
        "year" => "Year Completed",
        "skills" => "Skills",
    );

    return $columns;
}
function portfolio_custom_columns($column){
    global $post;

    switch ($column) {
        case "description":
            the_excerpt();
            break;
        case "year":
            $custom = get_post_custom();
            echo $custom["year_completed"][0];
            break;
        case "skills":
            echo get_the_term_list($post->ID, 'Skills', '', ', ','');
            break;
    }
}

if (!function_exists('init_action_banners')) {
    function init_action_banners()
    {
        register_post_type('action_banner', array(
            'labels' => array(
                'name' => __('Banners', 'plugin-action-banner'), // Основное название типа записи
                'singular_name' => __('Banner', 'plugin-action-banner'), // отдельное название записи типа Book
                'add_new' => __('Add new', 'plugin-action-banner'),
                'add_new_item' => __('Add new banner', 'plugin-action-banner'),
                'edit_item' => __('Edit banner', 'plugin-action-banner'),
                'new_item' => __('New banner', 'plugin-action-banner'),
                'view_item' => __('View banner', 'plugin-action-banner'),
                'search_items' => __('Find banner', 'plugin-action-banner'),
                'not_found' => __('Banners not found', 'plugin-action-banner'),
                'not_found_in_trash' => __('Not found banners in trash', 'plugin-action-banner'),
                'parent_item_colon' => '',
                'menu_name' => __('Action Banners', 'plugin-action-banner')
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
            'supports' => array('thumbnail')
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
        if (empty($slides)) {
            $slides = Array('items' => Array());
        } else {
            $slides = json_decode($slides, true);
        }
        ?>
        <p><?php echo __('You can use this shortcode to insert banner', 'plugin-action-banner')?>[action_banner id="<?php echo get_the_ID();?>"]</p>
        <p><label>Slides:</label><br/>
            <input id="slides-input" type="hidden" name="slides" value="<?php echo json_encode($slides) ?>"/>
            <select id="select-input">
                <option value="-1"><?php echo __('Please select slide', 'plugin-action-banner');?></option>
                <?php foreach ($slides['items'] as $num => $slide): ?>
                    <option value="<?php echo $num;?>"><?php echo $slide['title']?></option>
                <?php endforeach; ?>
            </select>
            <button id="add-slide"><?php echo __('Add slide', 'plugin-action-banner'); ?></button>
            <button id="remove-slide" style="display: none;"><?php echo __('Remove slide', 'plugin-action-banner');?></button>
            <br>
            <input type="text" id="slide-title" style="display: none;">
            <textarea id="slide-text" style="display: none;"></textarea>
            <input type="text" id="slide-image" style="display: none;">
            <button id="save-slide" style="display: none;"><?php echo __('Save slide', 'plugin-action-banner')?></button>
            <br>
        <div id="slide-buttons"></div><br>
        <input type="text" id="slide-buttons-key" style="display: none;"/>
        <input type="text" id="slide-button-value" style="display: none;"/>
        <button id="add-slide-button" style="display: none;"><?php echo __('Add button', 'plugin-action-banner');?></button>

        <script>
            var slides = <?php echo json_encode($slides['items']);?>;
            var lastSlideLength = slides.length;
            var currentSlide = -1;

            function sanitize(string) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#x27;',
                    "/": '&#x2F;',
                };
                const reg = /[&<>"'/]/ig;
                return string.replace(reg, (match)=>(map[match]));
            }

            function onClickAddSlide() {
                var optionName = "<?php echo __('Slide', 'plugin-action-banner')?> " + slides.length;
                var o = new Option(optionName, slides.length);
                if (lastSlideLength == 0) {
                    o.selected = true;
                }
                slides.push({
                    title: sanitize(optionName),
                    text: "",
                    buttons: {},
                    image: ""
                });
                jQuery(o).html(optionName);
                var selectInput = jQuery('#select-input');
                selectInput.append(o);
                if (lastSlideLength == 0) {
                    selectInput.change();
                }
                lastSlideLength = slides.length;
                jQuery('#slides-input').val(JSON.stringify({items: slides}));
                return false;
            }

            function onRemoveSlide() {
                //TODO: IGOR
                return false;
            }

            function onChangeSlideSelect() {
                var itemNumber = jQuery(this).val();
                if (itemNumber >= slides.length || itemNumber < 0) {
                    jQuery('#slide-title').hide();
                    jQuery('#slide-text').hide();
                    jQuery('#slide-image').hide();
                    jQuery('#slide-buttons').hide();
                    jQuery('#add-slide-button').hide();
                    jQuery('#slide-buttons-key').hide();
                    jQuery('#slide-button-value').hide();
                    jQuery('#save-slide').hide();
                    return;
                }
                currentSlide = itemNumber;
                jQuery('#slide-title').val(slides[currentSlide].title).show();
                jQuery('#slide-text').val(slides[currentSlide].text).show();
                jQuery('#slide-image').val(slides[currentSlide].image).show();
                jQuery('#slide-buttons').html(JSON.stringify(slides[currentSlide].buttons)).show();
                jQuery('#add-slide-button').show();
                jQuery('#slide-buttons-key').show();
                jQuery('#slide-button-value').show();
                jQuery('#save-slide').show();
                //TODO: buttons
            }

            function onClickSaveSlide() {

                slides[currentSlide].title = sanitize(jQuery('#slide-title').val());
                slides[currentSlide].text = sanitize(jQuery('#slide-text').val());
                slides[currentSlide].image = (jQuery('#slide-image').val());
                jQuery('#slides-input').val(JSON.stringify({items: slides}));
                jQuery('#select-input option[value="'+ currentSlide  +'"]').html(slides[currentSlide].title);
                return false;
            }

            function onClickAddButton() {
                var key = jQuery('#slide-buttons-key').val();
                var value = jQuery('#slide-button-value').val();
                slides[currentSlide].buttons[key] = value;
                jQuery('#slide-buttons-key').val("");
                jQuery('#slide-button-value').val("");
                jQuery('#slide-buttons').html(JSON.stringify(slides[currentSlide].buttons));
                return false;
            }

            jQuery(function(){
                console.log(slides);
                console.log("OK");

                jQuery('#add-slide').click(onClickAddSlide);
                jQuery('#select-input').change(onChangeSlideSelect);
                jQuery('#remove-slide').click(onRemoveSlide);
                jQuery('#save-slide').click(onClickSaveSlide);
                jQuery('#add-slide-button').click(onClickAddButton);
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
        $out = '';
        foreach ($slides['items'] as $slide) {
            $img = $slide['image'];
            $header = $slide['title'];
            $text = $slide['text'];
            $buttons = $slide['buttons'];
            $out .= '
        <div class="action-banner" style="background-image: url(' . $img . ');">
        	<div class="ab-wrapper">
                <div class="ab-header">' . $header . '</div>
                <div class="ab-text">' . $text . '</div>
                <div class="ab-buttons">';
            foreach ($buttons as $name => $link) {
                $out .= '<button onclick="document.location=\'' . $link . '\'">' . $name . '</button>';
            }
            $out .= '
                </div>
            </div>
        </div>
        ';
        }
        return $out;
    }
}

add_shortcode('action_banner', 'action_banner_shortcode');