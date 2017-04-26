<?php
## get current theme name
$option = get_option('fbar_settings');
$current_theme = $_GET['theme'];
$theme_found = false;
## build theme data array
global $wpdb;
$tb = $wpdb->prefix . "st_fbar";
$theme_array = $wpdb->get_results("SELECT * FROM $tb ", ARRAY_A);
if (!$redirect) :
## get current theme data
    foreach ($theme_array as $i => $theme) :
        if ($theme['objid'] == $current_theme) :
            $current_theme_name = ucfirst($theme['objid']);
            $current_theme_url = $theme['url'];
            $current_theme_purchase_url = $theme['ddn'];
            $theme_found = true;
        endif;
    endforeach;
    if ($theme_found == false) :
        $current_theme_name = $theme_array[0]['objid'];
        $current_theme_url = $theme_array[0]['url'];
        $current_theme_purchase_url = $theme_array[0]['ddn'];
    endif;
    ?>
            <!-- CSS Style -->
            <link rel="stylesheet" href="<?php echo plugins_url('/', __FILE__) ?>style.css"> 
         
            <!-- JavaScript -->
            <script type="text/javascript" src="<?php echo plugins_url('/', __FILE__) ?>js/jquery-1.9.1.min.js"></script>
            <script src="<?php echo plugins_url('/', __FILE__) ?>js/custom.js"></script> 
       
            <div id="switcher" <?php if( isset($option['color'] ) && $option['color'] != '' ){ ?> style="background-color: <?php echo $option['color'] ?>" <?php } ?>>
                <div class="center">
                   
                    <ul>
                        <li id="theme_list"><a id="theme_select" href="#"><?php
                                if ($theme_found == false) : echo "Select a theme...";
                                else: echo $current_theme_name;
                                endif;
                                ?></a>
                            <ul>
                                <?php
                                foreach ($theme_array as $i => $theme) :
                                    echo '<li><a href="#" rel="' . $theme['url'] . ',' . $theme['ddn'] . '">' .
                                    ucfirst($theme['objid']) .
                                    ' <span>' . $theme['type'] . '</span></a>';
                                    if (isset($theme['preview'])) {
                                        echo '<img alt="" class="preview" src="';
                                        if (strpos($theme['preview'], 'http://') === false) {
                                            echo 'product_previews/' . $theme['preview'];
                                        }
                                        else
                                            echo $theme['preview'];
                                        echo '">';
                                    }
                                    echo '</li>';
                                endforeach;
                                ?>
                            </ul>
                        </li>	
                    </ul>
                    <?php if ($option['responsive']) { ?>
                        <div class="responsive">
                            <a href="#" class="desktop active" title="View Desktop Version"></a> 
                            <a href="#" class="tabletlandscape" title="View Tablet Landscape (1024x768)"></a> 
                            <a href="#" class="tabletportrait" title="View Tablet Portrait (768x1024)"></a> 
                            <a href="#" class="mobilelandscape" title="View Mobile Landscape (480x320)"></a>
                            <a href="#" class="mobileportrait" title="View Mobile Portrait (320x480)"></a>
                        </div>
                    <?php } ?>
                    <?php if ($option['share']) { ?>
                        <div class="share">
                            <ul>
                                <li><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $current_theme_purchase_url; ?>" data-lang="en">Tweet</a>
                                    <script>!function(d, s, id) {
                                            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                            if (!d.getElementById(id)) {
                                                js = d.createElement(s);
                                                js.id = id;
                                                js.src = p + '://platform.twitter.com/widgets.js';
                                                fjs.parentNode.insertBefore(js, fjs);
                                            }
                                        }(document, 'script', 'twitter-wjs');</script></li>
                                <li><iframe src="//www.facebook.com/plugins/like.php?href=<?php echo $current_theme_purchase_url; ?>&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></li>
                                <li><div class="g-plusone" data-size="medium" data-href="<?php echo $current_theme_purchase_url; ?>"></div></li>
                            </ul>
                        </div>
                    <?php } ?>
                    <ul class="links">
                        <?php if ($option['purchase']) { ?>
                            <li class="purchase" rel="<?php echo $current_theme_purchase_url; ?>">
                                <a href="<?php echo $current_theme_purchase_url; ?>"><img src="<?php echo plugins_url('/', __FILE__) ?>images/purchase.png" alt="Web Design Tunes Themes" /> Purchase</a>
                            </li>
                        <?php } ?>
                        <?php if ($option['close']) { ?>
                            <li class="close" rel="<?php echo $current_theme_url; ?>">
                                <a href="<?php echo $current_theme_url; ?>"><img src="<?php echo plugins_url('/', __FILE__) ?>images/cross.png" alt="Web Design Tunes Themes" /> Close</a></li>		
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <iframe id="iframe" src="<?php echo $current_theme_url; ?>" frameborder="0" width="100%"></iframe>
            <script type="text/javascript">
                (function() {
                    var po = document.createElement('script');
                    po.type = 'text/javascript';
                    po.async = true;
                    po.src = 'https://apis.google.com/js/plusone.js';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(po, s);
                })();
            </script>
            
 <?php endif; ?>