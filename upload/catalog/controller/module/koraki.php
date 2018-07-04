<?php
require_once(DIR_SYSTEM . "library/koraki.php");

class ControllerModuleKoraki extends Controller {

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

    public function customer(&$route, &$customer_id, &$data) {
        $this->init();
        $this->koraki->customer($customer_id, $data);
    }

    public function newsletter(&$route, &$customer_id, &$data) {
        $this->init();
        $this->koraki->newsletter($customer_id, $data);
    }
}
