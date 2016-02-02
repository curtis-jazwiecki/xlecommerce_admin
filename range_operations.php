<?php require('includes/application_top.php');
  //BOF:authorization_check
  handle_authorization(basename(__FILE__));
  //EOF:authorization_check
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
?>
<?php
if ($_POST['action']=='get_lanes'){
    $range_id = $_POST['range_id'];
    $lanes = get_shooting_lanes_by_range_id($range_id);
    echo json_encode($lanes);
    exit();
} elseif ($_POST['action']=='register_start'){
    $oID = $_POST['oID'];
    $range_order_ref = $_POST['range_order_ref'];
    $index = $_POST['index'];
    $start = time();
    tep_db_query("update range_orders set timer_start='" . $start . "' where id='" . (int)$range_order_ref . "'");

    $item_query = tep_db_query("select p.products_id, p.base_price as price, r.calculate_via from products p, range_orders ro, ranges r where ro.id='" . (int)$range_order_ref . "' and ro.range_id=r.ranges_id and p.range_id=ro.range_id and p.lane_id=ro.lane_id and p.is_lane_item='1'");
    $info = tep_db_fetch_array($item_query); 
    $response = array(
        'oID' => $oID, 
        'range_order_ref' => $range_order_ref,
        'index' => $index,
        'start' => $start,
        'product_id' => $info['products_id'], 
        'product_price' => $info['price'], 
        'calculate_via' => $info['calculate_via'],
    );
    echo json_encode($response);
    exit();
} elseif ($_POST['action']=='register_stop'){
    $oID = $_POST['oID'];
    $range_order_ref = $_POST['range_order_ref'];
    $index = $_POST['index'];
    $stop = time();
    tep_db_query("update range_orders set timer_stop='" . $stop . "' where id='" . (int)$range_order_ref . "'"); 
    $response = array(
        'oID' => $oID, 
        'range_order_ref' => $range_order_ref,
        'index' => $index,
        'stop' => $stop,
    );
    echo json_encode($response);
    exit();
} elseif ($_POST['action']=='close_order'){
    $oID = $_POST['oID'];
    $range_order_ref = $_POST['range_order_ref'];
    $index = $_POST['index'];
    tep_db_query("update range_orders set is_active='0' where id='" . (int)$range_order_ref . "'"); 
    $response = array(
        'oID' => $oID, 
        'range_order_ref' => $range_order_ref,
        'index' => $index,
    );
    echo json_encode($response);
    exit();
}
?>
<body>
<link rel="stylesheet" type="text/css" href="includes/css/clock.css" />
<link rel="stylesheet" type="text/css" href="includes/css/jquery.mCustomScrollbar.css" />

<script src="includes/general.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="includes/javascript/clock.js"></script>
<script src="includes/javascript/jquery.mCustomScrollbar.concat.min.js"></script>

