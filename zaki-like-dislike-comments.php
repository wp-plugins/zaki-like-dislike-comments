<?php
/*
Plugin Name: Zaki Like Dislike Comments
Description: Add  a like / dislike rate system for comments
Author: Zaki Design
Version: 1.2
Author URI: http://www.zaki.it
*/

define('ZAKI_LIKE_DISLIKE_FILE',__FILE__);

// Classe main
require_once plugin_dir_path(ZAKI_LIKE_DISLIKE_FILE).'classes/class-zaki-plugins.php';
require_once plugin_dir_path(ZAKI_LIKE_DISLIKE_FILE).'classes/class-zaki-like-dislike.php';

// Hooks & Init
add_action('admin_init', 'ZakiLikeDislike_SettingsInit');
add_action('admin_menu', 'ZakiLikeDislike_AddMenuPages');
register_activation_hook(ZAKI_LIKE_DISLIKE_FILE, 'ZakiLikeDislike_Activation');
register_deactivation_hook( ZAKI_LIKE_DISLIKE_FILE, 'ZakiLikeDislike_Deactivation');

// Activation plugin
function ZakiLikeDislike_Activation() {
    
    // Install db table
    global $wpdb;
    $table_name = ZakiLikeDislike::getTableName();
    $wpdb->query("CREATE TABLE IF NOT EXISTS $table_name (
        `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `comment_id` INT( 11 ) NOT NULL ,
        `rate_like_value` INT( 11 ) NOT NULL DEFAULT  '0' ,
        `rate_dislike_value` INT( 11 ) NOT NULL DEFAULT  '0' ,
        `rate_like_ip` LONGTEXT NOT NULL ,
        `rate_dislike_ip` LONGTEXT NOT NULL
        ) ENGINE = MYISAM;");
    
    // Init options
    $settings = array(
        "usecss" => 1,
        "display_type" => "compact",
        "show" => 1
    );
    update_option('zaki_like_dislike_options', $settings);
}

// Deactivation plugin
function ZakiLikeDislike_Deactivation() {

    // Uninstall db table
    global $wpdb;
    $table_name = ZakiLikeDislike::getTableName();
    $wpdb->query("DROP TABLE IF EXISTS $table_name;");
    
    // Unregister options
    unregister_setting('zaki_like_dislike_options','zaki_like_dislike_options');
}

function ZakiLikeDislike_SettingsInit() {
    
    // Register options
    register_setting('zaki_like_dislike_options','zaki_like_dislike_options');
    
    // Add setting fields
    add_settings_section(
        'zaki_like_dislike_options_section_main',
        __('General Settings','zaki'),
        'ZakiLikeDislike_PageSetting_Section_Main_Callback',
        'zaki-like-dislike');
        
        add_settings_field(
            'zaki_like_dislike_op_usecss',
            __('Default CSS Style','zaki'),
            'ZakiLikeDislike_PageSetting_Section_Main_UseCSS_Callback',
            'zaki-like-dislike',
            'zaki_like_dislike_options_section_main');
        
        add_settings_field(
            'zaki_like_dislike_op_display_type',
            __('Rate display mode','zaki'),
            'ZakiLikeDislike_PageSetting_Section_Main_DisplayMode_Callback',
            'zaki-like-dislike',
            'zaki_like_dislike_options_section_main');
            
        add_settings_field(
            'zaki_like_dislike_op_show',
            __('Auto insert in theme','zaki'),
            'ZakiLikeDislike_PageSetting_Section_Main_Show_Callback',
            'zaki-like-dislike',
            'zaki_like_dislike_options_section_main');
}

// Sezione generale
function ZakiLikeDislike_PageSetting_Section_Main_Callback() {
    echo '';
}

    // Settaggio CSS di default
    function ZakiLikeDislike_PageSetting_Section_Main_UseCSS_Callback() {
        $settings = get_option('zaki_like_dislike_options');
        ?>
        <input name="zaki_like_dislike_options[usecss]" type="checkbox" value="1" class="code" <?php checked(1,$settings['usecss'],true); ?> />
        &nbsp;<?=__('Include default CSS for list','zaki')?>
        <p class="description">
            <?=__('Disable if you want to use your style.','zaki')?>
        </p>
        <?php
    }

    // Disply mode setting
    function ZakiLikeDislike_PageSetting_Section_Main_DisplayMode_Callback() {
        $settings = get_option('zaki_like_dislike_options');
        ?>
        <select name="zaki_like_dislike_options[display_type]">
            <option value="compact" <?php if($settings['display_type']=='compact') echo 'selected="selected"'; ?>><?=__('Compact mode','zaki')?></option>
            <option value="splitted" <?php if($settings['display_type']=='splitted') echo 'selected="selected"'; ?>><?=__('Splitted mode','zaki')?></option>
        </select><br />
        <p class="description">
            <?=__('Campact mode: Like and Dislike results will be grouped and displayed as a difference','zaki')?>
            <br />
            <?=__('Splitted mode: Like and Dislike results will not be grouped and displayed with their counter','zaki')?>
        </p>
        <?php
    }
    
    // Settaggio CSS di default
    function ZakiLikeDislike_PageSetting_Section_Main_Show_Callback() {
        $settings = get_option('zaki_like_dislike_options');
        ?>
        <input name="zaki_like_dislike_options[show]" type="checkbox" value="1" class="code" <?php checked(1,$settings['show'],true); ?> />
        &nbsp;<?=__('Show rate system before comment text?','zaki')?>
        <p class="description">
            <?=__('Alternatively, you can use this code in your comments loop:','zaki')?><br />
            <em><?=htmlentities("<?php if (class_exists('ZakiLikeDislike')) ZakiLikeDislike::getLikeDislikeHtml(); ?>");?></em>
        </p>
        <?php
    }

// Inizializzazione pagine menu
function ZakiLikeDislike_AddMenuPages() {

    //Controllo ed eventualmente includo il menu principale
    ZakiPlugins::checkMainMenu();
            
    // Pagine del plugins
    add_submenu_page(
        'zaki',
        __('Like Dislike Comments','zaki'),
        __('Like Dislike Comments','zaki'),
        'manage_options',
        'zaki-like-dislike',
        'ZakiLikeDislike_PageSettingHtml'
    );
    
}

// HTML Pagina principale di settaggio (main)
function ZakiLikeDislike_PageSettingHtml() {
    $settings = get_option('zaki_like_dislike_options');
    ?>  
    <div class="wrap zaki_like_dislike_page zaki_like_dislike_page_main">
        <?php screen_icon('options-general'); ?><h2><?=__('Zaki Like Dislike Comments','zaki')?></h2>      
        
        <form method="post" action="options.php">
            <?php settings_fields('zaki_like_dislike_options'); ?>
            <?php do_settings_sections('zaki-like-dislike'); ?>
            <p class="submit">
               <input name="submit" type="submit" id="submit" class="button-primary" value="<?=__('Save','zaki')?>" />
            </p>
        </form>
    </div>
    <?php
}


// Controllo inclusione CSS Frontend
function ZakiLikeDislike_CheckCssFrontendInclude() {
    $settings = get_option('zaki_like_dislike_options');
    if($settings['usecss']) : 
        wp_register_style('zaki_like_dislike_frontend_css',plugins_url('css/frontend.css', ZAKI_LIKE_DISLIKE_FILE));
        wp_enqueue_style('zaki_like_dislike_frontend_css');
    endif;   
}
add_action('init', 'ZakiLikeDislike_CheckCssFrontendInclude');

// Aggiunta codice nei commenti
function ZakiLikeDislike_AddPluginHml($content) {
    $settings = get_option('zaki_like_dislike_options');
    if($settings['show']) : 
        echo ZakiLikeDislike::getLikeDislikeHtml();
    endif;
    echo apply_filters( 'the_content', $content );
}
add_action('comment_text','ZakiLikeDislike_AddPluginHml');

// Ajax
require_once plugin_dir_path(ZAKI_LIKE_DISLIKE_FILE).'ajax/ajax-zaki-like-dislike.php';

// JS
require_once plugin_dir_path(ZAKI_LIKE_DISLIKE_FILE).'js/js-zaki-like-dislike.php';
