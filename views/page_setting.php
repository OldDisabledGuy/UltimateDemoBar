<script type="text/javascript" >

	fbar.on_submitform('#settingForm', function(data){
        fbar.loadPage('page_setting', '', '<?php _e("Successful", $config['plugin_name']) ?>');
    });
  	fbar.loadUploadButton();
    fbar.loadRemoveButton();
</script>
<div class="w100p bgl">
    <div class="w100p bgr p5b">
        <form class="stform" id="settingForm" action="<?php echo admin_url('/admin.php?page=' . $config['plugin_name'] . '&action=' . $current_action) ?>" method="post" >
            <ul>
                <li>
                    <label>Icon</label>
                    <div >
                        <?php 
                        $class_hide = "hide";
                        if (!empty($option['page_icon'])) {
                            $upload_data = wp_upload_dir();
                            $stplugin_picture_url = $upload_data['url'] . '/' .$option['page_icon'];
                            $stplugin_picture_dir = $upload_data['path'] . '/' . basename($option['page_icon']);
                            $class_hide = "";
                        }
                        ?>
                        <input value="<?php echo $stplugin_picture_dir ?>" name="data[page_icon]" id="id_stplugin_picture" type="hidden"/>
                        <div class="upload_button_div">
                            <span id="stplugin_picture" class="button image_upload_button">Upload Image</span>
                            <span pictureID="stplugin_picture" id="reset_stplugin_picture" class="button image_reset_button <?php echo $class_hide ?>">Remove</span>
                        </div>
                        <?php
                        if (!empty($option['page_icon'])) {
                            ?>
                            <img alt="" src="<?php echo $option['page_icon'] ?>" id="image_stplugin_picture" class="hide preview favicon" style="display: inline;"/>
                        <?php } ?>
                    </div>
                    <br clear="both" />
                </li>
                
                <li>
                    <label><?php _e('Title', $config['plugin_name']) ?>:</label>
                    <div>
                    <input  value="<?php echo $option['title'] ?>"  name="data[title]" type="text"  />
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label><?php _e('Meta Description', $config['plugin_name']) ?>:</label>
                     <div>
                    <textarea name="data[description]"><?php echo $option['description'] ?></textarea>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label><?php _e('Meta Keywords', $config['plugin_name']) ?>:</label>
                    <div>
                    <textarea name="data[keywords]"><?php echo $option['keywords'] ?></textarea>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label><?php _e('Page slug name', $config['plugin_name']) ?>:</label>
                     <div>
                    <input  value="<?php echo $option['slug_name'] ?>"  name="data[slug_name]" type="text"  />
					<br >
					<p>Your page url is :  <a href="<?php echo site_url() ?>/<?php echo $option['slug_name'] ?>" target="_blank"><?php echo site_url() ?>/<?php echo $option['slug_name'] ?></a></p>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <input type="submit" value="Submit"/>
                </li>
            </ul>
        </form>            
    </div>
</div>