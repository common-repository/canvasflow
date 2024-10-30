<?php
    $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vestibulum sed quam commodo aliquam. Suspendisse ligula purus, tempus vel facilisis quis, gravida a lectus. Donec pretium neque quis augue fringilla feugiat. Integer molestie ac dui quis finibus. Praesent eleifend lectus at erat vestibulum, a vulputate mauris lacinia. Integer molestie aliquam mauris, sit amet dictum ligula dapibus et. Nulla lorem ligula, euismod eget diam vitae, consectetur sagittis ipsum. Nunc tortor turpis, mollis sed justo id, imperdiet eleifend ex. Sed lacus dolor, fringilla vel libero et, fermentum consequat lectus. Maecenas a feugiat felis. Proin sed laoreet magna.<br>Curabitur laoreet tellus a neque luctus, et pretium mi iaculis. Donec eget scelerisque mauris, ornare pharetra dui. Praesent consequat dignissim magna. Praesent id porttitor lectus. Aliquam erat volutpat. Phasellus mollis risus non orci congue laoreet. Fusce bibendum nisi volutpat leo lobortis, sit amet vehicula felis mollis. Aliquam maximus suscipit augue at semper. In finibus eros ipsum, non gravida turpis ornare eu. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.<br>Pellentesque blandit urna leo, quis tincidunt magna volutpat convallis. Proin finibus laoreet augue, ac tristique enim fermentum ut. Vivamus purus nulla, eleifend in porttitor eget, sagittis eu augue. Phasellus tincidunt turpis semper ante rhoncus mattis. Ut faucibus nisl at sapien finibus interdum euismod eu sapien. Cras vel vulputate nibh. Maecenas eu libero sed sapien ullamcorper blandit eu id nibh. Nullam cursus, enim consectetur porttitor luctus, massa dolor fringilla odio, in lobortis lorem nisi nec velit. Praesent gravida ornare turpis vitae venenatis. Pellentesque tortor ante, tempus ut est eu, aliquet ornare mi. In vitae sagittis metus. Phasellus massa lectus, vehicula at orci a, tincidunt tincidunt nulla. Phasellus consequat imperdiet urna nec vehicula. Maecenas iaculis, massa in tincidunt facilisis, urna nulla varius felis, et placerat erat risus a est.<br>Sed interdum sapien eget dolor maximus porta. Nulla erat mi, vulputate ut quam eu, vestibulum lacinia elit. Cras nec enim sit amet nulla interdum vulputate consectetur non orci. Pellentesque gravida tincidunt magna ac bibendum. Suspendisse ultricies tristique pretium. Donec lobortis, ex et pharetra fringilla, arcu tellus consectetur dolor, a tristique nulla felis in libero. Cras sagittis hendrerit risus, cursus scelerisque libero mollis in. Nullam ut tortor enim. Sed faucibus venenatis dignissim. Morbi pretium laoreet massa, non tempus metus blandit non.<br>Maecenas convallis ligula ut neque euismod molestie. In in scelerisque purus, ultrices laoreet nisi. Suspendisse vestibulum pharetra tortor nec vehicula. Donec condimentum porta est ut dictum. Donec volutpat finibus ornare. Ut tincidunt, ipsum non mollis porttitor, risus massa lobortis neque, vitae placerat lorem turpis a dui. Suspendisse et nulla quam. Quisque in felis ut enim auctor consectetur vitae quis purus.";
    // echo "<div class=\"error-message\"><div><b>{$lorem}</b></div></div>";
