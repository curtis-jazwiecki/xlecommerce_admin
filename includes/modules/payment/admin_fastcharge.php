<?php
/*
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
class admin_fastcharge extends fastcharge {
    var $code, $title, $description, $enabled, $response;

    function __construct(){
        parent::__construct();
    }

    function before_process() {
        global $order;

        // Create a string that contains a listing of products ordered for the description field
        $description = '';
        for ($i=0; $i<sizeof($order->products); $i++) {
            $description .= $order->products[$i]['name'] . '(qty: ' . $order->products[$i]['qty'] . ') + ';
        }

        // Strip the last "\n" from the string
        $description = substr($description, 0, -2);

        // Create a variable that holds the order time
        $order_time = date("F j, Y, g:i a");

        // Calculate the next expected order id
        $last_order_id = tep_db_query("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");
        $new_order_id = $last_order_id->fields['orders_id'];
        $new_order_id = ($new_order_id + 1);

        // Populate an array that contains all of the data to be submitted
        $submit_data = array(
            'x_login'               => MODULE_PAYMENT_FASTCHARGE_LOGIN, // The login name as assigned to you by authorize.net
            'x_tran_key'            => MODULE_PAYMENT_FASTCHARGE_TXNKEY,  // The Transaction Key (16 digits) is generated through the merchant interface
            'x_relay_response'      => 'FALSE', // AIM uses direct response, not relay response
            'x_delim_char'          => '|',
            'x_delim_data'          => 'TRUE', // The default delimiter is a comma
            'x_version'             => '3.1',  // 3.1 is required to use CVV codes
            'x_type'                => MODULE_PAYMENT_FASTCHARGE_AUTHORIZATION_TYPE == 'Authorize' ? 'AUTH_ONLY': 'AUTH_CAPTURE',
            'x_method'              => 'CC',
            'x_amount'              => number_format($order->info['total'], 2),
            'x_card_num'            => $_POST['fastcharge_cc_number'],
            'x_exp_date'            => $_POST['fastcharge_cc_expires_month'] . substr($_POST['fastcharge_cc_expires_year'], -2),
            'x_card_code'           => $_POST['fastcharge_cc_cvv'],
            'x_email_customer'      => MODULE_PAYMENT_FASTCHARGE_EMAIL_CUSTOMER == 'True' ? 'TRUE': 'FALSE',
            'x_email_merchant'      => MODULE_PAYMENT_FASTCHARGE_EMAIL_MERCHANT == 'True' ? 'TRUE': 'FALSE',
            'x_cust_id'             => $_SESSION['customer_id'],
            'x_invoice_num'         => $new_order_id,
            'x_first_name'          => $order->billing['firstname'],
            'x_last_name'           => $order->billing['lastname'],
            'x_company'             => $order->billing['company'],
            'x_address'             => $order->billing['street_address'],
            'x_city'                => $order->billing['city'],
            'x_state'               => $order->billing['state'],
            'x_zip'                 => $order->billing['postcode'],
            'x_country'             => $order->billing['country']['title'],
            'x_phone'               => $order->customer['telephone'],
            'x_email'               => $order->customer['email_address'],
            'x_ship_to_first_name'  => $order->delivery['firstname'],
            'x_ship_to_last_name'   => $order->delivery['lastname'],
            'x_ship_to_address'     => $order->delivery['street_address'],
            'x_ship_to_city'        => $order->delivery['city'],
            'x_ship_to_state'       => $order->delivery['state'],
            'x_ship_to_zip'         => $order->delivery['postcode'],
            'x_ship_to_country'     => $order->delivery['country']['title'],
            'x_description'         => $description,
            //'x_Test_Request'      => (MODULE_PAYMENT_FASTCHARGE_TESTMODE == 'Test' ? 'TRUE' : 'FALSE'),
            'Date'                  => $order_time,
            'IP'                    => $_SERVER['REMOTE_ADDR'],
            'Session'               => tep_session_id()
        );

        if (MODULE_PAYMENT_FASTCHARGE_TESTMODE == 'Test') {
            $url = 'https://trans.secure-fastcharge.com/cgi-bin/authorize.cgi ';
        } else {
            $url = 'https://trans.secure-fastcharge.com/cgi-bin/authorize.cgi ';
        }


        // *****************  New code By prasoon Strats Here 	*************************
        // This section takes the input fields and converts them to the proper format
        // for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
        $data = "";

        foreach( $submit_data as $key => $value ) { 
            $data .= "$key=" . urlencode( $value ) . "&"; 
        }
        $data = rtrim( $data, "& " );
    
        // This sample code uses the CURL library for php to establish a connection,
    	// submit the post, and record the response.
        // If you receive an error, you may want to ensure that you have the curl
    	// library enabled in your php configuration
    
        $request = curl_init($url); // initiate curl object
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
    
        $response = curl_exec($request); // execute curl post and store results in $post_response
    	// additional options may be required depending upon your server configuration
    	// you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close ($request); // close curl object
    
        // This line takes the response and breaks it into an array using the specified delimiting character
        $this->response = explode($submit_data["x_delim_char"],$response);
    
		if ($this->response[0] != '1') {
			return array(
				'error' => $this->response[3] . ' - ' . MODULE_PAYMENT_FASTCHARGE_TEXT_DECLINED_MESSAGE
			);
		} else {
			return array();
		}
    }
}

?>