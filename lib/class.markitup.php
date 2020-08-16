<?php
    class markitup
    {

        static $yform_callback = null;

        public static function insertProfile($name, $description = '', $type = '', $minheight = '300', $maxheight = '800', $urltype = 'relative', $markitupButtons = '')
        {
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
                include_once(rex_path::addon('markitup', 'functions/cache_markitup_profiles.php'));
                $result = markitup_cache_update();
                return $result ? $result : $lastId;
            } catch (rex_sql_exception $e) {
                return $e->getMessage();
            }
        }

        public static function profileExists($name)
        {
            $sql = rex_sql::factory();
            $profile = $sql->setQuery("SELECT `name` FROM `".rex::getTable('markitup_profiles')."` WHERE `name` = ".$sql->escape($name)."")->getArray();
            unset($sql);

            if (!empty($profile)) {
                return true;
            } else {
                return false;
            }
        }

        public static function insertSnippet($name, $lang, $snippet, $description = '')
        {
            if (!$name) {
                return rex_i18n::msg('markitup_validate_empty', rex_i18n::msg('markitup_snippets_label_name'));
            }
            if (!$lang) {
                return rex_i18n::msg('markitup_validate_empty', rex_i18n::msg('markitup_snippets_label_lang'));
            }
            if (!$snippet) {
                return rex_i18n::msg('markitup_validate_empty', rex_i18n::msg('markitup_snippets_label_content'));
            }

            if (self::snippetExists($name, $lang)) {
                return rex_i18n::msg('markitup_validate_unique', rex_i18n::msg('markitup_snippets_label_name').' + '.rex_i18n::msg('markitup_snippets_label_lang'));
            }
            $languages = array_unique(
                array_map(
                    function ($l) {
                        return substr($l, 0, 2);
                    },
                    array_merge(rex_i18n::getLocales(), ['--'])
                )
            );
            if (!in_array($lang, $languages)) {
                return rex_i18n::msg('markitup_validate_invalid', rex_i18n::msg('markitup_snippets_label_lang'));
            }

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
                include_once(rex_path::addon('markitup', 'functions/cache_markitup_profiles.php'));
                $result = markitup_cache_update();
                return $result ? $result : $lastId;
            } catch (rex_sql_exception $e) {
                return $e->getMessage();
            }
        }

        public static function snippetExists($name, $lang)
        {
            $sql = rex_sql::factory();
            $snippet = $sql->getArray('SELECT `id` FROM `'.rex::getTable('markitup_snippets').'` WHERE `name` LIKE :name AND `lang` LIKE :lang', [':name'=>$name,':lang'=>$lang]);
            unset($sql);

            if ($snippet) {
                return $snippet[0]['id'];
            } else {
                return false;
            }
        }

        public static function parseOutput($type, $content)
        {
            $content = str_replace('<br />', '', $content);

            switch ($type) {
                case 'markdown':
                    require_once(rex_path::addon('markitup', 'lib/class.markitup_markdown.php')); //todo: use $this
                    $parser = new markitup_markdown();
                    return self::replaceYFormLink( $parser->text($content) );
                break;
                case 'textile':
                    require_once(rex_path::addon('markitup', 'lib/class.markitup_textile.php')); //todo: use $this
                    $parser = new markitup_textile();
                    return self::replaceYFormLink( $parser->custom_parse($content) );
                break;
            }

            return false;
        }

        public static function replaceYFormLink( $content )
        {
            $callback = function( $link ) { return 'javascript:void(0);'; };
            if( rex::isBackend() && rex::getUser() )
            {
                if( rex::getUser() )
                {
                    $callback = self::$yform_callback ?: 'self::createYFormLink';
                }
            }
            elseif( self::$yform_callback )
            {
                $callback = self::$yform_callback;
            }
            return preg_replace_callback (
                '/yform:(?<table_name>[a-z0-9_]+)\/(?<id>\d+)/' ,
                $callback,
                $content );
        }

        public static function createYFormLink( $link )
        {
            return 'javascript:markitupYformOpen( \''.mt_rand(1000000,999999999).'\', \''.$link[1].'\', \''.$link[2].'\' );';
        }

        public static function yformLinkInUse( $table_name, $data_id, $tableset=null, $fullResult=false )
        {
            $sql = rex_sql::factory();

            // $tableset = null     =>  Alle Tabellen betrachten
            // $tableset = 1        =>  Nur Core-Tabellen article, article_slice und media betrachten
            // $tableset = 2        =>  Nur YForm-Tabellen betrachten
            // $tableset = [...]    =>  Nur die angegebenen Tabellen betrachten
            if( null === $tableset )
            {
                $tableset = $sql->getTables();
            }
            elseif( is_array($tableset) )
            {
                // void
            }
            elseif( 1 === $tableset )
            {
                $tableset = ['rex_article','rex_article_slice','rex_media'];
            }
            elseif( 2 === $tableset || 3 == $tableset )
            {
                try {
                    $tableset = $sql->getArray('SELECT id, table_name FROM rex_yform_fields',[],PDO::FETCH_KEY_PAIR);
                } catch (\Exception $e) {
                    $tableset = []; // insb. falls es rex_yform_fields nicht gibt ($tableset = [frei angegeben])
                }
                if( 3 == $tableset )
                {
                    $tableset[] = 'rex_article';
                    $tableset[] = 'rex_article_slice';
                    $tableset[] = 'rex_media';
                }
            }
            else {
                $tableset = [];
            }

            // Wenn $fullResult angefordert ist, werden alle Tabellen durchlaufen und je Tabelle mit
            // gefundenem Link die Satznummern zurückgemeldet.
            // Ansonsten wird beim ersten Fund beendet
            $limit = true !== $fullResult ? 'LIMIT 1' : '';
            $result = [];

            foreach( $tableset as $table ){

                // Alle Felder ermitteln, deren Typen mit varchar(xx) oder text sind.
                try {
                    $qry = 'SHOW FIELDS FROM '.$sql->escapeIdentifier($table).' WHERE Type LIKE \'varchar%\' OR Type = \'text\'';
                    $fields = array_column( $sql->getArray( $qry ), 'Field');
                    if( !$fields ) continue;
                } catch (\Exception $e) {
                    continue; // Falls es die Tabelle nicht gibt; relevant für
                }

                // Je Feld die Abfrage aufbauen, ob der Link dort vorkommt.
                foreach( $fields as &$field )
                {
                    $field = 'LOCATE(\'yform:'.$table_name.'/'.$data_id.'\','.$sql->escapeIdentifier($field).')';
                }
                $qry = 'SELECT id FROM '.$sql->escapeIdentifier($table).' WHERE '.implode(' OR ',$fields ).$limit;
                if( $inUse = $sql->getArray( $qry ) )
                {
                    if( $limit ) return true;
                    $result[$table] = array_column( $inUse, 'id' );
                }
            }

            if( $result ) return $result;
            return false;
        }

    }
