<?php
    $wp_canvasflow = new WP_Canvasflow();
    if ( !defined('ABSPATH') ){
      define('ABSPATH', dirname(__FILE__) . '/');
    }
    require_once(ABSPATH . 'wp-config.php');
    
    include( plugin_dir_path( __FILE__ ) . 'canvasflow-api.php');
    $user_id = wp_get_current_user()->ID;
    $canvasflow_db = new Canvasflow_DB($user_id);

    class Canvasflow_Main {
        private $wpdb;
        private $user_id;

        private $page = 1;
        private $search = NULL;
        private $secret_key;
        private $merge_adjacent_paragraphs;
		private $title_in_content;
        private $auto_publish;
        private $feature_image;
        private $publication_id;
        private $publication_type;
        private $default_style_id;
        private $default_issue_id;
        private $default_collection_id;
        private $channel_name;
        private $canvasflow_api;
        private $canvasflow_db;

        private $canvasflow_tables_names;

        function __construct($user_id, $canvasflow_api, $canvasflow_db) {    
            $this->wpdb = $GLOBALS['wpdb'];        
            $this->user_id = $user_id;
            $this->canvasflow_db = $canvasflow_db;
            $this->canvasflow_api = $canvasflow_api;

            $this->canvasflow_tables_names = array();
            array_push($this->canvasflow_tables_names, $this->wpdb->prefix."canvasflow_credentials");
            array_push($this->canvasflow_tables_names, $this->wpdb->prefix."canvasflow_posts");

            $credentials = $this->canvasflow_db->get_user_credentials();
            $this->secret_key = $credentials->secret_key;
            $this->merge_adjacent_paragraphs = $credentials->merge_adjacent_paragraphs;
            $this->publication_id = $credentials->publication_id;
            $this->publication_type = $credentials->publication_type;
            $this->default_style_id = $credentials->style_id;
            $this->default_issue_id = $credentials->issue_id;
            $this->default_collection_id = $credentials->collection_id;
            $this->channel_name = $credentials->channel_name;
            $this->channel_id = $credentials->channel_id;
			$this->title_in_content = $credentials->title_in_content;
            $this->auto_publish = $credentials->auto_publish;
            $this->feature_image = $credentials->feature_image;
        }

        function render_view() {
            $search = $this->search;

            $secret_key = $this->secret_key;
            $merge_adjacent_paragraphs = $this->merge_adjacent_paragraphs;
            $publication_id = $this->publication_id;
            $publication_type = $this->publication_type;
            $default_style_id = $this->default_style_id;
            $default_issue_id = $this->default_issue_id;
            $default_collection_id = $this->default_collection_id;
            $channel_name = $this->channel_name;
            $channel_id = $this->channel_id;

            $page = $this->page;
            $search = $this->search;

            if(strlen(trim($secret_key)) == 0) {
                echo "<div class=\"error-message-static\"><div>Missing <a href=\"admin.php?page=canvasflow-settings\" style=\"color: #000\">API Key.</a></div></div>";
            } elseif (strlen(trim($publication_id)) == 0) {
                echo "<div class=\"error-message-static\"><div>Missing <a href=\"admin.php?page=canvasflow-settings\" style=\"color: #000\">Publication.</a></div></div>";
            } else {
                $styles = array();
                $styles_from_remote = $this->canvasflow_api->get_styles_from_remote($publication_id, $secret_key);
                if(sizeof($styles_from_remote) > 0) {
                    foreach($styles_from_remote as $style) {
                        array_push($styles, $style);
                    }
                }

                
                
                $issues = array();
                foreach($this->canvasflow_api->get_issues_from_remote($publication_id, $secret_key) as $issue) {
                    array_push($issues, $issue);
                }

                

                $collections = array();
                foreach($this->canvasflow_api->get_collections_by_publication($publication_id, $secret_key, $channel_name, $channel_id) as $collection) {
                    array_push($collections, $collection);
                }

                $order_by = "date";
                $order = "DESC";

                if(isset($_GET["search"])){
                    $search = $_GET["search"];
                } else if(isset($_POST["search"])){
                    $search = $_POST["search"];
                }

                if($search === ""){
                    $search = NULL;
                }
                
                if(isset($_GET['p'])){
                    $page = (int)$_GET["p"];
                }

                if(isset($_POST['p'])){
                    $page = (int)$_POST["p"];
                }

                if(isset($_GET["order"])){
                    $order = $_GET["order"];
                }

                if(isset($_GET["order_by"])){
                    $order_by = $_GET["order_by"];
                }
                
                $order_by_in_query = "published";

                switch ($order_by) {
                    case "author":
                        $order_by_in_query = "display_name";
                        break;
                    case "title":
                        $order_by_in_query = "title";
                        break;
                    case "date":
                        $order_by_in_query = "published";
                        break;
                    case "modified_date":
                        $order_by_in_query = "post_modified_date";
                        break;
                    case "type":
                        $order_by_in_query = "type";
                        break;
                    default:
                        $order_by_in_query = "published";
                }

                $posts_by_view = 20;

                
                $limit = $posts_by_view;
                $offset = (($page - 1) * $posts_by_view);

                $posts = $this->canvasflow_db->get_posts_in_main_by_filter($order, $order_by_in_query, $limit, $offset, $search);
                $total_of_post = $this->canvasflow_db->get_total_posts_in_main_by_filter($search);

                $total_of_pages = ceil($total_of_post / $posts_by_view);

                $hostname = get_site_url();
                include( plugin_dir_path( __FILE__ ) . 'views/canvasflow-main-view.php');
            }
        }

        function send_post($post_id, $style_id, $issue_id, $collection_id) {
			$author_id = wp_get_current_user()->ID;
			
			$current_user = wp_get_current_user();
			

            $post = get_post($post_id);
            $author_name = $this->km_get_users_name((int)$post->post_author);
            
            $post_title = apply_filters( 'the_title', $post->post_title);
			$post_content = apply_filters( 'the_content', $post->post_content);
			$post_date = $post->post_date;

            $auto_publish = $this->auto_publish;
            $feature_image = $this->feature_image;
            
            $response = $this->canvasflow_api->publish_post($post_content, $post_title, $post_id, $style_id, $issue_id, $this->secret_key, $this->merge_adjacent_paragraphs, $this->publication_id, $this->channel_id, $collection_id, $this->title_in_content, $author_name, $post_date, $auto_publish, $feature_image);
            $message = $response['message'];
            if($response['status'] === 'success') {
                echo "<div class=\"success-message\"><div><b>{$message}</b></div></div>";
                $this->canvasflow_db->update_post($post_id, $style_id, $issue_id, $collection_id);
            } else {
                echo "<div class=\"error-message\"><div><b>Upload failed</b> - {$message}</div></div>";
            }
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
    }

    

    $error_engine_count = 0;

    if(!$canvasflow_db->is_valid_wp_post_engine()) {
		$error_engine_count++;
        echo "<br><div class=\"error-message-static\"><div>Error: Unable to activate the Canvasflow for WordPress plugin.</br> </br> The <span style=\"color: grey;\">{$canvasflow_db->get_wp_posts_table_name()}</span> table engine must be configured as InnoDB <span style=\"color: grey;\">InnoDB</span> </br></br> To fix this problem run: <code style=\"background-color: #f1f1f1;\">ALTER TABLE {$canvasflow_db->get_wp_posts_table_name()} ENGINE=InnoDB</code> and re-activate the plugin</div></br></div>";
    }

    if(!$canvasflow_db->is_valid_wp_users_engine()) {
        $error_engine_count++;
        echo "<br><div class=\"error-message-static\"><div>Error: Unable to activate the Canvasflow for WordPress plugin.</br> </br> The <span style=\"color: grey;\">{$canvasflow_db->get_wp_users_table_name()}</span> table engine must be configured as InnoDB <span style=\"color: grey;\">InnoDB</span> </br></br> To fix this problem run: <code style=\"background-color: #f1f1f1;\">ALTER TABLE {$canvasflow_db->get_wp_users_table_name()} ENGINE=InnoDB</code> and re-activate the plugin</div></div>";
    }

    if($error_engine_count == 0) {
        $canvasflow_main = new Canvasflow_Main($user_id, $canvasflow_api, $canvasflow_db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['cf_nonce_send_article']) && wp_verify_nonce($_POST['cf_nonce_send_article'],'cf-send-article')){ 
                if(isset($_POST["id"])) {
                    $canvasflow_main->send_post($_POST["id"], $_POST["style_id"], $_POST["issue_id"], $_POST["collection_id"]);
                }
        
                $canvasflow_main->render_view();
            } else {
                echo "<div class=\"error-message-static\"><div>You didn't send the correct credentials</div></div>";
            }
        } else {
            $canvasflow_main->render_view();
        }
    } else {
		echo "</br>To learn more about MySQL storage engines and how to Convert MyISAM to InnoDB, please <a href='https://docs.canvasflow.io/article/199-unable-to-activate-cf-wp-plugin' target=\"_blank\" rel=\"noopener\"> click here</a>.";
	}
?>