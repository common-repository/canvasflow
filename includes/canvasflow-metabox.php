<?php
    class Canvasflow_Metabox {
        private $secret_key = '';
        private $publication_id = '';
        private $publication_type = '';
        private $channel_name = '';
        private $styles = array();
        private $issues = array();
        private $collections = array();
        private $canvasflow_api;
        private $canvasflow_db;

        function __construct() {
			include( plugin_dir_path( __FILE__ ) . 'canvasflow-api.php');
			
            $user_id = wp_get_current_user()->ID;
			$this->canvasflow_db = new Canvasflow_DB($user_id);
			
			if($this->canvasflow_db->has_valid_engines()) {
				$this->canvasflow_api = $canvasflow_api;
				$credentials = $this->canvasflow_db->get_user_credentials();
				$this->secret_key = $credentials->secret_key;
				$this->publication_id = $credentials->publication_id;
				$this->publication_type = $credentials->publication_type;

				$this->default_style_id = $credentials->style_id;
				$this->default_issue_id = $credentials->issue_id;   
				$this->default_collection_id = $credentials->collection_id;
				$this->channel_name = $credentials->channel_name;
				$this->channel_id = $credentials->channel_id;
			}
        }

        function load_arrays() {
            if($this->secret_key != '' && $this->publication_id) {
                $this->styles = array();
                foreach($this->canvasflow_api->get_styles_from_remote($this->publication_id, $this->secret_key) as $style) {
                    array_push($this->styles, $style);
                }

                $this->issues = array();
                foreach($this->canvasflow_api->get_issues_from_remote($this->publication_id, $this->secret_key) as $issue) {
                    array_push($this->issues, $issue);
                }

                if($this->channel_name == 'twixl') {
                    $this->collections = array();
                    foreach($this->canvasflow_api->get_collections_by_publication($this->publication_id, $this->secret_key, $this->channel_name, $this->channel_id) as $collection) {
                        array_push($this->collections, $collection);
                    }
                }
            }
		}

        function renderHTML($post) {
            $error_engine_count = 0;
    
			if(!$this->canvasflow_db->is_valid_wp_post_engine()) {
				$error_engine_count++;
				echo "<br><div class=\"error-message-static\"><div>Error: Unable to activate the Canvasflow for WordPress plugin.</br> </br> The <span style=\"color: grey;\">{$this->canvasflow_db->get_wp_posts_table_name()}</span> table engine must be configured as InnoDB <span style=\"color: grey;\">InnoDB</span> </br></br> To fix this problem run: <code style=\"background-color: #f1f1f1;\">ALTER TABLE {$this->canvasflow_db->get_wp_posts_table_name()} ENGINE=InnoDB</code> and re-activate the plugin</div></br></div>";
			}
        
            if(!$this->canvasflow_db->is_valid_wp_users_engine()) {
				$error_engine_count++;
				echo "<br><div class=\"error-message-static\"><div>Error: Unable to activate the Canvasflow for WordPress plugin.</br> </br> The <span style=\"color: grey;\">{$this->canvasflow_db->get_wp_users_table_name()}</span> table engine must be configured as InnoDB <span style=\"color: grey;\">InnoDB</span> </br></br> To fix this problem run: <code style=\"background-color: #f1f1f1;\">ALTER TABLE {$this->canvasflow_db->get_wp_users_table_name()} ENGINE=InnoDB</code> and re-activate the plugin</div></div>";
			}

            if($error_engine_count == 0) {
                $this->canvasflow_db->create_tables_if_not_exist();
                if(strlen(trim($this->secret_key)) > 0) {
                    if(strlen(trim($this->publication_id)) > 0) {
                        $post_id = $post->ID;
    
                        $this->issues = array();
                        $this->styles = array();
                        
                        $this->load_arrays();
                        $issues = $this->issues;
                        $styles = $this->styles;
                        $collections = $this->collections;
    
                        $selection = $this->canvasflow_db->get_selection_for_post($post_id);
    
                        $selected_style_id = $this->default_style_id;
                        if($selection->style_id != '') {
                            $selected_style_id = $selection->style_id;
                        }
    
                        $selected_issue_id = $this->default_issue_id;
                        if($selection->issue_id != '') {
                            $selected_issue_id = $selection->issue_id;
                        }
    
                        $selected_collection_id = $this->default_collection_id;
                        if($selection->issue_id != '') {
                            $selected_collection_id = $selection->collection_id;
                        }
    
                        $post_state = $this->canvasflow_db->get_post_state($post_id);
                        $post_state_content = "";
                        $post_state_style = "";
    
                        $publication_type = $this->publication_type;
                        if($post_state == "unpublished") {
                            $post_state_style = "meta-box-post-state-unpublished";
                            $post_state_content = "Unpublished";
                        } else if($post_state == "out_of_sync") {
                            $post_state_style = "meta-box-post-state-out-sync";
                            $post_state_content = "Out of sync";
                        } else {
                            $post_state_style = "meta-box-post-state-in-sync";
                            $post_state_content = "In sync";
                        }
                        
                        include( plugin_dir_path( __FILE__ ) . 'views/canvasflow-metabox-view.php');
                    } else {
                        echo "<div class=\"error-message-static\"><div>Missing <a href=\"admin.php?page=canvasflow-settings\" style=\"color: #000\">Publication.</a></div></div>";
                    }
                } else {
                    // echo "<div id=\"cf_test\">Test</div>";
                    echo "<div class=\"error-message-static\"><div>Missing <a href=\"admin.php?page=canvasflow-settings\" style=\"color: #000\">API Key.</a></div></div>";
                }
            } else {
				echo "</br>To learn more about MySQL storage engines and how to Convert MyISAM to InnoDB, please <a href='https://docs.canvasflow.io/article/199-unable-to-activate-cf-wp-plugin' target=\"_blank\" rel=\"noopener\"> click here</a>.";
			}
        }
    }
?>