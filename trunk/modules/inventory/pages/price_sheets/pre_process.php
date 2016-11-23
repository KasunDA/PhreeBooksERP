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
//  Path: /modules/inventory/pages/price_sheets/pre_process.php
//
$security_level = \core\classes\user::validate(SECURITY_ID_PRICE_SHEET_MANAGER);
/**************  include page specific files    *********************/
require_once(DIR_FS_WORKING . 'defaults.php');
/**************   page specific initialization  *************************/
$type        = isset($_GET['type'])  ? $_GET['type']   : 'c';
history_filter('inv_prices');
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_MODULES . 'inventory/pages/price_sheets/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($_REQUEST['action']) {
  case 'save':
  case 'update':
	\core\classes\user::validate_security($security_level, 2);
  	$id             = db_prepare_input($_POST['id']);
	$sheet_name     = db_prepare_input($_POST['sheet_name']);
	$revision       = db_prepare_input($_POST['revision']);
	$effective_date = \core\classes\DateTime::db_date_format($_POST['effective_date']);
	$default_sheet  = isset($_POST['default_sheet']) ? 1 : 0;
	$inactive       = isset($_POST['inactive']) ? 1 : 0;
	$encoded_prices = array();
	for ($i=0, $j=1; $i < MAX_NUM_PRICE_LEVELS; $i++, $j++) {
	  $price   = $admin->currencies->clean_value(db_prepare_input($_POST['price_'   . $j]));
	  $adj     = db_prepare_input($_POST['adj_' . $j]);
	  $adj_val = $admin->currencies->clean_value(db_prepare_input($_POST['adj_val_' . $j]));
	  $rnd     = db_prepare_input($_POST['rnd_' . $j]);
	  $rnd_val = $admin->currencies->clean_value(db_prepare_input($_POST['rnd_val_' . $j]));
	  $level_data = ($_POST['price_' . $j]) ? $price : '0';
	  $level_data .= ':' . db_prepare_input($_POST['qty_' . $j]);
	  $level_data .= ':' . db_prepare_input($_POST['src_' . $j]);
	  $level_data .= ':' . ($_POST['adj_' . $j]     ? $adj     : '0');
	  $level_data .= ':' . ($_POST['adj_val_' . $j] ? $adj_val : '0');
	  $level_data .= ':' . ($_POST['rnd_' . $j]     ? $rnd     : '0');
	  $level_data .= ':' . ($_POST['rnd_val_' . $j] ? $rnd_val : '0');
	  $encoded_prices[] = $level_data;
	}
	$default_levels = implode(';', $encoded_prices);
	// Check for duplicate price sheet names
	if ($_REQUEST['action'] == 'save') {
	  $result = $admin->DataBase->query("SELECT id FROM " . TABLE_PRICE_SHEETS . " WHERE sheet_name=".$admin->DataBase->quote($sheet_name));
	  if ($result->fetch(\PDO::FETCH_NUM) > 0) {
		$effective_date = \core\classes\DateTime::createFromFormat(DATE_FORMAT, $effective_date);
		$_REQUEST['action'] = 'new';
		throw new \core\classes\userException(SRVCS_DUPLICATE_SHEET_NAME);
	  }
	}
	$admin->DataBase->exec("INSERT INTO ".TABLE_PRICE_SHEETS." (id, sheet_name, type, inactive, revision, effective_date, default_sheet, default_levels) VALUES ($id, ".$admin->DataBase->quote($sheet_name).", '$type', $inactive, $revision, ".$effective_date->format("Y-m-d").", '$default_sheet', '$default_levels')  
ON DUPLICATE KEY UPDATE sheet_name=".$admin->DataBase->quote($sheet_name).", type='$type', inactive=$inactive, revision=$revision, effective_date=".$effective_date->format("Y-m-d").", default_sheet='$default_sheet', '$default_levels')");
	if ($default_sheet) {
		// Reset all other price sheet default flags if set to this price sheet
		$admin->DataBase->query("UPDATE " . TABLE_PRICE_SHEETS . " SET default_sheet = '0' WHERE sheet_name <> '".addslashes($sheet_name)."' and type = '$type'");
		// Set all price sheets with this name to default
	  	$admin->DataBase->query("UPDATE " . TABLE_PRICE_SHEETS . " SET default_sheet = '1' WHERE sheet_name = '".addslashes($sheet_name)."' and type = '$type'");
	}
	// set expiration date of previous rev if there is a older rev of this price sheet
	$experation_date = clone $effective_date;
	if ($id != '') $admin->DataBase->query("UPDATE " . TABLE_PRICE_SHEETS . " SET expiration_date = '" . $experation_date->modify("-1 day")->format("Y-m-d") . "'
	  where sheet_name = ".$admin->DataBase->quote($sheet_name)." and type = '$type' and ( expiration_date IS NULL or expiration_date = '0000-00-00' or expiration_date >= '{$effective_date->format('Y-m-d')}' ) and id < $id");
	gen_add_audit_log(TEXT_PRICE_SHEET. " - "  . ($_REQUEST['action'] == 'save') ? TEXT_SAVE : TEXT_UPDATE, $sheet_name);
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('psID', 'action')), 'SSL'));
	break;

  case 'delete':
	\core\classes\user::validate_security($security_level, 4);
  	$id = (int)db_prepare_input($_GET['psID']);
	$result = $admin->DataBase->query("select sheet_name, type, default_sheet from " . TABLE_PRICE_SHEETS . " where id = " . $id);
	$sheet_name = $result['sheet_name'];
	$type       = $result['type'];
	if ($result['default_sheet'] == '1') \core\classes\messageStack::add(PRICE_SHEET_DEFAULT_DELETED, 'caution');
	$admin->DataBase->exec("delete from " . TABLE_PRICE_SHEETS . " where id = '" . $id . "'");
	$admin->DataBase->exec("delete from " . TABLE_INVENTORY_SPECIAL_PRICES . " where price_sheet_id = '" . $id . "'");
	gen_add_audit_log(TEXT_PRICE_SHEET. " - "  . TEXT_DELETE, $sheet_name);
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('psID', 'action')).'&type='.$type, 'SSL'));
	break;

  case 'revise':
	\core\classes\user::validate_security($security_level, 2);
  	$old_id  = db_prepare_input($_GET['psID']);
	$result  = $admin->DataBase->query("select * from " . TABLE_PRICE_SHEETS . " where id = $old_id");
	$old_rev = $result['revision'];
	$date = new \core\classes\DateTime($result['effective_date']);
	$output_array = array(
	  'sheet_name'     => $result['sheet_name'],
	  'type'           => $type,
	  'revision'       => $result['revision'] + 1,
	  'effective_date' => $date->modify("+1 day")->format("Y-m-d"),
	  'default_sheet'  => $result['default_sheet'],
	  'default_levels' => $result['default_levels'],
	);
	db_perform(TABLE_PRICE_SHEETS, $output_array, 'insert');
	$id = \core\classes\PDO::lastInsertId('id'); // this is used by the edit function later on.
	// expire the old sheet
	$admin->DataBase->exec("UPDATE ".TABLE_PRICE_SHEETS." SET expiration_date='".$date->format('Y-m-d')."' WHERE id=$old_id");
	// Copy special pricing information to new sheet
	$levels = $admin->DataBase->query("select inventory_id, price_levels from " . TABLE_INVENTORY_SPECIAL_PRICES . " where price_sheet_id = $old_id");
	while (!$levels->EOF){//@todo
	  $admin->DataBase->query("insert into " . TABLE_INVENTORY_SPECIAL_PRICES . " set inventory_id = {$levels['inventory_id']},
	  price_sheet_id = $id, price_levels = '{$levels['price_levels']}'");
	  $levels->MoveNext();
	}
	gen_add_audit_log(TEXT_PRICE_SHEET. " - "  . TEXT_REVISE, $result['sheet_name'] . ' Rev. ' . $old_rev . ' => ' . ($old_rev + 1));
	$_REQUEST['action'] = 'edit'; // continue with edit.
  case 'edit':
	if(!isset($id)) $id = db_prepare_input($_POST['rowSeq']);
	$result         = $admin->DataBase->query("select * from " . TABLE_PRICE_SHEETS . " where id = $id");
	$sheet_name     = $result['sheet_name'];
	$revision       = $result['revision'];
	$effective_date = \core\classes\DateTime::createFromFormat(DATE_FORMAT, $result['effective_date']);
	$default_sheet  = ($result['default_sheet']) ? '1' : '0';
	$default_levels = $result['default_levels'];
	break;

  case 'go_first':    $_REQUEST['list'] = 1;       break;
  case 'go_previous': $_REQUEST['list'] = max($_REQUEST['list']-1, 1); break;
  case 'go_next':     $_REQUEST['list']++;         break;
  case 'go_last':     $_REQUEST['list'] = 99999;   break;
  case 'search':
  case 'search_reset':
  case 'go_page':
  case 'new':
  default:
}

