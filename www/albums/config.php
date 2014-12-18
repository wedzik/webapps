<?php
define ('ROOT_DIR', '/albums');
define('INCLUDE_GLOB_SCRIPTS_PATCH', $_SERVER['DOCUMENT_ROOT'].ROOT_DIR.'/_html/');

define('INCLUDE_JS_PATCH', ROOT_DIR.'/_html/js/');
define('INCLUDE_CSS_PATCH', ROOT_DIR.'/_html/css/');

define('GALLERY_ABSOLUTE_ROOT',  $_SERVER['DOCUMENT_ROOT'].'/../datadb/albums/');
define('GALLERY_ROOT', GALLERY_ABSOLUTE_ROOT);

define('DATA_ABSOLUTE_ROOT', $_SERVER['DOCUMENT_ROOT'].'/../datadb/albums/_albums_data/');
define('DATA_ROOT', DATA_ABSOLUTE_ROOT);
define('SECURITY_PHRASE', 'change this text!');

define('DIR_NAME_FILE', '_name.txt');
define('PASSWORD_FILE', '_password.txt');
define('DIR_IMAGE_FILE', '_image.jpg');
define('DIR_DESC_FILE', '_desc.txt');
define('DIR_DESC_IN_GALLERY', TRUE);
define('DIR_DESC_IN_INFO', TRUE);
define('DIR_SORT_REVERSE', FALSE);
define('DIR_SORT_BY_TIME', FALSE);
$dir_exclude = array('_albums_data', '_album_icons');
define('DIR_EXCLUDE_REGEX', '');
define('USE_IMAGICK', FALSE);

define('SHOW_IMAGE_EXT', FALSE);
define('IMAGE_SORT_REVERSE', FALSE);
define('IMAGE_SORT_BY_TIME', FALSE);
define('ROTATE_IMAGES', TRUE);
define('IMAGE_JPEG_QUALITY', 90);
define('IMAGE_EXCLUDE_REGEX', '');

define('SHOW_FILES', TRUE);
define('SHOW_FILE_EXT', TRUE);
define('FILE_IN_NEW_WINDOW', TRUE);
define('FILE_THUMB_EXT', '.jpg');
define('FILE_SORT_REVERSE', FALSE);
define('FILE_SORT_BY_TIME', FALSE);
$file_exclude = array();
$file_ext_exclude = array('.php', '.txt', '.sell','.gitignore');
define('FILE_EXCLUDE_REGEX', '');
$file_ext_thumbs = array('.pdf' => 'pdf.png');

define('ICONS_DIR', '_album_icons/');
define('LINK_BACK', '');
define('CHARSET', 'iso-8859-1');
define('DATE_FORMAT', 'Y-m-d h:i:s');
define('DESC_EXT', '.txt');
define('SORT_DIVIDER', '--');
define('SORT_NATURAL', TRUE);
define('FONT_SIZE', 12);
define('UNDERSCORE_AS_SPACE', TRUE);
define('NL_TO_BR', FALSE);
define('SHOW_EXIF_INFO', TRUE);
define('SHOW_IPTC_INFO', TRUE);
define('SHOW_INFO_BY_DEFAULT', FALSE);
define('ROUND_CORNERS', 3);

define('THUMB_MAX_WIDTH', 170);
define('THUMB_MAX_HEIGHT', 130);
define('THUMB_ENLARGE', FALSE);
define('THUMB_JPEG_QUALITY', 75);

define('USE_PREVIEW', TRUE);
define('PREVIEW_MAX_WIDTH', 1000);
define('PREVIEW_MAX_HEIGHT', 600);
define('PREVIEW_ENLARGE', FALSE);
//define('PREVIEW_ENLARGE', TRUE);
define('PREVIEW_JPEG_QUALITY', 75);

define('LOW_IMAGE_RESAMPLE_QUALITY', FALSE);
define('KEYBOARD_NAVIGATION', TRUE);
define('WATERMARK', '');
define('WATERMARK_FRACTION', 0.1);

define('MPO_STEREO_IMAGE', TRUE);
define('MPO_STEREO_DOTS', TRUE);
define('MPO_STEREO_MAX_WIDTH', 300);
define('MPO_STEREO_MAX_HEIGHT', 300);
define('MPO_FULL_IMAGE', TRUE);
define('MPO_FULL_ANAGLYPH', TRUE);
define('MPO_FULL_MAX_WIDTH', 1200);
define('MPO_FULL_MAX_HEIGHT', 600);
define('MPO_SPACING', 20);

define('INFO_BOX_WIDTH', 250);
define('MENU_BOX_HEIGHT', 70);
define('NAV_BAR_HEIGHT', 25);
define('THUMB_BORDER_WIDTH', 1);
define('THUMB_MARGIN', 10);
define('THUMB_BOX_MARGIN', 7);
define('THUMB_BOX_EXTRA_HEIGHT', 14);
define('THUMB_CHARS_MAX', 20);
define('FULLIMG_BORDER_WIDTH', 5);

define('NAVI_CHARS_MAX', 100);
define('OVERLAY_OPACITY', 90);
define('FADE_DURATION_MS', 300);
define('SLIDESHOW_DELAY_SEC', 5);

define('SHOW_MAX_IMAGES', FALSE);
define('SHOW_IMAGE_DAYS', FALSE);
define('DELETE_IMAGE_DAYS', FALSE);

