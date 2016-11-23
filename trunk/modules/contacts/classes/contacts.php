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
//  Path: /modules/contacts/classes/contacts.php
//
namespace contacts\classes;
class contacts {
	public  $security_token		= "";
	public  $terms_type         = 'AP';
	public  $title;
	public  $page_title_new;
	public  $page_title_edit;
	public  $auto_type          = false;
	public  $inc_auto_id 		= false;
	public  $auto_field         = '';
	public  $help		        = '';
	public  $tab_list           = array();
	public  $address_types      = array();
	public  $type               = '';
	public  $crm_log			= array();
	public  $crm_date           = '';
	public  $crm_rep_id         = '';
    public  $crm_action         = '';
    public  $crm_note           = '';
    public  $payment_cc_name    = '';
    public  $payment_cc_number  = '';
    public  $payment_exp_month  = '';
    public  $payment_exp_year   = '';
    public  $payment_cc_cvv2    = '';
    public  $special_terms      = '0';
    private $duplicate_id_error = ACT_ERROR_DUPLICATE_ACCOUNT;
    private $sql_data_array     = array();
    public  $dir_attachments;
    public  $security_level		= 0;

    public function __construct(){
    	global $admin;
    	if ($this->security_token != '') $this->security_level = \core\classes\user::validate($this->security_token); // in this case it must be done after the class is defined for
    	$this->page_title_new	= sprintf(TEXT_NEW_ARGS, $this->title);
    	$this->page_title_edit	= sprintf(TEXT_EDIT_ARGS, $this->title);
    	$this->dir_attachments  = DIR_FS_MY_FILES . "{$_SESSION['user']->company}/contacts/main/";
    	//set defaults
        $this->crm_date			= date('Y-m-d');
        $this->crm_rep_id		= $_SESSION['user']->account_id <> 0 ? $_SESSION['user']->account_id : $_SESSION['user']->admin_id;
        $this->fields 			= new \contacts\classes\fields(false, $this->type);
        foreach ($_POST as $key => $value) $this->$key = db_prepare_input($value);
        $this->special_terms  =  db_prepare_input($_POST['terms']); // TBD will fix when popup terms is redesigned
        if ($this->id  == '') $this->id  = $admin->cInfo->contactid;
        if ($this->id  != '') $this->getContact();
    }

	public function getContact() {
	  	global $admin;
	  	// expand attachments
		$this->attachments = $result['attachments'] ? unserialize($result['attachments']) : array();
		// load payment info
		if ($_SESSION['ENCRYPTION_VALUE'] && ENABLE_ENCRYPTION) {
		  	$sql = $admin->DataBase->prepare("SELECT id, hint, enc_value FROM ".TABLE_DATA_SECURITY." WHERE module='contacts' and ref_1={$this->id}");
		  	$sql->execute();
		  	while ($result = $sql->fetch(\PDO::FETCH_LAZY)) {
		    	$val = explode(':', \core\classes\encryption::decrypt($_SESSION['ENCRYPTION_VALUE'], $result['enc_value']));
		    	$this->payment_data[] = array(
			  	  'id'   => $result['id'],
			  	  'name' => $val[0],
			  	  'hint' => $result['hint'],
			  	  'exp'  => $val[2] . '/' . $val[3],
		    	);
		  	}
		}	// load sales reps
		$this->sales_rep_array = gen_get_rep_ids($basis->cInfo->contact->type);
  	}

	/**
	 * this function deletes a contact if it is save
	 * @return boolean
	 */
  	public function delete(){
		global $admin;
		\core\classes\user::validate_security($this->security_level, 4);
		if ( $this->id == '' ) throw new \core\classes\userException("the id field isn't set");	// error check, no delete if a journal entry exists
		$result = $admin->DataBase->query("SELECT id FROM ".TABLE_JOURNAL_MAIN." WHERE bill_acct_id={$this->id} OR ship_acct_id={$this->id} OR store_id={$this->id} LIMIT 1");
		if ($result->fetch(\PDO::FETCH_NUM) != 0) throw new \core\classes\userException(ACT_ERROR_CANNOT_DELETE);
	  	$admin->DataBase->exec("DELETE FROM ".TABLE_ADDRESS_BOOK ." WHERE ref_id={$this->id}");
	  	$admin->DataBase->exec("DELETE FROM ".TABLE_DATA_SECURITY." WHERE ref_1={$this->id}");
	  	$admin->DataBase->exec("DELETE FROM ".TABLE_CONTACTS     ." WHERE id={$this->id}");
	  	$admin->DataBase->exec("DELETE FROM ".TABLE_CONTACTS_LOG ." WHERE contact_id={$this->id}");
	  	foreach (glob("{$this->dir_attachments}contacts_{$this->id}_*.zip") as $filename) unlink($filename); // remove attachments
  	}

