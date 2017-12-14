<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2015 PhreeSoft      (www.PhreeSoft.com)       |
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
// Path: /modules/phreebooks/classes/admin.php
//
namespace phreebooks\classes;

define('SECURITY_ID_SEARCH',               4);
define('SECURITY_ID_GEN_ADMIN_TOOLS',     19);
define('SECURITY_ID_SALES_ORDER',         28);
define('SECURITY_ID_SALES_QUOTE',         29);
define('SECURITY_ID_SALES_INVOICE',       30);
define('SECURITY_ID_SALES_CREDIT',        31);
define('SECURITY_ID_SALES_STATUS',        32);
define('SECURITY_ID_POINT_OF_SALE',       33);
define('SECURITY_ID_INVOICE_MGR',         34);
define('SECURITY_ID_QUOTE_STATUS',        35);
define('SECURITY_ID_CUST_CREDIT_STATUS',  40);
define('SECURITY_ID_PURCHASE_ORDER',      53);
define('SECURITY_ID_PURCHASE_QUOTE',      54);
define('SECURITY_ID_PURCHASE_INVENTORY',  55);
define('SECURITY_ID_POINT_OF_PURCHASE',   56);
define('SECURITY_ID_PURCHASE_CREDIT',     57);
define('SECURITY_ID_PURCHASE_STATUS',     58);
define('SECURITY_ID_RFQ_STATUS',          59);
define('SECURITY_ID_VCM_STATUS',          60);
define('SECURITY_ID_PURCH_INV_STATUS',    61);
define('SECURITY_ID_SELECT_PAYMENT',     101);
define('SECURITY_ID_CUSTOMER_RECEIPTS',  102);
define('SECURITY_ID_PAY_BILLS',          103);
define('SECURITY_ID_ACCT_RECONCILIATION',104);
define('SECURITY_ID_ACCT_REGISTER',      105);
define('SECURITY_ID_VOID_CHECKS',        106);
define('SECURITY_ID_CUSTOMER_PAYMENTS',  107);
define('SECURITY_ID_VENDOR_RECEIPTS',    108);
define('SECURITY_ID_RECEIPTS_STATUS',    111);
define('SECURITY_ID_PAYMENTS_STATUS',    112);
define('SECURITY_ID_JOURNAL_ENTRY',      126);
define('SECURITY_ID_GL_BUDGET',          129);
define('SECURITY_ID_JOURNAL_STATUS',     130);
// New Database Tables
define('TABLE_ACCOUNTING_PERIODS',        DB_PREFIX . 'accounting_periods');
define('TABLE_ACCOUNTS_HISTORY',          DB_PREFIX . 'accounts_history');
define('TABLE_CHART_OF_ACCOUNTS',         DB_PREFIX . 'chart_of_accounts');
define('TABLE_CHART_OF_ACCOUNTS_HISTORY', DB_PREFIX . 'chart_of_accounts_history');
define('TABLE_JOURNAL_ITEM',              DB_PREFIX . 'journal_item');
define('TABLE_JOURNAL_MAIN',              DB_PREFIX . 'journal_main');
define('TABLE_TAX_AUTH',                  DB_PREFIX . 'tax_authorities');
define('TABLE_TAX_RATES',                 DB_PREFIX . 'tax_rates');
define('TABLE_RECONCILIATION',            DB_PREFIX . 'reconciliation');

class admin extends \core\classes\admin {
	public $sort_order = 2;
	public $id = 'phreebooks';
	public $description = MODULE_PHREEBOOKS_DESCRIPTION;
	public $core = true;
	public $version = '4.0';

