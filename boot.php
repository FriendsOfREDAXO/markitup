<?php
	if (rex::isBackend()) {
		rex_view::addJsFile($this->getAssetsUrl('jquery.markitup.js'));
		rex_view::addJsFile($this->getAssetsUrl('autosize.min.js'));
		rex_view::addJsFile($this->getAssetsUrl('scripts.js'));
		rex_view::addCssFile($this->getAssetsUrl('style.css'));
		if (file_exists($this->getAssetsPath('skin.css'))) {
			rex_view::addCssFile($this->getAssetsUrl('skin.css'));
		}
		
		//Start - get markitup-profiles
			$sql = rex_sql::factory();
			$profiles = $sql->setQuery("SELECT `name`, `type`, `minheight`, `maxheight`, `markitup_buttons` FROM `".rex::getTablePrefix()."markitup_profiles` ORDER BY `name` ASC")->getArray();
			unset($sql);
			
			$cssCode = [];
			$jsCode = [];
			
			$jsCode[] = 'function markitupInit() {';
			foreach ($profiles as $profile) {
				$cssCode[] = '  textarea.markitupEditor-'.$profile['name'].' { min-height: '.$profile['minheight'].'px; max-height: '.$profile['maxheight'].'px; }';
				$jsCode[] = '  $(\'.markitupEditor-'.$profile['name'].'\').not(\'.markitupActive\').markItUp({';
				
				$jsCode[] = '    nameSpace: "markitup_'.$profile['type'].'",';
				switch ($profile['type']) {
					case 'textile':
						$jsCode[] = '    onShiftEnter: {keepDefault:false, replaceWith:\'\n\n\'},';
					break;
				}
				
				$jsCode[] = '    markupSet: [';
				$jsCode[] = '      '.markitup::defineButtons($profile['type'], $profile['markitup_buttons'], $this);
				$jsCode[] = '    ]';
				$jsCode[] = '  }).addClass(\'markitupActive\');';
			}
			$jsCode[] = '}';
			
			$jsCode[] = '$(document).on(\'ready pjax:success\',function() {';
			$jsCode[] = '  markitupInit();';
			$jsCode[] = '  autosize($("textarea[class*=\'markitupEditor-\']"));';
			$jsCode[] = '});';
			
			if (!rex_file::put(rex_path::addonAssets('markitup', 'cache/markitup_profiles.css').'', implode(PHP_EOL, $cssCode))) {
				echo 'css-file konnte nicht gespeichert werden';
			}
			
			if (!rex_file::put(rex_path::addonAssets('markitup', 'cache/markitup_profiles.js').'', implode(PHP_EOL, $jsCode))) {
				echo 'js-file konnte nicht gespeichert werden';
			}
			
			rex_view::addCssFile($this->getAssetsUrl('cache/markitup_profiles.css'));
			rex_view::addJsFile($this->getAssetsUrl('cache/markitup_profiles.js'));
		//End - get markitup-profiles
	}
?>