<?php
class ControllerModuleKoraki extends Controller {
    /**
     * @var string Api endpoint
     */
    private static $api_endpoint = "http://localhost:5000/api/v1.0/Notifications";

    public function order() {
        if(!$this->session->data['order_id'])
            return;

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $base = $this->config->get('config_ssl');
        } else {
            $base = $this->config->get('config_url');
        }

        $this->load->model('checkout/order');
        $this->load->model('account/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if ($order_info) {
            $first_name = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
            $last_name = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
            $address1 = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
            $address2 = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
            $city = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
            $zip = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
            $country = $order_info['payment_country'];

            $order_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
            $product_name_html = "something";
            $cart_count = count($order_products);
            if($cart_count > 0){
                $product_name_html = "<a href='" . $base . "?route=product/product&product_id=" . $order_products[0]['product_id'] . "' target='_blank'>" . html_entity_decode($order_products[0]['name'], ENT_QUOTES, 'UTF-8') . "</a>";
                if($cart_count > 1){
                    $product_name_html .= " and " . ($cart_count - 1) . " other product";
                    $product_name_html .= $cart_count != 2 ? "s" : "";
                }
            }

            $items = array();
            foreach ($order_products as $product) {
                $item = array(
                    "product_id" => $product['product_id'],
                    "product_name" => $product['name']
                );
                $items[] = $item;
            }

            $variables = array(
                "fname" => $first_name,
                "lname" => $last_name,
                "address1" => $address1,
                "address2" => $address2,
                "city" => $city,
                "zip" => $zip,
                "country" => $country,
                "country_code" => html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'),
                "items" => $items
            );

            $location = $city ? $city . ", " . $country : $country;

            $post = array(
                "variables" => json_encode($variables),
                "notificationText" => $first_name . " from " . $location . " purchased " . $product_name_html,
                "location" => $address2 . ", " . $city . ", " . $city . ", " . $country
            );

            $this->post($post);
        }
    }

    private function post($body) {
        $this->load->model('setting/setting');

        $client_id = $this->config->get('koraki_client_id');
        $client_secret = $this->config->get('koraki_client_secret');
        if(empty($client_id) || empty($client_secret)){
            return;
        }

        $auth = base64_encode($client_id . ":" . $client_secret);

        $bodyString = json_encode($body);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1,
            CURLOPT_NOSIGNAL => 1,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $bodyString,
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic " . $auth,
                "content-type: application/json"
            ),
        ));

        curl_exec($curl);
        curl_error($curl);
        curl_close($curl);
    }
}