define('TEXT_GALLERY_NAME', 'Image Gallery');
define('TEXT_HOME', 'Home');
define('TEXT_CLOSE_IMG_VIEW', 'Close Image');
define('TEXT_ACTUAL_SIZE', 'Actual Size');
define('TEXT_FULLRES', 'Full resolution');
define('TEXT_PREVIOUS', '<< Previous');
define('TEXT_NEXT', 'Next >>');
define('TEXT_INFO', 'Information');
define('TEXT_DOWNLOAD_IMG', 'Download');
define('TEXT_DOWNLOAD', 'Download full-size image');
define('TEXT_SLIDESHOW', 'Slideshow');
define('TEXT_NO_IMAGES', 'No Images in gallery');
define('TEXT_DATE', 'Date');
define('TEXT_FILESIZE', 'File size');
define('TEXT_IMAGESIZE', 'Full Image');
define('TEXT_DISPLAYED_IMAGE', 'Displayed Image');
define('TEXT_DIR_NAME', 'Gallery Name');
define('TEXT_IMAGE_NAME', 'Image Name');
define('TEXT_FILE_NAME', 'File Name');
define('TEXT_DIRS', 'Sub galleries');
define('TEXT_IMAGES', 'Images');
define('TEXT_IMAGE_NUMBER', 'Image number');
define('TEXT_FILES', 'Files');
define('TEXT_DESCRIPTION', 'Description');
define('TEXT_DIRECT_LINK_GALLERY', 'Direct link to Gallery');
define('TEXT_DIRECT_LINK_IMAGE', 'Direct link to Image');
define('TEXT_NO_PREVIEW_FILE', 'No Preview for file');
define('TEXT_IMAGE_LOADING', 'Image Loading ');
define('TEXT_LINKS', 'Links');
define('TEXT_NOT_SCALED', 'Not Scaled');
define('TEXT_LINK_BACK', 'Back to my site');
define('TEXT_THIS_IS_FULL', 'Full');
define('TEXT_THIS_IS_PREVIEW', 'Preview');
define('TEXT_SCALED_TO', 'Scaled to');
define('TEXT_YES', 'Yes');
define('TEXT_NO', 'No');
define('TEXT_FIRST_VIEW', 'This is first view of this image. Refresh page to get information.');

define('TEXT_EXIF', 'EXIF');
define('TEXT_EXIF_DATE', 'EXIF Date');
define('TEXT_EXIF_CAMERA', 'Camera');
define('TEXT_EXIF_ISO', 'ISO');
define('TEXT_EXIF_SHUTTER', 'Shutter Speed');
define('TEXT_EXIF_APERTURE', 'Aperture');
define('TEXT_EXIF_FOCAL', 'Focal Length');
define('TEXT_EXIF_FLASH', 'Flash fired');
define('TEXT_EXIF_MISSING', 'No EXIF information in image');

define('TEXT_IPTC', 'IPTC');
define('TEXT_IPTC_TITLE', 'Document Title');
define('TEXT_IPTC_URGENCY', 'Urgency');
define('TEXT_IPTC_CATEGORY', 'Category');
define('TEXT_IPTC_SUBCATEGORIES', 'Subcategories');
define('TEXT_IPTC_SPECIALINSTRUCTIONS', 'Special Instructions');
define('TEXT_IPTC_CREATIONDATE', 'Creation Date');
define('TEXT_IPTC_AUTHORBYLINE', 'Author Byline');
define('TEXT_IPTC_AUTHORTITLE', 'Author Title');
define('TEXT_IPTC_CITY', 'City');
define('TEXT_IPTC_STATE', 'State');
define('TEXT_IPTC_COUNTRY', 'Country');
define('TEXT_IPTC_OTR', 'OTR');
define('TEXT_IPTC_HEADLINE', 'Headline');
define('TEXT_IPTC_SOURCE', 'Source');
define('TEXT_IPTC_PHOTOSOURCE', 'Photo Source');
define('TEXT_IPTC_COPYRIGHT', 'Copyright');
define('TEXT_IPTC_CAPTION', 'Caption');
define('TEXT_IPTC_CAPTIONWRITER', 'Caption Writer');
define('TEXT_IPTC_MISSING', 'No IPTC information in image');
define('PREPARE_PAGE_PASSWORD', 'admin');

$color_body_back = '#000000';
$color_body_text = '#aaaaaa';
$color_body_link = '#ffffff';
$color_body_hover = '#aaaaaa';

$color_thumb_border = '#606060';
$color_fullimg_border = '#ffffff';

$color_dir_box_border = '#505050';
$color_dir_box_back = '#000000';
$color_dir_box_text = '#aaaaaa';
$color_dir_hover = '#ffffff';
$color_dir_hover_text = '#000000';

$color_img_box_border = '#505050';
$color_img_box_back = '#202020';
$color_img_box_text = '#aaaaaa';
$color_img_hover = '#ffffff';
$color_img_hover_text = '#000000';

$color_file_box_border = '#404040';
$color_file_box_back = '#101010';
$color_file_box_text = '#aaaaaa';
$color_file_hover = '#ffffff';
$color_file_hover_text = '#000000';

$color_desc_box_border = '#404040';
$color_desc_box_back = '#202020';
$color_desc_box_text = '#aaaaaa';

$color_button_border = '#808080';
$color_button_back = '#000000';
$color_button_text = '#aaaaaa';
$color_button_border_off = '#505050';
$color_button_back_off = '#000000';
$color_button_text_off = '#505050';
$color_button_hover = '#ffffff';
$color_button_hover_text = '#000000';
$color_button_on = '#aaaaaa';
$color_button_text_on = '#000000';

$color_overlay = '#000000';
$color_menu_hover = '#ffffff';
