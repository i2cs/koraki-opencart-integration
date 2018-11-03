<?php
require_once(DIR_SYSTEM . "library/koraki.php");

class ControllerExtensionModuleKoraki extends Controller {
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
        $this->load->language('extension/module/koraki');
     
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
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'));
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

        $data['entry_events'] = $this->language->get('entry_events');
        $data['entry_credentials'] = $this->language->get('entry_credentials');
        $data['entry_widget'] = $this->language->get('entry_widget');
        $data['entry_client_id'] = $this->language->get('entry_client_id');
        $data['entry_client_secret'] = $this->language->get('entry_client_secret');
        $data['entry_client_id_placeholder'] = $this->language->get('entry_client_id_placeholder');
        $data['entry_client_secret_placeholder'] = $this->language->get('entry_client_secret_placeholder');
        $data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_position'] = $this->language->get('entry_position');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_checkout'] = $this->language->get('entry_checkout');
        $data['entry_registered'] = $this->language->get('entry_registered');
        $data['entry_newsletters'] = $this->language->get('entry_newsletters');
        $data['entry_review'] = $this->language->get('entry_review');
        $data['entry_credentials_help'] = $this->language->get('entry_credentials_help');
        $data['entry_events_help'] = $this->language->get('entry_events_help');
        $data['entry_widget_help'] = $this->language->get('entry_widget_help');
     
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
        
        if (isset($this->error['client_id'])) {
            $data['error_client_id'] = $this->error['client_id'];
        } else {
            $data['error_client_id'] = '';
        }
        
        if (isset($this->error['client_secret'])) {
            $data['error_client_secret'] = $this->error['client_secret'];
        } else {
            $data['error_client_secret'] = '';
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
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'),
            'separator' => ' :: '
        );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/koraki', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );
          
        $data['action'] = $this->url->link('extension/module/koraki', 'user_token=' . $this->session->data['user_token'], 'SSL'); // URL to be directed when the save button is pressed
     
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'); // URL to be redirected when cancel button is pressed

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

        if (isset($this->request->post['koraki_checkout'])) {
            $data['koraki_checkout'] = $this->request->post['koraki_checkout'];
        } else {
            $data['koraki_checkout'] = $this->config->get('koraki_checkout');
        }

        if (isset($this->request->post['koraki_registered'])) {
            $data['koraki_registered'] = $this->request->post['koraki_registered'];
        } else {
            $data['koraki_registered'] = $this->config->get('koraki_registered');
        }

        if (isset($this->request->post['koraki_newsletters'])) {
            $data['koraki_newsletters'] = $this->request->post['koraki_newsletters'];
        } else {
            $data['koraki_newsletters'] = $this->config->get('koraki_newsletters');
        }

        if (isset($this->request->post['koraki_review'])) {
            $data['koraki_review'] = $this->request->post['koraki_review'];
        } else {
            $data['koraki_review'] = $this->config->get('koraki_review');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/koraki', $data));

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
        $this->load->model('setting/event');
        // Register event for injecting Koraki widget
        $this->model_setting_event->addEvent('koraki.widget.push','catalog/view/common/content_bottom/before','extension/module/koraki/widget');

        // Register events for notification generation
        $this->model_setting_event->addEvent('koraki.publish.order.create', 'catalog/controller/checkout/confirm/after', 'extension/module/koraki/order');
        $this->model_setting_event->addEvent('koraki.publish.review.create', 'admin/model/catalog/review/editReview/after', 'extension/module/koraki/review');
        $this->model_setting_event->addEvent('koraki.publish.customer.create', 'catalog/model/account/customer/addCustomer/after', 'extension/module/koraki/customer');
        $this->model_setting_event->addEvent('koraki.publish.newsletter.create', 'catalog/model/account/customer/addCustomer/after', 'extension/module/koraki/newsletter');


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
        $this->load->model('setting/event');

        $this->model_setting_event->deleteEventByCode('koraki.widget.push');
        $this->model_setting_event->deleteEventByCode('koraki.publish.order.create');
        $this->model_setting_event->deleteEventByCode('koraki.publish.review.create');
        $this->model_setting_event->deleteEventByCode('koraki.publish.newsletter.create');
        $this->model_setting_event->deleteEventByCode('koraki.publish.customer.create');
    }

    /**
     * Review posted notification
     * @param $route
     * @param $review_id
     * @param $review
     */
    public function review(&$route, &$data) {
        $this->init();
        $this->koraki->review($data[0]);
    }

    protected function validate() {
 
        // Block to check the user permission to manipulate the module
        if (!$this->user->hasPermission('modify', 'extension/module/koraki')) {
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
