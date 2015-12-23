<?php
require('includes/application_top.php');
require(DIR_FS_ROOT.'amazon_mws/config.php');
$type = $_GET['type'];
$file_name = $_GET['file_name'];
$xml = null;
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php
if (!empty($type) && !empty($file_name)){
    switch($type){
        case 'request':
            if (file_exists(AMAZON_OUTGOING_DIRECTORY . $file_name)){
                $xml = simplexml_load_file(AMAZON_OUTGOING_DIRECTORY . $file_name);
            } elseif (file_exists(AMAZON_ARCHIVES_DIRECTORY . $file_name)){
                $xml = simplexml_load_file(AMAZON_ARCHIVES_DIRECTORY . $file_name);
            } elseif (file_exists(AMAZON_REQUEST_DIRECTORY . $file_name)){
                $xml = simplexml_load_file(AMAZON_REQUEST_DIRECTORY . $file_name);
            }
            break;
        case 'response':
            if (file_exists(AMAZON_RESPONSE_DIRECTORY . $file_name)){
                $xml = simplexml_load_file(AMAZON_RESPONSE_DIRECTORY . $file_name);
            }
            break;
    }
}
?>
        <div align="center">
        <?php
        if ($xml){
            $summary = '';
            $fields = array();
            $entries = array();
            $feed_type = $xml->MessageType;
            switch($feed_type){
                case 'Product':
                    $fields[] = 'Operation';
                    $fields[] = 'SKU';
                    $xpath = $xml->xpath('Message');
                    foreach($xpath as $message){
                        $entries[] = array((string)$message->OperationType, (string)$message->Product->SKU);
                    }
                    break;
                case 'Price':
                    $fields[] = 'SKU';
                    $xpath = $xml->xpath('Message');
                    foreach($xpath as $message){
                        $entries[] = array((string)$message->Price->SKU);
                    }
                    break;
                case 'ProductImage':
                    $fields[] = 'SKU';
                    $fields[] = 'Operation';
                    $fields[] = 'Image Type';
                    $xpath = $xml->xpath('Message');
                    foreach($xpath as $message){
                        $entries[] = array((string)$message->ProductImage->SKU, (string)$message->OperationType, (string)$message->ProductImage->ImageType);
                    }
                    break;
                case 'Inventory':
                    $fields[] = 'SKU';
                    $fields[] = 'Operation';
                    $fields[] = 'Quantity';
                    $xpath = $xml->xpath('Message');
                    foreach($xpath as $message){
                        $entries[] = array((string)$message->Inventory->SKU, (string)$message->OperationType, (string)$message->Inventory->Quantity);
                    }
                    break;
                case 'Override':
                    $fields[] = 'SKU';
                    $fields[] = 'Operation';
                    $xpath = $xml->xpath('Message');
                    foreach($xpath as $message){
                        $entries[] = array((string)$message->Override->SKU, (string)$message->OperationType);
                    }
                    break;
                case 'ProcessingReport':
                    $summary_node = $xml->Message->ProcessingReport;
                    $summary = '<div class="smallText" style="width:200px;float:left;">StatusCode</div><span class="smallText"> : ' . (string)$summary_node->StatusCode . '</span><br/>' .
                               '<div class="smallText" style="width:200px;float:left;">MessagesProcessed</div><span class="smallText"> : ' . (string)$summary_node->ProcessingSummary->MessagesProcessed . '</span><br/>' .
                               '<div class="smallText" style="width:200px;float:left;">MessagesSuccessful</div><span class="smallText"> : ' . (string)$summary_node->ProcessingSummary->MessagesSuccessful . '</span><br/>' .
                               '<div class="smallText" style="width:200px;float:left;">MessagesWithError</div><span class="smallText"> : ' . (string)$summary_node->ProcessingSummary->MessagesWithError . '</span><br/>' .
                               '<div class="smallText" style="width:200px;float:left;">MessagesWithWarning</div><span class="smallText"> : ' . (string)$summary_node->ProcessingSummary->MessagesWithWarning . '</span><br/>';

                    $fields[] = 'SKU';
                    $fields[] = 'Result Code';
                    $fields[] = 'Result Description';

                    $xpath = $xml->xpath('Message/ProcessingReport/Result');
                    foreach($xpath as $result){
                        $entries[] = array((string)$result->AdditionalInfo->SKU, (string)$result->ResultCode, (string)$result->ResultDescription);
                    }
                    break;
            }
        ?>
            <table>
                <tr class="dataTableRow">
                    <td class="smallText dataTableContent">Feed Type: <b><?php echo $feed_type; ?></b></td>
                </tr>
                <tr class="dataTableRow">
                    <td class="dataTableContent">
                        <table>
                            <?php if (!empty($summary)) { ?>
                            <tr class="dataTableRow"><td colspan="<?php echo count($fields); ?>" valign="top" class="dataTableContent"><?php echo $summary; ?></td></tr>
                            <tr class="dataTableRow"><td class="dataTableContent">&nbsp;</td></tr>
                            <?php } ?>
                            <tr class="dataTableRow">
                            <?php foreach($fields as $field) { ?>
                                <td class="smallText dataTableContent" valign="top"><b><?php echo $field; ?></b></td>
                            <?php } ?>
                            </tr>
                            <?php for($i=0; $i<count($entries); $i++) { ?>
                            <tr class="dataTableRow">
                                <?php for($j=0; $j<count($entries[$i]); $j++) { ?>
                                <td class="smallText dataTableContent" valign="top"><?php echo (empty($entries[$i][$j]) ? 'Value missing' : $entries[$i][$j]); ?></td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            </table>
        <?php } else { ?>
            File either invalid or no longer exists
        <?php } ?>
        </div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