<style>
.lane_box{
    width:  100%; 
    float:left;
}
#timer{
    color:#000000;
    text-align: center;
    font-weight: bold;
}
#timer_button_container{
    margin-top: 5px;
}
.range-box{
background: #e4efc0; /* Old browsers */
/* IE9 SVG, needs conditional override of 'filter' to 'none' */
background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2U0ZWZjMCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNhYmJkNzMiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
background: -moz-linear-gradient(top,  #e4efc0 0%, #abbd73 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e4efc0), color-stop(100%,#abbd73)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #e4efc0 0%,#abbd73 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #e4efc0 0%,#abbd73 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #e4efc0 0%,#abbd73 100%); /* IE10+ */
background: linear-gradient(to bottom,  #e4efc0 0%,#abbd73 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e4efc0', endColorstr='#abbd73',GradientType=0 ); /* IE6-8 */

}
/*menu 03-Jan-2013 bof*/
ul#nav > li > a {
    /*border: 1px solid;*/
    padding-bottom: 12px;
}

ul#nav ul {
    margin-top: 20px;
}
ul#nav > li {
    padding: 5px;
}
li#range a {
    width: 200px;    
}
li#range.block {    
    width: 200px;
}
#nav div{
    margin-top:6px;
}
/*menu 03-Jan-2013 eof*/
</style>
<script>
//var timers = new Array();//define array for holding timers object (through setTimeout) for each lane
//var durations = new Array();//define array for incremental durations (through setTimeout) for each lane
//var total_lanes = 0;//declare variable to hold total number of lanes for range
var interval = 1000;//interval for refreshing timer
var text_open_order = 'Open New Order';//verbiage for opening new order
var text_close_order = 'Close Order';//verbiage for close order
var text_start_stop = 'Start / Stop';//verbiage for start / stop timer
var text_reset = 'Reset';//verbiage for reset
var text_not_set = 'Not Set';//verbiage for order which is not yet set
//var timers = new Array();
//var durations = new Array();
var total_lanes = 0;
var text_order_header = '{lane} [ Order# {order} ]';
var arr_duration = new Array();
var arr_timer = new Array();
<?php
    $temp_ranges = get_shooting_ranges();
    foreach($temp_ranges as $temp_range){
        if ($temp_range['id']!=''){
            $count = 0;
            $temp_lanes = get_shooting_lanes_by_range_id($temp_range['id']);
            foreach($temp_lanes as $temp_lane){
                if ($temp_lane['id']!=''){
                    $duration_var = 'duration_R' . $temp_range['id'] . '_L' . $temp_lane['lane_id'];
                    $timer_var =  'timer_R' . $temp_range['id'] . '_L' . $temp_lane['lane_id'];
                    echo 'var ' . $timer_var . ' = undefined;' . "\n";
                    echo 'var ' . $duration_var . '=' . ((int)$temp_lane['interval'] * 1000) . ';' . "\n";
                    echo 'arr_duration[' . $count . '] = "' . $duration_var . '";' . "\n";
                    echo 'arr_timer[' . $count . '] = "' . $timer_var . '";' . "\n";
                    $count++;
                }
            }
        }
    }
?>

