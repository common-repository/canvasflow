<?php
    include( plugin_dir_path( __FILE__ ) . 'canvasflow-shortcodes.php');
    require_once(realpath(dirname(__FILE__) . '/..').'/canvasflow-plugin.php');
    class Canvasflow_Api {
        public $domain = 'canvasflow.io';
        private $base_url = "https://api.canvasflow.io/v1";
        
        private $version;
        private $max_timeout = 30;
        private $canvasflow_shortcodes;

        function __construct($version, $canvasflow_shortcodes) {
            $this->version = $version;
            $this->increase_timeout();
            $this->canvasflow_shortcodes = $canvasflow_shortcodes;
        }

        private function increase_timeout() {
            add_action('http_api_curl', 'sar_custom_curl_timeout', 9999, 1);
            function sar_custom_curl_timeout( $handle ){
                curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
                curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
            }
            // Setting custom timeout for the HTTP request
            add_filter( 'http_request_timeout', 'sar_custom_http_request_timeout', 9999 );
            function sar_custom_http_request_timeout( $timeout_value ) {
                return 30; // 30 seconds. Too much for production, only for testing.
            }
            // Setting custom timeout in HTTP request args
            add_filter('http_request_args', 'sar_custom_http_request_args', 9999, 1);
            function sar_custom_http_request_args( $r ){
                $r['timeout'] = 30; // 30 seconds. Too much for production, only for testing.
                return $r;
            }
        }

        public function publish_post($content, $post_title, $post_id, $style_id, $issue_id, $secret_key, $merge_adjacent_paragraphs, $publication_id, $channel_id, $collection_id, $title_in_content = FALSE, $author_name, $post_date, $auto_publish = FALSE, $enable_feature_image = TRUE) {
            // $url = $this->base_url."/article";
            // $url = "http://api.{$this->domain}/v1/index.cfm?endpoint=/article";
            $url = $this->base_url."/article";
            
            $result = array(
                'code' => 0,
                'status' => 'error',
                'message' => ''
            );

            if($enable_feature_image == TRUE) {
                $featured_image = wp_get_attachment_url( get_post_thumbnail_id($post_id), 'thumbnail' );
            
                if($featured_image !== FALSE){
                    $content = '<img src="'.$featured_image.'">'.$content;
                }
            }
    
            if($merge_adjacent_paragraphs == 1) {
                $merge_adjacent_paragraphs = 'true';
            } else {
                $merge_adjacent_paragraphs = 'false';
            }

            if($auto_publish == 1) {
                $auto_publish = 'true';
            } else {
                $auto_publish = 'false';
            }
    
            $style_id = (int) $style_id;
            $issue_id = (int) $issue_id;
            $channel_id = (int) $channel_id;

            if($this->canvasflow_shortcodes != null) {
                $this->canvasflow_shortcodes->register();
            }

            // Remove nested short_code
            $content = preg_replace('/(\S+)="\[[a-zA-Z0-9:;\.\s\(\)\-\,]*\]"/', "", $content);
            $content = preg_replace("/(\S+)=&#8221;\[[a-zA-Z0-9:;\.\s\(\)\-\,]*\]&#8221;/", "", $content);
            
            $content = do_shortcode($content);
            
            if($this->canvasflow_shortcodes != null) {
                $this->canvasflow_shortcodes->unregister();
            }

            echo '<script>console.log("Content")</script>';
            echo "<script>console.log(`{$content}`)</script>";
            
            // Append title in post content
            if($title_in_content == 1) {
                $content = "<h1>{$post_title}</h1>".$content;
            }
            
            // Uncomment this to ignore shortcodes
			// $content = preg_replace("/\[.*?\]/mi", "", $content);
			
            //echo "<b>Issue id</b> : {$issue_id}</br>";

            $categories = array();
            foreach(get_the_category($post_id) as $category) {
                array_push($categories, $category->slug);
            }

            $data = array(
                'secretKey' => $secret_key, 
                'content' => $content,
                'contentType' => "html",
                'targetId' => $collection_id,
                'publicationId' => $publication_id,
                'channelId' => $channel_id,
                'issueId' => $issue_id,
				'mergeAdjParagraphs' => $merge_adjacent_paragraphs,
                'author' => $author_name,
                'metadata' => json_encode($this->get_metadata($post_id)),
                'categories' => implode(", ", $categories),
				'createdDate ' => $post_date,
				'autoPublish' => $auto_publish,
                'styleId' => $style_id,
                'articleId' => $post_id,
                'articleName' => $post_title,
                'version' => $this->version
            );

            if($collection_id === '') {
                unset($data['targetId']);
            }
            
            $response = wp_remote_post( $url, array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                ),
                'body'        => $data,
                'cookies'     => array()
            ));
    
            if ( is_wp_error( $response ) ) {
                $message = $response->get_error_message();
                $result['status'] = 'error';
                $result['message'] = $message;
                $result['code'] = 500;
            } else {
                $code = $response['response']['code'];
                $result['code'] = $code;
                $body = $response['body'];
                if($code == 200){
                    $message = 'Article uploaded successfully';
                    $result['status'] = 'success';
                    $result['message'] = $message;
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    
                    $result['status'] = 'error';
                    $result['message'] = $message;
                }
            }
            return $result;
        }

        public function the_meta($post_id) {
            $response = '';
            if ( $keys = get_post_custom_keys($post_id) ) {
                $response .= "<ul class='post-meta'>\n";
                foreach ( (array) $keys as $key ) {
                    $keyt = trim( $key );
                    if ( is_protected_meta( $keyt, 'post' ) ) {
                        continue;
                    }
         
                    $values = array_map( 'trim', get_post_custom_values( $key, $post_id ));
                    $value = implode( $values, ', ' );
         
                    $html = sprintf( "<li><span>%s</span> %s</li>\n",
                        /* translators: %s: Post custom field name */
                        sprintf( _x( '%s:', 'Post custom field name' ), $key ),
                        $value
                    );
                    $response .= $html;
                }
                $response .= "</ul>\n";
            }

            return $response;
        }

        public function get_metadata($post_id){
            $response = array();
            if ( $keys = get_post_custom_keys($post_id) ) {
                foreach ( (array) $keys as $key ) {
                    $keyt = trim( $key );
                    if ( is_protected_meta( $keyt, 'post' ) ) {
                        continue;
                    }
         
                    $values = array_map( 'trim', get_post_custom_values( $key, $post_id ));
                    $value = implode( $values, ', ' );

                    $response[$key] = $value;
                }
            }

            return $response;
        }
    
        public function get_styles_from_remote($publication_id, $secret_key){
            $url = $this->base_url."/styles?secretkey={$secret_key}&publicationId={$publication_id}";
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));
    
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>Error: {$error_message}</div></div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    return json_decode($body, true);
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b>Error: {$message}</b></div></div>";
                }
            }
        }

        public function get_channels_from_remote($publication_id, $secret_key) {
            $url = $this->base_url."/publications?secretkey={$secret_key}";  
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>Error:  {$error_message}</div></div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    $publications = json_decode($body, true);
                    foreach($publications as $publication) {
                        if((string) $publication['id'] == (string) $publication_id) {
                            return $publication['channels'];
                        }
                    }
                    return array();
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b> Error: {$message}</b></div></div>";
                }
            }
        }

        public function get_issues_from_remote($publication_id, $secret_key) {
            $url = $this->base_url."/issues?secretkey={$secret_key}&publicationId={$publication_id}";
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>Error: {$error_message}</div></div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    return json_decode($body, true);
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b> Error: {$message}</b></div></div>";
                }
            }
        }

        public function validate_secret_key($secret_key) {
            $url = $this->base_url."/info?secretkey={$secret_key}";
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>Error: {$error_message}</b></div></div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    return TRUE;
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b>Error: {$message}</b></div></div>";
                }
            }
            return FALSE;
        }

        public function get_publication_type($publication_id, $secret_key) {
            $url = $this->base_url."/publications?secretkey={$secret_key}";  
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>Error: {$error_message}</div><br>Error fetching publication type</div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    $body = $response['body'];
                    $publications = json_decode($body, true);
                    foreach($publications as $publication) {
                        if((string) $publication['id'] == (string) $publication_id) {
                            return $publication['type'];
                        }
                    }
                    return '';
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b>Error: {$message}</b></div></div>";
                }
            }            
        }

        public function get_collections_by_publication($publication_id, $secret_key, $channel_name, $channel_id) {
            $url = $this->base_url."/{$channel_name}/collections?secretkey={$secret_key}&publicationId={$publication_id}&channelId={$channel_id}";
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>{$error_message}</div></div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    return json_decode($body, true);
                } else if($http_response_code == 404){
                    return json_decode('[]', true);
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b>Error: {$message}</b></div></div>";
                }
            }
            return array();
        }

        public function get_channel_name_by_publication($publication_id, $secret_key) {
            $url = $this->base_url."/publications?secretkey={$secret_key}";  
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));

            $body = $response['body'];

            $publications = json_decode($body, true);
            foreach($publications as $publication) {
                if((string) $publication['id'] == (string) $publication_id) {
                    return $publication['channel'];
                }
            }
            return '';
        }

        public function get_remote_publications($secret_key) {
            $url = $this->base_url."/publications?secretkey={$secret_key}";  
            $response = wp_remote_get($url, array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => array(
                    'Accept-Encoding' => 'gzip, deflate'
                )
            ));

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "<div class=\"error-message\"><div><b>Error: {$error_message}</div><br>Error fetching publications</div>";
            } else {
                $http_response_code = $response['response']['code'];
                $body = $response['body'];
                if($http_response_code == 200){
                    return json_decode($body, true);
                } else {
                    $message = $body;
                    $message = str_replace('"', '', $message);
                    echo "<div class=\"error-message\"><div><b>Error: {$message}</b></div></div>";
                }
            }
            return array();
        }
    }

    $canvasflow_api = new Canvasflow_Api(WP_Canvasflow::$version, $canvasflow_shortcodes);
?>