<?php
    if ( !defined('ABSPATH') ){
        define('ABSPATH', dirname(__FILE__) . '/');
    }

    require_once(ABSPATH . 'wp-config.php');
    class Canvasflow_DB {
        private $wpdb;
        private $user_id;
        private $cf_posts_table_name;
        private $cf_credentials_table_name;
        private $wp_users_table_name;
        private $wp_posts_table_name;

        function __construct($user_id = null) {
            $this->wpdb = $GLOBALS['wpdb'];
            $this->user_id = $user_id;
            $this->cf_credentials_table_name = $this->wpdb->prefix."canvasflow_credentials";
            $this->cf_posts_table_name = $this->wpdb->prefix."canvasflow_posts";

            $this->wp_users_table_name = $this->wpdb->users;
            $this->wp_posts_table_name = $this->wpdb->posts;
        }

        public function get_wp_users_table_name() {
            return $this->wp_users_table_name;
		}
		
		public function has_valid_engines() {
			$error_engine_count = 0;
    
			if(!$this->is_valid_wp_post_engine()) {
				$error_engine_count++;
			}

			if(!$this->is_valid_wp_users_engine()) {
				$error_engine_count++;
			}

			if($error_engine_count == 0) {
				return TRUE;
			}

			return FALSE;
		}

        public function get_wp_posts_table_name() {
            return $this->wp_posts_table_name;
        }

        public function get_user_credentials(){
            $user_id = $this->user_id;
            $query = "SELECT secret_key, merge_adjacent_paragraphs, publication_id, style_id, issue_id, publication_type, channel_name, collection_id, channel_id, custom_posts_types, title_in_content, auto_publish, feature_image  FROM {$this->cf_credentials_table_name} LIMIT 1;";
            $this->wpdb->query($query);
            
            $credentials = $this->wpdb->get_results($query);
            if(sizeof($credentials) > 0) {
                return $credentials[0];
            } else {
                $credential = new stdClass();
                $credential->secret_key = '';
                $credential->merge_adjacent_paragraphs = FALSE;
                $credential->publication_id = '';
                $credential->style_id = '';
                $credential->issue_id = '';
                $credential->publication_type = '';
                $credential->channel_name = '';
                $credential->collection_id = '';
				$credential->channel_id = '';
                $credential->custom_posts_types = '';
				$credential->title_in_content = FALSE;
                $credential->auto_publish = FALSE;
                $credential->feature_image = FALSE;
                return $credential;
            }
        }

        public function reset_canvasflow_posts_style() {
            $user_id = $this->user_id;
            $query = "UPDATE {$this->cf_posts_table_name} SET style_id = NULL;";
            $this->wpdb->query($query);
        }

        public function reset_canvasflow_posts_issue() {
            $user_id = $this->user_id;
            $query = "UPDATE {$this->cf_posts_table_name} SET issue_id = NULL;";
            $this->wpdb->query($query);
        }

        public function reset_canvasflow_collection() {
            $user_id = $this->user_id;
            $query = "UPDATE {$this->cf_posts_table_name} SET collection_id = NULL;";
            $this->wpdb->query($query);
        }

        public function exist_credentials(){
            $query = "SELECT * FROM {$this->cf_credentials_table_name} LIMIT 1;";
            $credentials = $this->wpdb->get_results($query);
            if(sizeof($credentials) > 0) {
                return TRUE;
			}
			
			return FALSE;
        }

        public function exist_post($post_id) {
            $post_id = esc_sql($post_id);

            $query = "SELECT * FROM {$this->cf_posts_table_name} WHERE post_id = {$post_id};";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return TRUE;
            }
            return FALSE;
        }

        public function insert_credentials_in_db($secret_key, $merge_adjacent_paragraphs = 0, $title_in_content = 0, $auto_publish = 0, $feature_image = 0) {
            $secret_key = esc_sql($secret_key);
			$merge_adjacent_paragraphs = esc_sql($merge_adjacent_paragraphs);
			$title_in_content = esc_sql($title_in_content);
            $auto_publish = esc_sql($auto_publish);
            $feature_image = esc_sql($feature_image);

            $user_id = $this->user_id;
            $query = "INSERT INTO {$this->cf_credentials_table_name} (user_id, secret_key, merge_adjacent_paragraphs, publication_id, style_id, issue_id, publication_type, custom_posts_types, title_in_content, auto_publish, feature_image) VALUES ({$user_id}, \"{$secret_key}\", {$merge_adjacent_paragraphs}, \"\", NULL, NULL, \"\", \"\", {$title_in_content}, {$auto_publish}, {$feature_image});";
            $this->wpdb->query($query);
        }

        public function update_credentials_in_db($secret_key, $merge_adjacent_paragraphs = 1, $publication_id = '', $style_id = 0, $issue_id = 0, $publication_type = "", $channel_name = "", $collection_id = NULL, $channel_id = NULL, $custom_posts_types = '', $title_in_content = 0, $auto_publish = 0, $feature_image = 0) {
            $secret_key = esc_sql($secret_key);
            $publication_id = esc_sql($publication_id);
			$merge_adjacent_paragraphs = esc_sql($merge_adjacent_paragraphs);
			$title_in_content = esc_sql($title_in_content);
            $style_id = esc_sql($style_id);
            $issue_id = esc_sql($issue_id);
            $publication_type = esc_sql($publication_type);
			$channel_name = esc_sql($channel_name);
			$custom_posts_types = esc_sql($custom_posts_types);
            $auto_publish = esc_sql($auto_publish);
            $feature_image = esc_sql($feature_image);

            $user_id = $this->user_id;
            
            $query = "";
            if($merge_adjacent_paragraphs == '') {
                $merge_adjacent_paragraphs = 1;
            } else {
                $merge_adjacent_paragraphs = esc_sql($merge_adjacent_paragraphs);
            }

            if($collection_id == NULL || $collection_id == '') {
                $collection_id = 'NULL';
            } else {
                $collection_id = esc_sql($collection_id);
            }

            if($channel_id == NULL || $channel_id == '') {
                $channel_id = 'NULL';
            } else {
                $channel_id = esc_sql($channel_id);
            }

            if($style_id == 0) {
			$query = "UPDATE {$this->cf_credentials_table_name} SET secret_key = \"{$secret_key}\", merge_adjacent_paragraphs = {$merge_adjacent_paragraphs}, publication_id = \"{$publication_id}\", style_id = NULL, publication_type = \"{$publication_type}\", channel_name = \"{$channel_name}\", collection_id = {$collection_id}, channel_id = {$channel_id}, custom_posts_types = \"{$custom_posts_types}\", title_in_content = {$title_in_content}, auto_publish = {$auto_publish}, feature_image = {$feature_image}";
            } else {
                $query = "UPDATE {$this->cf_credentials_table_name} SET secret_key = \"{$secret_key}\", merge_adjacent_paragraphs = {$merge_adjacent_paragraphs}, publication_id = \"{$publication_id}\", style_id = {$style_id}, publication_type = \"{$publication_type}\", channel_name = \"{$channel_name}\", collection_id = {$collection_id}, channel_id = {$channel_id}, custom_posts_types = \"{$custom_posts_types}\", title_in_content = {$title_in_content}, auto_publish = {$auto_publish}, feature_image = {$feature_image}";
            }

            if($issue_id == 0) {
                $query .= ", issue_id = NULL;";
            } else {
                $query .= ", issue_id = {$issue_id};";
            }

            // var_dump($query);

            $this->wpdb->query($query);
        }

        public function insert_post($post_id){
            $author_id = $this->user_id;
            $post_id = esc_sql($post_id);

            $time = current_time( 'mysql' );

            $query = "INSERT INTO {$this->cf_posts_table_name} (post_id, author_id, published) 
            VALUES ({$post_id}, {$author_id}, '{$time}');";
            $this->wpdb->query($query);
        }

        public function update_post($post_id, $style_id, $issue_id, $collection_id){
            $author_id = $this->user_id;

            $post_id = esc_sql($post_id);
            $style_id = esc_sql($style_id);
            $issue_id = esc_sql($issue_id);
            $collection_id = esc_sql($collection_id);

            $time = current_time( 'mysql' );
            if($collection_id == NULL) {
               $collection_id = 'NULL';
            }
            $query = "INSERT INTO {$this->cf_posts_table_name} (post_id, author_id, published, style_id, issue_id, collection_id) VALUES ({$post_id}, {$author_id}, '{$time}', {$style_id}, {$issue_id}, {$collection_id});";  
            
            if($this->exist_post($post_id)) {
                $query = "UPDATE {$this->cf_posts_table_name} SET author_id = {$author_id}, published = '{$time}', style_id = {$style_id}, issue_id = {$issue_id}, collection_id = {$collection_id} WHERE post_id = {$post_id};";
            }

            $this->wpdb->query($query);
        }

        public function get_selection_for_post($post_id) {
            $post_id = esc_sql($post_id);
            $query = "SELECT style_id, issue_id, collection_id FROM {$this->cf_posts_table_name} WHERE post_id = {$post_id} LIMIT 1;";

            $selections = $this->wpdb->get_results($query); 
            if(sizeof($selections) > 0) {
                return $selections[0];
            } else {
                $credential = new stdClass();
                $credential->style_id = '';
                $credential->issue_id = '';
                $credential->collection_id = '';
                return $credential;
            }
		}
		
		public function get_custom_posts_types() {
			$query = "SELECT custom_posts_types FROM {$this->cf_credentials_table_name} LIMIT 1;";

			$result = $this->wpdb->get_results($query);

			if(sizeof($result) > 0) {
				$response = $result[0]->custom_posts_types;
				if(strlen($response) > 0) {
					return explode(",", $response);
				}
            }

			return array();
		}

        public function get_post_state($post_id) {
            $post_id = esc_sql($post_id);
            
            $query = "SELECT canvasflow_posts.id as id, canvasflow_posts.published as published, post.post_modified as post_modified_date FROM {$this->wp_posts_table_name} as post LEFT JOIN {$this->cf_posts_table_name} as canvasflow_posts ON(post.id = canvasflow_posts.post_id) WHERE canvasflow_posts.post_id = ${post_id} LIMIT 1;";

            $posts = $this->wpdb->get_results($query); 
            if(sizeof ($posts) == 0) {
                return "unpublished";
            } else {
                $post = $posts[0];
                $published = $post->published;
                $post_modified_date = $post->post_modified_date;
                if(strtotime($post_modified_date) > strtotime($published)){
                    return "out_of_sync";
                } else {
                    return "in_sync";
                }
            }
        }

        public function update_article_manager(){
            $query = "SELECT post.id as id , post.post_title as title, post.post_content as content,  users.display_name as display_name, users.ID as user_id, canvasflow_posts.published as published, canvasflow_posts.ID as canvasflow_post_id, post.post_modified as post_modified_date FROM {$this->wp_posts_table_name} as post LEFT JOIN {$this->wp_users_table_name} as users  ON(post.post_author=users.ID) LEFT JOIN {$this->cf_posts_table_name} as canvasflow_posts ON(post.ID=canvasflow_posts.post_id)";

            $count = 0;
            $checksMap = array();
            foreach($_POST as $key => $value){
                if (is_numeric($key)) { 
                    $checksMap[(string) $key] = (int) $value;
                    if($count > 0) {
                        $query .= " OR post.id = ".$key;
                    } else {
						$query .= " WHERE ";
                        $query .= "post.id = ".$key;
                    }
                    $count++;
                }
            }

            $posts = array();

            $result_posts = $this->wpdb->get_results($query);
            foreach ( $result_posts as $post ){
                array_push($posts, $post);
            }

            $posts_to_delete = array();
            $posts_to_create = array();

            for($i = 0; $i < count($posts); $i++){
                $post = $posts[$i];

                $post_id = $post->id;

                $exist = 0;
                if($post->canvasflow_post_id != NULL) {
                    $exist = 1;
                }

                if($exist === 0 && $checksMap[$post_id] == 1){
                    array_push($posts_to_create, $post);
                } else if($exist === 1 && $checksMap[$post_id] == 0){
                    array_push($posts_to_delete, $post);
                }
            }

            $total_for_add = count($posts_to_create);
            $total_for_delete = count($posts_to_delete);

            if(($total_for_add + $total_for_delete) > 0) {
                try {
                    $this->add_post_to_manager($posts_to_create);
                    $this->delete_post_from_manager($posts_to_delete);
                    echo "<div class=\"success-message\"><div><b>Article manager updated</b></div></div>";
                } catch(Exception $e) {
                    echo "<div class=\"error-message\"><div><b>{$e->getMessage()}</b></div></div>";
                }  
            }
        }

        private function add_post_to_manager($posts) {
            if(count($posts) > 0) {
                $query = "INSERT INTO {$this->cf_posts_table_name} (post_id, author_id) VALUES ";
                $count = 0;
                for($i = 0; $i < count($posts); $i++){

                    $post = $posts[$i];
                    if($count == 0){
                        $query .= "({$post->id}, {$post->user_id}) ";
                    } else {
                        $query .= ", ({$post->id}, {$post->user_id})";
                    }
                    $count++;
                }
                $this->wpdb->query($query);
            }
        }

        private function delete_post_from_manager($posts){
            if(count($posts) > 0) {
                $query = "DELETE FROM {$this->cf_posts_table_name} WHERE";
                $count = 0;
                for($i = 0; $i < count($posts); $i++){
                    $post = $posts[$i];
                    if($count == 0){
                        $query .= " post_id = {$post->id} ";
                    } else {
                        $query .= " OR post_id = {$post->id}";
                    }
                    $count++;
                }
                $this->wpdb->query($query);
            }
        }

        public function get_posts_in_manager_by_filter($order, $order_by_in_query, $limit, $offset, $search) {
            $order = esc_sql($order);
            $order_by_in_query = esc_sql($order_by_in_query);
            $limit = esc_sql($limit);
            $offset = esc_sql($offset);
            $search = esc_sql($search);
			
			$custom_posts_types = $this->get_custom_posts_types();
			$custom_posts_types_query = "";
			$custom_posts_types_values = array();
			if(sizeof($custom_posts_types) > 0) {
				foreach ($custom_posts_types as $item) {
					array_push($custom_posts_types_values, "post.post_type = \"{$item}\"");
				}

				if(sizeof($custom_posts_types_values) > 0) {
					$custom_posts_types_query = implode(" OR ", $custom_posts_types_values);
					$custom_posts_types_query = " OR ".$custom_posts_types_query;
				}
			}

            $query = "SELECT post.id as id , post.post_title as title, post.post_content as content,  
            users.display_name as display_name, users.ID as user_id, canvasflow_posts.published as published, 
            canvasflow_posts.ID as canvasflow_post_id, post.post_modified as post_modified_date, post.post_type as type
            FROM {$this->wp_posts_table_name} as post 
            LEFT JOIN {$this->wp_users_table_name} as users ON(post.post_author=users.ID) 
            LEFT JOIN {$this->cf_posts_table_name} as canvasflow_posts ON(post.ID=canvasflow_posts.post_id) 
            WHERE post.post_parent = 0 AND (post.post_type = \"post\" OR post.post_type = \"page\" {$custom_posts_types_query})
            AND post.post_status != \"auto-draft\" AND post.post_status != \"trash\"";

            if(!is_null($search) && strlen($search) > 0){
                $query .= " AND post.post_title LIKE '%{$search}%'";
            }

			$query .= "ORDER BY {$order_by_in_query} {$order} LIMIT {$limit} OFFSET {$offset};";

            $posts = array();

            foreach ( $this->wpdb->get_results($query) as $post ){
                array_push($posts, $post);
            }

            return $posts;
        }

        public function get_total_posts_in_manager_by_filter($search) {
			$search = esc_sql($search);
			
			$custom_posts_types = $this->get_custom_posts_types();
			$custom_posts_types_query = "";
			$custom_posts_types_values = array();
			if(sizeof($custom_posts_types) > 0) {
				foreach ($custom_posts_types as $item) {
					array_push($custom_posts_types_values, "post.post_type = \"{$item}\"");
				}

				if(sizeof($custom_posts_types_values) > 0) {
					$custom_posts_types_query = implode(" OR ", $custom_posts_types_values);
					$custom_posts_types_query = " OR ".$custom_posts_types_query;
				}
			}
			
            $total_of_post = 0;
            $query = "SELECT count(*) as count 
            FROM {$this->wp_posts_table_name} as post
            LEFT JOIN {$this->wp_users_table_name} as users ON(post.post_author=users.ID) 
            LEFT JOIN {$this->cf_posts_table_name} as canvasflow_posts ON(post.ID=canvasflow_posts.post_id) 
            WHERE post.post_parent = 0 AND (post.post_type = \"post\" OR post.post_type = \"page\" {$custom_posts_types_query})
            AND post.post_status != \"auto-draft\" AND post.post_status != \"trash\"";
            
            if(!is_null($search) && strlen($search) > 0){
                $query .= " AND post.post_title LIKE '%{$search}%'";
            }

            $result = $this->wpdb->get_results($query);

            $total_of_post = $result[0]->count;
            
            if(gettype($total_of_post) == 'string') {
                $total_of_post = intval($total_of_post);
            }

            return $total_of_post;
        }

        public function get_posts_in_main_by_filter($order, $order_by_in_query, $limit, $offset, $search) {
            $order = esc_sql($order);
            $order_by_in_query = esc_sql($order_by_in_query);
            $limit = esc_sql($limit);
            $offset = esc_sql($offset);
			$search = esc_sql($search);
			
			$custom_posts_types = $this->get_custom_posts_types();
			$custom_posts_types_query = "";
			$custom_posts_types_values = array();
			if(sizeof($custom_posts_types) > 0) {
				foreach ($custom_posts_types as $item) {
					array_push($custom_posts_types_values, "post.post_type = \"{$item}\"");
				}

				if(sizeof($custom_posts_types_values) > 0) {
					$custom_posts_types_query = implode(" OR ", $custom_posts_types_values);
					$custom_posts_types_query = " OR ".$custom_posts_types_query;
				}
			}

            $query = "SELECT post.id as id , post.post_title as title, post.post_content as content,
            users.display_name as display_name, users.ID as user_id, canvasflow_posts.published as published,
            canvasflow_posts.ID as canvasflow_post_id, post.post_modified as post_modified_date, 
            post.post_type as type, canvasflow_posts.style_id as style_id, canvasflow_posts.collection_id as collection_id,
            canvasflow_posts.issue_id as issue_id 
            FROM {$this->wp_posts_table_name} as post 
            LEFT JOIN {$this->wp_users_table_name} as users ON(post.post_author=users.ID) 
            LEFT JOIN {$this->cf_posts_table_name} as canvasflow_posts ON(post.ID=canvasflow_posts.post_id)
            WHERE post.post_parent = 0 AND (post.post_type = \"post\" OR post.post_type = \"page\" {$custom_posts_types_query}) 
            AND post.post_status != \"auto-draft\" AND post.post_status != \"trash\" 
			AND canvasflow_posts.ID IS NOT NULL";  

            if(!is_null($search) && strlen($search) > 0){
                $query .= " AND  post.post_title LIKE '%{$search}%'";
            }

            $query .= " ORDER BY {$order_by_in_query} {$order} LIMIT {$limit} OFFSET {$offset};";

            $posts = array();

            $result_posts = $this->wpdb->get_results($query);
            foreach ( $result_posts as $post ){
                array_push($posts, $post);
            }

            return $posts;
        }

        public function get_total_posts_in_main_by_filter($search) {
			$search = esc_sql($search);
			
			$custom_posts_types = $this->get_custom_posts_types();
			$custom_posts_types_query = "";
			$custom_posts_types_values = array();
			if(sizeof($custom_posts_types) > 0) {
				foreach ($custom_posts_types as $item) {
					array_push($custom_posts_types_values, "post.post_type = \"{$item}\"");
				}

				if(sizeof($custom_posts_types_values) > 0) {
					$custom_posts_types_query = implode(" OR ", $custom_posts_types_values);
					$custom_posts_types_query = " OR ".$custom_posts_types_query;
				}
			}

            $total_of_post = 0;
            $query = "SELECT count(*) as count 
            FROM {$this->wp_posts_table_name} as post 
            LEFT JOIN {$this->wp_users_table_name} as users ON(post.post_author=users.ID) 
            LEFT JOIN {$this->cf_posts_table_name} as canvasflow_posts ON(post.ID=canvasflow_posts.post_id)
            WHERE post.post_parent = 0 AND (post.post_type = \"post\" OR post.post_type = \"page\" {$custom_posts_types_query}) 
            AND post.post_status != \"auto-draft\" AND post.post_status != \"trash\" 
            AND canvasflow_posts.ID IS NOT NULL";
            
            if(!is_null($search) && strlen($search) > 0){
                $query .= " AND post.post_title LIKE '%{$search}%'";
            }            

            $result = $this->wpdb->get_results($query);

            $total_of_post = $result[0]->count;
            
            if(gettype($total_of_post) == 'string') {
                $total_of_post = intval($total_of_post);
            }

            return $total_of_post;
        }

        public function is_valid_wp_post_engine() {
            if($this->get_wp_posts_engine() == 'InnoDB') {
                return TRUE;
            }
            return FALSE;
        }

        public function is_valid_wp_users_engine() {
            if($this->get_wp_users_engine() == 'InnoDB') {
                return TRUE;
            }
            return FALSE;
        }

        public function exist_canvasflow_post_table() {
            $query = "SHOW TABLE STATUS WHERE Name = '{$this->cf_posts_table_name}';";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return TRUE;
            }

            return FALSE;
        }

        public function exist_version_table() {
            $version_table_name = $this->wpdb->prefix."canvasflow_version";
            $query = "SHOW TABLE STATUS WHERE Name = '{$version_table_name}';";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
		}
		
		// custom_posts_types VARCHAR(100) NOT NULL DEFAULT ''

        public function create_canvasflow_posts_table() {
            $query = "CREATE TABLE IF NOT EXISTS {$this->cf_posts_table_name} (ID BIGINT(20) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, 
            post_id BIGINT(20) UNSIGNED NOT NULL, style_id BIGINT(20) UNSIGNED, issue_id BIGINT(20) UNSIGNED, author_id BIGINT(20) UNSIGNED NOT NULL, 
            published DATETIME,  collection_id BIGINT(20) UNSIGNED, CONSTRAINT {$this->cf_posts_table_name}_{$this->wp_posts_table_name}_ID_fk 
            FOREIGN KEY (post_id) REFERENCES {$this->wp_posts_table_name} (ID) ON DELETE CASCADE ON UPDATE CASCADE);";
            if($this->wpdb->query($query) == FALSE){
                $error = 'Error creating the canvasflow post table';
                throw new Exception($error);
            }
        }

        public function exist_credentials_table() {
            $query = "SHOW TABLE STATUS WHERE Name = '{$this->cf_credentials_table_name}';";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return TRUE;
            }

            return FALSE;
        }

        public function create_canvasflow_credentials_table() {
            $query = "CREATE TABLE IF NOT EXISTS {$this->cf_credentials_table_name} (ID BIGINT(20) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, 
            user_id BIGINT(20) UNSIGNED NOT NULL, merge_adjacent_paragraphs BOOLEAN NOT NULL DEFAULT 1, secret_key TEXT, publication_id 
            VARCHAR(100) NOT NULL DEFAULT '', style_id BIGINT(20) UNSIGNED, issue_id BIGINT(20) UNSIGNED, publication_type VARCHAR(100) 
            NOT NULL DEFAULT 'article', channel_name VARCHAR(100) DEFAULT '', channel_id BIGINT(20) UNSIGNED, collection_id BIGINT(20) UNSIGNED, 
			custom_posts_types TEXT NOT NULL DEFAULT '', title_in_content BOOLEAN NOT NULL DEFAULT 0, auto_publish BOOLEAN NOT NULL DEFAULT 0,
            feature_image BOOLEAN NOT NULL DEFAULT 1, CONSTRAINT {$this->cf_credentials_table_name}_{$this->wp_users_table_name}_ID_fk FOREIGN KEY (user_id) 
            REFERENCES {$this->wp_users_table_name} (ID) ON DELETE CASCADE ON UPDATE CASCADE);";
            $this->wpdb->query($query);
        }

        public function create_tables_if_not_exist() {
            if(!$this->exist_canvasflow_post_table()) {
                $this->create_canvasflow_posts_table();
            }
    
            if(!$this->exist_credentials_table()) {
                $this->create_canvasflow_credentials_table();
            }
        }

        public function update_tables_schema() {
            $tables = array();
            array_push($tables, $this->cf_posts_table_name);

            foreach($tables as $table_name) {
                $this->update_table_data($table_name);
            }
        }

        // This function is for debug what are the querys that are being send
        public function migrate_table_data_statements($tables_names, $version) {
            $statements = array();

            array_push($statements, "BEGIN;");
            array_push($statements, "START TRANSACTION;");

            foreach($tables_names as $table_name) {
                $old_column_names = $this->get_column_names($table_name);
                $column_names_in_query = implode(", ", $old_column_names);

                $data = $this->get_table_data($old_column_names, $table_name);

                array_push($statements, "DROP TABLE {$table_name};");
                if($table_name === $this->cf_posts_table_name) {
                    array_push($statements, "CREATE TABLE IF NOT EXISTS {$this->cf_posts_table_name} (ID BIGINT(20) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, 
                    post_id BIGINT(20) UNSIGNED NOT NULL, style_id BIGINT(20) UNSIGNED, issue_id BIGINT(20) UNSIGNED, author_id BIGINT(20) UNSIGNED NOT NULL, 
                    published DATETIME,  collection_id BIGINT(20) UNSIGNED, CONSTRAINT {$this->cf_posts_table_name}_{$this->wp_posts_table_name}_ID_fk 
                    FOREIGN KEY (post_id) REFERENCES {$this->wp_posts_table_name} (ID) ON DELETE CASCADE ON UPDATE CASCADE);");
                } else if($table_name === $this->cf_credentials_table_name) {
                    array_push($statements, "CREATE TABLE IF NOT EXISTS {$this->cf_credentials_table_name} (ID BIGINT(20) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, 
                    user_id BIGINT(20) UNSIGNED NOT NULL, merge_adjacent_paragraphs BOOLEAN NOT NULL DEFAULT 1, secret_key TEXT, publication_id 
                    VARCHAR(100) NOT NULL DEFAULT '', style_id BIGINT(20) UNSIGNED, issue_id BIGINT(20) UNSIGNED, publication_type VARCHAR(100) 
                    NOT NULL DEFAULT 'article', channel_name VARCHAR(100) DEFAULT '', channel_id BIGINT(20) UNSIGNED, collection_id BIGINT(20) UNSIGNED, 
                    custom_posts_types TEXT NOT NULL DEFAULT '', title_in_content BOOLEAN NOT NULL DEFAULT 0, auto_publish BOOLEAN NOT NULL DEFAULT 0,
                    feature_image BOOLEAN NOT NULL DEFAULT 1, CONSTRAINT {$this->cf_credentials_table_name}_{$this->wp_users_table_name}_ID_fk FOREIGN KEY (user_id) 
                    REFERENCES {$this->wp_users_table_name} (ID) ON DELETE CASCADE ON UPDATE CASCADE);");
                }
                
                $new_column_names = $this->get_column_names($table_name);
                $column_names = $this->intersect_column_names($old_column_names, $new_column_names);

                $column_names_types = $this->get_table_column_names_types($table_name);
                $insert_statements = $this->create_insert_statements_for_migration($table_name, $column_names, $column_names_types, $data);
                
                foreach($insert_statements as $statement) {
                    array_push($statements, $statement);
                }
            }
            
            $version_table_name = $this->wpdb->prefix."canvasflow_version";
            array_push($statements, "UPDATE {$version_table_name} SET version = '{$version}';");
            array_push($statements, "COMMIT");
            return $statements;
        }

        public function migrate_table_data($tables_names, $version) {
            $this->wpdb->query("BEGIN");
            $this->wpdb->query("START TRANSACTION");
            
            try {
                foreach($tables_names as $table_name) {
                    $old_column_names = $this->get_column_names($table_name);

                    $data = $this->get_table_data($old_column_names, $table_name);

                    $this->delete_table($table_name);
                    $this->create_table($table_name);
                    
                    $new_column_names = $this->get_column_names($table_name);
                    $column_names = $this->intersect_column_names($old_column_names, $new_column_names);

                    $column_names_types = $this->get_table_column_names_types($table_name);
                    $statements = $this->create_insert_statements_for_migration($table_name, $column_names, $column_names_types, $data);

                    $this->run_statements($statements);
                }

                $this->set_canvasflow_version($version);
                
                $this->wpdb->query("COMMIT");
            } catch(Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                $this->$wpdb->query("ROLLBACK");
            }
            
            return $data;
        }

        private function get_table_data($column_names, $table_name) {
            $column_names_in_query = implode(", ", $column_names);
            $query = "SELECT {$column_names_in_query} FROM {$table_name}";
            $result = $this->wpdb->get_results($query, ARRAY_A);
            $data = array();
            if(sizeof($result) > 0) {
                for($i=0; $i<sizeof($result); $i++) {
                    $record = array();
                    for($j=0;$j<sizeof($column_names);$j++) {
                        // echo "<b>{$column_names[$j]}</b>: '{$result[$i][$column_names[$j]]}'<br>";
                        $record[$column_names[$j]] = $result[$i][$column_names[$j]];
                    }
                    array_push($data, $record);
                }
            }

            return $data;
        }

        private function intersect_column_names($old_column_names, $new_column_names) {
            $column_names = array();
            foreach($new_column_names as $column_name) {
                $exist = in_array($column_name, $old_column_names);
                if($exist) {
                    array_push($column_names, $column_name);
                }
            }
            return $column_names;
        }

        private function create_insert_statements_for_migration($table_name, $column_names, $column_name_types, $data) {
            $statements = array();
            $column_names_in_query = implode(",", $column_names);

            if(sizeof($data) > 0) {
                foreach($data as $record) {
                    $query = "INSERT INTO {$table_name} ({$column_names_in_query}) VALUES ";
                    $values = array();
                    foreach($column_names as $column_name) {
                        if($record[$column_name] == null) {
							if($column_name == "custom_posts_types") {
								array_push($values, "''");
							} else {
								array_push($values, "NULL");
							}
                        } else {
                            if($column_name_types[$column_name] == "number") {
                                array_push($values, "{$record[$column_name]}");
                            } else {
                                array_push($values, "'{$record[$column_name]}'");
                            }
                        }
                        
                    }

                    $values_in_query = implode(", ", $values);
                    $query .= "({$values_in_query});";
                    array_push($statements, $query);
                }
            }

            return $statements;
        }

        private function run_statements($statements) {
            if(sizeof($statements) > 0) {
                foreach($statements as $statement) {
                    if($this->wpdb->query($statement) === FALSE) {
                        $error = "Error running the statement: '{$statement}'";
                        throw new Exception($error);
                    }
                }
            }
        }

        public function create_table($table_name) {
            if($table_name === $this->cf_posts_table_name) {
                $this->create_canvasflow_posts_table();
            } else if($table_name === $this->cf_credentials_table_name) {
                $this->create_canvasflow_credentials_table();
            }
        }

        public function delete_table($table_name) {
            $query = "SHOW TABLES LIKE '{$table_name}';";
    
            $result = $this->wpdb->get_results($query); 
            if(sizeof($result) > 0) {
                $query = "DROP TABLE {$table_name};";
                $this->wpdb->query($query);
            }   
        }

        public function get_column_names($table_name) {
            $query = "SHOW COLUMNS FROM {$table_name};";
    
            $column_names = array();
            $result = $this->wpdb->get_results($query);
            for($i=0; $i<sizeof($result); $i++) {
                $column_name = $result[$i]->Field;
                array_push($column_names, $column_name);
            }
            return $column_names;
        }

        public function get_table_column_names_types($table_name) {
            $query = "SHOW COLUMNS FROM {$table_name};";
    
            $column_names_types = array();
            $result = $this->wpdb->get_results($query);
            for($i=0; $i<sizeof($result); $i++) {
                $field = $result[$i]->Field;
                $type = $result[$i]->Type;
                if(preg_match('/(int|float|numeric|real)+/',$type)) {
                    $column_names_types[$field] = 'number';
                } else {
                    $column_names_types[$field] = 'text';
                }
            }
            return $column_names_types;
        }

        public function create_canvasflow_version_table() {
            $cf_table_name = $this->wpdb->prefix."canvasflow_credentials";
            $version_table_name = $this->wpdb->prefix."canvasflow_version";
            $query = "CREATE TABLE IF NOT EXISTS {$version_table_name} (ID BIGINT(20) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, version varchar(100) NOT NULL DEFAULT '')";
            $this->wpdb->query($query);
        }

        public function set_canvasflow_version($version) {
            $version_table_name = $this->wpdb->prefix."canvasflow_version";
            $query = "INSERT INTO {$version_table_name} (version) VALUES ('{$version}');";
            if($this->is_canvasflow_version_set()) {
                $query = "UPDATE {$version_table_name} SET version = '{$version}';";
            } 
            $this->wpdb->query($query);
        }

        public function delete_canvasflow_posts_table() {
            $cf_table_name = $this->wpdb->prefix."canvasflow_posts";
            $query = "SHOW TABLES LIKE '{$cf_table_name}';";
    
            $result = $this->wpdb->get_results($query); 
            if(sizeof($result) > 0) {
                $query = "DROP TABLE {$cf_table_name};";
                $this->wpdb->query($query);
            }
        }

        public function delete_canvasflow_credentials_table() {
            $cf_table_name = $this->wpdb->prefix."canvasflow_credentials";
            $query = "SHOW TABLES LIKE '{$cf_table_name}';";
    
            $result = $this->wpdb->get_results($query); 
            if(sizeof($result) > 0) {
                $query = "DROP TABLE {$cf_table_name};";
                $this->wpdb->query($query);
            }   
        }
    
        public function delete_canvasflow_version_table() {
            $cf_table_name = $this->wpdb->prefix."canvasflow_version";
            $query = "SHOW TABLES LIKE '{$cf_table_name}';";
    
            $result = $this->wpdb->get_results($query); 
            if(sizeof($result) > 0) {
                $query = "DROP TABLE {$cf_table_name};";
                $this->wpdb->query($query);
            }   
        }
    
        public function delete_canvasflow_category(){
            $terms_table_name = $this->wpdb->prefix."terms";
            $query = "DELETE FROM {$terms_table_name} WHERE slug=\"canvasflow\";";
            $this->wpdb->query($query);
        }

        public function is_canvasflow_version_set() {
            $version_table_name = $this->wpdb->prefix."canvasflow_version";
            $query = "SELECT * FROM {$version_table_name};";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function get_canvasflow_version() {
            $version_table_name = $this->wpdb->prefix."canvasflow_version";
            $query = "SELECT * FROM {$version_table_name};";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return $result[0]->version;;
            } else {
                return null;
            }
        }

        private function get_wp_posts_engine() {
            $query = "SHOW TABLE STATUS WHERE Name = '{$this->wp_posts_table_name}';";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return $result[0]->Engine;
            } else {
                return '';
            }
        }

        private function get_wp_users_engine() {
            $query = "SHOW TABLE STATUS WHERE Name = '{$this->wp_users_table_name}';";
            $result = $this->wpdb->get_results($query);
            if(sizeof($result) > 0) {
                return $result[0]->Engine;
            } else {
                return '';
            }
        }
    }
?>