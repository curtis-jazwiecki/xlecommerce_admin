<?php 
/*
  fl_ebay_update.php Michael Hammelmann 25.06.2004
  v 0.11
  http://www.flinkux.de / http://www.t4d.flinkux.de

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.

  for more informations contact michael.hammelmann@flinkux.de
*/
echo "Checking for dataupdate....";
require_once('includes/modules/fl_ebay/Ebay_CategoryQuery.php');
$sql="select categorie_version from ebay_config ";
$rs=tep_db_query($sql);
$cat_ver=tep_db_fetch_array($rs);

$mySession = new Ebay_Session();
$mySession->InitFromConfig("includes/modules/fl_ebay/config/ebay.config");

$catQuery = new Ebay_CategoryQuery($mySession);
$catQuery->setCategorySiteId($mySession->getSiteId());
$catQuery->setViewAllNodes(false);
$catQuery->setCategoryParent(0);
$res = $catQuery->Query(0);
$info = $catQuery->getResultInfo();
//echo $info->getVersion().":".$cat_ver['categorie_version']."<br>";;
if($info->getVersion() != $cat_ver['categorie_version'])
{
echo "update is running. Please wait a few minutes.";
$catQuery->setCategorySiteId(0);
$catQuery->setViewAllNodes(true);
// the level has to be in relation to the category
// so setting a parent-category needs also to set the right level
$catQuery->setLevelLimit(0);
// all from the root
$catQuery->setCategoryParent(0);
// return all info
$res = $catQuery->Query(1);

// just get a copy of the result till now
// this will only have the root-level categories
$catListRootLevel = $catQuery->getResultList();

 if ($res->isGood()) {

    foreach ($catListRootLevel as $cat) {
		$name=addslashes($cat->getCategoryName());
		$sql="replace into ebay_categorys(name,id,parentid,leaf,virtual,expired)values('".$name."',". $cat->getCategoryId().",".  $cat->getCategoryParentId() .",'". $cat->getLeafCategory()."','". $cat->getIsVirtual()."','".$cat->getIsExpired()."')";
		$rs=tep_db_query($sql);
    } 
	include('includes/modules/fl_ebay/fl_ebay_updateDetails.php');
	$sql="delete from ebay_config";
	$rst=tep_db_query($sql);
	$sql="insert into ebay_config ( categorie_version)values('".$info->getVersion()."')";
	$rst=tep_db_query($sql);

} 
}?>