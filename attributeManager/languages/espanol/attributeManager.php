<?php
/*
  $Id: attributeManager.php,v 1.0 21/02/06 Sam West$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
  
  English translation to AJAX-AttributeManager-V2.7
  
  by Shimon Doodkin
  http://help.me.pro.googlepages.com
  helpmepro1@gmail.com
*/

//attributeManagerPrompts.inc.php

define('AM_AJAX_YES', 'Si');
define('AM_AJAX_NO', 'No');
define('AM_AJAX_UPDATE', 'Actualizar');
define('AM_AJAX_CANCEL', 'Cancelar');
define('AM_AJAX_OK', 'OK');

define('AM_AJAX_SORT', 'Ordenar:');
define('AM_AJAX_TRACK_STOCK', 'Track Stock?');
define('AM_AJAX_TRACK_STOCK_IMGALT', 'Track this attribute stock ?');

define('AM_AJAX_ENTER_NEW_OPTION_NAME', 'Nuevo atributo');
define('AM_AJAX_ENTER_NEW_OPTION_VALUE_NAME', 'Nuevo valor');
define('AM_AJAX_ENTER_NEW_OPTION_VALUE_NAME_TO_ADD_TO', 'Nuevo nombre de valor a añadir a %s');

define('AM_AJAX_PROMPT_REMOVE_OPTION_AND_ALL_VALUES', 'Esta seguro de que quiere borrar los atributos de %s y todos sus valores para este producto?');
define('AM_AJAX_PROMPT_REMOVE_OPTION', 'Seguro que quiere borrar %s de este producto?');
define('AM_AJAX_PROMPT_STOCK_COMBINATION', 'Are you sure you want to remove this stock combination from this product?');

define('AM_AJAX_PROMPT_LOAD_TEMPLATE', 'Seguro que quiere recuperar %s de la plantilla? <br />Se sobreescribirán los atributos actuales del producto. La operación no se puede deshacer');
define('AM_AJAX_NEW_TEMPLATE_NAME_HEADER', 'Por favor incluya el nomber de la nueva plantilla. O...');
define('AM_AJAX_NEW_NAME', 'Nuevo nombre:');
define('AM_AJAX_CHOOSE_EXISTING_TEMPLATE_TO_OVERWRITE', ' ...<br /> ... escoja una que exista para sobreescribirla');
define('AM_AJAX_CHOOSE_EXISTING_TEMPLATE_TITLE', 'Ya existe:'); 
define('AM_AJAX_RENAME_TEMPLATE_ENTER_NEW_NAME', 'Por favor incluya el nuevo nombre para la plantilla %s');
define('AM_AJAX_PROMPT_DELETE_TEMPLATE', 'Confirme borrado de la plantilla %s?<br>La operación no se puede deshacer!');

//attributeManager.php

define('AM_AJAX_ADDS_ATTRIBUTE_TO_OPTION', 'Adds the selected attribute on the left to the %s option');
define('AM_AJAX_ADDS_NEW_VALUE_TO_OPTION', 'Adds a new value to the %s option');
define('AM_AJAX_PRODUCT_REMOVES_OPTION_AND_ITS_VALUES', 'Removes the option %1$s and the %2$d option value(s) below it  from this product');
define('AM_AJAX_CHANGES', 'Changes'); 
define('AM_AJAX_LOADS_SELECTED_TEMPLATE', 'Loads the selected template');
define('AM_AJAX_SAVES_ATTRIBUTES_AS_A_NEW_TEMPLATE', 'Saves the current attributes as a new template');
define('AM_AJAX_RENAMES_THE_SELECTED_TEMPLATE', 'Renames the selected template');
define('AM_AJAX_DELETES_THE_SELECTED_TEMPLATE', 'Deletes the selected template');
define('AM_AJAX_NAME', 'Name');
define('AM_AJAX_ACTION', 'Action');
define('AM_AJAX_PRODUCT_REMOVES_VALUE_FROM_OPTION', 'Removes %1$s from %2$s, from this product');
define('AM_AJAX_MOVES_VALUE_UP', 'Moves option value up');
define('AM_AJAX_MOVES_VALUE_DOWN', 'Moves option value down');
define('AM_AJAX_ADDS_NEW_OPTION', 'Adds a new option to the list');
define('AM_AJAX_OPTION', 'Option:');
define('AM_AJAX_VALUE', 'Value:');
define('AM_AJAX_PREFIX', 'Prefix:');
define('AM_AJAX_PRICE', 'Price:');
define('AM_AJAX_SORT', 'Sort:');
define('AM_AJAX_ADDS_NEW_OPTION_VALUE', 'Adds a new option value to the list');
define('AM_AJAX_ADDS_ATTRIBUTE_TO_PRODUCT', 'Adds the attribute to the current product');
define('AM_AJAX_QUANTITY', 'Quantity');
define('AM_AJAX_PRODUCT_REMOVE_ATTRIBUTE_COMBINATION_AND_STOCK', 'Removes this attribute combination and stock from this product');
define('AM_AJAX_UPDATE_OR_INSERT_ATTRIBUTE_COMBINATIONBY_QUANTITY', 'Update or Insert the attribute combination with the given quantity');

//attributeManager.class.php
define('AM_AJAX_TEMPLATES', '-- Templates --');
?>
