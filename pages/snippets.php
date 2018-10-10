<?php
	$func = rex_request('func', 'string');

	if ($func == '') {
		$list = rex_list::factory("SELECT `id`, `name`, `lang`, `description` , `content`  FROM `".rex::getTable('markitup_snippets')."` ORDER BY `name`, `lang` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('profiles_norowsmessage'));

		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'" title="'.$this->i18n('column_hashtag').' '.rex_i18n::msg('add').'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

        $list->setColumnLabel('name', $this->i18n('snippets_column_name'));
        $list->setColumnLabel('lang', $this->i18n('snippets_column_lang'));
        $list->setColumnLabel('description', $this->i18n('snippets_column_description'));
        $list->setColumnLabel('content', $this->i18n('snippets_column_content'));

		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);

		$list->removeColumn('id');

		$content = $list->get();

		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');

        // Wenn ein Snippet erfolgreich gespeichert wurde (add|edit)
        // werden die darauf basierenden Dateien markitup_profiles.[css|js] neu angelegt

        rex_extension::register( 'REX_FORM_SAVED', function( $ep ) {
            include_once( $this->getPath('functions/cache_markitup_profiles.php'));
            echo markitup_cache_update( );
        } );
        rex_extension::register( 'REX_FORM_DELETED', function( $ep ) {
            include_once( $this->getPath('functions/cache_markitup_profiles.php'));
            echo markitup_cache_update( );
        } );

		if ($func == 'edit') {
			$formLabel = $this->i18n('snippets_formcaption_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('snippets_formcaption_add');
		}

		$form = rex_form::factory(rex::getTable('markitup_snippets'), '', 'id='.$id);

		//Start - add name-field
			$nfield = $form->addTextField('name');
			$nfield->setLabel($this->i18n('snippets_label_name'));
            $nfield->getValidator()->add( 'notEmpty', $this->i18n('validate_empty',$this->i18n('snippets_label_name')));
        //End - add name-field

        //Start - add lang-field
			$field = $form->addSelectField('lang');
			$field->setLabel($this->i18n('snippets_label_lang'));
            $field->getValidator()->add( 'notEmpty', $this->i18n('validate_empty',$this->i18n('snippets_label_label')));
            $select = $field->getSelect();
            $languages = array_unique(
                array_map(
                    function($l) { return substr($l,0,2); },
                    rex_i18n::getLocales()
                )
            );
            sort( $languages );

            $select->addOption($this->i18n('snippets_label_lang_all'),'--');
            foreach( $languages as $lang ) {
                $select->addOption($lang, $lang);
            }
            $select->setSize(1);
            $field->getValidator()->add( 'custom', $this->i18n('validate_unique',$this->i18n('snippets_label_name').' + '.$this->i18n('snippets_label_lang')), function($value) use ($nfield, $id) {
                $snippets = rex_sql::factory()->getArray( 'SELECT id FROM '.rex::getTable('markitup_snippets').' WHERE name LIKE :name && lang LIKE :lang LIMIT 1', [':name'=>$value,':lang'=>$nfield->getValue()] );
                if( !$snippets ) return true;
                if( $snippets[0]['id'] == $id ) return true;
                return false;
            } );
		//End - add lang-field

		//Start - add content-field
			$field = $form->addTextAreaField('content');
			$field->setLabel($this->i18n('snippets_label_content'));
            $field->getValidator()->add( 'notEmpty', $this->i18n('validate_empty',$this->i18n('snippets_label_content')));
		//End - add content-field

        //Start - add description-field
			$field = $form->addTextAreaField('description');
			$field->setLabel($this->i18n('snippets_label_description'));
		//End - add description-field

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
