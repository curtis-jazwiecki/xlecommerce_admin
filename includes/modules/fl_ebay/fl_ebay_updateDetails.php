<?php 
/*
  fl_ebay_updateDetails.php Michael Hammelmann 25.06.2004
  v 0.1
  http://www.flinkux.de / http://www.t4d.flinkux.de
 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
  for more informations contact michael.hammelmann@flinkux.de
*/

require_once('includes/modules/fl_ebay/Ebay_CategoryQuery.php');
require_once('includes/modules/fl_ebay/Ebay_DetailQuery.php');
require_once('includes/modules/fl_ebay/Ebay_DetailEntry.php');

$sql="select categorie_version from ebay_config ";
$rs=tep_db_query($sql);
$cat_ver=tep_db_fetch_array($rs);


$mySession = new Ebay_Session();
$mySession->InitFromConfig("includes/modules/fl_ebay/config/ebay.config");

$eDetails[]='SiteId';
$eDetails[]='Region';
$eDetails[]='Country';
$eDetails[]='Currency';
$eDetails[]='ShippingOption';
$eDetails[]='ShippingPackage';
$eDetails[]='ShippingRegion';
$eDetails[]='ShippingService';
$eDetails[]='ShippingType';
$eDetails[]='PaymentOption';

foreach ($eDetails as $type)
{
$sql="drop table if exists ebay_env_".$type;
$rs=tep_db_query($sql);
$sql="CREATE  TABLE  if not exists ebay_env_".$type." ( `descr` varchar(50) NULL, `value` varchar(30) NULL) type=myisam;";
$rs=tep_db_query($sql);
$sql="delete from  ebay_env_".$type;
$rs=tep_db_query($sql);
$detail= new Ebay_DetailQuery($mySession);
$detail->setDetailName($type);
$res=$detail->Query(0);
$rList=$detail->getResultList();
if($res->isGood())
{
	foreach($rList as $cat )
	{

		for($i=0;$i< $cat->getEntriesCount();$i++)
		{
			$myDet=$cat->getEntries($i);

			$sql="insert into  ebay_env_".$type." (`descr`,`value`)values('". mysql_escape_string($myDet->getDescription())."','".$myDet->getValue()."')";
			$rs=tep_db_query($sql);
		}
	}
}
}	

?>