<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
class GunBroker {
	private $url;
	private $username;
	private $password;
	private $dev_key;
	public $list_entries_per_page;
	
	function GunBroker(){
		$this->url = 'http://api.gunbroker.com/';
		$this->list_entries_per_page = '25';
		$this->username = 'outdoorb';
		$this->password = 'nofear2324';
		$this->dev_key = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
	}
	
	private function getCurlOptionsGetUnsecured($url){
		$headers = array(
			'Accept: application/json', 
			'Content-Type: application/json'
		);
		$options = array(
			CURLOPT_URL => $url, 
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HTTPGET => true, 
			CURLOPT_RETURNTRANSFER => true, 
		);
		return $options;
	}
	
	private function getUnsecuredGetResponse($url){
		$curl = curl_init();
		$options = $this->getCurlOptionsGetUnsecured($url);
		curl_setopt_array($curl, $options);
		$response = curl_exec($curl);
		return json_decode($response);
	}
	
	public function getAllCategories($page_index='1', $sort_type='1'){
		$url = $this->url . 'Categories?PageSize=' . $this->list_entries_per_page . '&PageIndex=' . $page_index . '&SortType=' . $sort_type;
		return $this->getUnsecuredGetResponse($url);
	}
	
	public function getCategoriesById($category_id){
		$url = $this->url . 'Categories/' . $category_id;
		return $this->getUnsecuredGetResponse($url);
	}
	
	public function getGunBrokerTime(){
		$url = $this->url . 'GunBrokerTime';
		return $this->getUnsecuredGetResponse($url);
	}
	
	public function getShowcaseItems(){
		$url = $this->url . 'Items/Showcase?Count=' . $this->list_entries_per_page;
		return $this->getUnsecuredGetResponse($url);
	}
}
$obj = new GunBroker();
$obj->list_entries_per_page = 1;
$categories = $obj->getAllCategories();
print_r($categories);
?>