   	/**
   	* this function returns alle order
   	*/
  	function load_orders($journal_id, $only_open = true, $limit = 0) {
  		global $admin;
  		$raw_sql  = "SELECT id, journal_id, closed, closed_date, post_date, total_amount, purchase_invoice_id, purch_order_id FROM ".TABLE_JOURNAL_MAIN." WHERE";
  		$raw_sql .= ($only_open) ? " closed = '0' and " : "";
  		$raw_sql .= " journal_id in ({$journal_id}) and bill_acct_id = {$this->id} ORDER BY post_date DESC";
  		$raw_sql .= ($limit) ? " LIMIT {$limit}" : "";
  		$sql = $admin->DataBase->prepare($raw_sql);
  		$sql->execute();
  		if ($sql->fetch(\PDO::FETCH_NUM) == 0) return array();	// no open orders
  		$output = array();
  		$i = 1;
  		$output[0] = array('id' => '', 'text' => TEXT_NEW);
  		while ($result = $sql->fetch(\PDO::FETCH_ASSOC)) {
  	    	$output[$i] = $result;
  	    	$output[$i]['text'] = $result['purchase_invoice_id'];
  	    	$output[$i]['total_amount'] = in_array($result['journal_id'], array(7,13)) ? -$result['total_amount'] : $result['total_amount'];
  			$i++;
  		}
  		return $output;
  	}

  	public function data_complete(){
  		global $admin, $messageStack;
  		if ($this->auto_type && $this->short_name == '') {
    		$result = $admin->DataBase->query("SELECT {$this->auto_field} as next_id FROM ".TABLE_CURRENT_STATUS);
        	$this->short_name  = $result['next_id'];
        	$this->inc_auto_id = true;
    	}
  		$this->duplicate_id();
    	return true;
  	}

  	/**
   	* this function looks if there are duplicate id's if so it throws a exception.
   	*/

  	public function duplicate_id(){
  		global $admin;
	  	// check for duplicate short_name IDs
    	if ($this->id == '') {
      		$result = $admin->DataBase->query("SELECT id FROM ".TABLE_CONTACTS." WHERE short_name = '$this->short_name' AND type = '$this->type'");
    	} else {
      		$result = $admin->DataBase->query("SELECT id FROM ".TABLE_CONTACTS." WHERE short_name = '$this->short_name' AND type = '$this->type' AND id <> $this->id");
    	}
    	if ($result->fetch(\PDO::FETCH_NUM) > 0) throw new \core\classes\userException($this->duplicate_id_error);
  	}

  	/**
   	* this function saves all input in the contacts main page.
   	*/

