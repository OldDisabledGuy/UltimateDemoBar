<?php
 $pageURL = $_SERVER["REQUEST_URI"];
$parts = Explode('/', $pageURL);
$page= $parts[count($parts) - 1] ;
$redvalue= substr($page,0,12);
if($redvalue=='redirect.php')
{
$phrase  = $pageURL;
$healthy = array("redirect.php");
$yummy   = array("");
 $newphrase = str_replace($healthy, $yummy, $phrase);
header("location:".$newphrase);
}
function is_firefox() {
    $agent = '';
    // old php user agent can be found here
    if (!empty($HTTP_USER_AGENT))
        $agent = $HTTP_USER_AGENT;
    // newer versions of php do have useragent here.
    if (empty($agent) && !empty($_SERVER["HTTP_USER_AGENT"]))
        $agent = $_SERVER["HTTP_USER_AGENT"];
    if (!empty($agent) && preg_match("/firefox/si", $agent))
        return true;
    return false;
}
function is_windows() {
    $agent = '';
    // old php user agent can be found here
    if (!empty($HTTP_USER_AGENT))
        $agent = $HTTP_USER_AGENT;
    // newer versions of php do have useragent here.
    if (empty($agent) && !empty($_SERVER["HTTP_USER_AGENT"]))
        $agent = $_SERVER["HTTP_USER_AGENT"];
    if (!empty($agent) && preg_match("/windows/si", $agent))
        return true;
    return false;
}
## get current theme name
$option = get_option('fbar_settings');
$poption = get_option('page_fbar_settings');
$current_theme = (isset($_GET['theme'])? $_GET['theme'] : '');
$theme_found = false;
## build theme data array
global $wpdb;
$tb = $wpdb->prefix . "st_fbar";
$theme_array = $wpdb->get_results("SELECT * FROM $tb ORDER BY sort_order ASC", ARRAY_A);
if (!get_option('xredirect') || 1):
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
 $currentFile = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    ?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<?php //echo 'poption<pre>'.print_r($poption,true).'</pre>';?>
<?php //echo 'option<pre>'.print_r($option,true).'</pre>';?>
<title><?php echo (!empty($poption['title'])?$poption['title']:'') ?></title>
<!-- Mobile Specific -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="description" content="<?php echo (!empty($poption['description'])?$poption['description']:'') ?>" >
<meta name="keywords" content="<?php echo (!empty($poption['keywords'])?$poption['keywords']:'') ?>" >
<!-- CSS Style -->
<link rel="stylesheet" href="<?php echo plugins_url('/') ?>fbar/css/frame.css">
<!-- Favicons -->
<link rel="shortcut icon" href="<?php echo $poption['page_icon'] ?>">
<!-- JavaScript -->
<script type="text/javascript" src="<?php echo plugins_url('/') ?>fbar/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.js"></script>
<script src="<?php echo plugins_url('/') ?>fbar/js/custom.js"></script>
</head>
<body>
<div id="switcher" <?php if (isset($option['color']) && $option['color'] != '') { ?> style="background-color: <?php echo $option['color'] ?>" <?php } ?>>
  <div class="center">
    <?php 
	$preview_img =explode('.',$option['stplugin_picture']);
	if(!empty($preview_img[count($preview_img)-1])&&($preview_img[count($preview_img)-1]=='png'||$preview_img[count($preview_img)-1]=='jpg')){
	?>
    <div class="logo"> <a href="<?php site_url() ?>" title="<?php echo $poption['title'] ?>"> <img height="55" src="<?php echo isset($option['stplugin_picture']) && $option['stplugin_picture'] != '' ? $option['stplugin_picture'] : '' ?>" alt="<?php echo $poption['title'] ?>" /> </a> </div>
    <?php }?>
    <ul>
      <li id="theme_list"><a id="theme_select" href="#">
        <?php
			if ($theme_found == false) : echo "Select a theme...";
			else: echo $current_theme_name;
			endif;
		?>
        </a>
        <ul id="test1a">
          <?php
			foreach ($theme_array as $i => $theme) :
				echo '<li><a href="'.get_bloginfo('url').'/'.$poption['slug_name'].'?theme='.($theme['objid']). '" >' .
				ucfirst($theme['objid']) .(!empty($theme['typename'])?' <span style="background-color: ' . $theme['type'] . '">' . $theme['typename'] . '</span>':'').'</a>';
$preview_theme =explode('.',$theme['preview']);
					if(!empty($preview_theme[count($preview_theme)-1])&&$preview_theme[count($preview_theme)-1]=='png'){
					echo '<img alt="" class="preview" src="';
					if (strpos($theme['preview'], 'http://') === false)
					{
						echo 'product_previews/' . $theme['preview'];
					}
					else
					{
						echo $theme['preview'];
					}
					echo '" />';
				}
				echo '</li>';
			endforeach;
			?>
        </ul>
      </li>
    </ul>
    <?php if (!empty($option['responsive'])) { ?>
      <div class="responsive"> <a href="#" class="desktop active" title="View Desktop Version"></a> <a href="#" class="tabletlandscape" title="View Tablet Landscape (1024x768)"></a> <a href="#" class="tabletportrait" title="View Tablet Portrait (768x1024)"></a> <a href="#" class="mobilelandscape" title="View Mobile Landscape (480x320)"></a> <a href="#" class="mobileportrait" title="View Mobile Portrait (320x480)"></a> </div>
      <?php } ?>
    <?php if (!empty($option['share'])) { ?>
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
        <li>
          <div id="fb-root"></div>
          <script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
          <div class="fb-share-button" data-href="<?php echo $current_theme_purchase_url; ?>" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_theme_purchase_url; ?>%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share</a></div>
        </li>
        <li> 
          <!-- Place this tag where you want the share button to render. -->
          <div class="g-plus" data-action="share" data-annotation="bubble" data-href="<?php echo $current_theme_purchase_url; ?>"></div>
          <script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script></li>
      </ul>
    </div>
  <?php } ?>
  <ul class="links">
    <?php if (!empty($option['purchase'])) { ?>
      <li class="purchase" rel="<?php echo $current_theme_purchase_url; ?>"> <a href="<?php echo $current_theme_purchase_url; ?>"><img src="<?php echo plugins_url('/') ?>fbar/images/purchase.png" alt="Web Design Tunes Themes" /> Purchase</a> </li>
      <?php } ?>
    <?php if (!empty($option['close'])) { ?>
      <li class="close" rel="<?php echo $current_theme_url; ?>"> <a href="<?php echo $current_theme_url; ?>"><img src="<?php echo plugins_url('/') ?>fbar/images/cross.png" alt="Web Design Tunes Themes" /> Close</a></li>
      <?php } ?>
  </ul>
</div>
</div>
<iframe id="iframe" src="<?php echo $current_theme_url; ?>" frameborder="0" width="100%" height="100%"></iframe>
</body>
</html>
<?php
endif;
?>