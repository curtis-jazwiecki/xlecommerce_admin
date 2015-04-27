<?php
$first_name = trim($_POST['firstname']);
$last_name = trim($_POST['lastname']);
$email = trim($_POST['email_address']);
$zipcode = $_POST['postcode'];
 
/////////// REGISTER EMAIL WITH CONSTANT CONTACT ///////////////////
 
$UN = "outdoorbusinessnetwork";
$PW = "Obn124";
$Key = "499bc5ae-96ad-49c8-b660-b2ef8a2e91c8";
 
$entry = '<entry xmlns="http://www.w3.org/2005/Atom">
<id>data:,</id>
  <title />
  <author />
<updated>2010-10-16</updated>
<content type="application/vnd.ctct+xml">
 <ContactList xmlns="http://ws.constantcontact.com/ns/1.0/">
      <OptInDefault>false</OptInDefault>
            <Name>Subscribers List</Name>
            <SortOrder>1</SortOrder>
        </ContactList>
</content>
</entry>';
 
// Initialize the cURL session
$request ="https://api.constantcontact.com/ws/customers/" . $UN;
$ch = curl_init();
 
// Set up digest authentication
$userNamePassword = $Key . '%' . $UN . ':' . $PW ;
 
// Set cURL options
curl_setopt($ch, CURLOPT_URL, $request);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $userNamePassword);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS , $entry);
curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/atom+xml"));
curl_setopt($ch, CURLOPT_HEADER, false); // Do not return headers
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // If you set this to 0, it will take you to a page with the http response
 
// Execute cURL session and close it

if ($response = curl_exec($ch)) {
echo 'ok';
print_r($response);
} else {
	print_r(curl_error($ch));
}
?>