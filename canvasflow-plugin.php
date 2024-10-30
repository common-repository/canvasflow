<?php
/*
  Plugin Name: Canvasflow for WordPress
  Description: This out-of-the-box connector provides a quick and simple way to push your blog content directly to an existing Canvasflow publication.
  Version: 1.5.5
    Developer:  Canvasflow
    Developer URI: https://canvasflow.io
  License: GNU General Public License v3.0
  Text Domain: wp-canvasflow
*/

require_once(plugin_dir_path( __FILE__ ) . 'includes/canvasflow-db.php');

register_uninstall_hook( __FILE__, 'wpc_uninstall' );

/*
* Actions perform on de-activation of plugin
*/
function wpc_uninstall() {
	require_once(ABSPATH . 'wp-config.php');
	$wpdb = $GLOBALS['wpdb'];
	
	$author_id = wp_get_current_user()->ID;
    $canvasflow_db = new Canvasflow_DB($author_id);

    $canvasflow_db->delete_canvasflow_category();
	$canvasflow_db->delete_canvasflow_version_table();
	
	$canvasflow_tables_names = array();
    array_push($canvasflow_tables_names, $wpdb->prefix."canvasflow_credentials");
	array_push($canvasflow_tables_names, $wpdb->prefix."canvasflow_posts");
		
    foreach($canvasflow_tables_names as $table_name) {
        $canvasflow_db->delete_table($table_name);
	}
}

