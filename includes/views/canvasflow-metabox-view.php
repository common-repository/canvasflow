
<table class="form-table">
    <tbody>
        <input id="cf_post_id" type="hidden" value="<?php echo $post_id;?>">
        <input id="cf_nonce_send_article" name="cf_nonce_send_article" type="hidden" value="<?php echo wp_create_nonce('cf-send-article'); ?>" />
        <?php 
            if($publication_type != "issue") {
        ?>
            <input type="hidden" id="cf_issue_id" value="<?php echo $issues[0]['id'];?>">
        <?php
            } else {
            if(count($issues) > 0){
        ?>
            <tr>
                <th scope="row">
                    <label for="publication_id">Issue</label>
                </th>
                <td>
                    <select class="selectpicker" id="cf_issue_id" required>
                        <?php 
                            $select_options_issue = '';
                            foreach($issues as $issue) {
                                $issue_id = (string) $issue['id'];
                                $issue_name = $issue['name'];
                                if($issue_id == $selected_issue_id) {
                                    $select_options_issue.= '<option value="'.$issue_id.'" selected>'.$issue_name.'</option>';
                                } else {
                                    $select_options_issue.= '<option value="'.$issue_id.'">'.$issue_name.'</option>';
                                }  
                            }
                            echo $select_options_issue;
                        ?>
                    </select>
                    <br>
                    <!--<small>
                        <em>
                            Target issue
                        </em>
                    </small>-->
                </td>
            </tr>
        <?php 
            }
        }
        ?>
        <?php 
            if(count($collections) > 0) {
        ?>
            <tr>
                <th scope="row">
                    <label for="style_id">Collection</label>
                </th>
                <td>
                    <select class="selectpicker" id="cf_collection_id" required>
                        <?php 
                            $select_options_collection = '';
                            foreach($collections as $collection) {
                                $collection_id = (string) $collection['id'];
                                $collection_name = $collection['name'];
                                if($collection_id == $selected_collection_id) {
                                    $select_options_collection.= '<option value="'.$collection_id.'" selected>'.$collection_name.'</option>';
                                } else {
                                    $select_options_collection.= '<option value="'.$collection_id.'">'.$collection_name.'</option>';
                                }
                            }
                            echo $select_options_collection;
                        ?>
                    </select>
                    <br>
                    <!--<small>
                        <em>
                            CF Style
                        </em>
                    </small>-->
                </td>
            </tr>
        <?php
            } else {
                echo "<input type=\"hidden\" id=\"cf_collection_id\" value=\"\">";
            }
            if(count($styles) > 0){
        ?>
            <tr>
                <th scope="row">
                    <label for="style_id">Style</label>
                </th>
                <td>
                    <select class="selectpicker" id="cf_style_id" required>
                        <?php 
                            $select_options_style = '';
                            foreach($styles as $style) {
                                $style_id = (string) $style['id'];
                                $style_name = $style['name'];
                                if($style_id == $selected_style_id) {
                                    $select_options_style.= '<option value="'.$style_id.'" selected>'.$style_name.'</option>';
                                } else {
                                    $select_options_style.= '<option value="'.$style_id.'">'.$style_name.'</option>';
                                }
                            }
                            echo $select_options_style;
                        ?>
                    </select>
                    <br>
                    <!--<small>
                        <em>
                            CF Style
                        </em>
                    </small>-->
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="style_id">Status</label>
                </th>
                <td id="cf_state" class="<?php echo $post_state_style?>">
                    <?php echo $post_state_content?>
                </td>
                <div id="cf-alert" class="meta-box-alert"> </div>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>
<hr>
<div style="text-align: right;">
    <a id="cf_publish" class="meta-box-button" href="javascript:;">Publish to Canvasflow</a>
</div>