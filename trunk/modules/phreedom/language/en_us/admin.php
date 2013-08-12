<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /modules/phreedom/language/en_us/admin.php
//

// headings
define('MENU_HEADING_MY_COMPANY','My Company');
define('MENU_HEADING_CONFIG','Configuration');
define('TEXT_DEFAULT_GL_ACCOUNTS','Default GL Accounts');
define('MENU_HEADING_EMAIL','Email Preferences');
define('TEXT_EXTRA_TABS', 'Custom Tabs');
define('TEXT_EXTRA_FIELDS', 'Custom Fields');
define('TEXT_LOCAL','Local');
define('TEXT_DEBUG','Debug and Troubleshooting');
define('TEXT_LEGEND','Legend');
define('TEXT_TAB_NAME','Tab Title');
define('TEXT_TABS','Tabs');
define('TEXT_REQUIRED','REQUIRED');
define('HEADING_MODULE_IMPORT','Module Data Import/Export');
define('IE_HEADING_TITLE','Import/Export and Beginning Balances');
define('TEXT_TABLE_STATS','Table Statistics');
define('TEXT_ENGINE','DB Engine');
define('TEXT_NUM_ROWS','Number of Rows');
define('TEXT_COLLATION','Collation');
define('TEXT_NEXT_ID','Next Row ID');
define('TEXT_USE_IN_FILTER','Use in inventory filter');
define('TEXT_SETTINGS','Settings');

