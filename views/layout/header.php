<div class="w100p p15t po">
    <div class="cham_left"></div>
    <div class="cham_right"></div>
    <div class="p15l">
        <div class="<?php echo empty($headIcon)? 'icon_default':$headIcon; ?>">
            <h2 class="red_color upcase">
                <?php
                if (empty($result->id)) {
                    _e($headTitle1, $config['plugin_name']);
                } else {
                    _e($headTitle2, $config['plugin_name']);
                }
                ?>                
            </h2>
        </div>
    </div>
</div>