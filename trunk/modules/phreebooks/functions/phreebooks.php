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
//  Path: /modules/phreebooks/functions/phreebooks.php
//

function fetch_item_description($id) {
  global $admin;
  $result = $admin->DataBase->query("select description from " . TABLE_JOURNAL_ITEM . " where ref_id = " . $id . " limit 1");
  return $result['description'];
}

function validate_fiscal_year($next_fy, $next_period, $next_start_date, $num_periods = 12) {
  	$date = new \core\classes\DateTime($next_start_date);
  	$date->modify("-1 day");
  	for ($i = 0; $i < $num_periods; $i++) {
  		$date->modify('+1 month')->format("Y-m-d");
		$fy_array = array(
	  		'period'      => $next_period,
	  		'fiscal_year' => $next_fy,
			'start_date'  => $next_start_date,
	  		'end_date'    => $date->modify('+1 month')->format("Y-m-d"),
	  		'date_added'  => date('Y-m-d'),
		);
		db_perform(TABLE_ACCOUNTING_PERIODS, $fy_array, 'insert');
		$next_period++;
  	}
  	return $next_period--;
}

function modify_account_history_records($id, $add_acct = true) {
  	global $admin;
  	$result = $admin->DataBase->query("select max(period) as period from " . TABLE_ACCOUNTING_PERIODS);
  	$max_period = $result['period'];
  	if (!$max_period) die ('table: '.TABLE_ACCOUNTING_PERIODS.' is not set, run setup.');
  	if ($add_acct) {
    	$result = $admin->DataBase->query("select heading_only from " . TABLE_CHART_OF_ACCOUNTS . " where id = '{$id}'");
		if ($result['heading_only'] <> '1') {
	  		for ($i = 0, $j = 1; $i < $max_period; $i++, $j++) {
	    		$admin->DataBase->query("insert into " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " (account_id, period) values('{$id}', '{$j}')");
	  		}
		}
  	} else {
		$result = $admin->DataBase->exec("delete from " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " where account_id = '{$id}'");
  	}
}

function build_and_check_account_history_records() {
  global $admin;
  $result = $admin->DataBase->query("select max(period) as period from " . TABLE_ACCOUNTING_PERIODS);
  $max_period = $result['period'];
  if (!$max_period) die ('table: '.TABLE_ACCOUNTING_PERIODS.' is not set, run setup.');
  $result = $admin->DataBase->query("select id, heading_only from " . TABLE_CHART_OF_ACCOUNTS . " order by id");
  while (!$result->EOF) {
    if ($result['heading_only'] <> '1') {
	  $account_id = $result['id'];
	  for ($i = 0, $j = 1; $i < $max_period; $i++, $j++) {
	    $record_found = $admin->DataBase->query("select id from " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " where account_id = '" . $account_id . "' and period = " . $j);
	    if (!$record_found->fetch(\PDO::FETCH_NUM)) {
		  $admin->DataBase->query("insert into " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " (account_id, period) values('" . $account_id . "', '" . $j . "')");
		 }
	  }
	}
	$result->MoveNext();
  }
}

function get_fiscal_year_pulldown() {
    global $admin;
    $sql = $admin->DataBase->prepare("SELECT DISTINCT fiscal_year as id, fiscal_year as text from " . TABLE_ACCOUNTING_PERIODS . " order by fiscal_year");
    $sql->execute();
	return $sql->fetchAll(\PDO::FETCH_ASSOC);
}

function load_coa_types() {
  global $coa_types_list;
  if (!is_array($coa_types_list)) {
    require_once(DIR_FS_MODULES . 'phreebooks/defaults.php');
  }
  $coa_types = array();
  foreach ($coa_types_list as $value) {
    $coa_types[$value['id']] = array(
	  'id'    => $value['id'],
	  'text'  => $value['text'],
	  'asset' => $value['asset'],
	);
  }
  return $coa_types;
}

function load_coa_info($types = array()) { // includes inactive accounts
  global $admin;
  $coa_data = array();
  $string_sql = "SELECT * FROM " . TABLE_CHART_OF_ACCOUNTS;
  if (sizeof($types > 0)) $string_sql .= " WHERE account_type IN (" . implode(", ", $types) . ")";
  $sql = $admin->DataBase->prepare($string_sql);
  $sql->execute();
  return $sql->fetchAll(\PDO::FETCH_ASSOC);
}

