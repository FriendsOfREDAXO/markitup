<?php
	echo rex_view::info($this->i18n('help_infotext'));
	
	$code = '';
	$code .= '<fieldset class="form-horizontal">'.PHP_EOL;
	$code .= '  <div class="form-group">'.PHP_EOL;
	$code .= '    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>'.PHP_EOL;
	$code .= '    <div class="col-sm-10">'.PHP_EOL;
	$code .= '      <textarea class="form-control markitupEditor-markdown_full" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>'.PHP_EOL;
	$code .= '    </div>'.PHP_EOL;
	$code .= '  </div>'.PHP_EOL;
	$code .= '</fieldset>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('collapse', true, false);
	$fragment->setVar('collapsed', true, false);
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Input (Markdown)', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	$code = '';
	$code .= '<?php'.PHP_EOL;
	$code .= '  echo markitup::parseOutput (\'markdown\', \'REX_VALUE[id=1 output="html"]\');'.PHP_EOL;
	$code .= '?>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('collapse', true, false);
	$fragment->setVar('collapsed', true, false);
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Output (Markdown)', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = '';
	$code .= '<fieldset class="form-horizontal">'.PHP_EOL;
	$code .= '  <div class="form-group">'.PHP_EOL;
	$code .= '    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>'.PHP_EOL;
	$code .= '    <div class="col-sm-10">'.PHP_EOL;
	$code .= '      <textarea class="form-control markitupEditor-textile_full" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>'.PHP_EOL;
	$code .= '    </div>'.PHP_EOL;
	$code .= '  </div>'.PHP_EOL;
	$code .= '</fieldset>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('collapse', true, false);
	$fragment->setVar('collapsed', true, false);
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Input (Textile)', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = '';
	$code .= '<?php'.PHP_EOL;
	$code .= '  echo markitup::parseOutput (\'textile\', \'REX_VALUE[id=1 output="html"]\');'.PHP_EOL;
	$code .= '?>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('collapse', true, false);
	$fragment->setVar('collapsed', true, false);
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Output (Textile)', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = '';
	$code .= '<?php'.PHP_EOL;
	$code .= '  if (!markitup::profileExists(\'simple\')) {'.PHP_EOL;
	$code .= '    //Name, Beschreibung, Typ (markdown oder textile), Mindesthöhe, Maximalhöhe, URL-Art (relativ oder absolut), Buttons'.PHP_EOL;
	$code .= '    markitup::insertProfile (\'simple\', \'Lorem Ipsum\', \'textile\', 300, 800, \'relative\', \'bold,italic\');'.PHP_EOL;
	$code .= '  }'.PHP_EOL;
	$code .= '?>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('collapse', true, false);
	$fragment->setVar('collapsed', true, false);
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Via Modul oder AddOn ein Profil anlegen', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
?>