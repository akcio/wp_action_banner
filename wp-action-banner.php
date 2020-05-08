<?php
/*
Plugin Name: Action Banner
Plugin Script: wp-action-banner.php
Description: Allows you to create custom banners, which consist of an image, text, a link, and a call to action.  Custom banners are easily output via shortcodes.
Version: 0.1.33
Author: Разработчики занаклейкой.рф
Text Domain: plugin-action-banner
*/

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );



class WP_Action_Banner{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init_texdomain'));

        //add single shortcode metabox to banner add/edit screen
//        add_action( 'admin_menu', array($this,'add_meta_boxes')); // add our custom meta boxes
//        add_action( 'admin_menu', array($this,'add_settings_link')); // add a link to the settings page under the banners menu
//
//        // add media buttons to admin
//        $cur_post_type = ( isset($_GET['post']) ? get_post_type(intval($_GET['post'])) : '' );
//        if( is_admin() && ( empty($_REQUEST['post_type']) || $_REQUEST['post_type'] !== 'banner' ) && ($cur_post_type !== 'banner') )
//        {
////            global $CustomBanners_MediaButton;
////            $banner_shortcode = get_option("custom_banners_banner_shortcode", 'banner');
////
////            $CustomBanners_MediaButton = new Gold_Plugins_Media_Button('Banners', 'images-alt');
////            $CustomBanners_MediaButton->add_button('Single Banner Widget', $banner_shortcode, 'singlebannerwidget', 'images-alt');
////            $CustomBanners_MediaButton->add_button('List of Banners Widget', $banner_shortcode, 'bannerlistwidget', 'images-alt');
////            $CustomBanners_MediaButton->add_button('Rotating Banner Widget',  $banner_shortcode, 'rotatingbannerwidget', 'images-alt');
//        }
    }

    public function init_texdomain() {
        $mo_file_path = dirname(__FILE__) . '/languages/action-banner-'. determine_locale() . '.mo';
        load_textdomain( 'action-banner', $mo_file_path );
        load_plugin_textdomain('action-banner', false, dirname(plugin_basename(__FILE__)).'/languages/' );
    }

//    function create_post_types()
//    {
//        $postType = array(
//            'name' => 'Banner',
//            'plural' => 'Banners',
//            'slug' => 'banners',
//            'menu_icon' => 'dashicons-images-alt'
//        );
//
//        $customFields = array();
//        $customFields[] = array('name' => 'target_url', 'title' => 'Target URL', 'description' => 'Where a user should be sent when they click on the banner or the call to action button', 'type' => 'text');
//        $customFields[] = array('name' => 'cta_text', 'title' => 'Call To Action Text', 'description' => 'The "Call To Action" (text) of the button. Leave this field blank to hide the call to action button.', 'type' => 'text');
//        $customFields[] = array('name' => 'css_class', 'title' => 'CSS Class', 'description' => 'Any extra CSS classes that you would like applied to this banner.', 'type' => 'text');
//        $this->add_custom_post_type($postType, $customFields);
//
//        //load list of current posts that have featured images
//        $supportedTypes = get_theme_support( 'post-thumbnails' );
//
//        //none set, add them just to our type
//        if( $supportedTypes === false ){
//            add_theme_support( 'post-thumbnails', array( 'banner' ) );
//            //for the banner images
//        }
//        //specifics set, add our to the array
//        elseif( is_array( $supportedTypes ) ){
//            $supportedTypes[0][] = 'banner';
//            add_theme_support( 'post-thumbnails', $supportedTypes[0] );
//            //for the banner images
//        }
//
//        //move featured image box to main column
//        add_action('add_meta_boxes', array($this,'custom_banner_edit_screen'));
//
//        //remove unused meta boxes
//        add_action( 'admin_init', array($this,'custom_banners_unused_meta'));
//
//        // move the post editor under the other metaboxes
//        add_action( 'add_meta_boxes', array($this, 'reposition_editor_metabox'), 0 );
//
//        // enforce correct order of metaboxes
//        add_action('admin_init', array($this, 'set_metabox_order'));
//    }
//
//    function register_taxonomies()
//    {
//        $this->add_taxonomy('banner_groups', 'banner', 'Banner Group', 'Banner Groups');
//    }
//    private function add_custom_post_type($post_type, $custom_fields) {
//        register_post_type( 'movies',
//            // CPT Options
//            array(
//                'labels' => $custom_fields,
//                'public' => true,
//                'has_archive' => true,
//                'rewrite' => array('slug' => 'movies'),
//                'show_in_rest' => true,
//
//            )
//        );
//    }
//    // Our custom post type function
//    function create_posttype() {
//
//        register_post_type( 'movies',
//            // CPT Options
//            array(
//                'labels' => array(
//                    'name' => __( 'Movies' ),
//                    'singular_name' => __( 'Movie' )
//                ),
//                'public' => true,
//                'has_archive' => true,
//                'rewrite' => array('slug' => 'movies'),
//                'show_in_rest' => true,
//
//            )
//        );
//    }
//// Hooking up our function to theme setup
//add_action( 'init', 'create_posttype' );

}