/*****************   prepare to display templates  *************************/
$cal_ps = array(
  'name'      => 'datePost',
  'form'      => 'pricesheet',
  'fieldname' => 'effective_date',
  'imagename' => 'btn_date_1',
  'default'   => $effective_date,
);

$include_header = true;
$include_footer = true;

switch ($_REQUEST['action']) {
  case 'new':
  case 'edit':
    $include_template = 'template_detail.php';
    define('PAGE_TITLE', ($_REQUEST['action'] == 'new') ? sprintf(TEXT_NEW_ARGS, TEXT_PRICE_SHEET) : sprintf(TEXT_EDIT_ARGS, TEXT_PRICE_SHEET));
	break;
  default:
	$heading_array = array(
	  'sheet_name'      => TEXT_SHEET_NAME,
	  'inactive'        => TEXT_INACTIVE,
	  'revision'        => TEXT_REVISION,
	  'default_sheet'   => TEXT_DEFAULT,
	  'effective_date'  => TEXT_EFFECTIVE_DATE,
	  'expiration_date' => TEXT_EXPIRATION_DATE,
	);
	$result      = html_heading_bar($heading_array, array(TEXT_SPECIAL_PRICING, TEXT_ACTION));
	$list_header = $result['html_code'];
	$disp_order  = $result['disp_order'];
	// find the highest rev level by sheet name
	$result = $admin->DataBase->query("select distinct sheet_name, max(revision) as rev from " . TABLE_PRICE_SHEETS . "
	  where type = '$type' group by sheet_name");
	$rev_levels = array();
	while(!$result->EOF) {
	  $rev_levels[$result['sheet_name']] = $result['rev'];
	  $result->MoveNext();
	}
	// build the list for the page selected
	$search = '';
	if (isset($_REQUEST['search_text']) && $_REQUEST['search_text'] <> '') {
	  $search_fields = array('sheet_name', 'revision');
	  // hook for inserting new search fields to the query criteria.
	  if (is_array($extra_search_fields)) $search_fields = array_merge($search_fields, $extra_search_fields);
	  $search = " and (" . implode(" like %{$_REQUEST['search_text']}%' or ", $search_fields) . " like '%{$_REQUEST['search_text']}%)";
	}
	$field_list = array('id', 'inactive', 'sheet_name', 'revision', 'effective_date', 'expiration_date', 'default_sheet');
	// hook to add new fields to the query return results
	if (is_array($extra_query_list_fields) > 0) $field_list = array_merge($field_list, $extra_query_list_fields);
	$query_raw    = "SELECT SQL_CALC_FOUND_ROWS ".implode(', ', $field_list)." FROM ".TABLE_PRICE_SHEETS." WHERE type='$type' $search ORDER BY $disp_order";
	$query_result = $admin->DataBase->query($query_raw, (MAX_DISPLAY_SEARCH_RESULTS * ($_REQUEST['list'] - 1)).", ".MAX_DISPLAY_SEARCH_RESULTS);
    $query_split      = new \core\classes\splitPageResults($_REQUEST['list'], '');
    history_save('inv_prices');

    $include_template = 'template_main.php';
    define('PAGE_TITLE', $type == 'v' ? TEXT_VENDOR_PRICE_SHEETS : TEXT_CUSTOMER_PRICE_SHEETS);
}

?>