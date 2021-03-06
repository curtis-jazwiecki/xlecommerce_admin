<?php
$cron_script='yes';
require_once('cron_application_top.php');

define ('NODE_FEED_ID', 'FeedID');
define ('NODE_SMALL_IMAGE_PATH', 'SmallImagePath');
define ('NODE_MEDIUM_IMAGE_PATH', 'MediumImagePath');
define ('NODE_LARGE_IMAGE_PATH', 'LargeImagePath');
define ('NODE_CURRENCY_CODE', 'CurrencyCode');
define ('NODE_WEIGHT_UNIT', 'WeightUnit');
define ('NODE_CATEGORIES', 'Categories');
define ('NODE_CATEGORY_ID', 'CategoryID');
//BOF:mod 05032012
define ('NODE_CATEGORY_IDS', 'CategoryIDs');
//EOF:mod 05032012
define ('NODE_CATEGORY_NAME', 'CategoryName');
define ('NODE_CATEGORY_PARENT_ID', 'CategoryParentID');
define ('NODE_PRODUCT_OPTIONS', 'ProductOptions');
define ('NODE_PRODUCT_OPTION_ID', 'ProductOptionID');
define ('NODE_PRODUCT_OPTION_NAME', 'ProductOptionName');
define ('NODE_PRODUCT_OPTION_VALUES', 'ProductOptionValues');
define ('NODE_PRODUCT_OPTION_VALUE_ID', 'ProductOptionValueID');
define ('NODE_PRODUCT_OPTION_VALUE_NAME', 'ProductOptionValueName');
define ('NODE_PRODUCTS', 'Products');
define ('NODE_PRODUCT_NAME', 'ProductName');
define ('NODE_PRODUCT_DESCRIPTION', 'ProductDescription');
define ('NODE_PRODUCT_MODEL', 'ProductModel');
define ('NODE_PARENT_PRODUCT_MODEL', 'ParentProductModel');
define ('NODE_PRODUCT_QUANTITY', 'ProductQuantity');
define ('NODE_PRODUCT_MANUFACTURER', 'ProductManufacturer');
define ('NODE_PRODUCT_PRICE', 'WholesalePrice');
//BOF:mod 20120402
define ('NODE_MAP_PRICE', 'MAPPrice');
//EOF:mod 20120402
define ('NODE_PRODUCT_WEIGHT', 'ProductWeight');
define ('NODE_PRODUCT_SMALL_IMAGE', 'ProductSmallImage');
define ('NODE_PRODUCT_MEDIUM_IMAGE', 'ProductMediumImage');
define ('NODE_PRODUCT_LARGE_IMAGE', 'ProductLargeImage');
define ('NODE_PRODUCT_ATTRIBUTES', 'ProductAttributes');
define ('NODE_PRODUCT_OPTION_VALUE_PRICE', 'ProductOptionValuePrice');
define ('NODE_PRODUCT_SPECIFICATIONS', 'ProductSpecifications');
define ('PERMISSIBLE_FEEDS_LIMIT', '25');
define ('OBN_FEED_SOS_URL', 'http://67.227.172.78/admin/send_reqd_info_to_retailer.php');


class global_feed_to_osc{
	public $xml;
	public $feed_id;
	public $xml_feed_osc_id;
	public $small_image_path;
	public $medium_image_path;
	public $large_image_path;
	public $currency_code;
	public $weight_unit;
	public $categories;
	public $product_options;
	public $product_option_values;
	public $products;
	public $edit_flags;
	public $default_flag;
    //BOF:hash_task    
    public $options;
    //EOF:hash_task
    public $manufacturers;

	public function __construct(){
		$this->options = array();
        $this->manufacturers = array();
	}

