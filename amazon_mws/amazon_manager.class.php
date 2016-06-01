<?php
class amazon_manager{
    public $service;
    private $markup_coefficient;

    public function amazon_manager($service_type = 'mws'){
        $this->set_service_object($service_type);
        if (defined('AMAZON_PRICE_MARKUP_COEFFICIENT') && AMAZON_PRICE_MARKUP_COEFFICIENT>=1){
            $this->markup_coefficient = AMAZON_PRICE_MARKUP_COEFFICIENT;
        } else {
            $this->markup_coefficient = 1;
        }

    }

    private function set_service_object($service_type){
        switch($service_type){
            case 'mws':
		$config = array('ServiceURL' => AMAZON_FEEDS_SERVICE_URL);
		$this->service = new MarketplaceWebService_Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION);
                break;
            case 'mwso':
                $config = array('ServiceURL' => AMAZON_ORDERS_SERVICE_URL);
                $this->service = new MarketplaceWebServiceOrders_Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, APPLICATION_NAME, APPLICATION_VERSION, $config);
                break;
        }
    }

    private function get_feed_header($feed_type){
        $resp =
            '<?xml version="1.0" encoding="UTF-8"?>
                <AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <Header>
                        <DocumentVersion>1.01</DocumentVersion>
                        <MerchantIdentifier>' . AMAZON_MERCHANT_TOKEN . '</MerchantIdentifier>
                    </Header>
                    <MessageType>' . $this->get_message_type($feed_type) . '</MessageType>';
        return $resp;
    }

    private function get_feed_footer(){
        $resp = '</AmazonEnvelope>';
        return $resp;
    }

    private function get_message_type($feed_type){
        $response = '';
	switch($feed_type){
            case '_POST_PRODUCT_DATA_':
                $response = 'Product';
		break;
            case '_POST_PRODUCT_RELATIONSHIP_DATA_':
                $response = 'Relationship';
		break;
            case '_POST_PRODUCT_OVERRIDES_DATA_':
                $response = 'Override';
		break;
            case '_POST_PRODUCT_IMAGE_DATA_':
                $response = 'ProductImage';
		break;
            case '_POST_PRODUCT_PRICING_DATA_':
                $response = 'Price';
                break;
            case '_POST_INVENTORY_AVAILABILITY_DATA_':
                $response = 'Inventory';
		break;
            case '_POST_ORDER_ACKNOWLEDGEMENT_DATA_':
                $response = 'OrderAcknowledgement';
		break;
            case '_POST_ORDER_FULFILLMENT_DATA_':
                $response = 'OrderFulfillment';
		break;
            case '_POST_PRODUCT_OVERRIDES_DATA_':
                $response = 'Override';
        }
        return $response;
    }

    private function filter_description_data($content){
        /*$pos_start = stripos($content, '<a');
        while($pos_start!==false){
            $pos_end = stripos($content, '</a>', $pos_start);
            if ($pos_end!==false){
                if ($pos_start===0){
                    $content = substr($content, $pos_end + 4);
                } else {
                    $content = substr($content, 0, $pos_start) . substr($content, $pos_end + 4);
                }
                $pos_start = stripos($content, '<a');
            }
        }

        $pos_start = stripos($content, '<img');
        while($pos_start!==false){
            $pos_end = stripos($content, '/>', $pos_start);
            if ($pos_end!==false){
                if ($pos_start===0){
                    $content = substr($content, $pos_end+2);
                } else {
                    $content = substr($content, 0, $pos_start) . substr($content, $pos_end+2);
                }
                $pos_start = stripos($content, '<img');
            }
        }

        $pos_start = stripos($content, '<object');
        while($pos_start!==false){
            $pos_end = stripos($content, '</object>', $pos_start);
            if ($pos_end!==false){
                if ($pos_start===0){
                    $content = substr($content, $pos_end+9);
                } else {
                    $content = substr($content, 0, $pos_start) . substr($content, $pos_end+9);
                }
                $pos_start = stripos($content, '<object');
            }
        }

        $pos_start = stripos($content, '<iframe');
        while($pos_start!==false){
            $pos_end = stripos($content, '</iframe>', $pos_start);
            if ($pos_end!==false){
                if ($pos_start===0){
                    $content = substr($content, $pos_end+9);
                } else {
                    $content = substr($content, 0, $pos_start) . substr($content, $pos_end+9);
                }
                $pos_start = stripos($content, '<iframe');
            }
        }*/

        if (strlen($content)>2000){
            $content = substr($content, 0, 2000);
        }

        $content = stripslashes(htmlspecialchars(utf8_encode($content)));
        return $content;
    }

    public function create_product_feed_xml($products_col = array()){
        $xml = $this->get_feed_header('_POST_PRODUCT_DATA_');

        $models_str = '';
        if (count($products_col)){
            foreach($products_col as $model){
                $models_str .= "'" . $model . "', ";
            }
            $models_str = substr($models_str, 0, -2);
        }

        $count = 0;
        $sql_query = tep_db_query("select a.products_id, a.products_model, a.parent_products_model, a.variation_theme_id, a.base_price, a.products_weight, a.is_amazon_ok, a.amazon_category_id,
                                  a.products_status, b.products_name, b.products_description,
                                  c.manufacturers_name, pe.upc_ean
                                  from products a " .
                                  " inner join " . TABLE_PRODUCTS_DESCRIPTION . " b on (a.products_id=b.products_id and b.language_id='1')
                                  left join " . TABLE_MANUFACTURERS . " c on a.manufacturers_id=c.manufacturers_id
                                  left join products_extended pe on a.products_id=pe.osc_products_id " .
                                  (!empty($models_str) ? " where a.products_model in (" . $models_str . ") " : " where a.is_amazon_ok='1' limit 20000 ") );
        while ($sql_info = tep_db_fetch_array($sql_query)){
            if ($sql_info['products_status']=='1' && $sql_info['is_amazon_ok']=='1' && amazon_manager::category_is_ok_for_amazon($sql_info['products_id'])){
                $upc_ean_str = '';
		if (!empty($sql_info['upc_ean'])){
                    if (strlen($sql_info['upc_ean'])>7 && strlen($sql_info['upc_ean'])<=12){
                        $upc_ean_str = '<StandardProductID><Type>UPC</Type><Value>' . $sql_info['upc_ean'] . '</Value></StandardProductID>';
                    } elseif (strlen($sql_info['upc_ean'])==13){
                        $upc_ean_str = '<StandardProductID><Type>EAN</Type><Value>' . $sql_info['upc_ean'] . '</Value></StandardProductID>';
                    } 
                } elseif (strlen($sql_info['products_model'])>7 && strlen($sql_info['products_model'])<=16){ 
                      $upc_ean_str = '<StandardProductID><Type>UPC</Type><Value>' . $sql_info['products_model'] . '</Value></StandardProductID>';  
                    }
        
        $variation_exist = false;
        $variation_data =''; 
        $parentage='';       
       //parent/child products code 
       if (!empty($sql_info['parent_products_model']) ) {
                $parentage = 'child';
        if (!isset($variation_theme[$sql_info['parent_products_model']])) {
          $theme_query = tep_db_query("select p.products_id,p.variation_theme_id, vt.variation_theme_name from products p, amazon_variation_themes vt where p.variation_theme_id=vt.variation_theme_id and p.products_model = '" . $sql_info['parent_products_model'] . "'");
        if (tep_db_num_rows($theme_query) >0) {
        $theme = tep_db_fetch_array($theme_query);
        $variation_theme[$sql_info['parent_products_model']] = $variation_theme[$sql_info['products_model']] = $theme['variation_theme_name'];
         }
        } 
        if (isset($variation_theme[$sql_info['parent_products_model']])) {
         $variation_exist = true; 
         $variation_theme[$sql_info['products_model']] = $variation_theme[$sql_info['parent_products_model']] ;
         $variations_query = tep_db_query("select pv.*, av.variation_name from products_variations pv, amazon_variations av where pv.variation_id=av.variation_id and pv.products_id='" . (int)$sql_info['products_id']  . "'");
         if (tep_db_num_rows($variations_query) >0) {
            while ($variations = tep_db_fetch_array($variations_query)) {
              $variation_data .= '<'  . $variations['variation_name'] . '>' . $this->filter_description_data($variations['value']) .   '</'  . $variations['variation_name'] . '>' ;
            }
           } 
         }
       } else {
        $child_query = tep_db_query("select products_id from products where parent_products_model = '" . $sql_info['products_model'] . "' and is_amazon_ok='1'");
        if (tep_db_num_rows($child_query) >0 && $sql_info['variation_theme_id'] >0) {
            $variation_exist = true;
            $parentage = 'parent';
            if (!isset($variation_theme[$sql_info['products_model']])) {
            $theme_query = tep_db_query("select variation_theme_name from amazon_variation_themes  where variation_theme_id='" . $sql_info['variation_theme_id'] . "'");
           if (tep_db_num_rows($theme_query) >0) {
            $theme = tep_db_fetch_array($theme_query);
            $variation_theme[$sql_info['products_model']] = $theme['variation_theme_name'];
           }
        } 
       }       
      }
 
 $product_data ='';     
 if ($variation_exist)  {
    $product_data .= '<Sports>' .
                       '<VariationData>' .
                         '<Parentage>' . $parentage . '</Parentage>' .
                         '<VariationTheme>' . $variation_theme[$sql_info['products_model']] . '</VariationTheme>' ;
   if  ($variation_data != '') 
     $product_data .= $variation_data;
   $product_data .= '</VariationData>'.
                     '</Sports>' ;                      
                       
 }  else {
   $product_data .= '<Sports />'; 
 } 
 
 $item_type='';
 
 if ($sql_info['amazon_category_id'] > 0) {
    $amazon_cat_query = tep_db_query("select item_type from amazon_tree_guide where id='" . (int)$sql_info['amazon_category_id'] . "'");
    $result = tep_db_fetch_array($amazon_cat_query);
    $item_type = $result['item_type'];
 } else {
   $id_query = tep_db_query("select c.amazon_category_id from products_to_categories p2c left join categories c on p2c.categories_id=c.categories_id where p2c.products_id='" . $sql_info['products_id'] . "' and amazon_category_id>0");
   if (tep_db_num_rows($id_query) >0) {
     $id = tep_db_fetch_array($id_query);
     $amazon_cat_query = tep_db_query("select item_type from amazon_tree_guide where id='" . (int)$id['amazon_category_id'] . "'");
     $result = tep_db_fetch_array($amazon_cat_query);
     $item_type = $result['item_type'];
   }
 }
		$xml .= '<Message>' .
                            '<MessageID>' . ++$count . '</MessageID>' .
                            '<OperationType>Update</OperationType>' .
                            '<Product>' .
                                '<SKU>' . htmlspecialchars($sql_info['products_model']) . '</SKU>' .
                                $upc_ean_str .
				'<ProductTaxCode>A_SPORT_MISCSPORTS1</ProductTaxCode>' .
                                '<DescriptionData>' .
                                    '<Title>' . $this->filter_description_data($sql_info['products_name']) . '</Title>' .
                                    '<Description>' . $this->filter_description_data($sql_info['products_description']) . '</Description>' .
                                    '<MSRP currency="' . CURRENCY . '">' . number_format($sql_info['base_price'], 2, '.', '') . '</MSRP>' .
                                    (!empty($sql_info['manufacturers_name']) ? '<Manufacturer>' . htmlspecialchars($sql_info['manufacturers_name']) .  '</Manufacturer>' : '') .
                                    ($item_type != '' ? '<ItemType>' . $item_type . '</ItemType>': '') .
                                '</DescriptionData>' .
                                '<ProductData>' .
                                   $product_data .
                                '</ProductData>' .
                            '</Product>' .
                        '</Message>';

            } else {
                $xml .= '<Message>' .
                            '<MessageID>' . ++$count . '</MessageID>' .
                            '<OperationType>Delete</OperationType>' .
                            '<Product>' .
                                '<SKU>' . $this->filter_description_data($sql_info['products_model']) . '</SKU>' .
                            '</Product>' .
                        '</Message>';
            }
        }

        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_PRODUCT_DATA_');
    }

    public function create_relationships_feed_xml(){
        $xml = $this->get_feed_header('_POST_PRODUCT_RELATIONSHIP_DATA_');

        $count = 0;
        
        $sql_query = tep_db_query("select distinct(parent_products_model) from products where is_amazon_ok='1' and parent_products_model is not NULL limit 20000 ");

        while ($parent = tep_db_fetch_array($sql_query)){
            $xml .= '<Message>' .
                        '<MessageID>' . ++$count . '</MessageID>' .
			'<Relationship>' .
                            '<ParentSKU>' . htmlspecialchars($parent['parent_products_model']) . '</ParentSKU>';

            $sql_1 = tep_db_query("select a.products_id, a.products_model
                                   from products a where
                                   a.parent_products_model='" . $parent['parent_products_model'] . "' and
                                   a.products_status='1' and a.is_amazon_ok='1' ");
            while ($child = tep_db_fetch_array($sql_1)){
                $xml .= '<Relation>' .
                            '<SKU>'  . htmlspecialchars($child['products_model']) . '</SKU>' .
                            '<Type>Variation</Type>' .
                         '</Relation>';
            }

            $xml .= '</Relationship></Message>';
        }

        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_PRODUCT_RELATIONSHIP_DATA_');
    }

    public function set_markup_coefficient($val){
        if ($val>=1) $this->markup_coefficient = $val;
    }

    public function get_markup_coefficient(){
        return $this->markup_coefficient;
    }

    public function create_price_feed_xml(){
        $xml = $this->get_feed_header('_POST_PRODUCT_PRICING_DATA_');

        $count = 0;
        $sql_query = tep_db_query("select a.products_model, if (a.manual_price>0, a.manual_price, a.base_price) as products_price
                                  from " .  TABLE_PRODUCTS . " a
                                  where a.products_status='1' and a.is_amazon_ok='1' limit 20000");
        while ($sql_info = tep_db_fetch_array($sql_query)){
            $marked_up_price = $sql_info['products_price'] * $this->markup_coefficient;
            $xml .= '<Message>' .
                        '<MessageID>' . ++$count . '</MessageID>' .
                        '<Price>' .
                            '<SKU>' . $this->filter_description_data($sql_info['products_model']) . '</SKU>' .
                                '<StandardPrice currency="' . CURRENCY . '">' .  number_format($marked_up_price, 2, '.', '') . '</StandardPrice>' .
                            '</Price>' .
                        '</Message>';
        }

        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_PRODUCT_PRICING_DATA_');
    }

    public function create_inventory_feed_xml(){
       $xml = $this->get_feed_header('_POST_INVENTORY_AVAILABILITY_DATA_');
        $count = 0;
                
        $sql_query = tep_db_query("select a.products_model, a.products_quantity
                                  from products a where
                                  a.products_status='1' and       
                                a.is_amazon_ok='1' and a.products_quantity>='2' limit 20000");
        while ($sql_info = tep_db_fetch_array($sql_query)){
            //$products_quantity = (((int)$sql_info['products_quantity'] >= 0) ? $sql_info['products_quantity'] : '0');
			$products_quantity = (($sql_info['products_quantity'] >= MINIMUM_INVENTORY_LEVEL) ? $sql_info['products_quantity'] : '0');
            $availability = ($products_quantity=='0' ? '<Available>false</Available>' :  '<Quantity>' . $products_quantity . '</Quantity>');
            $xml .= '<Message>' .
                        '<MessageID>' . ++$count . '</MessageID>' .
                        '<OperationType>Update</OperationType>' .
			'<Inventory>' .
                            '<SKU>' . $this->filter_description_data($sql_info['products_model']) . '</SKU>' .
                            $availability .
							'<FulfillmentLatency>3</FulfillmentLatency>' .
                        '</Inventory>' .
                    '</Message>';
        }
        
        
        $xml .= $this->get_feed_footer();

        return $this->save_xml_to_outgoing_dir($xml, '_POST_INVENTORY_AVAILABILITY_DATA_');
    }

    public function file_urlencode($path){
        return str_replace(' ', '%20', $path);
    }

    public function create_image_feed_xml(){
        $xml = $this->get_feed_header('_POST_PRODUCT_IMAGE_DATA_');

        $count = 0;
        $sql_query = tep_db_query("select a.products_id, a.products_model, a.products_image,
                                  a.products_largeimage from " .
                                  TABLE_PRODUCTS . " a
                                  where a.products_status='1' and a.is_amazon_ok='1' limit 20000");
        while ($sql_info = tep_db_fetch_array($sql_query)){
            $image_name = $sql_info['products_largeimage'];
            if (tep_not_null($sql_info['products_largeimage']) && !empty($sql_info['products_largeimage'])) {
                $feed_status = amazon_manager::is_xml_feed_product($sql_info['products_id']);
                    if ($feed_status &&  (strpos($image_name, 'http://') || strpos($image_name,'https://'))) {
                        $image_name = $image_name;
                    }  else{
                        $image_name = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $image_name;
                    }


                    $xml .= '<Message>' .
                                '<MessageID>' . ++$count . '</MessageID>' .
                                '<OperationType>Update</OperationType>' .
                                '<ProductImage>' .
                                    '<SKU>' . $this->filter_description_data($sql_info['products_model']) . '</SKU>' .
                                    '<ImageType>Main</ImageType>' .
                                    '<ImageLocation>' . htmlspecialchars($image_name) . '</ImageLocation>' .
                                '</ProductImage>' .
                            '</Message>';
            } else {
                /*
                    $xml .= '<Message>' .
                                '<MessageID>' . $count++ . '</MessageID>' .
                                '<OperationType>Delete</OperationType>' .
                                '<ProductImage>' .
                                    '<SKU>' . $this->filter_description_data($sql_info['products_model']) . '</SKU>' .
                                    '<ImageType>Main</ImageType>' .
                                '</ProductImage>' .
                            '</Message>';
                    */
            }
        }

        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_PRODUCT_IMAGE_DATA_');
    }

    public function create_order_acknowledgement_feed_xml($amazon_order_id, $osc_order_id = ''){
        $xml = $this->get_feed_header('_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
        $xml .= '<Message>
                    <MessageID>1</MessageID>
                    <OrderAcknowledgement>
                        <AmazonOrderID>' . $amazon_order_id . '</AmazonOrderID>' .
                        (!empty($osc_order_id) ?
                        '<MerchantOrderID>' . $osc_order_id . '</MerchantOrderID>'
                        : '') .
                        '<StatusCode>Success</StatusCode>
                    </OrderAcknowledgement>
                </Message>';

        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
    }

    public function create_order_cancellation_feed_xml($amazon_order_id, $amazon_order_items, $cancel_reason){
        $xml = $this->get_feed_header('_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
        $xml .= '<Message>
                    <MessageID>1</MessageID>
                    <OrderAcknowledgement>
                        <AmazonOrderID>' . $amazon_order_id . '</AmazonOrderID>
                        <StatusCode>Failure</StatusCode>';
        foreach($amazon_order_items as $amazon_order_item_code){
            $xml .=     '<Item>
                            <AmazonOrderItemCode>' . $amazon_order_item_code . '</AmazonOrderItemCode>
                            <CancelReason>' . $cancel_reason . '</CancelReason>
                        </Item>';
        }

        $xml .=     '</OrderAcknowledgement>
                </Message>';

        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
    }

    public function create_order_fulfillment_feed_xml($orders_id){
        $sql = tep_db_query("select amazon_order_id, shipment_date from amazon_fulfillment_info where orders_id='" . (int)$orders_id . "'");
        if (tep_db_num_rows($sql)){
            $info = tep_db_fetch_array($sql);
            $amazon_order_id = $info['amazon_order_id'];
            $shipment_date = $info['shipment_date'];
            if (strlen($shipment_date)==8){
                $year = substr($shipment_date, 0, 4);
                $month = substr($shipment_date, 4, 2);
                $day = substr($shipment_date, 6, 2);
                $shipment_date_ts = mktime(0, 0, 0, $month, $day, $year);
            } else {
                $shipment_date_ts = time();
            }


            $xml = $this->get_feed_header('_POST_ORDER_FULFILLMENT_DATA_');
            $xml.= $this->get_feed_footer();
            $xml .= '<Message>
                        <MessageID>1</MessageID>
                            <OrderFulfillment>
                                <AmazonOrderID>' . $amazon_order_id . '</AmazonOrderID>
                                <FulfillmentDate>' . date('c', $shipment_date_ts) . '</FulfillmentDate>';
            $xml .=     '</OrderFulfillment>
                    </Message>';
            tep_db_query("update amazon_fulfillment_info set status='1', datetime_moved_to_amazon=now() where orders_id='" . (int)$orders_id . "'");
            return $this->save_xml_to_outgoing_dir($xml, '_POST_ORDER_FULFILLMENT_DATA_');
        } else {
            return '';
        }

        /*$sql = tep_db_query("select a.ship_agent, a.ship_method, a.tracking_id, a.shipment_date_string,
                            a.order_asn_id, c.amazon_order_id
                            from suppliers_order_asn a
                            inner join suppliers_order_feed b on a.order_feed_id=b.order_feed_id
                            inner join amazon_orders c on b.orders_id=c.orders_id
                            where b.orders_id='" . $orders_id . "' and b.suppliers_id='" . $this->supplier_id . "'
                            order by a.date_added desc, a.order_asn_id desc limit 0, 1");
        $info = tep_db_fetch_array($sql);
	$ship_agent = $info['ship_agent'];
	$ship_method = $info['ship_method'];
	$tracking_id = $info['tracking_id'];
	$shipment_date_string = $info['shipment_date_string'];
	$shipment_date = date('c', mktime(0, 0, 0, substr($shipment_date_string, 4, 2), substr($shipment_date_string, 6, 2), substr($shipment_date_string, 0, 4)));
	$order_asn_id = $info['order_asn_id'];
	$amazon_order_id = $info['amazon_order_id'];

        $sql = tep_db_query("select amazon_ship_type, carrier_code from suppliers_shipping where suppliers_id='" . $this->supplier_id . "' and shipping_method='" . $ship_method . "'");
        $info = tep_db_fetch_array($sql);
	$amazon_ship_type = $info['amazon_ship_type'];
	$carrier_code = $info['carrier_code'];

        $xml = $this->get_feed_header('_POST_ORDER_FULFILLMENT_DATA_');

        $xml .= '<Message>
                    <MessageID>1</MessageID>
                        <OrderFulfillment>
                            <AmazonOrderID>' . $amazon_order_id . '</AmazonOrderID>
                            <FulfillmentDate>' . $shipment_date . '</FulfillmentDate>
                            <FulfillmentData>'  .
                                (!empty($carrier_code)
                                    ?
                                    '<CarrierCode>' . $carrier_code . '</CarrierCode>'
                                    :
                                    '<CarrierName>' . $ship_agent . '</CarrierName>'
                                ) .
                                '<ShippingMethod>' . $amazon_ship_type . '</ShippingMethod>
                                <ShipperTrackingNumber>' . $tracking_id . '</ShipperTrackingNumber>
                            </FulfillmentData>';

	$sql = tep_db_query("select a.amazon_order_item_code, a.orders_products_id, b.products_quantity
                            from amazon_orders_products a
                            inner join orders_products op on a.orders_products_id=op.orders_products_id
                            inner join suppliers_order_asn_products b on a.sku=concat('" . $this->supplier_prefix . "', b.supplier_products_model)
                            where op.orders_id='" . (int) $orders_id . "' and b.order_asn_id ='" . $order_asn_id . "'");

        while ($info = tep_db_fetch_array($sql)){
            $xml .=         '<Item>
                                <AmazonOrderItemCode>' . $info['amazon_order_item_code'] . '</AmazonOrderItemCode>
                                <Quantity>' . $info['products_quantity'] . '</Quantity>
                            </Item>';
	}
        $xml .=     '</OrderFulfillment>
                </Message>';
        $xml.= $this->get_feed_footer();
        return $this->save_xml_to_outgoing_dir($xml, '_POST_ORDER_FULFILLMENT_DATA_');
        */
    }

    private function save_xml_to_outgoing_dir(&$xml, $feed_type){
        $filename = AMAZON_OUTGOING_DIRECTORY . $feed_type . time() . '.xml';
        $feedHandle = @fopen($filename , 'w+');
        fwrite($feedHandle, $xml);
        fclose($feedHandle);
        if (file_exists($filename)){
            return $filename;
        } else {
            return '';
        }
    }

    public function submit_product_feed($products_col = array()){
        $path_to_product_feed = $this->create_product_feed_xml($products_col);
        $this->submit_feed($path_to_product_feed, '_POST_PRODUCT_DATA_');
    }

    public function submit_relationships_feed(){
        $path_to_relationships_feed = $this->create_relationships_feed_xml();
        $this->submit_feed($path_to_relationships_feed, '_POST_PRODUCT_RELATIONSHIP_DATA_');
    }

    public function submit_price_feed(){
        $path_to_price_feed = $this->create_price_feed_xml();
        $this->submit_feed($path_to_price_feed, '_POST_PRODUCT_PRICING_DATA_');
    }

    public function submit_inventory_feed(){
        $path_to_inventory_feed = $this->create_inventory_feed_xml();
        $this->submit_feed($path_to_inventory_feed, '_POST_INVENTORY_AVAILABILITY_DATA_');
    }

    public function submit_image_feed(){
        $path_to_image_feed = $this->create_image_feed_xml();
        $this->submit_feed($path_to_image_feed, '_POST_PRODUCT_IMAGE_DATA_');
    }

    public function submit_order_acknowledgement_feed($amazon_order_id, $osc_order_id = ''){
        $path_to_order_ack_feed = $this->create_order_acknowledgement_feed_xml($amazon_order_id, $osc_order_id);
        $this->submit_feed($path_to_order_ack_feed, '_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
    }

    public function submit_order_cancellation_feed($amazon_order_id, $amazon_order_items = array(), $cancel_reason = ''){
        $path_to_order_canx_feed = $this->create_order_cancellation_feed_xml($amazon_order_id, $amazon_order_items, $cancel_reason);
        $this->submit_feed($path_to_order_canx_feed, '_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
    }

    public function submit_order_fulfillment_feed($orders_id){
        $path_to_order_fulfillment_feed = $this->create_order_fulfillment_feed_xml($orders_id);
        if (!empty($path_to_order_fulfillment_feed)){
            $this->submit_feed($path_to_order_fulfillment_feed, '_POST_ORDER_FULFILLMENT_DATA_');
        }
    }

    public function submit_feed($path, $feed_type){
        if (!empty($path)){
            $feedHandle = @fopen($path, 'r');
            $parameters = array (   'Merchant'          => MERCHANT_ID,
                                    'MarketplaceIdList' => array('Id' => array(AMAZON_MARKETPLACE_ID)),
                                    'FeedType'          => $feed_type,
                                    'FeedContent'       => $feedHandle,
                                    'PurgeAndReplace'   => false,
                                    'ContentMd5'        => base64_encode(md5(stream_get_contents($feedHandle), true)),
                                );
            $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
            $response = $this->invoke_submit_feed($this->service, $request);
            if ($response){
                $sql_data = array('filename' => basename($path),
                                'transaction_id' => $response['submission_id'],
                                'document_id' => $response['submission_id'],
                                'date_submitted' => 'now()',
                                'feed_type' => $response['feed_type'],);
                if (!empty($response['error'])){
                    $sql_data['submission_error'] = $response['error'];
                }
                tep_db_perform('amazon_feed', $sql_data);
            }
        }
    }

    public function fetch_feed_submission_results(){
        $sql = tep_db_query("select transaction_id from amazon_feed where response_id='0' and status='0' and submission_error is null");
        while ($entry = tep_db_fetch_array($sql)){
            $this->get_result_by_feed_submission_id($entry['transaction_id']);
        }
    }

    public function get_result_by_feed_submission_id($feed_submission_id){
        if (!empty($feed_submission_id)){
            $response_file_name = AMAZON_RESPONSE_DIRECTORY . $feed_submission_id . '_' . time() . '.xml';
            $parameters = array('Merchant'              => MERCHANT_ID,
                                'FeedSubmissionId'      => $feed_submission_id,
                                'FeedSubmissionResult'  =>  @fopen($response_file_name, 'w+'),
                                );
            $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);

            $this->invoke_get_feed_submission_result($this->service, $request);

            if (@$xml = simplexml_load_file($response_file_name)){
                $response_id = (string)$xml->Message->ProcessingReport->DocumentTransactionID;
                $status_code = (string)$xml->Message->ProcessingReport->StatusCode;
                if (!empty($response_id) && $status_code=='Complete'){
                    $sql_data = array('response_id' => $response_id,
                                      'response_file_name' => basename($response_file_name),
                                      'status' => '1');
                    tep_db_perform('amazon_feed', $sql_data, 'update', "transaction_id='" . $feed_submission_id . "'");

                    $sql = tep_db_query("select feed_type from amazon_feed where transaction_id='" . $feed_submission_id . "'");
                    $info = tep_db_fetch_array($sql);
                    if ($info['feed_type']=='_POST_ORDER_FULFILLMENT_DATA_'){
                        $content = file_get_contents($response_file_name);
                        tep_mail('tech1', 'tech1@outdoorbusinessnetwork.com', 'amazon POFD reesponse#' . $doc_id, $val , 'auto', 'tech1@outdoorbusinessnetwork.com');
                    }
                }
            }
        }
    }

    public function fetch_n_register_amazon_order($amazon_order_id){
        $request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
        $request->setSellerId(MERCHANT_ID);
        $orderIds = new MarketplaceWebServiceOrders_Model_OrderIdList();
        $orderIds->setId(array($amazon_order_id));
        $request->setAmazonOrderId($orderIds);
        $response = $this->invoke_get_order($this->service, $request);
        $this->register_orders_to_osc($response['orders']);
    }

    public function get_new_orders($created_after_ts){
        $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
        $request->setSellerId(MERCHANT_ID);
        $request->setCreatedAfter(new DateTime(date('Y-m-d H:i:s', $created_after_ts)));
        $status = new MarketplaceWebServiceOrders_Model_OrderStatusList();
        //$status->setStatus(array('Unshipped', 'PartiallyShipped'));
        $status->setStatus(array('Unshipped', 'PartiallyShipped', 'Shipped'));
        $request->setOrderStatus($status);

        $marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
        $marketplaceIdList->setId(array(AMAZON_MARKETPLACE_ID));
        $request->setMarketplaceId($marketplaceIdList);

        $response = $this->invoke_list_orders($this->service, $request);
	//print_r($response);
        $this->register_orders_to_osc($response['orders']);

        /*while(empty($response['error']) && !empty($response['next_token'])){
            unset($request);
            $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
            $request->setSellerId(MERCHANT_ID);
            $request->setNextToken($response['next_token']);
            $this->invoke_list_orders_by_next_token($service, $request, $response);
        }
        print_r($response);*/
    }

    public function get_order_items($amazon_order_id){
        $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
        $request->setSellerId(MERCHANT_ID);
        $request->setAmazonOrderId($amazon_order_id);
        $response = $this->invoke_list_order_items($this->service, $request);
        return $response;
    }

    public function register_orders_to_osc($orders){
	//require_once(DIR_FS_ROOT . 'public_html/ad_vision29/includes/classes/currencies.php');
	require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'currencies.php');
        $currencies = new currencies();

        $num = count($orders);
        for($i=0; $i<$num; $i++){
            $order = $orders[$i];
            //print_r($order);
            if (!amazon_manager::amazon_order_exists($order['amazon_order_id'])){
                $order_items = $this->get_order_items($order['amazon_order_id']);
                if ($order_items['items']){
                    $purchase_date = date('Y-m-d H:i:s', strtotime($order['purchase_date']));

                    $customers_id = amazon_manager::customer_exists_in_osc($order['buyer_email']);
                    if (empty($customers_id)){
                        $cstmr_data = array('customers_firstname'       => $order['buyer_name'],
                                            'customers_lastname'        => $order['buyer_name'],
                                            'customers_email_address'   => $order['buyer_email'],
                                            'customers_telephone'       => $order['shipping_address']['phone'], //api does not have property that holds buyer's phone although such property exieted earlier
                                            'customers_password'        => md5(time()), //NOTICE: is it correct? let's pass some dummy password
                                            'customers_nickname'        => $order['buyer_name']);
                        tep_db_perform('customers', $cstmr_data);
                        $customers_id = tep_db_insert_id();

                        $cstmr_info_data = array('customers_info_id'                    => $customers_id,
                                                 'customers_info_date_account_created'  => 'now()');
                        tep_db_perform('customers_info', $cstmr_info_data);

                        $sql = tep_db_query("select address_book_id from address_book where
                                            customers_id='" . $customers_id . "' and
                                            entry_firstname='" . tep_db_input($order['buyer_name']) . "' and
                                            entry_street_address='" . tep_db_input($order['shipping_address']['address1']) . "' and
                                            entry_suburb='" . tep_db_input($order['shipping_address']['address2'] . (!empty($order['shipping_address']['addrss3']) ? ', ' . $order['shipping_address']['address3'] : '')) . "' and
                                            entry_postcode='" . $order['shipping_address']['postal_code'] . "' and
                                            entry_city='" . tep_db_input($order['shipping_address']['city']) . "' and
                                            entry_state='" . tep_db_input($order['shipping_address']['state']) . "'");
                        if (tep_db_num_rows($sql)){
                            $sql_info = tep_db_fetch_array($sql);
                            $address_id = $sql_info['address_book_id'];
                        } else {
                            $adrs_book_data = array('customers_id'          => $customers_id,
                                                    'entry_firstname'       => $order['buyer_name'],
                                                    'entry_lastname'        => $order['buyer_name'],
                                                    'entry_street_address'  => $order['shipping_address']['address1'],
                                                    'entry_suburb'          => $order['shipping_address']['address2'] . (!empty($order['shipping_address']['addrss3']) ? ', ' . $order['shipping_address']['address3'] : ''),
                                                    'entry_postcode'        => $order['shipping_address']['postal_code'],
                                                    'entry_city'            => $order['shipping_address']['city'],
                                                    'entry_state'           => $order['shipping_address']['state'],
                                                    'entry_country_id'      => '223');
                            tep_db_perform('address_book', $adrs_book_data);
                            $address_id = tep_db_insert_id();
                        }
                        tep_db_query("update customers set customers_default_address_id='" . $address_id . "' where customers_id='" . $customers_id . "'");
                    }

                    $ord_data = array('customers_id'                => $customers_id,
                                      'customers_name'              => $order['buyer_name'],
                                      'customers_street_address'    => $order['shipping_address']['address1'],
                                      'customers_suburb'            => $order['shipping_address']['address2'] . (!empty($order['shipping_address']['addrss3']) ? ', ' . $order['shipping_address']['address3'] : ''),
                                      'customers_city'              => $order['shipping_address']['city'],
                                      'customers_postcode'          => $order['shipping_address']['postal_code'],
                                      'customers_state'             => $order['shipping_address']['state'],
                                      'customers_country'           => $order['shipping_address']['country_code'],
                                      'customers_telephone'         => $order['shipping_address']['phone'],
                                      'customers_email_address'     => $order['buyer_email'],
                                      'customers_address_format_id' => '1',
                                      'delivery_name'               => $order['shipping_address']['name'],
                                      'delivery_street_address'     => $order['shipping_address']['address1'],
                                      'delivery_suburb'             => $order['shipping_address']['address2'] . (!empty($order['shipping_address']['addrss3']) ? ', ' . $order['shipping_address']['address3'] : ''),
                                      'delivery_city'               => $order['shipping_address']['city'],
                                      'delivery_postcode'           => $order['shipping_address']['postal_code'],
                                      'delivery_state'              => $order['shipping_address']['state'],
                                      'delivery_country'            => $order['shipping_address']['country_code'],
                                      'delivery_address_format_id'  => '1',
                                      'billing_name'                => $order['buyer_name'],
                                      'billing_street_address'      => $order['shipping_address']['address1'],
                                      'billing_suburb'              => $order['shipping_address']['address2'] . (!empty($order['shipping_address']['addrss3']) ? ', ' . $order['shipping_address']['address3'] : ''),
                                      'billing_city'                => $order['shipping_address']['city'],
                                      'billing_postcode'            => $order['shipping_address']['postal_code'],
                                      'billing_state'               => $order['shipping_address']['state'],
                                      'billing_country'             => $order['shipping_address']['country_code'],
                                      'billing_address_format_id'   => '1',
                                      'date_purchased'              => $purchase_date,
                                      'last_modified'               => 'now()',
                                      'currency'                    => 'USD',
                                      'currency_value'              => '1',
                                      'payment_method'              => AMAZON_PAYMENT_METHOD,
                                      'cc_cvv'                      => '',
                                      'orders_status'               => (strtolower($order['order_status'])=='shipped' ? AMAZON_ORDER_STATUS_SHIPPED : AMAZON_ORDER_STATUS_UNSHIPPED),);
                    tep_db_perform('orders', $ord_data);
                    $orders_id = tep_db_insert_id();

                    $ord_status_data = array('orders_id'        => $orders_id,
                                             'orders_status_id' => (strtolower($order['order_status'])=='shipped' ? AMAZON_ORDER_STATUS_SHIPPED : AMAZON_ORDER_STATUS_UNSHIPPED),
                                             'date_added'       => 'now()',
                                             'customer_notified' =>'0',
                                             'comments' => '');
                    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $ord_status_data);

                    $amz_data = array   ('orders_id'                => $orders_id,
                                        'amazon_doc_id'             => '',
					'amazon_order_id'           => $order['amazon_order_id'],
					'amazon_session_id'         => $order['amazon_order_id'],
					'order_date'                => $purchase_date,
					'order_posted_date'         => $purchase_date,
					'buyer_email_address'       => $order['buyer_email'],
					'buyer_name'                => $order['buyer_name'],
					'buyer_phone_number'        => $order['shipping_address']['phone'],
					'delivery_name'             => $order['shipping_address']['name'],
					'address_field_one'         => $order['shipping_address']['address1'],
					'address_field_two'         => $order['shipping_address']['address2'] . (!empty($order['shipping_address']['addrss3']) ? ', ' . $order['shipping_address']['address3'] : ''),
					'city'                      => $order['shipping_address']['city'],
					'state'                     => $order['shipping_address']['state'],
					'postal_code'               => $order['shipping_address']['postal_code'],
					'country'                   => $order['shipping_address']['country_code'],
					'phone_number'              => $order['shipping_address']['phone'],
					'fulfillment_method'        => $order['fullfillment_channel'],
					'fulfillment_service_level' => $order['ship_service_level'],
					'date_added'                => 'now()',
					'last_modified'             => 'now()');
                    tep_db_perform('amazon_orders', $amz_data);

                    $sub_total = 0;
                    $tax = 0;
                    $shipping_price = 0;
                    $promotion_discount = 0;
                    for($j=0; $j<count($order_items['items']); $j++){
                        $item = $order_items['items'][$j];
                        $osc_products_id = amazon_manager::get_osc_products_id_by_sku($item['sku']);

                        $sub_total += (double)$item['amount'];
                        $tax += (double)$item['item_tax'];
                        $shipping_price += ((double)$item['shipping_price'] + (double)$item['shipping_tax']);
                        $promotion_discount += $item['promotion_discount'];

                        $orp_data = array(  'orders_id'                => $orders_id,
                                            'products_id'              => $osc_products_id,
                                            'products_model'           => $item['sku'],
                                            'products_name'            => $item['title'],
                                            'products_price'           => $item['amount']/$item['quantity_ordered'],
                                            'final_price'              => $item['amount']/$item['quantity_ordered'],
                                            'products_tax'             => $item['item_tax']/$item['quantity_ordered'],
                                            'products_quantity'        => $item['quantity_ordered'],);
                        tep_db_perform('orders_products', $orp_data);
                        $osc_orders_products_id = tep_db_insert_id();

			$aop_data = array('orders_products_id'           => $osc_orders_products_id,
                                          'amazon_order_item_code'       => $item['order_item_id'],
                                          'sku'                          => $item['sku'],
                                          'title'                        => $item['title'],
                                          'quantity'                     => $item['quantity_ordered'],
                                          'product_tax_code'             => '',
                                          'promotion_claim_code'         => '',
                                          'merchant_promotion_id'        => '',
                                          'promotion_component_type'     => '',
                                          'promotion_component_amount'   => '',
                                          'last_modified'                => 'now()',
                                          'date_added'                   => 'now()',
                                          'asin'                         => $item['asin'],);
			tep_db_perform('amazon_orders_products', $aop_data);

                        $sql = array('orders_products_id'               => $osc_orders_products_id,
                                     'component_type'                   => ITEM_PRICE_COMPONENT_PRINCIPAL,
                                     'component_amount'                 => $item['amount'],
                                     'date_added'                       => 'now()',
                                     'last_modified'                    => 'now()');
                        tep_db_perform('amazon_orders_products_price_components', $sql);

                        $sql = array('orders_products_id'               => $osc_orders_products_id,
                                     'component_type'                   => ITEM_PRICE_COMPONENT_SHIPPING,
                                     'component_amount'                 => $item['shipping_price'],
                                     'date_added'                       => 'now()',
                                     'last_modified'                    => 'now()');
                        tep_db_perform('amazon_orders_products_price_components', $sql);

                        $sql = array('orders_products_id'               => $osc_orders_products_id,
                                     'component_type'                   => ITEM_PRICE_COMPONENT_TAX,
                                     'component_amount'                 => $item['item_tax'],
                                     'date_added'                       => 'now()',
                                     'last_modified'                    => 'now()');
                        tep_db_perform('amazon_orders_products_price_components', $sql);

                        $sql = array('orders_products_id'               => $osc_orders_products_id,
                                     'component_type'                   => ITEM_PRICE_COMPONENT_SHIPPING_TAX,
                                     'component_amount'                 => $item['shipping_tax'],
                                     'date_added'                       => 'now()',
                                     'last_modified'                    => 'now()');
                        tep_db_perform('amazon_orders_products_price_components', $sql);

                        if (!empty($osc_products_id)){
                            tep_db_query("update products set products_quantity = products_quantity - '" . (int)$item['quantity_ordered'] . "' where products_id = '" . $osc_products_id . "'");
                        }
                    }

                    $ort_data = array('orders_id'                   => $orders_id,
                                      'title'                       => 'Sub-Total:',
                                      'text'                        => $currencies->format($sub_total),
                                      'value'                       => $sub_total,
                                      'class'                       => 'ot_subtotal',
                                      'sort_order'                  => '1',
                                      );
                    tep_db_perform('orders_total', $ort_data);

                    $ot_shipping_title = 'Shipping (Standard):';
                    if (!empty($order['ship_service_level'])){
                        $ot_shipping_title = 'Shipping (' . $order['ship_service_level'] . '):';
                    }

                    $ort_data = array('orders_id'                   => $orders_id,
                                      'title'                       => $ot_shipping_title,
                                      'text'                        => $currencies->format($shipping_price),
                                      'value'                       => $shipping_price,
                                      'class'                       => 'ot_shipping',
                                      'sort_order'                  => '2',
                                      );
                    tep_db_perform('orders_total', $ort_data);

                    if ($tax>0){
                        $ort_data = array('orders_id'               => $orders_id,
                                          'title'                   => 'TAX:',
                                          'text'                    => $currencies->format($tax),
                                          'value'                   => $tax,
                                          'class'                   => 'ot_tax',
                                          'sort_order'              => '3',
                                          );
                        tep_db_perform('orders_total', $ort_data);
                    }

                    if ($promotion_discount>0){
                        $ort_data = array('orders_id'               => $orders_id,
                                          'title'                   => 'Promotion Discount:',
                                          'text'                    => $currencies->format($promotion_discount),
                                          'value'                   => $promotion_discount,
                                          'class'                   => 'ot_discount',
                                          'sort_order'              => '4',
                                          );
                        tep_db_perform('orders_total', $ort_data);
                    }

                    $ort_data = array('orders_id'                   => $orders_id,
                                      'title'                       => 'Total:',
                                      'text'                        => '<b>' . $currencies->format(($sub_total + $shipping_price) - $promotion_discount) . '</b>',
                                      'value'                       => $sub_total + $shipping_price,
                                      'class'                       => 'ot_total',
                                      'sort_order'                  => '100',
                                      );
                    tep_db_perform('orders_total', $ort_data);

                    $amazon_temp = new amazon_manager('mws');
                    $amazon_temp->submit_order_acknowledgement_feed($order['amazon_order_id'], $orders_id);
                    unset($amazon_temp);
                }
            }
        }
    }

    public static function amazon_order_exists($amazon_order_id){
        $sql = tep_db_query("select orders_id from amazon_orders where amazon_order_id='" . $amazon_order_id . "'");
        if (tep_db_num_rows($sql)){
                return true;
        } else {
                return false;
        }
    }

    public static function customer_exists_in_osc($email){
        $customers_id = 0;
        $sql = tep_db_query("select customers_id from customers where customers_email_address='" . tep_db_input($email) . "'");
        if (tep_db_num_rows($sql)){
            $sql_info = tep_db_fetch_array($sql);
            $customers_id = $sql_info['customers_id'];
        }
        return $customers_id;
    }

    public static function category_is_ok_for_amazon($product_id){
        $resp = false;
        $sql = tep_db_query("select categories_id from products_to_categories where products_id='" . (int)$product_id . "' and categories_id > 0");
        if (tep_db_num_rows($sql)){
            while ($entry = tep_db_fetch_array($sql)){
                $sql_ = tep_db_query("select is_amazon_ok from categories where categories_id='" . $entry['categories_id'] . "'");
                $info = tep_db_fetch_array($sql_);
                if ($info['is_amazon_ok'] == '1'){
                    $resp = true;
                    break;
                }
            }
        }
        return $resp;
    }

    /*public static function get_supplier_id_by_products_model($model){
        $resp = '';
	$sql = tep_db_query("select suppliers_id, suppliers_prefix from suppliers");
	while($supplier = tep_db_fetch_array($sql)){
            $prefix = $supplier['suppliers_prefix'];
            $pos = strpos($model, $prefix);
            if ($pos!==false && $pos===0){
                $id = $supplier['suppliers_id'];
                $resp = $id;
                break;
            }
        }
        return $resp;
    }*/

    public static function get_osc_products_id_by_sku($sku){
        $products_id = '0';
	//$sql = tep_db_query("select products_id from products where products_model='" . $sku . "' or products_model like '__" . $sku . "' or products_model like '____" . $sku . "'");
        $sql = tep_db_query("select products_id from products where products_model='" . $sku . "'");
        if (tep_db_num_rows($sql)){
            $sql_info = tep_db_fetch_array($sql);
            $products_id = $sql_info['products_id'];
	}
	return $products_id;
    }

    public static function is_amazon_order($osc_order_id){
        $sql = tep_db_query("select count(*) as rowCount from amazon_orders where orders_id='" . $osc_order_id . "'");
        $sql_info = tep_db_fetch_array($sql);
        if ($sql_info['rowCount']){
            return true;
        } else {
            return false;
        }
    }

    public static function get_amazon_order_id($osc_order_id){
        $amazon_order_id = '';
        $sql = tep_db_query("select amazon_order_id from amazon_orders where orders_id='" . $osc_order_id . "'");
        if (tep_db_num_rows($sql)){
            $info = tep_db_fetch_array($sql);
            $amazon_order_id = $info['amazon_order_id'];
        }
        return $amazon_order_id;
    }

    public static function is_xml_feed_product($product_id) {
        $check_query = tep_db_query("select count(*) as total from products_extended where osc_products_id = '" . (int)$product_id . "'");
        $check = tep_db_fetch_array($check_query);
        if ($check['total'] > 0)
            return true;
        else
            return false;
    }

    private function invoke_list_order_items(MarketplaceWebServiceOrders_Interface $service, $request){
        $resp = array('next_token' => '', 'items' => array(), 'error' => '');
        try {
            $response = $service->listOrderItems($request);

            //echo ("Service Response\n");
            //echo ("=============================================================================\n");

            //echo("        ListOrderItemsResponse\n");
            if ($response->isSetListOrderItemsResult()) {
                //echo("            ListOrderItemsResult\n");
                $listOrderItemsResult = $response->getListOrderItemsResult();
                if ($listOrderItemsResult->isSetNextToken()) {
                    //echo("                NextToken\n");
                    //echo("                    " . $listOrderItemsResult->getNextToken() . "\n");
                    $resp['next_token'] = $listOrderItemsResult->getNextToken();
                }
                if ($listOrderItemsResult->isSetAmazonOrderId()) {
                    //echo("                AmazonOrderId\n");
                    //echo("                    " . $listOrderItemsResult->getAmazonOrderId() . "\n");
                }
                if ($listOrderItemsResult->isSetOrderItems()) {
                    //echo("                OrderItems\n");
                    $orderItems = $listOrderItemsResult->getOrderItems();
                    $orderItemList = $orderItems->getOrderItem();
                    foreach ($orderItemList as $orderItem) {
                        //echo("                    OrderItem\n");
                        $resp['items'][] = array('asin'                 => '',
                                                 'sku'                  => '',
                                                 'order_item_id'        => '',
                                                 'title'                => '',
                                                 'quantity_ordered'     => '',
                                                 'quantity_shipped'     => '',
                                                 'currency_code'        => '',
                                                 'amount'               => '',
                                                 'shipping_price'       => '',
                                                 'item_tax'             => '',
                                                 'shipping_tax'         => '',
                                                 'shipping_discount'    => '',
                                                 'promotion_discount'   => '', );
                        $temp_index = count($resp['items'])-1;
                        if ($orderItem->isSetASIN()) {
                            //echo("                        ASIN\n");
                            //echo("                            " . $orderItem->getASIN() . "\n");
                            $resp['items'][$temp_index]['asin'] = $orderItem->getASIN();
                        }
                        if ($orderItem->isSetSellerSKU()) {
                            //echo("                        SellerSKU\n");
                            //echo("                            " . $orderItem->getSellerSKU() . "\n");
                            $resp['items'][$temp_index]['sku'] = $orderItem->getSellerSKU();
                        }
                        if ($orderItem->isSetOrderItemId()) {
                            //echo("                        OrderItemId\n");
                            //echo("                            " . $orderItem->getOrderItemId() . "\n");
                            $resp['items'][$temp_index]['order_item_id'] = $orderItem->getOrderItemId();
                        }
                        if ($orderItem->isSetTitle()) {
                            //echo("                        Title\n");
                            //echo("                            " . $orderItem->getTitle() . "\n");
                            $resp['items'][$temp_index]['title'] = $orderItem->getTitle();
                        }
                        if ($orderItem->isSetQuantityOrdered()) {
                            //echo("                        QuantityOrdered\n");
                            //echo("                            " . $orderItem->getQuantityOrdered() . "\n");
                            $resp['items'][$temp_index]['quantity_ordered'] = $orderItem->getQuantityOrdered();
                        }
                        if ($orderItem->isSetQuantityShipped()) {
                            //echo("                        QuantityShipped\n");
                            //echo("                            " . $orderItem->getQuantityShipped() . "\n");
                            $resp['items'][$temp_index]['quantity_shipped'] = $orderItem->getQuantityShipped();
                        }
                        if ($orderItem->isSetItemPrice()) {
                            //echo("                        ItemPrice\n");
                            $itemPrice = $orderItem->getItemPrice();
                            if ($itemPrice->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $itemPrice->getCurrencyCode() . "\n");
                                $resp['items'][$temp_index]['currency_code'] = $itemPrice->getCurrencyCode();
                            }
                            if ($itemPrice->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $itemPrice->getAmount() . "\n");
                                $resp['items'][$temp_index]['amount'] = $itemPrice->getAmount();
                            }
                        }
                        if ($orderItem->isSetShippingPrice()) {
                            //echo("                        ShippingPrice\n");
                            $shippingPrice = $orderItem->getShippingPrice();
                            if ($shippingPrice->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $shippingPrice->getCurrencyCode() . "\n");
                            }
                            if ($shippingPrice->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $shippingPrice->getAmount() . "\n");
                                $resp['items'][$temp_index]['shipping_price'] = $shippingPrice->getAmount();
                            }
                        }
                        if ($orderItem->isSetGiftWrapPrice()) {
                            //echo("                        GiftWrapPrice\n");
                            $giftWrapPrice = $orderItem->getGiftWrapPrice();
                            if ($giftWrapPrice->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $giftWrapPrice->getCurrencyCode() . "\n");
                            }
                            if ($giftWrapPrice->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $giftWrapPrice->getAmount() . "\n");
                            }
                        }
                        if ($orderItem->isSetItemTax()) {
                            //echo("                        ItemTax\n");
                            $itemTax = $orderItem->getItemTax();
                            if ($itemTax->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $itemTax->getCurrencyCode() . "\n");
                            }
                            if ($itemTax->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $itemTax->getAmount() . "\n");
                                $resp['items'][$temp_index]['item_tax'] = $itemTax->getAmount();
                            }
                        }
                        if ($orderItem->isSetShippingTax()) {
                            //echo("                        ShippingTax\n");
                            $shippingTax = $orderItem->getShippingTax();
                            if ($shippingTax->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $shippingTax->getCurrencyCode() . "\n");
                            }
                            if ($shippingTax->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $shippingTax->getAmount() . "\n");
                                $resp['items'][$temp_index]['shipping_tax'] = $shippingTax->getAmount();
                            }
                        }
                        if ($orderItem->isSetGiftWrapTax()) {
                            //echo("                        GiftWrapTax\n");
                            $giftWrapTax = $orderItem->getGiftWrapTax();
                            if ($giftWrapTax->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $giftWrapTax->getCurrencyCode() . "\n");
                            }
                            if ($giftWrapTax->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $giftWrapTax->getAmount() . "\n");
                            }
                        }
                        if ($orderItem->isSetShippingDiscount()) {
                            //echo("                        ShippingDiscount\n");
                            $shippingDiscount = $orderItem->getShippingDiscount();
                            if ($shippingDiscount->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $shippingDiscount->getCurrencyCode() . "\n");
                            }
                            if ($shippingDiscount->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $shippingDiscount->getAmount() . "\n");
                                $resp['items'][$temp_index]['shipping_discount'] = $shippingDiscount->getAmount();
                            }
                        }
                        if ($orderItem->isSetPromotionDiscount()) {
                            //echo("                        PromotionDiscount\n");
                            $promotionDiscount = $orderItem->getPromotionDiscount();
                            if ($promotionDiscount->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $promotionDiscount->getCurrencyCode() . "\n");
                            }
                            if ($promotionDiscount->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $promotionDiscount->getAmount() . "\n");
                                $resp['items'][$temp_index]['promotion_discount'] = $promotionDiscount->getAmount();
                            }
                        }
                        if ($orderItem->isSetPromotionIds()) {
                            //echo("                        PromotionIds\n");
                            $promotionIds = $orderItem->getPromotionIds();
                            $promotionIdList  =  $promotionIds->getPromotionId();
                            foreach ($promotionIdList as $promotionId) {
                                //echo("                            PromotionId\n");
                                //echo("                                " . $promotionId);
                            }
                        }
                        if ($orderItem->isSetCODFee()) {
                            //echo("                        CODFee\n");
                            $CODFee = $orderItem->getCODFee();
                            if ($CODFee->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $CODFee->getCurrencyCode() . "\n");
                            }
                            if ($CODFee->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $CODFee->getAmount() . "\n");
                            }
                        }
                        if ($orderItem->isSetCODFeeDiscount()) {
                            //echo("                        CODFeeDiscount\n");
                            $CODFeeDiscount = $orderItem->getCODFeeDiscount();
                            if ($CODFeeDiscount->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $CODFeeDiscount->getCurrencyCode() . "\n");
                            }
                            if ($CODFeeDiscount->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $CODFeeDiscount->getAmount() . "\n");
                            }
                        }
                        if ($orderItem->isSetGiftMessageText()) {
                            //echo("                        GiftMessageText\n");
                            //echo("                            " . $orderItem->getGiftMessageText() . "\n");
                        }
                        if ($orderItem->isSetGiftWrapLevel()) {
                            //echo("                        GiftWrapLevel\n");
                            //echo("                            " . $orderItem->getGiftWrapLevel() . "\n");
                        }
                    }
                }
            }
            if ($response->isSetResponseMetadata()) {
                //echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) {
                    //echo("                RequestId\n");
                    //echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            }
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
             //echo("Caught Exception: " . $ex->getMessage() . "\n");
             //echo("Response Status Code: " . $ex->getStatusCode() . "\n");
             //echo("Error Code: " . $ex->getErrorCode() . "\n");
             //echo("Error Type: " . $ex->getErrorType() . "\n");
             //echo("Request ID: " . $ex->getRequestId() . "\n");
             //echo("XML: " . $ex->getXML() . "\n");
            $resp['error'] = $ex->getMessage();
        }
        return $resp;
    }

    private function invoke_get_order(MarketplaceWebServiceOrders_Interface $service, $request){
        $resp = array('next_token' => '', 'orders' => array(), 'error' => '');
        try {
              $response = $service->getOrder($request);

                //echo ("Service Response\n");
                //echo ("=============================================================================\n");

                //echo("        GetOrderResponse\n");
                if ($response->isSetGetOrderResult()) {
                   // echo("            GetOrderResult\n");
                    $getOrderResult = $response->getGetOrderResult();
                    if ($getOrderResult->isSetOrders()) {
                       // echo("                Orders\n");
                        $orders = $getOrderResult->getOrders();
                        $orderList = $orders->getOrder();
                        foreach ($orderList as $order) {
                        $resp['orders'][] = array('amazon_order_id'                 => '',
                                                  'purchase_date'                   => '',
                                                  'order_status'                    => '',
                                                  'fullfillment_channel'            => '',
                                                  'ship_service_level'              => '',
                                                  'shipping_address'                => array('name'         => '',
                                                                                             'address1'     => '',
                                                                                             'address2'     => '',
                                                                                             'address3'     => '',
                                                                                             'city'         => '',
                                                                                             'county'      => '',
                                                                                             'state'        => '',
                                                                                             'postal_code'  => '',
                                                                                             'country_code' => '',
                                                                                             'phone'        => '',
                                                                            ),
                                                  'currency_code'                   => '',
                                                  'order_amount'                    => '',
                                                  'payment_details'                 => array(),
                                                  'payment_method'                  => '',
                                                  'buyer_email'                     => '',
                                                  'buyer_name'                      => '',
                                                  'shipment_service_level_category' => '',
                            );
                        $temp_index = count($resp['orders']) - 1;
                        //echo("                    Order\n");
                        if ($order->isSetAmazonOrderId()) {
                            //echo("                        AmazonOrderId\n");
                            //echo("                            " . $order->getAmazonOrderId() . "\n");
                            $resp['orders'][$temp_index]['amazon_order_id'] = $order->getAmazonOrderId();
                        }
                        if ($order->isSetSellerOrderId()) {
                            //echo("                        SellerOrderId\n");
                            //echo("                            " . $order->getSellerOrderId() . "\n");
                        }
                        if ($order->isSetPurchaseDate()) {
                            //echo("                        PurchaseDate\n");
                            //echo("                            " . $order->getPurchaseDate() . "\n");
                            $resp['orders'][$temp_index]['purchase_date'] = $order->getPurchaseDate();
                        }
                        if ($order->isSetLastUpdateDate()) {
                            //echo("                        LastUpdateDate\n");
                            //echo("                            " . $order->getLastUpdateDate() . "\n");
                        }
                        if ($order->isSetOrderStatus()) {
                            //echo("                        OrderStatus\n");
                            //echo("                            " . $order->getOrderStatus() . "\n");
                            $resp['orders'][$temp_index]['order_status'] = $order->getOrderStatus();
                        }
                        if ($order->isSetFulfillmentChannel()) {
                            //echo("                        FulfillmentChannel\n");
                            //echo("                            " . $order->getFulfillmentChannel() . "\n");
                            $resp['orders'][$temp_index]['fullfillment_channel'] = $order->getFulfillmentChannel();
                        }
                        if ($order->isSetSalesChannel()) {
                            //echo("                        SalesChannel\n");
                            //echo("                            " . $order->getSalesChannel() . "\n");
                        }
                        if ($order->isSetOrderChannel()) {
                            //echo("                        OrderChannel\n");
                            //echo("                            " . $order->getOrderChannel() . "\n");
                        }
                        if ($order->isSetShipServiceLevel()) {
                            //echo("                        ShipServiceLevel\n");
                            //echo("                            " . $order->getShipServiceLevel() . "\n");
                            $resp['orders'][$temp_index]['ship_service_level'] = $order->getShipServiceLevel();
                        }
                        //$resp['orders'][$temp_index]['shipping_address'] = array();
                        if ($order->isSetShippingAddress()) {
                            //echo("                        ShippingAddress\n");
                            $shippingAddress = $order->getShippingAddress();
                            if ($shippingAddress->isSetName()) {
                                //echo("                            Name\n");
                                //echo("                                " . $shippingAddress->getName() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['name'] = $shippingAddress->getName();
                            }
                            if ($shippingAddress->isSetAddressLine1()) {
                                //echo("                            AddressLine1\n");
                                //echo("                                " . $shippingAddress->getAddressLine1() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['address1'] = $shippingAddress->getAddressLine1();
                            }
                            if ($shippingAddress->isSetAddressLine2()) {
                                //echo("                            AddressLine2\n");
                                //echo("                                " . $shippingAddress->getAddressLine2() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['address2'] = $shippingAddress->getAddressLine2();
                            }
                            if ($shippingAddress->isSetAddressLine3()) {
                                //echo("                            AddressLine3\n");
                                //echo("                                " . $shippingAddress->getAddressLine3() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['address3'] = $shippingAddress->getAddressLine3();
                            }
                            if ($shippingAddress->isSetCity()) {
                                //echo("                            City\n");
                                //echo("                                " . $shippingAddress->getCity() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['city'] = $shippingAddress->getCity();
                            }
                            if ($shippingAddress->isSetCounty()) {
                                //echo("                            County\n");
                                //echo("                                " . $shippingAddress->getCounty() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['county'] = $shippingAddress->getCounty();
                            }
                            if ($shippingAddress->isSetDistrict()) {
                                //echo("                            District\n");
                                //echo("                                " . $shippingAddress->getDistrict() . "\n");
                            }
                            if ($shippingAddress->isSetStateOrRegion()) {
                                //echo("                            StateOrRegion\n");
                                //echo("                                " . $shippingAddress->getStateOrRegion() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['state'] = $shippingAddress->getStateOrRegion();
                            }
                            if ($shippingAddress->isSetPostalCode()) {
                                //echo("                            PostalCode\n");
                                //echo("                                " . $shippingAddress->getPostalCode() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['postal_code'] = $shippingAddress->getPostalCode();
                            }
                            if ($shippingAddress->isSetCountryCode()) {
                                //echo("                            CountryCode\n");
                                //echo("                                " . $shippingAddress->getCountryCode() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['country_code'] = $shippingAddress->getCountryCode();
                            }
                            if ($shippingAddress->isSetPhone()) {
                                //echo("                            Phone\n");
                                //echo("                                " . $shippingAddress->getPhone() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['phone'] = $shippingAddress->getPhone();
                            }
                        }
                        if ($order->isSetOrderTotal()) {
                            //echo("                        OrderTotal\n");
                            $orderTotal = $order->getOrderTotal();
                            if ($orderTotal->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $orderTotal->getCurrencyCode() . "\n");
                                $resp['orders'][$temp_index]['currency_code'] = $orderTotal->getCurrencyCode();
                            }
                            if ($orderTotal->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $orderTotal->getAmount() . "\n");
                                $resp['orders'][$temp_index]['order_amount'] = $orderTotal->getAmount();
                            }
                        }
                        if ($order->isSetNumberOfItemsShipped()) {
                            //echo("                        NumberOfItemsShipped\n");
                            //echo("                            " . $order->getNumberOfItemsShipped() . "\n");
                        }
                        if ($order->isSetNumberOfItemsUnshipped()) {
                            //echo("                        NumberOfItemsUnshipped\n");
                            //echo("                            " . $order->getNumberOfItemsUnshipped() . "\n");
                        }

                        //$resp['orders'][$temp_index]['payment_details'] = array();
                        if ($order->isSetPaymentExecutionDetail()) {
                            //echo("                        PaymentExecutionDetail\n");
                            $paymentExecutionDetail = $order->getPaymentExecutionDetail();
                            $paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
                            foreach ($paymentExecutionDetailItemList as $paymentExecutionDetailItem) {
                                $temp = array('currency_code' => '', 'payment_amount' => '', 'sub_payment_method' => '');
                                //echo("                            PaymentExecutionDetailItem\n");
                                if ($paymentExecutionDetailItem->isSetPayment()) {
                                    //echo("                                Payment\n");
                                    $payment = $paymentExecutionDetailItem->getPayment();
                                    if ($payment->isSetCurrencyCode()) {
                                        //echo("                                    CurrencyCode\n");
                                        //echo("                                        " . $payment->getCurrencyCode() . "\n");
                                        $temp = array('currency_code' => $payment->getCurrencyCode());
                                    }
                                    if ($payment->isSetAmount()) {
                                        //echo("                                    Amount\n");
                                        //echo("                                        " . $payment->getAmount() . "\n");
                                        $temp = array('payment_amount' => $payment->getAmount());
                                    }
                                }
                                if ($paymentExecutionDetailItem->isSetSubPaymentMethod()) {
                                    //echo("                                SubPaymentMethod\n");
                                    //echo("                                    " . $paymentExecutionDetailItem->getSubPaymentMethod() . "\n");
                                    $temp = array('sub_payment_method' => $paymentExecutionDetailItem->getSubPaymentMethod());
                                }
                                $resp['orders'][$temp_index]['payment_details'][] = $temp;
                            }
                        }
                        if ($order->isSetPaymentMethod()) {
                            //echo("                        PaymentMethod\n");
                            //echo("                            " . $order->getPaymentMethod() . "\n");
                            $resp['orders'][$temp_index]['payment_method'] = $order->getPaymentMethod();
                        }
                        if ($order->isSetMarketplaceId()) {
                            //echo("                        MarketplaceId\n");
                            //echo("                            " . $order->getMarketplaceId() . "\n");
                        }
                        if ($order->isSetBuyerEmail()) {
                            //echo("                        BuyerEmail\n");
                            //echo("                            " . $order->getBuyerEmail() . "\n");
                            $resp['orders'][$temp_index]['buyer_email'] = $order->getBuyerEmail();
                        }
                        if ($order->isSetBuyerName()) {
                            //echo("                        BuyerName\n");
                            //echo("                            " . $order->getBuyerName() . "\n");
                            $resp['orders'][$temp_index]['buyer_name'] = $order->getBuyerName();
                        }
                        if ($order->isSetShipmentServiceLevelCategory()) {
                            //echo("                        ShipmentServiceLevelCategory\n");
                            //echo("                            " . $order->getShipmentServiceLevelCategory() . "\n");
                            $resp['orders'][$temp_index]['shipment_service_level_category'] = $order->getShipmentServiceLevelCategory();
                        }
                        }
                    }
                }
                if ($response->isSetResponseMetadata()) {
                    //echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                        //echo("                RequestId\n");
                        //echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         //echo("Caught Exception: " . $ex->getMessage() . "\n");
         //echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         //echo("Error Code: " . $ex->getErrorCode() . "\n");
         //echo("Error Type: " . $ex->getErrorType() . "\n");
         //echo("Request ID: " . $ex->getRequestId() . "\n");
         //echo("XML: " . $ex->getXML() . "\n");
         $resp['error'] = $ex->getMessage();
     }
     return $resp;
 }

    private function invoke_list_orders(MarketplaceWebServiceOrders_Interface $service, $request){
        $resp = array('next_token' => '', 'orders' => array(), 'error' => '');
        try {
            $response = $service->listOrders($request);

            //echo ("Service Response\n");
            //echo ("=============================================================================\n");

            //echo("        ListOrdersResponse\n");
            if ($response->isSetListOrdersResult()) {
                //echo("            ListOrdersResult\n");
                $listOrdersResult = $response->getListOrdersResult();
                if ($listOrdersResult->isSetNextToken()) {
                    //echo("                NextToken\n");
                    //echo("                    " . $listOrdersResult->getNextToken() . "\n");
                    $resp['next_token'] = $listOrdersResult->getNextToken();
                }
                if ($listOrdersResult->isSetCreatedBefore()) {
                    //echo("                CreatedBefore\n");
                    //echo("                    " . $listOrdersResult->getCreatedBefore() . "\n");
                }
                if ($listOrdersResult->isSetLastUpdatedBefore()) {
                    //echo("                LastUpdatedBefore\n");
                    //echo("                    " . $listOrdersResult->getLastUpdatedBefore() . "\n");
                }
                if ($listOrdersResult->isSetOrders()) {
                    //echo("                Orders\n");
                    $orders = $listOrdersResult->getOrders();
                    $orderList = $orders->getOrder();
                    foreach ($orderList as $order) {
                        $resp['orders'][] = array('amazon_order_id'                 => '',
                                                  'purchase_date'                   => '',
                                                  'order_status'                    => '',
                                                  'fullfillment_channel'            => '',
                                                  'ship_service_level'              => '',
                                                  'shipping_address'                => array('name'         => '',
                                                                                             'address1'     => '',
                                                                                             'address2'     => '',
                                                                                             'address3'     => '',
                                                                                             'city'         => '',
                                                                                             'county'      => '',
                                                                                             'state'        => '',
                                                                                             'postal_code'  => '',
                                                                                             'country_code' => '',
                                                                                             'phone'        => '',
                                                                            ),
                                                  'currency_code'                   => '',
                                                  'order_amount'                    => '',
                                                  'payment_details'                 => array(),
                                                  'payment_method'                  => '',
                                                  'buyer_email'                     => '',
                                                  'buyer_name'                      => '',
                                                  'shipment_service_level_category' => '',
                            );
                        $temp_index = count($resp['orders']) - 1;
                        //echo("                    Order\n");
                        if ($order->isSetAmazonOrderId()) {
                            //echo("                        AmazonOrderId\n");
                            //echo("                            " . $order->getAmazonOrderId() . "\n");
                            $resp['orders'][$temp_index]['amazon_order_id'] = $order->getAmazonOrderId();
                        }
                        if ($order->isSetSellerOrderId()) {
                            //echo("                        SellerOrderId\n");
                            //echo("                            " . $order->getSellerOrderId() . "\n");
                        }
                        if ($order->isSetPurchaseDate()) {
                            //echo("                        PurchaseDate\n");
                            //echo("                            " . $order->getPurchaseDate() . "\n");
                            $resp['orders'][$temp_index]['purchase_date'] = $order->getPurchaseDate();
                        }
                        if ($order->isSetLastUpdateDate()) {
                            //echo("                        LastUpdateDate\n");
                            //echo("                            " . $order->getLastUpdateDate() . "\n");
                        }
                        if ($order->isSetOrderStatus()) {
                            //echo("                        OrderStatus\n");
                            //echo("                            " . $order->getOrderStatus() . "\n");
                            $resp['orders'][$temp_index]['order_status'] = $order->getOrderStatus();
                        }
                        if ($order->isSetFulfillmentChannel()) {
                            //echo("                        FulfillmentChannel\n");
                            //echo("                            " . $order->getFulfillmentChannel() . "\n");
                            $resp['orders'][$temp_index]['fullfillment_channel'] = $order->getFulfillmentChannel();
                        }
                        if ($order->isSetSalesChannel()) {
                            //echo("                        SalesChannel\n");
                            //echo("                            " . $order->getSalesChannel() . "\n");
                        }
                        if ($order->isSetOrderChannel()) {
                            //echo("                        OrderChannel\n");
                            //echo("                            " . $order->getOrderChannel() . "\n");
                        }
                        if ($order->isSetShipServiceLevel()) {
                            //echo("                        ShipServiceLevel\n");
                            //echo("                            " . $order->getShipServiceLevel() . "\n");
                            $resp['orders'][$temp_index]['ship_service_level'] = $order->getShipServiceLevel();
                        }
                        //$resp['orders'][$temp_index]['shipping_address'] = array();
                        if ($order->isSetShippingAddress()) {
                            //echo("                        ShippingAddress\n");
                            $shippingAddress = $order->getShippingAddress();
                            if ($shippingAddress->isSetName()) {
                                //echo("                            Name\n");
                                //echo("                                " . $shippingAddress->getName() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['name'] = $shippingAddress->getName();
                            }
                            if ($shippingAddress->isSetAddressLine1()) {
                                //echo("                            AddressLine1\n");
                                //echo("                                " . $shippingAddress->getAddressLine1() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['address1'] = $shippingAddress->getAddressLine1();
                            }
                            if ($shippingAddress->isSetAddressLine2()) {
                                //echo("                            AddressLine2\n");
                                //echo("                                " . $shippingAddress->getAddressLine2() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['address2'] = $shippingAddress->getAddressLine2();
                            }
                            if ($shippingAddress->isSetAddressLine3()) {
                                //echo("                            AddressLine3\n");
                                //echo("                                " . $shippingAddress->getAddressLine3() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['address3'] = $shippingAddress->getAddressLine3();
                            }
                            if ($shippingAddress->isSetCity()) {
                                //echo("                            City\n");
                                //echo("                                " . $shippingAddress->getCity() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['city'] = $shippingAddress->getCity();
                            }
                            if ($shippingAddress->isSetCounty()) {
                                //echo("                            County\n");
                                //echo("                                " . $shippingAddress->getCounty() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['county'] = $shippingAddress->getCounty();
                            }
                            if ($shippingAddress->isSetDistrict()) {
                                //echo("                            District\n");
                                //echo("                                " . $shippingAddress->getDistrict() . "\n");
                            }
                            if ($shippingAddress->isSetStateOrRegion()) {
                                //echo("                            StateOrRegion\n");
                                //echo("                                " . $shippingAddress->getStateOrRegion() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['state'] = $shippingAddress->getStateOrRegion();
                            }
                            if ($shippingAddress->isSetPostalCode()) {
                                //echo("                            PostalCode\n");
                                //echo("                                " . $shippingAddress->getPostalCode() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['postal_code'] = $shippingAddress->getPostalCode();
                            }
                            if ($shippingAddress->isSetCountryCode()) {
                                //echo("                            CountryCode\n");
                                //echo("                                " . $shippingAddress->getCountryCode() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['country_code'] = $shippingAddress->getCountryCode();
                            }
                            if ($shippingAddress->isSetPhone()) {
                                //echo("                            Phone\n");
                                //echo("                                " . $shippingAddress->getPhone() . "\n");
                                $resp['orders'][$temp_index]['shipping_address']['phone'] = $shippingAddress->getPhone();
                            }
                        }
                        if ($order->isSetOrderTotal()) {
                            //echo("                        OrderTotal\n");
                            $orderTotal = $order->getOrderTotal();
                            if ($orderTotal->isSetCurrencyCode()) {
                                //echo("                            CurrencyCode\n");
                                //echo("                                " . $orderTotal->getCurrencyCode() . "\n");
                                $resp['orders'][$temp_index]['currency_code'] = $orderTotal->getCurrencyCode();
                            }
                            if ($orderTotal->isSetAmount()) {
                                //echo("                            Amount\n");
                                //echo("                                " . $orderTotal->getAmount() . "\n");
                                $resp['orders'][$temp_index]['order_amount'] = $orderTotal->getAmount();
                            }
                        }
                        if ($order->isSetNumberOfItemsShipped()) {
                            //echo("                        NumberOfItemsShipped\n");
                            //echo("                            " . $order->getNumberOfItemsShipped() . "\n");
                        }
                        if ($order->isSetNumberOfItemsUnshipped()) {
                            //echo("                        NumberOfItemsUnshipped\n");
                            //echo("                            " . $order->getNumberOfItemsUnshipped() . "\n");
                        }

                        //$resp['orders'][$temp_index]['payment_details'] = array();
                        if ($order->isSetPaymentExecutionDetail()) {
                            //echo("                        PaymentExecutionDetail\n");
                            $paymentExecutionDetail = $order->getPaymentExecutionDetail();
                            $paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
                            foreach ($paymentExecutionDetailItemList as $paymentExecutionDetailItem) {
                                $temp = array('currency_code' => '', 'payment_amount' => '', 'sub_payment_method' => '');
                                //echo("                            PaymentExecutionDetailItem\n");
                                if ($paymentExecutionDetailItem->isSetPayment()) {
                                    //echo("                                Payment\n");
                                    $payment = $paymentExecutionDetailItem->getPayment();
                                    if ($payment->isSetCurrencyCode()) {
                                        //echo("                                    CurrencyCode\n");
                                        //echo("                                        " . $payment->getCurrencyCode() . "\n");
                                        $temp = array('currency_code' => $payment->getCurrencyCode());
                                    }
                                    if ($payment->isSetAmount()) {
                                        //echo("                                    Amount\n");
                                        //echo("                                        " . $payment->getAmount() . "\n");
                                        $temp = array('payment_amount' => $payment->getAmount());
                                    }
                                }
                                if ($paymentExecutionDetailItem->isSetSubPaymentMethod()) {
                                    //echo("                                SubPaymentMethod\n");
                                    //echo("                                    " . $paymentExecutionDetailItem->getSubPaymentMethod() . "\n");
                                    $temp = array('sub_payment_method' => $paymentExecutionDetailItem->getSubPaymentMethod());
                                }
                                $resp['orders'][$temp_index]['payment_details'][] = $temp;
                            }
                        }
                        if ($order->isSetPaymentMethod()) {
                            //echo("                        PaymentMethod\n");
                            //echo("                            " . $order->getPaymentMethod() . "\n");
                            $resp['orders'][$temp_index]['payment_method'] = $order->getPaymentMethod();
                        }
                        if ($order->isSetMarketplaceId()) {
                            //echo("                        MarketplaceId\n");
                            //echo("                            " . $order->getMarketplaceId() . "\n");
                        }
                        if ($order->isSetBuyerEmail()) {
                            //echo("                        BuyerEmail\n");
                            //echo("                            " . $order->getBuyerEmail() . "\n");
                            $resp['orders'][$temp_index]['buyer_email'] = $order->getBuyerEmail();
                        }
                        if ($order->isSetBuyerName()) {
                            //echo("                        BuyerName\n");
                            //echo("                            " . $order->getBuyerName() . "\n");
                            $resp['orders'][$temp_index]['buyer_name'] = $order->getBuyerName();
                        }
                        if ($order->isSetShipmentServiceLevelCategory()) {
                            //echo("                        ShipmentServiceLevelCategory\n");
                            //echo("                            " . $order->getShipmentServiceLevelCategory() . "\n");
                            $resp['orders'][$temp_index]['shipment_service_level_category'] = $order->getShipmentServiceLevelCategory();
                        }
                    }
                }
            }
            if ($response->isSetResponseMetadata()) {
                //echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) {
                    //echo("                RequestId\n");
                    //echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            }
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
            //echo("Caught Exception: " . $ex->getMessage() . "\n");
            //echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            //echo("Error Code: " . $ex->getErrorCode() . "\n");
            //echo("Error Type: " . $ex->getErrorType() . "\n");
            //echo("Request ID: " . $ex->getRequestId() . "\n");
            //echo("XML: " . $ex->getXML() . "\n");
            $resp['error'] = $ex->getMessage();
        }
        return $resp;
    }

    private function invoke_get_feed_submission_result(MarketplaceWebService_Interface $service, $request){
      try {
              $response = $service->getFeedSubmissionResult($request);
              //print_r($response);
                //echo ("Service Response\n");
                //echo ("=============================================================================\n");

                //echo("        GetFeedSubmissionResultResponse\n");
                if ($response->isSetGetFeedSubmissionResultResult()) {
                  $getFeedSubmissionResultResult = $response->getGetFeedSubmissionResultResult();
                  //echo ("            GetFeedSubmissionResult");

                  if ($getFeedSubmissionResultResult->isSetContentMd5()) {
                    //echo ("                ContentMd5");
                    //echo ("                " . $getFeedSubmissionResultResult->getContentMd5() . "\n");
                  }
                }
                if ($response->isSetResponseMetadata()) {
                    //echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                       //echo("                RequestId\n");
                       //echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }

     } catch (MarketplaceWebService_Exception $ex) {
         //echo("Caught Exception: " . $ex->getMessage() . "\n");
         //echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         //echo("Error Code: " . $ex->getErrorCode() . "\n");
         //echo("Error Type: " . $ex->getErrorType() . "\n");
         //echo("Request ID: " . $ex->getRequestId() . "\n");
         //echo("XML: " . $ex->getXML() . "\n");
     }
    }

    private function invoke_submit_feed(MarketplaceWebService_Interface $service, $request){
        $resp = array('feed_type' => '', 'submission_id' => '', 'error' => '');
        try {
            $response = $service->submitFeed($request);
                //echo ("Service Response\n");
                //echo ("=============================================================================\n");

                //echo("        SubmitFeedResponse\n");
                if ($response->isSetSubmitFeedResult()) {
                    //echo("            SubmitFeedResult\n");
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) {
                        //echo("                FeedSubmissionInfo\n");
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        if ($feedSubmissionInfo->isSetFeedSubmissionId())
                        {
                            //echo("                    FeedSubmissionId\n");
                            //echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                            $resp['submission_id'] = $feedSubmissionInfo->getFeedSubmissionId();
                        }
                        if ($feedSubmissionInfo->isSetFeedType())
                        {
                            //echo("                    FeedType\n");
                            //echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                            $resp['feed_type'] = $feedSubmissionInfo->getFeedType();
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate())
                        {
                            //echo("                    SubmittedDate\n");
                            //echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus())
                        {
                            //echo("                    FeedProcessingStatus\n");
                            //echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate())
                        {
                            //echo("                    StartedProcessingDate\n");
                            //echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate())
                        {
                            //echo("                    CompletedProcessingDate\n");
                            //echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                    }
                }
                if ($response->isSetResponseMetadata()) {
                    //echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                        //echo("                RequestId\n");
                        //echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }

     } catch (MarketplaceWebService_Exception $ex) {
         //echo("Caught Exception: " . $ex->getMessage() . "\n");
         //echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         //echo("Error Code: " . $ex->getErrorCode() . "\n");
         //echo("Error Type: " . $ex->getErrorType() . "\n");
         //echo("Request ID: " . $ex->getRequestId() . "\n");
         //echo("XML: " . $ex->getXML() . "\n");
         $resp['error'] = $ex->getMessage();
     }
     return $resp;
    }

    /*public static function get_category_templates(){
        $templates = array();
        $sql = tep_db_query("select amazon_product_feed_template_id , amazon_product_feed_template_name from amazon_product_feed_templates order by amazon_product_feed_template_name");
        while ($entry = tep_db_fetch_array($sql)){
            $templates[] = array('id' => $entry['amazon_product_feed_template_id'], 'text' => $entry['amazon_product_feed_template_name']);
        }
        return $templates;
    }

    public static function get_category_templates_dropdown($name, $default = '', $parameters = '', $required = false){
        $templates = array_merge(array(array('id' => '', 'text' => '-- Select --')), amazon_manager::get_category_templates());
        return tep_draw_pull_down_menu($name, $templates, $default, $parameters, $required);
    }*/

    public static function get_amazon_categories(){
        $categories = array();
        $sql = tep_db_query("select amazon_categories_id, parent_id, amazon_categories_name from amazon_categories where parent_id is null order by amazon_categories_name");
        while ($entry = tep_db_fetch_array($sql)){
            $categories[] = array('id' => $entry['amazon_categories_id'], 'parent_id' => $entry['parent_id'], 'text' => $entry['amazon_categories_name']);
            $sql_2 = tep_db_query("select amazon_categories_id, parent_id, amazon_categories_name from amazon_categories where parent_id='" . $entry['amazon_categories_id'] . "' order by amazon_categories_name");
            while ($entry_2 = tep_db_fetch_array($sql_2)){
                $categories[] = array('id' => $entry_2['amazon_categories_id'], 'parent_id' => $entry_2['parent_id'], 'text' => $entry_2['amazon_categories_name']);
            }
        }
        return $categories;
    }

    public static function get_amazon_categories_dropdown($name, $default = '', $parameters = ''){
        $categories = amazon_manager::get_amazon_categories();
        $select_html = '<select name="' . $name . '" ' . $parameters . '>';
        $select_html .= '<option value="">-- Select --</option>';
        $optgroup_open = false;
        foreach($categories as $category){
            if (empty($category['parent_id'])){
                if ($optgroup_open){
                    $select_html .= '</optgroup>';
                    $optgroup_open = false;
                }
                $select_html .= '<optgroup label="' . $category['text'] . '">';
                $optgroup_open = true;
            } else {
                $select_html .= '<option value="' . $category['id'] . '" ' . ($category['id']=$default ? ' selected="true" ' : '') . '>' . $category['text'] . '</option>';
            }
        }
        if ($optgroup_open){
            $select_html .= '</optgroup>';
            $optgroup_open = false;
        }
        $select_html .= '</select>';

        return $select_html;
    }

}
?>
