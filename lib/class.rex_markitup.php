<?php
	require_once (rex_path::addon('rex_markitup','lib/class.rex_markitup_markdown.php')); //todo: use $this
	require_once (rex_path::addon('rex_markitup','lib/class.rex_markitup_textile.php')); //todo: use $this
	
	class rex_markitup {
		
		public static function insertProfile ($name, $description = '', $type = '', $markitupButtons = '') {
			$sql = rex_sql::factory();
			$sql->setTable(rex::getTablePrefix().'markitup_profiles');
			$sql->setValue('name', $name);
			$sql->setValue('description', $description);
			$sql->setValue('type', $type);
			$sql->setValue('markitup_buttons', $markitupButtons);
			
			try {
				$sql->insert();
				return $sql->getLastId();
			} catch (rex_sql_exception $e) {
				return $e->getMessage();
			}
		}
		
		public static function profileExists ($name) {
			$sql = rex_sql::factory();
			$profile = $sql->setQuery("SELECT `name` FROM `".rex::getTablePrefix()."markitup_profiles` WHERE `name` = ".$sql->escape($name)."")->getArray();
			unset($sql);
			
			if (!empty($profile)) {
				return true;
			} else {
				return false;
			}
		}
		
		public static function parseOutput ($type, $content) {
			$content = str_replace('<br />', '', $content);
			
			switch ($type) {
				case 'markdown':
					$parser = new rex_markitup_markdown();
					return $parser->text($content);
				break;
				case 'textile':
					$parser = new rex_markitup_textile();
					return $parser->parse($content);
				break;
			}
			
			return false;
		}
		
		public static function defineButtons($type, $profileButtons, $that) {
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
	}
?>