	function __construct() {
		$this->text = sprintf ( TEXT_MODULE_ARGS, TEXT_PHREEBOOKS );
		$this->prerequisites = array ( // modules required and rev level for this module to work properly
				'phreedom' 	=> 4.0,
				'contacts' 	=> 4.0,
				'inventory' => 4.0,
				'payment' 	=> 3.6,
				'phreeform' => 3.6
		);
		// Load configuration constants for this module, must match entries in admin tabs
		$this->keys = array (
				'AUTO_UPDATE_PERIOD' => '1',
				'SHOW_FULL_GL_NAMES' => '2',
				'ROUND_TAX_BY_AUTH' => '0',
				'ENABLE_BAR_CODE_READERS' => '0',
				'SINGLE_LINE_ORDER_SCREEN' => '1',
				'ENABLE_ORDER_DISCOUNT' => '0',
				'ALLOW_NEGATIVE_INVENTORY' => '1',
				'AR_DEFAULT_GL_ACCT' => '1100',
				'AR_DEF_GL_SALES_ACCT' => '4000',
				'AR_SALES_RECEIPTS_ACCOUNT' => '1020',
				'AR_DISCOUNT_SALES_ACCOUNT' => '4900',
				'AR_DEF_FREIGHT_ACCT' => '4300',
				'AR_DEF_DEPOSIT_ACCT' => '1020',
				'AR_DEF_DEP_LIAB_ACCT' => '2400',
				'AR_USE_CREDIT_LIMIT' => '1',
				'AR_CREDIT_LIMIT_AMOUNT' => '2500.00',
				'APPLY_CUSTOMER_CREDIT_LIMIT' => '0',
				'AR_PREPAYMENT_DISCOUNT_PERCENT' => '0',
				'AR_PREPAYMENT_DISCOUNT_DAYS' => '0',
				'AR_NUM_DAYS_DUE' => '30',
				'AR_AGING_HEADING_1' => '0-30',
				'AR_ACCOUNT_AGING_START' => '0',
				'AR_AGING_HEADING_2' => '31-60',
				'AR_AGING_PERIOD_1' => '30',
				'AR_AGING_HEADING_3' => '61-90',
				'AR_AGING_PERIOD_2' => '60',
				'AR_AGING_HEADING_4' => 'Over 90',
				'AR_AGING_PERIOD_3' => '90',
				'AR_CALCULATE_FINANCE_CHARGE' => '0',
				'AR_ADD_SALES_TAX_TO_SHIPPING' => '0',
				'AUTO_INC_CUST_ID' => '0',
				'AR_SHOW_CONTACT_STATUS' => '0',
				'AR_TAX_BEFORE_DISCOUNT' => '1',
				'AP_DEFAULT_INVENTORY_ACCOUNT' => '1200',
				'AP_DEFAULT_PURCHASE_ACCOUNT' => '2000',
				'AP_PURCHASE_INVOICE_ACCOUNT' => '1020',
				'AP_DEF_FREIGHT_ACCT' => '6800',
				'AP_DISCOUNT_PURCHASE_ACCOUNT' => '2000',
				'AP_DEF_DEPOSIT_ACCT' => '1020',
				'AP_DEF_DEP_LIAB_ACCT' => '2400',
				'AP_USE_CREDIT_LIMIT' => '1',
				'AP_CREDIT_LIMIT_AMOUNT' => '5000.00',
				'AP_PREPAYMENT_DISCOUNT_PERCENT' => '0',
				'AP_PREPAYMENT_DISCOUNT_DAYS' => '0',
				'AP_NUM_DAYS_DUE' => '30',
				'AP_AGING_HEADING_1' => '0-30',
				'AP_AGING_START_DATE' => '0',
				'AP_AGING_HEADING_2' => '31-60',
				'AP_AGING_DATE_1' => '30',
				'AP_AGING_HEADING_3' => '61-90',
				'AP_AGING_DATE_2' => '60',
				'AP_AGING_HEADING_4' => 'Over 90',
				'AP_AGING_DATE_3' => '90',
				'AP_ADD_SALES_TAX_TO_SHIPPING' => '0',
				'AUTO_INC_VEND_ID' => '0',
				'AP_SHOW_CONTACT_STATUS' => '0',
				'AP_TAX_BEFORE_DISCOUNT' => '1'
		);
		// add new directories to store images and data
		$this->dirlist = array (
				'phreebooks',
				'phreebooks/orders'
		);
		// Load tables
		$this->tables = array (
				TABLE_ACCOUNTING_PERIODS => "CREATE TABLE " . TABLE_ACCOUNTING_PERIODS . " (
			  period int(11) NOT NULL default '0',
			  fiscal_year int(11) NOT NULL default '0',
			  start_date date NOT NULL default '0000-00-00',
			  end_date date NOT NULL default '0000-00-00',
			  date_added date NOT NULL default '0000-00-00',
			  last_update timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (period)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_ACCOUNTS_HISTORY => "CREATE TABLE " . TABLE_ACCOUNTS_HISTORY . " (
			  id int(11) NOT NULL auto_increment,
			  ref_id int(11) NOT NULL default '0',
			  acct_id int(11) NOT NULL default '0',
			  amount double NOT NULL default '0',
			  journal_id int(2) NOT NULL default '0',
			  purchase_invoice_id char(24) default NULL,
			  so_po_ref_id int(11) default NULL,
			  post_date datetime default NULL,
			  PRIMARY KEY  (id),
			  KEY acct_id (acct_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_CHART_OF_ACCOUNTS => "CREATE TABLE " . TABLE_CHART_OF_ACCOUNTS . " (
			  id char(15) NOT NULL default '',
			  description char(64) NOT NULL default '',
			  heading_only enum('0','1') NOT NULL default '0',
			  primary_acct_id char(15) default NULL,
			  account_type tinyint(4) NOT NULL default '0',
			  account_inactive enum('0','1') NOT NULL default '0',
			  PRIMARY KEY (id),
			  KEY type (account_type),
			  KEY heading_only (heading_only)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_CHART_OF_ACCOUNTS_HISTORY => "CREATE TABLE " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " (
			  id int(11) NOT NULL auto_increment,
			  period int(11) NOT NULL default '0',
			  account_id char(15) NOT NULL default '',
			  beginning_balance double NOT NULL default '0',
			  debit_amount double NOT NULL default '0',
			  credit_amount double NOT NULL default '0',
			  budget double NOT NULL default '0',
			  last_update date NOT NULL default '0000-00-00',
			  PRIMARY KEY  (id),
			  KEY period (period),
			  KEY account_id (account_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_JOURNAL_ITEM => "CREATE TABLE " . TABLE_JOURNAL_ITEM . " (
			  id int(11) NOT NULL auto_increment,
			  ref_id int(11) NOT NULL default '0',
			  item_cnt int(11) NOT NULL default '0',
			  so_po_item_ref_id int(11) default NULL,
			  gl_type char(3) NOT NULL default '',
			  reconciled int(2) NOT NULL default '0',
			  sku varchar(24) default NULL,
			  qty float NOT NULL default '0',
			  description varchar(255) default NULL,
			  debit_amount double default '0',
			  credit_amount double default '0',
			  gl_account varchar(15) NOT NULL default '',
			  taxable int(11) NOT NULL default '0',
			  full_price DOUBLE NOT NULL default '0',
			  serialize enum('0','1') NOT NULL default '0',
			  serialize_number varchar(24) default NULL,
			  project_id VARCHAR(16) default NULL,
			  purch_package_quantity float default NULL,
			  post_date date NOT NULL default '0000-00-00',
			  date_1 datetime NOT NULL default '0000-00-00 00:00:00',
			  PRIMARY KEY  (id),
			  KEY ref_id (ref_id),
			  KEY so_po_item_ref_id (so_po_item_ref_id),
			  KEY reconciled (reconciled)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_JOURNAL_MAIN => "CREATE TABLE " . TABLE_JOURNAL_MAIN . " (
			  id int(11) NOT NULL auto_increment,
			  period int(2) NOT NULL default '0',
			  journal_id int(2) NOT NULL default '0',
			  post_date date NOT NULL default '0000-00-00',
			  store_id int(11) default '0',
			  description varchar(32) default NULL,
			  closed enum('0','1') NOT NULL default '0',
			  closed_date date NOT NULL default '0000-00-00',
			  printed int(11) NOT NULL default '0',
			  freight double default '0',
			  discount double NOT NULL default '0',
			  shipper_code varchar(20) NOT NULL default '',
			  terms varchar(32) default '0',
			  sales_tax double NOT NULL default '0',
			  tax_auths varchar(16) NOT NULL default '0',
			  total_amount double NOT NULL default '0',
			  currencies_code char(3) NOT NULL DEFAULT '',
			  currencies_value DOUBLE NOT NULL DEFAULT '1.0',
			  so_po_ref_id int(11) NOT NULL default '0',
			  purchase_invoice_id varchar(24) default NULL,
			  purch_order_id varchar(24) default NULL,
			  recur_id int(11) default NULL,
			  admin_id int(11) NOT NULL default '0',
			  rep_id int(11) NOT NULL default '0',
			  waiting enum('0','1') NOT NULL default '0',
			  gl_acct_id varchar(15) default NULL,
			  bill_acct_id int(11) NOT NULL default '0',
			  bill_address_id int(11) NOT NULL default '0',
			  bill_primary_name varchar(32) default NULL,
			  bill_contact varchar(32) default NULL,
			  bill_address1 varchar(32) default NULL,
			  bill_address2 varchar(32) default NULL,
			  bill_city_town varchar(24) default NULL,
			  bill_state_province varchar(24) default NULL,
			  bill_postal_code varchar(10) default NULL,
			  bill_country_code char(3) default NULL,
			  bill_telephone1 varchar(20) default NULL,
			  bill_email varchar(48) default NULL,
			  ship_acct_id int(11) NOT NULL default '0',
			  ship_address_id int(11) NOT NULL default '0',
			  ship_primary_name varchar(32) default NULL,
			  ship_contact varchar(32) default NULL,
			  ship_address1 varchar(32) default NULL,
			  ship_address2 varchar(32) default NULL,
			  ship_city_town varchar(24) default NULL,
			  ship_state_province varchar(24) default NULL,
			  ship_postal_code varchar(24) default NULL,
			  ship_country_code char(3) default NULL,
			  ship_telephone1 varchar(20) default NULL,
			  ship_email varchar(48) default NULL,
			  terminal_date date default NULL,
			  drop_ship enum('0','1') NOT NULL default '0',
			  PRIMARY KEY  (id),
			  KEY period (period),
			  KEY journal_id (journal_id),
			  KEY post_date (post_date),
			  KEY closed (closed),
			  KEY bill_acct_id (bill_acct_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_TAX_AUTH => "CREATE TABLE " . TABLE_TAX_AUTH . " (
			  tax_auth_id int(3) NOT NULL auto_increment,
			  type varchar(1) NOT NULL DEFAULT 'c',
			  description_short char(15) NOT NULL default '',
			  description_long char(64) NOT NULL default '',
			  account_id char(15) NOT NULL default '',
			  vendor_id int(5) NOT NULL default '0',
			  tax_rate float NOT NULL default '0',
			  PRIMARY KEY  (tax_auth_id),
			  KEY description_short (description_short)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_TAX_RATES => "CREATE TABLE " . TABLE_TAX_RATES . " (
			  tax_rate_id int(3) NOT NULL auto_increment,
			  type varchar(1) NOT NULL DEFAULT 'c',
			  description_short varchar(15) NOT NULL default '',
			  description_long varchar(64) NOT NULL default '',
			  rate_accounts varchar(64) NOT NULL default '',
			  freight_taxable enum('0','1') NOT NULL default '0',
			  PRIMARY KEY  (tax_rate_id),
			  KEY description_short (description_short)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				TABLE_RECONCILIATION => "CREATE TABLE " . TABLE_RECONCILIATION . " (
			  id int(11) NOT NULL auto_increment,
			  period int(11) NOT NULL default '0',
			  gl_account varchar(15) NOT NULL default '',
			  statement_balance double NOT NULL default '0',
			  cleared_items text,
			  PRIMARY KEY  (id),
			  KEY period (period)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;"
		);
		
		// banking customers
		$this->mainmenu["banking"]->submenu ["customer_payment"]  	= new \core\classes\menuItem (5, 	TEXT_CUSTOMER_PAYMENT,			'action=LoadJournalManager&amp;jID=18&amp;type=c', 	SECURITY_ID_CUSTOMER_RECEIPTS,							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["banking"]->submenu ["customer_payment"]->submenu ["new"] 	= new \core\classes\menuItem ( 5, 	sprintf(TEXT_NEW_ARGS, TEXT_CUSTOMER_PAYMENT),		'module=phreebooks&amp;page=bills&amp;jID=18&amp;type=c');
		$this->mainmenu["banking"]->submenu ["customer_payment"]->submenu ["mgr"] 	= new \core\classes\menuItem (15, 	sprintf(TEXT_MANAGER_ARGS, TEXT_CUSTOMER_PAYMENT),	'action=LoadJournalManager&amp;jID=18&amp;type=c');
		$this->mainmenu["banking"]->submenu ["customer_refund"]  	= new \core\classes\menuItem (10, 	TEXT_CUSTOMER_REFUND,			'action=LoadJournalManager&amp;jID=20&amp;type=c', 	SECURITY_ID_CUSTOMER_PAYMENTS, 							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["banking"]->submenu ["customer_refund"]->submenu ["new"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_NEW_ARGS, TEXT_CUSTOMER_REFUND),		'module=phreebooks&amp;page=bills&amp;jID=20&amp;type=c');
		$this->mainmenu["banking"]->submenu ["customer_refund"]->submenu ["mgr"] 	= new \core\classes\menuItem (20, 	sprintf(TEXT_MANAGER_ARGS, TEXT_CUSTOMER_REFUND),	'action=LoadJournalManager&amp;jID=20&amp;type=c');
		// banking vendors
		$this->mainmenu["banking"]->submenu ["vendor_payment"]  	= new \core\classes\menuItem (15, 	TEXT_VENDOR_PAYMENT,			'action=LoadJournalManager&amp;jID=20&amp;type=v', 	SECURITY_ID_PAY_BILLS, 									'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["banking"]->submenu ["vendor_payment"]->submenu ["new"] 	= new \core\classes\menuItem ( 5, 	sprintf(TEXT_NEW_ARGS, TEXT_VENDOR_PAYMENT),		'module=phreebooks&amp;page=bills&amp;jID=20&amp;type=v');
		$this->mainmenu["banking"]->submenu ["vendor_payment"]->submenu ["bulk"]	= new \core\classes\menuItem (10, 	TEXT_PAY_BY_DUE_DATE,	'module=phreebooks&amp;page=bulk_bills', 	SECURITY_ID_SELECT_PAYMENT, 							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["banking"]->submenu ["vendor_payment"]->submenu ["mgr"] 	= new \core\classes\menuItem (15, 	sprintf(TEXT_MANAGER_ARGS, TEXT_VENDOR_PAYMENT),	'action=LoadJournalManager&amp;jID=20&amp;type=v');
		$this->mainmenu["banking"]->submenu ["vendor_refund"]  		= new \core\classes\menuItem (20, 	TEXT_VENDOR_REFUND,			'action=LoadJournalManager&amp;jID=18&amp;type=v', 		SECURITY_ID_CUSTOMER_PAYMENTS,							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["banking"]->submenu ["vendor_refund"]->submenu ["new"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_NEW_ARGS, TEXT_VENDOR_REFUND),		'module=phreebooks&amp;page=bills&amp;jID=18&amp;type=v');
		$this->mainmenu["banking"]->submenu ["vendor_refund"]->submenu ["mgr"] 	= new \core\classes\menuItem (20, 	sprintf(TEXT_MANAGER_ARGS, TEXT_VENDOR_REFUND),	'action=LoadJournalManager&amp;jID=18&amp;type=v');
		$this->mainmenu["banking"]->submenu ["register"]  			= new \core\classes\menuItem (40, 	TEXT_BANK_ACCOUNT_REGISTER,			'module=phreebooks&amp;page=register', 					SECURITY_ID_ACCT_REGISTER,								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["banking"]->submenu ["reconciliation"]		= new \core\classes\menuItem (60, 	TEXT_ACCOUNT_RECONCILIATION,		'module=phreebooks&amp;page=reconciliation', 			SECURITY_ID_ACCT_RECONCILIATION,						'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["customers"]->submenu ["quotes"]  	= new \core\classes\menuItem (20, 	TEXT_QUOTE,			'action=LoadJournalManager&amp;jID=9&amp;list=1', 						SECURITY_ID_SALES_QUOTE,								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["customers"]->submenu ["quotes"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_QUOTE),			'module=phreebooks&amp;page=orders&amp;jID=9');
		$this->mainmenu["customers"]->submenu ["quotes"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_QUOTE),		'action=LoadJournalManager&amp;jID=9');
		$this->mainmenu["customers"]->submenu ["orders"]   	= new \core\classes\menuItem (30, 	TEXT_ORDER,			'action=LoadJournalManager&amp;jID=10&amp;list=1', 						SECURITY_ID_SALES_ORDER,								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["customers"]->submenu ["orders"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_ORDER),			'module=phreebooks&amp;page=orders&amp;jID=10');
		$this->mainmenu["customers"]->submenu ["orders"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_ORDER),		'action=LoadJournalManager&amp;jID=10');
		$this->mainmenu["customers"]->submenu ["invoice"]   	= new \core\classes\menuItem (30, 	TEXT_INVOICE,			'action=LoadJournalManager&amp;jID=12&amp;list=1', 				SECURITY_ID_SALES_INVOICE, 								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["customers"]->submenu ["invoice"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_INVOICE),			'module=phreebooks&amp;page=orders&amp;jID=12');
		$this->mainmenu["customers"]->submenu ["invoice"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_INVOICE),		'action=LoadJournalManager&amp;jID=12');
		$this->mainmenu["customers"]->submenu ["credits"]   	= new \core\classes\menuItem (30, 	TEXT_CREDIT,			'action=LoadJournalManager&amp;jID=13&amp;list=1', 				SECURITY_ID_SALES_CREDIT, 								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["customers"]->submenu ["credits"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_CREDIT),			'module=phreebooks&amp;page=orders&amp;jID=13');
		$this->mainmenu["customers"]->submenu ["credits"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_CREDIT),		'action=LoadJournalManager&amp;jID=13');
		//VENDOR
		$this->mainmenu["vendors"]->submenu ["quotes"]  	= new \core\classes\menuItem (20, 	TEXT_QUOTE,			'action=LoadJournalManager&amp;jID=3&amp;list=1', 						SECURITY_ID_PURCHASE_QUOTE,								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["vendors"]->submenu ["quotes"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_QUOTE),			'module=phreebooks&amp;page=orders&amp;jID=3');
		$this->mainmenu["vendors"]->submenu ["quotes"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_QUOTE),		'action=LoadJournalManager&amp;jID=3');
		$this->mainmenu["vendors"]->submenu ["orders"]   	= new \core\classes\menuItem (30, 	TEXT_ORDER,			'action=LoadJournalManager&amp;jID=4&amp;list=1', 						SECURITY_ID_PURCHASE_ORDER,								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["vendors"]->submenu ["orders"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_ORDER),			'module=phreebooks&amp;page=orders&amp;jID=4');
		$this->mainmenu["vendors"]->submenu ["orders"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_ORDER),		'action=LoadJournalManager&amp;jID=4');
		$this->mainmenu["vendors"]->submenu ["invoice"]   	= new \core\classes\menuItem (30, 	TEXT_INVOICE,			'action=LoadJournalManager&amp;jID=6&amp;list=1', 					SECURITY_ID_PURCHASE_INVENTORY,							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["vendors"]->submenu ["invoice"]->submenu ["new"] 	= new \core\classes\menuItem (5, 	sprintf(TEXT_NEW_ARGS, TEXT_INVOICE),			'module=phreebooks&amp;page=orders&amp;jID=6');
		$this->mainmenu["vendors"]->submenu ["invoice"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_INVOICE),		'action=LoadJournalManager&amp;jID=6');
		$this->mainmenu["vendors"]->submenu ["credits"]   	= new \core\classes\menuItem (30, 	TEXT_CREDIT,				'action=LoadJournalManager&amp;jID=7&amp;list=1', 				SECURITY_ID_PURCHASE_CREDIT,							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["vendors"]->submenu ["credits"]->submenu ["new"] 	= new \core\classes\menuItem ( 5, 	sprintf(TEXT_NEW_ARGS, TEXT_CREDIT),			'module=phreebooks&amp;page=orders&amp;jID=7');
		$this->mainmenu["vendors"]->submenu ["credits"]->submenu ["mgr"] 	= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_CREDIT),		'action=LoadJournalManager&amp;jID=7');
		$this->mainmenu["gl"]->submenu ["journals"]    		= new \core\classes\menuItem ( 5, 	TEXT_GENERAL_JOURNAL,		'action=LoadJournalManager&amp;jID=2&amp;list=1', 				SECURITY_ID_JOURNAL_ENTRY,								'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["gl"]->submenu ["journals"]->submenu ["new"] 		= new \core\classes\menuItem ( 5, 	sprintf(TEXT_NEW_ARGS, TEXT_GENERAL_JOURNAL),			'module=phreebooks&amp;page=orders&amp;jID=2');
		$this->mainmenu["gl"]->submenu ["journals"]->submenu ["mgr"] 		= new \core\classes\menuItem (10, 	sprintf(TEXT_MANAGER_ARGS, TEXT_GENERAL_JOURNAL),		'action=LoadJournalManager&amp;jID=2');
		$this->mainmenu["gl"]->submenu ["search"]    		= new \core\classes\menuItem (15, 	TEXT_SEARCH,				'module=phreebooks&amp;page=search&amp;journal_id=-1',	 				SECURITY_ID_SEARCH,										'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["gl"]->submenu ["budget"]    		= new \core\classes\menuItem (50, 	TEXT_BUDGETING,				'module=phreebooks&amp;page=budget',				 					SECURITY_ID_GL_BUDGET,									'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["gl"]->submenu ["admin_tools"]		= new \core\classes\menuItem (70, 	TEXT_ADMINISTRATIVE_TOOLS,	'module=phreebooks&amp;page=admin_tools',				 				SECURITY_ID_GEN_ADMIN_TOOLS,							'MODULE_PHREEBOOKS_STATUS');
		$this->mainmenu["company"]->submenu ["configuration"]->submenu ["phreebooks"]  = new \core\classes\menuItem (sprintf(TEXT_MODULE_ARGS, TEXT_PHREEBOOKS), sprintf(TEXT_MODULE_ARGS, TEXT_PHREEBOOKS),	'module=phreebooks&amp;page=admin',    SECURITY_ID_CONFIGURATION);		
		parent::__construct ();
	}

	function install($path_my_files, $demo = false) {
		global $admin;
		parent::install ( $path_my_files, $demo );
		// load some current status values
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_po_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_po_num VARCHAR( 16 ) NOT NULL DEFAULT '5000';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_so_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_so_num VARCHAR( 16 ) NOT NULL DEFAULT '10000';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_inv_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_inv_num VARCHAR( 16 ) NOT NULL DEFAULT '20000';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_check_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_check_num VARCHAR( 16 ) NOT NULL DEFAULT '100';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_deposit_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_deposit_num VARCHAR( 16 ) NOT NULL DEFAULT '';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_cm_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_cm_num VARCHAR( 16 ) NOT NULL DEFAULT 'CM1000';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_vcm_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_vcm_num VARCHAR( 16 ) NOT NULL DEFAULT 'VCM1000';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_ap_quote_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_ap_quote_num VARCHAR( 16 ) NOT NULL DEFAULT 'RFQ1000';" );
		if (! $admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_ar_quote_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_ar_quote_num VARCHAR( 16 ) NOT NULL DEFAULT 'QU1000';" );
			// copy standard images to phreeform images directory
		$dir_source = DIR_FS_MODULES . 'phreebooks/images/';
		$dir_dest = DIR_FS_MY_FILES . $_SESSION ['company'] . '/phreeform/images/';
		@copy ( $dir_source . 'phreebooks_logo.jpg', $dir_dest . 'phreebooks_logo.jpg' );
		@copy ( $dir_source . 'phreebooks_logo.png', $dir_dest . 'phreebooks_logo.png' );
		$this->notes [] = MODULE_PHREEBOOKS_NOTES_1;
		$this->notes [] = MODULE_PHREEBOOKS_NOTES_2;
		$this->notes [] = MODULE_PHREEBOOKS_NOTES_3;
		$this->notes [] = MODULE_PHREEBOOKS_NOTES_4;
	}

	function after_ValidateUser(\core\classes\basis &$basis) {
		if (AUTO_UPDATE_PERIOD) \core\classes\DateTime::update_period(false);
	}

	function upgrade(\core\classes\basis &$basis) {
		parent::upgrade($basis);
		$db_version = defined ( 'MODULE_PHREEBOOKS_STATUS' ) ? MODULE_PHREEBOOKS_STATUS : 0;
		if (version_compare ( $db_version, '2.1', '<' )) { // For PhreeBooks release 2.1 or lower to update to Phreedom structure
			require (DIR_FS_MODULES . 'phreebooks/functions/updater.php');
			if ($basis->DataBase->table_exists ( TABLE_PROJECT_VERSION )) {
				$result = $basis->DataBase->query ( "select * from " . TABLE_PROJECT_VERSION . " WHERE project_version_key = 'PhreeBooks Database'" );
				$db_version = $result->fields ['project_version_major'] . '.' . $result->fields ['project_version_minor'];
				if (version_compare ( $db_version, '2.1', '<' ))
					execute_upgrade ( $db_version );
				$db_version = 2.1;
			}
		}
		if (version_compare ( $db_version, '2.1', '==' )) {
			$db_version = $this->release_update ( $this->id, 3.0, DIR_FS_MODULES . 'phreebooks/updates/R21toR30.php' );
			// remove table project_version, no longer needed
			$basis->DataBase->query ( "DROP TABLE " . TABLE_PROJECT_VERSION );
		}
		if (version_compare ( $db_version, '3.0', '==' )) {
			$db_version = $this->release_update ( $this->id, 3.1, DIR_FS_MODULES . 'phreebooks/updates/R30toR31.php' );
		}
		if (version_compare ( $db_version, '3.1', '==' )) {
			validate_path ( DIR_FS_MY_FILES . $_SESSION ['company'] . '/phreebooks/orders/', 0755 );
			$admin->DataBase->write_configure ( 'ALLOW_NEGATIVE_INVENTORY', '1' );
			$db_version = 3.2;
		}
		if (version_compare ( $db_version, '3.2', '==' )) {
			$admin->DataBase->write_configure ( 'APPLY_CUSTOMER_CREDIT_LIMIT', '0' ); // flag for using credit limit to authorize orders
			$basis->DataBase->query ( "ALTER TABLE " . TABLE_JOURNAL_MAIN . " CHANGE `shipper_code` `shipper_code` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''" );
			require_once (DIR_FS_MODULES . 'phreebooks/defaults.php');
			if (is_array ( glob ( DIR_FS_ADMIN . 'PHREEBOOKS_DIR_MY_ORDERS*.zip' ) )) {
				foreach ( glob ( DIR_FS_ADMIN . 'PHREEBOOKS_DIR_MY_ORDERS*.zip' ) as $file ) {
					$newfile = str_replace ( 'PHREEBOOKS_DIR_MY_ORDERS', '', $file );
					$newfile = str_replace ( DIR_FS_ADMIN, '', $newfile );
					rename ( $file, PHREEBOOKS_DIR_MY_ORDERS . $newfile );
				}
			}
			$db_version = 3.3;
		}
		if (version_compare ( $db_version, '3.4', '<' )) {
			if (! $basis->DataBase->field_exists ( TABLE_JOURNAL_ITEM, 'item_cnt' ))
				$basis->DataBase->query ( "ALTER TABLE " . TABLE_JOURNAL_ITEM . " ADD item_cnt INT(11) NOT NULL DEFAULT '0' AFTER ref_id" );
			$db_version = 3.4;
		}
		if (version_compare ( $db_version, '3.51', '<' )) {
			$sql = $basis->DataBase->prepare ( "SELECT id, so_po_ref_id FROM " . TABLE_JOURNAL_MAIN . " WHERE journal_id = 16 AND so_po_ref_id > 0" );
			$sql->execute();
		  	while ($result = $sql->fetch(\PDO::FETCH_ASSOC)) { // to fix transfers to store 0 from any other store
				if ($result['so_po_ref_id'] > $result['id']) {
					$basis->DataBase->query ( "UPDATE " . TABLE_JORNAL_MAIN . " SET so_po_ref_id = -1 WHERE id=" . $result['id'] );
				}
			}
			if (! $basis->DataBase->field_exists ( TABLE_JOURNAL_ITEM, 'purch_package_quantity' ))
				$basis->DataBase->query ( "ALTER TABLE " . TABLE_JOURNAL_ITEM . " ADD purch_package_quantity float default NULL AFTER project_id" );
		}
	}

	function delete($path_my_files) {
		global $admin;
		parent::delete ( $path_my_files );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_po_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_po_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_so_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_so_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_inv_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_inv_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_check_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_check_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_deposit_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_deposit_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_cm_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_cm_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_vcm_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_vcm_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_ap_quote_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_ap_quote_num" );
		if ($admin->DataBase->field_exists ( TABLE_CURRENT_STATUS, 'next_ar_quote_num' ))
			$admin->DataBase->query ( "ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_ar_quote_num" );
	}

	function load_reports() {
		$id = $this->add_report_heading ( TEXT_CUSTOMERS, 'cust' );
		$this->add_report_folder ( $id, TEXT_REPORTS, 'cust', 'fr' );
		$this->add_report_folder ( $id, TEXT_SALES_QUOTES, 'cust:quot', 'ff' );
		$this->add_report_folder ( $id, TEXT_SALES_ORDER, 'cust:so', 'ff' );
		$this->add_report_folder ( $id, TEXT_INVOICES_OR_PACKING_SLIPS, 'cust:inv', 'ff' );
		$this->add_report_folder ( $id, TEXT_CUSTOMER_CREDIT_MEMOS, 'cust:cm', 'ff' );
		$this->add_report_folder ( $id, TEXT_CUSTOMER_STATEMENTS, 'cust:stmt', 'ff' );
		$this->add_report_folder ( $id, TEXT_COLLECTION_LETTERS, 'cust:col', 'ff' );
		$this->add_report_folder ( $id, TEXT_LABELS . ' - ' .  TEXT_CUSTOMER, 'cust:lblc', 'ff' );
		$id = $this->add_report_heading ( TEXT_VENDORS, 'vend' );
		$this->add_report_folder ( $id, TEXT_REPORTS, 'vend', 'fr' );
		$this->add_report_folder ( $id, TEXT_PURCHASE_QUOTES, 'vend:quot', 'ff' );
		$this->add_report_folder ( $id, TEXT_PURCHASE_ORDERS, 'vend:po', 'ff' );
		$this->add_report_folder ( $id, TEXT_VENDOR_CREDIT_MEMOS, 'vend:cm', 'ff' );
		$this->add_report_folder ( $id, TEXT_LABELS . ' - ' . TEXT_VENDOR, 'vend:lblv', 'ff' );
		$this->add_report_folder ( $id, TEXT_VENDOR_STATEMENTS, 'vend:stmt', 'ff' );
		$id = $this->add_report_heading ( TEXT_BANKING, 'bnk' );
		$this->add_report_folder ( $id, TEXT_REPORTS, 'bnk', 'fr' );
		$this->add_report_folder ( $id, TEXT_DEPOSIT_SLIPS, 'bnk:deps', 'ff' );
		$this->add_report_folder ( $id, TEXT_BANK_CHECKS, 'bnk:chk', 'ff' );
		$this->add_report_folder ( $id, TEXT_SALES_RECEIPTS, 'bnk:rcpt', 'ff' );
		$id = $this->add_report_heading ( TEXT_GENERAL_LEDGER, 'gl' );
		$this->add_report_folder ( $id, TEXT_REPORTS, 'gl', 'fr' );
		parent::load_reports ();
	}

	function load_demo() {
		global $admin;
		// Data for table `tax_authorities`
		$admin->DataBase->query ( "TRUNCATE TABLE " . TABLE_TAX_AUTH );
		$admin->DataBase->query ( "INSERT INTO " . TABLE_TAX_AUTH . " VALUES (1, 'c', 'City Tax', 'City Tax on Taxable Items', '2312', 0, 2.5);" );
		$admin->DataBase->query ( "INSERT INTO " . TABLE_TAX_AUTH . " VALUES (2, 'c', 'State Tax', 'State Sales Tax Payable', '2316', 0, 5.1);" );
		$admin->DataBase->query ( "INSERT INTO " . TABLE_TAX_AUTH . " VALUES (3, 'c', 'Special Dist', 'Special District Tax (RTD, etc)', '2316', 0, 1.1);" );
		// Data for table `tax_rates`
		$admin->DataBase->query ( "TRUNCATE TABLE " . TABLE_TAX_RATES );
		$admin->DataBase->query ( "INSERT INTO " . TABLE_TAX_RATES . " VALUES (1, 'c', 'Local Tax', 'Local POS Tax', '1:2:3', '0');" );
		$admin->DataBase->query ( "INSERT INTO " . TABLE_TAX_RATES . " VALUES (2, 'c', 'State Only', 'State Only Tax - Shipments', '2', '0');" );
		parent::load_demo ();
	}
	
	/**
	 * @todo merge with loadOpenOrders
	 * @param \core\classes\basis $basis
	 * @throws \core\classes\userException
	 */
	
	function loadOrders (\core\classes\basis &$basis) {
		\core\classes\messageStack::debug_log("executing ".__METHOD__ ); 
		if (property_exists($basis->cInfo, 'journal_id') !== true) throw new \core\classes\userException(TEXT_JOURNAL_ID_NOT_DEFINED);
		if ($basis->cInfo->rows == 'NaN') $basis->cInfo->rows = MAX_DISPLAY_SEARCH_RESULTS;
		$offset = ($basis->cInfo->rows)? " LIMIT ".(($basis->cInfo->page - 1) * $basis->cInfo->rows).", {$basis->cInfo->rows}" : "";
		$raw_sql  = "SELECT m.id, journal_id, closed, closed_date, period, m.post_date, YEAR(m.post_date) as year, MONTH(m.post_date) as month, total_amount, purchase_invoice_id, purch_order_id, i.qty FROM ".TABLE_JOURNAL_MAIN." m JOIN ".TABLE_JOURNAL_ITEM." i ON m.id = i.ref_id WHERE ";
		$raw_sql .= ($basis->cInfo->only_open)  ? " m.closed = '0' AND " : "";
		$raw_sql .= ($basis->cInfo->post_date)  ? " m.post_date = '{$basis->cInfo->post_date}' AND " : "";
		$raw_sql .= ($basis->cInfo->post_date_min)  ? " m.post_date >= '{$basis->cInfo->post_date_min}' AND " : "";
		$raw_sql .= ($basis->cInfo->post_date_max)  ? " m.post_date <= '{$basis->cInfo->post_date_max}' AND " : "";
		$raw_sql .= ($basis->cInfo->contact_id) ? " m.bill_acct_id = '{$basis->cInfo->contact_id}' AND " : "";
		$raw_sql .= ($basis->cInfo->sku) ? " i.sku = '{$basis->cInfo->sku}' AND " : "";
		$raw_sql .= " m.journal_id IN ({$basis->cInfo->journal_id}) ORDER BY post_date DESC $offset";
		$sql = $basis->DataBase->prepare($raw_sql);
		$sql->execute();
		$results = $sql->fetchAll(\PDO::FETCH_ASSOC);
		$basis->cInfo->total = sizeof($results);
		$basis->cInfo->rows = $results;
	}
	
	/**
	 * load the SO's and PO's and get order, expected del date
	 * @throws \core\classes\userException
	 */
	function loadOpenOrders  (\core\classes\basis &$basis) {
		\core\classes\messageStack::debug_log("executing ".__METHOD__ );
		try{
			$temp = $basis->cInfo;
			if (!property_exists($basis->cInfo, 'journal_id')) 	throw new \core\classes\userException(TEXT_JOURNAL_ID_NOT_DEFINED);
			switch ($basis->cInfo->journal_id) {
				case  4:
					$gl_type   = 'por';
					break;
				case 10:
					$gl_type   = 'sos';
					break;
			}
			if (property_exists($basis->cInfo, 'store_id')) $store_id = " AND m.store_id = '{$basis->cInfo->store_id}' ";
			if (property_exists($basis->cInfo, 'sku')) $sku = " AND i.sku = '{$basis->cInfo->sku}' ";
			if (property_exists($basis->cInfo, 'sku')) {
				$sql = $basis->DataBase->prepare("SELECT m.id, m.journal_id, m.store_id, m.purchase_invoice_id, i.qty, i.post_date, i.date_1, i.id as item_id, m.total_amount, m.bill_primary_name
		  	  		FROM " . TABLE_JOURNAL_MAIN . " m INNER JOIN " . TABLE_JOURNAL_ITEM . " i ON m.id = i.ref_id
					WHERE m.journal_id  = {$basis->cInfo->journal_id} {$sku} {$store_id} AND m.closed = '0'
					ORDER BY i.date_1");
				$sql->execute();
				while($result = $sql->fetch(\PDO::FETCH_ASSOC)) {
					// this looks for partial received to make sure this item is still on order
					$adj = $basis->DataBase->query($sql = "SELECT SUM(qty) as qty FROM " . TABLE_JOURNAL_ITEM . " WHERE gl_type = '{$gl_type}' AND so_po_item_ref_id = '{$result['item_id']}' "); 
					if ($result['qty'] > $adj['qty']) $results = $result;
				}
			}else{
				$sql = $basis->DataBase->prepare("SELECT m.id, m.journal_id, m.store_id, m.purchase_invoice_id, i.qty, i.post_date, i.date_1, i.id as item_id, m.total_amount, m.bill_primary_name
		  	  		FROM " . TABLE_JOURNAL_MAIN . " m INNER JOIN " . TABLE_JOURNAL_ITEM . " i ON m.id = i.ref_id
						WHERE m.journal_id  = {$basis->cInfo->journal_id} {$store_id} AND m.closed = '0' AND i.gl_type = 'ttl'
						ORDER BY i.date_1");
				$sql->execute();
				$results = $sql->fetchAll(\PDO::FETCH_ASSOC);
			}
			$basis->cInfo->total = sizeof($results);
			$basis->cInfo->rows = $results;
		}catch (\Exception $e) {
			$basis->cInfo->rows = 0;
			$basis->cInfo->success = false;
			$basis->cInfo->error_message = $e->getMessage();
		}
	}
	
	function GetInventoryAvarages (\core\classes\basis &$basis){
		\core\classes\messageStack::development("executing ".__METHOD__ );
		if (property_exists($basis->cInfo, 'sku') !== true) throw new \core\classes\userException(TEXT_SKU_NOT_DEFINED);
		$last_year = CURRENT_ACCOUNTING_PERIOD - 12;
		$sql = "SELECT m.period, ROUND( AVG(i.qty),2 ) AS average FROM " . TABLE_JOURNAL_MAIN . " m INNER JOIN " . TABLE_JOURNAL_ITEM . " i ON m.id = i.ref_id
				  WHERE m.journal_id in (12, 19) AND i.sku = '{$basis->cInfo->sku}' AND m.period >= '{$last_year}'
				  GROUP BY m.period";
		$sql = $basis->DataBase->prepare($sql);
		$sql->execute();
		$basis->cInfo->rows = $sql->fetchAll(\PDO::FETCH_ASSOC);;
		$basis->cInfo->total = sizeof( $basis->cInfo->rows);
	}
	
	function GetAllContactsAndJournals (\core\classes\basis &$basis) {
		\core\classes\messageStack::debug_log("executing ".__METHOD__ );
		if (empty($basis->cInfo->contact_id)) throw new \core\classes\userException(TEXT_CONTACT_ID_NOT_DEFINED); 
		if (empty($basis->cInfo->jID)) $basis->StartEvent('GetAllContacts'); 
		if (isset($basis->cInfo->dept_rep_id)) {
			$criteria[] = "c.dept_rep_id = '{$basis->cInfo->dept_rep_id}'";
		}else{
			$criteria[] = "a.type = '{$basis->cInfo->type}m'";
		}
		if (isset($basis->cInfo->search_text) && $basis->cInfo->search_text <> '') {
			$search_fields = array('a.primary_name', 'a.contact', 'a.telephone1', 'a.telephone2', 'a.address1',
					'a.address2', 'a.city_town', 'a.postal_code', 'c.short_name');
			// hook for inserting new search fields to the query criteria.
			if (is_array($extra_search_fields)) $search_fields = array_merge($search_fields, $extra_search_fields);
			$criteria[] = '(' . implode(" like '%{$basis->cInfo->search_text}%' or ", $search_fields) . " like '%{$basis->cInfo->search_text}%')";
		}
		$criteria[] = ($basis->cInfo->only_open) ? " j.closed = '0' and " : "";
		$criteria[] = " journal_id in ({$basis->cInfo->jID}) ";
		if ($basis->cInfo->contact_show_inactive == false) $criteria[] = "(c.inactive = '0' or c.inactive = '')"; // inactive flag
		$search = (sizeof($criteria) > 0) ? (' WHERE ' . implode(' and ', $criteria)) : '';
		$query_raw = "SELECT id as contactid, short_name, CASE WHEN c.type = 'e' OR c.type = 'i' THEN CONCAT(contact_first , ' ',contact_last) ELSE primary_name END AS name, address1, city_town, postal_code, telephone1, inactive, j.id as journal, purchase_invoice_id FROM ".TABLE_CONTACTS." c LEFT JOIN ".TABLE_ADDRESS_BOOK." a ON c.id = a.ref_id LEFT JOIN ".TABLE_JOURNAL_MAIN." j ON c.id = j.bill_acct_id $search ORDER BY {$basis->cInfo->sort} {$basis->cInfo->order}";
		$sql = $basis->DataBase->prepare($query_raw);
		$sql->execute();
		$results = $sql->fetchAll(\PDO::FETCH_ASSOC);
		$basis->cInfo->total = sizeof($results);
		$basis->cInfo->rows = $results;
	}
	
	function GetAllJournals (\core\classes\basis &$basis) {
		\core\classes\messageStack::debug_log("executing ".__METHOD__ );
		if (!property_exists($basis->cInfo, 'jID')) 	throw new \core\classes\userException(TEXT_JOURNAL_TYPE_NOT_DEFINED);
		if ($basis->cInfo->rows == 'NaN') $basis->cInfo->rows = MAX_DISPLAY_SEARCH_RESULTS;
		$offset = ($basis->cInfo->rows)? " LIMIT ".(($basis->cInfo->page - 1) * $basis->cInfo->rows).", {$basis->cInfo->rows}" : "";
		$period = ($basis->cInfo->search_period == 'all') ? '' : " and period = {$basis->cInfo->search_period} ";
		if (isset($basis->cInfo->search_text) && $basis->cInfo->search_text <> '') {
			$search_fields = array('a.primary_name', 'a.contact', 'a.telephone1', 'a.telephone2', 'a.address1',
					'a.address2', 'a.city_town', 'a.postal_code', 'c.short_name');
			// hook for inserting new search fields to the query criteria.
			$criteria[] = '(' . implode(" like '%{$basis->cInfo->search_text}%' or ", $search_fields) . " like '%{$basis->cInfo->search_text}%')";
		}
		$criteria[] = ($basis->cInfo->only_open) ? " j.closed = '0' and " : "";
		$search = (sizeof($criteria) > 0) ?  implode(' and ', $criteria) : '';
//		$sql = $basis->DataBase->prepare("SELECT *, MONTH(post_date) as month, YEAR(post_date) as year FROM " . TABLE_JOURNAL_MAIN . " WHERE journal_id in ({$basis->cInfo->jID}) {$search} {$period} ORDER BY {$basis->cInfo->sort} {$basis->cInfo->order}");
//		$sql->execute();
//		$results = $sql->fetchAll(\PDO::FETCH_ASSOC);
//		$basis->cInfo->total = sizeof($results);
		$sql = $basis->DataBase->prepare("SELECT a.*, MONTH(a.post_date) as month, YEAR(a.post_date) as year FROM " . TABLE_JOURNAL_MAIN . " a JOIN " . TABLE_JOURNAL_ITEM . " b WHERE journal_id in ({$basis->cInfo->jID}) {$search} {$period} ORDER BY {$basis->cInfo->sort} {$basis->cInfo->order} $offset");
		$sql->execute();
		$results = $sql->fetchAll(\PDO::FETCH_ASSOC);
		$basis->cInfo->total = sizeof($results);
		$basis->cInfo->rows = $results;
	}
	
	function LoadJournalManager (\core\classes\basis $basis){
  		\core\classes\messageStack::debug_log("executing ".__METHOD__ );
  		$basis->observer->send_menu($basis);
  		switch ($basis->cInfo->jID) {
  			case  2: $security_level = SECURITY_ID_JOURNAL_ENTRY;      break;
  			case  3: $security_level = SECURITY_ID_PURCHASE_QUOTE;     break;
  			case  4: $security_level = SECURITY_ID_PURCHASE_ORDER;     break;
  			case  6: $security_level = SECURITY_ID_PURCHASE_INVENTORY; break;
  			case  7: $security_level = SECURITY_ID_PURCHASE_CREDIT;    break;
  			case  9: $security_level = SECURITY_ID_SALES_QUOTE;        break;
  			case 10: $security_level = SECURITY_ID_SALES_ORDER;        break;
  			case 12: $security_level = SECURITY_ID_SALES_INVOICE;      break;
  			case 13: $security_level = SECURITY_ID_SALES_CREDIT;       break;
  			case 18: $security_level = SECURITY_ID_CUSTOMER_RECEIPTS;  break;
  			case 20: $security_level = SECURITY_ID_PAY_BILLS;          break;
  			case 21: $security_level = SECURITY_ID_PAY_BILLS;          break;//@todo
  			case 22: $security_level = SECURITY_ID_PAY_BILLS;          break;//@todo
  			default:
  				die('Bad or missing journal id found (LoadJournalManager), jID needs to be passed to this script to identify the correct procedure.');
  		}
  		\core\classes\user::validate_security($security_level);?>
  			<div data-options="region:'center'">
  				<table id="dg" title="<?php echo sprintf(TEXT_MANAGER_ARGS, TEXT_JOURNAL);?>" style="height:500px;padding:50px;">
  					<thead>
  						<tr>
  							<th data-options="field:'post_date',align:'right',sortable:true, formatter: function(value,row,index){ return formatDate(new Date(value))}"><?php echo TEXT_DATE;?></th>
  		               		<th data-options="field:'purchase_invoice_id',align:'left',sortable:true"><?php echo TEXT_DESCRIPTION//@todo?></th>
  		            	   	<th data-options="field:'bill_primary_name',align:'center',sortable:true"><?php echo in_array ( $basis->cInfo->jID, array (9,10,12,13,19) ) ? TEXT_CUSTOMER_NAME : TEXT_VENDOR_NAME?></th>
  		    	           	<th data-options="field:'purch_order_id',align:'center',sortable:true"><?php echo TEXT_REFERENCE?></th>
  		        	       	<th data-options="field:'closed',align:'center',sortable:true,formatter: function(value,row,index){ if(value == '1'){return '<?php echo TEXT_YES?>'}else{return ''}}"><?php echo TEXT_CLOSED?></th>
  		        	       	<th data-options="field:'total_amount',align:'right',sortable:true, formatter: function(value,row,index){ return formatCurrency(value)}"><?php echo TEXT_AMOUNT?></th>
   		        	       	<th data-options="field:'id',align:'right',formatter:actionformater"><?php echo TEXT_ACTIONS?></th>
  		            	</tr>
  		        	</thead>
  		    	</table>
  		    	<div id="toolbar">
  		    		<a class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editJournal()"><?php echo sprintf(TEXT_EDIT_ARGS, TEXT_JOURNAL);?></a>
  			        <a class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newJournal()"><?php echo sprintf(TEXT_NEW_ARGS, TEXT_JOURNAL);?></a>
  		        	<a class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deleteJournal()"><?php echo sprintf(TEXT_DELETE_ARGS, TEXT_JOURNAL);?></a>
  		        	<?php echo \core\classes\htmlElement::checkbox('Journal_show_inactive', TEXT_SHOW_INACTIVE, '1', false,'onChange="doSearch()"' );?>
  		        	<div style="float: right;">
						<?php 
						echo \core\classes\htmlElement::period_combobox('search_period', TEXT_ACCOUNTING_PERIOD, CURRENT_ACCOUNTING_PERIOD);
						echo \core\classes\htmlElement::search('search_text','doSearch');?>
					</div>
  		    	</div>
  		    	<div id="win" class="easyui-window">
  		    		<div id="contactToolbar" style="margin:2px 5px;">
  						<a class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="closeWindow()"><?php echo TEXT_CANCEL?></a>
  						<?php if (\core\classes\user::validate($security_level, true) < 2){?>
  						<a class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="saveContact()" ><?php echo TEXT_SAVE?></a>
  						<?php }?>
  						<a class="easyui-linkbutton" iconCls="icon-help" plain="true" onclick="loadHelp()"><?php TEXT_HELP?></a>
  					</div>
  				</div>
  	    	</div>	
  			<script type="text/javascript">
	  			function actionformater (value,row,index){ 
					var temp = '';
					var security_level = <?php echo \core\classes\user::validate($security_level)?>;
					if (security_level > 1) temp += buildIcon(icon_path+'16x16/actions/edit-find-replace.png', "<?php echo TEXT_EDIT;?>", 'onclick="editContact('+index+','+row+')"');
					if (security_level > 3) temp += buildIcon(icon_path+'16x16/apps/accessories-text-editor.png', '<?php echo TEXT_RENAME;?>', 'onclick="renameItem('+row.contactid+')"');
					if (security_level > 3) temp += buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', "<?php echo TEXT_DELETE;?>", 'onclick="deleteItem('+row.contactid+')"');
					if (row.attachments != '')  temp += buildIcon(icon_path+'16x16/status/mail-attachment.png', "<?php echo TEXT_DOWNLOAD_ATTACHMENT;?>",'onclick="downloadAttachment('+row.contactid+')"'); //@todo
					if (security_level > 2)  temp += buildIcon(icon_path+'16x16/mimetypes/x-office-spreadsheet.png', "<?php echo TEXT_SALES;?>",'onclick="contactChart(\'annual_sales\', '+row.contactid+')"'); //@todo
					return temp;
				}	
  				
  				document.title = '<?php echo sprintf(TEXT_MANAGER_ARGS, $contact); ?>';
  		    	function doSearch(value){
  		    		console.log('A search was requested.');
  		        	$('#dg').datagrid('load',{
  		        		search_text: $('#search_text').val(),
						search_period: $('#search_period').val(),
  		        		dataType: 'json',
  		                contentType: 'application/json',
  		                async: false,
  		                jID: '<?php echo $basis->cInfo->jID;?>',
  		                Journal_show_inactive: document.getElementById('Journal_show_inactive').checked ? 1 : 0,
  		        	});
  		    	}

  		    	$('#search_period').combobox({
  	  		    	onChange:function(oldvalue, newvalue){
	  		    		console.log('search period is changed.');
	  		        	$('#dg').datagrid('load',{
	  		        		search_text: $('#search_text').val(),
							search_period: $('#search_period').val(),
	  		        		dataType: 'json',
	  		                contentType: 'application/json',
	  		                async: false,
	  		                jID: '<?php echo $basis->cInfo->jID;?>',
	  		                Journal_show_inactive: document.getElementById('Journal_show_inactive').checked ? 1 : 0,
	  		        	});
  	  		    	}
	  		    });
  	
  		        function newJournal(){
  		        	$.messager.progress();
  		            $('#win').window('open').window('center').window('setTitle','<?php echo sprintf(TEXT_NEW_ARGS, TEXT_JOURNAL);?>');
  		            $('#win').window('refresh', "index.php?action=newJournal");
  		            $('#win').window('resize');
  		        }
  		        
  		        function editContact(){
  			        $('#win').window('open').window('center').window('setTitle','<?php echo sprintf(TEXT_EDIT_ARGS, TEXT_JOURNAL);?>');
  		        }
  		        
  				$('#dg').datagrid({
  					url:		"index.php?action=GetAllJournals",
  					queryParams: {
						search_period: <?php echo CURRENT_ACCOUNTING_PERIOD;?>,
  						dataType: 'json',
  		                contentType: 'application/json',
  		                async: false,
						jID: '<?php echo $basis->cInfo->jID;?>',
  					},
  					onLoadSuccess: function(data){
  						console.log('the loading of the datagrid was succesfull');
  						$.messager.progress('close');
  						if(data.total == 0) $.messager.alert('<?php echo TEXT_ERROR?>',"<?php echo TEXT_NO_RESULTS_FOUND?>");
  						if(data.error_message) $.messager.alert('<?php echo TEXT_ERROR?>',data.error_message);
  					},
  					onLoadError: function(){
  						console.error('the loading of the datagrid resulted in a error');
  						$.messager.progress('close');
  						$.messager.alert('<?php echo TEXT_ERROR?>','Load error:'+arguments.responseText);
  					},
  					onDblClickRow: function(index , row){
  						console.log('a row in the datagrid was double clicked');
  						document.location = "index.php?action=editJournal&id="+ row.id;
  						//$('#win').window('open').window('center').window('setTitle',"<?php echo TEXT_EDIT?>"+ ' ' + row.name);
  					},
  					pagination: true,
  					pageSize:   <?php echo MAX_DISPLAY_SEARCH_RESULTS?>,
  		  			PageList:   <?php echo MAX_DISPLAY_SEARCH_RESULTS?>,
  					remoteSort:	true,
  					idField:	"id",
  					fitColumns:	true,
  					singleSelect:true,
  					sortName:	"post_date",
  					sortOrder: 	"asc",
  					loadMsg:	"<?php echo TEXT_PLEASE_WAIT?>",
  					toolbar: 	"#toolbar",
  					rowStyler: function(index,row){
  						if (row.waiting == '1') return 'background-color:lightblue';
  						if (row.closed  == '1') return 'background-color:pink';
  					},
  				});
  				
  				$('#win').window({
  		        	href:		"index.php?action=editJournal",
  					closed: true,
  					title:	"<?php echo sprintf(TEXT_EDIT_ARGS, TEXT_JOURNAL);?>",
  					fit:	true,
  					queryParams: {
  						dataType: 'html',
  		                contentType: 'text/html',
  		                async: false,
  					},
  					onLoadError: function(){
  						console.error('the loading of the window resulted in a error');
  						$.messager.alert('<?php echo TEXT_ERROR?>');
  						$.messager.progress('close');
  					},
  					onOpen: function(){
  						$.messager.progress('close');
  					},
  					onBeforeLoad: function(param){
  						var row = $('#dg').datagrid('getSelected');
  						param.contactid = row.contactid;
  					},
  				});
  				
  				function closeWindow(){
  					$.messager.progress();
  					$('#Journal').form('clear');
  					console.log('close Journal window');
  					$('#win').window('close', true);
  				}
  			</script><?php 
  		$basis->observer->send_footer($basis);
  	}
  	
  	function GetTaxRates (\core\classes\basis $basis){
  		\core\classes\messageStack::development("executing ".__METHOD__ );
  		if (!property_exists($basis->cInfo, 'type')) 	throw new \core\classes\userException(TEXT_CONTACT_TYPE_NOT_DEFINED);
  		$tax_rates        = ord_calculate_tax_drop_down($basis->cInfo->type);
  		foreach ($tax_rates as $key => $tax_rate){
  			$basis->cInfo->rows[$key] = $tax_rate;
  		}
  		$basis->cInfo->total = sizeof($basis->cInfo->rows);
  	}
}
?>