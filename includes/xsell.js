/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
function addToList(){	
	var prod_id_master;
	if (update_cross.products_id.value==''){
		var container = getObjectRef('div_listing');
		var prod_menu = container.childNodes[container.childNodes.length-1];
		prod_id_master = prod_menu.options[prod_menu.options.selectedIndex].value;
		
		if (prod_id_master==''){
			alert("Please select main product to proceed further");
			prod_menu.focus();
			prod_menu.select();
			return false;		
		}
	}
	else{
		prod_id_master = 'P' + update_cross.products_id.value;
	}
	
	container = getObjectRef('div_related');
	prod_menu = container.childNodes[container.childNodes.length-1];
	var prod_id_related = prod_menu.options[prod_menu.options.selectedIndex].value;
	var prod_name_related = prod_menu.options[prod_menu.options.selectedIndex].text;
	
	if (prod_id_related==''){
		alert("Please select product to proceed further");
		prod_menu.focus();
		prod_menu.select();
		return false;		
	}
	
	if (prod_id_master==prod_id_related){
		alert("Main product and related products can not be same");
		prod_menu.focus();
		prod_menu.select();
		return false;		
	}
	if (relatedProdIDExists(prod_id_related)){
		alert("Related product already added");
		prod_menu.focus();
		prod_menu.select();
		return false;		
	}
	
	var table = getTableReference();
	appendRow(table, prod_name_related, prod_id_related);	
}

function getTableReference(){
	var table = getObjectRef('tab_products');
	if (navigator.appName.toLowerCase()=='microsoft internet explorer'){
		return table.childNodes[0];
	}else{
		return table;
	}			
}

function getTableRowNum(){
	var table = getTableReference();
	//if (navigator.appName.toLowerCase()=='microsoft internet explorer'){
		return table.childNodes.length;
	//}else{
	//	return table.childNodes.length;
	//}			
}

function getTableBaseIndex(){
	var table = getTableReference();
	//if (navigator.appName.toLowerCase()=='microsoft internet explorer'){
		return 0;
	//}else{
	//	return 1;
	//}			
}

function getTableSortOrderColumnIndex(){
	return 0;
}

function getTableProdNameColumnIndex(){
	return 1;
}

function getTableProdIDColumnIndex(){
	return 2;
}

function getTableActionColumnIndex(){
	return 3;
}

function getTdMoveUpIndex(){
	return 0;
}

function getTdMoveDownIndex(){
	return 1;
}

function getTdDelIndex(){
	return 2;
}

function relatedProdIDExists(curProdID){
	var table = getTableReference();
	var tableBaseIndex = getTableBaseIndex();
	var prodIDClmIndex = getTableProdIDColumnIndex();
	var td;
	var blnResp = false;
	
	for(var i=tableBaseIndex+1; i<table.childNodes.length; i++){
		td = table.childNodes[i].childNodes[prodIDClmIndex];
		if (curProdID.replace('P', '')==td.innerHTML){
			blnResp = true;
			break;
		}
	}
	return blnResp;
}

function setActionStatus(){
	var table = getTableReference();
	var tableBaseIndex = getTableBaseIndex();
	var actionClmIndex = getTableActionColumnIndex();
	var up = getTdMoveUpIndex();
	var down = getTdMoveDownIndex();
	var del = getTdDelIndex();
	var td;
	var input;
	for(var i=tableBaseIndex+1; i<table.childNodes.length; i++){
		td = table.childNodes[i].childNodes[actionClmIndex];		
		if (i==(tableBaseIndex+1)){
			td.childNodes[up].style.display = 'none';
			td.childNodes[down].style.display = (i==table.childNodes.length-1 ? 'none' : 'inline');
			td.childNodes[del].style.display = 'inline';
		}else if(i==(table.childNodes.length-1)){
			td.childNodes[up].style.display = 'inline';
			td.childNodes[down].style.display = 'none';
			td.childNodes[del].style.display = 'inline';
		}else{
			td.childNodes[up].style.display = 'inline';
			td.childNodes[down].style.display = 'inline';
			td.childNodes[del].style.display = 'inline';
		}
	}
}

function getRelatedProdIDs(){
	var table = getTableReference();
	var tableBaseIndex = getTableBaseIndex();
	var prodIDClmIndex = getTableProdIDColumnIndex();
	var sortOrderClmIndex = getTableSortOrderColumnIndex();
	var td_id, td_sort;
	var resp = new Array();
	for(var i=tableBaseIndex+1; i<table.childNodes.length; i++){
		td_sort = table.childNodes[i].childNodes[sortOrderClmIndex];
		td_id = table.childNodes[i].childNodes[prodIDClmIndex];
		resp.push(td_sort.innerHTML + "|" + td_id.innerHTML);
	}
	return resp;
}

