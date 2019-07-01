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
                $lastId = $sql->getLastId();
                // Profilbezogenes JS/CSS generieren
                include_once( rex_path::addon('markitup', 'functions/cache_markitup_profiles.php') );
                $result = markitup_cache_update( );
				return $result ? $result : $lastId;
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

        public static function insertSnippet ($name, $lang, $snippet, $description = '') {

            if( !$name ) return rex_i18n::msg('markitup_validate_empty',rex_i18n::msg('markitup_snippets_label_name'));
            if( !$lang ) return rex_i18n::msg('markitup_validate_empty',rex_i18n::msg('markitup_snippets_label_lang'));
            if( !$snippet ) return rex_i18n::msg('markitup_validate_empty',rex_i18n::msg('markitup_snippets_label_content'));

            if( self::snippetExists ($name, $lang) ) return rex_i18n::msg('markitup_validate_unique',rex_i18n::msg('markitup_snippets_label_name').' + '.rex_i18n::msg('markitup_snippets_label_lang'));
            $languages = array_unique(
                array_map(
                    function($l) { return substr($l,0,2); },
                    array_merge(rex_i18n::getLocales(),['--'])
                )
            );
            if( !in_array($lang,$languages)) return rex_i18n::msg('markitup_validate_invalid',rex_i18n::msg('markitup_snippets_label_lang'));

			$sql = rex_sql::factory();
			$sql->setTable(rex::getTable('markitup_snippets'));
			$sql->setValue('name', $name);
			$sql->setValue('description', $description);
			$sql->setValue('lang', $lang);
			$sql->setValue('content', $snippet);

			try {
				$sql->insert();
                $lastId = $sql->getLastId();
                // Profilbezogenes JS/CSS generieren
                include_once( rex_path::addon('markitup', 'functions/cache_markitup_profiles.php') );
                $result = markitup_cache_update( );
				return $result ? $result : $lastId;
			} catch (rex_sql_exception $e) {
				return $e->getMessage();
			}
		}

        public static function snippetExists ($name, $lang) {
			$sql = rex_sql::factory();
			$snippet = $sql->getArray('SELECT `id` FROM `'.rex::getTable('markitup_snippets').'` WHERE `name` LIKE :name AND `lang` LIKE :lang' ,[':name'=>$name,':lang'=>$lang]);
            unset($sql);

			if ( $snippet ) {
				return $snippet[0]['id'];
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