?>
<div class="wrap">
    <h1>Canvasflow Settings</h1>
    <hr />
    <br />
    <form method="post" action="admin.php?page=canvasflow-settings">
        <input name="cf_nonce_update_setting" type="hidden" value="<?php echo wp_create_nonce('cf-update-setting'); ?>" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="secret_key">API Key: </label>
                    </th>
                    <td>
                        <input name="secret_key" type="text" value="<?php echo $secret_key;?>" class="regular-text" required>
                        <?php
                            if(count($publications) > 0){
                        ?>
                            <br/>
                            <small>
                                <em>
                                    <span  class="connect-success">&#10004; Connected: <b><?php echo $canvasflow_domain;?></b>
                                </em>
                            </small>
                            <?php } ?>
                    </td>
                </tr>
                <?php
                    if(count($publications) > 0){
                ?>
                <tr>
                    <th scope="row">
                        <label for="publication_id">Publication: </label>
                    </th>
                    <td>
                        <select class="selectpicker" name="publication_id" required>
                        <?php 
                            $is_an_option_selected = FALSE;
                            $select_options = '';
                            foreach ($publications as $publication) {
                                $publication_id = (string) $publication['id'];
                                $publication_name = $publication['name'];
                                if($publication_id == $selected_publication) {
                                    $select_options.= '<option value="'.$publication_id.'" selected>'.$publication_name.'</option>';
                                    $is_an_option_selected = TRUE;
                                } else {
                                    $select_options.= '<option value="'.$publication_id.'">'.$publication_name.'</option>';
                                }
                            }

                            if($is_an_option_selected == TRUE) {
                                $select_options = '<option value="">Select a publication:</option>'.$select_options;
                            } else {
                                $select_options = '<option value="" selected>Select a publication:</option>'.$select_options;
                            }

                            echo $select_options;
                        ?>
                        </select>
                        <br/>
                        <small>
                            <em>
                                Sets the Canvasflow publication articles are published to.
                            </em>
                        </small>
                    </td>
                </tr>
                <?php
                    if(count($channels) > 0) {
                ?>
                    <tr>
                        <th scope="row">
                            <label for="channel_id">Channel: </label>
                        </th>
                        <td>
                            <select class="selectpicker" name="channel_id" required>
                            <?php 
                                $select_option_channel = '';
                                foreach ($channels as $channel) {
                                    $channel_id = $channel['id'];
                                    $channel_name = $channel['name'];
                                    if($channel_id == $selected_channel_id) {
                                        $select_option_channel.= '<option value="'.$channel_id.'" selected>'.$channel_name.'</option>';
                                    } else {
                                        $select_option_channel.= '<option value="'.$channel_id.'">'.$channel_name.'</option>';
                                    }
                                }
    
                                echo $select_option_channel;
                            ?>
                            </select>
                            <br/>
                            <small>
                                <em>
                                Sets the publish channel for the publication
                                </em>
                            </small>
                        </td>
                    </tr>
                <?php 
                    }
                ?>

                <?php
                    if(count($collections) > 0) {
                ?>
                    <tr>
                        <th scope="row">
                            <label for="collection_id">Default Collection: </label>
                        </th>
                        <td>
                            <select class="selectpicker" name="collection_id" required>
                            <?php 
                                $select_option_collection = '';
                                foreach ($collections as $collection) {
                                    $collection_id = $collection['id'];
                                    $collection_name = $collection['name'];
                                    if($collection_id == $selected_collection_id) {
                                        $select_option_collection.= '<option value="'.$collection_id.'" selected>'.$collection_name.'</option>';
                                    } else {
                                        $select_option_collection.= '<option value="'.$collection_id.'">'.$collection_name.'</option>';
                                    }
                                }
    
                                echo $select_option_collection;
                            ?>
                            </select>
                            <br/>
                            <small>
                                <em>
                                    Sets the default collection for article upload
                                </em>
                            </small>
                        </td>
                    </tr>
                <?php 
                    }
                ?>
				<?php 
					/*echo "</br><b>Selected Publication Type</b>: {$selected_publication_type}</br>";
					var_dump($issues);*/
                    if($selected_publication_type != "issue" && count($issues) > 0) {
                ?>
                    <input type="hidden" name="issue_id" value="<?php echo $issues[0]['id'];?>">

                <?php 
                    } else {
                    if(count($issues) > 0) {
                ?>
                <tr>
                    <th scope="row">
                        <label for="style_id">Default Issue: </label>
                    </th>
                    <td>
                        <select class="selectpicker" name="issue_id" required>
                        <?php 
                            $select_option_issue = '';
                            foreach ($issues as $issue) {
                                $issue_id = $issue['id'];
                                $issue_name = $issue['name'];
                                if($issue_id == $selected_issue) {
                                    $select_option_issue.= '<option value="'.$issue_id.'" selected>'.$issue_name.'</option>';
                                } else {
                                    $select_option_issue.= '<option value="'.$issue_id.'">'.$issue_name.'</option>';
                                }
                            }

                            echo $select_option_issue;
                        ?>
                        </select>
                        <br/>
                        <small>
                            <em>
                            Sets the default target issue for article upload
                            </em>
                        </small>
                    </td>
                </tr>
                <?php
                        } 
                    }
                ?>
                <?php
                    if(count($styles) > 0){
                ?>
                <tr>
                    <th scope="row">
                        <label for="style_id">Default Style: </label>
                    </th>
                    <td>
                        <select class="selectpicker" name="style_id" required>
                        <?php 
                            $select_option_style = '';
                            foreach ($styles as $style) {
                                $style_id = $style['id'];
                                $style_name = $style['name'];
                                if($style_id == $selected_style) {
                                    $select_option_style.= '<option value="'.$style_id.'" selected>'.$style_name.'</option>';
                                } else {
                                    $select_option_style.= '<option value="'.$style_id.'">'.$style_name.'</option>';
                                }
                            }

                            echo $select_option_style;
                        ?>
                        </select>
                        <br/>
                        <small>
                            <em>
                                Determines the default style applied to an uploaded article. <br/>This can be overridden per article upload.
                            </em>
                        </small>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="merge_adjacent_paragraphs">Merge adjacent paragraphs:</label>
                    </th>
                    <td>
                        <?php
                            echo "<input type=\"hidden\" value=\"0\" name=\"merge_adjacent_paragraphs\">";
                            if($merge_adjacent_paragraphs == TRUE){                                
                                echo "<input type=\"checkbox\" name=\"merge_adjacent_paragraphs\" value=\"1\" checked>";
                            } else {
                                echo "<input type=\"checkbox\" name=\"merge_adjacent_paragraphs\" value=\"1\">";
                            }
                        ?>
                        <br/>
                        <small>
                            <em>
                                By default, adjacent paragraphs are merged into a single Canvasflow paragraph component. <br />  Disabling this option forces paragraphs to be treated as individual components within Canvasflow.<br/>  This makes manipulating the order of paragraph content more flexible.
                            </em>
                        </small>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="title_in_content">Include Title in content:</label>
                    </th>
                    <td>
                        <?php
                            echo "<input type=\"hidden\" value=\"0\" name=\"title_in_content\">";
                            if($title_in_content == TRUE){                                
                                echo "<input type=\"checkbox\" name=\"title_in_content\" value=\"1\" checked>";
                            } else {
                                echo "<input type=\"checkbox\" name=\"title_in_content\" value=\"1\">";
                            }
                        ?>
                        <br/>
                        <small>
                            <em>
                            If the article Title <strong>does not</strong> appear in the Body copy of the post, enabling this option will create a ‘Title component’ at the top of the published Body copy. 
                            </em>
                        </small>
                    </td>
                </tr>
				<tr>
                    <th scope="row">
                        <label for="title_in_content">Auto Publish:</label>
                    </th>
                    <td>
                        <?php
                            echo "<input type=\"hidden\" value=\"0\" name=\"auto_publish\">";
                            if($auto_publish == TRUE){                                
                                echo "<input type=\"checkbox\" name=\"auto_publish\" value=\"1\" checked>";
                            } else {
                                echo "<input type=\"checkbox\" name=\"auto_publish\" value=\"1\">";
                            }
                        ?>
                        <br/>
                        <small>
                            <em>
								Automatically publish to the connected distribution platform when publishing from WordPress (supported platforms only).
                            </em>
                        </small>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="title_in_content">Feature Image:</label>
                    </th>
                    <td>
                        <?php
                            echo "<input type=\"hidden\" value=\"0\" name=\"feature_image\">";
                            if($feature_image == TRUE){                                
                                echo "<input type=\"checkbox\" name=\"feature_image\" value=\"1\" checked>";
                            } else {
                                echo "<input type=\"checkbox\" name=\"feature_image\" value=\"1\">";
                            }
                        ?>
                        <br/>
                        <small>
                            <em>
                                Choose if the feature image is included at the top of a published article
                            </em>
                        </small>
                    </td>
                </tr>
				<tr>
					<th scope="row">
                        <label for="custom_posts_types">Custom post types:</label>
                    </th>
                    <td>
                        <input name="custom_posts_types" type="text" value="<?php echo $custom_posts_types;?>" class="regular-text">
						<br/>
                        <small>
                            <em>
								Enter a comma delimited list of custom post types to make them eligible for publishing (eg. news-article, legal-article)
                            </em>
                        </small>
                    </td>
                </tr>
                <?php
                    }}
                ?>   
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" class="button button-primary" value="Save">
        </p>
        <p>Version: <b><?php echo WP_Canvasflow::$version;?></b></p>
    </form>
<div>