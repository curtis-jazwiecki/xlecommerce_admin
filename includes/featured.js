//featured.js
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
function displaySelection(containerElemID, id, query_type){
	try{
		var container = getObjectRef(containerElemID);
		selectBox = getSelectElement(container);
		container.appendChild(selectBox);
		populate_dropdown(selectBox, id, query_type);
		
	}catch(e){alert(e);}
}

function get_add_button_ref(container){
	if (container.getAttribute('addbuttonid')!='' && 
		container.getAttribute('addbuttonid')!=null &&
		container.getAttribute('addbuttonid')!='undefined'){
		return getObjectRef(container.getAttribute('addbuttonid'));
	}
	else{
		return null;
	}
}

function populate_dropdown(selectBox, id, query_type){
	//var span_loader = getObjectRef('span_loader');
	var span_loader = selectBox.parentNode.nextSibling;
	span_loader.style.display = 'block';
	var btn_add = get_add_button_ref(selectBox.parentNode);

	selectBox.options.length = 0;	
	var xmlHttp = getXmlHttpRequestObject();
	xmlHttp.onreadystatechange = function stateChanged(){
			if (xmlHttp.readyState==4){
				try{
				var xmlDoc = xmlHttp.responseXML;
				if (xmlDoc != null){
					var rootNode;
					var isProductCol = false;
					if (xmlDoc.getElementsByTagName('products')!=null && xmlDoc.getElementsByTagName('products')!=undefined && xmlDoc.getElementsByTagName('products').length>0){
						rootNode = xmlDoc.getElementsByTagName('products')[0];
						addOptionElement(selectBox, '-- Select Product --', '');
						if (btn_add){
							btn_add.style.display = 'block';
						}else{
							if (query_type=='F'){
								selectBox.onchange = function(){registerProductID(selectBox);}
							}						
						}					
						isProductCol = true;
					}else if (xmlDoc.getElementsByTagName('categories')!=null && xmlDoc.getElementsByTagName('categories')!=undefined && xmlDoc.getElementsByTagName('categories').length>0){
						rootNode = xmlDoc.getElementsByTagName('categories')[0];
						addOptionElement(selectBox, '-- Select Category --', '');
						selectBox.onchange = function(){moveLevelDown(selectBox, query_type);}
					}
					else{
						addOptionElement(selectBox, 'No info available', '');
					}

					if(rootNode){
						for(var i=0; i<rootNode.childNodes.length; i++){
							keyNode = rootNode.childNodes[i].getElementsByTagName('key')[0];
							valNode = rootNode.childNodes[i].getElementsByTagName('value')[0];
							if (keyNode.childNodes[0] && valNode.childNodes[0]){
								addOptionElement(selectBox, keyNode.childNodes[0].nodeValue, valNode.childNodes[0].nodeValue);
							}
						}
					}
				} else {
					addOptionElement(selectBox, 'No info available', '');
				}
				span_loader.style.display = 'none';				
				} catch(e){
					alert(e);
				}
			}
	}
  	xmlHttp.open("GET","ajax_catalog_list.php?id=" + id + "&type=" + query_type, true);
  	xmlHttp.send(null);
}

function moveLevelDown(selectElem, query_type){
	try	{
		//var container = getObjectRef('div_listing');
		var container = selectElem.parentNode;
		var btn_add = get_add_button_ref(container);
		if (btn_add){
			btn_add.style.display = 'none';
		}
		var selectIndex = parseInt(selectElem.id.replace('menu_', ''));
		var removeBrTag = false;
		for(var i=0; i<container.childNodes.length; i++){
			if (container.childNodes[i]){
				if (container.childNodes[i].type && container.childNodes[i].type.toLowerCase().indexOf('select')!=-1){
					currentIndex = parseInt(container.childNodes[i].id.replace('menu_', ''));
					if(currentIndex>selectIndex){
						container.removeChild(container.childNodes[i]);
						i--;						
					}else if(currentIndex==selectIndex){
						removeBrTag = true;
					}
				}
				if(removeBrTag && container.childNodes[i].nodeName.toLowerCase()=='br'){
					container.removeChild(container.childNodes[i]);
					i--;
				}
			}
		}
		if (selectElem.options.selectedIndex>0){
			//displaySelection('div_listing', selectElem.options[selectElem.options.selectedIndex].value, query_type);		
			displaySelection(container.id, selectElem.options[selectElem.options.selectedIndex].value, query_type);
		}
	}catch(e){alert(e);}
}

function registerProductID(selectElem){
	var productID = selectElem.options[selectElem.options.selectedIndex].value;
	if (productID && productID!=''){
		productID = productID.replace('P', '');
		new_feature.products_id.value = productID;
	}
}