function appendRow(table, key, value){
	//alert(table + '\n' + key + '\n' + value);
	try{		
		var tr;
		var td;
		var input;
		
		value = value.replace('P', '');	
		
		if (table.childNodes.length==0 || (table.childNodes.length==1 && table.childNodes[0].nodeName.toLowerCase().indexOf('text')!=-1)){
			tr = document.createElement('tr');
			
			td = document.createElement('td');
			td.setAttribute('class', '');
			td.setAttribute('className', '');
			td.innerHTML = '&nbsp;<b>Sort Order</b>&nbsp;';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.setAttribute('class', '');
			td.setAttribute('className', '');
			td.innerHTML = '&nbsp;<b>Product Name</b>&nbsp;';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.setAttribute('class', '');
			td.setAttribute('className', '');
			td.innerHTML = '&nbsp;<b>Product ID</b>&nbsp;';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.setAttribute('class', '');
			td.setAttribute('className', '');
			td.innerHTML = '&nbsp;<b>Action</b>&nbsp;';
			tr.appendChild(td);
			
			table.appendChild(tr);
		}
		
		var row_num = getTableRowNum();
		 
		tr = document.createElement('tr');
			
		td = document.createElement('td');
		td.setAttribute('class', '');
		td.setAttribute('className', '');
		td.innerHTML = row_num;
		tr.appendChild(td);
			
		td = document.createElement('td');
		td.setAttribute('class', '');
		td.setAttribute('className', '');
		td.innerHTML = key;
		tr.appendChild(td);
			
		td = document.createElement('td');
		td.setAttribute('class', '');
		td.setAttribute('className', '');
		td.innerHTML = value;
		tr.appendChild(td);
		
		td = document.createElement('td');
		td.setAttribute('class', '');
		td.setAttribute('className', '');
		td.setAttribute('align', 'right');
	
		input = document.createElement('input');
		input.setAttribute('type', 'button');
		input.value='Up';
		input.style.display = 'none';
		input.onclick = function(){moveUp(input);}
		td.appendChild(input);
		
		input = document.createElement('input');
		input.setAttribute('type', 'button');
		input.value='Down';
		input.style.display = 'none';
		input.onclick = function(){moveDown(input);}
		td.appendChild(input);
		
		input = document.createElement('input');
		input.setAttribute('type', 'button');
		input.value='Delete';
		input.style.display = 'none';
		input.onclick = function(){deleteRow(input);}
		td.appendChild(input);
		
		tr.appendChild(td);
		
		table.appendChild(tr);
		
		setActionStatus();
	}catch(e){alert(e);}
}

function moveUp(buttonRef){
	try{
		var current_node = buttonRef.parentNode.parentNode;
		var previous_node = current_node.previousSibling;
		
		var prodNameClmIndex = getTableProdNameColumnIndex();
		var prodIDClmIndex = getTableProdIDColumnIndex();
		
		var temp_id = current_node.childNodes[prodIDClmIndex].innerHTML;
		var temp_name = current_node.childNodes[prodNameClmIndex].innerHTML;
			
		current_node.childNodes[prodIDClmIndex].innerHTML = previous_node.childNodes[prodIDClmIndex].innerHTML;
		current_node.childNodes[prodNameClmIndex].innerHTML = previous_node.childNodes[prodNameClmIndex].innerHTML;
		
		previous_node.childNodes[prodIDClmIndex].innerHTML = temp_id;
		previous_node.childNodes[prodNameClmIndex].innerHTML = temp_name;
	}catch(e){alert(e);}
}

function moveDown(buttonRef){
	try{
		var current_node = buttonRef.parentNode.parentNode;
		var next_node = current_node.nextSibling;
		
		var prodNameClmIndex = getTableProdNameColumnIndex();
		var prodIDClmIndex = getTableProdIDColumnIndex();
		
		var temp_id = current_node.childNodes[prodIDClmIndex].innerHTML;
		var temp_name = current_node.childNodes[prodNameClmIndex].innerHTML;
			
		current_node.childNodes[prodIDClmIndex].innerHTML = next_node.childNodes[prodIDClmIndex].innerHTML;
		current_node.childNodes[prodNameClmIndex].innerHTML = next_node.childNodes[prodNameClmIndex].innerHTML;
		
		next_node.childNodes[prodIDClmIndex].innerHTML = temp_id;
		next_node.childNodes[prodNameClmIndex].innerHTML = temp_name;
	}catch(e){alert(e);}	
}

function deleteRow(buttonRef){
	var table = getTableReference();
	buttonRef.parentNode.parentNode.style.backgroundColor='yellow';
	if (!confirm('This action will remove the product from selection!!')){
		buttonRef.parentNode.parentNode.style.backgroundColor='';
	}else{
		var current_node = buttonRef.parentNode.parentNode;
		var remove_node = current_node;
		var sortOrderClmIndex = getTableSortOrderColumnIndex();
		var td;
		current_node = current_node.nextSibling;
		while(current_node){
			td = current_node.childNodes[sortOrderClmIndex];
			td.innerHTML = parseInt(td.innerHTML) - 1;
			current_node = current_node.nextSibling;
		}
		table.removeChild(remove_node);
	}
}

function saveSelection(){
	try{
		var error_msg = 'Error report:-\n'; 
		var blnPsd = true;
		var prod_id_master;
		if (update_cross.products_id.value==''){
			var container = getObjectRef('div_listing');
			var prod_menu = container.childNodes[container.childNodes.length-1];
			prod_id_master = prod_menu.options[prod_menu.options.selectedIndex].value;
			
			if (prod_id_master==''){
				error_msg += '- Main product not selected.\n';
				//prod_menu.focus();
				//prod_menu.select();
				blnPsd = false;		
			}
		}
		else{
			prod_id_master = 'P' + update_cross.products_id.value;
		}
		
		/*if (getTableRowNum()-1<=0){
			error_msg += '- Related product(s) not specified.\n';
			blnPsd = false;
		}
		if (!blnPsd){
			alert(error_msg);
		}
		else{*/
			update_cross.products_id.value = prod_id_master.replace('P', ''); 
			update_cross.related_products_id.value = getRelatedProdIDs().join('&join;');
		//}
		return blnPsd;
	}catch(e){
		alert(e);
		return false;
	}
}