new WP_Action_Banner();

function my_custom_init(){
    register_post_type('book', array(
        'labels'             => array(
            'name'               => 'Книги', // Основное название типа записи
            'singular_name'      => 'Книга', // отдельное название записи типа Book
            'add_new'            => 'Добавить новую',
            'add_new_item'       => 'Добавить новую книгу',
            'edit_item'          => 'Редактировать книгу',
            'new_item'           => 'Новая книга',
            'view_item'          => 'Посмотреть книгу',
            'search_items'       => 'Найти книгу',
            'not_found'          =>  'Книг не найдено',
            'not_found_in_trash' => 'В корзине книг не найдено',
            'parent_item_colon'  => '',
            'menu_name'          => 'Книги'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('custom-fields','title', 'editor', 'thumbnail')
    ) );

    register_taxonomy("Skills", array("book"), array("hierarchical" => true, "label" => "Skills", "singular_label" => "Skill", "rewrite" => true));
}
add_action('init', 'my_custom_init');

add_action("admin_init", "admin_init");

function admin_init(){
    add_meta_box("year_completed-meta", "Year Completed", "year_completed", "book", "side", "low");
    add_meta_box("credits_meta", "Design & Build Credits", "credits_meta", "book", "normal", "low");
}

function year_completed(){
    global $post;
    $custom = get_post_custom($post->ID);
    $year_completed = $custom["year_completed"][0];
    ?>
    <label>Year:</label>
    <input name="year_completed" value="<?php echo $year_completed; ?>" />
    <?php
}

function credits_meta() {
    global $post;
    $custom = get_post_custom($post->ID);
    $designers = $custom["designers"][0];
    $developers = $custom["developers"][0];
    $producers = $custom["producers"][0];
    ?>
    <p><label>Designed By:</label><br />
        <textarea cols="50" rows="5" name="designers"><?php echo $designers; ?></textarea></p>
    <p><label>Built By:</label><br />
        <textarea cols="50" rows="5" name="developers"><?php echo $developers; ?></textarea></p>
    <p><label>Produced By:</label><br />
        <textarea cols="50" rows="5" name="producers"><?php echo $producers; ?></textarea></p>
    <?php
}

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
        <p><?php echo __('You can use this shortcode to insert banner')?>[action_banner_shortcode id="<?php echo get_the_ID();?>"]</p>
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
        if (empty($atts['id'])) {
            return '';
        }
        $custom = get_post_custom($atts['id']);
        $slides = $custom["slides"][0];
        if (empty($slides)) {
            $slides = Array('items' => Array());
        } else {
            $slides = json_decode($slides, true);
        }
        $out = 'NTNTNTNTNTNTNTNTNTTNTNNTN';
        foreach ($slides as $slide) {
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