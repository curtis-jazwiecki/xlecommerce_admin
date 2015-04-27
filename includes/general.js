function SetFocus() {
  if (document.forms.length > 0) {
    var field = document.forms[0];
    for (i=0; i<field.length; i++) {
      if ( (field.elements[i].type != "image") &&
           (field.elements[i].type != "hidden") &&
           (field.elements[i].type != "reset") &&
           (field.elements[i].type != "submit") ) {

        document.forms[0].elements[i].focus();

        if ( (field.elements[i].type == "text") ||
             (field.elements[i].type == "password") )
          document.forms[0].elements[i].select();

        break;
      }
    }
  }
}

function rowOverEffect(object) {
  if (object.className == 'dataTableRow') object.className = 'dataTableRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'dataTableRowOver') object.className = 'dataTableRow';
}

//BOF AJAX functionality 
function getXmlHttpRequestObject(){
	var xmlHttpObject;
	try{
		xmlHttpObject = new XMLHttpRequest();
		return xmlHttpObject;
	}catch(e){
		try{
			xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP");
			return xmlHttpObject;
		}catch(e){
			try{
				xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP");
				return xmlHttpObject;				
			}catch(e){
				return false;
			}
		}		
	}	
}

function getObjectRef(idVal){
	return document.getElementById(idVal);
}

function getSelectElement(containerRef){	
	selectElemCount = getSelectElementsCount(containerRef);
	if(selectElemCount>0){
		containerRef.appendChild(document.createElement('br'));
	}	
	selectElem = document.createElement('select');
	selectElem.setAttribute('id', 'menu_' + selectElemCount);	

	return selectElem;
}

function addOptionElement(dropdownRef, key, val){
	dropdownRef.options.length++;
	dropdownRef.options[dropdownRef.options.length-1].text = key;
	dropdownRef.options[dropdownRef.options.length-1].value = val;
}

function getSelectElementsCount(containerRef){
	var count = 0;
	for(var i=0; i<containerRef.childNodes.length; i++){
		if (containerRef.childNodes[i].type && containerRef.childNodes[i].type.toLowerCase().indexOf('select')!=-1){
			count++;
		}
	}
	return count;
}

//EOF AJAX functionality