function fill_paid_invoice_array($id, $account_id, $type = 'c') {
	// to build this data array, all current open invoices need to be gathered and then the paid part needs
	// to be applied along with discounts taken by row.
	global $admin;
	$negate = (($_GET['jID'] == 20 && $type == 'c') || ($_GET['jID'] == 18 && $type == 'v')) ? true : false;
	// first read all currently open invoices and the payments of interest and put into an array
	$paid_indeces = array();
	if ($id > 0) {
	  $result = $admin->DataBase->query("select distinct so_po_item_ref_id from " . TABLE_JOURNAL_ITEM . " where ref_id = " . $id);
	  while (!$result->EOF) {
	    if ($result->fields['so_po_item_ref_id']) $paid_indeces[] = $result->fields['so_po_item_ref_id'];
	    $result->MoveNext();
	  }
	}
	switch ($type) {
	  case 'c': $search_journal = '(12, 13)'; break;
	  case 'v': $search_journal = '(6, 7)';   break;
	  default: throw new \core\classes\userException("unknown type $type");
	}
	$open_invoices = array();
	$sql = "select id, journal_id, post_date, terms, purch_order_id, purchase_invoice_id, total_amount, gl_acct_id
	  from " . TABLE_JOURNAL_MAIN . "
	  where (journal_id in " . $search_journal . " and closed = '0' and bill_acct_id = " . $account_id . ")";
	if (sizeof($paid_indeces) > 0) $sql .= " or (id in (" . implode(',',$paid_indeces) . ") and closed = '0')";
	$sql .= " order by post_date";
	$result = $admin->DataBase->query($sql);
	while (!$result->EOF) {
	  if ($result->fields['journal_id'] == 7 || $result->fields['journal_id'] == 13) {
	    $result->fields['total_amount'] = -$result->fields['total_amount'];
	  }
	  $result->fields['total_amount'] -= fetch_partially_paid($result->fields['id']);
	  $result->fields['description']   = $result->fields['purch_order_id'];
	  $result->fields['discount']      = '';
	  $result->fields['amount_paid']   = '';
	  $open_invoices[$result->fields['id']] = $result->fields;
	  $result->MoveNext();
	}
	// next read the record of interest and add/adjust open invoice array with amounts
	$sql = "select id, ref_id, so_po_item_ref_id, gl_type, description, debit_amount, credit_amount, gl_account
	  from " . TABLE_JOURNAL_ITEM . " where ref_id = " . $id;
	$result = $admin->DataBase->query($sql);
	while (!$result->EOF) {
	  $amount = ($result->fields['debit_amount']) ? $result->fields['debit_amount'] : $result->fields['credit_amount'];
	  if ($negate) $amount = -$amount;
	  $index = $result->fields['so_po_item_ref_id'];
	  switch ($result->fields['gl_type']) {
	    case 'dsc': // it's the discount field
		  $open_invoices[$index]['discount']      = $amount;
		  $open_invoices[$index]['amount_paid']  -= $amount;
		  break;
		case 'chk':
	    case 'pmt': // it's the payment field
		  $open_invoices[$index]['total_amount'] += $amount;
		  $open_invoices[$index]['description']   = $result->fields['description'];
		  $open_invoices[$index]['amount_paid']   = $amount;
		  break;
		case 'ttl':
		  $payment_fields = $result->fields['description']; // payment details
		default:
	  }
	  $result->MoveNext();
	}
	ksort($open_invoices);

	$balance   = 0;
	$index     = 0;
	$item_list = array();
	foreach ($open_invoices as $key => $line_item) {
	  // fetch some information about the invoice
	  $sql = "select id, post_date, terms, purchase_invoice_id, purch_order_id, gl_acct_id, waiting
		from " . TABLE_JOURNAL_MAIN . " where id = " . $key;
	  $result = $admin->DataBase->query($sql);
	  $due_dates = calculate_terms_due_dates($result->fields['post_date'], $result->fields['terms'], ($type == 'v' ? 'AP' : 'AR'));
	  if ($negate) {
	    $line_item['total_amount'] = -$line_item['total_amount'];
	    $line_item['discount']     = -$line_item['discount'];
	    $line_item['amount_paid']  = -$line_item['amount_paid'];
	  }
	  $balance += $line_item['total_amount'];
	  $item_list[] = array(
		'id'                  => $result->fields['id'],
		'waiting'             => $result->fields['waiting'],
		'purchase_invoice_id' => $result->fields['purchase_invoice_id'],
		'purch_order_id'      => $result->fields['purch_order_id'],
		'percent'             => $due_dates['discount'],
		'post_date'           => $result->fields['post_date'],
		'early_date'          => \core\classes\DateTime::createFromFormat(DATE_FORMAT, $due_dates['early_date']),
		'net_date'            => \core\classes\DateTime::createFromFormat(DATE_FORMAT, $due_dates['net_date']),
		'total_amount'        => $admin->currencies->format($line_item['total_amount']),
		'gl_acct_id'          => $result->fields['gl_acct_id'],
		'description'         => $line_item['description'],
		'discount'            => $line_item['discount']    ? $admin->currencies->format($line_item['discount']) : '',
		'amount_paid'         => $line_item['amount_paid'] ? $admin->currencies->format($line_item['amount_paid']) : '',
	  );
	  $index++;
	}
	switch(PHREEBOOKS_DEFAULT_BILL_SORT) {
	  case 'due_date': // sort the open invoice array to order by preference
		foreach ($item_list as $key => $row) {
			$net_date[$key]   = $row['net_date'];
			$invoice_id[$key] = $row['purchase_invoice_id'];
		}
		array_multisort($net_date, SORT_ASC, $invoice_id, SORT_ASC, $item_list);
	  default: // sort by invoice number
	}
    return array('balance' => $balance, 'payment_fields' => $payment_fields, 'invoices' => $item_list);
}

