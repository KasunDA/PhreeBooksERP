<?php
/*
 * Module Bizuno admin functions
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.TXT.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Bizuno to newer
 * versions in the future. If you wish to customize Bizuno for your
 * needs please refer to http://www.phreesoft.com for more information.
 *
 * @name       Bizuno ERP
 * @author     Dave Premo, PhreeSoft <support@phreesoft.com>
 * @copyright  2008-2018, PhreeSoft, Inc.
 * @license    http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @version    3.x Last Update: 2018-09-06
 * @filesource /lib/controller/module/bizuno/admin.php
 */

namespace bizuno;

class bizunoAdmin 
{
	public  $moduleID = 'bizuno';
    private $update_queue = [];

	function __construct()
    {
        $this->lang     = getLang($this->moduleID);
		$this->settings = array_replace_recursive(getStructureValues($this->settingsStructure()), getModuleCache($this->moduleID, 'settings', false, false, []));
        $this->structure= [
            'url'            => BIZUNO_URL."controller/module/$this->moduleID/",
            'version'        => MODULE_BIZUNO_VERSION,
			'category'		 => 'bizuno',
			'required'       => '1',
			'usersAttachPath'=> 'data/bizuno/users/uploads',
			'quickBar'       => ['styles'=>['float'=>'right','padding'=>'1px'],'child'=>[
				'sysMsg'     => ['order'=>20,'label'=>lang('messages'),'icon'=>'email','classes'=>['msgCount'],'required'=>true,'hideLabel'=>true,'attr'=>['id'=>'sysMsg'],'events'=>['onClick'=>"hrefClick('bizuno/messages/manager');"]],
				'encrypt'    => ['order'=>60,'label'=>lang('bizuno_encrypt_enable'),'icon'=>'encrypt-off','required'=>true,'hideLabel'=>true,'attr'=>['id'=>'ql_encrypt'],
                    'events' => ['onClick'=>"windowEdit('bizuno/main/encryptionForm','winEncrypt','".jsLang('bizuno_encrypt_enable')."',400,150)"]],
				'newTab'     => ['order'=>95,'label'=>lang('new_tab'), 'icon'=>'add','required'=>true,'hideLabel'=>true,'events'=>['onClick'=>"tabOpen('', '');"]],
                'home'       => ['order'=>90,'label'=>lang('bizuno_company'),'icon'=>'employee','events'=>['onClick'=>"hrefClick('');"],'child'=>[
                    'admin'  => ['order'=>10,'label'=>lang('settings'),'icon'=>'settings','events'=>['onClick'=>"hrefClick('bizuno/settings/manager');"]],
                    'profile'=> ['order'=>20,'label'=>lang('profile'), 'icon'=>'profile', 'events'=>['onClick'=>"hrefClick('bizuno/profile/edit');"]],
                    'roles'  => ['order'=>30,'label'=>lang('roles'),   'icon'=>'roles',   'events'=>['onClick'=>"hrefClick('bizuno/roles/manager');"]],
                    'users'  => ['order'=>40,'label'=>lang('users'),   'icon'=>'users',   'events'=>['onClick'=>"hrefClick('bizuno/users/manager');"]],
                    'help'   => ['order'=>50,'label'=>lang('help'),    'icon'=>'help',    'required'=>true,'events'=>['onClick'=>"winHref('bizuno_help', 'https://www.bizuno.com?p=bizuno/portal/helpMain')"]],
                    'message'=> ['order'=>60,'label'=>lang('messages'),'icon'=>'email',   'required'=>true,'events'=>['onClick'=>"hrefClick('bizuno/messages/manager');"]],
                    'ticket' => ['order'=>70,'label'=>lang('support'), 'icon'=>'support', 'required'=>true,'events'=>['onClick'=>"hrefClick('bizuno/tools/ticketMain');"],'hidden'=>defined('BIZUNO_SUPPORT_EMAIL')?false:true],
                    'logout' => ['order'=>90,'label'=>lang('logout'),  'icon'=>'logout',  'required'=>true,'events'=>['onClick'=>"jsonAction('bizuno/portal/logout');"]]]]]],
			'menuBar' => ['child'=>[
                'tools' => ['order'=>70,'label'=>lang('tools'),'icon'=>'tools','group'=>'tool','events'=>['onClick'=>"hrefClick('bizuno/main/bizunoHome&menuID=tools');"],'child'=>[
                    'imgmgr' => ['order'=>75,'label'=>lang('image_manager'),'icon'=>'mimeImg', 'events'=>['onClick'=>"jsonAction('bizuno/image/manager');"]],
                    'impexp' => ['order'=>85,'label'=>lang('bizuno_impexp'),'icon'=>'refresh', 'events'=>['onClick'=>"hrefClick('bizuno/tools/impExpMain');"]],
                    'backup' => ['order'=>90,'label'=>lang('backup'),       'icon'=>'backup',  'events'=>['onClick'=>"hrefClick('bizuno/backup/manager');"]]]]]],
			'hooks' => ['phreebooks'=>  ['tools'=>  [
                'fyCloseHome'=> ['page'=>'tools','class'=>'bizunoTools','order'=>50],
                'fyClose'    => ['page'=>'tools','class'=>'bizunoTools','order'=>50]]]]];
		if (strpos(getUserCache('profile', 'admin_encrypt', false, ''), ':')) {
			$this->structure['quickBar']['child']['encrypt'] = ['tip'=>lang('encrypt_enabled'),'order'=>60,'icon'=>'icon-encrypt-on'];
		}
		$this->dirlist = ['backups','data','images','temp'];
        $this->reportStructure = [
            'misc' => ['title'=>'misc', 'folders'=>  [
                'misc:rpt' => ['type'=>'dir', 'title'=>'reports'],
                'misc:misc'=> ['type'=>'dir', 'title'=>'forms']]],
            'bnk'  => ['title'=>'banking', 'folders'=>  [
                'bnk:rpt'  => ['type'=>'dir', 'title'=>'reports'],
                'bnk:j18'  => ['type'=>'dir', 'title'=>'bank_deposit'],
                'bnk:j20'  => ['type'=>'dir', 'title'=>'bank_check']]],
            'cust' => ['title'=>'customers', 'folders'=>  [
                'cust:rpt' => ['type'=>'dir', 'title'=>'reports'],
                'cust:j9'  => ['type'=>'dir', 'title'=>'journal_main_journal_id_9'],
                'cust:j10' => ['type'=>'dir', 'title'=>'journal_main_journal_id_10'],
                'cust:j12' => ['type'=>'dir', 'title'=>'journal_main_journal_id_12'],
                'cust:j13' => ['type'=>'dir', 'title'=>'journal_main_journal_id_13'],
                'cust:j19' => ['type'=>'dir', 'title'=>'sales_receipt'],
                'cust:lblc'=> ['type'=>'dir', 'title'=>'label'],
                'cust:ltr' => ['type'=>'dir', 'title'=>'letter'],
                'cust:stmt'=> ['type'=>'dir', 'title'=>'statement']]],
            'gl'   => ['title'=>'general_ledger', 'folders'=>  [
                'gl:rpt'   => ['type'=>'dir', 'title'=>'reports', 'type'=>'dir']]],
            'hr'   => ['title'=>'employees', 'folders'=> [
                'hr:rpt'   => ['type'=>'dir', 'title'=>'reports']]],
            'inv'  => ['title'=>'inventory', 'folders'=>  [
                'inv:rpt'  => ['type'=>'dir', 'title'=>'reports']]],
            'vend' => ['title'=>'vendors', 'folders'=>  [
                'vend:rpt' => ['type'=>'dir', 'title'=>'reports'],
                'vend:j3'  => ['type'=>'dir', 'title'=>'journal_main_journal_id_3'],
                'vend:j4'  => ['type'=>'dir', 'title'=>'journal_main_journal_id_4'],
                'vend:j7'  => ['type'=>'dir', 'title'=>'journal_main_journal_id_7'],
                'vend:lblv'=> ['type'=>'dir', 'title'=>'label'],
                'vend:stmt'=> ['type'=>'dir', 'title'=>'statement']]]];
		$this->phreeformProcessing = [
            'json'    => ['text'=>$this->lang['pf_proc_json'],    'group'=>lang('tools'),  'module'=>$this->moduleID,'function'=>'viewFormat'],
            'today'   => ['text'=>lang('today'),                  'group'=>lang('date'),   'module'=>$this->moduleID,'function'=>'viewFormat']];
		$this->phreeformFormatting = [
            'uc'      => ['text'=>$this->lang['pf_proc_uc'],      'group'=>lang('text'),   'module'=>$this->moduleID,'function'=>'viewFormat'],
			'lc'      => ['text'=>$this->lang['pf_proc_lc'],      'group'=>lang('text'),   'module'=>$this->moduleID,'function'=>'viewFormat'],
			'yesBno'  => ['text'=>$this->lang['pf_proc_yesbno'],  'group'=>lang('text'),   'module'=>$this->moduleID,'function'=>'viewFormat'],
			'blank'   => ['text'=>$this->lang['pf_proc_blank'],   'group'=>lang('text'),   'module'=>$this->moduleID,'function'=>'viewFormat'],
			'printed' => ['text'=>$this->lang['pf_proc_printed'], 'group'=>lang('text'),   'module'=>$this->moduleID,'function'=>'viewFormat'],
			'neg'     => ['text'=>$this->lang['pf_proc_neg'],     'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'n2wrd'   => ['text'=>$this->lang['pf_proc_n2wrd'],   'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'null0'   => ['text'=>$this->lang['pf_proc_null0'],   'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'rnd2d'   => ['text'=>$this->lang['pf_proc_rnd2d'],   'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'currency'=> ['text'=>lang('currency'),               'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'curLong' => ['text'=>lang('currency_long'),          'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'curNull0'=> ['text'=>$this->lang['pf_cur_null_zero'],'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'precise' => ['text'=>$this->lang['pf_proc_precise'], 'group'=>lang('numeric'),'module'=>$this->moduleID,'function'=>'viewFormat'],
			'date'    => ['text'=>$this->lang['pf_proc_date'],    'group'=>lang('date'),   'module'=>$this->moduleID,'function'=>'viewFormat']];
		$this->phreeformSeparators = [
            'sp'     => ['text'=>$this->lang['pf_sep_space1'], 'module'=>$this->moduleID,'function'=>'viewSeparator'],
			'2sp'    => ['text'=>$this->lang['pf_sep_space2'], 'module'=>$this->moduleID,'function'=>'viewSeparator'],
			'comma'  => ['text'=>$this->lang['pf_sep_comma'],  'module'=>$this->moduleID,'function'=>'viewSeparator'],
			'com-sp' => ['text'=>$this->lang['pf_sep_commasp'],'module'=>$this->moduleID,'function'=>'viewSeparator'],
			'nl'     => ['text'=>$this->lang['pf_sep_newline'],'module'=>$this->moduleID,'function'=>'viewSeparator'],
			'semi-sp'=> ['text'=>$this->lang['pf_sep_semisp'], 'module'=>$this->moduleID,'function'=>'viewSeparator'],
			'del-nl' => ['text'=>$this->lang['pf_sep_delnl'],  'module'=>$this->moduleID,'function'=>'viewSeparator']];
        $this->notes = [$this->lang['note_bizuno_install_1']];
	}

	/**
     * User configurable settings structure
     * @return array structure for settings forms
     */
    private function settingsStructure()
    {
        foreach ([0,1,2,3,4] as $value) { $selPrec[] = ['id'=>$value, 'text'=>$value]; }
        $selSep = [['id'=>'.','text'=>'Dot (.)'],['id'=>',','text'=>'Comma (,)'],['id'=>' ','text'=>'Space ( )']];
        $locale = localeLoadDB();
        foreach ($locale->Timezone as $value) { $timezones[] = ['id' => $value->Code, 'text'=> $value->Description]; }
//      $timezones = ['Etc/GMT+8'=>'Los Angeles (+8)','Etc/GMT+7'=>'Denver (+7)','Etc/GMT+6'=>'Chicago (+6)','Etc/GMT+5'=>'New York (+5)'];
        $selDate= [
            ['id'=>'m/d/Y','text'=>'mm/dd/yyyy'],
            ['id'=>'d/m/Y','text'=>'dd/mm/yyyy'],
            ['id'=>'Y/m/d','text'=>'yyyy/mm/dd'],
            ['id'=>'d.m.Y','text'=>'dd.mm.yyyy'],
            ['id'=>'Y.m.d','text'=>'yyyy.mm.dd'],
            ['id'=>'dmY',  'text'=>'ddmmyyyy'],
            ['id'=>'Ymd',  'text'=>'yyyymmdd'],
            ['id'=>'Y-m-d','text'=>'yyyy-mm-dd'],
        ];
		$ISO_country = getModuleCache('bizuno', 'settings', 'company', 'country', 'USA');
		$data  = [
            'general' => [
                'password_min'    => ['options'=>['min'=> 8],           'attr'=>['type'=>'integer','value'=> 8]],
				'max_rows'        => ['options'=>['min'=>10,'max'=>100],'attr'=>['type'=>'integer','value'=>20]],
				'session_max'     => ['options'=>['min'=> 0,'max'=>300],'attr'=>['type'=>'integer','value'=> 0]]], // min zero for auto refresh
			'company' => [
                'id'              => ['label'=>pullTableLabel('contacts',     'short_name', 'b'),'attr'=>['value'=>getUserCache('profile', 'biz_title')]],
				'primary_name'    => ['label'=>pullTableLabel('address_book', 'primary_name'),   'attr'=>['value'=>getUserCache('profile', 'biz_title')]],
				'contact'         => ['label'=>pullTableLabel('address_book', 'contact', 'm')],
				'email'           => ['label'=>pullTableLabel('address_book', 'email', 'm')],
				'contact_ap'      => ['label'=>pullTableLabel('address_book', 'contact', 'p')],
                'email_ap'        => ['label'=>pullTableLabel('address_book', 'email', 'p')],
				'contact_ar'      => ['label'=>pullTableLabel('address_book', 'contact', 'r')],
                'email_ar'        => ['label'=>pullTableLabel('address_book', 'email', 'r')],
				'address1'        => ['label'=>pullTableLabel('address_book', 'address1')],
				'address2'        => ['label'=>pullTableLabel('address_book', 'address2')],
				'city'            => ['label'=>pullTableLabel('address_book', 'city')],
				'state'           => ['label'=>pullTableLabel('address_book', 'state')],
				'postal_code'     => ['label'=>pullTableLabel('address_book', 'postal_code')],
				'country'         => ['label'=>pullTableLabel('address_book', 'country'),'attr'=>['type'=>'country']],
				'telephone1'      => ['label'=>pullTableLabel('address_book', 'telephone1')],
				'telephone2'      => ['label'=>pullTableLabel('address_book', 'telephone2')],
				'telephone3'      => ['label'=>pullTableLabel('address_book', 'telephone3')],
				'telephone4'      => ['label'=>pullTableLabel('address_book', 'telephone4')],
				'website'         => ['label'=>pullTableLabel('address_book', 'website')],
				'tax_rate_id'     => ['label'=>pullTableLabel('contacts',     'gov_id_number')],
				'logo'            => ['attr'=>['type'=>'hidden']]],
			'my_phreesoft_account' => [
                'phreesoft_user'  => ['label'=>lang('username')],
				'phreesoft_pass'  => ['label'=>lang('password'),'attr'=>['type'=>'password']],
				'test_con'        => ['label'=>lang('test'),'icon'=>'checkin','attr'=>['type'=>'hidden'],'events'=>['onClick'=>"jsonAction('bizuno/admin/testAccount', 0, jq('#my_phreesoft_account_phreesoft_user').val()+';'+jq('#my_phreesoft_account_phreesoft_pass').val());"]]],
            'mail' => [
                'smtp_enable'     => ['attr'=>['type'=>'selNoYes']],
				'smtp_host'       => ['attr'=>['value'=>'smtp.gmail.com']],
                'smtp_port'       => ['attr'=>['type'=>'integer', 'value'=>587]],
                'smtp_user'       => ['attr'=>['value'=>'']],
				'smtp_pass'       => ['attr'=>['type'=>'password','value'=>'']]],
			'bizuno_api' => [
                'gl_receivables'  => ['attr'=>['type'=>'ledger','id'=>'bizuno_api_gl_receivables','value'=>getModuleCache('phreebooks','settings','customers','gl_receivables')]],
				'gl_sales'        => ['attr'=>['type'=>'ledger','id'=>'bizuno_api_gl_sales',      'value'=>getModuleCache('phreebooks','settings','customers','gl_sales')]],
                'gl_discount'     => ['attr'=>['type'=>'ledger','id'=>'bizuno_api_gl_discount',   'value'=>getModuleCache('phreebooks','settings','customers','gl_discount')]],
                'gl_tax'          => ['attr'=>['type'=>'ledger','id'=>'bizuno_api_gl_tax',        'value'=>getModuleCache('phreebooks','settings','customers','gl_liability')]],
				'tax_rate_id'     => ['values'=>viewSalesTaxDropdown('c'),'attr'=>['type'=>'select','value'=>0]]],
			'locale' => [
                'timezone'        => ['values'=>$timezones,'attr'=>['type'=>'select','value'=>'America/New_York']],
                'number_precision'=> ['values'=>$selPrec,  'attr'=>['type'=>'select','value'=>'2']],
				'number_decimal'  => ['values'=>$selSep,   'attr'=>['type'=>'select','value'=>'.']],
				'number_thousand' => ['values'=>$selSep,   'attr'=>['type'=>'select','value'=>',']],
				'number_prefix'   => ['attr'=>['value'=>'']],
				'number_suffix'   => ['attr'=>['value'=>'']],
				'number_neg_pfx'  => ['attr'=>['value'=>'-']],
				'number_neg_sfx'  => ['attr'=>['value'=>'']],
				'date_short'      => ['values'=>$selDate,'attr'=>['type'=>'select','value'=>'m/d/Y']]],
            ];
		settingsFill($data, $this->moduleID);
		return $data;
	}

    public function testAccount(&$layout=[])
    {
        $parts  = explode(';', clean('data', 'text', 'get'), 2);
        $creds  = ['UserID'=>!empty($parts[0]) ? $parts[0] : '', 'UserPW'=>!empty($parts[1]) ? $parts[1] : ''];
        $io     = new io();
        $success= $io->apiPhreeSoft('testAccount', $creds);
        if (!empty($success['success'])) { msgAdd($this->lang['account_verified'], 'success'); }
    }

	/**
     * Special initialization methods for this module
     * @return boolean - true on success, false on error
     */
    function initialize()
    {
		return true;
	}

	/**
     * Structure for Settings main page for module Bizuno
     * @param array $layout - structure coming in
     * @return array - modified $layout
     */
    public function adminHome(&$layout=[])
    {
        if (!$security = validateSecurity('bizuno', 'admin', 1)) { return; }
        msgDebug("\nEditing with settings = ".print_r(getModuleCache('bizuno', 'settings'), true));
        $imgSrc = getModuleCache('bizuno', 'settings', 'company', 'logo');
        $imgDir = dirname($imgSrc) == '/' ? '/' : dirname($imgSrc).'/';
        $tools  = $this->getViewTools();
		$data   = [
            'tabs'   => ['tabAdmin'=> ['divs'=>  [
                'settings'=> ['order'=>20,'label'=>lang('settings'),  'src'=>BIZUNO_LIB."view/tabAdminSettings.php"],
				'tabs'    => ['order'=>40,'label'=>lang('extra_tabs'),'type'=>'html','html'=>'','options'=>['href'=>"'".BIZUNO_AJAX."&p=bizuno/tabs/manager'"]],
				'tools'   => ['order'=>50,'label'=>lang('tools'),'type'=>'divs','divs'=>[
                    'status' => ['order'=>20,'label'=>$this->lang['admin_status_update'],'type'=>'divs','divs'=>[
                        'formBOF'=> ['order'=>10,'type'=>'form','key'=>'frmStatus'],
                        'body'   => ['order'=>50,'type'=>'fields','fields'=>$tools['status']],
                        'formEOF'=> ['order'=>85,'type'=>'html','html'=>"</form>"]]],
                    'encrpt' => ['order'=>30,'label'=>$this->lang['admin_encrypt_update'],'type'=>'fields','fields'=>$tools['encrypt']],
                    'encDel' => ['order'=>40,'label'=>$this->lang['btn_security_clean'],  'type'=>'fields','fields'=>$tools['encDel']]]],
				'tabDBs'  => ['order'=>60,'label'=>lang('dashboards'),'settings'=>['module'=>$this->moduleID,'path'=>'dashboards'],'src'=>BIZUNO_LIB."view/tabAdminMethods.php"],
				'stats'   => ['order'=>99,'label'=>lang('statistics'),'type'=>'html','html'=>$this->getViewStats()]]]],
            'divs'   => ['footerLogo'  =>['order'=>99,'type'=>'html','html'=>'<div id="imdtl_company_logo"></div>']],
			'forms'  => ['frmStatus'=>['attr'=>['type'=>'form','action'=>BIZUNO_AJAX."&p=bizuno/settings/statusSave"]]],
            'jsBody' => ['company_logo'=>"imgManagerInit('company_logo', '$imgSrc', '$imgDir', 'images/');"],
            'jsReady'=> ['init'=>"ajaxForm('frmStatus');"]];
		// add special fields
		$data['module_install_btn']= ['attr'=>['type'=>'button','value'=>lang('install')]];
		$data['module_remove_btn'] = ['attr'=>['type'=>'button','value'=>lang('remove')]];
		$data['module_prop_btn']   = ['icon'=>'settings', 'size'=>'medium'];
		$layout = array_replace_recursive($layout, adminStructure($this->moduleID, $this->settingsStructure(), $this->lang), $data);
	}

    private function getViewTools()
    {
		$status = dbLoadStructure(BIZUNO_DB_PREFIX."current_status");
		$result = dbGetRow(BIZUNO_DB_PREFIX."current_status", "id=1");
		foreach ($result as $key => $value) {
			$status[$key]['position']= 'after';
			$status[$key]['break']   = true;
			$status[$key]['label']   = sprintf(lang('next_ref'), lang($key));
			$status[$key]['attr']['value'] = $value;
		}
		ksort($status);
        $output = [
            'status' => ['status_btn'=>['icon'=>'save','label'=>'save','events'=>['onClick'=>"jq('#frmStatus').submit();"]]],
            'encrypt'=> [
                'desc'              => ['html'=>$this->lang['desc_encrypt_config'],'attr'=>['type'=>'raw']],
                'encrypt_key_btn'   => ['label'=>'','events'=>['onClick'=>"encryptChange();"],'attr'=>['type'=>'button','value'=>lang('change')]],
                'encrypt_key_orig'  => ['label'=>$this->lang['admin_encrypt_old'],    'position'=>'after','attr'=>['type'=>'password']],
                'encrypt_key_new'   => ['label'=>$this->lang['admin_encrypt_new'],    'position'=>'after','attr'=>['type'=>'password']],
                'encrypt_key_dup'   => ['label'=>$this->lang['admin_encrypt_confirm'],'position'=>'after','attr'=>['type'=>'password']]],
            'encDel' => [
                'desc'              => ['html'=>$this->lang['desc_security_clean'],'attr'=>['type'=>'raw']],
                'encrypt_clean_date'=> ['label' =>$this->lang['desc_security_clean_date'],'attr'=>['type'=>'date','value'=>date('Y-m-d')]],
                'encrypt_clean_btn' => ['events'=>['onClick'=>"jq('body').addClass('loading'); jsonAction('bizuno/tools/encryptionClean', 0, jq('#encrypt_clean_date').datebox('getValue'));"],
                    'attr'=>['type'=>'button','value'=>lang('start')]]]];
        foreach ($status as $key => $settings) { if ($key != 'id') { $output['status'][$key] = $settings; } }
        return $output;
    }

    private function getViewStats()
    {
        $stmt   = dbGetResult("SHOW TABLE STATUS");
        $stats  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $output = "<!-- db table stats -->\n<fieldset><legend>".$this->lang['table_stats']."</legend>\n".'<table style="border-style:none;width:100%">
<thead class="panel-header">
    <tr><th>'.lang('table')."</th><th>".$this->lang['db_engine']."</th><th>".$this->lang['db_rows']."</th><th>".$this->lang['db_collation']."</th><th>".lang('size')."</th><th>".$this->lang['db_next_id']."</th></tr>
</thead>
<tbody>\n";
        foreach ($stats as $table) {
            $output .= "<tr><td>{$table['Name']}</td><td>{$table['Engine']}</td><td>{$table['Rows']}</td><td>{$table['Collation']}</td><td>".($table['Data_length']+$table['Index_length'])."</td><td>{$table['Auto_increment']}</td></tr>\n";
        }
        $output .= "</tbody></table>\n</fieldset>\n";
        return $output;
    }
	/**
     * Special operations to save settings page beyond core settings
     * Check for company name change and update portal
     */
    public function adminSave()
    {
        $newTitle = clean('company_primary_name', 'text', 'post');
        if (getUserCache('profile', 'biz_title') <> $newTitle) { portalUpdateBizID($newTitle); }
		readModuleSettings($this->moduleID, $this->settings);
	}

	/**
	 * This method pulls common data and uploads to browser to speed up page updates. It should be extended by every module that wants to upload static data for a browser session
	 */
	public function loadBrowserSession(&$layout=[])
    {
        // load the default currency, locale
        $locale       = getModuleCache('bizuno', 'settings', 'locale');
		$dateDelimiter= substr(preg_replace("/[a-zA-Z]/", "", $locale['date_short']), 0, 1);
		$locales      = localeLoadDB(); // load countries
		$countries    = [];
        $defISO       = getModuleCache('bizuno', 'settings', 'company', 'country');
        $defTitle     = $defISO;
		foreach ($locales->Locale as $value) {
			$countries[] = ['iso3'=>$value->Country->ISO3, 'iso2'=>$value->Country->ISO2, 'title'=>$value->Country->Title];
            if ($defISO == $value->Country->ISO3) { $defTitle = $value->Country->Title; }
		}
        $ISOCurrency = getUserCache('profile', 'currency', false, 'USD');     
		$data = [
            'calendar'  => ['format'=>$locale['date_short'], 'delimiter'=>$dateDelimiter],
			'country'   => ['iso'=>$defISO,'title'=>$defTitle],
			'currency'  => ['defaultCur'=>$ISOCurrency, 'currencies'=>getModuleCache('phreebooks', 'currency', 'iso')],
            'language'  => substr(getUserCache('profile', 'language', false, 'en_US'), 0, 2),
			'locale'    => [
                'precision'=> isset($locale['number_precision'])? $locale['number_precision']: '2',
				'decimal'  => isset($locale['number_decimal'])  ? $locale['number_decimal']  : '.',
				'thousand' => isset($locale['number_thousand']) ? $locale['number_thousand'] : ',',
				'prefix'   => isset($locale['number_prefix'])   ? $locale['number_prefix']   : '',
				'suffix'   => isset($locale['number_suffix'])   ? $locale['number_suffix']   : '',
				'neg_pfx'  => isset($locale['number_neg_pfx'])  ? $locale['number_neg_pfx']  : '-',
				'neg_sfx'  => isset($locale['number_neg_sfx'])  ? $locale['number_neg_sfx']  : ''],
            'dictionary'=> $this->getBrowserLang(),
            'countries' => ['total'=>sizeof($countries), 'rows'=>$countries]];
        $layout = array_replace_recursive($layout, ['content'=>$data]);
	}
    
    private function getBrowserLang()
    {
        return ['ACCOUNT'=>lang('account'),
            'CITY'       =>lang('address_book_city'),
            'CONTACT_ID' =>lang('contacts_short_name'),
            'EDIT'       =>lang('edit'),
            'FINISHED'   =>lang('finished'),
            'INFORMATION'=>lang('information'),
            'MESSAGE'    =>lang('message'),
            'NAME'       =>lang('address_book_primary_name'),
            'PLEASE_WAIT'=>lang('please_wait'),
            'SETTINGS'   =>lang('settings'),
            'SHIPPING_ESTIMATOR'=>lang('shipping_estimator'), // @todo move this to extShipping and add to loadBrowserSession hook for extShipping
            'STATE'      =>lang('address_book_state'),
            'TITLE'      =>lang('title'),
            'TOTAL'      =>lang('total'),
            'TRASH'      =>lang('trash'),
            'TYPE'       =>lang('type'),
            'VIEW'       =>lang('view')];
    }

    /**
     * Sets the admin account and database credentials, portal specific
     * @param type $layout
     */
    public function installPreFlight(&$layout=[])
    {
        $success = true;
        require_once(BIZUNO_ROOT."portal/guest.php");
        $guest = new guest();
        if (method_exists($guest, 'installPreFlight')) { $success = $guest->installPreFlight($layout); }
        $bID = clean('bID', 'integer', 'get');
        if ($success) { $layout = ['content'=>['action'=>'eval', 'actionData'=>"jsonAction('bizuno/admin/installForm', $bID);"]]; }
    }

    /**
     * Sets the popup form to load db and set starting settings
     * @param arrray $layout
     */
    public function installForm(&$layout=[])
    {
		$bID  = clean('rID', 'integer', 'get');
        if (!$bID) { return msgAdd("bad business ID: $bID"); }
        $data = ['type'=>'popup','title'=>$this->lang['bizuno_install_title'],'attr'=>['id'=>'bizInstall','wClosable'=>false],
            'toolbars' => ['tbInstall'=>  ['icons'=> [
                'instBack' => ['order'=>10,'icon'=>'close','label'=>lang('cancel'),'events'=>['onClick'=>"bizWindowClose('bizInstall');"]],
				'instNext' => ['order'=>20,'icon'=>'next', 'label'=>lang('next'),  'events'=>['onClick'=>"installSave($bID);"]]]]],
			'divs' => [
                'toolbar'=> ['order'=>10,'type'=>'toolbar','key'   =>'tbInstall'],
                'divBOF' => ['order'=>15,'type'=>'html',   'html'  =>'<div id="divInstall"><p>'.$this->lang['intro'].'</p>'],
				'body'   => ['order'=>50,'type'=>'fields', 'fields'=>$this->getViewInstall()],
                'divEOF' => ['order'=>85,'type'=>'html',   'html'  =>"</div>"]],
           'jsBody'=>['init'=>$this->getViewInstallJS()]];
		$layout = array_replace_recursive($layout, $data);
	}

    private function getViewInstall()
    {
		$langs = viewLanguages(true);
		$crncy = viewCurrencyDropdown();
	    $charts= localeLoadCharts();
		$years = [];
		$year  = date('Y');
        for ($i=2; $i>=0; $i--) { $years[] = ['id'=>$year - $i, 'text'=>$year - $i]; }
        $biz_title   = ['label'=>$this->lang['biz_title'],                     'attr'=>['value'=>getUserCache('profile', 'biz_title'),'maxlength'=>'16']];
        $biz_lang    = ['label'=>$this->lang['biz_lang'],    'values'=>$langs, 'attr'=>['type'=>'select','value'=>'en_US']];
        $biz_currency= ['label'=>$this->lang['biz_currency'],'values'=>$crncy, 'attr'=>['type'=>'select','value'=>'USD']];
        $biz_chart   = ['label'=>$this->lang['biz_chart'],   'values'=>$charts,'attr'=>['type'=>'select','value'=>"locale/en_US/charts/retailCorp.xml"]];
        $biz_fy      = ['label'=>$this->lang['biz_fy'],      'values'=>$years, 'attr'=>['type'=>'select','value'=>date('Y')]];
        return [
            'biz_title'   => array_merge($biz_title,   ['break'=>true]),
            'biz_lang'    => array_merge($biz_lang,    ['break'=>true]),
            'biz_currency'=> array_merge($biz_currency,['break'=>true]),
            'biz_chart'   => array_merge($biz_chart,   ['break'=>true]),
            'biz_fy'      => $biz_fy];
    }

    private function getViewInstallJS()
    {
        return "function installSave(bizID) {
    jq('#instNext').linkbutton({ iconCls:'iconL-loading',text:'' });
    divData = jq('#divInstall :input').serializeObject();
    jq.ajax({
        url:     '".BIZUNO_AJAX."&p=bizuno/admin/installBizuno&bID='+bizID,
        type:    'post',
        data:    divData,
        async:   false,
        success: function (data) { processJson(data); }
    });
    jq('#bizInstall').window('destroy');
}";
    }

	public function installBizuno(&$layout=[])
    {
        global $bizunoUser;
        require_once(BIZUNO_LIB ."controller/module/bizuno/settings.php");
		require_once(BIZUNO_LIB ."controller/module/phreebooks/admin.php");
        require_once(BIZUNO_ROOT."portal/guest.php");
        require_once(BIZUNO_LIB ."model/registry.php");
        ini_set('memory_limit','1024M'); // temporary
        $guest = new guest();
        if (method_exists($guest, 'installBizunoPre')) { if (!$guest->installBizunoPre()) { return; } } // pre-install for portal 
        $usrEmail = biz_validate_user()[0];
        if (!$usrEmail) { return msgAdd('User is not logged in!'); }
        setUserCache('profile', 'biz_id',   clean('bID',         'text',   'get'));
		setUserCache('profile', 'biz_title',clean('biz_title',   'text',   'post'));
		setUserCache('profile', 'language', clean('biz_lang',    'text',   'post'));
		setUserCache('profile', 'currency', clean('biz_currency','text',   'post'));
		setUserCache('profile', 'chart',    clean('biz_chart',   'text',   'post'));
		setUserCache('profile', 'first_fy', clean('biz_fy',      'integer','post'));
		// error check title
        if (strlen(getUserCache('profile', 'biz_title')) < 3) { return msgAdd('Your business name needs to be from 3 to 15 characters!'); }
		// Here we go, ready to install
		$bAdmin = new bizunoSettings();
		msgDebug("\n  Creating the company directory");
		if (!is_dir(BIZUNO_DATA)) { mkdir(BIZUNO_DATA, 0755); }
		// ready to install, tables first
        if (dbTableExists(BIZUNO_DB_PREFIX.'journal_main')) { return msgAdd("Cannot install, the database has tables present. Aborting!"); }
		$tables = [];
		require(BIZUNO_LIB."controller/module/bizuno/install/tables.php"); // get the tables
		$bAdmin->adminInstTables($tables);
        // Set the current_status to defaults for module install to work properly
		dbWrite(BIZUNO_DB_PREFIX."current_status", ['id'=>1]);
        // Load PhreeBooks defaults
		$pbAdmin = new phreebooksAdmin();
		$pbAdmin->installFirst();// load the chart, currency and initialize
		// now Modules
		setUserCache('security', 'admin', 4);
        msgDebug("\nModule list to install = ".print_r($guest->getModuleList(true), true));
		foreach ($guest->getModuleList(true) as $module => $path) {
			$bAdmin->moduleInstall($layout, $module, $path);
		}
		// create the admin user account
        setUserCache('profile', 'email', $usrEmail);
        $admin_id = isset($GLOBALS['bizuno_install_admin_id']) ? $GLOBALS['bizuno_install_admin_id'] : 1;
        setUserCache('profile', 'admin_id', $admin_id); // since first record in db
		$role_id  = dbWrite(BIZUNO_DB_PREFIX."roles", ['title'=>'admin','settings'=>'']);
		$userData = ['email'=>$usrEmail, 'title'=>'Admin', 'role_id'=>$role_id, 'settings'=>json_encode($bizunoUser)];
        dbWrite(BIZUNO_DB_PREFIX."users", $userData);
        $bAdmin->adminFillSecurity($role_id, 4);
		// create some starting dashboards
        $dashData1 = ['user_id'=>$admin_id,'menu_id'=>'home','module_id'=>'bizuno','dashboard_id'=>'my_to_do', 'column_id'=>0,'row_id'=>0,'settings'=>json_encode($bAdmin->notes)];
        dbWrite(BIZUNO_DB_PREFIX."users_profiles", $dashData1);
        $dashData2 = ['user_id'=>$admin_id,'menu_id'=>'home','module_id'=>'bizuno','dashboard_id'=>'daily_tip','column_id'=>1,'row_id'=>1];
        dbWrite(BIZUNO_DB_PREFIX."users_profiles", $dashData2);
        $dashData3 = ['user_id'=>$admin_id,'menu_id'=>'home','module_id'=>'bizuno','dashboard_id'=>'ps_news',  'column_id'=>2,'row_id'=>2];
        dbWrite(BIZUNO_DB_PREFIX."users_profiles", $dashData3);
        if (method_exists($guest, 'installBizuno')) { $guest->installBizuno(); } // hook for after db set up for portal 
        // build the registry
        $registry = new bizRegistry();
        $registry->initRegistry(getUserCache('profile', 'email'), getUserCache('profile', 'biz_id'));
        $company = getModuleCache('bizuno', 'settings', 'company'); // set the business title and id
        $company['id'] = $company['primary_name'] = getUserCache('profile', 'biz_title');
        setModuleCache('bizuno', 'settings', 'company', $company);
		msgLog(lang('user_login')." ".getUserCache('profile', 'email'));
        portalWrite('business', ['title'=>$company['id'],'date_last_visit'=>date('Y-m-d h:i:s')], 'update', "id='".getUserCache('profile', 'biz_id')."'");
		$layout = ['content'=>['action'=>'eval','actionData'=>"loadSessionStorage();"]];
	}
}
