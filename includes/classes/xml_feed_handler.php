<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
	define('NODE_DOCUMENT_ID', 								'DocumentID');// XML feed node
	define('NODE_GENERATION_DATE', 							'GenerationDate');// XML feed node
	define('NODE_CATALOG_ID',	 							'CatalogID');// XML feed node
	define('NODE_PRODUCTS', 								'Products');// XML feed node
	define('NODE_PRODUCT', 									'LineItem');// XML feed node
	define('NODE_DESCRIPTION', 								'Description');// XML feed node
	define('KEY_DESCRIPTION', 								'desc_short');//associative array's key
	define('NODE_UPC_EAN', 									'UPC_EAN');// XML feed node
	define('KEY_UPC_EAN', 									'upc_ean');//associative array's key
	define('NODE_UNIT_COST', 								'UnitCost');// XML feed node
	define('KEY_UNIT_COST', 								'unit_cost');//associative array's key
	define('KEY_UNIT_COST_CURRENCY',						KEY_UNIT_COST . '_cur');//associative array's key
	define('NODE_UNIT_MSRP', 								'UnitMSRP');// XML feed node
	define('KEY_UNIT_MSRP', 								'unit_msrp');//associative array's key
	define('KEY_UNIT_MSRP_CURRENCY',						KEY_UNIT_MSRP . '_cur');//associative array's key
	define('ATTRIB_CURRENCY',								'Currency');//XML feed attribute name
	define('NODE_VENDOR_SKU', 								'VendorSKU');// XML feed node
	define('KEY_VENDOR_SKU', 								'vendor_sku');//associative array's key
	define('NODE_VENDOR_ATTRIBUTES', 						'VendorAttributesExtended');// XML feed node
	define('NODE_ATTRIBUTE_NAME',	 						'AttributeName');// XML feed node
	define('NODE_ATTRIBUTE_VALUE',	 						'AttributeValue');// XML feed node
	define('TEXT_FULFILLER',								'Fulfiller');// XML feed node value for attributeName
	define('KEY_FULFILLER',									'fulfiller');//associative array's key
	define('TEXT_PRODUCT_NAME',								'Product Name');// XML feed node value for attributeName
	define('KEY_PRODUCT_NAME',								'prod_name');//associative array's key
	define('TEXT_PRODUCT_ID',								'Product ID');// XML feed node value for attributeName
	define('KEY_PRODUCT_ID',								'prod_id');//associative array's key
	define('TEXT_PRODUCT_WEIGHT',							'Weight');// XML feed node value for attributeName
	define('KEY_PRODUCT_WEIGHT',							'prod_weight');//associative array's key
	define('TEXT_SALES_PRICE',								'SalesPrice');// XML feed node value for attributeName
	define('KEY_SALES_PRICE',								'sales_price');//associative array's key
	define('TEXT_MINIMUM_ACCEPTABLE_PRICE',					'Minimum Acceptable Price');// XML feed node value for attributeName
	define('KEY_MINIMUM_ACCEPTABLE_PRICE',					'min_acceptable_price');//associative array's key
	define('TEXT_DESCRIPTION_NRA_NUMBER',					'Description / NRA Number');// XML feed node value for attributeName
	define('KEY_DESCRIPTION_NRA_NUMBER',					'desc_nra_num');//associative array's key
	define('TEXT_MODEL',									'Model');// XML feed node value for attributeName
	define('KEY_MODEL',										'model');//associative array's key
	define('NODE_BUYER_ATTRIBUTES', 						'BuyerAttributesExtended');// XML feed node
	define('TEXT_CATEGORY_PATH',							'Category');// XML feed node value for attributeName
	define('KEY_CATEGORY_PATH',								'category_path');//associative array's key
	define('NODE_BRAND_NAME', 								'BrandName');// XML feed node
	define('KEY_BRAND_NAME', 								'brand_name');//associative array's key
	define('NODE_SELLING_QTY', 								'SellingQty');// XML feed node
	define('KEY_SELLING_QTY', 								'selling_qty');//associative array's key
	define('NODE_EXTENDED_INFO', 							'ExtendedInfo');// XML feed node
	define('NODE_DESCRIPTION_LONG',							'LongDescription');// XML feed node
	define('KEY_DESCRIPTION_LONG',							'desc_long');//associative array's key
	define('URL_IMAGE_THUMBS',								'http://www.wildcatcommerce.com/productimages/thumbs/');//URL value hardcoded but being used in this way for any future customisation
	define('URL_IMAGE_MEDIUM',								'http://www.wildcatcommerce.com/productimages/medium/');//URL value hardcoded but being used in this way for any future customisation
	define('URL_IMAGE_LARGE',								'http://www.wildcatcommerce.com/productimages/large/');//URL value hardcoded but being used in this way for any future customisation
	define('NODE_IMAGE_INFO',								'ImageInfo');// XML feed node
	define('NODE_IMAGE_LIST',								'ImageList');// XML feed node
	define('TEXT_IMAGE_NAME',								'Image Name');// XML feed node value for attributeName
	define('KEY_IMAGE_NAME',								'image_name');//associative array's key
	define('NODE_URI',										'URI');// XML feed node
	define('TABLE_PRODUCTS_EXTENDED',						'products_extended');//OSC table reference
	define('TABLE_XML_FEED',								'xml_feed');//OSC table reference
	define('TABLE_PRODUCTS_XML_FEED_FLAGS',					'products_xml_feed_flags');//OSC table reference
		
	class xml_feed_handler{
		private $xml_file_name;//holds XML file path
		private $xml_doc;//reference to xml document

		public $document_id;//holds xml feed's document ID
		public $gen_date_timestamp;//holds xml feed's generation date time stamp
		public $catalog_id;//holds xml feed's catalog ID
		public $url_image_thumbs;//holds xml feed's image url thumb
		public $url_image_medium;//holds xml feed's image url medium
		public $url_image_large;//holds xml feed's image url large
		
		public $xml_feed_id;//holds auto-generated xml feed id
		
		public $products;//holds 'LineItem' node reference for all products
		public $product_current;// holds 'LineItem' node reference for current product as array
		public $product_info;// holds all the values specific to current product as associative array
		public $product_attribs;// holds current product's'  'VendorAttributes' name-value pair that do not have a separate reference. the values are committed to product options / value 
		public $product_category_path;//associative array holding current product''s key(categoryName) and value(categoryID), the last entry is the category product belongs to
		public $product_category_id;//holds current product's category id
		public $product_osc_id;//holds existing or auto-generated osc product id associated with current product
		public $product_osc_id_exists;//a check to confirm if current product's osc id already exists
		private $product_option_new;//a check if the option is new and needs to be committed to database
		private $product_option_value_new;//a check if the option value is new and needs to be committed to database
		private $edit_flags = array('products_quantity', //if set to 1 inventory value will be updated
								'products_price', //if set to 1 price value (products_price/base_price) will be updated
								'categories_id',//if set to 1 category id value will be updated
								'products_description'//if set to 1 inventory value will be updated
								);
		private $default_flag = '1111';//default value assigned to set the product status
		private $price_type = array('UP'=>KEY_UNIT_COST, 
									'MS'=>KEY_UNIT_MSRP, 
									'MA'=>KEY_MINIMUM_ACCEPTABLE_PRICE, 
									'SP'=>KEY_SALES_PRICE);//a listing of price levels that are part of XML feed
		//on osc's admin panel, a configuration is in place to activate one out of the four values shown above
		public $default_price_type = XML_FEED_DEFAULT_PRICE_TYPE;//holds currently activated price type. gets the value from OSC constant
		
		public function xml_feed_handler(){
			$this->xml_file_name = '../../acusport.xml';//xml file path, will be later modified to be dynamic (if reqd)
			$this->xml_doc = simplexml_load_file($this->xml_file_name);//loads xml file
			$this->document_id = $this->unescape_node_value((string)$this->xml_doc->{NODE_DOCUMENT_ID});//set document id
			$this->gen_date_timestamp = $this->get_timestamp($this->unescape_node_value((string)$this->xml_doc->{NODE_GENERATION_DATE}));//set time stamp
			$this->catalog_id = $this->unescape_node_value((string)$this->xml_doc->{NODE_CATALOG_ID});//set catalog id
			$this->url_image_thumbs = URL_IMAGE_THUMBS;//set image url thumbnail
			$this->url_image_medium = URL_IMAGE_MEDIUM;//set image url medium
			$this->url_image_large = URL_IMAGE_LARGE;//set image url large
			$this->xml_feed_id - $this->register_xml_feed();//func called to set XML_feed_id to database
			$this->products = $this->xml_doc->{NODE_PRODUCTS}->children();//a collection of product nodes created
		}
		
		private function get_timestamp($date_string){
			//converts date string format YYYY-MM-DD to unix timestamp
			return strtotime($date_string);
		}
		
		public function set_current_product($node_ref){
				//func gets called for each product
				$this->reset_fields();//func calld to reset variables before referencing new product  
				$this->product_current = $node_ref;//pointer set to current product
				$this->product_info[KEY_DESCRIPTION] = $this->unescape_node_value((string)$this->product_current->{NODE_DESCRIPTION});
				$this->product_info[KEY_UPC_EAN] = $this->unescape_node_value((string)$this->product_current->{NODE_UPC_EAN});
				$this->product_info[KEY_UNIT_COST] = $this->unescape_node_value((string)$this->product_current->{NODE_UNIT_COST});
				$this->product_info[KEY_UNIT_COST_CURRENCY] = $this->unescape_node_value((string)$this->product_current->{NODE_UNIT_COST}[ATTRIB_CURRENCY]);
				$this->product_info[KEY_UNIT_MSRP] = $this->unescape_node_value((string)$this->product_current->{NODE_UNIT_MSRP});
				$this->product_info[KEY_UNIT_MSRP_CURRENCY] = $this->unescape_node_value((string)$this->product_current->{NODE_UNIT_MSRP}[ATTRIB_CURRENCY]);
				$this->product_info[KEY_VENDOR_SKU] = $this->unescape_node_value((string)$this->product_current->{NODE_VENDOR_SKU});
				$this->set_vendor_attributes();//func called to take care of 'VendorAttributes' node
				$this->set_buyer_attributes();//func called to take care of 'BuyerAttributes' node
				$this->product_info[KEY_BRAND_NAME] = $this->unescape_node_value((string)$this->product_current->{NODE_BRAND_NAME});
				$this->product_info[KEY_SELLING_QTY] = $this->unescape_node_value((string)$this->product_current->{NODE_SELLING_QTY});
				$this->set_extended_info();//func called to take care of 'ExtendedInfo' node
				$this->set_category_levels();//func called to take care of category levels for current product
				$this->product_osc_id = $this->get_product_osc_id();//generates new osc product's id if new product else gets existing osc product's id
				
				//all products that r not part current xml feed will be incativated for viewing
				/*tep_db_query("update " . 
							TABLE_PRODUCTS . 
							" set products_status='0', products_last_modified=now() where products_id not in " .
							" (select distinct(osc_products_id) from " . 
								TABLE_PRODUCTS_EXTENDED . 
								" where xml_feed_id<>'" . $this->xml_feed_id . "')");*/

		}
		
		private function set_vendor_attributes(){
			$vendor_attribs = $this->product_current->{NODE_VENDOR_ATTRIBUTES};// get node reference
			foreach($vendor_attribs->children() as $attrib){
				switch ($this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_NAME})){				
					case TEXT_FULFILLER:
						$this->product_info[KEY_FULFILLER] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_PRODUCT_NAME:
						$this->product_info[KEY_PRODUCT_NAME] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_PRODUCT_ID:
						$this->product_info[KEY_PRODUCT_ID] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_PRODUCT_WEIGHT:
						$this->product_info[KEY_PRODUCT_WEIGHT] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_SALES_PRICE:
						$this->product_info[KEY_SALES_PRICE] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_MINIMUM_ACCEPTABLE_PRICE:
						$this->product_info[KEY_MINIMUM_ACCEPTABLE_PRICE] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_DESCRIPTION_NRA_NUMBER:
						$this->product_info[KEY_DESCRIPTION_NRA_NUMBER] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					case TEXT_MODEL:
						$this->product_info[KEY_MODEL] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
					default:
						$this->product_attribs[$this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_NAME})] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;							
				}
			}			
		}
		
		private function set_buyer_attributes(){			
			$buyer_attribs = $this->product_current->{NODE_BUYER_ATTRIBUTES};
			foreach($buyer_attribs->children() as $attrib){
				switch ($this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_NAME})){				
					case TEXT_CATEGORY_PATH:
						$this->product_info[KEY_CATEGORY_PATH] = $this->unescape_node_value((string)$attrib->{NODE_ATTRIBUTE_VALUE});
						break;
				}
			}
		}
		
		private function set_extended_info(){
			$extended_info_node = $this->product_current->{NODE_EXTENDED_INFO};
			$this->product_info[KEY_DESCRIPTION_LONG] = $this->unescape_node_value((string)$extended_info_node->{NODE_DESCRIPTION_LONG});
			$this->product_info[KEY_IMAGE_NAME] = $this->unescape_node_value((string)$extended_info_node->{NODE_IMAGE_LIST}->{NODE_IMAGE_INFO}->{NODE_URI}); 
		}
		
		private function set_category_levels(){
			$vals = explode('||', $this->product_info[KEY_CATEGORY_PATH]);// split XML feed's category value
			foreach($vals as $val){
				$val = trim($val);
				$parent_id = 0;
				if (count($this->product_category_path)){//if array holds any value
					$arr_keys = array_keys($this->product_category_path);
					$parent_id = (int)$this->product_category_path[$arr_keys[count($arr_keys)-1]];//get parent ID
					$this->product_category_path[$val] = $this->get_category_id($val, $parent_id);
				}
				else{//if this is the first item to be entered
					$this->product_category_path[$val] = $this->get_category_id($val, 0);
				}
			}
			
			$arr_keys = array_keys($this->product_category_path);
			$this->product_category_id = (int)$this->product_category_path[$arr_keys[count($arr_keys)-1]];//set category id from the last item
		}
		
		private function get_category_id($cat_name, $parent_id = 0){
			$sql = tep_db_query("select a.categories_id " .
								" from " . TABLE_CATEGORIES . " a inner join " .
								TABLE_CATEGORIES_DESCRIPTION . " b on a.categories_id=b.categories_id " .
								" and b.language_id='1' " .
								" and a.parent_id='" . (int)$parent_id . "' "  .
								" and b.categories_name='" . $cat_name . "'");
			if (tep_db_num_rows($sql)){
				$sql_info = tep_db_fetch_array($sql);
				return (int)$sql_info['categories_id']; 
			}
			else{
				return (int)$this->add_category($cat_name, $parent_id);
			}						 
		}
		
		private function add_category($cat_name, $parent_id = 0){
			$sql_array = array('parent_id' => $parent_id,
							   'categories_status' => '1',
							   'date_added' => 'now()',
							   'last_modified' => 'now()',
							   'banner_image' => '');
			tep_db_perform(TABLE_CATEGORIES, $sql_array);
			$categoris_id = tep_db_insert_id();
			$sql_array = array('categories_id' => $categoris_id,
							  'language_id' => '1', 
							  'categories_name' => $cat_name);
			tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_array);
			return $categoris_id; 
		}
		
		private function unescape_node_value($val){
			//func called to filter url encoded data and to make it safe for commiting to dataase
			//all further filters can be applied here
			return str_replace('\'', '\'\'', urldecode($val));
		}
		
		private function get_product_osc_id(){
			$product_id;
			//check if the product's osc id already exists
			$sql = tep_db_query("select osc_products_id from " .
								TABLE_PRODUCTS_EXTENDED . " where prod_id='" .
								$this->product_info[KEY_PRODUCT_ID] . "'");
			if (tep_db_num_rows($sql)){//osc product id exists				
				$sql_info = tep_db_fetch_array($sql);				
				$this->product_osc_id_exists = 1;//set exists variable
				$product_id = $sql_info['osc_products_id'];
				
				$markup = '';
				$lock_price = 0;
				$roundoff_flag = 0;
				//fetches markup and lock price value from products dataTable 
				$sql = tep_db_query("select markup, lock_price, manual_price, roundoff_flag from " .
						TABLE_PRODUCTS . " where products_id='" .
						$product_id . "'");
				if (tep_db_num_rows($sql)){
					$sql_info = tep_db_fetch_array($sql);
					
					$markup = $sql_info['markup'];
					$lock_price = (int)$sql_info['lock_price'];
					$roundoff_flag = (int)$sql_info['roundoff_flag'];
				}
				
				//set values for updating products_extended dataTable
				$sql_array = array('desc_short'=>$this->product_info[KEY_DESCRIPTION],
									'upc_ean'=>$this->product_info[KEY_UPC_EAN],
									'unit_cost'=>$this->product_info[KEY_UNIT_COST],
									'unit_cost_cur'=>$this->product_info[KEY_UNIT_COST_CURRENCY],
									'unit_msrp'=>$this->product_info[KEY_UNIT_MSRP],
									'unit_msrp_cur'=>$this->product_info[KEY_UNIT_MSRP_CURRENCY],
									'vendor_sku'=>$this->product_info[KEY_VENDOR_SKU],
									'fulfiller'=>$this->product_info[KEY_FULFILLER],
									'prod_id'=>$this->product_info[KEY_PRODUCT_ID],
									'min_acceptable_price'=>$this->product_info[KEY_MINIMUM_ACCEPTABLE_PRICE],
									'sales_price'=>$this->product_info[KEY_SALES_PRICE],
									'brand_name'=>$this->product_info[KEY_BRAND_NAME],
									'xml_feed_id'=>$this->xml_feed_id,
									'categories_id'=>$this->product_category_id,
									'last_modified'=>'now()');
				tep_db_perform(TABLE_PRODUCTS_EXTENDED, $sql_array, 'update', "osc_products_id = '" . $product_id . "'");
				
				//looks up for products category id
				$sql = tep_db_query("select categories_id from " .
								TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" .
								$product_id . "'");
				$sql_info = tep_db_fetch_array($sql);
				$this->product_category_id = (int)$sql_info['categories_id'];
				
				//looks up for product's xml feed flag status 
				$sql = tep_db_query("select flags from " . 
					 					TABLE_PRODUCTS_XML_FEED_FLAGS . 
										 " where products_id='" . $product_id . "'");
				if (tep_db_num_rows($sql)){
						$sql_info = tep_db_fetch_array($sql);
						$this->default_flag = $sql_info['flags'];// updates default flag status
				}
				
				$flag_index = -1;// local variable to be used while looping through each flag value
				$tab_prod_sql = '';//partial part for products dataTable
				$tab_prod_ext_sql = '';//partial part for products_extended dataTable
				$tab_prod_2_cat_sql = '';//partial part for products2categories dataTable
				$tab_prod_desc_sql = '';//partial part for products_description dataTable
				foreach($this->edit_flags as $flag){//loops through the flags array set in declaration
					$flag_index++;//increment local variable to fetch the required flag
					if (substr($this->default_flag, $flag_index, 1)){
						switch ($flag){
							case 'products_quantity':
								$tab_prod_sql .= (empty($tab_prod_sql) ? " " : ", ") . 
												  " products_quantity='" .
												  (int)$this->product_info[KEY_SELLING_QTY] . "' ";//set value for updating
								break;
							case 'products_price':
								//these two cases will never get executed and thus will not cause any change to price
								//if lock_price is active (1) & XML feed price flag is in-active(0) => do not update products_price and base_price
								//if lock_price is in-active (0) & XML feed price flag is in-active(0) =>  do not update products_price and base_price
								$price = $this->product_info[$this->price_type[$this->default_price_type]];
								if ($lock_price){
									//if lock_price is active (1) & XML feed price flag is also active(1) => do not update products_price, but update base_price
									$tab_prod_sql .= (empty($tab_prod_sql) ? " " : ", ") . 
													  " base_price='" .
													  $price . "' ";//set value for updating
								}else{
									//if lock_price is in-active (0) & XML feed price flag is active(1) => update both products_price and base_price
									$tab_prod_sql .= (empty($tab_prod_sql) ? " " : ", ") . 
													  " products_price='" . 
													  ($roundoff_flag ? $this->apply_roundoff($this->get_price_with_markup($price, $markup)) : $this->get_price_with_markup($price, $markup)) . "', base_price='" .
													  $price . "' ";//set value for updating
								}
								break;
							case 'categories_id':
								$arr_keys = array_keys($this->product_category_path);
								$this->product_category_id = (int)$this->product_category_path[$arr_keys[count($arr_keys)-1]];
								$tab_prod_2_cat_sql .= (empty($tab_prod_2_cat_sql) ? " " : ", ") . 
												  " categories_id='" .
												  $this->product_category_id . "' ";//set value for updating
								break;
							case 'products_description':
								$tab_prod_desc_sql .= (empty($tab_prod_desc_sql) ? " " : ", ") . 
												  " products_description='" .
												  $this->product_info[KEY_DESCRIPTION_LONG] . "' ";//set value for updating
								break;
						}
					}
				}
				
				if (!empty($tab_prod_sql)){//check if variable is not empty
					tep_db_query("update " . 
								TABLE_PRODUCTS . " set " .
								$tab_prod_sql . 
								", products_last_modified=now() where products_id='" .
								$product_id . "'");
				}
				
				if (!empty($tab_prod_ext_sql)){//check if variable is not empty
					tep_db_query("update " . 
								TABLE_PRODUCTS_EXTENDED . " set " .
								$tab_prod_ext_sql . 
								", last_modified=now() where osc_products_id='" .
								$product_id . "'");
				}
				
				if (!empty($tab_prod_2_cat_sql)){//check if variable is not empty
					tep_db_query("update " . 
								TABLE_PRODUCTS_TO_CATEGORIES . " set " .
								$tab_prod_2_cat_sql . 
								" where products_id='" .
								$product_id . "'");
				}
				
				if (!empty($tab_prod_desc_sql)){//check if variable is not empty
					tep_db_query("update " . 
								TABLE_PRODUCTS_DESCRIPTION . " set " .
								$tab_prod_desc_sql . 
								" where products_id='" .
								$product_id . "' and language_id='1'");
				}
				
				return $product_id;// returns product_id | all database activities for this product are finished
			}else{//osc product id does not exists | treat as new entry 
				$this->product_osc_id_exists = 0;//a check that this is a new entry				
				//set values for products dataTable
				$sql_array = array('products_quantity'=>$this->product_info[KEY_SELLING_QTY],
									//'products_model'=>$this->product_info[KEY_MODEL],
									'products_model'=>$this->product_info[KEY_VENDOR_SKU],
									'products_image'=>$this->product_info[KEY_IMAGE_NAME],									
									'products_price'=>(ROUNDOFF_FLAG ? $this->apply_roundoff($this->get_price_with_markup($this->product_info[$this->price_type[$this->default_price_type]], DEFAULT_MARKUP)) : $this->get_price_with_markup($this->product_info[$this->price_type[$this->default_price_type]], DEFAULT_MARKUP)),
									//'products_price'=>$this->product_info[$this->price_type[$this->default_price_type]],
									'products_date_added'=>'now()',
									'products_last_modified'=>'now()',
									'products_weight'=>$this->product_info[KEY_PRODUCT_WEIGHT],
									'products_status'=>'1',
									'base_price'=>$this->product_info[$this->price_type[$this->default_price_type]],
									'manufacturers_id'=>$this->get_manufacturers_id($this->product_info[KEY_BRAND_NAME]),
									'roundoff_flag'=>ROUNDOFF_FLAG);
				tep_db_perform(TABLE_PRODUCTS, $sql_array);
				$product_id =  tep_db_insert_id();
				
				//set values for products_description
				$sql_array = array('products_id'=>$product_id,
									'language_id'=>'1',
									'products_name'=>$this->product_info[KEY_PRODUCT_NAME],
									'products_description'=>$this->product_info[KEY_DESCRIPTION_LONG]);
				tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_array);
				
				//set values for products_to_categories
				$sql_array = array('products_id'=>$product_id,
									'categories_id'=>$this->product_category_id);
				tep_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_array);
				
				//set values for products_extended
				$sql_array = array('desc_short'=>$this->product_info[KEY_DESCRIPTION],
									'upc_ean'=>$this->product_info[KEY_UPC_EAN],
									'unit_cost'=>$this->product_info[KEY_UNIT_COST],
									'unit_cost_cur'=>$this->product_info[KEY_UNIT_COST_CURRENCY],
									'unit_msrp'=>$this->product_info[KEY_UNIT_MSRP],
									'unit_msrp_cur'=>$this->product_info[KEY_UNIT_MSRP_CURRENCY],
									'vendor_sku'=>$this->product_info[KEY_VENDOR_SKU],
									'fulfiller'=>$this->product_info[KEY_FULFILLER],
									'prod_id'=>$this->product_info[KEY_PRODUCT_ID],
									'min_acceptable_price'=>$this->product_info[KEY_MINIMUM_ACCEPTABLE_PRICE],
									'sales_price'=>$this->product_info[KEY_SALES_PRICE],
									'brand_name'=>$this->product_info[KEY_BRAND_NAME],
									'osc_products_id'=>$product_id,
									'xml_feed_id'=>$this->xml_feed_id,
									'categories_id'=>$this->product_category_id,
									'date_added'=>'now()',
									'last_modified'=>'now()');
				tep_db_perform(TABLE_PRODUCTS_EXTENDED, $sql_array);
				
				//check if the product associated with options / option values 
				$option_id;
				$value_id;
				foreach($this->product_attribs as $key=>$value){
					$option_id = $this->get_products_option_id($key);
					$value_id = $this->get_products_options_value_id($value);
					
					if ($this->product_option_new){//if new option
						$sql_array = array('products_options_id'=>$option_id,
											'language_id'=>'1',
											'products_options_name'=>$key,
											'is_xml_feed_option'=>'1');
						tep_db_perform(TABLE_PRODUCTS_OPTIONS, $sql_array);	
					}				
					
					if ($this->product_option_value_new){//if new option value
						$sql_array = array('products_options_values_id'=>$value_id,
											'language_id'=>'1',
											'products_options_values_name'=>$value);
						tep_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES, $sql_array);
					}					
					
					if (!($this->products_options_to_values_association_exists($option_id, $value_id))){//associate optionValue with option
						$sql_array = array('products_options_id'=>$option_id,
											'products_options_values_id'=>$value_id);
						tep_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS, $sql_array);
					}

					//associate the options with product					
					$sql_array = array('products_id'=>$product_id,
										'options_id'=>$option_id,
										'options_values_id'=>$value_id);
					tep_db_perform(TABLE_PRODUCTS_ATTRIBUTES, $sql_array);										
				}
				
				//insert flag value in database for the product
				tep_db_query("insert into " . 
							TABLE_PRODUCTS_XML_FEED_FLAGS .
							" (products_id, flags, last_modified) " . 
							" values " .
							" ('" . $product_id . "', '" . $this->default_flag . "', now())");
				
				return $product_id;// returns product_id | all database activities for this product are finished
			}
		}
		
		private function get_price_with_markup($base_price, $markup){
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
		
		private function get_manufacturers_id($cur_brand_name){
			$sql = tep_db_query("select manufacturers_id from " . TABLE_MANUFACTURERS . 
								" where manufacturers_name='" . str_replace('\'', '\'\'', $cur_brand_name) . "'");
			if (tep_db_num_rows($sql)){
				$sql_info = tep_db_fetch_array($sql);
				$id = $sql_info['manufacturers_id'];
				return $id; 
			}else{
				$sql_array = array('manufacturers_name'=>$cur_brand_name,
									'date_added'=>'now()',
									'last_modified'=>'now()');
				tep_db_perform(TABLE_MANUFACTURERS, $sql_array);
				$id = tep_db_insert_id();
				tep_db_query("insert into " . 
							 TABLE_MANUFACTURERS_INFO . 
							 " (manufacturers_id, languages_id, manufacturers_url, url_clicked) values ('" .
							 $id . "', '1', '', '0')");
				return $id;
			}
		}
		
		private function products_options_to_values_association_exists($option_id, $value_id){
			$sql = tep_db_query("select products_options_values_to_products_options_id from " .
								TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where " .
								" products_options_id='" . $option_id . "' and " .
								" products_options_values_id='" . $value_id . "'");
			if (tep_db_num_rows($sql)){
				return 1;
			}else{
				return 0;
			}
		}
		
		private function get_products_option_id($text_option){
			$sql = tep_db_query("select products_options_id from " 
								. TABLE_PRODUCTS_OPTIONS . " where language_id='1' and " .
								" products_options_name='" . $text_option . "'");
			if (tep_db_num_rows($sql)){
				$sql_info = tep_db_fetch_array($sql);
				$this->product_option_new = 0;
				return (int)$sql_info['products_options_id'];
			}else{
				$this->product_option_new = 1;
				return $this->get_next_option_id();
			}
		}
		
		private function get_products_options_value_id($text_value){
			$sql = tep_db_query("select products_options_values_id from " 
								. TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id='1' and " .
								" products_options_values_name='" . $text_value . "'");
			if (tep_db_num_rows($sql)){
				$sql_info = tep_db_fetch_array($sql);
				$this->product_option_value_new = 0;
				return (int)$sql_info['products_options_values_id'];
			}else{
				$this->product_option_value_new = 1;
				return $this->get_next_option_value_id();
			}
		}
		
		private function get_next_option_id(){
			$sql = tep_db_query("select max(products_options_id) as count from " . TABLE_PRODUCTS_OPTIONS);
			$sql_info = tep_db_fetch_array($sql);
			return (int)$sql_info['count']+1;
		}
		
		private function get_next_option_value_id(){
			$sql = tep_db_query("select max(products_options_values_id) as count from " . TABLE_PRODUCTS_OPTIONS_VALUES);
			$sql_info = tep_db_fetch_array($sql);
			return (int)$sql_info['count']+1;
		}
		
		private function register_xml_feed(){
			//this func is called by the constructor resulting in XML feed's registration to database
			//a unique auto-generated id is generated (xml_feed_id)
			$sql_array = array('document_id'=>$this->document_id,
								'gen_date'=>$this->gen_date_timestamp,
								'catalog_id'=>$this->catalog_id,
								'url_image_thumbs'=>$this->url_image_thumbs,
								'url_image_medium'=>$this->url_image_medium,
								'url_image_large'=>$this->url_image_large,
								'date_added'=>'now()');
			tep_db_perform(TABLE_XML_FEED, $sql_array);
			$this->xml_feed_id = tep_db_insert_id();
		}
		
		private function apply_roundoff($price_value){	
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
		
		private function reset_fields(){
			//func called to reset variables before a new product reference is set
			unset($this->product_current);//unset previous product reference
			$this->product_info = array();//clear product_info array
			$this->product_attribs = array();//clear product attributes array
			$this->product_category_path = array();//clease category path array
			$this->default_flag = '1111';//reset default flag status if affected by previous product
			unset($this->product_category_id);//unset category id
			unset($this->product_osc_id);//unset product' osc id
			unset($this->product_osc_id_exists);//unset flag
		}		

	}
?>
