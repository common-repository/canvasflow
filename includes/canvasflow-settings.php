<?php
    $wp_canvasflow = new WP_Canvasflow();
    include( plugin_dir_path( __FILE__ ) . 'canvasflow-api.php');

    class Canvasflow_Settings {
        private $secret_key = '';
        private $merge_adjacent_paragraphs = FALSE;
        private $publication_id = '';
        private $publication_type = '';
        private $channel_name = '';
        private $collection_id = NULL;
        private $publications = array();
        private $style_id = 0;
        private $issue_id = 0;
        private $channel_id = 0;
        private $styles = array();     
        private $channels = array();
        private $issues = array();
        private $collections = array();
        private $valid_secret_key = TRUE;
        private $canvasflow_api;
		private $canvasflow_db;
        private $custom_posts_types = '';
		private $title_in_content = FALSE;
        private $auto_publish = FALSE;
        private $feature_image = FALSE;
        
        function __construct($canvasflow_api, $canvasflow_db) {
            $this->canvasflow_api = $canvasflow_api;
            $this->canvasflow_db = $canvasflow_db;
        }

        public function load_credentials() {
            if($this->valid_secret_key) {
                $credentials = $this->canvasflow_db->get_user_credentials();
                $db_secret_key = $credentials->secret_key;
                $db_merge_adjacent_paragraphs = $credentials->merge_adjacent_paragraphs;
                $db_publication_id = $credentials->publication_id;
                $db_publication_type = $credentials->publication_type;
                $db_style_id = $credentials->style_id;
                $db_issue_id = $credentials->issue_id;
                $db_channel_name = $credentials->channel_name;
                $db_collection_id = $credentials->collection_id;
				$db_channel_id = $credentials->channel_id;
                $db_custom_posts_types = $credentials->custom_posts_types;
				$db_title_in_content = $credentials->title_in_content;
                $db_auto_publish = $credentials->auto_publish;
                $db_feature_image = $credentials->feature_image;

                $this->merge_adjacent_paragraphs = $db_merge_adjacent_paragraphs;
				$this->title_in_content = $db_title_in_content;
                $this->auto_publish = $db_auto_publish;
                $this->feature_image = $db_feature_image;

                if($db_secret_key != '') {
                    $this->secret_key = $db_secret_key;
                    $this->publications = array();
                    foreach($this->canvasflow_api->get_remote_publications($db_secret_key) as $publication) {
                        array_push($this->publications, $publication);
                    }

                    if($db_publication_id != '') {
                        $this->publication_id = $db_publication_id;
                        $this->publication_type = $db_publication_type;

                        $this->styles = array();
                        foreach($this->canvasflow_api->get_styles_from_remote($this->publication_id, $db_secret_key) as $style) {
                            array_push($this->styles, $style);
                        }

                        if($db_style_id != '') {
                            $this->style_id = $db_style_id;
                        } else {
                            $this->style_id = $this->styles[0]['id'];
                        }

                        $this->channels = array();
                        foreach($this->canvasflow_api->get_channels_from_remote($this->publication_id, $db_secret_key) as $channel) {
                            array_push($this->channels, $channel);
                        }

                        if($db_channel_id != '') {
                            $this->channel_id = $db_channel_id;
                            foreach($this->channels as $channel){
                                if($channel['id'] == $this->channel_id) {
                                    $this->channel_name = $channel['type'];        
                                }
                            }
                        } else {
                            $this->channel_id = $this->channels[0]['id'];
                            $this->channel_name = $this->channels[0]['type'];
                        }


                        $this->issues = array();
                        foreach($this->canvasflow_api->get_issues_from_remote($this->publication_id, $db_secret_key) as $issue) {
                            array_push($this->issues, $issue);
                        }

                        if($db_issue_id != '') {
                            $this->issue_id = $db_issue_id;
                        } else {
                            $this->issue_id = $this->issues[0]['id'];
						}
						
						if($db_custom_posts_types != '') {
                            $this->custom_posts_types = $db_custom_posts_types;
                        }

                        $this->collections = array();
                        if ($this->channel_name == 'twixl') {
                            foreach($this->canvasflow_api->get_collections_by_publication($this->publication_id, $this->secret_key, $this->channel_name, $this->channel_id) as $collection) {
                                array_push($this->collections, $collection);
                            }
                            
                            if($db_collection_id != '') {
                                $this->collection_id = $db_collection_id;
                            } else {
                                if(sizeof($this->collections) > 0) {
                                    $this->collection_id = $this->collections[0]['id'];
                                }
                            }
                        }

                    } else {
                        $this->style_id = '';
                        $this->styles = array(); 

                        $this->db_issue_id = '';
                        $this->issues = array();

                        $this->db_channel_id = '';
						$this->channels = array();
						
						$this->custom_posts_types = '';
                    }
                }
            } else {
                $this->publication_id = '';
                $this->publication_type = '';
                $this->publications = array();

                $this->style_id = '';
                $this->styles = array();

                $this->db_issue_id = '';
                $this->issues = array();

                $this->channels = array();
                $this->channel_name = '';
                $this->channel_id = '';
				$this->collection_id = '';
				$this->custom_posts_types = '';
            }
        }

        public function update_credentials($request_secret_key, $request_merge_adjacent_paragraphs, $request_publication_id, $request_style_id, $request_issue_id, $request_publication_type, $request_collection_id, $request_channel_id, $request_custom_posts_types, $request_title_in_content, $request_auto_publish = 0, $request_feature_image = 0) {
            $credentials = $this->canvasflow_db->get_user_credentials();

            $db_secret_key = $credentials->secret_key;
            $db_publication_id = $credentials->publication_id;
            $db_style_id = $credentials->style_id;
            $db_issue_id = $credentials->issue_id;
            $db_publication_type = $credentials->publication_type;
            $db_collection_id = $credentials->collection_id;
            $db_channel_name = $credentials->channel_name;
			$db_channel_id = $credentials->channel_id;
            $db_custom_posts_types = $credentials->custom_posts_types;
            $db_feature_image = $credentials->feature_image;
            
            $this->merge_adjacent_paragraphs = $request_merge_adjacent_paragraphs;
			$this->title_in_content = $request_title_in_content;
            $this->auto_publish = $request_auto_publish;
            $this->feature_image = $request_feature_image;

            if($this->canvasflow_api->validate_secret_key($request_secret_key)) {
                if($db_secret_key != $request_secret_key) {
                    $this->update_secret_key($request_secret_key);
                    $this->canvasflow_db->reset_canvasflow_posts_style();
                    $this->canvasflow_db->reset_canvasflow_posts_issue();
                    $this->canvasflow_db->reset_canvasflow_collection();
                } else {
                    $this->secret_key = $request_secret_key;
                    if($db_publication_id != $request_publication_id) {
                        $this->update_publication($request_publication_id, $request_publication_type);
                        $this->canvasflow_db->reset_canvasflow_posts_style();
                        $this->canvasflow_db->reset_canvasflow_posts_issue();
                        $this->canvasflow_db->reset_canvasflow_collection();
                    } else {
                        $this->publication_id = $request_publication_id;
                        $this->publication_type = $request_publication_type;
                        $this->style_id = $request_style_id;
                        $this->issue_id = $request_issue_id;
                        // $this->channel_name = $db_channel_name;
                        $this->collection_id = $request_collection_id;
						$this->channel_id = $request_channel_id;
						$this->custom_posts_types = $request_custom_posts_types;
                        foreach($this->canvasflow_api->get_channels_from_remote($this->publication_id, $this->secret_key) as $channel) {
                            if($channel['id'] == $this->channel_id){
                                $this->channel_name = $channel['type'];
                            }
                        }
                    }
                }
                $this->save_credentials($this->secret_key, $this->merge_adjacent_paragraphs, $this->publication_id, $this->style_id, $this->issue_id, $this->publication_type, $this->channel_name, $this->collection_id, $this->channel_id, $this->custom_posts_types, $this->title_in_content, $this->auto_publish, $this->feature_image);
            } else {
                $this->valid_secret_key = FALSE;
                $this->secret_key = $request_secret_key;
            }
        }

        private function update_secret_key($new_secret_key) {
            $this->secret_key = $new_secret_key;
            $this->merge_adjacent_paragraphs = 1;
            $this->publication_id = '';
            $this->publications = array();
            $this->style_id = 0;
            $this->issue_id = 0;
            $this->collection_id = NULL;
            $this->styles = array();  
            $this->issues = array();
			$this->collections = array();
            $this->custom_posts_types = '';
			$this->title_in_content = 0;
            $this->auto_publish = 0;
            $this->feature_image = 0;
        }

        private function update_publication($new_publication_id, $new_publication_type) {
            $this->publication_id = $new_publication_id;
            $this->publication_type = $new_publication_type;

            $this->publications = array();
            foreach($this->canvasflow_api->get_remote_publications($this->secret_key) as $publication) {
                array_push($this->publications, $publication);
            }

            $this->styles = array();
            foreach($this->canvasflow_api->get_styles_from_remote($this->publication_id, $this->secret_key) as $style) {
                array_push($this->styles, $style);
            }
            $this->style_id = $this->styles[0]['id'];

            $this->issues = array();
            foreach($this->canvasflow_api->get_issues_from_remote($this->publication_id, $this->secret_key) as $issue) {
                array_push($this->issues, $issue);
            }
            $this->issue_id = $this->issues[0]['id'];

            $this->channels = array();
            foreach($this->canvasflow_api->get_channels_from_remote($this->publication_id, $this->secret_key) as $channel) {
                array_push($this->channels, $channel);
            }
            $this->channel_id = $this->channels[0]['id'];
            $this->channel_name = $this->channels[0]['type'];

            $this->collections = array();
            if ($this->channel_name == 'twixl') {
                foreach($this->canvasflow_api->get_collections_by_publication($this->publication_id, $this->secret_key, $this->channel_name, $this->channel_id) as $collection) {
                    array_push($this->collections, $collection);
				}
				if(sizeof($this->collections) > 0) {
					$this->collection_id = $this->collections[0]['id'];
				}
            }
        }

        public function render_view() {
            $secret_key = $this->secret_key;
            $merge_adjacent_paragraphs = $this->merge_adjacent_paragraphs;

            $publications = $this->publications;
            $styles = $this->styles;
            $issues = $this->issues;
            $collections = $this->collections;
            $channels = $this->channels;
            
            $selected_publication = $this->publication_id;
            $selected_publication_type = $this->publication_type;            
            $selected_style = $this->style_id;
            $selected_issue = $this->issue_id;
            $selected_collection_id = $this->collection_id;
            $selected_channel_id = $this->channel_id;

			$canvasflow_domain = $this->canvasflow_api->domain;
            $custom_posts_types = $this->custom_posts_types;
			$title_in_content = $this->title_in_content;
            $auto_publish = $this->auto_publish;
            $feature_image = $this->feature_image;

            include( plugin_dir_path( __FILE__ ) . 'views/canvasflow-settings-view.php');
        }

        private function save_credentials($secret_key, $merge_adjacent_paragraphs, $publication_id, $style_id, $issue_id, $publication_type, $channel_name, $collection_id, $channel_id, $custom_posts_types, $title_in_content, $auto_publish, $feature_image){
            if(!$this->canvasflow_db->exist_credentials()) {
                $this->canvasflow_db->insert_credentials_in_db($secret_key, $merge_adjacent_paragraphs, $title_in_content, $auto_publish, $feature_image);
            } else {
                $this->canvasflow_db->update_credentials_in_db($secret_key, $merge_adjacent_paragraphs, $publication_id, $style_id, $issue_id, $publication_type, $channel_name, $collection_id, $channel_id, $custom_posts_types, $title_in_content, $auto_publish, $feature_image);
            }
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

        $canvasflow_settings = new Canvasflow_Settings($canvasflow_api, $canvasflow_db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['cf_nonce_update_setting']) && wp_verify_nonce($_POST['cf_nonce_update_setting'],'cf-update-setting')){
                try {
                    $secret_key = '';
                    if(isset($_POST["secret_key"])) {
                        $secret_key = $_POST["secret_key"];
                    }
    
                    $merge_adjacent_paragraphs = 1;
                    if(isset($_POST["merge_adjacent_paragraphs"])) {
                        $merge_adjacent_paragraphs = $_POST["merge_adjacent_paragraphs"];
                    }

                    $title_in_content = 0;
                    if(isset($_POST["title_in_content"])) {
                        $title_in_content = $_POST["title_in_content"];
					}
					
					$auto_publish = 0;
                    if(isset($_POST["auto_publish"])) {
                        $auto_publish = $_POST["auto_publish"];
                    }

                    $feature_image = 0;
                    if(isset($_POST["feature_image"])) {
                        $feature_image = $_POST["feature_image"];
                    }
    
                    $publication_id = '';
                    $publication_type = '';
                    if(isset($_POST["publication_id"])) {
                        $publication_id = $_POST["publication_id"];
                        $publication_type = $canvasflow_api->get_publication_type($publication_id, $secret_key);
                    }
    
                    $style_id = '';
                    if(isset($_POST["style_id"])) {
                        $style_id = $_POST["style_id"];
                    }
    
                    $issue_id = '';
                    if(isset($_POST["issue_id"])) {
                        $issue_id = $_POST["issue_id"];
                    }
    
                    $collection_id = '';
                    if(isset($_POST["collection_id"])) {
                        $collection_id = $_POST["collection_id"];
                    }
    
                    $request_channel_id = '';
                    if(isset($_POST["channel_id"])) {
                        $request_channel_id = $_POST["channel_id"];
					}
					
					$request_custom_posts_types = '';
                    if(isset($_POST["custom_posts_types"])) {
						$request_custom_posts_types = $_POST["custom_posts_types"];
						$custom_posts_types_array = explode(",", $request_custom_posts_types);
						$custom_posts_types = array();

						foreach ($custom_posts_types_array as $value) {
							$value = str_replace(' ', '', $value);
							if(strlen($value) > 0){
								array_push($custom_posts_types, $value);
							}
						}

						if(sizeof($custom_posts_types) > 0) {
							$custom_posts_types = array_unique($custom_posts_types);
							$request_custom_posts_types = implode(",", $custom_posts_types);
						} elseif (sizeof($custom_posts_types) == 1) {
							$request_custom_posts_types = $custom_posts_types[0];
						} else {
							$request_custom_posts_types = '';
						}
                    }
    
                    $canvasflow_settings->update_credentials($secret_key, $merge_adjacent_paragraphs, $publication_id, $style_id, $issue_id, $publication_type, $collection_id, $request_channel_id, $request_custom_posts_types, $title_in_content, $auto_publish, $feature_image);
                    echo "<div class=\"success-message\"><div><b>Settings Updated</b></div></div>";
                }catch(Exception $e) {
                    echo "<div class=\"error-message\"><div><b>{$e->getMessage()}</b></div></div>";
                }
            } else {
                echo "<div class=\"error-message-static\"><div>You didn't send the correct credentials</div></div>";
            } 
        }

        $canvasflow_settings->load_credentials();
        $canvasflow_settings->render_view();
    } else {
		echo "</br>To learn more about MySQL storage engines and how to Convert MyISAM to InnoDB, please <a href='https://docs.canvasflow.io/article/199-unable-to-activate-cf-wp-plugin' target=\"_blank\" rel=\"noopener\"> click here</a>.";
	}
?>