// Defines for login screen
define('HEADING_TITLE', 'PhreeBooks Login');
define('TEXT_LOGIN_NAME', 'Username: ');
define('TEXT_LOGIN_PASS', 'Password: ');
define('TEXT_LOGIN_COMPANY','Select Company: ');
define('TEXT_LOGIN_LANGUAGE','Select Language: ');
define('TEXT_LOGIN_THEME','Select Theme');
define('TEXT_LOGIN_MENU','Select Menu Location');
define('TEXT_LOGIN_COLORS','Select Color Scheme');
define('TEXT_PASSWORD_FORGOTTEN', 'Resend Password');
define('TEXT_LOGIN_BUTTON','Login');
define('TEXT_FORM_PLEASE_WAIT','Please wait ... If upgrading, this may take a while.');
define('TEXT_COPYRIGHT_NOTICE','This program is free software: you can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation, either version 3 of 
the License, or any later version. This program is distributed 
in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS 
FOR A PARTICULAR PURPOSE. See the GNU General Public License for 
more details. The license that is bundled with this package is 
located %s.');

// General
define('PB_PF_DEF_CURRENCY','Default Currency');
define('PB_PF_NULL_DEF_CURRENCY','Null 0 - Default Currency');
define('PB_PF_NULL_POSTED_CURRENCY','Null 0 - Posted Currency');
define('PB_PF_POSTED_CURRENCY','Posted Currency');
define('PB_PF_ROUND_DECIMAL','Round Decimal Places');
define('PB_PF_ROUND_PRECISE','Round Precision');
define('PB_PF_USER_NAME','User Name');
define('GEN_DEFAULT_STORE','Default Store');
define('GEN_DEF_CASH_ACCT','Default Cash Account');
define('GEN_RESTRICT_STORE','Restrict Entries to this Store?');
define('GEN_DEF_AR_ACCT','Default Receivables Account');
define('GEN_DEF_AP_ACCT','Default Payables Account');
define('GEN_RESTRICT_PERIOD','Restrict Posts to Current Period?');
define('GEN_AUDIT_DB_DATA_BACKUP','Audit Log Database Table Backed Up');
define('GEN_AUDIT_DB_DATA_CLEAN','Audit Log Database Table Cleaned');
define('HEADING_TITLE_CRASH_TITLE','PhreeBooks SQL Error Trace Information');
define('HEADING_TITLE_CRASH_INFORMATION','PhreeBooks has encountered an unexpected error. Click on the button below to download the debug trace file information to send to the PhreeBooks Development Team for troubleshooting assistance.');
define('HEADING_TITLE_CRASH_BUTTON','Download Debug Information');
define('GENERAL_CONFIG_SAVED','Configuration values have been saved.');
define('GEN_ADM_TOOLS_SEQ_HEADING','Change Various Sequence Numbers');
define('GEN_ADM_TOOLS_SEQ_DESC','Changes to the sequencing can be made here.<br />NOTE 1: PhreeBooks does not allow duplicate sequences, be sure the new starting sequence will not conflict with any currently posted values.<br />Note 2: The next_deposit_num is generated by the system and uses the current date.<br />Note 3: The next_check_num can be set at the payment screen prior to posting a payment and will continue from the entered value.');
define('TEXT_THEMES_COLORS_TITLE','Themes and Color Schemes');
define('TEXT_THEMES_COLORS_DESC','Set your prefered theme and color scheme. Press the Save icon to switch theme and see it in action.');
define('GEN_ADMIN_CANNOT_CHANGE_ROLES','Your permissions do not allow the users role/security to be changed!');
define('GEN_ERROR_NO_THEME_COLORS','A color choice must be made, this theme does not appear to have any! Please select another theme.');
define('ERROR_CANNOT_CREATE_MODULE_DIR','Error creating directory: %s. Check your permissions!');
define('ERROR_CANNOT_REMOVE_MODULE_DIR','Error removing directory: %s. The directory may not exist or may not be empty! It must be removed by hand.');
define('GEN_ADM_TOOLS_CLEAN_LOG','Backup/Clean Audit Logs');
define('GEN_ADM_TOOLS_CLEAN_LOG_DESC','This operation creates a downloaded backup of your audit log database file. This will help keep the database size down and reduce company backup file sizes. Backing up this log is recommended before cleaning out to preserve PhreeBooks transaction history. <br />INFORMATION: Cleaning out the audit log will leave the current periods data in the database table and remove all other records.');
define('GEN_ADM_TOOLS_CLEAN_LOG_BACKUP','Backup Audit Log');
define('GEN_ADM_TOOLS_CLEAN_LOG_CLEAN','Clean Out Audit Log');
define('GEN_ADM_TOOLS_BTN_CLEAN_CONFIRM','Are you sure you want to delete these log records?');
define('GEN_ADM_TOOLS_BTN_BACKUP','Backup Now!');
define('GEN_ADM_TOOLS_BTN_CLEAN','Clean Now!');
define('GEN_ADM_TOOLS_SECURITY_HEADING','Clean Data Security Values');
define('GEN_ADM_TOOLS_SECURITY_DESC','This tool cleans all data security values with a expiration date prior to a selected date. WARNING: This operation cannot be undone!');
define('TEXT_CLEAN_BEFORE','Clean all values with expiration date before:');
define('TEXT_CLEAN_SECURITY_SUCCESS','Successfully removed %s data security records.');
define('GL_HEADING_BEGINNING_BALANCES','Chart of Accounts - Beginning Balances');
define('GL_HEADING_IMPORT_BEG_BALANCES','Import Beginning Balances');
define('GL_BTN_IMP_BEG_BALANCES','Import Inventory, Accounts Payable, Accounts Receivable Beginning Balances');
define('GL_UTIL_BEG_BAL_LEGEND','General Journal Beginning Balances');
define('GL_UTIL_BEG_BAL_TEXT','For initial set-ups and transfers from another accounting system.');
define('GL_BTN_BEG_BAL','Enter Beginning Balances');
define('TEXT_IMPORT_JOURNAL_ENTRIES','Import Journal Entries');
define('GL_BB_IMPORT_INVENTORY','Import Inventory');
define('GL_BB_IMPORT_PAYABLES','Import Accounts Payable');
define('GL_BB_IMPORT_RECEIVABLES','Import Accounts Receivable');
define('GL_BB_IMPORT_SALES_ORDERS','Import Sales Orders');
define('GL_BB_IMPORT_PURCH_ORDERS','Import Purchase Orders');
define('GL_BB_IMPORT_HELP_MSG','Refer to the help file for format requirements.');
define('HEADING_MODULE_IMPORT_EXPORT','Import/Export Database Tables');
define('TEXT_IMPORT_EXPORT_INFO','Table Information');
define('GEN_IMPORT_EXPORT_MESSAGE','Importing can be through XML or CSV format. Click on the sample button to download a sample file to use for formatting purposes.');
define('SAMPLE_XML','Sample XML');
define('SAMPLE_CSV','Sample CSV');
define('GEN_IMPORT_MESSAGE','The list below displays the tables available for import. Select a format, upload a file and press the Import button to continue.');
define('GEN_EXPORT_MESSAGE','Select a format and press the Export button to continue.');
define('GEN_TABLES_AVAILABLE','Tables Available to: ');
/************************** (General) ***********************************************/
define('CD_07_17_DESC', 'Minimum length of password');
define('CD_08_01_DESC', 'Maximum number of search results returned per page');
define('CD_08_03_DESC', 'Automatically check for program updates at login to PhreeBooks.');
define('CD_08_05_DESC', 'Hides messages on successful operations. Only caution and error messages will be displayed.');
define('CD_08_07_DESC', 'Updates the exchange rate for loaded currencies at every login.<br />If disabled, currencies may be manually updated in the Setup => Currencies menu.');
define('CD_08_10_DESC', 'Limits the length of history values shown in customer/vendor accounts for sales/purchases.');
define('CD_15_01_DESC', 'Session Timeout - Enter the time in seconds (default = 3600). Example: 3600= 1 hour<br />Note: Too few seconds can result in timeout issues when adding/editing products.');
define('CD_15_05_DESC', 'When enabled, this option will use ajax to refresh the session timer every 5 minutes preventing the session from expiring and logging out the user. This feature helps prevent dropped posts when PhreeBooks has been inactive and a post results in a login screen.');
define('CD_20_99_DESC', 'Enable trace generation for debug purposes. If Yes is selected, an additional menu will be added to the Tools menu to download the trace information to help debug posting problems.');
define('CD_09_01_DESC', 'Specifies the export preference when exporting reports and forms. Local will save them in the /my_files/reports directory of the webserver for use with all companies. Download will download the file to your browser to save/print on your local machine.');
define('CD_00_01_DESC', 'Sets the display format for displayed and entered dates (default m/d/Y), m - month; d - day; Y - four digit year. Refer to the php.net function <b>date</b> for format requirements.');
define('CD_00_02_DESC', 'Identifies the delimiter used to seperate dates (default /). This must match the delimiter use in the Date format above.');
define('CD_00_03_DESC', 'Sets the display format for formal with time (default m/d/Y h:i:s a). Refer to the php.net date function for format options.');
/************************** (My Company) ***********************************************/
define('CD_01_01_DESC', 'The name of my company');
define('CD_01_02_DESC', 'The default name or identifier to use for all receivable operations.');
define('CD_01_03_DESC', 'The default name or identifier to use for all payable operations.');
define('CD_01_04_DESC', 'First address line');
define('CD_01_05_DESC', 'Second address line');
define('CD_01_06_DESC', 'The city or town where this company is located');
define('CD_01_07_DESC', 'The state or region where this company is located');
define('CD_01_08_DESC', 'Postal or Zip code where this company is located');
define('CD_01_09_DESC', 'The country this company is located <br /><br /><strong>Note: Please remember to update the company state or region.</strong>');
define('CD_01_10_DESC', 'Enter the company\'s primary telephone number');
define('CD_01_11_DESC', 'Secondary telephone number (may also be toll free number)');
define('CD_01_12_DESC', 'Enter the company\'s fax number');
define('CD_01_13_DESC', 'Enter the general company email address');
define('CD_01_14_DESC', 'Enter the homepage of the company website (without the http://)');
define('CD_01_15_DESC', 'Enter the company\'s (Federal) tax ID number');
define('CD_01_16_DESC', 'Enter the company ID number. This number is used to identify transactions generated locally versus imported/exported transactions.');
define('CD_01_18_DESC', 'Enable multiple branch functionality.<br />If No is selected, only one company location will be assumed.');
define('CD_01_19_DESC', 'Enable multiple currencies in user entry screens.<br />If No is selected, only the default currency wil be used.');
define('CD_01_20_DESC', 'Automatically switch to the language\'s currency when it is changed');
define('CD_01_25_DESC', 'Whether or not to enable the shipping functions and shipping fields.');
define('CD_01_30_DESC', 'Whether or not allow storage of encrypted fields.');
/************************** E-mail Settings ***********************************************/
define('CD_12_01_DESC', 'Defines the method for sending mail.<br /><strong>PHP</strong> is the default, and uses built-in PHP wrappers for processing.<br />Servers running on Windows and MacOS should change this setting to <strong>SMTP</strong>.<br /><strong>SMTPAUTH</strong> should only be used if your server requires SMTP authorization to send messages. You must also configure your SMTPAUTH settings in the appropriate fields in this admin section.<br /><strong>sendmail</strong> is for linux/unix hosts using the sendmail program on the server<br /><strong>"sendmail -f"</strong> is only for servers which require the use of the -f parameter to send mail. This is a security setting often used to prevent spoofing. Will cause errors if your host mailserver is not configured to use it.<br /><strong>Qmail</strong> is used for linux/unix hosts running Qmail as sendmail wrapper at /var/qmail/bin/sendmail.');
define('CD_12_02_DESC', 'Defines the character sequence used to separate mail headers.');
define('CD_12_04_DESC', 'Send e-mails in HTML format');
define('CD_12_10_DESC', 'Email address of Store Owner.  Used as "display only" when informing customers of how to contact you.');
define('CD_12_11_DESC', 'Address from which email messages will be "sent" by default. Can be over-ridden at compose-time in admin modules.');
define('CD_12_15_DESC', 'Please select the Admin extra email format');
define('CD_12_70_DESC', 'Enter the mailbox account name (me@mydomain.com) supplied by your host. This is the account name that your host requires for SMTP authentication. (Only required if using SMTP Authentication for email)');
define('CD_12_71_DESC', 'Enter the password for your SMTP mailbox. (Only required if using SMTP Authentication for email)');
define('CD_12_72_DESC', 'Enter the DNS name of your SMTP mail server. i.e. mail.mydomain.com or 55.66.77.88 (Only required if using SMTP Authentication for email)');
define('CD_12_73_DESC', 'Enter the IP port number that your SMTP mailserver operates on. (Only required if using SMTP Authentication for email)');
define('CD_12_74_DESC', 'What currency conversions do you need for Text emails? (Default = &amp;pound;,�:&amp;euro;,�)');
/************************** Currencies Settings ***********************************************/
define('SETUP_TITLE_CURRENCIES', 'Currencies');
define('SETUP_CURRENCY_NAME', 'Currency');
define('SETUP_CURRENCY_CODES', 'Code');
define('SETUP_UPDATE_EXC_RATE','Update Exchange Rate');
define('SETUP_CURR_EDIT_INTRO', 'Please make any necessary changes');
define('SETUP_INFO_CURRENCY_TITLE', 'Title:');
define('SETUP_INFO_CURRENCY_CODE', 'Code:');
define('SETUP_INFO_CURRENCY_SYMBOL_LEFT', 'Symbol Left:');
define('SETUP_INFO_CURRENCY_SYMBOL_RIGHT', 'Symbol Right:');
define('SETUP_INFO_CURRENCY_DECIMAL_POINT', 'Decimal Point:');
define('SETUP_INFO_CURRENCY_THOUSANDS_POINT', 'Thousands Point:');
define('SETUP_INFO_CURRENCY_DECIMAL_PLACES', 'Decimal Places:');
define('SETUP_INFO_CURRENCY_DECIMAL_PRECISE', 'Decimal Precision: For use with unit prices and quantities at a higher precision than currency values. This value is typically set to the number of decimal places:');
define('SETUP_INFO_CURRENCY_VALUE', 'Value:');
define('SETUP_CURR_INSERT_INTRO', 'Please enter the new currency with its related data');
define('SETUP_CURR_DELETE_INTRO', 'Are you sure you want to delete this currency?');
define('SETUP_INFO_HEADING_NEW_CURRENCY', 'New Currency');
define('SETUP_INFO_HEADING_EDIT_CURRENCY', 'Edit Currency');
define('SETUP_INFO_SET_AS_DEFAULT', 'Set as default (requires a manual update of currency values)');
define('SETUP_INFO_CURRENCY_UPDATED', 'The exchange rate for %s (%s) was updated successfully via %s.');
define('SETUP_ERROR_CANNOT_CHANGE_DEFAULT', 'The default currency cannot be changed once entries have been entered in the system!');
define('SETUP_ERROR_CURRENCY_INVALID', 'Error: The exchange rate for %s (%s) was not updated via %s. Is it a valid currency code?');
define('SETUP_WARN_PRIMARY_SERVER_FAILED', 'Warning: The primary exchange rate server (%s) failed for %s (%s) - trying the secondary exchange rate server.');
define('SETUP_LOG_CURRENCY','Currencies - ');
// Encryption defines
define('GEN_ADM_TOOLS_BTN_SAVE','Save Changes');
define('GEN_ADM_TOOLS_SET_ENCRYPTION_KEY','Enter Encryption Key');
define('GEN_ENCRYPTION_GEN_INFO','Encryption services depend on a key used to encrypt data in the database. DO NOT LOSE THE KEY, otherwise data can not be decrypted!');
define('GEN_ENCRYPTION_COMP_TYPE','Enter the Encryption key used to store secure data.');
define('GEN_ENCRYPTION_KEY','Encryption key ');
define('GEN_ENCRYPTION_KEY_CONFIRM','Re-enter key ');
define('ERROR_WRONG_ENCRYPT_KEY_MATCH','The encryption keys do not match!');
define('ERROR_WRONG_ENCRYPT_KEY','You entered an encryption key but it did not match the stored value.');
define('GEN_ENCRYPTION_KEY_SET','The encryption key has been set.');
define('GEN_ENCRYPTION_KEY_CHANGED','The encryption key has been changed.');
define('GEN_ADM_TOOLS_SET_ENCRYPTION_PW','Create/Change Encryption Key');
define('GEN_ADM_TOOLS_SET_ENCRYPTION_PW_DESC','Set the encryption key to use if \'Encryption Enabled\' is turned on. If setting for the first time, the old encryption key is blank.');
define('GEN_ADM_TOOLS_ENCRYPT_OLD_PW','Old Encryption Key');
define('GEN_ADM_TOOLS_ENCRYPT_PW','New Encryption Key');
define('GEN_ADM_TOOLS_ENCRYPT_PW_CONFIRM','Re-enter New Encryption Key');
define('ERROR_OLD_ENCRYPT_NOT_CORRECT','The current encrypted key does not match the stored value!');
// backup defines
define('BOX_HEADING_RESTORE','Company Restore');
define('GEN_BACKUP_ICON_TITLE','Start Backup');
define('GEN_BACKUP_GEN_INFO','Please select the backup compression type and options below.');
define('GEN_BACKUP_COMP_TYPE','Compression Type: ');
define('GEN_COMP_BZ2',' bz2 (Linux)');
define('GEN_COMP_ZIP',' Zip');
define('GEN_COMP_NONE','None (Database Only)');
define('GEN_BACKUP_DB_ONLY',' Database Only');
define('GEN_BACKUP_FULL',' Database and Company Data Files');
define('GEN_BACKUP_SAVE_LOCAL',' Save a local copy in webserver (my_files/backups) directory');
define('GEN_BACKUP_WARNING','Warning! This operation will delete and re-write the database. Are you sure you want to continue?');
define('GEN_BACKUP_NO_ZIP_CLASS','The zip class cannot be found. PHP needs the zip library installed to back up with zip compression.');
define('GEN_BACKUP_FILE_ERROR','The zip file cannot be created. Check permissions for the directory: ');
define('GEN_BACKUP_DOWNLOAD_EMPTY','The download file does not contain any data!');
// company manager
define('SETUP_CO_MGR_COPY_CO','New/Copy Company');
define('SETUP_CO_MGR_DEL_CO','Delete Company');
define('TEXT_DEF_DATA','Basic Data');
define('TEXT_ALL_DATA','All Data');
define('TEXT_DEMO_DATA','Demo Data');
define('SETUP_CO_MGR_COPY_HDR','Enter the database information for the new company. (Must conform to mysql naming conventions, typically 8-12 alphanumeric characters) This name is used as the database name and will be added to the my_files directory to hold company specific data. The database must exist prior to creating the company.');
define('SETUP_CO_MGR_SRVR_NAME','Database Server ');
define('SETUP_CO_MGR_DB_NAME','Database Name ');
define('SETUP_CO_MGR_DB_USER','Database User Name ');
define('SETUP_CO_MGR_DB_PW','Database Password ');
define('SETUP_CO_MGR_CO_NAME','Company Full Name ');
define('SETUP_CO_MGR_MOD_SELECT','Please select the modules to copy/create and the data to copy. To create a new company, select Basic Data or Demo Data:');
define('SETUP_CO_MGR_ERROR_EMPTY_FIELD','Database name and company name cannot be blank!');
define('SETUP_CO_MGR_DUP_DB_NAME','Error - The database name cannot be the same as the current database name!');
define('SETUP_CO_MGR_CANNOT_CONNECT','Error connecting to the new database. Check the username and password.');
define('SETUP_CO_MGR_ERROR_1','Error creating database tables.');
define('SETUP_CO_MGR_CREATE_SUCCESS','Successfuly created new company');
define('SETUP_CO_MGR_DELETE_SUCCESS','The company was successfully deleted!');
define('SETUP_CO_MGR_LOG','Company Manager - ');
define('SETUP_CO_MGR_SELECT_DELETE','Select the company to delete: ');
define('SETUP_CO_MGR_DELETE_CONFIRM','WARNING: THIS WILL DELETE THE DATABASE AND ALL COMPANY SPECIFIC FILES! ALL DATA WILL BE LOST!');
define('SETUP_CO_MGR_JS_DELETE_CONFIRM','Are you sure you want to delete this company?');
define('SETUP_CO_MGR_NO_SELECTION','No company was selected to delete!');

define('INV_HEADING_CATEGORY_NAME', 'Tab Name');
define('INV_INFO_CATEGORY_DESCRIPTION', 'Tab description');
define('EXTRA_TABS_TAB_NAME', 'Tab name<br />Name should be short (10) with no special characters or spaces.');
define('EXTRA_TABS_INSERT_INTRO', 'Please enter the new tab with its properties');
define('INV_EDIT_INTRO', 'Please make any necessary changes');
define('INV_INFO_HEADING_NEW_CATEGORY', 'New Tab');
define('TEXT_NEW_FIELD','New Field');
define('INV_INFO_HEADING_EDIT_CATEGORY', 'Edit Tab');
define('EXTRA_TABS_DELETE_INTRO', 'Are you sure you want to delete this tab?\nTabs cannot be deleted if there is a field within the tab.');
define('EXTRA_TABS_DELETE_ERROR','This tab name already exists, please use another name.');
define('EXTRA_FIELDS_LOG','Extra Fields (%s)');
define('EXTRA_TABS_LOG','Asset Tabs (%s)');
define('INV_CATEGORY_MEMBER', 'Tab Member:');
define('INV_FIELD_NAME', 'Field Name:');
define('ASSETS_ERROR_FIELD_BLANK','The asset field name is blank, please enter a field name and re-check all entries in this form!');
define('ASSETS_ERROR_FIELD_DUPLICATE','The field name you entered is already in use, please enter a new field name!');
define('ASSETS_FIELD_RESERVED_WORD','The field name you entered is a MySQL reserved word, please enter a new field name!');
define('ASSETS_FIELD_DELETE_INTRO', 'Are you sure you want to delete this asset field?');
define('EXTRA_FIELDS_ERROR_NO_TABS','There are no custom tabs, please add at least one custom tab before adding fields.');
/************************** ( Tabs/Fields) ***********************************************/
define('INV_LABEL_DEFAULT_TEXT_VALUE', 'Default Value: ');
define('INV_LABEL_MAX_NUM_CHARS', 'Maximum Number of Characters (Length)');
define('INV_LABEL_FIXED_255_CHARS', 'Fixed at 255 Characters Maximum');
define('INV_LABEL_MAX_255', '(for lengths less than 256 Characters)');
define('INV_LABEL_CHOICES', 'Enter Selection String');
define('INV_LABEL_TEXT_FIELD', 'Text Field');
define('INV_LABEL_HTML_TEXT_FIELD', 'HTML Code');
define('INV_LABEL_HYPERLINK', 'Hyper-Link');
define('INV_LABEL_IMAGE_LINK', 'Image File Name');
define('INV_LABEL_INVENTORY_LINK', 'Inventory Link <br>(Link pointing to another inventory item (URL))');
define('INV_LABEL_INTEGER_FIELD', 'Integer Number');
define('INV_LABEL_INTEGER_RANGE', 'Integer Range');
define('INV_LABEL_DECIMAL_FIELD', 'Decimal Number');
define('INV_LABEL_DECIMAL_RANGE', 'Decimal Range');
define('INV_LABEL_DEFAULT_DISPLAY_VALUE', 'Display Format (Max,Decimal)');
define('INV_LABEL_DROP_DOWN_FIELD', 'Dropdown List');
define('INV_LABEL_MULTI_SELECT_FIELD','Multiple Options Checkboxes');
define('INV_LABEL_RADIO_FIELD', 'Radio Button');
define('INV_LABEL_RADIO_EXPLANATION','Enter choices, separated by commas as:<br />value1:desc1:def1,value2:desc2:def2<br /><u>Key:</u><br />value = The value to place into the database<br />desc = Textual description of the choice<br />def = Default 0 or 1, 1 being the default choice<br />Note: Only 1 default is allowed per list');
define('INV_LABEL_DATE_TIME_FIELD', 'Date and Time');
define('INV_LABEL_CHECK_BOX_FIELD', 'Check Box <br>(Yes or No Choice)');
define('INV_LABEL_TIME_STAMP_FIELD', 'Time Stamp');
define('INV_LABEL_TIME_STAMP_VALUE', 'System field to track the last date and time <br> a change to a particular inventory item was made.');
define('INV_FIELD_NAME_RULES','Fieldnames cannot contain spaces or special characters and must be 64 characters or less.');
define('INV_CATEGORY_CANNOT_DELETE','Cannot delete category. It is being used by field: ');
define('INV_CANNOT_DELETE_SYSTEM','Fields in the System category cannot be deleted!');
define('INV_IMAGE_PATH_ERROR','Error in the path specified for the upload image!');
define('INV_IMAGE_FILE_TYPE_ERROR','Error in the uploaded image file. Not an acceptable file type.');
define('INV_IMAGE_FILE_WRITE_ERROR','There was a problem writing the image file to the specified directory.');
define('INV_FIELD_RESERVED_WORD','The field name entered is a reserved word. Please choose a new field name.');
// Audit Log Messages
define('GEN_LOG_LOGIN','User Login -> ');
define('GEN_LOG_LOGIN_FAILED','Failed User Login - id -> ');
define('GEN_LOG_LOGOFF','User Logoff -> ');
define('GEN_LOG_RESEND_PW','Re-sent Password to email -> ');

?>