<?php

namespace FriendsOfRedaxo\MarkItUp;

use Exception;
use PDO;
use Random\RandomException;
use rex;
use rex_i18n;
use rex_markdown;
use rex_sql;
use rex_sql_exception;

use function count;
use function in_array;
use function is_array;

/**
 * @api
 * @package FriendsOfRedaxo\MarkItUp
 */
class Markitup
{
    /** @var ?callable */
    public static $yform_callback = null;

    /** @var array<string,string> */
    public static $type_in_scope = [
        'varchar' => 'Type LIKE \'varchar%\'',
        'text' => 'Type = \'text\'',
        'mediumtext' => 'Type = \'mediumtext\'',
    ];

    /**
     * NOTICE: Die Methode ist vermutlich überflüssig. Sie wird innerhalb des Addons an keiner Stelle aufgerufen.
     */
    public static function insertProfile(string $name, string $description = '', string $type = '', string $minheight = '300', string $maxheight = '800', string $urltype = 'relative', string $markitupButtons = ''): string
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('markitup_profiles'));
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
            $result = Cache::update();
            return '' === $result ? $lastId : $result;
        } catch (rex_sql_exception $e) {
            return $e->getMessage();
        }
    }

    public static function profileExists(string $name): bool
    {
        $profile = rex_sql::factory()
            ->setTable(rex::getTable('markitup_profiles'))
            ->setWhere('name = :name', [':name' => $name])
            ->select('id');
        return 0 < $profile->getRows();
    }

    public static function insertSnippet(string $name, string $lang, string $snippet, string $description = ''): string
    {
        if ('' === $name) {
            return rex_i18n::msg('markitup_validate_empty', rex_i18n::msg('markitup_snippets_label_name'));
        }
        if ('' === $lang) {
            return rex_i18n::msg('markitup_validate_empty', rex_i18n::msg('markitup_snippets_label_lang'));
        }
        if ('' === $snippet) {
            return rex_i18n::msg('markitup_validate_empty', rex_i18n::msg('markitup_snippets_label_content'));
        }

        if (self::snippetExists($name, $lang)) {
            return rex_i18n::msg('markitup_validate_unique', rex_i18n::msg('markitup_snippets_label_name') . ' + ' . rex_i18n::msg('markitup_snippets_label_lang'));
        }
        $languages = array_unique(
            array_map(
                static function ($l) {
                    return substr($l, 0, 2);
                },
                array_merge(rex_i18n::getLocales(), ['--']),
            ),
        );
        if (!in_array($lang, $languages, true)) {
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
            $result = Cache::update();
            return '' === $result ? $lastId : $result;
        } catch (rex_sql_exception $e) {
            return $e->getMessage();
        }
    }

    public static function snippetExists(string $name, string $lang): bool|int
    {
        $snippets = rex_sql::factory()
            ->setTable(rex::getTable('markitup_snippets'))
            ->setWhere('`name` LIKE :name AND `lang` LIKE :lang', [':name' => $name, ':lang' => $lang])
            ->select('id');

        if (0 === $snippets->getRows()) {
            return false;
        }

        return $snippets->getValue('id');
    }

    public static function parseOutput(string $type, string $content): bool|string
    {
        $content = str_replace('<br />', '', $content);

        switch ($type) {
            case 'markdown':
                /**
                 * Der alte Code (bis V3.7) setze auf der eigenen Markdown-Klasse auf.
                 * Da Markdown in längst im REDAXO-Core steht (rex_markdown) und auch
                 * MarkItUp den Core-Vendor nutzt, kann man auch gleich auf rex_markdown gehen.
                 *
                 * TODO: alten Code entfernen
                 */
                $parser = rex_markdown::factory();
                return self::replaceYFormLink($parser->parse($content));
                // $parser = new Markdown();
                // return self::replaceYFormLink($parser->text($content));
                break;
            case 'textile':
                return self::replaceYFormLink(Textile::custom_parse($content));
                break;
        }

        return false;
    }

    public static function replaceYFormLink(string $content): string
    {
        $callback = static function ($link) { return 'javascript:void(0);'; };
        if (rex::isBackend() && null !== rex::getUser()) {
            $callback = is_callable(self::$yform_callback) ? self::$yform_callback : self::createYFormLink(...);
        } elseif (is_callable(self::$yform_callback)) {
            $callback = self::$yform_callback;
        }

        return preg_replace_callback(
            '/yform:(?<table_name>[a-z0-9_]+)\/(?<id>\d+)/',
            $callback,
            $content);
    }

    /**
     * @param string[] $link 
     */
    public static function createYFormLink(array $link): string
    {
        return 'javascript:markitupYformOpen( \'' . random_int(1000000, 999999999) . '\', \'' . $link[1] . '\', \'' . $link[2] . '\' );';
    }

    /**
     * @param array<string>|int|null $tableset
     * @return array<string,int[]>|bool
     */
    public static function yformLinkInUse(string $table_name, int $data_id, array|int|null $tableset = null, bool|int $fullResult = false): array|bool
    {
        $sql = rex_sql::factory();

        // $tableset = null     =>  Alle Tabellen betrachten
        // $tableset = 1        =>  Nur Core-Tabellen article, article_slice und media betrachten
        // $tableset = 2        =>  Nur YForm-Tabellen betrachten
        // $tableset = [...]    =>  Nur die angegebenen Tabellen betrachten
        if (null === $tableset) {
            /** @var list<string> $tables */
            $tables = $sql->getTables();
        } elseif (is_array($tableset)) {
            // void
            /** @var list<string> $tables */
            $tables = $tableset;
        } elseif (1 === $tableset) {
            /** @var list<string> $tables */
            $tables = ['rex_article', 'rex_article_slice', 'rex_media'];
        } elseif (2 === $tableset || 3 === $tableset) {
            try {
                $sql->setTable(rex::getTable('yform_table'));
                $sql->select('id, table_name');
                /** @var list<string> $tables */
                $tables = $sql->getArray(fetchType: PDO::FETCH_KEY_PAIR);
            } catch (Exception $e) {
                /** @var list<string> $tables */
                $tables = []; // insb. falls es rex_yform_fields nicht gibt ($tableset = [frei angegeben])
            }
        } else {
            /** @var list<string> $tables */
            $tables = [];
        }

        // Wenn $fullResult angefordert ist, werden alle Tabellen durchlaufen und je Tabelle mit
        // gefundenem Link die Satznummern zurückgemeldet.
        // Ansonsten wird beim ersten Fund beendet
        $limit = ' LIMIT 1';
        if (true == $fullResult) {
            $limit = '';
        } elseif (is_numeric($fullResult)) {
            $limit = ' LIMIT ' . $fullResult;
            $fullResult = true;
        } else {
            $fullResult = false;
        }

        /**
         * @var array<string,int[]> $result
         */
        $result = [];
        foreach ($tables as $table) {
            $where = self::yformInUseWhere($table, $table_name, $data_id);
            $qry = 'SELECT id FROM ' . $sql->escapeIdentifier($table) . ' WHERE ' . $where . $limit;
            $inUse = $sql->getArray($qry);
            if (0 < count($inUse)) {
                if (!$fullResult) {
                    return true;
                }
                /** @var int[] $idList */
                $idList = array_column($inUse, 'id');
                $result[$table] = $idList;
            }
        }

        if (0 < count($result)) {
            return $result;
        }
        return false;
    }

    /**
     * @param mixed $locator_id
     * @param array<string>|null $fields_in_scope
     * @param array<string>|null $type_in_scope
     */
    public static function yformInUseWhere(string $target_table, string $locator_table, $locator_id, ?array $fields_in_scope = null, ?array $type_in_scope = null): string
    {
        // Alle Felder ermitteln, deren Typen mit varchar(xx), text etc. sind.
        $sql = rex_sql::factory();
        try {
            $condition = [];
            
            if (null === $type_in_scope) {
                $type_in_scope = self::$type_in_scope;
            }
            if( 0 < count($type_in_scope)) {
                $condition[] = implode(' OR ', $type_in_scope);
            }

            if (is_array($fields_in_scope)) {
                $condition[] = 'Field IN (\'' . implode('\',\'', $fields_in_scope) . '\')';
            }
            
            if (0 === count($condition)) {
                return '';
            }
            $where = '(' . implode(') AND (', $condition) . ')';
            $qry = 'SHOW FIELDS FROM ' . $sql->escapeIdentifier($target_table) . ' WHERE ' . $where;
            $fields = array_column($sql->getArray($qry), 'Field');
        } catch (Exception $e) {
            return ''; // Falls es die Tabelle nicht gibt;
        }

        // Je Feld die Abfrage aufbauen, ob der Link dort vorkommt.
        foreach ($fields as &$field) {
            $field = 'LOCATE(\'yform:' . $locator_table . '/' . $locator_id . '\',' . $sql->escapeIdentifier($field) . ')';
        }
        return implode(' OR ', $fields);
    }
}