function fetch_partially_paid($id) {
  global $admin;
  $sql = "select sum(i.debit_amount) as debit, sum(i.credit_amount) as credit
	from " . TABLE_JOURNAL_MAIN . " m inner join " . TABLE_JOURNAL_ITEM . " i on m.id = i.ref_id
	where i.so_po_item_ref_id = $id and m.journal_id in (18, 20) and i.gl_type in ('chk', 'pmt')
	group by m.journal_id";
  $result = $admin->DataBase->query($sql);
  if($result->fetch(\PDO::FETCH_NUM) == 0) return 0;
  if ($result->fields['debit'] || $result->fields['credit']) {
    return $result->fields['debit'] + $result->fields['credit'];
  } else {
    return 0;
  }
}

function calculate_terms_due_dates($post_date, $terms_encoded, $type = 'AR') {
  	$terms = explode(':', $terms_encoded);
  	$net_date = new \core\classes\DateTime($post_date); 
  	$early_date = new \core\classes\DateTime($post_date);
  	$result = array();
  	switch ($terms[0]) {
		default:
		case '0': // Default terms
			$result['discount'] = constant($type . '_PREPAYMENT_DISCOUNT_PERCENT') / 100;
			$result['net_date'] = $net_date->modify("+". constant($type . '_NUM_DAYS_DUE'). " day")->format("Y-m-d");
			if ($result['discount'] <> 0) {
		  		$result['early_date'] = $early_date->modify("+".constant($type . '_PREPAYMENT_DISCOUNT_DAYS')." day")->format("Y-m-d");
			} else {
		  		$result['early_date'] = $post_date; // move way out
			}
			break;
		case '1': // Cash on Delivery (COD)
		case '2': // Prepaid
			$result['discount']   = 0;
			$result['early_date'] = $net_date->format("Y-m-d");
			$result['net_date']   = $post_date;
			break;
		case '3': // Special terms
			$result['discount']   = $terms[1] / 100;
			$result['early_date'] = $early_date->modify("+{$terms[2]} day")->format("Y-m-d");
			$result['net_date']   = $net_date->modify("+{$terms[3]} day")->format("Y-m-d");
			break;
		case '4': // Due on day of next month
			$result['discount']   = $terms[1] / 100;
			$result['early_date'] = $early_date->modify("+{$terms[2]} day")->format("Y-m-d");
			$result['net_date']   = \core\classes\DateTime::db_date_format( $terms[3] );
			break;
		case '5': // Due at end of month
			$result['discount']   = $terms[1] / 100;
			$result['early_date'] = $early_date->modify("+{$terms[2]} day")->format("Y-m-d");
			$result['net_date']   = $net_date->modify("+{$net_date->format('t')} day")->format("Y-m-d");
			break;
  	}
  	return $result;
}

