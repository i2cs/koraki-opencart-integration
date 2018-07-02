<?php
class Koraki {

    private $that;

    private $client_id;
    private $client_secret;
    private $api_endpoint = "https://api.koraki.io/api/v1.0/Notifications";

    /**
     * Koraki constructor.
     * @param $that
     */
    public function __construct(&$that)
    {
        $this->that = $that;

        $this->that->load->model('setting/setting');

        $this->client_id = $this->that->config->get('koraki_client_id');
        $this->client_secret = $this->that->config->get('koraki_client_secret');
    }

    /**
     * Binds Koraki widget to UI
     * @param $route
     * @param $data
     */
    public function widget(&$route, &$data) {
        $appId = $this->that->config->get("koraki_client_id");
        $status = $this->that->config->get("koraki_status");
        if(!empty($appId) && $status) {
            $data["modules"][] = "<script>window.sparkleSettings = { app_id: \"$appId\" }; !function(){function t(){var t=a.createElement(\"script\"); t.type=\"text/javascript\", t.async=!0,t.src=\"//api.koraki.io//widget/v1.0/js\"; var e=a.getElementsByTagName(\"script\")[0];e.parentNode.insertBefore(t,e)} var e=window,a=document;e.attachEvent?e.attachEvent(\"onload\",t):e.addEventListener(\"load\",t,!1)}();</script>";
        }
    }


    /**
     * Publish order add event
     */
    public function order() {
        if(!$this->that->session->data['order_id'])
            return;

        if (isset($this->that->request->server['HTTPS']) && (($this->that->request->server['HTTPS'] == 'on') || ($this->that->request->server['HTTPS'] == '1'))) {
            $base = $this->that->config->get('config_ssl');
        } else {
            $base = $this->that->config->get('config_url');
        }

        $this->that->load->model('checkout/order');
        $this->that->load->model('account/order');
        $this->that->load->model('catalog/product');
        $this->that->load->model('tool/image');

        $order_info = $this->that->model_checkout_order->getOrder($this->that->session->data['order_id']);
        if ($order_info) {
            $first_name = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
            $last_name = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
            $address1 = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
            $address2 = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
            $city = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
            $zip = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
            $country = $order_info['payment_country'];

            $order_products = $this->that->model_account_order->getOrderProducts($this->that->session->data['order_id']);
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
                $p = $this->that->model_catalog_product->getProduct($product['product_id']);
                $popup = $this->that->model_tool_image->resize($p['image'], $this->that->config->get($this->that->config->get('config_theme') . '_image_thumb_width'), $this->that->config->get($this->that->config->get('config_theme') . '_image_thumb_height'));
                $item = array(
                    "product_id" => $product['product_id'],
                    "product_name" => $product['name'],
                    "thumbnail" => $popup
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

    /**
     * Publish review add event
     */
    public function review($review_id) {
        if (isset($this->that->request->server['HTTPS']) && (($this->that->request->server['HTTPS'] == 'on') || ($this->that->request->server['HTTPS'] == '1'))) {
            $base = $this->that->config->get('config_ssl');
        } else {
            $base = $this->that->config->get('config_url');
        }

        if(empty($review_id)){
            return;
        }

        $this->that->load->model('catalog/review');

        $review = $this->that->model_catalog_review->getReview($review_id);

        if(isset($review) && !empty($review['rating']) && !empty($review['rating'] >= 3) && $review['status']==1){

            $this->that->load->model('catalog/product');
            $this->that->load->model('tool/image');

            $p = $this->that->model_catalog_product->getProduct($review['product_id']);
            $popup = $this->that->model_tool_image->resize($p['image'], $this->that->config->get($this->that->config->get('config_theme') . '_image_thumb_width'), $this->that->config->get($this->that->config->get('config_theme') . '_image_thumb_height'));

            $item = array(
                "product_id" => $p['product_id'],
                "product_name" => $p['name'],
                "thumbnail" => $popup
            );

            $variables = array(
                "number" => $review['rating'] ."/5",
                "item" => array(
                    $item
                )
            );
            $product = "<a href='" . $base . "?route=product/product&product_id=" . $p['product_id'] . "' target='_blank'>" . html_entity_decode($p['name'], ENT_QUOTES, 'UTF-8') . "</a>";

            $post = array(
                "variables" => json_encode($variables),
                "notificationText" => $review['author'] . " made a ". $review['rating'] ."/5 star rating on " . $product,
                "location" => ""
            );

            $this->post($post);
        }
    }

    private function post($body) {
        if(empty($this->client_id) || empty($this->client_secret)){
            return;
        }

        $auth = base64_encode($this->client_id . ":" . $this->client_secret);

        $bodyString = json_encode($body);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 3,
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