<?php
	$func = rex_request('func', 'string');

	if ($func == '') {
		$list = rex_list::factory("SELECT `id`, `name`, `description`, `type`, CONCAT('markitupEditor-',`name`) as `cssclass` FROM `".rex::getTable('markitup_profiles')."` ORDER BY `name` ASC");
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
		$list->setColumnLabel('cssclass', $this->i18n('profiles_column_cssclass'));

		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);

		$list->removeColumn('id');

		$content = $list->get();

		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo $content;
	} else if ($func == 'add' || $func == 'edit') {

        // Wenn ein Profil erfolgreich gespeichert wurde (add|edit)
        // werden die darauf basierenden Dateien markitup_profiles.[css|js] neu angelegt

        rex_extension::register( 'REX_FORM_SAVED', function( $ep ) {
            include_once( $this->getPath('functions/cache_markitup_profiles.php'));
            echo markitup_cache_update( );
        } );
        rex_extension::register( 'REX_FORM_DELETED', function( $ep ) {
            include_once( $this->getPath('functions/cache_markitup_profiles.php'));
            echo markitup_cache_update( );
        } );

		$id = rex_request('id', 'int');

		if ($func == 'edit') {
			$formLabel = $this->i18n('profiles_formcaption_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('profiles_formcaption_add');
		}

		$form = rex_form::factory(rex::getTable('markitup_profiles'), '', 'id='.$id);

		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('profiles_label_name'));
            $field->getValidator()->add( 'notEmpty', $this->i18n('validate_empty',$this->i18n('profiles_label_name')));
            $field->getValidator()->add( 'custom', $this->i18n('validate_unique',$this->i18n('profiles_label_name')), function($value) use ($id) {
                $profiles = rex_sql::factory()->getArray( 'SELECT id FROM '.rex::getTable('markitup_profiles').' WHERE name LIKE :name LIMIT 1', [':name'=>$value] );
                if( !$profiles ) return true;
                if( $profiles[0]['id'] == $id ) return true;
                return false;
            } );
		//End - add name-field

		//Start - add description-field
			$field = $form->addTextField('description');
			$field->setLabel($this->i18n('profiles_label_description'));
		//End - add description-field

		//Start - add minheight-field
			$field = $form->addTextField('minheight');
			$field->setLabel($this->i18n('profiles_label_minheight'));
		//End - add minheight-field

		//Start - add maxheight-field
			$field = $form->addTextField('maxheight');
			$field->setLabel($this->i18n('profiles_label_maxheight'));
		//End - add maxheight-field

		//Start - add urltype-field
			$field = $form->addSelectField('urltype');
			$field->setLabel($this->i18n('profiles_label_urltype'));

			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption($this->i18n('profiles_label_urltype_option_relative'), 'relative');
			$select->addOption($this->i18n('profiles_label_urltype_option_absolute'), 'absolute');
		//End - add urltype-field

		//Start - add type-field
			$field = $form->addSelectField('type');
			$field->setLabel($this->i18n('profiles_label_type'));

			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption('---', 0);
			$select->addOption('Markdown', 'markdown');
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
							'<b>clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2]</b><br>'.
							$this->i18n('profiles_buttons_clips_description').'<br>'.
							'<br>'.
							'<b>deleted</b><br>'.
							$this->i18n('profiles_buttons_deleted_description').'<br>'.
							'<br>'.
							'<b>emaillink</b><br>'.
							$this->i18n('profiles_buttons_emaillink_description').'<br>'.
							'<br>'.
							'<b>externallink</b><br>'.
							$this->i18n('profiles_buttons_externallink_description').'<br>'.
							'<br>'.
							'<b>internallink</b><br>'.
							$this->i18n('profiles_buttons_internallink_description').'<br>'.
							'<br>'.
							'<b>italic</b><br>'.
							$this->i18n('profiles_buttons_italic_description').'<br>'.
							'<br>'.
							'<b>groupheading[1|2|3|4|5|6]</b><br>'.
							$this->i18n('profiles_buttons_groupheading_description').'<br>'.
							'<br>'.
							'<b>grouplink[file|internal|external|mailto]</b><br>'.
							$this->i18n('profiles_buttons_grouplink_description').'<br>'.
							'<br>'.
							'<b>heading1</b><br>'.
							$this->i18n('profiles_buttons_heading1_description').'<br>'.
							'<br>'.
							'<b>heading2</b><br>'.
							$this->i18n('profiles_buttons_heading2_description').'<br>'.
							'<br>'.
							'<b>heading3</b><br>'.
							$this->i18n('profiles_buttons_heading3_description').'<br>'.
							'<br>'.
							'<b>heading4</b><br>'.
							$this->i18n('profiles_buttons_heading4_description').'<br>'.
							'<br>'.
							'<b>heading5</b><br>'.
							$this->i18n('profiles_buttons_heading5_description').'<br>'.
							'<br>'.
							'<b>heading6</b><br>'.
							$this->i18n('profiles_buttons_heading6_description').'<br>'.
							'<br>'.
							'<b>media</b><br>'.
							$this->i18n('profiles_buttons_media_description').'<br>'.
							'<br>'.
							'<b>medialink</b><br>'.
							$this->i18n('profiles_buttons_medialink_description').'<br>'.
							'<br>'.
							'<b>orderedlist</b><br>'.
							$this->i18n('profiles_buttons_orderedlist_description').'<br>'.
							'<br>'.
							'<b>paragraph</b><br>'.
							$this->i18n('profiles_buttons_paragraph_description').'<br>'.
							'<br>'.
							'<b>quote</b><br>'.
							$this->i18n('profiles_buttons_quote_description').'<br>'.
							'<br>'.
							'<b>sub</b><br>'.
							$this->i18n('profiles_buttons_sub_description').'<br>'.
							'<br>'.
							'<b>sup</b><br>'.
							$this->i18n('profiles_buttons_sup_description').'<br>'.
							'<br>'.
							'<b>table</b><br>'.
							$this->i18n('profiles_buttons_table_description').'<br>'.
							'<br>'.
							'<b>underline</b><br>'.
							$this->i18n('profiles_buttons_underline_description').'<br>'.
							'<br>'.
							'<b>unorderedlist</b><br>'.
							$this->i18n('profiles_buttons_unorderedlist_description').'<br>'.
                            '<br>'.
                            '<b>YForm-Tabelle</b><br>'.
                            $this->i18n('markitup_profiles_buttons_yform_description1').'<br>'.
                            $this->i18n('markitup_profiles_buttons_yform_description2').'<br>'.
                            $this->i18n('markitup_profiles_buttons_yform_description3').'<br>'.
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
	}
?>
