<?php
    $wp_canvasflow = new WP_Canvasflow();

    class Canvasflow_Post_Manager {
        private $page = 1;
        private $search = NULL;
        private $secret_key;
        private $merge_adjacent_paragraphs;
        private $publication_id;
        private $default_style_id;

        private $canvasflow_db;

        function __construct($canvasflow_db) {
			$this->canvasflow_db = $canvasflow_db;
			
			if($this->canvasflow_db->has_valid_engines()) {
				$credentials = $this->canvasflow_db->get_user_credentials();
				$this->secret_key = $credentials->secret_key;
				$this->merge_adjacent_paragraphs = $credentials->merge_adjacent_paragraphs;
				$this->publication_id = $credentials->publication_id;
				$this->default_style_id = $credentials->style_id;
			}
        }

        function update_article_manager(){
            $this->canvasflow_db->update_article_manager();
        }

        function render_view(){
            $page = $this->page;
            $search = $this->search;

            $order_by = "canvasflow_id";
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
            
            $order_by_in_query = "canvasflow_post_id";

            switch ($order_by) {
                case "author":
                    $order_by_in_query = "display_name";
                    break;
                case "title":
                    $order_by_in_query = "title";
                    break;
                case "date":
                    $order_by_in_query = "post_modified_date";
                    break;
                case "modified_date":
                    $order_by_in_query = "post_modified_date";
                    break;
                case "canvasflow_id":
                    $order_by_in_query = "canvasflow_post_id";
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

            $posts = $this->canvasflow_db->get_posts_in_manager_by_filter($order, $order_by_in_query, $limit, $offset, $search);
            $total_of_post = $this->canvasflow_db->get_total_posts_in_manager_by_filter($search);
            $total_of_pages = ceil($total_of_post / $posts_by_view);

            $hostname = get_site_url();
            include( plugin_dir_path( __FILE__ ) . 'views/canvasflow-post-manager-view.php');
        }
    }

    $error_engine_count = 0;

    $user_id = wp_get_current_user()->ID;
    $canvasflow_db = new Canvasflow_DB($user_id);
    
    if(!$canvasflow_db->is_valid_wp_post_engine()) {
		$error_engine_count++;
        echo "<br><div class=\"error-message-static\"><div>Error: Unable to activate the Canvasflow for WordPress plugin.</br> </br> The <span style=\"color: grey;\">{$canvasflow_db->get_wp_posts_table_name()}</span> table engine must be configured as InnoDB <span style=\"color: grey;\">InnoDB</span> </br></br> To fix this problem run: <code style=\"background-color: #f1f1f1;\">ALTER TABLE {$canvasflow_db->get_wp_posts_table_name()} ENGINE=InnoDB</code> and re-activate the plugin</div></br></div>";
    }

    if(!$canvasflow_db->is_valid_wp_users_engine()) {
        $error_engine_count++;
        echo "<br><div class=\"error-message-static\"><div>Error: Unable to activate the Canvasflow for WordPress plugin.</br> </br> The <span style=\"color: grey;\">{$canvasflow_db->get_wp_users_table_name()}</span> table engine must be configured as InnoDB <span style=\"color: grey;\">InnoDB</span> </br></br> To fix this problem run: <code style=\"background-color: #f1f1f1;\">ALTER TABLE {$canvasflow_db->get_wp_users_table_name()} ENGINE=InnoDB</code> and re-activate the plugin</div></div>";
    }

    if($error_engine_count == 0) {
        $canvasflow_db->create_tables_if_not_exist();
        
        $canvasflow_post_manager = new Canvasflow_Post_Manager($canvasflow_db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $canvasflow_post_manager->update_article_manager();
        }

        $canvasflow_post_manager->render_view();
    } else {
		echo "</br>To learn more about MySQL storage engines and how to Convert MyISAM to InnoDB, please <a href='https://docs.canvasflow.io/article/199-unable-to-activate-cf-wp-plugin' target=\"_blank\" rel=\"noopener\"> click here</a>.";
	}
?>