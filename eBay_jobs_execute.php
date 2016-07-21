<?php
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
require('cron_application_top.php');
require_once(DIR_FS_ROOT . 'eBay_manager/eBay.class.php');
$action = !empty($_GET['action']) ? strtolower(trim($_GET['action'])) : '';

if (!empty($action)){
    $eBay = new eBay;
    
    switch ($action){
        case 'getjobs':
            $job_type = !empty($_GET['job_type']) ? ucfirst(strtolower(trim($_GET['job_type']))) : '';
            $eBay->getJobs($job_type);
            break;
        case 'getjobstatus':
            $job_id = !empty($_GET['job_id']) ? trim($_GET['job_id']) : '';
            if (!empty($job_id)){
                $eBay->getJobStatus($job_id);
            }
            break;
        case 'abortjob':
            $job_id = !empty($_GET['job_id']) ? trim($_GET['job_id']) : '';
            if (!empty($job_id)){
                $eBay->abortJob($job_id);
            }
            break;
        case 'uploaditems':
            $eBay->startUploadJob();
            break;
        case 'downloadreports':
            $eBay->downloadReports();
            break;
        case 'downloadreport':
            $job_id = !empty($_GET['job_id']) ? trim($_GET['job_id']) : '';
            $file_reference_id = !empty($_GET['file_reference_id']) ? trim($_GET['file_reference_id']) : '';
            if (!empty($job_id) && !empty($file_reference_id)){
                $eBay->downloadReport($job_id, $file_reference_id);
            }
            break;
        case 'getorderreportphase1':
            $eBay->getOrderReportPhase1();
            break;
        case 'getorderreportphase2':
            $eBay->getOrderReportPhase2();
            break;
        case 'getorderreportphase3':
            $eBay->getOrderReportPhase3();
            break;
        case 'fireorderack':
            $order_feed_id = !empty($_GET['order_feed_id']) ? trim($_GET['order_feed_id']) : '';
            if(!empty($order_feed_id)){
                $this->fireOrderAck($order_feed_id);
            } else {
                $this->fireOrderAck();
            }
            break;
        case 'orderfulfillment':
            $order_feed_id = !empty($_GET['order_feed_id']) ? trim($_GET['order_feed_id']) : '';
            if(!empty($order_feed_id)){
                $this->fireOrderFulfillment($order_feed_id);
            } else {
                $this->fireOrderFulfillment();
            }
            break;					
		case 'movesingleitem':
			$product_id = !empty($_GET['product_id']) ? trim($_GET['product_id']) : '';
			if (!empty($product_id)){
				$eBay->moveItemToEbay($product_id);
			}
			break;
    }    
}

echo 'reached end point';
?>