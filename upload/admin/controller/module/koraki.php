<?php
class ControllerModuleKoraki extends Controller {
    /**
     * @var array
     */
    private $error = array();
    /**
     * @var Koraki
     */
    private $koraki;
    /**
     * Index function for admin UI loader
     */
    public function index() {
        // Loading the language file of koraki
        $this->load->language('module/koraki');
     
        // Set the title of the page to the heading title in the Language file i.e., Hello World
        $this->document->setTitle($this->language->get('heading_title'));
     
        // Load the Setting Model  (All of the OpenCart Module & General Settings are saved using this Model )
        $this->load->model('setting/setting');
        // Start If: Validates and check if data is coming by save (POST) method
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            // Parse all the coming data to Setting Model to save it in database.
            $this->model_setting_setting->editSetting('koraki', $this->request->post);
     
            // To display the success text on data save
            $this->session->data['success'] = $this->language->get('text_success');
            // Redirect to the Module Listing
            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }
     
        // Assign the language data for parsing it to view
        $this->data['heading_title'] = $this->language->get('heading_title');
     
        $this->data['text_edit']    = $this->language->get('text_edit');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_content_top'] = $this->language->get('text_content_top');
        $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');      
        $this->data['text_column_left'] = $this->language->get('text_column_left');
        $this->data['text_column_right'] = $this->language->get('text_column_right');
        $this->data['entry_events'] = $this->language->get('entry_events');
        $this->data['entry_credentials'] = $this->language->get('entry_credentials');
        $this->data['entry_widget'] = $this->language->get('entry_widget');
        $this->data['entry_client_id'] = $this->language->get('entry_client_id');
        $this->data['entry_client_secret'] = $this->language->get('entry_client_secret');
        $this->data['entry_client_id_placeholder'] = $this->language->get('entry_client_id_placeholder');
        $this->data['entry_client_secret_placeholder'] = $this->language->get('entry_client_secret_placeholder');
        $this->data['entry_layout'] = $this->language->get('entry_layout');
        $this->data['entry_position'] = $this->language->get('entry_position');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_checkout'] = $this->language->get('entry_checkout');
        $this->data['entry_registered'] = $this->language->get('entry_registered');
        $this->data['entry_newsletters'] = $this->language->get('entry_newsletters');
        $this->data['entry_review'] = $this->language->get('entry_review');
        $this->data['entry_credentials_help'] = $this->language->get('entry_credentials_help');
        $this->data['entry_events_help'] = $this->language->get('entry_events_help');
        $this->data['entry_widget_help'] = $this->language->get('entry_widget_help');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_module'] = $this->language->get('button_add_module');
        $this->data['button_remove'] = $this->language->get('button_remove');
	$this->data['heading_title'] = $this->language->get('heading_title');
	$this->data['button_cancel'] = $this->language->get('button_cancel');
	$this->data['text_installed'] = $this->language->get('text_installed');
         
        // This Block returns the warning if any
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
     
        // This Block returns the error code if any
        if (isset($this->error['code'])) {
            $this->data['error_code'] = $this->error['code'];
        } else {
            $this->data['error_code'] = '';
        }     
     
        // Making of Breadcrumbs to be displayed on site
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/koraki', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
          
        $this->data['action'] = $this->url->link('module/koraki', 'token=' . $this->session->data['token'], 'SSL'); // URL to be directed when the save button is pressed
     
        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'); // URL to be redirected when cancel button is pressed
        if (isset($this->request->post['koraki_client_id'])) {
            $this->data['koraki_client_id'] = $this->request->post['koraki_client_id'];
        } else {
            $this->data['koraki_client_id'] = $this->config->get('koraki_client_id');
        }
        if (isset($this->request->post['koraki_client_secret'])) {
            $this->data['koraki_client_secret'] = $this->request->post['koraki_client_secret'];
        } else {
            $this->data['koraki_client_secret'] = $this->config->get('koraki_client_secret');
        }
        // This block parses the status (enabled / disabled)
        if (isset($this->request->post['koraki_status'])) {
            $this->data['koraki_status'] = $this->request->post['koraki_status'];
        } else {
            $this->data['koraki_status'] = $this->config->get('koraki_status');
        }
        if (isset($this->request->post['koraki_checkout'])) {
            $this->data['koraki_checkout'] = $this->request->post['koraki_checkout'];
        } else {
            $this->data['koraki_checkout'] = $this->config->get('koraki_checkout');
        }
        if (isset($this->request->post['koraki_registered'])) {
            $this->data['koraki_registered'] = $this->request->post['koraki_registered'];
        } else {
            $this->data['koraki_registered'] = $this->config->get('koraki_registered');
        }
        if (isset($this->request->post['koraki_newsletters'])) {
            $this->data['koraki_newsletters'] = $this->request->post['koraki_newsletters'];
        } else {
            $this->data['koraki_newsletters'] = $this->config->get('koraki_newsletters');
        }
        if (isset($this->request->post['koraki_review'])) {
            $this->data['koraki_review'] = $this->request->post['koraki_review'];
        } else {
            $this->data['koraki_review'] = $this->config->get('koraki_review');
        }

	$this->load->model('design/layout');

	$this->data['layouts'] = $this->model_design_layout->getLayouts();

	$this->template = 'module/koraki.tpl';
	$this->children = array(
		'common/header',
		'common/footer'
	);

	$this->response->setOutput($this->render());
    }
    /**
     * Initializes koraki class
     */
    private function init()
    {
        $this->koraki = new Koraki($this);
    }
    /**
     * On module installation
     */
    public function install() {
        $this->load->model('setting/setting');
        
        $arr = array(
            "koraki_checkout" => 1,
            "koraki_registered" => 1,
            "koraki_newsletters" => 1,
            "koraki_review" => 1,
            "koraki_status" => 1
        );
        $this->model_setting_setting->editSetting('koraki', $arr);
    }
    /**
     * On module uninstall
     */
    public function uninstall() {

    }
    /**
     * Review posted notification
     * @param $route
     * @param $review_id
     * @param $review
     */
    public function review(&$route, &$review_id) {
        $this->init();
        $this->koraki->review($review_id);
    }
    protected function validate() {
 
        // Block to check the user permission to manipulate the module
        if (!$this->user->hasPermission('modify', 'module/koraki')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->request->post['koraki_client_id']) {
            $this->error['warning'] = $this->language->get('error_client_id');
        }
        if (!$this->request->post['koraki_client_secret']) {
            $this->error['warning'] = $this->language->get('error_client_secret');
        }
 
        // Block returns true if no error is found, else false if any error detected
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
