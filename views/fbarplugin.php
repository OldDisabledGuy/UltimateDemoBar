<script type="text/javascript" >
    fbar.sortColumn();
    fbar.shortAction();
    jQuery(document).ready(function() {
        jQuery("#sortable").sortable(
                {
                    start: function(event, ui) {
                        ui.item.startPos = ui.item.index();
                    },
                    stop: function(event, ui) {
                       
                        jQuery.post('admin.php?page=fbar&action=sortable',{start: ui.item.startPos, newp :  ui.item.index()});
                    }
                });
        jQuery("#sortable").disableSelection();
    });
</script>
</script>
<div class="buttonActionContainer">
    <a href="javascript:fbar.loadPage('fbarpluginEdit')" onclick="" class="button add-new-h2 buttonAction">
        <?php _e('Add site', $config['plugin_name']) ?>
    </a>
</div>
<div class="w100p bgl">
    <div class="w100p bgr">
        <div class="inner_container">
            <table class="widefat" cellspacing="0" >
                <thead>
                    <tr>
                        <?php print_column_headers('stplugin_stplugin'); ?>
                    </tr>
                </thead>
                <tbody id="sortable">
                    <?php
                    if (count($results) > 0) {
                        $tr_class = "";
                        $upload_data = wp_upload_dir();
                        foreach ($results as $result) {
                            if (empty($tr_class) || $tr_class == "class='bg_gray2'") {
                                $tr_class = "class='bg_gray1 ui-state-default'";
                            } else {
                                $tr_class = "class='bg_gray2 ui-state-default'";
                            }
							if(isset($result->status)){
                            switch ($result->status) {
                                case 1:
                                    $status = __('Completed', $config['plugin_name']);
                                    break;
                                default:
                                    $status = __('Incompleted', $config['plugin_name']);
                                    break;
                            }
							}
							$preview_img =explode('.',$result->preview);
                          
                            echo "<tr id='$result->id' $tr_class >"
                            . "<td>" . $result->objid
                            . "</td>"
                            . "<td>" . $result->url . "</td>"
                   			. "<td>".(!empty($preview_img[count($preview_img)-1])&&($preview_img[count($preview_img)-1]=='png'||$preview_img[count($preview_img)-1]=='jpg')? '<img src="' . $result->preview . '" width="150" >': "")."</td>"
                            . "<td>" . $result->typename . "</td>"
                            . "<td>" . $result->ddn . "</td>"
                            . "<td>"
                            . "<div class=\"short_action\">"
                            . "<span action='fbarpluginEdit' short_action='' id='$result->id' ><img src=\"{$config['imagesUrl']}edit_icon.png\" alt=\"\" /></span>"
                            . "<span class='trash' short_action='delete' id='$result->id' confirm='<strong>Warning!</strong>Are you sure you want to delete this item?' ><img src=\"{$config['imagesUrl']}bin_icon.png\" alt=\"\" /></span>"
                            . "</div>"
                            . "</td>"
                            . "</tr>"
                            ;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <div class="page_d">
                <?php echo $pagination ?>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>