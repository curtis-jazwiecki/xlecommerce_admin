/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};
CKEDITOR.editorConfig = function( config ) {
 //  config.filebrowserBrowseUrl = 'ckfinder/ckfinder.html';
 //  config.filebrowserImageBrowseUrl = 'ckfinder/ckfinder.html?type=Images';
 //  config.filebrowserFlashBrowseUrl = 'ckfinder/ckfinder.html?type=Flash';
 //  config.filebrowserUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
 //  config.filebrowserImageUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
  // config.filebrowserFlashUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
};

CKEDITOR.editorConfig = function(config) {
// ...
   config.filebrowserBrowseUrl = 'kcfinder/browse.php?opener=ckeditor&type=files';
   config.filebrowserImageBrowseUrl = 'kcfinder/browse.php?opener=ckeditor&type=images';
   config.filebrowserFlashBrowseUrl = 'kcfinder/browse.php?opener=ckeditor&type=flash';
   config.filebrowserUploadUrl = 'kcfinder/upload.php?opener=ckeditor&type=files';
   config.filebrowserImageUploadUrl = 'kcfinder/upload.php?opener=ckeditor&type=images';
   config.filebrowserFlashUploadUrl = 'kcfinder/upload.php?opener=ckeditor&type=flash';
// ...
};