	public function save(){
  		global $admin;
  		$this->id ? \core\classes\user::validate_security($this->security_level, 3) : \core\classes\user::validate_security($this->security_level, 2);
  		$sql_data_array = $this->fields->what_to_save();
  		$sql_data_array['id']				= $this->id;
  		$sql_data_array['class']			= addcslashes(get_class($this), '\\');
    	$sql_data_array['type']            	= $this->type;
    	$sql_data_array['short_name']      	= $this->short_name;
    	$sql_data_array['inactive']        	= isset($this->inactive) ? '1' : '0';
    	$sql_data_array['contacts_level'] 	= $this->contacts_level;
    	$sql_data_array['contact_first']   	= $this->contact_first;
    	$sql_data_array['contact_middle']  	= $this->contact_middle;
    	$sql_data_array['contact_last']    	= $this->contact_last;
    	$sql_data_array['store_id']        	= $this->store_id;
    	$sql_data_array['gl_type_account'] 	= (is_array($this->gl_type_account)) ? implode('', array_keys($this->gl_type_account)) : $this->gl_type_account;
    	$sql_data_array['gov_id_number']   	= $this->gov_id_number;
    	$sql_data_array['dept_rep_id']     	= $this->dept_rep_id;
    	$sql_data_array['account_number']  	= $this->account_number;
    	$sql_data_array['special_terms']   	= $this->special_terms;
    	$sql_data_array['price_sheet']     	= $this->price_sheet;
    	$sql_data_array['tax_id']          	= $this->tax_id;
    	$sql_data_array['last_update']     	= 'now()';
//    	if ($this->id == '') { //create record
    		$sql_data_array['first_date'] = 'now()';
    		$keys = array_keys($sql_data_array);
    		$fields = "'".implode("', '",$keys)."'";
    		$placeholder = "'".implode("', '",$sql_data_array)."'";
    		unset($sql_data_array['id']);
    		unset($sql_data_array['first_date']);
    		$output = implode(', ', array_map(
    			function ($v, $k) { return sprintf("%s='%s'", $k, $v); },
    			$sql_data_array,
    			array_keys($sql_data_array)
    		));
    		$sql = $admin->DataBase->prepare("INSERT INTO ".TABLE_CONTACTS." ($fields) VALUES ($placeholder) ON DUPLICATE KEY UPDATE $output");//@todo
    		$sql->execute();
//        	db_perform(TABLE_CONTACTS, $sql_data_array, 'insert');
        	$this->id = $basis->DataBase->lastInsertId('id');
			//	if auto-increment see if the next id is there and increment if so.
    	    if ($this->inc_auto_id) { // increment the ID value
        	    $next_id = string_increment($this->short_name);
            	$admin->DataBase->query("UPDATE ".TABLE_CURRENT_STATUS." SET {$this->auto_field} = '$next_id'");
	        }
    	    gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_ADD . '-' . $this->title, $this->short_name);
/*    	} else { // update record
    		$keys = array_keys($sql_data_array);
    		$fields = '`'.implode('`, `',$keys).'`';
    		$placeholder = '`:'.implode('`:, `',$keys).'`';
    		$sql = $admin->DataBase->prepare("UPDATE ".TABLE_CONTACTS." SET ($fields) VALUES ($placeholder)");
    		$sql->execute(get_object_vars($this));
        	//db_perform(TABLE_CONTACTS, $sql_data_array, 'update', "id = '$this->id'");
        	gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_UPDATE . '-' . $this->title, $this->short_name);
    	}*/
  	}

  	
  	/**
  	 * this method outputs a line on the template page.
  	 */
  	function list_row ($js_function = "submitSeq") {
  		\core\classes\messageStack::debug_log("executing ".__METHOD__ ." of class ". get_class($admin_class));
  		$security_level = \core\classes\user::validate($this->security_token); // in this case it must be done after the class is defined for
  		$bkgnd          = ($this->inactive) ? ' style="background-color:pink"' : '';
  		$attach_exists  = $this->attachments ? true : false;
  		echo "<td $bkgnd onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->short_name) 	."</td>";
  		echo "<td $bkgnd onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->primary_name)	. "</td>";
  		echo "<td 		 onclick='$js_function( $this->id, \"editContact\")'></td>";
  		echo "<td    {$this->inactive}    onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->address1) 	."</td>";
  		echo "<td        onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->city_town)	."</td>";
  		echo "<td        onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->state_province)."</td>";
  		echo "<td        onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->postal_code)	."</td>";
  		echo "<td 	     onclick='$js_function( $this->id, \"editContact\")'>". htmlspecialchars($this->telephone1)	."</td>";
  		echo "<td align='right'>";
  		// build the action toolbar
  		if ($js_function == "submitSeq") {
			if ($security_level > 1) echo html_icon('mimetypes/x-office-presentation.png', TEXT_SALES, 'small', 	"onclick='contactChart(\"annual_sales\", $this->id)'") . chr(10);
	  		if ($security_level > 1) echo html_icon('actions/edit-find-replace.png', TEXT_EDIT, 'small', 			"onclick='window.open(\"" . html_href_link(FILENAME_DEFAULT, "cID={$this->id}&amp;action=editContact", 'SSL')."\",\"_blank\")'"). chr(10);
	  		if ($attach_exists) 	 echo html_icon('status/mail-attachment.png', TEXT_DOWNLOAD_ATTACHMENT,'small', "onclick='submitSeq($this->id, \"ContactAttachmentDownloadFirst\", true)'") . chr(10);
	  		if ($security_level > 3) echo html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 			"onclick='if (confirm(\"" . ACT_WARN_DELETE_ACCOUNT . "\")) submitSeq($this->id, \"DeleteContact\")'") . chr(10);
  		} else if ($js_function == "setReturnAccount"){
  			switch ($this->journal_id) {
  				case  6:
  				case  7:
  				case 12:
  				case 13:
  					switch ($this->journal_id) {
  						case  6: $search_journal = 4;  break;
  						case  7: $search_journal = 6;  break;
  						case 12: $search_journal = 10; break;
  						case 13: $search_journal = 12; break;
  					}
  					$open_order_array = $this->load_orders($search_journal);
  					if ($open_order_array) {
  						echo html_pull_down_menu('open_order_' . $this->id, $open_order_array, '', "onchange='setReturnOrder(\"{$this->id}\")'");
  					}
  			}
  		}
  		echo "</td>";
  	}

//	function __destruct(){print_r($this);}
}
?>