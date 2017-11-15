<?php
	require_once (rex_path::addon('markitup','lib/class.markitup_markdown.php')); //todo: use $this
	require_once (rex_path::addon('markitup','lib/class.markitup_textile.php')); //todo: use $this
	
	class markitup {
		
		public static function insertProfile ($name, $description = '', $type = '', $minheight = '300', $maxheight = '800', $urltype = 'relative', $markitupButtons = '') {
			$sql = rex_sql::factory();
			$sql->setTable(rex::getTablePrefix().'markitup_profiles');
			$sql->setValue('name', $name);
			$sql->setValue('description', $description);
			$sql->setValue('type', $type);
			$sql->setValue('minheight', $minheight);
			$sql->setValue('maxheight', $maxheight);
			$sql->setValue('urltype', $urltype);
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
					$parser = new markitup_markdown();
					return $parser->text($content);
				break;
				case 'textile':
					$parser = new markitup_textile();
					return $parser->custom_parse($content);
				break;
			}
			
			return false;
		}
		
		public static function defineButtons($type, $profileButtons, $that) {
			static $markItUpButtons = null;

			if (!$markItUpButtons) {
				$markItUpButtons = rex_file::getConfig(rex_path::addon('markitup', 'config.yml'));
			}
			
			$buttonString = '';
			$profileButtons = explode(',', $profileButtons);
			foreach ($profileButtons as $profileButton) {
				$profileButton = trim($profileButton);
				$options = [];
				
				if ($profileButton == '|') {
					$buttonString .= '{separator:\'&nbsp;\'},';
					continue;
				}
				
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
