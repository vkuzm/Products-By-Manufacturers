<?php
	class ControllerModuleProductManufacturers extends Controller {
		protected function index($setting) {
			static $module = 0;
			
			$this->data['setting'] = $setting; 
			
			$this->language->load('module/productmanufacturers');
			
			$this->data['heading_title'] = $this->language->get('heading_title');
			
			$this->data['button_cart'] = $this->language->get('button_cart');
			
			$this->load->model('module/productmanufacturers');
			
			$this->load->model('tool/image');
			
			$this->data['products'] = array();
			
			$data = array(
			'filter_manufacturer_id' => $setting['brand'], 
			'limit'              => $setting['limit']
			);
			
			$results = $this->model_module_productmanufacturers->getManufacturerProducts($data);
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
					} else {
					$image = false;
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
					$special = false;
				}	
				
				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
					} else {
					$rating = false;
				}
				
				$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
				);
			}
			
			$this->data['module'] = $module++;
			$this->data['title'] = $setting['title'];
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/productmanufacturers.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/productmanufacturers.tpl';
				} else {
				$this->template = 'default/template/module/productmanufacturers.tpl';
			}
			
			$this->render();
		}
	}
?>