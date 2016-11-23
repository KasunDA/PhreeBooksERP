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
//  Path: /modules/contacts/pages/main/template_i_general.php
//
// some setup
\core\classes\messageStack::debug_log(" results = ".print_r($basis->cInfo->contact,true) );
$acct_def = (!$basis->cInfo->contact->dept_rep_id) ? array() : array(array('id'=>$basis->cInfo->contact->department_rep->id, 'text'=>$basis->cInfo->contact->department_rep->dept_rep_id));
// *********************** Display account information ****************************** ?>
	<div title="<?php echo TEXT_GENERAL;?>" style="padding:10px">
  		<fieldset>
    		<legend><?php echo TEXT_CONTACT_INFORMATION; ?></legend>
    		<table>
      			<tr>
					<td><?php echo \core\classes\htmlElement::textbox("short_name",	TEXT_CONTACT_ID, 'size="21" maxlength="20"', $basis->cInfo->contact->short_name, $basis->cInfo->contact->auto_type == false);?></td>
	      			<td><?php echo \core\classes\htmlElement::checkbox('inactive', TEXT_INACTIVE, '1', $basis->cInfo->contact->inactive );?></td>
    				<td><?php echo \core\classes\htmlElement::textbox("contact_middle",	TEXT_TITLE,	'size="33" maxlength="32"', $basis->cInfo->contact->contact_middle);?></td>
      			</tr>
      			<tr>
			    	<td><?php echo \core\classes\htmlElement::textbox("contact_first",	TEXT_FIRST_NAME,  	'size="33" maxlength="32"', $basis->cInfo->contact->contact_first);?></td>
			        <td><?php echo \core\classes\htmlElement::textbox("contact_last",	TEXT_LAST_NAME, 	'size="33" maxlength="32"', $basis->cInfo->contact->contact_last);?></td>
			    </tr>
      			<tr>
      				<td><?php echo \core\classes\htmlElement::textbox("account_number",	TEXT_FACEBOOK_ID, 	'size="17" maxlength="16"'); ?></td>
      				<td><?php echo \core\classes\htmlElement::textbox("gov_id_number", 	TEXT_TWITTER_ID, 	'size="17" maxlength="16"'); ?></td>
      			</tr>
      			<tr>
        			<td><?php echo \core\classes\htmlElement::combobox('dept_rep_id', TEXT_LINK_TO, $acct_def, $basis->cInfo->contact->dept_rep_id); ?></td>
      			</tr>
    		</table>
  		</fieldset>
<script type="text/javascript">
$('#dept_rep_id').combobox({
    url:'index.php?module=contacts&page=ajax&op=load_contact_info&guess='+$(this).combobox('getValue'),
    valueField:'id',
    textField:'text',
    queryParams: {
		dataType: 'json',
        contentType: 'application/json',
        async: false,
	},
});
</script>
<?php // *********************** Mailing/Main Address (only one allowed) ****************************** ?>
	  	<div id="address_panel" class="easyui-panel" title="<?php echo TEXT_MAIN_MAILING_ADDRESS; ?>" style= "width:75%"> 	</div>
	  	<script type="text/javascript">
			$('#address_panel').panel({
	            border: true,
	            queryParams: {
	            	contentType:'inlineForm',
			        async: false,
			        contact_id: '<?php echo $basis->cInfo->contact->id;?>',
			        type: '<?php echo $basis->cInfo->contact->type;?>m',
				},
	            cache: false,
	            href:'index.php?action=editAddress',
	            loadMsg:	"<?php echo TEXT_PLEASE_WAIT?>",
	            onLoad: function(){
	           
	            },
	            onBeforeLoad: function(param){
	        		console.log('loading the main_address form');
	        	},
	        	onLoadSuccess: function(data){
	        		console.log('the loading the main_address form was succesfull');
	        		$.messager.progress('close');
	        	},
	            onLoadError: function(){
	        		console.error('the loading of the main_address form resulted in a error');
	        		$.messager.progress('close');
	        		$.messager.alert('<?php echo TEXT_ERROR?>','Load error for main_address form');
	        	},
	        });
		</script>

	<?php // *********************** Attachments  ************************************* ?>
	  	<div>
	   		<fieldset>
	   			<legend><?php echo TEXT_ATTACHMENTS; ?></legend>
	   			<table class="ui-widget" style="border-collapse:collapse;margin-left:auto;margin-right:auto;">
	    			<thead class="ui-widget-header">
	     				<tr><th colspan="3"><?php echo TEXT_ATTACHMENTS; ?></th></tr>
	    			</thead>
	    			<tbody class="ui-widget-content">
	     				<tr><td colspan="3"><?php echo TEXT_SELECT_FILE_TO_ATTACH . ' ' . html_file_field('file_name'); ?></td></tr>
	     				<tr  class="ui-widget-header">
	      					<th><?php echo html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small'); ?></th>
	      					<th><?php echo TEXT_FILE_NAME; ?></th>
	      					<th><?php echo TEXT_ACTION; ?></th>
	     				</tr>
	<?php
						if (sizeof($basis->cInfo->contact->attachments) > 0) {
						  	foreach ($basis->cInfo->contact->attachments as $key => $value) {
						    	echo '<tr>';
							    echo ' <td>' . \core\classes\htmlElement::checkbox('rm_attach_'.$key, '', '1', false) . '</td>' . chr(10);
							    echo " <td>{$value}</td>" . chr(10);
							    echo ' <td>' . html_button_field('dn_attach_'.$key, TEXT_DOWNLOAD, "onclick='submitSeq({$key}, \"ContactAttachmentDownload\", true)'") . '</td>';
							    echo '</tr>' . chr(10);
						  	}
						} else {
						  	echo '<tr><td colspan="3">' . TEXT_NO_DOCUMENTS_HAVE_BEEN_FOUND . '</td></tr>';
						} ?>
	    			</tbody>
	   			</table>
	   		</fieldset>
	  	</div>
	</div>