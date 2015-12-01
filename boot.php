<?php
	if (rex::isBackend()) {
		function markitupDefineButtons ($type, $profileButtons, $that) {
			$markItUpButtons = rex_file::getConfig(rex_path::addon('rex_markitup', 'config.yml'));
			
			$buttonString = '';
			$profileButtons = explode(',', $profileButtons);
			foreach ($profileButtons as $profileButton) {
				$options = [];
				
				if (preg_match('/(.*)\[(.*)\]/', $profileButton, $matches)) {
					$profileButton = $matches[1];
					
					//Start - explode parameters
						$parameters = explode('|', $matches[2]);
						$parameterString = '';
						foreach ($parameters as $parameter) {
							if (strpos($parameter, '=') !== false) {
								list($key, $value) = explode('=',$parameter);
								$options[] = ['name' => addslashes($key), 'openWith' => addslashes($value)];
							} else {
								$options[] = $parameter;
							}
						}
						
					//End - explode parameters
				}
				
				$buttonString .= "{";
				
				foreach (['name', 'key', 'openWith', 'closeWith', 'className', 'replaceWith'] as $property) {
					if (!empty($markItUpButtons[$profileButton][$property])) {
						if (in_array($property, ['openWith', 'closeWith'])) {
							$buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton][$property][$type]."',".PHP_EOL;
						} else if ($property == 'replaceWith') {
							$buttonString .= "  ".$property.":".$markItUpButtons[$profileButton][$property][$type].",".PHP_EOL;
						} else if ($property == 'name') {
							$buttonString .= "  ".$property.":'".$that->i18n($markItUpButtons[$profileButton][$property])."',".PHP_EOL;
						} else {
							$buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton][$property]."',".PHP_EOL;
						}
					}
				}
				
				//Start - dropdown
					if (!empty($options)) {
						$buttonString .= "  dropMenu: [";
						
						foreach ($options as $option) {
							$buttonString .= "{";
							
							if (is_array($option)) {
								foreach ($option as $property => $value) {
									$buttonString .= "  ".$property.":'".$value."',";
								}
							} else {
								foreach (['name', 'key', 'openWith', 'closeWith', 'replaceWith'] as $property) {
									if (!empty($markItUpButtons[$profileButton]['children'][$option][$property])) {
										if (in_array($property, ['openWith', 'closeWith'])) {
											$buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton]['children'][$option][$property][$type]."',".PHP_EOL;
										} else {
											if ($property == 'name') {
												$buttonString .= "  ".$property.":'".$that->i18n($markItUpButtons[$profileButton]['children'][$option][$property])."',".PHP_EOL;
											} else if ($property == 'replaceWith') {
												$buttonString .= "  ".$property.":".$markItUpButtons[$profileButton]['children'][$option][$property][$type].",".PHP_EOL;
											} else {
												$buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton]['children'][$option][$property]."',".PHP_EOL;
											}
										}
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
		
		/////////////////////////////////////////////
		
		rex_view::addJsFile($this->getAssetsUrl('jquery.markitup.js'));
		rex_view::addJsFile($this->getAssetsUrl('scripts.js'));
		rex_view::addCssFile($this->getAssetsUrl('style.css'));
		
		//Start - get markitup-profiles
			$sql = rex_sql::factory();
			$profiles = $sql->setQuery("SELECT `name`, `type`, `markitup_buttons` FROM `".rex::getTablePrefix()."markitup_profiles` ORDER BY `name` ASC")->getArray();
			unset($sql);
			
			$jsCode = [];
			
			$jsCode[] = 'function markitupInit() {';
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
			$jsCode[] = '}';
			
			$jsCode[] = '$(document).on(\'ready pjax:success\',function() {';
			$jsCode[] = '  markitupInit();';
			$jsCode[] = '});';
			
			if (!rex_file::put(rex_path::addonAssets('rex_markitup', 'cache/markitup_profiles.js').'', implode(PHP_EOL, $jsCode))) {
				echo 'js-file konnte nicht gespeichert werden';
			}
			
			rex_view::addJsFile($this->getAssetsUrl('cache/markitup_profiles.js'));
		//End - get markitup-profiles
		
		//Start - use OUTPUT_FILTER-EP to use an custom callback
			rex_extension::register('OUTPUT_FILTER', function($param) {
				$page = rex_request('page', 'string');
				$opener_input_field = rex_request('opener_input_field', 'string');
				
				$content = $param->getSubject();
				
				if (substr($opener_input_field, 0, 9) == 'markitup_') {
					switch ($page) {
						case 'mediapool/media':
							$content = preg_replace("|javascript:selectMedia\(\'(.*)\', \'(.*)\'\);|", "javascript:btnImageCallbackInsert('".$opener_input_field."','$1','$2');self.close();", $content);
						break;
						case 'linkmap':
							$content = preg_replace("|javascript:insertLink\(\'(.*)\',\'(.*)\'\);|",  "javascript:btnLinkInternalCallbackInsert('".$opener_input_field."','$1','$2');self.close();", $content);
						break;
					}
				}
				
				return $content;
			});
		//End - use OUTPUT_FILTER-EP to use an custom callback
	}
?>