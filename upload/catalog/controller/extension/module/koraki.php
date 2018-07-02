<?php
require_once(DIR_SYSTEM . "library/koraki.php");

class ControllerExtensionModuleKoraki extends Controller {

    /** @var Koraki */
    private $koraki;

    private function init()
    {
        $this->koraki = new Koraki($this);
    }

    public function widget(&$route, &$data) {
        $this->init();
        $this->koraki->widget($route, $data);
    }

    public function order() {
        $this->init();
        $this->koraki->order();
    }

    public function customer(&$route, &$data, &$customer_id) {
        $this->init();
        $this->koraki->customer($customer_id, $data);
    }
}
