<?php
	if (rex::isBackend()) {
		function markitupDefineButtons($type, $profileButtons, $that) {
			//Start - define all buttons
				$buttons = [];
				
				//Start - define button 'bold'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_bold');
					$btn['key'] = 'B';
					$btn['className'] = 'bold';
					$btn['openWith']['textile'] = '*';
					$btn['closeWith']['textile'] = '*';
					$buttons['bold'] = $btn;
				//End - define button 'bold'
				
				//Start - define button 'deleted'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_deleted');
					$btn['key'] = 'S';
					$btn['className'] = 'deleted';
					$btn['openWith']['textile'] = '-';
					$btn['closeWith']['textile'] = '-';
					$buttons['deleted'] = $btn;
				//End - define button 'deleted'
				
				//Start - define button 'formatting'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_formatting');
					$btn['className'] = 'formatting';
					$btn['children'] = [];
					
					for ($i = 1; $i <= 6; $i++) {
						$child = [];
						$child['name'] = $that->i18n('profiles_buttons_formatting_option_h'.$i);
						$child['openWith']['textile'] = 'h'.$i.'(!(([![Class]!]))!). ';
						
						$btn['children']['h'.$i] = $child;
					}
					$btn['children']['p'] = ['name' => $that->i18n('profiles_buttons_formatting_option_p'), 'openWith' => ['textile'=>'p(!(([![Class]!]))!). ']];
					$buttons['formatting'] = $btn;
				//End - define button 'formatting'
				
				//Start - define button 'italic'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_italic');
					$btn['key'] = 'I';
					$btn['className'] = 'italic';
					$btn['openWith']['textile'] = '_';
					$btn['closeWith']['textile'] = '_';
					$buttons['italic'] = $btn;
				//End - define button 'italic'
				
				//Start - define button 'orderedlist'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_orderedlist');
					$btn['className'] = 'orderedlist';
					$btn['openWith']['textile'] = '(!(* |!|*)!)';
					$btn['closeWith']['textile'] = '';
					$buttons['orderedlist'] = $btn;
				//End - define button 'orderedlist'
				
				//Start - define button 'underline'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_underline');
					$btn['key'] = 'U';
					$btn['className'] = 'underline';
					$btn['openWith']['textile'] = '+';
					$btn['closeWith']['textile'] = '+';
					$buttons['underline'] = $btn;
				//End - define button 'underline'
				
				//Start - define button 'unorderedlist'
					$btn = [];
					$btn['name'] = $that->i18n('profiles_buttons_unorderedlist');
					$btn['className'] = 'unorderedlist';
					$btn['openWith']['textile'] = '(!(# |!|#)!)';
					$btn['closeWith']['textile'] = '';
					$buttons['unorderedlist'] = $btn;
				//End - define button 'unorderedlist'
			//End - define all buttons
			
			
			$buttonString = '';
			$profileButtons = explode(',', $profileButtons);
			foreach ($profileButtons as $profileButton) {
				$options = [];
				
				if (preg_match('/(.*)\[(.*)\]/', $profileButton, $matches)) {
					$profileButton = $matches[1];
					$options = explode('|', $matches[2]);
				}
				
				$buttonString .= "{";
				
				foreach (['name', 'key', 'openWith', 'closeWith', 'className'] as $property) {
					if (!empty($buttons[$profileButton][$property])) {
						if (in_array($property, ['openWith', 'closeWith'])) {
							$buttonString .= "  ".$property.":'".$buttons[$profileButton][$property][$type]."',";
						} else {
							$buttonString .= "  ".$property.":'".$buttons[$profileButton][$property]."',";
						}
					}
				}
				
				//Start - dropdown
					if (!empty($options)) {
						$buttonString .= "  dropMenu: [";
						
						foreach ($options as $option) {
							$buttonString .= "{";
								foreach (['name', 'key', 'openWith', 'closeWith'] as $property) {
									if (!empty($buttons[$profileButton]['children'][$option][$property])) {
										if (in_array($property, ['openWith', 'closeWith'])) {
											$buttonString .= "  ".$property.":'".$buttons[$profileButton]['children'][$option][$property][$type]."',";
										} else {
											$buttonString .= "  ".$property.":'".$buttons[$profileButton]['children'][$option][$property]."',";
										}
									}
								}
							$buttonString .= "},";
						}
						
						$buttonString .= "  ]";
					}
				//End - dropdown
				
				$buttonString .= "},";
			}
			
			return $buttonString;
		}
		
		rex_view::addJsFile($this->getAssetsUrl('jquery.markitup.js'));
		rex_view::addCssFile($this->getAssetsUrl('style.css'));
		
		//Start - get markitup-profiles
			$sql = rex_sql::factory();
			$profiles = $sql->setQuery("SELECT `name`, `language`, `type`, `markitup_buttons` FROM `".rex::getTablePrefix()."markitup_profiles` ORDER BY `name` ASC")->getArray();
			unset($sql);
			
			$jsCode = [];
			$jsCode[] = '$(document).on(\'ready pjax:success\',function() {';
			foreach ($profiles as $profile) {
				$jsCode[] = '  $(\'.markitupEditor-'.$profile['name'].'\').markItUp({';
				
				$jsCode[] = '    nameSpace: "markitup_'.$profile['type'].'",';
				switch ($profile['type']) {
					case 'textile':
						$jsCode[] = '    onShiftEnter: {keepDefault:false, replaceWith:\'\n\n\'},';
					break;
				}
				
				$jsCode[] = '    markupSet: [';
				$jsCode[] = '      '.markitupDefineButtons($profile['type'], $profile['markitup_buttons'], $this);
				$jsCode[] = '    ]';
				$jsCode[] = '  });';
				
			}
			$jsCode[] = '});';
			
			if (!rex_file::put(rex_path::addonAssets('rex_markitup', 'cache/markitup_profiles.js').'', implode(PHP_EOL, $jsCode))) {
				echo 'js-file konnte nicht gespeichert werden';
			}
			
			rex_view::addJsFile($this->getAssetsUrl('cache/markitup_profiles.js'));
		//End - get markitup-profiles
		

	}
?>