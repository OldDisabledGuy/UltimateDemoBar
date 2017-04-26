<?php
/*
  Plugin Name: Ultimate Demo Bar
  Author: John Blair
  Author URI: http://theolddisabledguy.com
  Plugin URI: http://theolddisabledguy.com
  Description: Ultimate Demo Bar - A Responsive Theme Switcher Demo Bar WordPress Plugin that shows Price, Demo Picture.
  Version: 0.5
  This plugin inherits the GPL license from it's parent system, WordPress.
 */
/* ----------------------------------------------------------------------------------- */
/* Start Plugin Functions - Please refrain from editing this file */
/* ----------------------------------------------------------------------------------- */
if (!class_exists("stcontroller_3_0")) {
    class stcontroller_3_0 {
        protected static $_instance = null;
        public $view = null;
        public $config = array();
        public $layout = "default";
        public $actionName = "index";
        public $isAjax = 0;
        public $pluginPage = "index";
        public $option = "";
        public $views = array();
        public function __construct($config) {
            $this->setConfig($config);
            $this->actionName = (isset($config['current_action'])?$config['current_action'] :'');
            $this->layout = $this->config['default_layout'];
            $this->isAjax = false;
            $this->view = new stview_3_0 ();
            $this->functions = new stfunctions_3_0($this->config);
            $this->view->config = $this->config;
            $this->option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
        }
        public function __destruct() {
            
        }
        public function add_admin() {
            if (is_admin()) {
                $this->execute();
            } else {
                if (isset($_REQUEST['page'])) {
                    if ($_REQUEST['page'] == $this->config['plugin_name']) {
                        $this->execute();
                    }
                } else {
                    $this->execute();
                }
            }
        }
        public function ajax_action() {
            $this->isAjax = true;
            $this->execute();
            die();
        }
        public static function getInstance() {
            if (null === self::$_instance) {
                self::$_instance = new self ();
            }
            return self::$_instance;
        }
        public function setConfig($config) {
            $this->config = $config;
            if ($this->actionName == "index") {
                $this->actionName = $config['default_action'];
            }
            $this->config['backendCss'] = $this->config['pluginPath'] . 'css/';
            $this->config['backendScript'] = $this->config['pluginPath'] . 'js/';
            $this->config['imagesUrl'] = $this->config['pluginPath'] . 'images/';
        }
        public function execute() {
            $moption = get_option('page_fbar_settings');
            $mconfig = $this->config;
            if (is_admin() || in_array($this->actionName, array_keys($this->views))) {
                if (is_callable(array($this, $this->actionName)) == false) {
                    die("<br />Action not found: <b>Class: " . $this->config['plugin_name'] . "Controller, Action: " . $this->actionName . "</b> in " . __FILE__);
                }
                $this->registerTabMenu();
                $actionNameCall = $this->actionName;
                $this->$actionNameCall();
                $data = $this->view->getData();
                $data ['current_action'] = $this->actionName;
                $data ['functions'] = $this->functions;
                $data ['config'] = $this->config;
                $layoutHeader = get_include_contents($this->config['pluginDir'] . "/views/layout/header.php", $data);
                $layoutFooter = get_include_contents($this->config['pluginDir'] . "/views/layout/footer.php", $data);
                $output = get_include_contents($this->config['pluginDir'] . "/views/{$this->actionName}.php", $data);
                if ($this->isAjax) {
                    if (is_admin())
                        echo $layoutHeader . $output . $layoutFooter;
                    else
                        echo $output;
                } else {
                    $data = $this->view->getData();
                    $data ['content'] = $layoutHeader;
                    $data ['content'] .= $output;
                    $data ['content'] .= $layoutFooter;
                    $data ['config'] = $this->config;
                    $data ['tabmenus'] = $this->tabMenu;
                    $data ['current_action'] = $this->actionName;
                    $data ['option'] = $this->option;
                    $data ['functions'] = $this->functions;
                    if (is_admin()) {
                        $layout = get_include_contents($this->config['pluginDir'] . "/views/layout/{$this->layout}.php", $data);
                        echo $layout;
                    } else {
                        $this->layout = $this->views[$this->actionName];
                        if (empty($this->layout))
                            $this->layout = $this->config['default_theme_layout'];
                        $layout = get_include_contents($this->config['pluginDir'] . "/views/layout/{$this->layout}.php", $data);
                        echo $layout;
                        exit;
                    }
                }
            }else if ('/' . $moption['slug_name'] == $mconfig['current_action']) {
                $layout = get_include_contents($this->config['pluginDir'] . "/views/layout/themesdemo.php", '');
                echo $layout;
                exit;
            }
        }
        public function loadCss() {
            //this is function import javascript
            echo '<link href="' . $this->config['frontendCss'] . 'style.css" rel="stylesheet" type="text/css" />' . "\n";
            echo '<link href="' . $this->config['frontendCss'] . 'custom.css" rel="stylesheet" type="text/css" />' . "\n";
            ;
        }
        public function loadScript() {
            //this is function import javascript
            wp_enqueue_script('jquery');
            wp_enqueue_script('validate', $this->config['frontendScript'] . 'jquery.validate.pack.js', array('jquery'));
            wp_enqueue_script($this->config['plugin_name'], $this->config['frontendScript'] . $this->config['plugin_name'] . '.js');
        }
        public function loadDefaultScript() {
            //this is function import javascript
            wp_enqueue_script('jQueryUI', $this->config['backendScript'] . 'jquery-ui-1.8.7.custom.min.js', array('jquery'), '1.8.7');
            wp_enqueue_script('validate', $this->config['backendScript'] . 'jquery.validate.pack.js', array('jquery'), '1.5.5');
            wp_enqueue_script('stCore', $this->config['backendScript'] . $this->config['plugin_name'] . '.js', array('jquery'), '1.0');
            wp_enqueue_script('ajaxupload', $this->config['backendScript'] . 'ajaxupload.js', array('jquery'), '1.0');
            wp_enqueue_script('tooltip', $this->config['backendScript'] . 'jquery.ui.tooltip.js', array('jquery'), '2.0');
            wp_enqueue_script('blockUI', $this->config['backendScript'] . 'jquery.blockUI.js', array('jquery'), '2.36');
            wp_enqueue_script('colorPicker', $this->config['backendScript'] . 'jquery.colorPicker.js', array('jquery'), '3.0');
        }
        public function loadDefaultStyle() {
            wp_register_style('jQueryUI', $this->config['backendCss'] . 'jquery-ui/jquery-ui-1.8.7.custom.css');
            wp_register_style('stStyle', $this->config['backendCss'] . 'style.css');
            wp_register_style('colorPicker', $this->config['backendCss'] . 'colorPicker.css');
            wp_print_styles('jQueryUI');
            wp_print_styles('stStyle');
            wp_print_styles('colorPicker');
        }
        public function loadSourceSubAdmin() {
            //this is function import javascript ???
            echo '<link href="' . $this->config['backendCss'] . 'custom.css" rel="stylesheet" type="text/css" />' . "\n";
            echo '<script type="text/javascript" src="' . $this->config['backendScript'] . $this->config['plugin_name'] . '.js"></script>';
        }
        public function loadAdminCss() {
            
        }
        public function loadAdminScript() {
            
        }
        public function loadAdminGlobalScript() {
            
        }
        public function loadAdminGlobalCss() {
            
        }
        public function loadmodel($modelName, $dir = '') {
            $className = $modelName . "_model";
            $dir = empty($dir) ? $this->config['pluginDir'] : $dir;
            $fileName = $dir . "models/{$modelName}.php";
            require_once $fileName;
            $obj = new $className($this->config);
            return $obj;
        }
        public function index() {
            
        }
        protected function setTabMenu($tabmenus) {
            $addons = $this->option['addons'];
            foreach ($tabmenus as $key => $value) {
                if (!$this->config['disable_tabs']) {
                    $this->config['disable_tabs'] = array();
                }
                if (!in_array($key, $this->config['disable_tabs'])) {
                    if (count($value) > 1) {
                        if (array_key_exists('plugin_name', $value)) {
                            $this->tabMenu [$key] = $value;
                        } else {
                            foreach ($value as $key_child => $value_child) {
                                if (!in_array($key_child, $this->config['disable_tabs'])) {
                                    $this->tabMenu [$key][$key_child] = $value_child;
                                }
                            }
                        }
                    } else {
                        $this->tabMenu [$key] = $value;
                    }
                }
            }
            if (isset($this->config['addOn'])&&(!empty($this->config['addOnFolder']) && count($this->config['addOn']) > 0)) {
                if (!in_array('addOn', $this->config['disable_tabs']))
                    $this->tabMenu ['addOn'] = $this->config['addOnTitle'];
            }
        }
        protected function registerTabMenu() {
            if (isset($this->config['admin_tabs'])) {
                $this->setTabMenu($this->config['admin_tabs']);
            }
        }
        public function activationPlugin() {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            if (!empty($this->config['tableOptions'])) {
                $table = $wpdb->prefix . "st_options";
                $structure = "CREATE TABLE IF NOT EXISTS $table (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `option_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
                  `option_value` longtext NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
                ";
                dbDelta($structure);
            }
        }
        public function deactivationPlugin() {
            
        }
        public function getAction($action = '', $agrs = '') {
            if ($action != '' && !is_object($action)) {
                $actionNameCall = $action;
            } else {
                $actionNameCall = $this->actionName;
            }
            if ($agrs != '')
                $this->$actionNameCall($agrs);
            else
                $this->$actionNameCall();
            $data = $this->view->getData();
            $data ['current_action'] = $actionNameCall;
            $data ['config'] = $this->config;
            $data ['current_action'] = $actionNameCall;
            $output = get_include_contents($this->config['pluginDir'] . "views/{$actionNameCall}.php", $data);
            if ($action != '' && !is_object($action)) {
                return $output;
            } else {
                echo $output;
            }
        }
        protected function setView($views) {
            foreach ($views as $view => $layout) {
                $this->views[$view] = $layout;
            }
        }
        protected function registerView() {
            
        }
        protected function updateOption($data = '', $value = '') {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if ($this->update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        protected function getOption($key = '', $user_id = 0) {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $user_id);
            if (!empty($key))
                return $option[$key];
            else
                return $option;
        }
        /*
         * This function help you get all option of current user
         * and another option but not include option of another users
         */
        public function getGeneralOptions() {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if ($this->option)
                return array_merge($option, $this->option);
            else
                return $option;
        }
        protected function updateGeneralOption($data = '', $value = '') {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if (update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        public function update_option($option, $newvalue, $user_id = 0) {
            global $wpdb;
            $option = trim($option);
            if (empty($option))
                return false;
            wp_protect_special_option($option);
            if (is_object($newvalue))
                $newvalue = wp_clone($newvalue);
            $newvalue = sanitize_option($option, $newvalue);
            $oldvalue = $this->get_option($option);
            // If the new and old values are the same, no need to update.
            if ($newvalue === $oldvalue)
                return false;
            if (empty($this->config['tableOptions'])) {
                if (false === $oldvalue)
                    return add_option($option, $newvalue);
            }else {
                $st_options_model = $this->loadmodel("st_options", $this->config['modelOptionDir']);
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $newvalue = maybe_serialize($newvalue);
                $id = $st_options_model->getID($user_id, $option);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                return $st_options_model->saveRecords($data);
            }
            $notoptions = wp_cache_get('notoptions', 'options');
            if (is_array($notoptions) && isset($notoptions[$option])) {
                unset($notoptions[$option]);
                wp_cache_set('notoptions', $notoptions, 'options');
            }
            $_newvalue = $newvalue;
            $newvalue = maybe_serialize($newvalue);
            if (!defined('WP_INSTALLING')) {
                $alloptions = wp_load_alloptions();
                if (isset($alloptions[$option])) {
                    $alloptions[$option] = $_newvalue;
                    wp_cache_set('alloptions', $alloptions, 'options');
                } else {
                    wp_cache_set($option, $_newvalue, 'options');
                }
            }
            if (empty($this->config['tableOptions'])) {
                $result = $wpdb->update($wpdb->options, array('option_value' => $newvalue), array('option_name' => $option));
            } else {
                $st_options_model = $this->loadmodel("st_options", $this->config['modelOptionDir']);
                $user_id = get_current_user_id();
                $id = $st_options_model->getId($user_id, $option);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                $result = $st_options_model->saveRecords($data);
                //$result = $wpdb->update($this->config['tableOptions'], array('user_id' => $user_id), array('option_value' => $newvalue), array('option_name' => $option));
            }
            if ($result) {
                return true;
            }
            return false;
        }
        function get_option($option, $user_id = 0, $default = false) {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where = 'option_name = %s LIMIT 1';
            } else {
                $optionTable = $this->config['tableOptions'];
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $where = "user_id = $user_id AND option_name = %s LIMIT 1";
            }
            $option = trim($option);
            if (empty($option))
                return false;
            if (defined('WP_SETUP_CONFIG'))
                return false;
            if ($user_id == get_current_user_id()) {
                $value = wp_cache_get($option, 'options');
            } else {
                $value = false;
            }
            if (false === $value) {
                $suppress = $wpdb->suppress_errors();
                $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $optionTable WHERE {$where} ", $option));
                $wpdb->suppress_errors($suppress);
                if (is_object($row)) {
                    $value = $row->option_value;
                    wp_cache_add($option, $value, 'options');
                }
                else
                    return $default;
            }
            return apply_filters('option_' . $option, maybe_unserialize($value));
        }
        function wp_load_alloptions() {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where1 = "WHERE autoload = 'yes'";
                if (!defined('WP_INSTALLING') || !is_multisite()) {
                    $alloptions = wp_cache_get('alloptions', 'options');
                }
                else
                    $alloptions = false;
            } else {
                $optionTable = $this->config['tableOptions'];
                $user_id = get_current_user_id();
                $where1 = "WHERE user_id = $user_id AND autoload = 'yes'";
                $where2 = "WHERE user_id = $user_id";
                $alloptions = false;
            }
            if (!$alloptions) {
                $suppress = $wpdb->suppress_errors();
                if (!$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where1 "))
                    $alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where2");
                $wpdb->suppress_errors($suppress);
                $alloptions = array();
                foreach ((array) $alloptions_db as $o)
                    $alloptions[$o->option_name] = $o->option_value;
                if (!defined('WP_INSTALLING') || !is_multisite())
                    wp_cache_add('alloptions', $alloptions, 'options');
            }
            //var_dump($alloptions);
            return $alloptions;
        }
        public function ajaxPostAction() {
            $save_type = $_POST['type'];
            //Uploads
            if ($save_type == 'upload') {
                $clickedID = $_POST['data']; // Acts as the name
                $filename = $_FILES[$clickedID];
                $filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']);
                $override['test_form'] = false;
                $override['action'] = 'wp_handle_upload';
                $uploaded_file = wp_handle_upload($filename, $override);
                list($width, $height) = getimagesize($uploaded_file['file']);
                if ($_POST['thumbnail_width'] != '') {
                    $max_w = $_POST['thumbnail_width'];
                    $max_h = round(($max_w * $height) / $width);
                    if (isset($uploaded_file['file'])) {
						$image = wp_get_image_editor( $uploaded_file['file'] );
						if ( ! is_wp_error( $image ) ) {
							//$image->rotate( 90 );
							$image->resize( $max_w, $max_h, array('center','center') );
							$image->save( $uploaded_file['file'] );
						}
						//echo $image;
                        //$thumbnail = image_resize($uploaded_file['file'], $max_w, $max_h, true, 'thumb');
                       
                        @$thumbnail_url = $upload_dir_url . basename($image);
                    }
                }
                if (!empty($uploaded_file['error'])) {
                    echo 'Upload Error: ' . $uploaded_file['error'] . "|*| Upload Error";
                    die;
                } else {
                    echo $uploaded_file['url'] . "|*|" . $uploaded_file['file'];
                    die;
                } // Is the Response
            } elseif ($save_type == 'image_remove') {
                $file = $_POST['file']; // Acts as the name
                $img_name = basename($file);
                $img_name = explode('.', $img_name);
                $img_name = $img_name[0];
                $file_thumb = str_replace($img_name, $img_name . '-thumb', $file);
                unlink($file);
                unlink($file_thumb);
            }
        }
    }
}
if (!class_exists("stmodel_3_0")) {
    class stmodel_3_0 {
        protected static $_instance = null;
        protected $wpdb = null;
        public $config = null;
        public $_tableName = "";
        public $_primaryKey = "";
        public $option = '';
        public function __construct($config) {
            global $wpdb;
            $this->wpdb = $wpdb;
            $this->config = $config;
            $this->functions = new stfunctions_3_0($this->config);
			if(isset($this->config['plugin_name']) && isset($this->config['option_key']))
            	$this->option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (empty($this->_primaryKey)) {
                $this->_primaryKey = 'id';
            }
        }
        public function loadmodel($modelName) {
            $className = $modelName . "_model";
            $fileName = $this->config['pluginDir'] . "models/{$modelName}.php";
            require_once $fileName;
            $obj = new $className($this->config);
            return $obj;
        }
        public static function getInstance() {
            if (null === self::$_instance) {
                self::$_instance = new self ();
            }
            return self::$_instance;
        }
        public static function getTable($tableName) {
            $table = new self ();
            $table->setTableName($tableName);
            return $table;
        }
        public function setTableName($tableName) {
            $this->_tableName = $tableName;
        }
        public function setPrimaryKey($primaryKey) {
            $this->_primaryKey = $primaryKey;
        }
        public function __call($function, $args) {
            return call_user_func_array(
                    array($this->wpdb, $function), $args
            );
        }
        public function __get($key) {
            return $this->wpdb->$key;
        }
        public function getRecords($args = '') {
            $default_args = array(
                'fields' => '*',
                'and_where' => '',
                'or_where' => '',
                'where' => '',
                'order' => '',
                'limit' => '',
                'group_by' => '',
                'custom_query' => '',
            );
            if ($args == '')
                $args = $default_args;
            $fields = isset($args['fields']) ? $args['fields'] : '*';
            $and_where = isset($args['and_where']) ? $args['and_where'] : '';
            $or_where = isset($args['or_where']) ? $args['or_where'] : '';
            $custom_where = isset($args['where'])? $args['where'] : '';
            $order = isset($args['order']) ? $args['order'] : '';
            $limit = isset($args['limit']) ? $args['limit'] : '';
            $group_by = isset($args['group_by']) ? $args['group_by'] : '';
            $custom_query = isset($args['custom_query']) ? $args['custom_query'] : '';
            if (!empty($custom_query)) {
                $query = $custom_query;
            } else {
                if ($fields != '*')
                    $fields = implode(', ', $fields);
                //
                $where = ' WHERE ';
                if ($and_where != '')
                    $where .= implode('', $and_where);
                if ($or_where != '')
                    $where .= implode('', $or_where);
                if ($custom_where != '')
                    $where .= $custom_where;
                if ($where == ' WHERE ')
                    $where = '';
                //
                if ($order != '')
                    $order = ' ORDER BY ' . implode(' ', $order);
                //
                if ($limit != '' && count($limit)>=1)
                    $limit = ' LIMIT '. implode(', ', $limit);
				else
					$limit = '';
                if ($group_by != '')
                    $group_by = ' GROUP BY ' . $group_by;
                //
                $query = "SELECT $fields
                        FROM $this->_tableName
                        $where
                        $group_by
                        $order
                        $limit
                        ";
            }
//            echo $query;
            return $this->get_results($query);
        }
        public function deleteRecords($id = '') {
            $query = "DELETE FROM $this->_tableName
                    WHERE `{$this->_primaryKey}` = $id";
            return $this->query($query);
        }
        public function saveRecords($data = '', $where = '') {
            if ($data[$this->_primaryKey]) {
                if ($where == '') {
                    $where = array($this->_primaryKey => intval($data[$this->_primaryKey]));
                }
                return $this->update($this->_tableName, $data, $where);
            } else {
                return $this->insert($this->_tableName, $data);
            }
        }
        public function countRecords($args = '') {
            $args['limit'] = array();
            $result = $this->getRecords($args);
            return count($result);
        }
        protected function updateOption($data = '', $value = '') {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if ($this->update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        protected function getOption($key = '', $user_id = 0) {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $user_id);
            if (!empty($key))
                return $option[$key];
            else
                return $option;
        }
        /*
         * This function help you get all option of current user
         * and another option but not include option of another users
         */
        public function getGeneralOptions() {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if ($this->option)
                return array_merge($option, $this->option);
            else
                return $option;
        }
        protected function updateGeneralOption($data = '', $value = '') {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if (update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        public function update_option($option, $newvalue, $user_id = 0) {
            global $wpdb;
            $option = trim($option);
            if (empty($option))
                return false;
            wp_protect_special_option($option);
            if (is_object($newvalue))
                $newvalue = wp_clone($newvalue);
            $newvalue = sanitize_option($option, $newvalue);
            $oldvalue = $this->get_option($option);
            // If the new and old values are the same, no need to update.
            if ($newvalue === $oldvalue)
                return false;
            if (empty($this->config['tableOptions'])) {
                if (false === $oldvalue)
                    return add_option($option, $newvalue);
            }else {
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $newvalue = maybe_serialize($newvalue);
                $query = "SELECT id FROM `{$this->config['tableOptions']}`
                WHERE `user_id` = {$user_id} AND `option_name` like('{$option}')
                ";
                $id = $wpdb->get_var($query);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                if ($data['id']) {
                    if ($where == '') {
                        $where = array('id' => intval($data['id']));
                    }
                    return $this->update($this->config['tableOptions'], $data, $where);
                } else {
                    return $this->insert($this->config['tableOptions'], $data);
                }
            }
            $notoptions = wp_cache_get('notoptions', 'options');
            if (is_array($notoptions) && isset($notoptions[$option])) {
                unset($notoptions[$option]);
                wp_cache_set('notoptions', $notoptions, 'options');
            }
            $_newvalue = $newvalue;
            $newvalue = maybe_serialize($newvalue);
            if (!defined('WP_INSTALLING')) {
                $alloptions = wp_load_alloptions();
                if (isset($alloptions[$option])) {
                    $alloptions[$option] = $_newvalue;
                    wp_cache_set('alloptions', $alloptions, 'options');
                } else {
                    wp_cache_set($option, $_newvalue, 'options');
                }
            }
            if (empty($this->config['tableOptions'])) {
                $result = $wpdb->update($wpdb->options, array('option_value' => $newvalue), array('option_name' => $option));
            } else {
                $user_id = get_current_user_id();
                $query = "SELECT id FROM `{$this->config['tableOptions']}`
                WHERE `user_id` = {$user_id} AND `option_name` like('{$option}')
                ";
                $id = $wpdb->get_var($query);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                if ($data['id']) {
                    if ($where == '') {
                        $where = array('id' => intval($data['id']));
                    }
                    return $this->update($this->config['tableOptions'], $data, $where);
                } else {
                    return $this->insert($this->config['tableOptions'], $data);
                }
            }
            if ($result) {
                return true;
            }
            return false;
        }
        function get_option($option, $user_id = 0, $default = false) {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where = 'option_name = %s LIMIT 1';
            } else {
                $optionTable = $this->config['tableOptions'];
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $where = "user_id = $user_id AND option_name = %s LIMIT 1";
            }
            $option = trim($option);
            if (empty($option))
                return false;
            if (defined('WP_SETUP_CONFIG'))
                return false;
            $value = wp_cache_get($option, 'options');
            if (false === $value) {
                $suppress = $wpdb->suppress_errors();
                $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $optionTable WHERE {$where} ", $option));
                $wpdb->suppress_errors($suppress);
                if (is_object($row)) {
                    $value = $row->option_value;
                    wp_cache_add($option, $value, 'options');
                }
                else
                    return $default;
            }
            return apply_filters('option_' . $option, maybe_unserialize($value));
        }
        function wp_load_alloptions() {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where1 = "WHERE autoload = 'yes'";
                if (!defined('WP_INSTALLING') || !is_multisite()) {
                    $alloptions = wp_cache_get('alloptions', 'options');
                }
                else
                    $alloptions = false;
            } else {
                $optionTable = $this->config['tableOptions'];
                $user_id = get_current_user_id();
                $where1 = "WHERE user_id = $user_id AND autoload = 'yes'";
                $where2 = "WHERE user_id = $user_id";
                $alloptions = false;
            }
            if (!$alloptions) {
                $suppress = $wpdb->suppress_errors();
                if (!$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where1 "))
                    $alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where2");
                $wpdb->suppress_errors($suppress);
                $alloptions = array();
                foreach ((array) $alloptions_db as $o)
                    $alloptions[$o->option_name] = $o->option_value;
                if (!defined('WP_INSTALLING') || !is_multisite())
                    wp_cache_add('alloptions', $alloptions, 'options');
            }
            //var_dump($alloptions);
            return $alloptions;
        }
    }
}
if (!class_exists("stview_3_0")) {
    class stview_3_0 {
        protected static $_instance = null;
        private $_data = array();
        public function __construct() {
            
        }
        public static function getInstance() {
            if (null === self::$_instance) {
                self::$_instance = new self ();
            }
            return self::$_instance;
        }
        public function getData() {
            return $this->_data;
        }
        public function __set($key, $value) {
            $this->_data [$key] = $value;
        }
        public function __get($key) {
            return $this->_data [$key];
        }
    }
}
if (!class_exists("sthook_3_0")) {
    class sthook_3_0 {
        public $option = '';
        public function __construct($config) {
            $this->config = $config;
            $fucntionName = $config['plugin_name'] . "Functions";
            $this->functions = new $fucntionName($this->config);
            //$this->config['pluginDir'] = WP_PLUGIN_DIR . '/' . $config['plugin_name'] . '/';
            $this->option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            $this->hookManage();
            $this->hookCustom();
        }
        public function loadmodel($modelName, $dir = '') {
            $className = $modelName . "_model";
            $dir = empty($dir) ? $this->config['pluginDir'] : $dir;
            $fileName = $dir . "models/{$modelName}.php";
            require_once $fileName;
            $obj = new $className($this->config);
            return $obj;
        }
        public function hookManage() {
            $method = array($this, 'loadTextdomain');
            add_action('init', $method);
            $method = array($this, 'languageStart');
            add_filter('load_textdomain_mofile', $method, 100, 2);
            $method = array($this, 'includeExtraPlugin');
            add_action($this->config['plugin_name'] . '_extra_contruction', $method);
            $method = array($this, 'actionTitle');
            add_filter('wp_title', $method, 100, 3);
        }
        public function hookCustom() {
            
        }
        public function includeExtraPlugin() {
            
        }
        //Initialize hook function
        public function loadTextdomain() {
            load_plugin_textdomain($this->config['plugin_name'], false, $this->config['plugin_name'] . '/languages');
        }
        public function languageStart($mofile, $domain) {
            global $locale;
            //$fbcontent_settings = get_option('fbcontent_settings');
            $language = $this->option['st_language'];
            if ($domain == $this->config['plugin_name']) {
                $mofile = str_replace($locale, $language, $mofile);
                return $mofile;
            }
            return $mofile;
        }
        /**
         * Prints title of action page.
         *
         * @since PluginExpert 1.0
         */
        public function actionTitle($title, $sep, $seplocation) {
            //bloginfo('name');
            if ($title == '')
                $title = get_bloginfo('name');
            return $title;
        }
        protected function updateOption($data = '', $value = '') {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if ($this->update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        protected function getOption($key = '', $user_id = 0) {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $user_id);
            if (!empty($key))
                return $option[$key];
            else
                return $option;
        }
        /*
         * This function help you get all option of current user
         * and another option but not include option of another users
         */
        public function getGeneralOptions() {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if ($this->option)
                return array_merge($option, $this->option);
            else
                return $option;
        }
        protected function updateGeneralOption($data = '', $value = '') {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if (update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        public function update_option($option, $newvalue, $user_id = 0) {
            global $wpdb;
            $option = trim($option);
            if (empty($option))
                return false;
            wp_protect_special_option($option);
            if (is_object($newvalue))
                $newvalue = wp_clone($newvalue);
            $newvalue = sanitize_option($option, $newvalue);
            $oldvalue = $this->get_option($option);
            // If the new and old values are the same, no need to update.
            if ($newvalue === $oldvalue)
                return false;
            if (empty($this->config['tableOptions'])) {
                if (false === $oldvalue)
                    return add_option($option, $newvalue);
            }else {
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $newvalue = maybe_serialize($newvalue);
                $query = "SELECT id FROM `{$this->config['tableOptions']}`
                WHERE `user_id` = {$user_id} AND `option_name` like('{$option}')
                ";
                $id = $wpdb->get_var($query);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                if ($data['id']) {
                    if ($where == '') {
                        $where = array('id' => intval($data['id']));
                    }
                    return $this->update($this->config['tableOptions'], $data, $where);
                } else {
                    return $this->insert($this->config['tableOptions'], $data);
                }
            }
            $notoptions = wp_cache_get('notoptions', 'options');
            if (is_array($notoptions) && isset($notoptions[$option])) {
                unset($notoptions[$option]);
                wp_cache_set('notoptions', $notoptions, 'options');
            }
            $_newvalue = $newvalue;
            $newvalue = maybe_serialize($newvalue);
            if (!defined('WP_INSTALLING')) {
                $alloptions = wp_load_alloptions();
                if (isset($alloptions[$option])) {
                    $alloptions[$option] = $_newvalue;
                    wp_cache_set('alloptions', $alloptions, 'options');
                } else {
                    wp_cache_set($option, $_newvalue, 'options');
                }
            }
            if (empty($this->config['tableOptions'])) {
                $result = $wpdb->update($wpdb->options, array('option_value' => $newvalue), array('option_name' => $option));
            } else {
                $user_id = get_current_user_id();
                $query = "SELECT id FROM `{$this->config['tableOptions']}`
                WHERE `user_id` = {$user_id} AND `option_name` like('{$option}')
                ";
                $id = $wpdb->get_var($query);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                if ($data['id']) {
                    if ($where == '') {
                        $where = array('id' => intval($data['id']));
                    }
                    return $this->update($this->config['tableOptions'], $data, $where);
                } else {
                    return $this->insert($this->config['tableOptions'], $data);
                }
            }
            if ($result) {
                return true;
            }
            return false;
        }
        function get_option($option, $user_id = 0, $default = false) {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where = 'option_name = %s LIMIT 1';
            } else {
                $optionTable = $this->config['tableOptions'];
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $where = "user_id = $user_id AND option_name = %s LIMIT 1";
            }
            $option = trim($option);
            if (empty($option))
                return false;
            if (defined('WP_SETUP_CONFIG'))
                return false;
            $value = wp_cache_get($option, 'options');
            if (false === $value) {
                $suppress = $wpdb->suppress_errors();
                $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $optionTable WHERE {$where} ", $option));
                $wpdb->suppress_errors($suppress);
                if (is_object($row)) {
                    $value = $row->option_value;
                    wp_cache_add($option, $value, 'options');
                }
                else
                    return $default;
            }
            return apply_filters('option_' . $option, maybe_unserialize($value));
        }
        function wp_load_alloptions() {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where1 = "WHERE autoload = 'yes'";
                if (!defined('WP_INSTALLING') || !is_multisite()) {
                    $alloptions = wp_cache_get('alloptions', 'options');
                }
                else
                    $alloptions = false;
            } else {
                $optionTable = $this->config['tableOptions'];
                $user_id = get_current_user_id();
                $where1 = "WHERE user_id = $user_id AND autoload = 'yes'";
                $where2 = "WHERE user_id = $user_id";
                $alloptions = false;
            }
            if (!$alloptions) {
                $suppress = $wpdb->suppress_errors();
                if (!$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where1 "))
                    $alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where2");
                $wpdb->suppress_errors($suppress);
                $alloptions = array();
                foreach ((array) $alloptions_db as $o)
                    $alloptions[$o->option_name] = $o->option_value;
                if (!defined('WP_INSTALLING') || !is_multisite())
                    wp_cache_add('alloptions', $alloptions, 'options');
            }
            //var_dump($alloptions);
            return $alloptions;
        }
    }
}
if (!class_exists("stfunctions_3_0")) {
    class stfunctions_3_0 {
        public $option = '';
        public function __construct($config) {
            $this->config = $config;
			if(isset($this->config['plugin_name']) && isset($this->config['option_key']))
            $this->option = $this->getOption($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
        }
        public function loadmodel($modelName, $dir = '') {
            $className = $modelName . "_model";
            $dir = empty($dir) ? $this->config['pluginDir'] : $dir;
            $fileName = $dir . "models/{$modelName}.php";
            require_once $fileName;
            $obj = new $className($this->config);
            return $obj;
        }
        public static function getClientIp() {
            $ip;
            if (getenv("HTTP_CLIENT_IP"))
                $ip = getenv("HTTP_CLIENT_IP");
            else if (getenv("HTTP_X_FORWARDED_FOR"))
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            else if (getenv("REMOTE_ADDR"))
                $ip = getenv("REMOTE_ADDR");
            else
                $ip = "UNKNOWN";
            return $ip;
        }
        public static function getBrowerType() {
            // Declare known browsers to look for
            $known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape',
                'konqueror', 'gecko');
            // Clean up agent and build regex that matches phrases for known browsers
            // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
            // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
            $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
            $pattern = '#(?<browser>' . join('|', $known) .
                    ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';
            // Find all phrases (or return empty array if none found)
            if (!preg_match_all($pattern, $agent, $matches))
                return array();
            // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
            // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
            // in the UA).  That's usually the most correct.
            $i = count($matches['browser']) - 1;
            return array('browser' => $matches['browser'][$i], 'version' => $matches['version'][$i]);
        }
        public function sendMessage($message, $action = '', $data = array()) {
            if (!empty($action)) {
                $html = 'var data_post = new Array();';
                if (count($data) > 0 && is_array($data)) {
                    foreach ($data as $key => $value) {
                        $html .= 'data_post["' . $key . '"] = "' . $value . '";';
                    }
                }
                return "<script type='text/javascript'>"
                        . 'jQuery(document).ready(function(){'
                        . $html
                        . $this->config['plugin_name'] . ".loadPage('{$action}', data_post, '{$message}');"
                        . '});'
                        . "</script>"
                ;
            } else {
                return "<script type='text/javascript'>"
                        . 'jQuery(document).ready(function(){'
                        . $this->config['plugin_name'] . ".sendMessage('$message');"
                        . '});'
                        . "</script>"
                ;
            }
        }
        public function Redirect($location) {
            return "<script type='text/javascript'>"
                    . "window.location = '$location';"
                    . "</script>"
            ;
        }
        public function pagination($start = NULL, $limit = NULL, $total = NULL, $cur_page = 1, $action = '') {
            if ($total > $limit) {
                $start = $start ? $start : 0;
                $limit = $limit ? $limit : $this->config['per_page'];
                $total = $total ? $total : 0;
                $total_page = ceil($total / $limit);
                $page_num = $this->config['page_num'];
                $cur_step = ceil($cur_page / $page_num);
                $max_page = ($cur_step * $page_num < $total_page) ? $cur_step * $page_num : $total_page;
                $min_page = ($cur_step - 1) * $page_num;
                $min_page = $min_page == 0 ? 1 : $min_page;
                if ($cur_page == $max_page) {
                    $min_page += 1;
                    $max_page = ($max_page + 1 < $total_page) ? ($max_page + 1) : $total_page;
                }
                $html = '<div id="st_pagination">';
                $html .= '<ul>';
                if ($cur_page > 1) {
                    $html .= "<li><a href='javascript:void(0)' onclick='{$this->config['plugin_name']}.paging(0, \"$action\")' >&laquo;</a></li>";
                    $html .= "<li><a href='javascript:void(0)' onclick='{$this->config['plugin_name']}.paging(" . ($cur_page - 1) . ", \"$action\")' >&lsaquo;</a></li>";
                }
                for ($i = $min_page; $i <= $max_page; $i++) {
                    if ($i == 0 || $i == $cur_page)
                        $current = 'class="current"';
                    else
                        $current = '';
                    $html .= "<li $current><a href='javascript:void(0)' onclick='{$this->config['plugin_name']}.paging($i, \"$action\")' >$i</a></li>";
                }
                if ($cur_page < $total_page) {
                    $html .= "<li><a href='javascript:void(0)' onclick='{$this->config['plugin_name']}.paging(" . ($cur_page + 1) . ", \"$action\")' >&rsaquo;</a></li>";
                    $html .= "<li><a href='javascript:void(0)' onclick='{$this->config['plugin_name']}.paging($total_page, \"$action\")' >&raquo;</a></li>";
                }
                $html .= '</ul>';
                $html .= '</div>';
                return $html;
            } else {
                return;
            }
        }
        public function getAddOns() {
            if ($this->config['subMenu'])
                $option = get_option($this->config['parentPage'] . '_' . $this->config['parentOptionKey'] . '_settings');
            else
                $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            $addons = $option['addons'];
            $addOnPerm = $this->getAddOnPermission($this->config);
            $user_id = get_current_user_id();
            $user = get_user_by('id', $user_id);
            if ($this->config['addOnPerm'] && is_array($addOnPerm[$user_id]))
                $addOnPermCheck = in_array($config['plugin_name'], $addOnPerm[$user_id]);
            else
                $addOnPermCheck = true;
            $results = array();
            if (count($addons) > 0) {
                foreach ($addons as $key => $value) {
                    if ((!empty($key) && $value == 1 && $addOnPermCheck) || ($user->user_login == $this->config['userAdminName'] && $value == 1)) {
                        $results[$key] = $value;
                    }
                }
            }
            return $results;
        }
        public function getAddOnPermission() {
            require_once(ABSPATH . 'wp-includes/pluggable.php');
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            return $option['addOnPerm'];
        }
        public function getErrorMessage($wp_error) {
            if ($wp_error->get_error_code()) {
                $messages = '';
                foreach ($wp_error->get_error_codes() as $code) {
                    foreach ($wp_error->get_error_messages($code) as $error) {
                        $messages .= '	' . $error . "<br />\n";
                    }
                }
                $arg = array(
                    'status' => 'error',
                    'message' => __($messages, $config['plugin_name'])
                );
                return $arg;
            }
        }
        public function generateSubTabs($config) {
            $addons = $this->getAddOns();
            if (count($addons) > 0) {
                $current_page = $_REQUEST['page'];
                if ($config['subMenu']) {
                    $parentName = str_replace('st', '', $config['parentPage']);
                    $parentPage = $config['parentPage'];
                } else {
                    $parentName = str_replace('st', '', $config['plugin_name']);
                    $parentPage = $config['plugin_name'];
                }
                $class = $current_page == $parentPage ? "class='curr_sub'" : "";
                echo "<li {$class}><a href='" . admin_url("/admin.php?page={$parentPage}") . "'><span>Dashboard</span></a></li>";
                foreach ($addons as $key => $value) {
                    if (!empty($key)) {
                        $name = str_replace('st', '', $key);
                        $name = $name == 'ToolBar' ? str_replace($name, 'Web Toolbar', $name) : $name;
                        $class = $current_page == $key ? "class='curr_sub'" : "";
                        echo "<li {$class}><a href='" . admin_url("/admin.php?page={$key}") . "'><span>{$name}</span></a></li>";
                    }
                }
            }
        }
        protected function updateOption($data = '', $value = '') {
            $option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if ($this->update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        protected function getOption($key = '', $user_id = 0) {
            if(isset($this->config['plugin_name']) && isset($this->config['option_key']))
				$option = $this->get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $user_id);
			else 
				$option='';
            if (!empty($key))
                return $option[$key];
            else
                return $option;
        }
        /*
         * This function help you get all option of current user
         * and another option but not include option of another users
         */
        public function getGeneralOptions() {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if ($this->option)
                return array_merge($option, $this->option);
            else
                return $option;
        }
        protected function updateGeneralOption($data = '', $value = '') {
            $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $option[$key] = $value;
                }
            } else {
                $option[$data] = $value;
            }
            if (update_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings', $option)) {
                return true;
            } else {
                return false;
            }
        }
        public function update_option($option, $newvalue, $user_id = 0) {
            global $wpdb;
            $option = trim($option);
            if (empty($option))
                return false;
            wp_protect_special_option($option);
            if (is_object($newvalue))
                $newvalue = wp_clone($newvalue);
            $newvalue = sanitize_option($option, $newvalue);
            $oldvalue = $this->get_option($option);
            // If the new and old values are the same, no need to update.
            if ($newvalue === $oldvalue)
                return false;
            if (empty($this->config['tableOptions'])) {
                if (false === $oldvalue)
                    return add_option($option, $newvalue);
            }else {
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $newvalue = maybe_serialize($newvalue);
                $query = "SELECT id FROM `{$this->config['tableOptions']}`
                WHERE `user_id` = {$user_id} AND `option_name` like('{$option}')
                ";
                $id = $wpdb->get_var($query);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                if ($data['id']) {
                    if ($where == '') {
                        $where = array('id' => intval($data['id']));
                    }
                    return $this->update($this->config['tableOptions'], $data, $where);
                } else {
                    return $this->insert($this->config['tableOptions'], $data);
                }
            }
            $notoptions = wp_cache_get('notoptions', 'options');
            if (is_array($notoptions) && isset($notoptions[$option])) {
                unset($notoptions[$option]);
                wp_cache_set('notoptions', $notoptions, 'options');
            }
            $_newvalue = $newvalue;
            $newvalue = maybe_serialize($newvalue);
            if (!defined('WP_INSTALLING')) {
                $alloptions = wp_load_alloptions();
                if (isset($alloptions[$option])) {
                    $alloptions[$option] = $_newvalue;
                    wp_cache_set('alloptions', $alloptions, 'options');
                } else {
                    wp_cache_set($option, $_newvalue, 'options');
                }
            }
            if (empty($this->config['tableOptions'])) {
                $result = $wpdb->update($wpdb->options, array('option_value' => $newvalue), array('option_name' => $option));
            } else {
                $user_id = get_current_user_id();
                $query = "SELECT id FROM `{$this->config['tableOptions']}`
                WHERE `user_id` = {$user_id} AND `option_name` like('{$option}')
                ";
                $id = $wpdb->get_var($query);
                $data = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'option_name' => $option,
                    'option_value' => $newvalue,
                );
                if ($data['id']) {
                    if ($where == '') {
                        $where = array('id' => intval($data['id']));
                    }
                    return $this->update($this->config['tableOptions'], $data, $where);
                } else {
                    return $this->insert($this->config['tableOptions'], $data);
                }
            }
            if ($result) {
                return true;
            }
            return false;
        }
        function get_option($option, $user_id = 0, $default = false) {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where = 'option_name = %s LIMIT 1';
            } else {
                $optionTable = $this->config['tableOptions'];
                if ($user_id == 0)
                    $user_id = get_current_user_id();
                $where = "user_id = $user_id AND option_name = %s LIMIT 1";
            }
            $option = trim($option);
            if (empty($option))
                return false;
            if (defined('WP_SETUP_CONFIG'))
                return false;
            $value = wp_cache_get($option, 'options');
            if (false === $value) {
                $suppress = $wpdb->suppress_errors();
                $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $optionTable WHERE {$where} ", $option));
                $wpdb->suppress_errors($suppress);
                if (is_object($row)) {
                    $value = $row->option_value;
                    wp_cache_add($option, $value, 'options');
                }
                else
                    return $default;
            }
            return apply_filters('option_' . $option, maybe_unserialize($value));
        }
        function wp_load_alloptions() {
            global $wpdb;
            if (empty($this->config['tableOptions'])) {
                $optionTable = $wpdb->options;
                $where1 = "WHERE autoload = 'yes'";
                if (!defined('WP_INSTALLING') || !is_multisite()) {
                    $alloptions = wp_cache_get('alloptions', 'options');
                }
                else
                    $alloptions = false;
            } else {
                $optionTable = $this->config['tableOptions'];
                $user_id = get_current_user_id();
                $where1 = "WHERE user_id = $user_id AND autoload = 'yes'";
                $where2 = "WHERE user_id = $user_id";
                $alloptions = false;
            }
            if (!$alloptions) {
                $suppress = $wpdb->suppress_errors();
                if (!$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where1 "))
                    $alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $optionTable $where2");
                $wpdb->suppress_errors($suppress);
                $alloptions = array();
                foreach ((array) $alloptions_db as $o)
                    $alloptions[$o->option_name] = $o->option_value;
                if (!defined('WP_INSTALLING') || !is_multisite())
                    wp_cache_add('alloptions', $alloptions, 'options');
            }
            //var_dump($alloptions);
            return $alloptions;
        }
        public function disguise_curl($url) {
            
        }
        public function customEditor($content = 'test', $editor_id = 'content') {
            
        }
        public function dynamicThumb($args) {
            
        }
    }
}
if (!function_exists("get_include_contents")) {
    function get_include_contents($filename, $data = '') {
        if (is_file($filename)) {
            if (is_array($data)) {
                extract($data);
            }
            ob_start();
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }
}
if (!function_exists("add_include_path")) {
    function add_include_path($path) {
        foreach (func_get_args() AS $path) {
            if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir')) {
                trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
                continue;
            }
            $paths = explode(PATH_SEPARATOR, get_include_path());
            if (array_search($path, $paths) === false)
                array_push($paths, $path);
            set_include_path(implode(PATH_SEPARATOR, $paths));
        }
    }
}
if (!function_exists("remove_include_path")) {
    function remove_include_path($path) {
        foreach (func_get_args() AS $path) {
            $paths = explode(PATH_SEPARATOR, get_include_path());
            if (($k = array_search($path, $paths)) !== false)
                unset($paths[$k]);
            else
                continue;
            if (!count($paths)) {
                trigger_error("Include path '{$path}' can not be removed because it is the only", E_USER_NOTICE);
                continue;
            }
            set_include_path(implode(PATH_SEPARATOR, $paths));
        }
    }
}
if (!function_exists('get_called_class')) {
    class class_tools {
        static $i = 0;
        static $fl = null;
        static function get_called_class() {
            $bt = debug_backtrace();
            if (self::$fl == $bt[2]['file'] . $bt[2]['line']) {
                self::$i++;
            } else {
                self::$i = 0;
                self::$fl = $bt[2]['file'] . $bt[2]['line'];
            }
            $lines = file($bt[2]['file']);
            preg_match_all('/([a-zA-Z0-9\_]+)::' . $bt[2]['function'] . '/', $lines[$bt[2]['line'] - 1], $matches);
            return $matches[1][self::$i];
        }
    }
    function get_called_class() {
        return class_tools::get_called_class();
    }
}
if (!function_exists('get_core')) {
    function get_core($cipherStream, $private) {
        $cipherStreamArray = explode(":", $cipherStream);
        unset($cipherStream);
        $cipherText = $cipherStreamArray[0];
        $public = $cipherStreamArray[1];
        unset($cipherStreamArray);
        $cipherTextArray = array();
        for ($i = 0; $i < strlen($cipherText); $i+=2)
            array_push($cipherTextArray, substr($cipherText, $i, 2));
        unset($cipherText);
        $shiftArray = array();
        for ($i = 0; $i < ceil(sizeof($cipherTextArray) / 40); $i++)
            array_push($shiftArray, sha1($private . $i . $public));
        unset($private);
        unset($public);
        $plainChar = null;
        $plainTextArray = array();
        for ($i = 0; $i < sizeof($cipherTextArray); $i++) {
            $plainChar = hexdec($cipherTextArray[$i]) - ord($shiftArray[$i]);
            $plainChar -= floor($plainChar / 255) * 255;
            $plainTextArray[$i] = chr($plainChar);
        }
        unset($cipherTextArray);
        unset($shiftArray);
        unset($plainChar);
        $plainText = implode("", $plainTextArray);
        return $plainText;
    }
}
if (!function_exists('disguise_curl')) :
// disguises the curl using fake headers and a fake user agent.
    function disguise_curl($url) {
        $curl = curl_init();
        // Setup headers - I used the same headers from Firefox version 2.0.0.6
        // below was split up because php.net said the line was too long. :/
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank.
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($curl); // execute the curl command
        curl_close($curl); // close the connection
        return $html; // and finally, return $html
    }
endif;
if (!function_exists('st_get_domain')) :
// disguises the curl using fake headers and a fake user agent.
    function st_get_domain() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        preg_match('@^(?:http://)?([^/]+)@i', $pageURL, $matches);
        $host = $matches[1];
        preg_match('/[^.]+\.[^.]+$/', $host, $matches);
        return 'localhost'; //$matches[0];
    }
endif;
if (!class_exists("stApiClient")) {
    class stApiClient {
        protected $_privateKey = "";
        protected $_email = "";
        public $db = null;
        protected $_clientInfo = array();
        protected $_params = array();
        protected $_token = "";
        protected $_serverUrl = "http://localhost/apiserver/index.php";
        public $opts = array(
            CURLOPT_HEADER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE
        );
        public function __construct($email, $privateKey) {
            $this->_privateKey = $privateKey;
            $this->_email = $email;
        }
        public function getToken($params) {
            $newArray = sort($params);
            $str = "";
            foreach ($params as $k => $value) {
                $str .= $value;
            }
            $this->_token = md5($str . $this->_privateKey);
            return $this->_token;
        }
        public function getURL($params) {
            return $this->_serverUrl . "&token=" . $this->getToken($params);
        }
        public function getRemoteData($params) {
            $url = $this->getURL($params);
//            var_dump($this->post($url, $params));
            return (array) json_decode($this->post($url, $params));
        }
        public function setEmail($email) {
            $this->_email = $email;
        }
        public function setPrivateKey($privateKey) {
            $this->_privateKey = $privateKey;
        }
        public function debug($data) {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            die;
        }
        function r($ch, $opt) {
            # assign user's options
            foreach ($opt as $k => $v) {
                $this->opts[$k] = $v;
            }
            curl_setopt_array($ch, $this->opts);
            curl_exec($ch);
            $r = curl_exec($ch);
            curl_close($ch);
            return $r;
        }
        function get($url = '', $opt = array()) {
            $ch = curl_init($url);
            return $this->r($ch, $opt);
        }
        function post($url = '', $data = array(), $opt = array()) {
            # set POST options
            $this->opts[CURLOPT_POST] = TRUE;
            $this->opts[CURLOPT_POSTFIELDS] = $data;
            # create cURL resource
            $ch = curl_init($url);
            return $this->r($ch, $opt);
        }
        public function setServerUrl($url) {
            $this->_serverUrl = $url;
        }
        /**
          start custom function
         */
        public function getCore($param) {
            $params = array('function' => 'getLicense', 'email' => $this->_email);
            foreach ($param as $key => $value) {
                $params[$key] = $value;
            }
            return $this->getRemoteData($params);
        }
    }
}
class fbar {
    public $config = array();
    public $addOn = array();
    public function __construct($config) {
        $this->config = $this->setDefaultAction($config);
        if (empty($this->config['pluginDir']))
            $this->config['pluginDir'] = WP_PLUGIN_DIR . '/' . $config['plugin_name'] . '/';
        if (empty($this->config['pluginPath']))
            $this->config['pluginPath'] = WP_PLUGIN_URL . '/' . $config['plugin_name'] . '/';
        $this->getAddOnConfig();
        if (!is_admin()) {
            $method = array($this, 'frontEndExcute');
            add_action('after_setup_theme', $method);
        } else {
            $method = array($this, 'backEndExcute');
            add_action('admin_menu', $method);
        }
        $this->loadLib();
    }
    public function createAddOn() {
        $addOnPerm = $this->getAddOnPermission();
        $user_id = get_current_user_id();
        $user = get_user_by('id', $user_id);
        if ($this->config['addOnPerm'] && is_array($addOnPerm[$user_id]))
            $addOnPermCheck = in_array($config['plugin_name'], $addOnPerm[$user_id]);
        else
            $addOnPermCheck = true;
        if (count($this->config['addOn']) > 0) {
            $this->haveSubMenu = false;
            foreach ($this->config['addOn'] as $config) {
                $option = $this->option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
                if ($option['addons'][$config['plugin_name']] && ($addOnPermCheck || $user->user_login == $this->config['userAdminName'])) {
                    $fileDir = $this->config['fileDir'];
                    require_once $fileDir . DIRECTORY_SEPARATOR . $this->config['addOnFolder'] . DIRECTORY_SEPARATOR . $config['plugin_name'] . DIRECTORY_SEPARATOR . $config['plugin_name'] . '.php';
                    $config['pluginDir'] = $this->config['pluginDir'] . $this->config['addOnFolder'] . '/' . $config['plugin_name'] . '/';
                    $config['fileDir'] = $this->config['fileDir'] . DIRECTORY_SEPARATOR . $this->config['addOnFolder'] . DIRECTORY_SEPARATOR . $config['plugin_name'];
                    $config['pluginPath'] = $this->config['pluginPath'] . $this->config['addOnFolder'] . '/' . $config['plugin_name'] . '/';
                    $config['subMenu'] = true;
                    $config['parentPage'] = $this->config['plugin_name'];
                    $config['parentOptionKey'] = $this->config['option_key'];
                    $config['tableOptions'] = $this->config['tableOptions'];
                    $config['parentDir'] = $this->config['pluginDir'];
                    $config['userAdminName'] = $this->config['userAdminName'];
                    $config = $this->setDefaultAction($config);
                    $pluginClass = new $config['plugin_name']($config);
                    if ($_REQUEST['active']) {
                        $pluginClass->activationPlugin();
                    }
                    $this->haveSubMenu = true;
                }
            }
        }
    }
    public function setDefaultAction($config) {
        if (is_array($config['disable_tabs']) && in_array($config['default_action'], $config['disable_tabs'])) {
            $diff_array = array_diff(array_keys($config['admin_tabs']), $config['disable_tabs']);
            $config['default_action'] = array_shift($diff_array);
        }
        return $config;
    }
    public function getAddOnConfig() {
        if (!empty($this->config['addOnFolder'])) {
            $addOnFolder = $this->config['pluginDir'] . $this->config['addOnFolder'];
            $folder_array = @scandir($addOnFolder);
            if ($folder_array) {
                foreach ($folder_array as $folder) {
                    if ($folder[0] != '.') {
                        $config_tmp = include $addOnFolder . '/' . $folder . '/config.php';
                        $this->config['addOn'][] = $config_tmp;
                    }
                }
            }
        }
    }
    public function frontEndExcute() {
        // $this->addFrontendScript();
        if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $this->config['plugin_name'] && $_REQUEST ['ajax'] == 1)) {
            $this->ajaxAction();
        } else {
            $this->excute();
        }
    }
    public function addFrontendScript() {
        $config = $this->config;
        $controllerName = $config['plugin_name'] . "Controller";
        $controllerObject = new $controllerName($config);
        $method = array($controllerObject, 'loadCss');
        add_action("wp_print_styles", $method);
        $method = array($controllerObject, 'loadScript');
        add_action("wp_print_scripts", $method);
    }
    public function backEndExcute() {
        $icon = WP_PLUGIN_URL . '/' . $this->config['plugin_name'] . '/images/favicon2.ico';
        $method = array($this, 'excute');
        if (isset($this->config['addOn'])&&(count($this->config['addOn']) > 0 && $this->haveSubMenu) ){
            add_submenu_page($this->config['plugin_name'], $this->config['page_title'], $this->config['plugin_menu_title'], $this->config['permission_capabilitie'], $this->config['plugin_name'], $method);
        }
        if (isset($this->config['subMenu'])&&$this->config['subMenu']) {
            $this->pluginPage = add_submenu_page($this->config['parentPage'], $this->config['page_title'], $this->config['plugin_menu_title'], $this->config['permission_capabilitie'], $this->config['plugin_name'], $method);
        } else {
            $this->pluginPage = add_menu_page($this->config['page_title'], $this->config['plugin_menu_title'], $this->config['permission_capabilitie'], $this->config['plugin_name'], $method, $icon);
        }
        $this->addBackendScript();
        if (isset($_REQUEST['page'])&&($_REQUEST['page'] == $this->config['plugin_name'])) {
            if (isset($_REQUEST['ajax'])&&($_REQUEST ['ajax'] == 1)) {
                $this->ajaxAction();
            }
        }
    }
    public function addBackendScript() {
        $config = $this->config;
        $controllerName = $config['plugin_name'] . "Controller";
        $controllerObject = new $controllerName($config);
        //load default resource
        $method = array($controllerObject, 'loadDefaultScript');
        add_action("admin_print_scripts-$this->pluginPage", $method);
        $method = array($controllerObject, 'loadDefaultStyle');
        add_action("admin_print_styles-$this->pluginPage", $method);
        //load custom resource
        $method = array($controllerObject, 'loadAdminScript');
        add_action("admin_print_scripts-$this->pluginPage", $method);
        $method = array($controllerObject, 'loadAdminCss');
        add_action("admin_print_styles-$this->pluginPage", $method);
        //load global resource
        $method = array($controllerObject, 'loadAdminGlobalScript');
        add_action("admin_print_scripts", $method, 100);
        $method = array($controllerObject, 'loadAdminGlobalCss');
        add_action("admin_print_styles", $method, 100);
        unset($controllerObject);
    }
    public function loadLib() {
        $fileDir = $this->config['fileDir'];
        require_once $fileDir . '/controller.php';
    }
    public function excute() {
        $config = $this->config;
        $config['current_action'] = $this->getAction();
        $controllerName = $config['plugin_name'] . "Controller";
        $controllerObject = new $controllerName($config);
        $controllerObject->registerView();
        $controllerObject->add_admin();
        do_action($config['plugin_name'] . '_extra_contruction');
    }
    public function ajaxAction() {
        $config = $this->config;
        $config['current_action'] = $this->getAction();
        $controllerName = $config['plugin_name'] . "Controller";
        $controllerObject = new $controllerName($config);
        $controllerObject->registerView();
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?page=', $url);
        if (count($url) > 1 && is_admin()) {
            if (isset($config['subMenu'])&&($config['subMenu'] && $url[1] != $config['plugin_name'])) {
                $controllerObject->loadSourceSubAdmin();
            }
        }
        if (!is_admin() && $_REQUEST['external']) {
            do_action('wp_head');
        }
        $controllerObject->ajax_action();
        do_action($config['plugin_name'] . '_extra_contruction');
    }
    public function activationPlugin() {
        $config = $this->config;
        $controllerName = $config['plugin_name'] . "Controller";
        $controllerObject = new $controllerName($config);
        $controllerObject->activationPlugin();
    }
    public function deactivationPlugin() {
        $config = $this->config;
        $controllerName = $config['plugin_name'] . "Controller";
        $controllerObject = new $controllerName($config);
        $controllerObject->deactivationPlugin();
    }
    public function getAction() {
        $option = get_option('page_fbar_settings');
		
		$filename = basename($_SERVER['REQUEST_URI']);
		$filename = substr($filename,0,strpos($filename,'?')?strpos($filename,'?'): strlen($filename));
		
        if (isset($_REQUEST ['action'])) {
            return $_REQUEST ['action'];
        } elseif (is_admin()) {
            return $this->config['default_action'];
        }
		elseif ($filename == 'wp-login.php'){
			return $this->config['default_action'];
		}
		elseif (get_option('permalink_structure') != '' && !is_admin()) {
			
			/*echo "<script>alert('".substr($filename,0,strpos($filename,'?'))."');</script>";*/
			$url = $_SERVER['REQUEST_URI'];
            $home_url = get_home_url('/');
            $parts = parse_url($home_url);
            /*             * * return the host domain ** */
            $url = $parts['scheme'] . '://' . $parts['host'] . $url;
            $url = str_replace($home_url, '', $url);
            if ($url == '/'.$option['slug_name']) {
                return $url != '' ? $url : false;
            } else if(substr($url, 1,  strlen($option['slug_name'])) == $option['slug_name']){ 
              return '/'.$option['slug_name']; 
            }else {
//            
                $url_tmp = explode('/', $url);
                foreach ($url_tmp as $value) {
                    if ($value != '') {
                        $title_tmp = $value;
                        break;
                    }
                }
                if (!empty($title_tmp)) {
                    return $title_tmp;
                }
            }
			
        }
        return false;
    }
    public function loadmodel($modelName) {
        $className = $modelName . "_model";
        $fileName = $this->config['pluginDir'] . "models/{$modelName}.php";
        require_once $fileName;
        $obj = new $className($this->config);
        return $obj;
    }
    public function getAddOnPermission() {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        $option = get_option($this->config['plugin_name'] . '_' . $this->config['option_key'] . '_settings');
        return $option['addOnPerm'];
    }
    //for widgets
    function register_st_widgets() {
        
    }
}
$fileDir = dirname(__FILE__);
$config = include $fileDir . '/config.php';
$subDir = explode('wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR, $fileDir);
$isSubDir = $subDir[1] == $config['plugin_name'] ? false : true;
if (!$isSubDir) {
    $config['fileDir'] = $fileDir;
    $pluginClass = new $config['plugin_name']($config);
    $method = array($pluginClass, 'activationPlugin');
    register_activation_hook(__FILE__, $method);
    $method = array($pluginClass, 'deactivationPlugin');
    register_deactivation_hook(__FILE__, $method);
}
?>