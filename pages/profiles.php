<?php
	$func = rex_request('func', 'string');
	
	if ($func == '') {
		$list = rex_list::factory("SELECT `id`, `name`, `description`, `type` FROM `".rex::getTablePrefix()."markitup_profiles` ORDER BY `name` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('profiles_norowsmessage'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'" title="'.$this->i18n('column_hashtag').' '.rex_i18n::msg('add').'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnLabel('name', $this->i18n('profiles_column_name'));
		$list->setColumnLabel('description', $this->i18n('profiles_column_description'));
		$list->setColumnLabel('type', $this->i18n('profiles_column_type'));
		
		// functions column spans 2 data-columns
		$funcs = $this->i18n('profiles_column_functions');
		
		$list->addColumn($funcs, '<i class="rex-icon rex-icon-edit"></i> '.rex_i18n::msg('edit'), -1, ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams($funcs, ['id' => '###id###', 'func' => 'edit']);
		
		$delete = 'deleteCol';
		$list->addColumn($delete, '<i class="rex-icon rex-icon-delete"></i> '.rex_i18n::msg('delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams($delete, ['id' => '###id###', 'func' => 'delete']);
		$list->addLinkAttribute($delete, 'data-confirm', rex_i18n::msg('delete').' ?');
		
		$list->removeColumn('id');
		
		$content = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('profiles_formcaption_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('profiles_formcaption_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'markitup_profiles', '', 'id='.$id);
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('profiles_label_name'));
		//End - add name-field
		
		//Start - add description-field
			$field = $form->addTextField('description');
			$field->setLabel($this->i18n('profiles_label_description'));
		//End - add description-field

		//Start - add type-field
			$field = $form->addSelectField('type');
			$field->setLabel($this->i18n('profiles_label_type'));
			
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption('---', 0);
			$select->addOption('Textile', 'textile');
		//End - add type-field
		
		//Start - add markitup_buttons-field
			$field = $form->addTextAreaField('markitup_buttons');
			$field->setLabel($this->i18n('profiles_label_markitupbuttons'));
			
			$field = $form->addRawField('
				<dl class="rex-form-group form-group">
					<dt>
						&nbsp;
					</dt>
					<dd>
						<p><a href="javascript:void(0);" onclick="$(\'#rex-markitup-buttons-help\').toggle(\'fast\');">Zeige/verberge Hilfe</a></p>
						<div id="rex-markitup-buttons-help" style="display:none">'.
							'<b>bold</b><br>'.
							$this->i18n('profiles_buttons_bold_description').'<br>'.
							'<br>'.
							'<b>clips</b><br>'.
							$this->i18n('profiles_buttons_clips_description').'<br>'.
							'<br>'.
							'<b>clips</b><br>'.
							'<b>deleted</b><br>'.
							$this->i18n('profiles_buttons_deleted_description').'<br>'.
							'<br>'.
							'<b>formatting[h1|h2|h3|h4|h5|h6|p]</b><br>'.
							$this->i18n('profiles_buttons_formatting_description').'<br>'.
							'<br>'.
							'<b>italic</b><br>'.
							$this->i18n('profiles_buttons_italic_description').'<br>'.
							'<br>'.
							'<b>orderedlist</b><br>'.
							$this->i18n('profiles_buttons_orderedlist_description').'<br>'.
							'<br>'.
							'<b>underline</b><br>'.
							$this->i18n('profiles_buttons_underline_description').'<br>'.
							'<br>'.
							'<b>unorderedlist</b><br>'.
							$this->i18n('profiles_buttons_unorderedlist_description').'<br>'.
							'
						</div>
					</dd>
				</dl>
			');
		//End - add markitup_buttons-field
		
		if ($func == 'edit') {
			$form->addParam('id', $id);
		}
		
		$content = $form->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'delete') {
		$id = rex_request('id', 'int');
		
		$del = rex_sql::factory();
		$del->setQuery('DELETE FROM ' . rex::getTablePrefix() . 'markitup_profiles WHERE `id` = "'.$id.'"');
		echo 'Profil wurde gelöscht'; //todo: translate
	}
?>