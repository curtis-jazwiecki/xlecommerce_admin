<?php
	//include ('includes/application_top.php');
	
	try {
		$client = new SoapClient(null, array('location' => 'http://67.227.172.78/admin/product_feed_service.php', 
											 'uri' => 'http://global-supplier',
											 'trace' => true, 
											 'classmap' => 'ProductFeed'));
		$params = array('retailer_token' => '1234');
		$options = array('timeout' => '1000', 'trace' => true);
		$result = $client->__soapCall('GetProductFeed', $params, $options);
		//print_r($result);
	} catch (Soapfault $e){
		echo '**********<br>';
		echo $e;
		echo '**********<br>';
	}
	echo $client->__getLastResponse();
	
	/*define ('OBJECT_NAME_FEED_ID'								, 'FeedID');
	define ('OBJECT_NAME_SMALL_IMAGE_PATH'						, 'SmallImagePath');
	define ('OBJECT_NAME_MEDIUM_IMAGE_PATH'						, 'MediumImagePath');
	define ('OBJECT_NAME_LARGE_IMAGE_PATH'						, 'LargemagePath');
	define ('OBJECT_NAME_CURRENCY_CODE'							, 'CurrencyCode');
	define ('OBJECT_NAME_WEIGHT_UNIT'							, 'WeightUnit');
	define ('OBJECT_NAME_CATEGORIES'							, 'Categories');
	define ('OBJECT_NAME_CATEGORY_ID'							, 'CategoryID');
	define ('OBJECT_NAME_CATEGORY_NAME'							, 'CategoryName');
	define ('OBJECT_NAME_CATEGORY_PARENT_ID'					, 'CategoryParentID');
	define ('OBJECT_NAME_PRODUCT_OPTIONS'						, 'ProductOptions');
	define ('OBJECT_NAME_PRODUCT_OPTION_ID'						, 'ProductOptionID');
	define ('OBJECT_NAME_PRODUCT_OPTION_NAME'					, 'ProductOptionName');
	define ('OBJECT_NAME_PRODUCT_OPTION_VALUES'					, 'ProductOptionValues');
	define ('OBJECT_NAME_PRODUCTS'								, 'Products');
	
	class product_feed_to_osc{
		public $response;
		public $feed_id;
		public $small_image_path;
		public $medium_image_path;
		public $large_image_path;
		public $currency_code;
		public $weight_unit;
		public $categories;
		public $product_options;
		public $product_option_values;
		public $products;
		
		public function move_feed_to_osc(){
			try {
				$client = new SoapClient(null, array('location' => 'http://67.227.172.78/admin/product_feed_service.php', 
													 'uri' => 'http://global-supplier',
													 'trace' => true, 
													 'classmap' => 'ProductFeed'));
				$params = array('retailer_token' => '1234');
				$options = array('timeout' => '1000', 'trace' => true);
				$this->response = $client->__soapCall('GetProductFeed', $params, $options);
				//print_r($this->response);
			} catch (Soapfault $e){
				echo '**********<br>';
				echo $e;
				echo '**********<br>';
			}
			//echo $client->__getLastResponse();			
			$this->feed_id 					=& $this->response->{OBJECT_NAME_FEED_ID};
			$this->small_image_path 		=& $this->response->{OBJECT_NAME_SMALL_IMAGE_PATH};
			$this->medium_image_path 		=& $this->response->{OBJECT_NAME_MEDIUM_IMAGE_PATH};
			$this->large_image_path 		=& $this->response->{OBJECT_NAME_LARGE_IMAGE_PATH};
			$this->currency_code 			=& $this->response->{OBJECT_NAME_CURRENCY_CODE};
			$this->weight_unit 				=& $this->response->{OBJECT_NAME_WEIGHT_UNIT};
			$categoriesCol 					=& $this->response->{OBJECT_NAME_CATEGORIES};
			$productOptionsCol	 			=& $this->response->{OBJECT_NAME_PRODUCT_OPTIONS};
			$this->product_option_values 	=& $this->response->{OBJECT_NAME_PRODUCT_OPTION_VALUES};
			$this->products 				=& $this->response->{OBJECT_NAME_PRODUCTS};
			
			$temp_id = '';
			$temp_name = '';
			$temp_parent_id = '';
			foreach($categoriesCol as $category){
				$temp_id = $category->{OBJECT_NAME_CATEGORY_ID};
				$temp_name = $category->{OBJECT_NAME_CATEGORY_NAME};
				$temp_parent_id = $category->{OBJECT_NAME_CATEGORY_PARENT_ID};
				
				$temp_ref = $this->get_osc_category_reference($temp_name, 
						(empty($temp_parent_id) ? 0 : (int)$this->categories[$temp_parent_id]['osc_id']));
				
				$this->categories[$temp_id] = array('osc_id' => $temp_ref['categories_id'],
													'osc_parent_id' => $temp_ref['parent_id'],
													'name' => $temp_name);
			}
			
			foreach ($productOptionsCol as $productOption){
				$temp_id = $productOption->{OBJECT_NAME_PRODUCT_OPTION_ID};
				$temp_name = $productOption->{OBJECT_NAME_PRODUCT_OPTION_NAME};
				
				
			}
		}
		
		private function get_osc_category_reference($cat_name, $cat_parent_id){
			$resp = array();
			$new_entry = 1;
			if (empty($cat_parent_id)){
				$cat_parent_id = 0;
			}
			
			$sql = tep_db_query("select a.categories_id, b.parent_id  
								from categories_description a 
								inner join categories b on a.categories_id=b.categories_id 
								where a.categories_name='" . $tempName . "' and a.language_id='1'");
			if (tep_db_num_rows($sql)){
				while ($sql_info = tep_db_fetch_array($sql)){
					if ($cat_parent_id==$sql_info['parent_id']){
						$resp['categories_id'] = $sql_info['categories_id'];
						$resp['parent_id'] = $sql_info['parent_id'];
						$new_entry = 0;
						break;
					}
				}
			}
			
			if ($new_entry){
				$sql_data_array = array('parent_id' => $cat_parent_id,
										'banner_image' => '',
										'markup' => '0%',
										'markup_modified' => 'now()');
				tep_db_perform('categories', $sql_data_array);
			    $resp['categories_id'] = tep_db_insert_id();
			    $resp['parent_id'] = $cat_parent_id;
			    $sql_data_array = array('categories_id' => $resp['categories_id'], 
										'language_id' => '1', 
										'categories_name' => $cat_name);
				tep_db_perform('categories_description', $sql_data_array);
			}
			return $resp;
		}
	}
	$feed = new product_feed_to_osc();
	$feed->move_feed_to_osc();
	//echo $feed->feed_id;
	
	include ('includes/application_bottom.php');*/
?>