function load_cash_acct_balance($post_date, $gl_acct_id, $period) {
  global $admin, $messageStack;
  $acct_balance = 0;
  if (!$gl_acct_id) return $acct_balance;
  $sql = "select beginning_balance from " . TABLE_CHART_OF_ACCOUNTS_HISTORY . "
	where account_id = '{$gl_acct_id}' and period = " . $period;
  $result = $admin->DataBase->query($sql);
  $acct_balance = $result->fields['beginning_balance'];

  // load the payments and deposits for the current period
  $bank_list = array();
  $sql = "select i.debit_amount, i.credit_amount
	from " . TABLE_JOURNAL_MAIN . " m inner join " . TABLE_JOURNAL_ITEM . " i on m.id = i.ref_id
	where m.period = " . $period . " and i.gl_account = '" . $gl_acct_id . "' and m.post_date <= '" . $post_date . "'
	order by m.post_date, m.journal_id";
  $result = $admin->DataBase->query($sql);
  while (!$result->EOF) {
    $acct_balance += $result->fields['debit_amount'] - $result->fields['credit_amount'];
    $result->MoveNext();
  }
  return $acct_balance;
}
	/**
	 * returns array of tax autorities.
	 * @throws \core\classes\userException when there are no tax records
	 * @return array
	 */
  	function gen_build_tax_auth_array() {
    	global $admin;
    	$sql = $admin->DataBase->prepare("SELECT tax_auth_id, description_short, account_id , tax_rate FROM " . TABLE_TAX_AUTH . " ORDER BY description_short");
    	$sql->execute();
    	if ($sql->fetch(\PDO::FETCH_NUM) < 1) throw new \core\classes\userException("there are no tax records");
    	while ($tax_auth_values = $sql->fetch(\PDO::FETCH_ASSOC)){
			$tax_auth_array[$tax_auth_values['tax_auth_id']] = array(
			  'description_short' => $tax_auth_values['description_short'],
			  'account_id'        => $tax_auth_values['account_id'],
			  'tax_rate'          => $tax_auth_values['tax_rate'],
			);
		}
    	return $tax_auth_array;
  	}

  	function gen_calculate_tax_rate($tax_authorities_chosen, $tax_auth_array) {
		$chosen_auth_array = explode(':', $tax_authorities_chosen);
		$total_tax_rate = 0;
		while ($chosen_auth = array_shift($chosen_auth_array)) {
	  		$total_tax_rate += $tax_auth_array[$chosen_auth]['tax_rate'];
		}
		return $total_tax_rate;
  	}

  	/**
  	 * will return tax rates.
  	 * @param string $type witch contact_type should be looked for.
  	 * c= customers, v = vendors, b = both
  	 * @param bool $contactForm is contact form is true a additional option will be presented (product default).
  	 * @return array (id, rate, text, auths)
  	 */
  	function ord_calculate_tax_drop_down($type = 'c', $contactForm = false) {
	    global $admin;
		$tax_auth_array = gen_build_tax_auth_array();
	    $raw_sql = "SELECT tax_rate_id, description_short, rate_accounts FROM " . TABLE_TAX_RATES;
		switch ($type) {
			  default:
			  case 'c':
			  case 'v': $raw_sql .= " where type = '$type'"; break;
			  case 'b': // both
		}
		$sql = $admin->DataBase->query($raw_sql);
		$sql->execute();
	    $tax_rate_drop_down = array();
	    if ($contactForm == true) $tax_rate_drop_down[] = array('id' => '-1', 'text' => TEXT_PRODUCT_DEFAULT, 'auths' => '');
	    $tax_rate_drop_down[] = array('id' => '0', 'rate' => '0', 'text' => TEXT_NONE, 'auths' => '');
	    while ($tax_rates = $sql->fetch(\PDO::FETCH_ASSOC)){
			$tax_rate_drop_down[] = array(
			  'id'    => $tax_rates['tax_rate_id'],
			  'rate'  => gen_calculate_tax_rate($tax_rates['rate_accounts'], $tax_auth_array),
			  'text'  => $tax_rates['description_short'],
			  'auths' => $tax_rates['rate_accounts'],
			);
		}
		return $tax_rate_drop_down;
  	}

  function ord_get_so_po_num($id = '') {
	global $admin;
	$result = $admin->DataBase->query("select purchase_invoice_id from " . TABLE_JOURNAL_MAIN . " where id = " . $id);
	return ($result->fetch(\PDO::FETCH_NUM)) ? $result->fields['purchase_invoice_id'] : '';
  }

  function ord_get_projects() {
    global $admin;
    $result_array = array();
    $result_array[] = array('id' => '', 'text' => TEXT_NONE);
	// fetch cost structure
	$costs = array();
	$result = $admin->DataBase->query("select cost_id, description_short from " . TABLE_PROJECTS_COSTS . " where inactive = '0'");
	while(!$result->EOF) {
	  $costs[$result->fields['cost_id']] = $result->fields['description_short'];
	  $result->MoveNext();
	}
	// fetch phase structure
	$phases = array();
	$result = $admin->DataBase->query("select phase_id, description_short, cost_breakdown from " . TABLE_PROJECTS_PHASES . " where inactive = '0'");
	while(!$result->EOF) {
	  $phases[$result->fields['phase_id']] = array(
	  	'text'   => $result->fields['description_short'],
		'detail' => $result->fields['cost_breakdown'],
	  );
	  $result->MoveNext();
	}
	// fetch projects
	$result = $admin->DataBase->query("select id, short_name, account_number from " . TABLE_CONTACTS . " where type = 'j' and inactive <> '1'");
	while(!$result->EOF) {
	  $base_id   = $result->fields['id'];
	  $base_text = $result->fields['short_name'];
	  if ($result->fields['account_number'] == '1' && sizeof($phases) > 0) { // use phases
		foreach ($phases as $phase_id => $phase) {
		  $phase_base = $base_id   . ':' . $phase_id;
		  $phase_text = $base_text . ' -> ' . $phase['text'];
		  if ($phase['detail'] == '1' && sizeof($costs) > 0) {
		    foreach ($costs as $cost_id => $cost) {
              $result_array[] = array('id' => $phase_base . ':' . $cost_id, 'text' => $phase_text . ' -> ' . $cost);
			}
		  } else {
            $result_array[] = array('id' => $phase_base, 'text' => $phase_text);
		  }
		}
	  } else {
        $result_array[] = array('id' => $base_id, 'text' => $base_text);
	  }
	  $result->MoveNext();
	}
    return $result_array;
  }

  function build_search_sql($fields, $id, $id_from = '', $id_to = '') {
    $crit = array();
    foreach ($fields as $field) {
	  $output = '';
	  switch ($id) {
	    default:
		case 'all':  break;
		case 'eq':   if ($id_from) $output .= $field . " = '" . $id_from . "'";      break;
		case 'neq':  if ($id_from) $output .= $field . " <> '" . $id_from . "'";     break;
		case 'like': if ($id_from) $output .= $field . " like '%" . $id_from . "%'"; break;
		case 'rng':
		  if ($id_from)          $output .= $field . " >= '" . $id_from . "'";
		  if ($output && $id_to) $output .= " and ";
		  if ($id_to)            $output .= $field . " <= '" . $id_to . "'";
	  }
	  if ($output) $crit[] = $output;
    }
    return ($crit) ? ('(' . implode(' or ', $crit) . ')') : '';
  }

  	/**
  	 * this function will repost journals
  	 * @param unknown $journals
     * @param unknown $start_date
	 * @param unknown $end_date
	 * @throws \core\classes\userException
   	 * @return number|boolean
   	 */

  	function repost_journals($journals, $start_date, $end_date) {
		global $admin;
		try{
			$end_date = new \core\classes\DateTime($end_date);
			if (sizeof($journals) == 0) throw new \core\classes\userException('no journals received to repost');
			$sql = "SELECT id FROM ".TABLE_JOURNAL_MAIN." WHERE journal_id IN (".implode(',', $journals).")
			  AND post_date>= '$start_date' AND post_date<'".$end_date->modify("+1 day")->format("Y-m-d")."' ORDER BY post_date, id";
			$result = $admin->DataBase->query($sql);
			$cnt = 0;
			$admin->DataBase->beginTransaction();
			while (!$result->EOF) {
			    $gl_entry = new \core\classes\journal($result->fields['id']);
			    $gl_entry->remove_cogs_rows(); // they will be regenerated during the re-post
			    if (!$gl_entry->Post('edit', true)) throw new \core\classes\userException('Failed Re-posting the journals, try a smaller range. The record that failed was # '.$gl_entry->id);
				$cnt++;
			    $result->MoveNext();
			}
		    $admin->DataBase->commit();
			return $cnt;

	  	}catch(Exception $e){
  		  $admin->DataBase->rollBack();
  		  throw $e;
  		}
  	}

  function calculate_aging($id, $type = 'c', $special_terms = '0') {
  	global $admin;
  	$output = array();
  	if (!$id) return $output;
  	$today         = date('Y-m-d');
  	$terms         = explode(':', $special_terms);
  	$credit_limit  = $terms[4] ? $terms[4] : constant(($type=='v'?'AP':'AR').'_CREDIT_LIMIT_AMOUNT');
	$due_days      = $terms[3] ? $terms[3] : constant(($type=='v'?'AP':'AR').'_NUM_DAYS_DUE');
	$due_date      = new \core\classes\DateTime();
	$due_date->modify("-{$due_days} day")->format('Y-m-d');
	$late_30 = new \core\classes\DateTime();
	$late_30->modify("-". ($type == 'v' ? AP_AGING_DATE_1 : AR_AGING_PERIOD_1). " day")->format('Y-m-d');
	$late_60 = new \core\classes\DateTime();
	$late_60->modify("-". ($type == 'v' ? AP_AGING_DATE_2 : AR_AGING_PERIOD_2). " day")->format('Y-m-d');
	$late_90 = new \core\classes\DateTime();
	$late_90->modify("-". ($type == 'v' ? AP_AGING_DATE_3 : AR_AGING_PERIOD_3). " day")->format('Y-m-d');
	$output = array(
	  'balance_0'  => '0',
	  'balance_30' => '0',
	  'balance_60' => '0',
	  'balance_90' => '0',
	);
	$inv_jid = ($type == 'v') ? '6, 7' : '12, 13';
	$pmt_jid = ($type == 'v') ? '20' : '18';
	$total_outstanding = 0;
	$past_due          = 0;
	$sql = "select id from " . TABLE_JOURNAL_MAIN . "
		where bill_acct_id = " . $id . " and journal_id in (" . $inv_jid . ") and closed = '0'";
	$open_inv = $admin->DataBase->query($sql);
	while(!$open_inv->EOF) {
	  $result = $admin->DataBase->query("select debit_amount, credit_amount from " . TABLE_JOURNAL_ITEM . " where gl_type = 'ttl' and ref_id = " . $open_inv->fields['id']);
	  $result2 = $admin->DataBase->query("select journal_id, post_date from " . TABLE_JOURNAL_MAIN . " where id = " . $open_inv->fields['id']);
	  $total_billed = $result->fields['debit_amount'] - $result->fields['credit_amount'];
	  $post_date = $result2->fields['post_date'];
	  $result = $admin->DataBase->query("select sum(debit_amount) as debits, sum(credit_amount) as credits
	    from " . TABLE_JOURNAL_ITEM . " where so_po_item_ref_id = '" . $open_inv->fields['id'] . "' and gl_type in ('pmt', 'chk')");
	  $total_paid = $result->fields['credits'] - $result->fields['debits'];
	  $balance = $total_billed - $total_paid;
	  if ($type == 'v') $balance = -$balance;
	  // start the placement in aging array
	  if ($post_date < $due_date) $past_due += $balance;
	  if ($post_date < $late_90) {
		$output['balance_90'] += $balance;
	    $total_outstanding += $balance;
	  } elseif ($post_date < $late_60) {
		$output['balance_60'] += $balance;
	    $total_outstanding += $balance;
	  } elseif ($post_date < $late_30) {
		$output['balance_30'] += $balance;
	    $total_outstanding += $balance;
	  } elseif ($post_date <= $today) {
		$output['balance_0']  += $balance;
	    $total_outstanding += $balance;
	  } // else it's in the future
	  $open_inv->MoveNext();
	}
	$output['total']        = $total_outstanding;
	$output['past_due']     = $past_due;
	$output['credit_limit'] = $credit_limit;
	$output['terms_lang']   = gen_terms_to_language($special_terms, false, ($type=='v'?'AP':'AR'));
	return $output;
  }

?>