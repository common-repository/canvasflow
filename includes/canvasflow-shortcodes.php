<?php
    class Canvasflow_Shortcodes {
        private $wpdb;
        private $ngg_gallery_table;
        private $ngg_pictures_table;

        function __construct() {
            $this->wpdb = $GLOBALS['wpdb'];
            $this->ngg_gallery_table = $this->wpdb->prefix.'ngg_gallery';
            $this->ngg_pictures_table = $this->wpdb->prefix.'ngg_pictures';
        }

        public function register() {
            add_shortcode('ngg', array($this, 'ngg_shortcode'));
            add_shortcode('ngg_images', array($this, 'ngg_images_shortcode'));
        }

        public function unregister() {
            remove_shortcode('ngg');
            remove_shortcode('ngg_images');
        }

        public function unregister_tag($tag) {
            remove_shortcode($tag);
        }

        public function ngg_shortcode($atts) {
            if(!$this->exist_ngg_tables()) {
                return '';
            }

            $gallery = shortcode_atts(array(
                'ids' => 0
            ), $atts);
 
            $gallery_id = $this->trim_quotes($gallery['ids']);

            if(gettype($gallery_id) !== 'integer') {
                try {
                    $gallery_id = (int) $gallery_id;
                } catch(Exception $e) {
                    return '';
                }
            }

            if($gallery_id === 0) {
                return '';
            }

            $response = '<div class="gallery">';

            $gallery = $this->get_gallery($gallery_id);
            for ($i=0; $i<sizeof($gallery->pictures); $i++) {
                $picture = $gallery->pictures[$i];
                $src = get_home_url().'/'.$gallery->path.$picture;
                $response .= "<img src=\"{$src}\">";
            } 

            $response .= '</div>';


            return $response;
        } 

        public function ngg_images_shortcode($atts) {
            if(!$this->exist_ngg_tables()) {
                return '';
            }

            $gallery = shortcode_atts(array(
                'container_ids' => 0,
                'template' => ''
            ), $atts);
 
            $gallery_id = $this->trim_quotes($gallery['container_ids']);

            if(gettype($gallery_id) !== 'integer') {
                try {
                    $gallery_id = (int) $gallery_id;
                } catch(Exception $e) {
                    return '';
                }
            }

            if($gallery_id === 0) {
                return '';
            }

            $gallery = $this->get_gallery($gallery_id);

            if(sizeof($gallery->pictures) == 0) {
                return '';
            }

            $response = '<div class="gallery">';

            
            for ($i=0; $i<sizeof($gallery->pictures); $i++) {
                $picture = $gallery->pictures[$i];
                $src = get_home_url().'/'.$gallery->path.$picture;
                $response .= "<img src=\"{$src}\">";
            } 

            $response .= '</div>';

            return $response;
        } 

        public function trim_quotes($str) {
            $str = str_replace(array("&#8243;","&amp;"), "", $str); 
            $str = str_replace(array("&#8221;","&amp;"), "", $str); 
            return $str;
        }

        private function get_gallery($gallery_id) {
            $gallery_id = esc_sql($gallery_id);
            $query = "SELECT path, title, name FROM {$this->ngg_gallery_table} WHERE gid = {$gallery_id} LIMIT 1;";

            $galleries = $this->wpdb->get_results($query); 

            $gallery = new stdClass();
            $gallery->path = '';
            $gallery->title = '';
            $gallery->name = '';
            $gallery->pictures = array();

            if(sizeof($galleries) > 0) {
                $gallery->path = $galleries[0]->path;
                $gallery->title = $galleries[0]->title;
                $gallery->name = $galleries[0]->name;
                $gallery->pictures = $this->get_pictures_from_gallery($gallery_id);
            }

            return $gallery;
        }

        private function get_pictures_from_gallery($gallery_id) {
            $gallery_id = esc_sql($gallery_id);
            $pictures = array();

            $query = "SELECT filename FROM {$this->ngg_pictures_table} WHERE galleryid = {$gallery_id};";
            $result = $this->wpdb->get_results($query);

            if(sizeof($result) > 0) {
                for ($i=0; $i<sizeof($result); $i++) {
                    array_push($pictures,$result[$i]->filename);
                } 
            }

            return $pictures;
        }

        private function exist_ngg_tables() {
            return $this->exist_ngg_gallery_table() && $this->exist_ngg_pictures_table();
        }

        private function exist_ngg_gallery_table() {
            $query = "SHOW TABLES LIKE '{$this->ngg_gallery_table}';";
            $result = $this->wpdb->get_results($query);

            if(sizeof($result) > 0) {
                return true;
            }

            return false;
        }

        private function exist_ngg_pictures_table() {
            $query = "SHOW TABLES LIKE '{$this->ngg_pictures_table}';";
            $result = $this->wpdb->get_results($query);

            if(sizeof($result) > 0) {
                return true;
            }

            return false;
        }
    }

    $canvasflow_shortcodes = new Canvasflow_Shortcodes();
?>