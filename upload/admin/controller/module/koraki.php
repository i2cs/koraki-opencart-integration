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
            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }
     
        // Assign the language data for parsing it to view
        $data['heading_title'] = $this->language->get('heading_title');
     
        $data['text_edit']    = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_content_top'] = $this->language->get('text_content_top');
        $data['text_content_bottom'] = $this->language->get('text_content_bottom');      
        $data['text_column_left'] = $this->language->get('text_column_left');
        $data['text_column_right'] = $this->language->get('text_column_right');

        $data['entry_client_id'] = $this->language->get('entry_client_id');
        $data['entry_client_secret'] = $this->language->get('entry_client_secret');
        $data['entry_client_id_placeholder'] = $this->language->get('entry_client_id_placeholder');
        $data['entry_client_secret_placeholder'] = $this->language->get('entry_client_secret_placeholder');
        $data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_position'] = $this->language->get('entry_position');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
     
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
         
        // This Block returns the warning if any
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
     
        // This Block returns the error code if any
        if (isset($this->error['code'])) {
            $data['error_code'] = $this->error['code'];
        } else {
            $data['error_code'] = '';
        }     
     
        // Making of Breadcrumbs to be displayed on site
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/koraki', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
          
        $data['action'] = $this->url->link('module/koraki', 'token=' . $this->session->data['token'], 'SSL'); // URL to be directed when the save button is pressed
     
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'); // URL to be redirected when cancel button is pressed

        if (isset($this->request->post['koraki_client_id'])) {
            $data['koraki_client_id'] = $this->request->post['koraki_client_id'];
        } else {
            $data['koraki_client_id'] = $this->config->get('koraki_client_id');
        }

        if (isset($this->request->post['koraki_client_secret'])) {
            $data['koraki_client_secret'] = $this->request->post['koraki_client_secret'];
        } else {
            $data['koraki_client_secret'] = $this->config->get('koraki_client_secret');
        }

        // This block parses the status (enabled / disabled)
        if (isset($this->request->post['koraki_status'])) {
            $data['koraki_status'] = $this->request->post['koraki_status'];
        } else {
            $data['koraki_status'] = $this->config->get('koraki_status');
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/koraki.tpl', $data));

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
        $this->load->model('extension/event');
        // Register event for injecting Koraki widget
        $this->model_extension_event->addEvent('koraki.widget.push','catalog/view/common/content_bottom/before','module/koraki/widget');

        // Register events for notification generation
        $this->model_extension_event->addEvent('koraki.publish.order.create', 'catalog/controller/checkout/confirm/after', 'module/koraki/order');
        $this->model_extension_event->addEvent('koraki.publish.review.create', 'admin/model/catalog/review/editReview/before', 'module/koraki/review');
    }

    /**
     * On module uninstall
     */
    public function uninstall() {
        $this->load->model('extension/event');

        $this->model_extension_event->deleteEvent('koraki.widget.push');
        $this->model_extension_event->deleteEvent('koraki.publish.order.create');
        $this->model_extension_event->deleteEvent('koraki.publish.review.create');
    }

    /**
     * Review posted notification
     * @param $route
     * @param $review_id
     * @param $review
     */
    public function review(&$route, &$review_id, &$review) {
        $this->init();
        $this->koraki->review($route, $review_id, $review);
    }

    protected function validate() {
 
        // Block to check the user permission to manipulate the module
        if (!$this->user->hasPermission('modify', 'module/koraki')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['koraki_client_id']) {
            $this->error['client_id'] = $this->language->get('error_client_id');
        }

        if (!$this->request->post['koraki_client_secret']) {
            $this->error['client_secret'] = $this->language->get('error_client_secret');
        }
 
        // Block returns true if no error is found, else false if any error detected
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
