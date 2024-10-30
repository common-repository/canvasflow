<div class="wrap">

    <h1 class="wp-heading-inline">Canvasflow Upload Manager</h1>
    <hr />
    <br />
    <hr class="wp-header-end">
    <h2 class="screen-reader-text">Filter posts list</h2>
    <ul class="subsubsub">
        <li class="all"><a href="edit.php?post_type=post" class="current">All <span class="count">(<?php echo sizeof($posts);?>)</span></a></li>
    </ul>
        <form method="post" action="admin.php?page=wp-canvasflow-plugin">
        
            <p class="search-box">
                <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
                <input type="search" name="search">
                <input type="submit" class="button" value="Search">
            </p>
        </form>
        <div class="tablenav top">
            <form method="post" action="admin.php?page=wp-canvasflow-plugin">
                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php echo sizeof($posts)?> items</span>
                    <span>
                        <?php
                            if($page > 1){
                                if($page > 2){
                                    if(is_null($search)){
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p=1&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">«</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p=1&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">«</span></a>";
                                    }
                                    
                                } else {
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">«</span>";
                                }
                                $previous_page = $page - 1;
                                if(is_null($search)){
                                    echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$previous_page}&amp;order_by={$order_by}&amp;order={$order}\">‹</a>";
                                } else {
                                    echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$previous_page}&amp;order_by={$order_by}&amp;search={$search}\">‹</a>";
                                }
                                

                            } else {
                                echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">«</span>";
                                echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">‹</span>";
                            }
                        ?>
                        
                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                            <input class="current-page" id="current-page-selector" type="number" name="p" value="<?php echo $page;?>" size="1" aria-describedby="table-paging" required>
                            <input type="hidden" name="order_by" value="<?php echo $order_by;?>">
                            <input type="hidden" name="order" value="<?php echo $order;?>">
                            <?php
                                if(!is_null($search)){
                                    echo "<input type=\"hidden\" name=\"search\" value=\"{$search}\">";
                                }
                            ?>
                            <span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $total_of_pages;?></span></span></span>
                            <?php
                                if($page < $total_of_pages){
                                    $next_page = $page + 1;
                                    if(is_null($search)){
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    }
                                    
                                    if($next_page == $total_of_pages){
                                        echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">»</span>";
                                    } else {
                                        if(is_null($search)){
                                            echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">»</span></a>";
                                        } else {
                                            echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">»</span></a>";
                                        }
                                        
                                    }
                                } else {
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">›</span>";
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">»</span>";
                                }  
                            ?>                        
                        </span>
                </div>
            </form>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">Posts list</h2>
        <?php 
            if($order === "ASC"){
                $order = "DESC";
            } else {
                $order = "ASC";
            }
        ?>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <?php 
                                if(is_null($search)){
                                    echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=title&amp;order={$order}\">";
                                } else {
                                    echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=title&amp;order={$order}&amp;search={$search}\">";
                                }
                            ?>
                            <span>Title</span>
                            <?php 
                                if($order_by == "title"){
                                    if($order == "ASC"){
                                        echo "<span class=\"dashicons dashicons-arrow-down\"></span>";
                                    } else {
                                        echo "<span class=\"dashicons dashicons-arrow-up\"></span>";
                                    }
                                }
                            ?>
                        </a>
                    </th>
                        <th scope="col" id="author" class="manage-column column-author">
                            <?php 
                                if(is_null($search)){
                                    echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=author&amp;order={$order}\">";
                                } else {
                                    echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=author&amp;order={$order}&amp;search={$search}\">";
                                }
                            ?>
                            <span>Author</span>
                            <?php 
                                if($order_by == "author"){
                                    if($order == "ASC"){
                                        echo "<span class=\"dashicons dashicons-arrow-down\"></span>";
                                    } else {
                                        echo "<span class=\"dashicons dashicons-arrow-up\"></span>";
                                    }
                                }
                            ?>
                    </th>
                    <th scope="col" id="comments" class="manage-column column-comments num sortable desc">
                        <span>Upload</span>
                    </th>
                    <?php 
                        if(count($collections) > 0) {
                    ?>
                        <th scope="col" id="collections" class="manage-column column-author num sortable desc">
                            <span>Collection</span>
                        </th>
                    <?php }?>
                    <th scope="col" id="styles" class="manage-column column-author num sortable desc">
                        <span>Style</span>
                    </th>
                    <?php 
                        if($publication_type == "issue") {
                    
                    ?> 
                            <th scope="col" id="issues" class="manage-column column-author num sortable desc">
                                <span>Issue</span>
                            </th>
                    <?php
                        }
                    ?>
                    <th scope="col" id="styles" class="manage-column column-author num sortable desc">
                            <?php 
                                if(is_null($search)){
                                    echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=type&amp;order={$order}\">";
                                } else {
                                    echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=type&amp;order={$order}&amp;search={$search}\">";
                                }
                            ?>
                            <span>Type</span>
                            <?php 
                                if($order_by == "type"){
                                    if($order == "ASC"){
                                        echo "<span class=\"dashicons dashicons-arrow-down\"></span>";
                                    } else {
                                        echo "<span class=\"dashicons dashicons-arrow-up\"></span>";
                                    }
                                }
                            ?>
                    </th>
                    <th scope="col" id="date" class="manage-column column-author sortable asc">
                        <?php 
                            if(is_null($search)){
                                echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=date&amp;order={$order}\">";
                            } else {
                                echo "<a href=\"admin.php?page=wp-canvasflow-plugin&amp;order_by=date&amp;order={$order}&amp;search={$search}\">";
                            }
                        ?>
                            <span>Published</span>
                            <?php 
                                if($order_by == "date"){
                                    if($order == "ASC"){
                                        echo "<span class=\"dashicons dashicons-arrow-down\"></span>";
                                    } else {
                                        echo "<span class=\"dashicons dashicons-arrow-up\"></span>";
                                    }
                                }
                            ?>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php 
                    if($order === "ASC"){
                        $order = "DESC";
                    } else {
                        $order = "ASC";
                    }
                ?>
                <?php
                    for($i = 0; $i < sizeof($posts); $i++){
                        $post = $posts[$i];
                        $id = $post->id;
                        $title = $post->title;
                        $content = $post->content;
                        $type = $post->type;
                        $user_display_name = $post->display_name;
                        $user_id = $post->user_id;
                        
                        $published = $post->published;
                        $canvasflow_post_id = $post->canvasflow_post_id;
                        $post_modified_date = $post->post_modified_date;
                        $current_style_id = $post->style_id;
                        $current_issue_id = $post->issue_id;
                        $current_collection_id = $post->collection_id;
                        if($post->style_id == '') {
                            $current_style_id = $default_style_id;
                        }
                        if($post->issue_id == '') {
                            $current_issue_id = $default_issue_id;
                        }

                ?>
                <tr id="post-<?php echo $id;?>" class="iedit author-self level-0 post-<?php echo $id;?> type-post status-publish format-standard hentry category-uncategorized">
                    <form method="post" action="admin.php?page=wp-canvasflow-plugin" id="upload-form-<?php echo $id;?>">
                    <input name="cf_nonce_send_article" type="hidden" value="<?php echo wp_create_nonce('cf-send-article'); ?>" />
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                        <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                        <strong><a class="row-title" href="post.php?post=<?php echo $id;?>&amp;action=edit" aria-label="“<?php echo $title;?>” (Edit)"><?php echo $title;?></a></strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="post.php?post=<?php echo $id;?>&amp;action=edit" aria-label="Edit “<?php echo $title;?>”">Edit</a> | 
                            </span>
                            <span class="view">
                                <a href="<?php echo get_permalink( $id );?>" rel="permalink" aria-label="View “<?php echo $title;?>”">View</a>
                            </span>
                        </div>
                    </td>
                    <td class="author column-author" data-colname="Author"><a href="edit.php?post_type=post&amp;author=<?php echo $user_id;?>"><?php echo $user_display_name;?></a></td>
                    <td class="comments column-comments" data-colname="Upload">
                        
                            <div class="post-com-count-wrapper">
                                
                                <input type="hidden" name="id" value="<?php echo $id;?>">
                                <input type="hidden" name="p" value="<?php echo $page;?>">
                                <input type="hidden" name="order_by" value="<?php echo $order_by;?>">
                                <input type="hidden" name="order" value="<?php echo $order;?>">

                                <?php 
                                    if(!is_null($search)){
                                        echo "<input type=\"hidden\" name=\"search\" value=\"{$search}\">";
                                    }
                                ?>
                                
                                <button type="submit" form="upload-form-<?php echo $id;?>" value="Submit" style="border:none; background-color: transparent; cursor: pointer;">
                                    <?php 
                                        if($published != NULL){
                                            if(strtotime($post_modified_date) > strtotime($published)){
                                                echo "<span class=\"dashicons dashicons-upload\" style=\"color: orange;\" title=\"Upload\">";
                                            } else {
                                                echo "<span class=\"dashicons dashicons-upload\" style=\"color: green;\" title=\"Upload\">";
                                            }
                                            
                                        } else {
                                            echo "<span class=\"dashicons dashicons-upload\" style=\"color: grey;\" title=\"Upload\">";
                                        }
                                    ?>
                                    
                                </button>
                            </div>
                        
                    </td>
                    <?php 
                        if(count($collections) > 0) {
                    ?>
                        <td class="date column-author" data-colname="Style">
                        
                        <?php  
                            $collection_select = "<select class=\"selectpicker\" name=\"collection_id\" style=\"position: relative; width: 100%\" required>";
                            foreach($collections as $collection) {
                                $id = $collection['id'];
                                $collection_name = $collection['name'];
                                if($id == $current_collection_id) {
                                    $collection_select.= '<option value="'.$id.'" selected name="'.$current_collection_id.'">'.$collection_name.'</option>';
                                } else {
                                    $collection_select.= '<option value="'.$id.'">'.$collection_name.'</option>';
                                }
                            }
                            $collection_select.= "</select>";
                            echo $collection_select;
                        ?>
                    </td>
                    <?php } else {
                        echo "<input type=\"hidden\" name=\"collection_id\" value=\"\">";
                    }?>
                    <td class="date column-author" data-colname="Style">
                        
                        <?php  
                            $style_select = "<select class=\"selectpicker\" name=\"style_id\" style=\"position: relative; width: 100%\" required>";
                            foreach($styles as $style) {
                                $id = $style['id'];
                                $style_name = $style['name'];
                                if($id == $current_style_id) {
                                    $style_select.= '<option value="'.$id.'" selected name="'.$current_style_id.'">'.$style_name.'</option>';
                                } else {
                                    $style_select.= '<option value="'.$id.'">'.$style_name.'</option>';
                                }
                            }
                            $style_select.= "</select>";
                            echo $style_select;
                        ?>
                    </td>
                    <?php
                        if($publication_type == "issue") {
                    ?>
                        <td class="date column-author" data-colname="Style">
                            
                            <?php  
                                $issue_select = "<select class=\"selectpicker\" name=\"issue_id\" style=\"position: relative; width: 100%\" required>";
                                foreach($issues as $issue) {
                                    $id = $issue['id'];
                                    $issue_name = $issue['name'];
                                    if($id == $current_issue_id) {
                                        $issue_select.= '<option value="'.$id.'" selected name="'.$current_issue_id.'">'.$issue_name.'</option>';
                                    } else {
                                        $issue_select.= '<option value="'.$id.'">'.$issue_name.'</option>';
                                    }
                                }
                                $issue_select.= "</select>";
                                echo $issue_select;
                            ?>
                        </td>
                    <?php 
                        } else {
                    ?>
                            <input type="hidden" name="issue_id" value="<?php echo $issues[0]['id']?>">
                    <?php
                        }
                    ?>
                    <td class="date column-author" data-colname="Type">
                        <div><?php echo $type;?></div>
                    </td>
                    <td class="date column-author" data-colname="Date">
                        <?php 
                            if(!$published){
                                echo "-";
                            } else {
                                echo date_format(date_create($published),"Y/m/d h:i A ");
                            }
                        ?>
                    </td>
                    </form>
                </tr>
                <?php
                    }
                ?>

            </tbody>
        </table>
        <div class="tablenav bottom">
            <div class="alignleft actions">
            </div>
                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php echo sizeof($posts)?> items</span>
                    <span>
                        <?php
                            if($page > 1){
                                if($page > 2){
                                    if(is_null($search)){
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p=1&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">«</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p=1&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">«</span></a>";
                                    }
                                    
                                } else {
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">«</span>";
                                }
                                $previous_page = $page - 1;
                                if(is_null($search)){
                                    echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$previous_page}&amp;order_by={$order_by}&amp;order={$order}\">‹</a>";
                                } else {
                                    echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$previous_page}&amp;order_by={$order_by}&amp;search={$search}\">‹</a>";
                                }
                                

                            } else {
                                echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">«</span>";
                                echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">‹</span>";
                            }
                        ?>
                        
                        <span class="paging-input">
                            
                            <?php
                                if(!is_null($search)){
                                    echo "<input type=\"hidden\" name=\"search\" value=\"{$search}\">";
                                }
                            ?>
                            <span class="tablenav-paging-text"><span class="total-pages"> <?php echo $page;?> of <?php echo $total_of_pages;?></span></span></span>
                            <?php
                                if($page < $total_of_pages){
                                    $next_page = $page + 1;
                                    if(is_null($search)){
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    }
                                    
                                    if($next_page == $total_of_pages){
                                        echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">»</span>";
                                    } else {
                                        if(is_null($search)){
                                            echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">»</span></a>";
                                        } else {
                                            echo "<a class=\"next-page\" href=\"admin.php?page=wp-canvasflow-plugin&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">»</span></a>";
                                        }
                                        
                                    }
                                } else {
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">›</span>";
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">»</span>";
                                }  
                            ?>                        
                        </span>
                </div>
            <br class="clear">
        </div>
    <br class="clear">
</div>
