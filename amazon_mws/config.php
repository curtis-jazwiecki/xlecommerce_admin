<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
define('DATE_FORMAT', 							'Y-m-d\TH:i:s\Z');
/*define('AMAZON_USERNAME',						AMAZON_USERNAME);
define('AMAZON_PASSWORD',						AMAZON_PASSWORD);
define('AMAZON_MERCHANT_TOKEN',					AMAZON_MERCHANT_TOKEN);
define('MERCHANT_ID',							MERCHANT_ID);
define('AMAZON_MARKETPLACE_ID', 				AMAZON_MARKETPLACE_ID);
define('AMAZON_DEVELOPMENT_ACCOUNT_NUMBER',   	AMAZON_DEVELOPMENT_ACCOUNT_NUMBER);
define('AWS_ACCESS_KEY_ID', 					AWS_ACCESS_KEY_ID);
define('AWS_SECRET_ACCESS_KEY',					AWS_SECRET_ACCESS_KEY);*/
define('AMAZON_FEEDS_SERVICE_URL',              'https://mws.amazonservices.com');
define('AMAZON_ORDERS_SERVICE_URL',             'https://mws.amazonservices.com/Orders/2011-01-01');
define('APPLICATION_NAME',						'obnapp');
define('APPLICATION_VERSION',					'0.0.1');
define('AMAZON_DIRECTORY',                      DIR_FS_ROOT . 'amazon/');
define('AMAZON_MWS_DIRECTORY',                  DIR_FS_ADMIN . 'amazon_mws/');
define('AMAZON_OUTGOING_DIRECTORY',             AMAZON_DIRECTORY . 'outgoing/');
define('AMAZON_RESPONSE_DIRECTORY',             AMAZON_DIRECTORY . 'response/');

/*define('AMAZON_ORDER_STATUS_UNSHIPPED',			'1');
define('AMAZON_ORDER_STATUS_SHIPPED',			'3');
define('AMAZON_ORDER_STATUS_CANCELLED',			'7');*/

define ('CURRENCY',                             'USD');
define ('AMAZON_PAYMENT_METHOD',                'Credit Card');
define ('ITEM_PRICE_COMPONENT_PRINCIPAL',       'Principal');
define ('ITEM_PRICE_COMPONENT_SHIPPING',        'Shipping');
define ('ITEM_PRICE_COMPONENT_TAX',             'Tax');
define ('ITEM_PRICE_COMPONENT_SHIPPING_TAX',    'ShippingTax');

//define ('MINIMUM_INVENTORY_LEVEL',			MINIMUM_INVENTORY_LEVEL);


set_include_path(get_include_path() . PATH_SEPARATOR . DIR_FS_ADMIN . 'amazon_mws' . PATH_SEPARATOR);
require_once(AMAZON_MWS_DIRECTORY . 'amazon_manager.class.php');

function __autoload($className){
    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    foreach($includePaths as $includePath){
        if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
            require_once $filePath;
            return;
        }
    }
}
?>
