<?php
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
                    require_once (rex_path::addon('markitup','lib/class.markitup_markdown.php')); //todo: use $this
					$parser = new markitup_markdown();
					return $parser->text($content);
				break;
				case 'textile':
            	    require_once (rex_path::addon('markitup','lib/class.markitup_textile.php')); //todo: use $this
					$parser = new markitup_textile();
					return $parser->custom_parse($content);
				break;
			}

			return false;
		}
	}