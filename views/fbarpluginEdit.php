<script type="text/javascript" >
//    fbar.load_tooltip();
    fbar.on_submitform('#fbarForm', function(data){
        fbar.loadPage('fbarplugin', '', '<?php _e("Successful", $config['plugin_name']) ?>');
    });
    fbar.loadUploadButton();
    fbar.loadRemoveButton();
    jQuery(document).ready(function(){
        jQuery('#color1').colorPicker();
    });
</script>
<div class="w100p bgl">
    <div class="w100p bgr p5b">
        <form class="stform" id="fbarForm" action="<?php echo admin_url('/admin.php?page=' . $config['plugin_name'] . '&action=' . $current_action) ?>" method="post" >
            <ul>
                <li>
                    <label><?php _e('ID', $config['plugin_name']); ?>:</label>
                    <div>
                        <input value="<?php echo (!empty($result->objid)?$result->objid:'') ?>" name="data[objid]" id="paykey" type="text"/>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label><?php _e('Url', $config['plugin_name']); ?>:</label>
                    <div>
                        <input value="<?php echo (!empty($result->url)?$result->url:'') ?>" name="data[url]" id="customer_id" type="text"/>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label>Preview</label>
                    <div>
                        <?php 
                        $class_hide = "hide";
                        if (!empty($result->preview)) {
                            $upload_data = wp_upload_dir();
                            $stplugin_picture_url = $upload_data['url'] . '/' .$result->preview;
                            $stplugin_picture_dir = $upload_data['path'] . '/' . basename($result->preview);
                            $class_hide = "";
                        }
                        ?>
                        <input value="<?php echo $stplugin_picture_dir ?>" name="data[preview]" id="id_stplugin_picture" type="hidden"/>
                        <div class="upload_button_div">
                            <span id="stplugin_picture" class="button image_upload_button">Upload Image</span>
                            <span pictureID="stplugin_picture" id="reset_stplugin_picture" class="button image_reset_button <?php echo $class_hide ?>">Remove</span>
                        </div>
                        <?php
                        //if (!empty($result->preview) ) {
  $preview_img =explode('.',$result->preview);
                          if(!empty($preview_img[count($preview_img)-1])&&($preview_img[count($preview_img)-1]=='png'||$preview_img[count($preview_img)-1]=='jpg')){
                            ?>
                            <img alt="" src="<?php echo $result->preview; ?>" id="image_stplugin_picture" class="hide preview" style="display: inline;"/>
                        <?php } ?>
                    </div>
                    <br clear="both" />
                </li>
                 <li>
                    <label><?php _e('Type', $config['plugin_name']); ?>:</label>
                    <div >
                        <input  value="<?php echo (!empty($result->typename)?$result->typename:'') ?>" name="data[typename]" type="text"/>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label><?php _e('Color', $config['plugin_name']); ?>:</label>
                    <div style="position: relative;" id="talaothat">
                        <input id="color1" value="<?php echo (!empty($result->type)?$result->type:'') ?>" name="data[type]" type="text"/>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <label><?php _e(' Download URL', $config['plugin_name']); ?>:</label>
                    <div>
                        <input title="Sample tip" value="<?php echo (!empty($result->ddn)?$result->ddn:'') ?>" class="desc_true" name="data[ddn]" id="form_ip" type="text"/>
                    </div>
                    <br clear="both" />
                </li>
                <li>
                    <input type="submit" value="Submit"/>
                </li>
            </ul>
            <input type="hidden" value="<?php echo (!empty($result->id)?$result->id:'') ?>" id="id" name="data[id]"/>
        </form>
    </div>
</div>