class WP_Canvasflow{
    private $wpdb;
    public static $version = "1.5.5";
    private $canvasflow_db;
    private $canvasflow_tables_names;
    // Constructor
    function __construct() {
        $this->wpdb = $GLOBALS['wpdb'];
        $this->canvasflow_db = new Canvasflow_DB();
        
        $this->canvasflow_tables_names = array();
        array_push($this->canvasflow_tables_names, $this->wpdb->prefix."canvasflow_credentials");
        array_push($this->canvasflow_tables_names, $this->wpdb->prefix."canvasflow_posts");

        add_action( 'admin_menu', array( $this, 'wpc_add_menu' ));

        register_activation_hook( __FILE__, array( $this, 'wpc_install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'wpc_deactivate' ) );
        add_action('admin_enqueue_scripts', array( $this, 'register_style'));
        add_action( 'upgrader_process_complete', array( $this, 'wpc_update'), 10, 2);
    }

    /*
      * Actions perform at loading of admin menu
      */
    function wpc_add_menu() {
        add_menu_page( 
            __('Canvasflow'), // Page Title
            __('Canvasflow'), // Menu Title
            'manage_options', 
            'wp-canvasflow-plugin', 
            '', 
            plugins_url('assets/img/favicon.png', __FILE__),
            '2' // Position
        );
        add_submenu_page( 
        	'wp-canvasflow-plugin', 
        	'Upload ' . ' Manager', 
        	'Upload Manager', 
        	'manage_options', 
        	'wp-canvasflow-plugin', 
        	array(
				$this,
                'wpc_page_main'
            )
        );
        add_submenu_page( 
        	'wp-canvasflow-plugin', 
        	'Posts ' . ' Manager', 
        	'Posts Manager', 
        	'manage_options', 
        	'canvasflow-posts-manager', 
        	array(
				$this,
                'wpc_post_manager'
            )
        );
        add_submenu_page( 
        	'wp-canvasflow-plugin', 
        	'Canvasflow ' . ' Settings', 
        	'Settings', 
        	'manage_options', 
        	'canvasflow-settings', 
        	array(
				$this,
                'wpc_page_settings'
            )
        );
    }

    function register_style( $page ) {
        wp_enqueue_style( 'cf-style', plugins_url('assets/css/style.css', __FILE__));
    }

    /*
     * Actions perform on loading of menu pages
     */
    function wpc_page_main() {
        include( plugin_dir_path( __FILE__ ) . 'includes/canvasflow-main.php');
    }
    function wpc_page_settings() {
        include( plugin_dir_path( __FILE__ ) . 'includes/canvasflow-settings.php');
    }
    function wpc_post_manager() {
        include( plugin_dir_path( __FILE__ ) . 'includes/canvasflow-post-manager.php');
    }
    /*
     * Actions perform on activation of plugin
     */
    function wpc_install() {
        require_once(ABSPATH . 'wp-config.php');
        if($this->canvasflow_db->is_valid_wp_post_engine() && $this->canvasflow_db->is_valid_wp_users_engine()) {
            if(!$this->canvasflow_db->exist_version_table()) {
                $this->initialize_canvasflow_db();
            } else {
                $canvasflow_version_db = $this->canvasflow_db->get_canvasflow_version();
                if(self::$version != $canvasflow_version_db) {
                    $this->canvasflow_db->migrate_table_data($this->canvasflow_tables_names, self::$version);
                    wp_create_category('Canvasflow');
                }
            }
        }
	}


    function wpc_deactivate() {
        $this->canvasflow_db->delete_canvasflow_category();
    }

    function wpc_update($upgrader_object, $options) {
        if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
            $canvasflow_version_db = $this->canvasflow_db->get_canvasflow_version();
            if(self::$version != $canvasflow_version_db) {
                $this->canvasflow_db->migrate_table_data($this->canvasflow_tables_names, self::$version);
                wp_create_category('Canvasflow');
            }
         }
    }

    function initialize_canvasflow_db() {
        $this->canvasflow_db->create_canvasflow_version_table();
        $this->canvasflow_db->set_canvasflow_version(self::$version);
        $this->canvasflow_db->create_canvasflow_posts_table();
        $this->canvasflow_db->create_canvasflow_credentials_table();
        wp_create_category('Canvasflow');
    }
}
new WP_Canvasflow();

/* Define the custom box */
add_action( 'add_meta_boxes', 'canvasflow_add_custom_box');
add_action( 'admin_enqueue_scripts', 'register_script' );
add_action( 'wp_ajax_send_to_cf_action', 'send_to_cf_action');

function register_script($hook) {
	if ('post.php' !== $hook) {
        return;
	}

	//wp_enqueue_script('cf_metabox_script', plugin_dir_url(__FILE__) . 'assets/js/cf-metabox.js');
	
	wp_register_script('cf_metabox_script', plugin_dir_url(__FILE__) . 'assets/js/cf-metabox.js', array(), false, true);
	wp_localize_script( 'cf_metabox_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
	wp_enqueue_script('cf_metabox_script');
}

/* Adds a box to the main column on the Post and Page edit screens */
function canvasflow_add_custom_box() {
	$title = 'Canvasflow';
	$author_id = wp_get_current_user()->ID;
    $canvasflow_db = new Canvasflow_DB($author_id);
	$screens = array( 'post', 'page');
	foreach($canvasflow_db->get_custom_posts_types() as $custom_post_type) {
		array_push($screens, $custom_post_type);
	}
    add_meta_box( 'cf_post', $title, 'cf_editor_meta_box' , $screens, 'side');
}

/* Prints the box content */
function cf_editor_meta_box( $post ) {

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'canvasflow_noncename' );

    include( plugin_dir_path( __FILE__ ) . 'includes/canvasflow-metabox.php');
    $metaBox = new Canvasflow_Metabox();
    $metaBox->renderHTML($post);
}

function send_to_cf_action() {
    if (isset($_POST['cf_nonce_send_article']) && wp_verify_nonce($_POST['cf_nonce_send_article'],'cf-send-article')){ 
        if(isset($_POST["id"])) {
            $response = post_article($_POST["id"], $_POST["style_id"], $_POST["issue_id"], $_POST["collection_id"]);
            wp_die($response['message'], 'Response', array('response' => $response['code']));
        }
    } else {
        wp_die("You didn't send the correct credentials", "Missing Arguments", array('response' => 403));
    }
}

function post_article($post_id, $style_id, $issue_id, $collection_id) {
    require_once(plugin_dir_path( __FILE__ ) . 'includes/canvasflow-api.php');

	$post = get_post($post_id);
	$post_title = apply_filters( 'the_title', $post->post_title);
	$post_content = apply_filters( 'the_content', $post->post_content);

	$post_date = $post->post_date;

    /*$post_title = $post->post_title; 
	$post_content = $post->post_content;*/
	
	$author_id = wp_get_current_user()->ID;
    $canvasflow_db = new Canvasflow_DB($author_id);

    $credentials = $canvasflow_db->get_user_credentials();
    $secret_key = $credentials->secret_key;
    $merge_adjacent_paragraphs = $credentials->merge_adjacent_paragraphs;
    $publication_id = $credentials->publication_id;
    $channel_id = $credentials->channel_id;
	$title_in_content = $credentials->title_in_content;
    $auto_publish = $credentials->auto_publish;
    $feature_image = $credentials->feature_image;

    $current_user = wp_get_current_user();
    

    $author_name = km_get_users_name((int)$post->post_author);

    $response = $canvasflow_api->publish_post($post_content, $post_title, $post_id, $style_id, $issue_id, $secret_key, $merge_adjacent_paragraphs, $publication_id, $channel_id, $collection_id, $title_in_content, $author_name, $post_date, $auto_publish, $feature_image);
    if($response['status'] === 'success') {   
        $canvasflow_db->update_post($post_id, $style_id, $issue_id, $collection_id);
    }

    return $response;
}

function km_get_users_name( $user_id = null ) {
    $user_info = $user_id ? new WP_User( $user_id ) : wp_get_current_user();
    if ( $user_info->first_name ) {
        if ( $user_info->last_name ) {
            return $user_info->first_name . ' ' . $user_info->last_name;
        }
        return $user_info->first_name;
    }
    return $user_info->display_name;
}
?>