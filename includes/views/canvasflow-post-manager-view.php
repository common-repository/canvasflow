
<div class="wrap">
    <h1 class="wp-heading-inline">Canvasflow Post Manager</h1>
    <hr />
    <br />
    <a href="post-new.php" class="page-title-action">Add New</a>    
    <button class="page-title-action" style="background: #0085ba;
    border-color: #0073aa #006799 #006799; color: #fff;" form="syncPosts" type="submit">Save</button>
    <hr class="wp-header-end">
    <h2 class="screen-reader-text">Filter posts list</h2>
    <ul class="subsubsub">
        <li class="all"><a href="edit.php?post_type=post" class="current">All <span class="count">(<?php echo $total_of_post;?>)</span></a></li>
    </ul>
        <form method="post" action="admin.php?page=canvasflow-posts-manager">
            <p class="search-box">
                <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
                <input type="search" name="search">
                <input type="submit" class="button" value="Search">
            </p>
        </form>
        <div class="tablenav top">
                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php echo sizeof($posts)?> items</span>
                    <span>
                        <?php
                            if($page > 1){
                                if($page > 2){
                                    if(is_null($search)){
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p=1&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">«</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p=1&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">«</span></a>";
                                    }
                                    
                                } else {
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">«</span>";
                                }
                                $previous_page = $page - 1;
                                if(is_null($search)){
                                    echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$previous_page}&amp;order_by={$order_by}&amp;order={$order}\">‹</a>";
                                } else {
                                    echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$previous_page}&amp;order_by={$order_by}&amp;search={$search}\">‹</a>";
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
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    }
                                    
                                    if($next_page == $total_of_pages){
                                        echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">»</span>";
                                    } else {
                                        if(is_null($search)){
                                            echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">»</span></a>";
                                        } else {
                                            echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">»</span></a>";
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
                    <td class="manage-column column-cb check-column"></td>
                    <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <?php 
                                if(is_null($search)){
                                    echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=title&amp;order={$order}\">";
                                } else {
                                    echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=title&amp;order={$order}&amp;search={$search}\">";
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
                                    echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=author&amp;order={$order}\">";
                                } else {
                                    echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=author&amp;order={$order}&amp;search={$search}\">";
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
                    <th scope="col" id="type" class="manage-column column-author">
                            <?php 
                                if(is_null($search)){
                                    echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=type&amp;order={$order}\">";
                                } else {
                                    echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=type&amp;order={$order}&amp;search={$search}\">";
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
                    <th scope="col" id="date" class="manage-column column-date sortable asc">
                        <?php 
                            if(is_null($search)){
                                echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=modified_date&amp;order={$order}\">";
                            } else {
                                echo "<a href=\"admin.php?page=canvasflow-posts-manager&amp;order_by=modified_date&amp;order={$order}&amp;search={$search}\">";
                            }
                        ?>
                            <span>Modified</span>
                            <?php 
                                if($order_by == "modified_date"){
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
            <form method="post" action="admin.php?page=canvasflow-posts-manager" id="syncPosts">
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
                        $user_display_name = $post->display_name;
                        $user_id = $post->user_id;
                        $type = $post->type;
                        
                        $published = $post->published;
                        $canvasflow_post_id = $post->canvasflow_post_id;
                        $post_modified_date = $post->post_modified_date;

                        $existInCanvasflowPosts = FALSE;
                        if(isset($canvasflow_post_id)){
                            $existInCanvasflowPosts = TRUE;
                        }

                ?>
                <tr id="post-<?php echo $id;?>" class="iedit author-self level-0 post-<?php echo $id;?> type-post status-publish format-standard hentry category-uncategorized">
                    <th scope="row" class="check-column">
                        <?php 
                            echo "<input type=\"hidden\" value=\"0\" name=\"{$id}\">";
                            if($existInCanvasflowPosts == TRUE){
                                echo "<input type=\"checkbox\" name=\"{$id}\" value=\"1\" checked>";
                            } else {
                                echo "<input type=\"checkbox\" name=\"{$id}\" value=\"1\">";
                            }
                        ?>			
                        
                    </th>
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
                    <td class="author column-author" data-colname="Type"><?php echo $type;?></td>
                    <td class="date column-date" data-colname="Modified date">
                        <?php 
                            if(!$post_modified_date){
                                echo "-";
                            } else {
                                echo date_format(date_create($post_modified_date),"Y/m/d h:i A ");
                            }
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </form>
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
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p=1&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">«</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p=1&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">«</span></a>";
                                    }
                                    
                                } else {
                                    echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">«</span>";
                                }
                                $previous_page = $page - 1;
                                if(is_null($search)){
                                    echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$previous_page}&amp;order_by={$order_by}&amp;order={$order}\">‹</a>";
                                } else {
                                    echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$previous_page}&amp;order_by={$order_by}&amp;search={$search}\">‹</a>";
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
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    } else {
                                        echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$next_page}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">›</span></a>";
                                    }
                                    
                                    if($next_page == $total_of_pages){
                                        echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">»</span>";
                                    } else {
                                        if(is_null($search)){
                                            echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}\"><span aria-hidden=\"true\">»</span></a>";
                                        } else {
                                            echo "<a class=\"next-page\" href=\"admin.php?page=canvasflow-posts-manager&amp;p={$total_of_pages}&amp;order_by={$order_by}&amp;order={$order}&amp;search={$search}\"><span aria-hidden=\"true\">»</span></a>";
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
