<?php
	echo rex_view::info($this->i18n('tutorial_infotext'));
	
	$moduleInput = '';
	$moduleInput .= '<fieldset class="form-horizontal">'.PHP_EOL;
	$moduleInput .= '  <div class="form-group">'.PHP_EOL;
	$moduleInput .= '    <label class="col-sm-2 control-label" for="markitup_1">VALUE 1</label>'.PHP_EOL;
	$moduleInput .= '    <div class="col-sm-10">'.PHP_EOL;
	$moduleInput .= '      <textarea cols="1" rows="6" class="form-control markitupEditor-full" id="markitup_1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>'.PHP_EOL;
	$moduleInput .= '    </div>'.PHP_EOL;
	$moduleInput .= '  </div>'.PHP_EOL;
	$moduleInput .= '</fieldset>'.PHP_EOL;
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Input', false); //todo
	$fragment->setVar('body', highlight_string($moduleInput, true), false);
	echo $fragment->parse('core/page/section.php');
?>