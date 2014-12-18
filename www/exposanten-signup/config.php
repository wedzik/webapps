<?php
define('MAIN_ROOT_DIR', '/exposanten-signup');

define('INCLUDE_SCRIPTS_URL', 'http://' . $_SERVER['HTTP_HOST'] . MAIN_ROOT_DIR . '/_html/');
define('INCLUDE_PHP_SCRIPTS_URL', 'http://' . $_SERVER['HTTP_HOST'] . MAIN_ROOT_DIR . '/_html/php/');

define('INCLUDE_JS_PATCH', 'http://' . $_SERVER['HTTP_HOST'] . MAIN_ROOT_DIR . '/_html/js/');
define('INCLUDE_CSS_PATCH', 'http://' . $_SERVER['HTTP_HOST'] . MAIN_ROOT_DIR . '/_html/css/');

define('INCLUDE_SCRIPTS_PATCH', dirname(__FILE__).'/_html/php/');
define('INCLUDE_LIBS_PATCH', dirname(__FILE__).'/_html/libs/');
define('DATA_PATCH', $_SERVER['DOCUMENT_ROOT'].'/../datadb'.MAIN_ROOT_DIR);
define('CONFIG_EVENTS_PATCH', DATA_PATCH . '/config/');
define('CONFIG_MEMEBRS_PATCH', DATA_PATCH . '/members/');
define('NEW_MEMEBRS_FILE', 'new_members.csv');
define('CSV_DELIMITER', ';');

//define('APPLETS_ROOT_URL', 'http://' . $_SERVER['HTTP_HOST'] . MAIN_ROOT_DIR . '/_html/applets/');
define('ADMIN_PASSWORD', 'admin');

define('XML_KEY_NAME', 'name');
define('XML_KEY_WHEN', 'when');
define('XML_KEY_WHEN_NOTE', 'when_note');
define('XML_KEY_WHAT', 'what');
define('XML_KEY_WHAT_NOTE', 'what_note');
define('XML_KEY_CONFIRMATION_TEXT', 'confirmation_text');
define('XML_KEY_ACCEPT_EMAIL_SUBJECT', 'accept_email_subject');
define('XML_KEY_ACCEPT_EMAIL_TEXT', 'accept_email_text');
define('XML_KEY_REJECT_EMAIL_SUBJECT', 'reject_email_subject');
define('XML_KEY_REJECT_EMAIL_TEXT', 'reject_email_text');
define('XML_KEY_INVOICE_EMAIL_SUBJECT', 'invoice_email_subject');
define('XML_KEY_INVOICE_EMAIL_TEXT', 'invoice_email_text');

define('XML_KEY_NAME_CAPTION', 'Name');
define('XML_KEY_WHEN_CAPTION', 'When');
define('XML_KEY_WHEN_NOTE_CAPTION', 'When_note');
define('XML_KEY_WHAT_CAPTION', 'What');
define('XML_KEY_WHAT_NOTE_CAPTION', 'What_note');
define('XML_KEY_CONFIRMATION_TEXT_CAPTION', 'confirmation_text');
define('XML_KEY_ACCEPT_EMAIL_SUBJECT_CAPTION', 'accept_email_subject');
define('XML_KEY_ACCEPT_EMAIL_TEXT_CAPTION', 'accept_email_text');
define('XML_KEY_REJECT_EMAIL_SUBJECT_CAPTION', 'reject_email_subject');
define('XML_KEY_REJECT_EMAIL_TEXT_CAPTION', 'reject_email_text');
define('XML_KEY_INVOICE_EMAIL_SUBJECT_CAPTION', 'invoice_email_subject');
define('XML_KEY_INVOICE_EMAIL_TEXT_CAPTION', 'invoice_email_text');

define('STEP_1_HEADER', 'Wanneer');
define('STEP_2_HEADER', 'Wat');
define('STEP_3_HEADER', 'Wie');
define('STEP_4_HEADER', 'Bevestiging');

define('LABEL_COMPANY_NAME', 'Naam bedrijf/instelling:');
define('LABEL_CONTACT_NAME', 'Contactpersoon, naam:');
define('LABEL_ADDRESS', 'Adres:');
define('LABEL_PHONE', 'Telefoon:');
define('LABEL_EMAIL', 'E-mail:');
define('LABEL_WEBSITE', 'Website:');
define('LABEL_INDUSTRY', 'Branche:');
define('LABEL_PRODUCTS', 'Producten en/of diensten:');

define('MESSAGE_EVENT_CREATED', 'New event created');
define('MESSAGE_EVENT_NOT_CREATED', 'ERROR: New event not created.');
define('MESSAGE_WRONG_EVENT_NAME', 'ERROR: Wrong event name.');
define('MESSAGE_EVENT_ALREADY_EXISTS', 'ERROR: Event already exists.');
define('MESSAGE_EVENT_SAVED', 'Event saved.');
define('MESSAGE_EVENT_NOT_SAVED', 'ERROR: Event not saved.');

define('EMAIL_SUBJECT_ADMIN_NEW_MEMBER', 'New member notification.');
define('ADMIN_EMAIL', 'wedzikmail@gmail.com');

define('MESSAGE_SELECT_ANY_VALUE', 'Select at least one value');