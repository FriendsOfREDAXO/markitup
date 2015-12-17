<?php
	echo rex_view::info($this->i18n('tutorial_infotext'));
	
	$code = '';
	$code .= '<fieldset class="form-horizontal">'.PHP_EOL;
	$code .= '  <div class="form-group">'.PHP_EOL;
	$code .= '    <label class="col-sm-2 control-label" for="markitup_1">VALUE 1</label>'.PHP_EOL;
	$code .= '    <div class="col-sm-10">'.PHP_EOL;
	$code .= '      <textarea class="form-control markitupEditor-full" id="markitup_1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>'.PHP_EOL;
	$code .= '    </div>'.PHP_EOL;
	$code .= '  </div>'.PHP_EOL;
	$code .= '</fieldset>'.PHP_EOL;
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Input', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = '';
	$code .= '<?php'.PHP_EOL;
	$code .= '  if (!rex_markitup::profileExists(\'simple\')) {'.PHP_EOL;
	$code .= '    rex_markitup::insertProfile(\'simple\', \'Lorem Ipsum\', \'textile\', \'bold,italic\');'.PHP_EOL;
	$code .= '  }'.PHP_EOL;
	$code .= '?>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Via Modul oder AddOn ein Profil anlegen', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
?>