$(document).ready(function() {
    $('#clock').clockize();
    //$('iframe[name="order"]').height(window.innerHeight - 250);
    $('div#lane_timers, iframe[name="order"]').height(window.innerHeight - 250);
    $('div#order').mCustomScrollbar();
    
    $('select[name="range"]').change(function(){//on change in ranges drop-down
        for(var i=0; i<arr_timer.length; i++){
            eval('clearTimeout(' + arr_timer[i] + ')');
            eval(arr_timer[i] + ' = undefined');
        }
        $('iframe[name="order"]').attr('src', '<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN; ?>show_range_logo.php?range_id=' + $('select[name="range"]').val());
        $('#lane_timers').html('');//set right column, holding times, to blank
        $('#order_header').html('');
        range_id = $(this).val();//fetch selected range
        $.ajax({ //place ajax call to fetch lanes associated with selected range
           url: 'range_operations.php', 
           method: 'post',
           dataType: 'json',
           data: {
            action: 'get_lanes',
            range_id : range_id
           }, 
           success: function(resp){ //on success
                //window.timers = new Array();//define array for holding timers object (through setTimeout) for each lane
                //window.durations = new Array();//define array for incremental durations (through setTimeout) for each lane
                total_lanes = 0;//declare variable to hold total number of lanes for range
                html = ''; //declare html content, to be diaplayed, as blank
                entries = new Array();//declare array to hold html content against each lane
                max_entries_per_row = 4;//obsolete. not valid any more. WILL BE REMOVED
                count = 0;//set counter
                for(var entry in resp){//loop through each lane
                    if (resp[entry].id=='') continue;//if lane id is blank, skip and move to next
                    count++;//increment counter
                    
                    oID = '';//declare osc order id
                    range_order_ref = '';//declare reference for range order (range_orders data table) to link with osc order
                    start = '';//declare start timestamp
                    stop = '';//declare stop timestamp
                    product_price = '';
                    product_id = '';
                    calculate_via = '';
                    timer = '00:00:00';//set default timer value
                    lane_occupied = false;//by default, lane is considered as unoccupied 
                    
                    //in case of re-loading, due to any reason, of page check for values so that page's initial state can be set
                    //details that are returned are coming with interval since start time for the lane (in seconds) 
                    if (parseInt(resp[entry].interval)>0 || resp[entry].oID!=''){//set values only if this interval is > 0
                        //console.log('order_exists');
                        oID = resp[entry].oID;//set osc order id
                        range_order_ref = resp[entry].range_order_ref;//set range order ref
                        if (resp[entry].start!=null){
                            start = resp[entry].start;//set start timestamp (in seconds)    
                        } 
                        if (resp[entry].stop!=null){//if lane is not yet occupied by customer
                            stop = resp[entry].stop;//skip setting of 'stop' timestamp
                        } 
                        product_price = resp[entry].product_price;
                        product_id = resp[entry].product_id;
                        calculate_via = resp[entry].calculate_via;
                        timer = formatTime(parseInt(resp[entry].interval) * 1000);//set lane's timer (in milisec) which is being returned as part of response
                        
                        lane_occupied = true;//consider lane as occupied
                        //window.durations[count] = parseInt(resp[entry].interval) * 1000//set duration to start from actual time instead of 0
                        //console.log('setting duratioin');
                        eval('duration_R' + $('select[name="range"]').val() + '_L' + resp[entry].id + '=' + (parseInt(resp[entry].interval) * 1000));
                        //console.log('duratioin set');
                        //window.timers[count] = setTimeout(function(){ setTime(count); }, interval);//execute functionality for
                        //console.log(count); 
                        //console.log('timer_R' + $('select[name="range"]').val() + '_L' + resp[entry].id);
                        //eval('timer_R' + $('select[name="range"]').val() + '_L' + resp[entry].id + '= setTimeout(function(){ setTime(count); }, interval)');
                        //console.log(eval('typeof timer_R' + $('select[name="range"]').val() + '_L' + resp[entry].id));
                        
                    } 
                    /*else {
                        eval('duration_R' + resp[entry].range_id + '_L' + resp[entry].lane_id + ' = 0');
                        clearTimeout(eval('timer_R' + resp[entry].range_id + '_L' + resp[entry].lane_id));
                        eval('timer_R' + resp[entry].range_id + '_L' + resp[entry].lane_id + ' = undefined');
                    }*/
                        
                    entries[count] = '';//initialize array element to blank
                    entries[count] += '<div class="col-md-12">';
                        entries[count] += '<div class=" lane_box panel panel-' + (lane_occupied ? 'danger' : 'success') + '" id="lane_box_' + count + '">';
                            entries[count] += '<div class="panel-heading">';
                                entries[count] += '<h3 class="panel-title" id="lane_customer_' + count + '">';
                                    entries[count] += resp[entry].text;
                                entries[count] += '</h3>';
                            entries[count] += '</div>';
                            entries[count] += '<div class="panel-body range-box">';
                                entries[count] += '<div id="timer">';
                                    entries[count] += '<span id="stopwatch_' + count + '">' + timer + '</span>';
                                entries[count] += '</div>';
                                
                                entries[count] += '<div id="timer_button_container">';
                                    entries[count] += '<button id="openorder_' + count + '" type="button" class="btn btn-primary btn-sm" style="width:40%;float:left;" in_close_mode="' + (lane_occupied ? '1' : '0') + '"' + (lane_occupied ? 'disabled' : '') + '>';//'in_close_mode' flag set to denote whether clicking on the button should close the order 
                                        entries[count] += (lane_occupied ? text_close_order : text_open_order);
                                    entries[count] += '</button>';
                                    entries[count] += '<button id="timer_' + count + '" type="button" class="btn btn-primary btn-sm" style="width:40%;float:right;" ' + (lane_occupied ? '' : ' disabled ') + '>';
                                        entries[count] += text_start_stop;
                                    entries[count] += '</button>';
                                entries[count] += '</div>';
                                
                                entries[count] += '<div id="timer_button_container">';
                                    entries[count] += '<button id="reset_' + count + '" type="button" class="btn btn-warning btn-sm" style="width:100%;margin-top:5px;">';
                                        entries[count] += text_reset;
                                    entries[count] += '</button>';
                                entries[count] += '</div>';
                                
                                entries[count] += '<div class="alert alert-info">';
                                    entries[count] += '<small>order# <strong><a href="#" id="order_' + count + '" range_id="' + $('select[name="range"]').val() + '" lane_id="' + resp[entry].id + '" oID="' + oID + '" range_order_ref="' + range_order_ref + '" start="' + start + '" stop="' + stop + '" product_id="' + product_id + '" product_price="' + product_price + '" calculate_via="' + calculate_via + '" >' + (oID=='' ? text_not_set : oID) + '</a></strong></small>';//set all custom attributes on this anchor so that they can be manipulated while handling lane: osc order id, range order reference, start timestamp, stop timestamp. all future custom attributes will be added here so that values can be fetched from single element
                                    entries[count] += '<br><br>';
                                    entries[count] += '<small><strong><a href="#" id="editorder_' + count + '"' + (lane_occupied ? '' : 'disabled') + '>Edit Order</a></strong></small>';
                                entries[count] += '</div>';
                                
                            entries[count] += '</div>';
                        entries[count] += '</div>';
                    entries[count] += '</div>';
                    
                    //reset_open_settings(resp[entry], count) 
                }
                
                for(var i=1; i<entries.length; i++){//loop through each lane entry to create html for lanes (right column)
                    if (i==1){
                        html += '<div class="row">';
                        html += entries[i];
                    } else if (i%max_entries_per_row==0){
                        html += entries[i];
                        html += '</div><div class="row">';
                    } else {
                        html += entries[i];
                    }
                }
                html += '</div>';
                $total_lanes = count;//fetch total lanes
                
                $('#lane_timers').html(html);//set html
                $("#lane_timers").mCustomScrollbar();
                
                /*$.each($('a[id^="order_"]'), function(){
                    index = $(this).attr('id').replace(/order_/i, '');//fetch index (suffix)
                    console.log(index + ' ');
                    if ($(this).attr('oID')!=''){
                        console.log('order associated ');
                        //window.timers[index] = setTimeout(function(){ setTime(index); }, interval);
                        eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = setTimeout(function(){ setTime(index); }, interval)');
                        console.log(index +  ' | ' + 'timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id'));
                    }
                });*/
               //eval('timer_R16_L39 = setTimeout(function(){ setTime(1); }, interval)');
           }  
            
        });
    });
    
    $(document).on('click', 'button[id^="openorder_"]', function(){//open order button clicked
        index = $(this).attr('id').replace(/openorder_/i, '');//fetch index (suffix)
        if ($(this).attr('in_close_mode')=='1'){//if the button is meant for closing order
            $.ajax({ //call ajax to close order
                url: 'range_operations.php', 
                method: 'post', 
                dataType: 'json',
                data: {
                    action: 'close_order', 
                    oID: $('#order_' + index).attr('oID'), 
                    range_order_ref: $('#order_' + index).attr('range_order_ref'), 
                    index: index
                }, 
                success: function(response){
                    $('#lane_box_' + response.index).removeClass('panel-danger').addClass('panel-success');
                    $('#stopwatch_' + response.index).html('00:00:00');
                    $('button#openorder_' + response.index).removeAttr('disabled').html(text_open_order).attr('in_close_mode', '');
                    $('button#timer_' + response.index).attr('disabled', 'disabled');
                    //if (typeof timers[response.index]!="undefined"){//if timer object for the lane if open, for any reason 
                    if (typeof eval('timer_R' + $('#order_' + response.index).attr('range_id') + '_L' + $('#order_' + response.index).attr('lane_id')) != "undefined"){
                        //durations[response.index] = 0;//reset duration
                        eval('duration_R' + $('#order_' + response.index).attr('range_id') + '_L' + $('#order_' + response.index).attr('lane_id') + ' = 0');
                        //clearTimeout(timers[response.index]);
                        clearTimeout(eval('timer_R' + $('#order_' + response.index).attr('range_id') + '_L' + $('#order_' + response.index).attr('lane_id')));
                        //timers[response.index] = undefined;
                        eval('timer_R' + $('#order_' + response.index).attr('range_id') + '_L' + $('#order_' + response.index).attr('lane_id') + ' = undefined');
                    }
                    /*$('#order_' + response.index).attr({
                        'oID': '', 
                        'range_order_ref': '', 
                        'start': '', 
                        'stop': '',
                        'product_id': '', 
                        'product_price': '', 
                        'calculate_via': '' 
                    }).html(text_not_set);*/
                    $('#order_' + response.index).attr('oID', '');
                    $('#order_' + response.index).attr('range_order_ref', '');
                    $('#order_' + response.index).attr('start', '');
                    $('#order_' + response.index).attr('stop', '');
                    $('#order_' + response.index).attr('product_id', '');
                    $('#order_' + response.index).attr('product_price', '');
                    $('#order_' + response.index).attr('calculate_via', '');
                    $('#order_' + response.index).html(text_not_set);
                    //$('iframe[name="order"]').src('');
                    $('iframe[name="order"]').attr('src', '');
                    $('#order_header').html('');
                    $('#reset_' + index).removeAttr('disabled');
                }
            });
        } else { //if the order is yet to be created
            //$('#lane_box_' + index).removeClass('panel-success').addClass('panel-danger');
            lane_id = $('#order_' + index).attr('lane_id');//fetch lane id
            range_id = $('#order_' + index).attr('range_id');//fetch range_id
            $('#reset_' + index).attr('disabled', 'disabled');
            $.ajax({//call ajax for creating blank order
                url: 'create_order_process_POS.php', 
                method: 'post', 
                dataType: 'json',
                data: {
                    range_order: '1', //a flag to denote that it's range order
                    range_id: range_id, 
                    lane_id: lane_id, 
                    index: index //passing index so that lane elements can be handled after getting response 
                }, 
                success: function(response){
                    //set parameters retruned by call and disable button. it will be activated after timer is stopped
                    /*$('#order_' + response.index).attr({
                     'oID': response.oID, 
                     'range_order_ref': response.range_order_ref, 
                     'start': '', 
                     'stop': '', 
                     'product_id': '', 
                     'product_price': '', 
                     'calculate_via': ''
                    }).html(response.oID);*/
                    $('#order_' + response.index).attr('oID', response.oID);
                    $('#order_' + response.index).attr('range_order_ref', response.range_order_ref);
                    $('#order_' + response.index).attr('start', '');
                    $('#order_' + response.index).attr('stop', '');
                    $('#order_' + response.index).attr('product_id', '');
                    $('#order_' + response.index).attr('product_price', '');
                    $('#order_' + response.index).attr('calculate_via', '');
                    $('#order_' + response.index).html(response.oID);
                    $('#order_header').html( text_order_header.replace('{lane}', $('#lane_customer_' + response.index).html() ).replace('{order}', response.oID) );

                    $('#timer_' + response.index).removeAttr('disabled');//enable start/stop timer button
                    
                    /*if (typeof timers[index]!="undefined"){
                        durations[index] = 0;
                        clearTimeout(timers[index]);
                        timers[index] = undefined;
                    }*/
                   $('#openorder_' + response.index).attr({
                    'in_close_mode': '', 
                    'disabled': 'disabled'
                   }).html(text_close_order);
                    
                    $('iframe[name="order"]').attr('src', '<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN; ?>edit_orders_POS.php?oID=' + response.oID + '&range_order_ref=' + response.range_order_ref + '&range_id=' + response.range_id + '&lane_id=' + response.lane_id + '&index=' + response.index);
                                    
                }
            });
        }
    });
    
    $(document).on('click', 'button[id^="timer_"]', function(){ //when start/stop button clicked
        index = $(this).attr('id').replace(/timer_/i, '');//fetch index to be used as suffix to fetch lane specific attributes
        $('#lane_box_' + index).removeClass('panel-success').addClass('panel-danger');//change panel heading from green to red to denote that the lane is occupied
        //if (typeof timers[index]=="undefined"){//if timer is not yet set for the lane
        if (typeof eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id'))=="undefined"){
            //durations[index] = 0;//set duration for the lane to 0 and will be incremented
            eval('duration_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = 0'); 
            //timers[index] = setTimeout(function(){ setTime(index); }, interval);//execute functionality for incrementing duration
            eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = setTimeout(function(){ setTime(index); }, interval)');
            $('button#openorder_' + index).attr('disabled', 'disabled');//disable open order button
            $.ajax({//call ajax for registering start timestamp
                url: 'range_operations.php', 
                method: 'post', 
                dataType: 'json', 
                data: {
                    action: 'register_start', 
                    oID: $('#order_' + index).attr('oID'), 
                    range_order_ref: $('#order_' + index).attr('range_order_ref'), 
                    index: index
                }, 
                success: function(response){
                    /*$('#order_' + response.index).attr({
                        'start': response.start, 
                        'product_id': response.product_id, 
                        'product_price': response.product_price, 
                        'calculate_via': response.calculate_via
                    });//set start attribute*/
                    $('#order_' + response.index).attr('start', response.start);
                    $('#order_' + response.index).attr('product_id', response.product_id);
                    $('#order_' + response.index).attr('product_price', response.product_price);
                    $('#order_' + response.index).attr('calculate_via', response.calculate_via);
                }
            });
        } else { //if timer evaluates to an object, it denotes that timer is already running and the button is now clicked for stopping
            //clearTimeout(timers[index]);//stop functionality that is incrementing timer
            clearTimeout(eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id')));
            //timers[index] = undefined;
            eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = undefined');
            //durations[index] = 0;
            eval('duration_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = 0');
            $.ajax({ //call ajax to register stop timestamp along with other settings
                url: 'range_operations.php',
                method: 'post', 
                dataType: 'json', 
                data: {
                    action: 'register_stop', 
                    oID: $('#order_' + index).attr('oID'), 
                    range_order_ref: $('#order_' + index).attr('range_order_ref'), 
                    index: index
                },
                success: function(response){
                    $('#order_' + response.index).attr('stop', response.stop);//set stop attribute
                    $('button#openorder_' + response.index).removeAttr('disabled').html(text_close_order).attr('in_close_mode', '1');//enable attribute to close order, change button verbiage to 'close order' and set 'in_close_mode' flag to 1
                    $('button#timer_' + index).attr('disabled', 'disabled');//now disable timer button as order is to be closed before freeing the lane
                    
                    interval_in_sec = parseInt($('#order_' + response.index).attr('stop')) - parseInt($('#order_' + response.index).attr('start'));
                    
                    $.ajax({
                        url: 'edit_orders_add_product_POS.php?action=add_product&oID=' + response.oID, 
                        method: 'post',
                        dataType: 'json',  
                        data: {
                            range_order: '1',
                            step: '5',
                            add_product_products_id: $('#order_' + response.index).attr('product_id'),
                            //prorata_value: prorata_value,  
                            add_product_categories_id: '',
                            add_product_quantity: '1',
                            range_order_ref: response.range_order_ref, 
                            index: response.index, 
                            calculate_via: $('#order_' + response.index).attr('calculate_via'), 
                            interval_in_sec: interval_in_sec, 
                            product_price: $('#order_' + response.index).attr('product_price')
                        }, 
                        success: function(response){
                            $('iframe[name="order"]').attr('src', '<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN; ?>edit_orders_POS.php?oID=' + response.oID + '&range_order_ref=' + response.range_order_ref + '&index=' + response.index);//set src for iframe
                        }
                    });
                }
            });
            
            //timers[index] = undefined;
        }
    });
    
    $(document).on('click', 'a[id^="order_"], a[id^="editorder_"]', function(event){ //on edting order
        event.preventDefault();
        //$('iframe[name="order"]').height(window.innerHeight - 250);
        //fetch index depending on which anchor tag was clicked
        if ($(this).attr('id').indexOf('editorder')!=-1){
            index = $(this).attr('id').replace(/editorder_/i, '');    
        } else {
            index = $(this).attr('id').replace(/order_/i, '');
        }
        osc_order_id = $('#order_' + index).attr('oID');//fetch osc order id to check whether it exists or not
        if (osc_order_id!=''){//proceed further only if it does not exists
            order = $('#order_' + index);//set order object
            //if (typeof eval('timer_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id'))=="undefined"){
                /*start = parseInt($(order).attr('start')) * 1000;
                var d = new Date;
                time_elapsed =   d.getTime() - start;
                
                start_with = parseInt(eval('duration_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id'))) + time_elapsed;
                eval('duration_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id') + ' = ' + start_with);
                eval('$("stopwatch_' + index + '").html(formatTime(' + start_with + '))');
                eval('timer_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id') + '= setTimeout(function(){ setTime(index); }, interval)');*/
                start = parseInt($(order).attr('start')) * 1000;
                var d = new Date;
                time_elapsed =   d.getTime() - start;
                eval('duration_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id') + ' = ' + time_elapsed);
                eval('$("stopwatch_' + index + '").html(formatTime(duration_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id') +  '))');
                eval('timer_R' + $(order).attr('range_id') + '_L' + $(order).attr('lane_id') + '= setTimeout(function(){ setTime(index); }, interval)');
            //}
            
            $('iframe[name="order"]').attr('src', '<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN; ?>edit_orders_POS.php?oID=' + $(order).attr('oID') + '&range_order_ref=' + $(order).attr('range_order_ref') + '&range_id=' + $(order).attr('range_id') + '&lane_id=' + $(order).attr('lane_id') + '&index=' + index);//set src for iframe
            
                    $('#order_header').html( text_order_header.replace('{lane}', $('#lane_customer_' + index).html() ).replace('{order}', $(order).attr('oID') ) );
        }
    });
    
    $(document).on('click', 'button[id^="reset_"]', function(){//rest lane
        index = $(this).attr('id').replace(/reset_/i, '');//fetch index
        $('#lane_box_' + index).removeClass('panel-danger').addClass('panel-success');
        $('#stopwatch_' + index).html('00:00:00');
        $('button#openorder_' + index).removeAttr('disabled').html(text_open_order).attr('in_close_mode', '');
        $('button#timer_' + index).attr('disabled', 'disabled');
        //if (typeof timers[index]!="undefined"){//if timer object for the lane if open, for any reason
        if (typeof eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id')) !="undefined"){ 
            //durations[index] = 0;//reset duration
            eval('duration_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = 0');
            //clearTimeout(timers[index]);
            clearTimeout(eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id')));
            //timers[index] = undefined;
            eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = undefined');
        }
        $('#order_' + index).attr({
            'oID': '', 
            'range_order_ref': '', 
            'start': '', 
            'stop': '',
            'product_id': '', 
            'product_price': '', 
            'calculate_via': ''
        }).html(text_not_set);
        $('#order_header').html('');
        //$('iframe[name="order"]').src('');
        $('iframe[name="order"]').attr('src', '');
    });
    

    

    
    $('select[name="range"]').val('16').trigger('change');
    $('iframe[name="order"]').attr('src', '<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN; ?>show_range_logo.php?range_id=16');
});

