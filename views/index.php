<script type="text/javascript" >
//    stplugin.load_tooltip('#settingForm');
    fbar.on_submitform('#settingForm');
    fbar.loadUploadButton('200');
    fbar.loadRemoveButton();
    jQuery(document).ready(function(){
        jQuery('#color1').colorPicker();
    });
    
</script>
<div class="w100p bgl">
    <div class="w100p bgr p5b">
        <form class="stform" id="settingForm" action="<?php echo admin_url('/admin.php?page=' . $config['plugin_name'] . '&action=' . $current_action) ?>" method="post" >
            <ul>
                <li>
                    <label>Logo</label>
                    <div>
                        <?php 
                        $class_hide = "hide";
                        if (!empty($option['stplugin_picture'])) {
                            $upload_data = wp_upload_dir();
                            $stplugin_picture_url = $upload_data['url'] . '/' .$option['stplugin_picture'];
                            $stplugin_picture_dir = $upload_data['path'] . '/' . basename($option['stplugin_picture']);
                            $class_hide = "";
                        }
                        ?>
                        <input value="<?php echo $stplugin_picture_dir ?>" name="data[stplugin_picture]" id="id_stplugin_picture" type="hidden"/>
                        <div class="upload_button_div">
                            <span id="stplugin_picture" class="button image_upload_button">Upload Image</span>
                            <span pictureID="stplugin_picture" id="reset_stplugin_picture" class="button image_reset_button <?php echo $class_hide ?>">Remove</span>
                        </div>
                        <?php
                        if (!empty($option['stplugin_picture']) ) {
                            ?>
                            <img alt="" src="<?php echo $option['stplugin_picture'] ?>" id="image_stplugin_picture" class="hide preview" style="display: inline;"/>
                        <?php } ?>
                    </div>
                    <br clear="both" />
                </li>
                <li >
                    <label><?php _e('Color', $config['plugin_name']) ?>:</label>
                    <div style="position: relative;" id="talaothat">
                     <input id="color1" value="<?php echo $option['color'] ?>"  name="data[color]" type="text"  />
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label>Responsive button</label>
                    <div>
                        <input <?php echo $option['responsive'] == 1 ? 'checked' : '' ?> title="Radio 1" value="1" name="data[responsive]" id="radio1" type="radio" tabindex="5" />
                        <label><?php _e('Yes', $config['plugin_name']) ?></label>
                        <input <?php echo $option['responsive'] == 0 ? 'checked' : '' ?> title="Radio 2" value="0" name="data[responsive]" id="radio11" type="radio" tabindex="6" />
                        <label><?php _e('No', $config['plugin_name']) ?></label>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label>Close button</label>
                    <div>
                        <input <?php echo $option['close'] == 1 ? 'checked' : '' ?> title="Radio 1" value="1" name="data[close]" id="radio1" type="radio" tabindex="5" />
                        <label><?php _e('Yes', $config['plugin_name']) ?></label>
                        <input <?php echo $option['close'] == 0 ? 'checked' : '' ?> title="Radio 2" value="0" name="data[close]" id="radio11" type="radio" tabindex="6" />
                        <label><?php _e('No', $config['plugin_name']) ?></label>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label>Purchase button</label>
                    <div>
                        <input <?php echo $option['purchase'] == 1 ? 'checked' : '' ?> title="Radio 1" value="1" name="data[purchase]" id="radio1" type="radio" tabindex="5" />
                        <label><?php _e('Yes', $config['plugin_name']) ?></label>
                        <input <?php echo $option['purchase'] == 0 ? 'checked' : '' ?> title="Radio 2" value="0" name="data[purchase]" id="radio11" type="radio" tabindex="6" />
                        <label><?php _e('No', $config['plugin_name']) ?></label>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label>Share button</label>
                    <div>
                        <input <?php echo $option['share'] == 1 ? 'checked' : '' ?> title="Radio 1" value="1" name="data[share]" id="radio1" type="radio" tabindex="5" />
                        <label><?php _e('Yes', $config['plugin_name']) ?></label>
                        <input <?php echo $option['share'] == 0 ? 'checked' : '' ?> title="Radio 2" value="0" name="data[share]" id="radio11" type="radio" tabindex="6" />
                        <label><?php _e('No', $config['plugin_name']) ?></label>
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
