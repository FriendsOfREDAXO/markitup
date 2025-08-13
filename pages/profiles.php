<?php

namespace FriendsOfRedaxo\MarkItUp;

use rex;
use rex_addon;
use rex_extension;
use rex_form;
use rex_fragment;
use rex_i18n;
use rex_list;
use rex_sql;

/** @var rex_addon $this */

$func = rex_request('func', 'string');

if ('' === $func) {
    $list = rex_list::factory('SELECT `id`, `name`, `description`, `type`, CONCAT("markitupEditor-",`name`) as `cssclass` FROM `' . rex::getTable('markitup_profiles') . '` ORDER BY `name` ASC');
    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage($this->i18n('profiles_norowsmessage'));

    // icon column
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . $this->i18n('column_hashtag') . ' ' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-action"></i></a>';
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
} elseif ('add' === $func || 'edit' === $func) {
    // Wenn ein Profil erfolgreich gespeichert wurde (add|edit)
    // werden die darauf basierenden Dateien markitup_profiles.[css|js] neu angelegt

    rex_extension::register('REX_FORM_SAVED', static function ($ep) {
        echo Cache::update();
    });
    rex_extension::register('REX_FORM_DELETED', static function ($ep) {
        echo Cache::update();
    });

    $id = rex_request('id', 'int');

    if ('edit' === $func) {
        $formLabel = $this->i18n('profiles_formcaption_edit');
    } elseif ('add' === $func) {
        $formLabel = $this->i18n('profiles_formcaption_add');
    }

    $form = rex_form::factory(rex::getTable('markitup_profiles'), '', 'id=' . $id);

    // Start - add name-field
    $field = $form->addTextField('name');
    $field->setLabel($this->i18n('profiles_label_name'));
    $field->getValidator()->add('notEmpty', $this->i18n('validate_empty', $this->i18n('profiles_label_name')));
    $field->getValidator()->add('custom', $this->i18n('validate_unique', $this->i18n('profiles_label_name')), static function ($value) use ($id) {
        $profiles = rex_sql::factory()
            ->setTable(rex::getTable('markitup_profiles'))
            ->setWhere('name LIKE :name', [':name' => $value])
            ->select('id');
        return 0 === $profiles->getRows() || $id === $profiles->getValue('id');
    });
    // End - add name-field

    // Start - add description-field
    $field = $form->addTextField('description');
    $field->setLabel($this->i18n('profiles_label_description'));
    // End - add description-field

    // Start - add minheight-field with validation
    $field = $form->addTextField('minheight');
    $field->setLabel($this->i18n('profiles_label_minheight'));
    $field->getValidator()->add('notEmpty', $this->i18n('validate_empty', $this->i18n('profiles_label_minheight')));
    $field->getValidator()->add('custom', $this->i18n('profiles_label_minheight_validation'), static function ($value) {
        return is_numeric($value) && $value >= 50 && $value <= 2000;
    });
    // End - add minheight-field

    // Start - add maxheight-field with validation
    $field = $form->addTextField('maxheight');
    $field->setLabel($this->i18n('profiles_label_maxheight'));
    $field->getValidator()->add('notEmpty', $this->i18n('validate_empty', $this->i18n('profiles_label_maxheight')));
    $field->getValidator()->add('custom', $this->i18n('profiles_label_maxheight_validation'), static function ($value) {
        return is_numeric($value) && $value >= 50 && $value <= 2000;
    });
    // End - add maxheight-field

    // Start - add urltype-field
    $field = $form->addSelectField('urltype');
    $field->setLabel($this->i18n('profiles_label_urltype'));

    $select = $field->getSelect();
    $select->setSize(1);
    $select->addOption($this->i18n('profiles_label_urltype_option_relative'), 'relative');
    $select->addOption($this->i18n('profiles_label_urltype_option_absolute'), 'absolute');
    // End - add urltype-field

    // Start - add type-field
    $field = $form->addSelectField('type');
    $field->setLabel($this->i18n('profiles_label_type'));

    $select = $field->getSelect();
    $select->setSize(1);
    $select->addOption('---', 0);
    $select->addOption('Markdown', 'markdown');
    $select->addOption('Textile', 'textile');
    // End - add type-field

    // Start - add markitup_buttons-field
    $field = $form->addTextAreaField('markitup_buttons');
    $field->setLabel($this->i18n('profiles_label_markitupbuttons'));

    $field = $form->addRawField('
			<dl class="rex-form-group form-group">
				<dt>
					&nbsp;
				</dt>
				<dd>
					<div class="rex-docs">
						<details>
							<summary style="cursor: pointer; padding: 8px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
								<strong>📚 ' . $this->i18n('profiles_buttons_help_title') . '</strong>
							</summary>
							<div style="margin-top: 10px; padding: 15px; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;">
								<div class="row">
									<div class="col-md-6">
										<h5>' . $this->i18n('profiles_buttons_basic_title') . '</h5>
										<table class="table table-sm table-bordered">
											<tr><td><code>bold</code></td><td>' . $this->i18n('profiles_buttons_bold_description') . '</td></tr>
											<tr><td><code>italic</code></td><td>' . $this->i18n('profiles_buttons_italic_description') . '</td></tr>
											<tr><td><code>underline</code></td><td>' . $this->i18n('profiles_buttons_underline_description') . '</td></tr>
											<tr><td><code>deleted</code></td><td>' . $this->i18n('profiles_buttons_deleted_description') . '</td></tr>
											<tr><td><code>code</code></td><td>' . $this->i18n('profiles_buttons_code_description') . '</td></tr>
										</table>
									</div>
									<div class="col-md-6">
										<h5>' . $this->i18n('profiles_buttons_links_title') . '</h5>
										<table class="table table-sm table-bordered">
											<tr><td><code>internallink</code></td><td>' . $this->i18n('profiles_buttons_internallink_description') . '</td></tr>
											<tr><td><code>externallink</code></td><td>' . $this->i18n('profiles_buttons_externallink_description') . '</td></tr>
											<tr><td><code>emaillink</code></td><td>' . $this->i18n('profiles_buttons_emaillink_description') . '</td></tr>
											<tr><td><code>media</code></td><td>' . $this->i18n('profiles_buttons_media_description') . '</td></tr>
										</table>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<h5>' . $this->i18n('profiles_buttons_advanced_title') . '</h5>
										<table class="table table-sm table-bordered">
											<tr><td><code>groupheading[1|2|3|4|5|6]</code></td><td>' . $this->i18n('profiles_buttons_groupheading_description') . '</td></tr>
											<tr><td><code>clips[name1=text1|name2=text2]</code></td><td>' . $this->i18n('profiles_buttons_clips_description') . '</td></tr>
											<tr><td><code>table</code></td><td>' . $this->i18n('profiles_buttons_table_description') . '</td></tr>
											<tr><td><code>|</code></td><td>' . $this->i18n('profiles_buttons_separator_description') . '</td></tr>
										</table>
									</div>
								</div>
								<div class="alert alert-info">
									<strong>' . $this->i18n('profiles_buttons_tip_title') . ':</strong>
									' . $this->i18n('profiles_buttons_tip_description') . '
								</div>
							</div>
						</details>
					</div>
				</dd>
			</dl>
		');
    if ('edit' === $func) {
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
