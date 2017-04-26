<?php
class fbarplugin_model extends stmodel_3_0 {
    public function __construct($config) {
        parent::__construct($config);
        $this->setTableName($this->prefix . 'st_fbar');
        $this->setPrimaryKey('id');
    }
    public function saveStplugin($post) {
        if($post['id'] == '')
            $post['sort_order'] = count($this->getRecords(array())) + 1;
        return $this->saveRecords($post);
    }
}
class fbarController extends stcontroller_3_0 {
    public function __construct($config) {
        parent::__construct($config);
    }
    public function activationPlugin() {
        parent::activationPlugin();
        // put your activation code here
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $table = $wpdb->prefix . "st_fbar";
        $structure = "CREATE TABLE IF NOT EXISTS $table (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `url` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
              `preview` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
              `type` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
              `typename` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
              `ddn` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
              `objid` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
              `sort_order` int(11) NOT NULL ,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;
            ";
        dbDelta($structure);
    }
    public function deactivationPlugin() {
        // put your deactivation code here
//        global $wpdb;
//        $table = $wpdb->prefix . "st_fbar";
//        $query = "DROP TABLE $table";
//        $wpdb->query($query);
    }
    public function registerView() {
        $views = array(
            'fbar' => 'themesdemo',
            'redirect' => 'redirect',
        );
        $this->setView($views);
    }
    public function index() {
        $option = $this->option;
        //initialize variable to view
        $this->view->option = $option;
        $this->view->headIcon = 'icon_re';
        $this->view->headTitle1 = 'General Settings';
        if (isset($_POST['data'])) {
            $data = $_POST['data'];
            $upload_data = wp_upload_dir();
            $data['stplugin_picture'] = $upload_data['url'] . '/' . basename($data['stplugin_picture']);
            if (update_option('fbar_settings', $data)) {
                $arg = array(
                    'status' => 'success',
                    'message' => __('Update option successful', $config['plugin_name'])
                );
                echo json_encode($arg);
                die;
            } else {
                $arg = array(
                    'status' => 'error',
                    'message' => __('Update option unsuccessful', $config['plugin_name'])
                );
                echo json_encode($arg);
                die;
            }
        }
        $this->view->option = get_option('fbar_settings');
    }
    public function page_setting() {
        global $config;
		$option = $this->option;
        //initialize variable to view
        $this->view->option = $option;
        $this->view->headIcon = 'icon_re';
        $this->view->headTitle1 = 'Page Settings';
        if (isset($_POST['data'])){
            $data = $_POST['data'];
            $upload_data = wp_upload_dir();
            $data['page_icon'] = $upload_data['url'] . '/' . basename($data['page_icon']);
            if (update_option('page_fbar_settings', $data)) {
                $arg = array(
                    'status' => 'success',
                    'message' => __('Update option successful', $config['plugin_name'])
                );
                echo json_encode($arg);
                die;
            } else {
                $arg = array(
                    'status' => 'error',
                    'message' => __('Update option unsuccessful', $config['plugin_name'])
                );
                echo json_encode($arg);
                die;
            }
        }
        $this->view->option = get_option('page_fbar_settings');
    }
    public function fbarplugin() {
		global $config;
		$stplugin_model = new fbarplugin_model($config);
        // short action handle
        if (isset($_REQUEST['short_action'])&&($_REQUEST['short_action'] == 'delete')) {
            if ($stplugin_model->deleteRecords($_REQUEST['id'])) {
                $arg = array(
                    'status' => 'success',
                    'message' => __('Delete successful', $config['plugin_name'])
                );
                echo $this->functions->sendMessage(json_encode($arg));
            } else {
                $arg = array(
                    'status' => 'error',
                    'message' => __('Delete unsuccessful', $config['plugin_name'])
                );
                echo $this->functions->sendMessage(json_encode($arg));
            }
        }
        $args = array();
        $cur_page = isset($_REQUEST['cur_page']) ? $_REQUEST['cur_page'] : 1;
        $limit = $this->config['per_page'];
        $start = ($cur_page - 1) * $limit;
        $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0;
        $sort_type = isset($_REQUEST['sort_type']) ? $_REQUEST['sort_type'] : 0;
        $args['order'] = array('sort_order', "ASC");
        $args['limit'] = array($start, $limit);
        $results = $stplugin_model->getRecords($args);
        $args['limit'] = array();
        $total = $stplugin_model->countRecords($args);
        $pagination = $this->functions->pagination($start, $limit, $total, $cur_page, $this->actionName);
        $columns = array(
            'objid' => __('ID', $this->config['plugin_name']),
            'url' => __('Url', $this->config['plugin_name']),
            'preview' => __('Preview', $this->config['plugin_name']),
            'typename' => __('Type', $this->config['plugin_name']),
            'download_url' => __('Download url', $this->config['plugin_name']),
            'short_action' => __('Action', $this->config['plugin_name']),
        );
        register_column_headers('stplugin_stplugin', $columns);
        //initialize variable to view
        $this->view->results = $results;
        $this->view->pagination = $pagination;
        $this->view->headIcon = 'icon_re';
        $this->view->headTitle1 = 'Sites list';
        $this->view->iconNew = 'Add site';
        $this->view->viewNew = 'fbarpluginEdit';
    }
    public function fbarpluginEdit() {
        $stplugin_model = new fbarplugin_model($config=''?$config:NULL);
        $results = '';
        if (isset($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            $args = array();
            $args['where'] = " `id` = $id";
            $results = $stplugin_model->getRecords($args);
        }
        $result = isset($results[0])?$results[0]:'';
        //initialize variable to view
        $this->view->result = $result;
        $this->view->headIcon = 'icon_re';
        $this->view->headTitle1 = 'New site';
        $this->view->headTitle2 = 'Edit site';
        // Handle post
        if (isset($_POST['data'])){
            $data = $_POST['data'];
            $upload_data = wp_upload_dir();
            $data['preview'] = $upload_data['url'] . '/' . basename($data['preview']);
            if ($stplugin_model->saveStplugin($data)) {
                $arg = array(
                    'status' => 'success',
                    'message' => __('Create successful', $config['plugin_name'])
                );
                echo $this->functions->sendMessage(json_encode($arg));
            } else {
                $arg = array(
                    'status' => 'success',
                    'message' => __('Create unsuccessful', $config['plugin_name'])
                );
                echo $this->functions->sendMessage(json_encode($arg));
            }
        }
    }
    public function fbar() {
        $option = get_option('page_fbar_settings');
        $list = explode("/", $_SERVER['REDIRECT_URL']);
        $num = count($list);
        $this->view->headIcon = 'icon_re';
        $this->view->headTitle1 = 'Menu child 1 Pages';
    }
    public function redirect() {
        
    }
    public function sortable() {
        global $wpdb;
        $tb = $wpdb->prefix . "st_fbar";
        $start = $_POST['start'] + 1;
        $new = $_POST['newp'] + 1;
        if ($new > $start) {
            // create in sql statement 
            $in_array = "(";
            for ($i = $start; $i <= $new; $i++) {
                if ($i == $new) {
                    $in_array .= "'$i')";
                } else {
                    $in_array .= "'$i',";
                }
            }
            $data = $wpdb->get_results("SELECT * FROM $tb WHERE sort_order IN $in_array ORDER BY sort_order");
            foreach ($data as $key => $row) {
                if ($key == 0) {
                    $wpdb->update($tb, array('sort_order' => $new), array('id' => $row->id));
                } else {
                    $wpdb->update($tb, array('sort_order' => $row->sort_order - 1), array('id' => $row->id));
                }
            }
        } else if ($new < $start) {
            // create in sql statement 
            $in_array = "(";
            for ($i = $new; $i <= $start; $i++) {
                if ($i == $start) {
                    $in_array .= "'$i')";
                } else {
                    $in_array .= "'$i',";
                }
            }
            $data = $wpdb->get_results("SELECT * FROM $tb WHERE sort_order IN $in_array ORDER BY sort_order");
            print_r($data);
            foreach ($data as $key => $row) {
                if ($key == count($data) - 1) {
                    $wpdb->update($tb, array('sort_order' => $new), array('id' => $row->id));
                } else {
                    $wpdb->update($tb, array('sort_order' => $row->sort_order + 1), array('id' => $row->id));
                }
            }
        }
    }
}
?>