	public function inventory_feed_to_osc(){
		//echo 'START: ' . strtoupper(date('dMy H:i:s', time())) . "\n";
		$dir = DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/';
		$feeds = array();
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while(($file = readdir($dh)) !== false){
					$pos = strpos($file, 'inventory_feed_');
					if ($pos!==false){
						if (!count($feeds)){
							$feeds[] = $dir . $file;
						} else {
							$index_to_move = -1;
							for($i=0; $i<count($feeds); $i++){
								if (filemtime($feeds[$i]) > filemtime($dir . $file)){
									$index_to_move = $i;
									break;
								}
							}
							if ($index_to_move==-1){
								$feeds[] = $dir . $file;
							} else {
								for($i=count($feeds)-1; $i>=$index_to_move; $i--){
									$feeds[$i+1] = $feeds[$i];
								}
								$feeds[$index_to_move] = $dir . $file;
							}
						}
					}
				}
				closedir($dh);
			}
		}
		//echo "Feeds count: " . sizeof($feeds) . "\n";
		if (sizeof($feeds)>(int)PERMISSIBLE_FEEDS_LIMIT){
			//echo "Feeds count exceeds permissible limit!\nDeleting all feed files....";
			foreach($feeds as $remove_file){
				@unlink($remove_file);
			}
			//echo "deleted\n";
			//echo 'Fetching full inventory feed from OBN....';
			$params = array('request_type' => 'stock_feed',
							'retailer_token' => OBN_RETAILER_TOKEN);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, OBN_FEED_SOS_URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			$resp = curl_exec($ch);
			curl_close($ch);
			//echo "fetched\n";
			$this->xml = @simplexml_load_string($resp);
			if ($this->xml){
				//echo "handling dynamic full inventory.....";
				$this->handle_inventory_feed_xml();
			}
		} else {
			foreach($feeds as $feed){
				$this->xml = @simplexml_load_file($feed);
				if ($this->xml){
					//echo "handling inventory feed " . basename($feed) . ".....";
					$this->handle_inventory_feed_xml($feed);
				} else {
					@unlink($feed);
				}
			}
		}

		//echo 'STOP: ' . strtoupper(date('dMy H:i:s', time())) . "\n\n";
	}

	private function handle_inventory_feed_xml($feed = ''){
		$this->feed_id =& $this->xml->{NODE_FEED_ID};

		$sql_data_array = array('document_id' => $this->feed_id,
								'catalog_id' => '',
								'url_image_thumbs' => '',
								'url_image_medium' => '',
								'url_image_large' => '',
								'date_added' => 'now()');
		tep_db_perform('xml_feed', $sql_data_array);
		$this->xml_feed_osc_id = tep_db_insert_id();

		foreach($this->xml->{NODE_PRODUCTS}->children() as $product){
			$temp_prod_model = htmlspecialchars_decode((string)$product->{NODE_PRODUCT_MODEL});
			$temp_prod_qty = (string)$product->{NODE_PRODUCT_QUANTITY};
			$sql = tep_db_query("select a.products_id, b.flags from products a inner join products_xml_feed_flags b on a.products_id=b.products_id where a.products_model='" . tep_db_input($temp_prod_model) . "'");
			if (tep_db_num_rows($sql)){
				$prod_exists = 1;
				$sql_info = tep_db_fetch_array($sql);
				$temp_prod_osc_id = $sql_info['products_id'];
				$temp_flags = $sql_info['flags'];
				$flag_prod_qty = substr($temp_flags, 0, 1);
			}

			if ($prod_exists && $flag_prod_qty){
				$sql_data_array = array('products_quantity' => (int)$temp_prod_qty,
										'products_last_modified' => 'now()');
				tep_db_perform('products', $sql_data_array, 'update', "products_id = '" . $temp_prod_osc_id . "'");
			}
		}
		//echo "handled.....";
		if (!empty($feed)){
			if (@unlink($feed)){
				//echo "file deleted\n";
			} else {
				//echo "file delete error\n";
			}
		} else {
			//echo "delete not applicable as dynamic\n";
		}
	}

	private function get_category_markup($category_id){
		$markup = '';
		$sql = tep_db_query("select markup from categories where categories_id='" . $category_id . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			$markup = $info['markup'];
		}
		return $markup;
	}

	public function product_feed_to_osc(){
		/*if (count($feeds) ){
			$sql = tep_db_query("select distinct a.parent_products_model, b.products_id from products a inner join products b on a.parent_products_model=b.products_model where a.parent_products_model!=''");
			while($entry = tep_db_fetch_array($sql)){
				$options_query = tep_db_query("select distinct pa.options_id from products_attributes pa inner join products p using (products_id) where p.parent_products_model='" . str_replace("'", "''", $entry['parent_products_model']) . "'");
				while ($option = tep_db_fetch_array($options_query)){
					$attribute_query = tep_db_query("select products_attributes_id from products_attributes where products_id='" . (int)$entry['products_id'] . "' and options_id='" . (int)$option['options_id'] . "'");
					if (!tep_db_num_rows($attribute_query)){
						$option_data = array(
							'products_id' => $entry['products_id'], 
							'options_id' => $option['options_id'], 
						);
						tep_db_perform('products_attributes', $option_data);
					}
				}
			}
		//}
		die("done");*/
		/*tep_db_query("truncate table manufacturers");
		tep_db_query("truncate table manufacturers_info");
		tep_db_query("truncate table products_options");
		tep_db_query("truncate table products_options_values");
		tep_db_query("truncate table products_options_values_to_products_options");
		tep_db_query("truncate table products_attributes");
		tep_db_query("truncate table products_xml_feed_flags");
		tep_db_query("truncate table products_to_categories");
		tep_db_query("truncate table products_description");
		tep_db_query("truncate table products_extended");
		tep_db_query("truncate table products");
		tep_db_query("truncate table categories");
		tep_db_query("truncate table categories_description");*/
		
		//BOF:mod 05032012
		$dir = DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/';
		//$dir = '/home/devkroll/{8ADB0310-8B21-1A40-B4DE-4D2D43DDF360}/';
		$feeds = array();
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while(($file = readdir($dh)) !== false){
					$pos = strpos($file, 'product_feed_');
					if ($pos!==false){
						$feeds[] = $dir . $file;
					}
				}
				closedir($dh);
			}
		}

        if (empty($this->categories)){
            if (file_exists($dir . 'OBN_categories.xml')){
                $categories_xml = simplexml_load_file($dir . 'OBN_categories.xml');
                foreach($categories_xml->children() as $category){
                    $temp_id = (string)$category->{NODE_CATEGORY_ID};
                    $temp_name = htmlspecialchars_decode((string)$category->{NODE_CATEGORY_NAME});
                    $temp_parent_id = (string)$category->{NODE_CATEGORY_PARENT_ID};
                    $this->add_category_to_osc($temp_id, $temp_name, ($temp_parent_id ? $temp_parent_id : '0'));
                }
            } 
        }


        if (empty($this->product_options)){
            if (file_exists($dir . 'OBN_options.xml')){
                $options_xml = simplexml_load_file($dir . 'OBN_options.xml');
                foreach($options_xml->ProductOptions->children() as $product_option){
                    $temp_id = (string)$product_option->{NODE_PRODUCT_OPTION_ID};
                    $temp_name = htmlspecialchars_decode((string)$product_option->{NODE_PRODUCT_OPTION_NAME});
                    $this->add_product_option_to_osc($temp_id, $temp_name);
                }
                foreach($options_xml->ProductOptionValues->children() as $product_option_value){
                    $temp_option_id = (string)$product_option_value->{NODE_PRODUCT_OPTION_ID};
                    $temp_id = (string)$product_option_value->{NODE_PRODUCT_OPTION_VALUE_ID};
                    $temp_name = htmlspecialchars_decode((string)$product_option_value->{NODE_PRODUCT_OPTION_VALUE_NAME});
                    $this->add_product_option_value_to_osc($temp_id, $temp_name, $temp_option_id);
                }
            }
        }
        //EOF:01APR2015
       	//check for existence of 'parent_products_model' data column exists
           $parent_products_model_exists = false;
           $column_check = tep_db_query("show columns from products like 'parent_products_model'");
            if (tep_db_num_rows($column_check)){
                $parent_products_model_exists = true;
            }
            
            $specification_table_exists = false;
             $table_exists_query = tep_db_query("show tables from " . DB_DATABASE . " like 'product_specifications'");
              if (tep_db_num_rows($table_exists_query)){
                $specification_table_exists = true;
               } 
  
		foreach($feeds as $feed){
		echo $feed . "\n";
			/*
		//EOF:mod 05032012
			$this->xml = simplexml_load_file(DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/product_feed_' . OBN_RETAILER_TOKEN . '.xml');
		//BOF:mod 05032012
			*/
			$this->xml = @simplexml_load_file($feed);
			if (!$this->xml){
				@unlink($feed);
				//echo "\t" . 'xml corrupt' . "\n";
				continue;
			}
		//EOF:mod 05032012
			$this->feed_id =& $this->xml->{NODE_FEED_ID};
			$this->small_image_path =& $this->xml->{NODE_SMALL_IMAGE_PATH};
			$this->medium_image_path =& $this->xml->{NODE_MEDIUM_IMAGE_PATH};
			$this->large_image_path =& $this->xml->{NODE_LARGE_IMAGE_PATH};
			$this->currency_code =& $this->xml->{NODE_CURRENCY_CODE};
			$this->weight_unit =& $this->xml->{NODE_WEIGHT_UNIT};
			if (empty($this->categories)){
				$this->categories = array();
				$this->product_options = array();
				$this->product_option_values = array();
			}
			$this->products = array();
			$this->edit_flags = array('products_quantity', //if set to 1 inventory value will be updated
						  'products_price', //if set to 1 price value (products_price/base_price) will be updated
						  'categories_id',//if set to 1 category id value will be updated
						  'products_description', //if set to 1 inventory value will be updated
						  'products_image', // if set to 1 products_image value will be updated
						  //BOF:mod 20120419
						  'manual_price', // if set to 1, manual price will be populated with MAPPrice (if exists)
						  //EOF:mod 20120419
						  );
			//BOF:mod 20120419
			/*
			//EOF:mod 20120419
			$this->default_flag = '11111';//default value assigned to set the product status
			//BOF:mod 20120419
			*/
			$this->default_flag = '111110';//default value assigned to set the product status
			//EOF:mod 20120419

			$sql_data_array = array('document_id' => $this->feed_id,
						'catalog_id' => '',
						'url_image_thumbs' => $this->small_image_path,
						'url_image_medium' => $this->medium_image_path,
						'url_image_large' => $this->large_image_path,
						'date_added' => 'now()');
			tep_db_perform('xml_feed', $sql_data_array);
			$this->xml_feed_osc_id = tep_db_insert_id();

			$temp_id = '';
			$temp_name = '';
			$temp_option_id = '';
			$temp_value_id = '';
			$temp_prod_cat_id = '';
			//BOF:mod 05032012
			$temp_prod_cat_ids = '';
			//EOF:mod 05032012
			$temp_parent_id = '';
			$temp_prod_desc = '';
			$temp_prod_model = '';
			$temp_parent_prod_model = '';
			$temp_prod_qty = '';
			$temp_prod_manuf = '';
			$temp_prod_price = '';
			$temp_prod_map = '';
			$temp_prod_weight = '';
			$temp_prod_image_small = '';
			$temp_prod_image_medium = '';
			$temp_prod_image_large = '';
			$temp_prod_specs = '';
            
			if (empty($this->categories)){
				foreach($this->xml->{NODE_CATEGORIES}->children() as $category){
					$temp_id = (string)$category->{NODE_CATEGORY_ID};
					$temp_name = htmlspecialchars_decode((string)$category->{NODE_CATEGORY_NAME});
					$temp_parent_id = (string)$category->{NODE_CATEGORY_PARENT_ID};
					//$this->add_category_to_osc($temp_id, $temp_name, ($temp_parent_id ? substr($temp_parent_id, 1) : '0'));
					//echo $temp_id . ' : ' . $temp_name . ' : ' . $temp_parent_id . "\n";
					$this->add_category_to_osc($temp_id, $temp_name, ($temp_parent_id ? $temp_parent_id : '0'));
				}
				//print_r($this->categories);
				foreach($this->xml->{NODE_PRODUCT_OPTIONS}->children() as $product_option){
					$temp_id = (string)$product_option->{NODE_PRODUCT_OPTION_ID};
					$temp_name = htmlspecialchars_decode((string)$product_option->{NODE_PRODUCT_OPTION_NAME});
					//echo $temp_id . ' : ' . $temp_name . "\n";
					$this->add_product_option_to_osc($temp_id, $temp_name);
				}
				//print_r($this->product_options);
				foreach($this->xml->{NODE_PRODUCT_OPTION_VALUES}->children() as $product_option_value){
					$temp_option_id = (string)$product_option_value->{NODE_PRODUCT_OPTION_ID};
					$temp_id = (string)$product_option_value->{NODE_PRODUCT_OPTION_VALUE_ID};
					$temp_name = htmlspecialchars_decode((string)$product_option_value->{NODE_PRODUCT_OPTION_VALUE_NAME});
					//echo $temp_option_id . ' : ' . $temp_id . ' : ' . $temp_name . "\n";
					$this->add_product_option_value_to_osc($temp_id, $temp_name, $temp_option_id);
				}
			}
            
			//print_r($this->product_option_values);
			foreach($this->xml->{NODE_PRODUCTS}->children() as $product){
				$products_status = (string)$product['status'];
				if($products_status){
					//echo "$temp_prod_model: start...";
					$temp_disclaimer_status = (string)$product['disclaimerStatus'];
					$temp_is_ok_for_shipping = (string)$product['isOKForShipping'];
					$temp_name = utf8_decode(htmlspecialchars_decode((string)$product->{NODE_PRODUCT_NAME}));
					$temp_prod_cat_id = (string)$product->{NODE_CATEGORY_ID};
					//BOF:mod 05032012
					$temp_prod_cat_ids = (string)$product->{NODE_CATEGORY_IDS};
					//EOF:mod 05032012
					$temp_prod_desc = utf8_decode(htmlspecialchars_decode((string)$product->{NODE_PRODUCT_DESCRIPTION}));
					$temp_prod_model = htmlspecialchars_decode((string)$product->{NODE_PRODUCT_MODEL});
					$temp_parent_prod_model = htmlspecialchars_decode((string)$product->{NODE_PARENT_PRODUCT_MODEL});
					$temp_prod_qty = (string)$product->{NODE_PRODUCT_QUANTITY};
					$temp_prod_manuf = htmlspecialchars_decode((string)$product->{NODE_PRODUCT_MANUFACTURER});
					$temp_prod_price = (string)$product->{NODE_PRODUCT_PRICE};
					//BOF:mod 20120402
					$temp_prod_map = (string)$product->{NODE_MAP_PRICE};
					//EOF:mod 20120402
					$temp_prod_weight = (string)$product->{NODE_PRODUCT_WEIGHT};
					$temp_prod_image_small = (string)$product->{NODE_PRODUCT_SMALL_IMAGE};
					$temp_prod_image_medium = (string)$product->{NODE_PRODUCT_MEDIUM_IMAGE};
					$temp_prod_image_large = (string)$product->{NODE_PRODUCT_LARGE_IMAGE};
					$temp_prod_specs = utf8_decode(htmlspecialchars_decode((string)$product->{NODE_PRODUCT_SPECIFICATIONS}));
					$temp_manuf_osc_id = 0;
					$temp_prod_osc_id = 0;
					$prod_exists = 0;
					//echo $temp_name . ' : ' . $temp_prod_cat_id . ' : ' . $temp_prod_desc . ' : ' . $temp_prod_model . ' : ' . $temp_prod_qty . ' : ' . $temp_prod_manuf . ' : ' . $temp_prod_price . ' : ' . $temp_prod_map . ' : ' . $temp_prod_weight . ' : ' . $temp_prod_image . '<br>';
                if (!empty($temp_prod_manuf)) {
                   if(!isset($this->manufacturers[$temp_prod_manuf])) {
					$sql = tep_db_query("select manufacturers_id from manufacturers where manufacturers_name='" . str_replace("'", "''", $temp_prod_manuf) . "'");
					//echo "1...";
					if (tep_db_num_rows($sql)){
						$sql_info = tep_db_fetch_array($sql);
						$temp_manuf_osc_id = $sql_info['manufacturers_id'];
					} else {
							$sql_data_array = array('manufacturers_name' => $temp_prod_manuf,
										'date_added' => 'now()',
										'last_modified' => 'now()',
										//'markup' => '0%',
										//'markup_modified' => 'now()'
                                                                                );
							tep_db_perform('manufacturers', $sql_data_array);
							$temp_manuf_osc_id = tep_db_insert_id();
							
							$sql_data_array = array(
								'manufacturers_id' => $temp_manuf_osc_id, 
								'languages_id' => '1', 
							);
					      tep_db_perform('manufacturers_info', $sql_data_array);
					  }
                     $this->manufacturers[$temp_prod_manuf] = $temp_manuf_osc_id;
                      
                     } else {
                      $temp_manuf_osc_id = $this->manufacturers[$temp_prod_manuf];
                     } 
                 }else {
				    $temp_manuf_osc_id = '0';
				  }
                  
					//echo "2...";
                    $status_update_locked = false;
					$sql = tep_db_query("select products_id, lock_status from products where products_model='" . str_replace("'", "''", $temp_prod_model) . "'");
					//echo "3...";
					if (tep_db_num_rows($sql)){
						$prod_exists = 1;
						$sql_info = tep_db_fetch_array($sql);
						$temp_prod_osc_id = $sql_info['products_id'];
                        $status_update_locked = isset($sql_info['products_id']) && $sql_info['lock_status']=='1' ? true : false;
						//looks up for product's xml feed flag status
						$sql = tep_db_query("select flags from products_xml_feed_flags where products_id='" . $temp_prod_osc_id . "'");
						//echo "4...";
						if (tep_db_num_rows($sql)){
								$sql_info = tep_db_fetch_array($sql);
								$this->default_flag = $sql_info['flags'];// updates default flag status
								$flag_prod_qty = substr($this->default_flag, 0, 1);
								$flag_prod_price = substr($this->default_flag, 1, 1);
								$flag_cat_id = substr($this->default_flag, 2, 1);
								$flag_prod_desc = substr($this->default_flag, 3, 1);
								$flag_prod_image = substr($this->default_flag, 4, 1);
								//BOF:mod 20120419
								$flag_manual_price = substr($this->default_flag, 5, 1);
								if (empty($flag_manual_price)) $flag_manual_price = '0';
								//EOF:mod 20120419
						}
					}

					$products_price = 0;
					$markup = '';
					$lock_price = 0;
					if ($prod_exists){
						$sql = tep_db_query("select products_price, base_price, markup, lock_price, roundoff_flag
								    from products where products_id='" .
								    $temp_prod_osc_id . "'");
						//echo "5...";
						if (tep_db_num_rows($sql)){
							$sql_info = tep_db_fetch_array($sql);
							$markup = $sql_info['markup'];
							$lock_price = (int)$sql_info['lock_price'];
							$roundoff_flag = (int)$sql_info['roundoff_flag'];
							if ($flag_prod_price){
								if (!$lock_price){
									$products_price = ($roundoff_flag ? $this->apply_roundoff($this->get_price_with_markup($temp_prod_price, $markup)) : $this->get_price_with_markup($temp_prod_price, $markup));
									$base_price = $temp_prod_price;
								} else {
									$products_price = $sql_info['products_price'];
									$base_price = $temp_prod_price;
								}
							} else {
									$products_price = $sql_info['products_price'];
									$base_price = $sql_info['base_price'];
							}
						} else {
							$products_price = (ROUNDOFF_FLAG ? $this->apply_roundoff($this->get_price_with_markup($temp_prod_price, DEFAULT_MARKUP)) : $this->get_price_with_markup($temp_prod_price, DEFAULT_MARKUP));
							$markup = DEFAULT_MARKUP;
							$roundoff_flag = ROUNDOFF_FLAG;
							$base_price = $temp_prod_price;
						}
					} else {
							$products_price = (ROUNDOFF_FLAG ? $this->apply_roundoff($this->get_price_with_markup($temp_prod_price, DEFAULT_MARKUP)) : $this->get_price_with_markup($temp_prod_price, DEFAULT_MARKUP));
							$markup = DEFAULT_MARKUP;
							$roundoff_flag = ROUNDOFF_FLAG;
							$base_price = $temp_prod_price;
					}
                    
                    //BOF:hash_task
                    /*if (!empty($temp_parent_prod_model)){
						$parent_query = tep_db_query("select products_id from products where products_model='" . str_replace("'", "''", $temp_parent_prod_model) . "'");
						echo "6...";
                        if (tep_db_num_rows($parent_query)){
                            $parent_info = tep_db_fetch_array($parent_query);
                            //$option_query = tep_db_query();
                            foreach($this->options as $option){
                                $attribute_query = tep_db_query("select products_attributes_id from products_attributes where products_id='" . (int)$parent_info['products_id'] . "' and options_id='" . (int)$option . "'");
								echo "7...";
                                if (!tep_db_num_rows($attribute_query)){
                                    $option_data = array(
                                        'products_id' => $parent_info['products_id'], 
                                        'options_id' => $option, 
                                    );
                                    tep_db_perform('products_attributes', $option_data);
                                }
                            }
                        }
                    }*/
                    //EOF:hash_task

					$sql_data_array = array('products_model' => $temp_prod_model,
								//'products_image' => (empty($temp_prod_image_small) ? '' : $this->small_image_path . $temp_prod_image_small),
								//'products_mediumimage' => (empty($temp_prod_image_medium) ? '' : $this->medium_image_path . $temp_prod_image_medium),
								//'products_largeimage' => (empty($temp_prod_image_large) ? '' : $this->large_image_path . $temp_prod_image_large),
								'products_price' => $products_price,
								'products_weight' => $temp_prod_weight,
								'manufacturers_id' => $temp_manuf_osc_id,
								'base_price' => $base_price,
								'markup' => $markup,
								'lock_price' => $lock_price,
								'roundoff_flag' => $roundoff_flag,
								'products_size' => '0',
								'products_tax_class_id' => '1',
								'disclaimer_needed' => $temp_disclaimer_status,
								'is_ok_for_shipping' => $temp_is_ok_for_shipping,
                                //'parent_products_model' => (empty($temp_parent_prod_model) ? 'null' : $temp_parent_prod_model),
                                );
				
					if ($parent_products_model_exists && !empty($temp_parent_prod_model) && $temp_parent_prod_model != 'NULL') {
						$sql_data_array['parent_products_model'] = $temp_parent_prod_model;
					}
					
					//BOF:mod 20120419
					if (!empty($temp_prod_map) && $temp_prod_map>0){
						//if ($flag_manual_price || ($flag_manual_price && OBN_POPULATE_MANUAL_PRICE=='true' && OBN_ACTIVATE_MANUAL_PRICE_FOR_ALL_ITEMS=='true')){
						if ($flag_manual_price || (OBN_POPULATE_MANUAL_PRICE=='true' && OBN_ACTIVATE_MANUAL_PRICE_FOR_ALL_ITEMS=='true')){
							$sql_data_array['products_price'] = $temp_prod_map;
							$sql_data_array['manual_price'] = $temp_prod_map;
							$sql_data_array['lock_price'] = '1';
						}
					}
					//EOF:mod 20120419
					if ($prod_exists){
						if($flag_prod_qty){
						//	$sql_data_array['products_quantity'] = (int)$temp_prod_qty;
						}

						if ($flag_prod_image){
							$sql_data_array['products_image'] = (empty($temp_prod_image_small) ? '' : $this->small_image_path . $temp_prod_image_small);
							$sql_data_array['products_mediumimage'] = (empty($temp_prod_image_medium) ? '' : $this->medium_image_path . $temp_prod_image_medium);
							$sql_data_array['products_largeimage'] = (empty($temp_prod_image_large) ? '' : $this->large_image_path . $temp_prod_image_large);
						}

						$sql_data_array['products_last_modified'] = 'now()';
						//as per previously laid conventions, product status should remain unmodified for existing items
						//this seems to be the reason for parent products not acivating at retailer's end
                        if (!$status_update_locked) $sql_data_array['products_status'] = '1';
						tep_db_perform('products', $sql_data_array, 'update', "products_id = '" . $temp_prod_osc_id . "'");
						//echo "8...";
					} else {
						$sql_data_array['products_quantity'] = (int)$temp_prod_qty;
						$sql_data_array['products_status'] = '1';
						$sql_data_array['products_date_added'] = 'now()';
						$sql_data_array['products_image'] = (empty($temp_prod_image_small) ? '' : $this->small_image_path . $temp_prod_image_small);
						$sql_data_array['products_mediumimage'] = (empty($temp_prod_image_medium) ? '' : $this->medium_image_path . $temp_prod_image_medium);
						$sql_data_array['products_largeimage'] = (empty($temp_prod_image_large) ? '' : $this->large_image_path . $temp_prod_image_large);
						tep_db_perform('products', $sql_data_array);
						$temp_prod_osc_id = tep_db_insert_id();
						//echo "9...";
					}

					$sql_data_array = array('products_id' => $temp_prod_osc_id,
											'language_id' => '1');
					if ($prod_exists){
						if($flag_prod_desc){
							$sql_data_array['products_description'] = $temp_prod_desc;
							$sql_data_array['products_specifications'] = $temp_prod_specs;
							$sql_data_array['products_name'] = $temp_name;
						}
						tep_db_perform('products_description', $sql_data_array, 'update', "products_id = '" . $temp_prod_osc_id . "' and language_id='1'");
						//echo "10...";
						//BOF:mod 20120622
						if(!$flag_prod_desc){
							$p_name_exists_query = tep_db_query("select products_name from products_description where products_id = '" . $temp_prod_osc_id . "' and language_id='1'");
							$p_name_exists_info = tep_db_fetch_array($p_name_exists_query);
							if (empty($p_name_exists_info['products_name'])){
								$sql_data_array = array('products_description' 		=> $temp_prod_desc,
														'products_specifications' 	=> $temp_prod_specs,
														'products_name' 			=> $temp_name);
								tep_db_perform('products_description', $sql_data_array, 'update', "products_id = '" . $temp_prod_osc_id . "' and language_id='1'");
								//echo "11...";
							}
						}
						//EOF:mod 20120622
					} else {
						$sql_data_array['products_description'] = $temp_prod_desc;
						$sql_data_array['products_specifications'] = $temp_prod_specs;
						$sql_data_array['products_name'] = $temp_name;
						tep_db_perform('products_description', $sql_data_array);
						//echo "12...";
					}
					//BOF:mod 05032012
					/*
					//EOF:mod 05032012
					//if (!empty($temp_prod_cat_id)){
						$sql_data_array = array('products_id' => $temp_prod_osc_id,
												'categories_id' => (int)$this->categories[$temp_prod_cat_id]['osc_cat_id']);
						if ($prod_exists){
							//echo 'P2C (UPDATE): ' . $temp_prod_osc_id . ' : ' . (int)$this->categories[$temp_prod_cat_id]['osc_cat_id'] . '<br>';
							if ($flag_cat_id){
								//tep_db_perform('products_to_categories', $sql_data_array, 'update', " products_id = '" . $temp_prod_osc_id . "' ");
								tep_db_query("delete from products_to_categories where products_id='" . $temp_prod_osc_id . "'");
								tep_db_perform('products_to_categories', $sql_data_array);
							}
						} else {
							//echo 'P2C (NEW): ' . $temp_prod_osc_id . ' : ' . (int)$this->categories[$temp_prod_cat_id]['osc_cat_id'] . '<br>';
							tep_db_perform('products_to_categories', $sql_data_array);
							$markup = $this->get_category_markup((int)$this->categories[$temp_prod_cat_id]['osc_cat_id']);
							if (!empty($markup) && $markup!=DEFAULT_MARKUP){
								$products_price = ($roundoff_flag ? $this->apply_roundoff($this->get_price_with_markup($temp_prod_price, $markup)) : $this->get_price_with_markup($temp_prod_price, $markup));
								$sql_array = array('markup' => $markup, 'products_price' => $products_price);
								tep_db_perform('products', $sql_array, 'update', "products_id='" . $temp_prod_osc_id . "'");
							}
						}
					//}
					//BOF:mod 05032012
					*/
					if (!$prod_exists || $flag_cat_id){
						if ($prod_exists) tep_db_query("delete from products_to_categories where products_id='" . $temp_prod_osc_id . "'");
						if (!empty($temp_prod_cat_id)){
							tep_db_query("insert ignore into products_to_categories (products_id, categories_id) values ('" . (int)$temp_prod_osc_id . "', '" . (int)$this->categories[$temp_prod_cat_id]['osc_cat_id'] . "')");
							if (!$prod_exists){
								$markup = $this->get_category_markup((int)$this->categories[$temp_prod_cat_id]['osc_cat_id']);
								if (!empty($markup) && $markup!=DEFAULT_MARKUP){
									$products_price = ($roundoff_flag ? $this->apply_roundoff($this->get_price_with_markup($temp_prod_price, $markup)) : $this->get_price_with_markup($temp_prod_price, $markup));
									$sql_array = array('markup' => $markup, 'products_price' => $products_price);
									tep_db_perform('products', $sql_array, 'update', "products_id='" . $temp_prod_osc_id . "'");
									//echo "13...";
								}
							}
						}
						if (!empty($temp_prod_cat_ids)){
							$temp_cats_col = explode(',', $temp_prod_cat_ids);
							foreach($temp_cats_col as $temp_cat){
								tep_db_query("insert ignore into products_to_categories (products_id, categories_id) values ('" . (int)$temp_prod_osc_id . "', '" . (int)$this->categories[$temp_cat]['osc_cat_id'] . "')");
								//echo "14...";
								if (!$prod_exists){
									$markup = $this->get_category_markup((int)$this->categories[$temp_cat]['osc_cat_id']);
									if (!empty($markup) && $markup!=DEFAULT_MARKUP){
										$products_price = ($roundoff_flag ? $this->apply_roundoff($this->get_price_with_markup($temp_prod_price, $markup)) : $this->get_price_with_markup($temp_prod_price, $markup));
										$sql_array = array('markup' => $markup, 'products_price' => $products_price);
										tep_db_perform('products', $sql_array, 'update', "products_id='" . $temp_prod_osc_id . "'");
										//echo "15...";
									}
								}
							}
						}
					}
					//EOF:mod 05032012

					$sql_data_array = array('unit_cost' => $temp_prod_price,
											'unit_cost_cur' => $this->currency_code,
											//BOF:mod 20120419
											'min_acceptable_price' => $temp_prod_map,
											//EOF:mod 20120419
											'sales_price' =>$temp_prod_price,
											'osc_products_id' => $temp_prod_osc_id,
											'xml_feed_id' => $this->xml_feed_osc_id);
					if ($prod_exists){
						$sql_data_array['last_modified'] = 'now()';
						tep_db_perform('products_extended', $sql_data_array, 'update', "osc_products_id = '" . $temp_prod_osc_id . "'");
						//echo "16...";
					} else {
						$sql_data_array['date_added'] = 'now()';
						tep_db_perform('products_extended', $sql_data_array);
						//echo "17...";
					}
					
                    $attributes_array = array();
					foreach($product->{NODE_PRODUCT_ATTRIBUTES}->children() as $attribute){
						$temp_option_id = (string)$attribute->{NODE_PRODUCT_OPTION_ID};
						$temp_value_id = (string)$attribute->{NODE_PRODUCT_OPTION_VALUE_ID};
						$temp_prod_price = (string)$attribute->{NODE_PRODUCT_OPTION_VALUE_PRICE};

						$sql = tep_db_query("select products_attributes_id from products_attributes where products_id='" .
											$temp_prod_osc_id . "' and options_id='" .
											$this->product_options[$temp_option_id]['osc_option_id'] . "' and options_values_id='" .
											$this->product_option_values[$temp_value_id]['osc_value_id'] . "'");
						$sql_data_array = array('products_id' => $temp_prod_osc_id,
												'options_id' => $this->product_options[$temp_option_id]['osc_option_id'],
												'options_values_id' => $this->product_option_values[$temp_value_id]['osc_value_id'],
												'options_values_price' => $temp_prod_price,
												'price_prefix' => '');
						if (tep_db_num_rows($sql)){
							$sql_info = tep_db_fetch_array($sql);
                            $attributes_array[] = $sql_info['products_attributes_id'];
							tep_db_perform('products_attributes', $sql_data_array, 'update', "products_attributes_id = '" . $sql_info['products_attributes_id'] . "'");
						} else {
							tep_db_perform('products_attributes', $sql_data_array);
                            $attributes_id = tep_db_insert_id();
                            $attributes_array[] = $attributes_id;
						}
						
					}
                    if (sizeof($attributes_array) > 0)
                       tep_db_query("delete from products_attributes where products_id='" . (int)$temp_prod_osc_id . "' and products_attributes_id not in (" . implode(",", $attributes_array) . ")");
					//echo "18...";
                    
     
                                        if ($specification_table_exists) {
                                            tep_db_query("delete from product_specifications where products_id='" . (int)$temp_prod_osc_id . "'");
                                            foreach($product->Specifications->children() as $specification){
                                                $temp_name = (string)$specification->SpecificationName;
                                                $temp_value = (string)$specification->SpecificationValue;
                                              if (!isset($specification_name_array[$temp_name])) {
                                                tep_db_query("insert into product_specification_names (name) values ('" . tep_db_input($temp_name) . "') on duplicate key update id=last_insert_id(id)");
                                                $name_query = tep_db_query("select last_insert_id() as name_id");
                                                $name_info = tep_db_fetch_array($name_query);
                                                $name_id = $name_info['name_id'];
                                                $specification_name_array[$temp_name] = $name_id;
                                              } else {
                                                $name_id = $specification_name_array[$temp_name];
                                              }
                                                tep_db_query("insert into product_specification_values (specification_name_id, value) values ('" . (int)$name_id . "', '" .  tep_db_input($temp_value) . "') on duplicate key update id=last_insert_id(id)");
                                                $value_query = tep_db_query("select last_insert_id() as value_id");
                                                $value_info = tep_db_fetch_array($value_query);
                                                $value_id = $value_info['value_id'];

                                                tep_db_query("insert ignore into product_specifications (products_id, specification_id) values ('" . (int)$temp_prod_osc_id . "', '" . (int)$value_id  . "')");
                                            } 
                                          } 


                                            $sql = tep_db_query("select products_xml_feed_flags_id from products_xml_feed_flags where products_id='" . $temp_prod_osc_id . "'");
					if (!tep_db_num_rows($sql)){
						$sql_array = array('products_id' 	=> $temp_prod_osc_id,
											'flags' 		=> $this->default_flag,
											'last_modified' => 'now()');
						tep_db_perform('products_xml_feed_flags', $sql_array);
					}
					//echo "19...";
					//echo "$temp_prod_model: end\n";
				} else {
					$temp_prod_model = htmlspecialchars_decode((string)$product->{NODE_PRODUCT_MODEL});
					tep_db_query("update products set products_date_disabled=now() where products_model='" . tep_db_input($temp_prod_model) . "' and products_status='1'");
					tep_db_query("update products set products_status='0' where products_model='" . tep_db_input($temp_prod_model) . "' and lock_status<>'1'");
				}
			}
		//BOF:mod 05032012
			@unlink($feed);
		}
		//EOF:mod 05032012
	}

	private function add_product_option_value_to_osc($value_feed_id, $value_name, $option_feed_id){
		$value_name = trim($value_name);
		if (!empty($value_name)){
			//if (!empty($value_name)){
				$new_entry = 1;
				$resp = array('value_id' => '');
				$sql = tep_db_query("select products_options_values_id
									from products_options_values where products_options_values_name='" .
									str_replace("'", "''", $value_name) . "'");
				if (tep_db_num_rows($sql)){
					$sql_info = tep_db_fetch_array($sql);
					$resp['value_id'] = $sql_info['products_options_values_id'];
					$new_entry = 0;
				}

				if ($new_entry){
					$value_id = $this->get_next_products_options_value_id();
					$sql_data_array = array('products_options_values_id' => $value_id,
											'language_id' => '1',
											'products_options_values_name' => $value_name);
					tep_db_perform('products_options_values', $sql_data_array);
					$resp['value_id'] = $value_id;
				}
				$this->product_option_values[$value_feed_id] = array('osc_value_id' => $resp['value_id']);

				$sql = tep_db_query("select products_options_values_to_products_options_id
									from products_options_values_to_products_options where products_options_id='" .
									$this->product_options[$option_feed_id]['osc_option_id'] . "' and products_options_values_id='" .
									$this->product_option_values[$value_feed_id]['osc_value_id'] . "'");
				if (!tep_db_num_rows($sql)){
					$sql_data_array = array('products_options_id' => $this->product_options[$option_feed_id]['osc_option_id'],
											'products_options_values_id' => $this->product_option_values[$value_feed_id]['osc_value_id']);
					tep_db_perform('products_options_values_to_products_options', $sql_data_array);
				}
			//}
		}
	}

	private function add_product_option_to_osc($option_feed_id, $option_name){
		$option_name = trim($option_name);
		if (!empty($option_name)){
			$new_entry = 1;
			$resp = array('option_id' => '');
			$sql = tep_db_query("select products_options_id
								from products_options where products_options_name='" .
								str_replace("'", "''", $option_name) . "'");
			if (tep_db_num_rows($sql)){
				$sql_info = tep_db_fetch_array($sql);
				$resp['option_id'] = $sql_info['products_options_id'];
				$new_entry = 0;
			}

			if ($new_entry){
				$option_id = $this->get_next_products_option_id();
				$sql_data_array = array('products_options_id' => $option_id,
										'language_id' => '1',
										'products_options_name' => $option_name);
				tep_db_perform('products_options', $sql_data_array);
				$resp['option_id'] = $option_id;
			}
			$this->product_options[$option_feed_id] = array('osc_option_id' => $resp['option_id']);
			//BOF:hash_task
			if (!in_array($resp['option_id'], $this->options)){
				$this->options[] = $resp['option_id'];
			}
			//EOF:hash_task
		}
	}

	private function get_next_products_option_id(){
		$sql = tep_db_query("select max(products_options_id) as count from products_options");
		$sql_info = tep_db_fetch_array($sql);

		return ((int)$sql_info['count'] + 1) ;
	}

	private function get_next_products_options_value_id(){
		$sql = tep_db_query("select max(products_options_values_id) as count from products_options_values");
		$sql_info = tep_db_fetch_array($sql);

		return ((int)$sql_info['count'] + 1);
	}

	private function add_category_to_osc($cat_feed_id, $cat_name, $cat_parent_id){
		$new_entry = 1; //initialize new-entry flag to 1 by default
		$resp = array('cat_id' => '', 'parent_id' => '');//set the return object
		//fetch categoryID, parentId where category name matches with $cat_name
		//$sql = tep_db_query("select a.categories_id, b.parent_id from categories_description a inner join categories b on a.categories_id=b.categories_id where a.categories_name='" . tep_db_input($cat_name) . "' and a.language_id='1'");
                //above query modify to ensure value does not carries single quote without special handling.
                //although tep_db_input function handled such cases but, somehow, it's not working and I'm getting errors: 22jan2015
                $sql = tep_db_query("select a.categories_id, b.parent_id from categories_description a inner join categories b on a.categories_id=b.categories_id where a.categories_name='" . addslashes(tep_db_input($cat_name)) . "' and a.language_id='1'");
		if (tep_db_num_rows($sql)){ // if some rows exist
			while ($sql_info = tep_db_fetch_array($sql)){ // loop through each row
				$cur_parent_id = $sql_info['parent_id'];// set variable for storing parent_id
				if (empty($cur_parent_id)){ // is parent ID is empty i.e. ==0
					$cur_parent_id = '0'; // set parent_id as 0
				}

				if ($cur_parent_id==$cat_parent_id){
					// this will be the case if we are dealing with topmost categories that have no parent_ids associated with them
					$resp['cat_id'] = $sql_info['categories_id']; // set return object for cat_id
					$resp['parent_id'] = $cur_parent_id; // set return object for parent id
					$new_entry = 0;// set new_entry flag to 0 as match already located
					break;// get out of the loop
				} elseif ($cur_parent_id==$this->categories[$cat_parent_id]['osc_cat_id']) {
					//this will be the case of categories that are under other categories
					//as the parent category is already handled (all topmost categories are handled prior to dealing with child categories)
					//if the condition evaluates to true
					//(checks if current parent id matches the osc_cat_id associated with the parent category)
					$resp['cat_id'] = $sql_info['categories_id']; // set return object for cat_id
					$resp['parent_id'] = $cur_parent_id; // set return object for cat_id
					$new_entry = 0;// set new_entry flag to 0 as match already located
					break;// get out of the loop
				} elseif ($cat_parent_id==='0'){ // if above two contitions are not met and $cat_parent_id is '0'
					// check if the parent id is associated with is_category_group flag
					$sql_1 = tep_db_query("select is_category_group from categories where categories_id='" . $cur_parent_id . "'");
					$sql_info_1 = tep_db_fetch_array($sql_1);
					$is_category_group = $sql_info_1['is_category_group'];
					if ($is_category_group){
						// if is_category_group flag is activated it means we have to treat the entry as if parent_id is 0
						// (because the movement was done by retailer locally in order to create category groups)
						$resp['cat_id'] = $sql_info['categories_id'];
						$resp['parent_id'] = $cur_parent_id;
						$new_entry = 0;
						break;
					}
				}
			}
		}

		if ($new_entry){//if new_entry flag is still 1, we have to register new entry
			$sql_data_array = array('parent_id' => (!$cat_parent_id ? '0' : $this->categories[$cat_parent_id]['osc_cat_id']),
									'sort_order' => '0',
									'banner_image' => '',
									'markup' => DEFAULT_MARKUP,
									'date_added' => 'now()',
									'last_modified' => 'now()',
									'markup_modified' => 'now()');
			tep_db_perform('categories', $sql_data_array);
			$resp['cat_id'] = tep_db_insert_id();
			$resp['parent_id'] = (!$cat_parent_id ? '0' : $this->categories[$cat_parent_id]['osc_cat_id']);
			$sql_data_array = array('categories_id' => $resp['cat_id'],
									'language_id' => '1',
									'categories_name' => $cat_name);
			tep_db_perform('categories_description', $sql_data_array);
		}
		$this->categories[$cat_feed_id] = array('osc_cat_id' => $resp['cat_id'],
											'osc_parent_id' => $resp['parent_id']);
	}

	protected function get_price_with_markup($base_price, $markup){
		if (empty($markup)){
			return $base_price;
		}else{
				$markup_figure = $markup;// holds markup figure
				$markup_in_percent = 0;//a check if markup is in percentage
				$markup_in_negative = 0;//a check if markup is negative
				if (substr($markup_figure, -1)=='%'){ // if markup in percentage
					$markup_in_percent = 1;//update percentage check
					$markup_figure = substr($markup_figure, 0, -1);//modify markup figure by removing percentage
				}
				if (substr($markup_figure, 0, 1)=='-'){// if negative value exists
					$markup_in_negative = 1;//update negetive check
					$markup_figure = substr($markup_figure, 1);//modify markup figure by removing minus
				}
				return ($markup_in_negative ? ($base_price - ($markup_in_percent ? (($base_price*$markup_figure)/100)  :  $markup_figure)) : ($base_price + ($markup_in_percent ? (($base_price*$markup_figure)/100)  :  $markup_figure)));
		}
	}

	protected function apply_roundoff($price_value){
		$pos = strpos($price_value, '.');
		if ($pos===false){
			$response = ($price_value-1) . '.99';
		}
		else{
			$response = $price_value;
			$value_parts = explode('.', $response);
			if (strlen($value_parts[1])>2){
				$value_parts[1] = substr($value_parts[1], 0, 2);
			}
			$response = $value_parts[0] . '.' . ($value_parts[1] + (99 - $value_parts[1]));

		}
		return $response;
	}
}

/*
$feed = new global_feed_to_osc();
echo NODE_FEED_ID . ' : ' . $feed->feed_id . '<br>';
echo NODE_SMALL_IMAGE_PATH . ' : ' . $feed->small_image_path . '<br>';
echo NODE_MEDIUM_IMAGE_PATH . ' : ' . $feed->medium_image_path . '<br>';
echo NODE_LARGE_IMAGE_PATH . ' : ' . $feed->large_image_path . '<br>';
echo NODE_CURRENCY_CODE . ' : ' . $feed->currency_code . '<br>';
echo NODE_WEIGHT_UNIT . ' : ' . $feed->weight_unit . '<br>';*/
?>