function setTime(index){
    //console.log(index);
    //if (typeof durations[index] != "undefined"){
        //durations[index] += interval;
        //if ($('#order_' + index).attr('range_id')!="undefined"){
            eval('duration_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' += interval');
            //$('#stopwatch_' + index).html(formatTime(durations[index]));
            $('#stopwatch_' + index).html(formatTime( eval('duration_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id')) ));
            //console.log('{' + index + '}' + ' | ' + durations[index] + ' | ' + $('#stopwatch_' + index).html());
            //timers[index] = setTimeout(function(){ setTime(index); }, interval);
            eval('timer_R' + $('#order_' + index).attr('range_id') + '_L' + $('#order_' + index).attr('lane_id') + ' = setTimeout(function(){ setTime(index); }, interval)');
        //}
    //}
}

function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {str = '0' + str;}
    return str;
}
function formatTime(time) {
    sec_temp = parseInt(time/1000);
    sec = sec_temp%60;
    
    remaining_sec = (sec_temp - sec)
     
    min_temp = parseInt(remaining_sec/60);
    min = min_temp%60;
    
    remaining_min = (min_temp - min)
     
    hrs = parseInt(remaining_min/60);
   
    return (hrs > 0 ? pad(hrs, 2) : "00") + ":" + (min > 0 ? pad(min, 2) : "00") + ":" + (sec > 0 ? pad(sec, 2) : "00");
}
</script>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

         <section>
         <!-- START Page content-->
         <section class="main-content">
            <h3>Ranges POS
               <br>
            </h3>
            <!-- START panel-->
            <div class="panel panel-default">
               
               <!-- START table-responsive-->
               
               <div class="table-responsive">
               <!-- START your table-->
<table class="table table-bordered table-hover">
        <tr>
            <td>
                <div id="clock" class="pull-left"></div>
                <div class="pull-right">
                <?php echo tep_draw_pull_down_menu('range', get_shooting_ranges(), '', 'class="form-control"'); ?>
                </div>
                <div class="pull-right"><button type="button" class="btn btn-default">Select Shooting Range: </button></div>    
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <div id="order_header" class="col-md-12" style="color:#ffffff;">
                        Lane# 12 [Order# 2167]
                    </div>
                </div>
                <form>
                    <input type="hidden" name="country" value="223" />
                    <div class="row">
                        <div id="order" class="col-md-8" style="color: #000000;">
                            <iframe name="order" src="<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN; ?>blank_page.php" style="width:100%;"></iframe>
                        </div>
                        <div class="col-md-4" id="lane_timers"></div>
                    </div>
                </form>
            </td>
        </tr>
    </table>  
               <!-- END your table-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>