<?php

namespace FriendsOfRedaxo\MarkItUp;

use Exception;
use PDO;
use rex;
use rex_i18n;
use rex_markdown;
use rex_sql;
use rex_sql_exception;

use function count;
use function in_array;
use function is_array;
use function is_callable;

/**
 * @api
 * @package FriendsOfRedaxo\MarkItUp
 */
class Markitup
{
    /** @var callable */
    public static $yform_callback;

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
        // TODO: SQL verbessern
        $sql->setTable(rex::getTablePrefix() . 'markitup_profiles');
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
        $sql = rex_sql::factory();
        // TODO: SQL verbessern
        $profile = $sql->setQuery('SELECT `name` FROM `' . rex::getTable('markitup_profiles') . '` WHERE `name` = ' . $sql->escape($name) . '')->getArray();
        unset($sql);

        if (!empty($profile)) {
            return true;
        }
        return false;
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
            $result = Cache::update();
            return '' === $result ? $lastId : $result;
        } catch (rex_sql_exception $e) {
            return $e->getMessage();
        }
    }

    public static function snippetExists(string $name, string $lang): bool|int
    {
        $sql = rex_sql::factory();
        // TODO: SQL verbessern
        $snippet = $sql->getArray('SELECT `id` FROM `' . rex::getTable('markitup_snippets') . '` WHERE `name` LIKE :name AND `lang` LIKE :lang', [':name' => $name, ':lang' => $lang]);
        unset($sql);

        if (0 < count($snippet)) {
            return $snippet[0]['id'];
        }
        return false;
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
            // TODO: umstellen auf moderne Schreibweise
            $callback = is_callable(self::$yform_callback) ? self::$yform_callback : (__CLASS__ . '::createYFormLink');
        } elseif (self::$yform_callback) {
            $callback = self::$yform_callback;
        }
        return preg_replace_callback(
            '/yform:(?<table_name>[a-z0-9_]+)\/(?<id>\d+)/',
            $callback,
            $content);
    }

    public static function createYFormLink(string $link): string
    {
        return 'javascript:markitupYformOpen( \'' . random_int(1000000, 999999999) . '\', \'' . $link[1] . '\', \'' . $link[2] . '\' );';
    }

    /**
     * @param array<string>|int|null $tableset
     * @ param bool $fullResult
     * @return array<int>|bool
     */
    public static function yformLinkInUse(string $table_name, int $data_id, array|int|null $tableset = null, bool|int $fullResult = false): array|bool
    {
        $sql = rex_sql::factory();

        // $tableset = null     =>  Alle Tabellen betrachten
        // $tableset = 1        =>  Nur Core-Tabellen article, article_slice und media betrachten
        // $tableset = 2        =>  Nur YForm-Tabellen betrachten
        // $tableset = [...]    =>  Nur die angegebenen Tabellen betrachten
        if (null === $tableset) {
            $tableset = $sql->getTables();
        } elseif (is_array($tableset)) {
            // void
        } elseif (1 === $tableset) {
            $tableset = ['rex_article', 'rex_article_slice', 'rex_media'];
        } elseif (2 === $tableset || 3 === $tableset) {
            // FIXME: Murks ist das! tableset=3 kommt nie zur Wirkung. Rauswerfen
            try {
                // TODO: SQL verbessern
                $tableset = $sql->getArray('SELECT id, table_name FROM rex_yform_table', [], PDO::FETCH_KEY_PAIR);
            } catch (Exception $e) {
                $tableset = []; // insb. falls es rex_yform_fields nicht gibt ($tableset = [frei angegeben])
            }
            if (3 === $tableset) {
                $tableset[] = 'rex_article';
                $tableset[] = 'rex_article_slice';
                $tableset[] = 'rex_media';
            }
        } else {
            $tableset = [];
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

        $result = [];
        foreach ($tableset as $table) {
            $where = self::yformInUseWhere($table, $table_name, $data_id);
            // TODO: SQL verbessern
            $qry = 'SELECT id FROM ' . $sql->escapeIdentifier($table) . ' WHERE ' . $where . $limit;
            $inUse = $sql->getArray($qry);
            if (0 < count($inUse)) {
                if (!$fullResult) {
                    return true;
                }
                $result[$table] = array_column($inUse, 'id');
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
            if (is_array($type_in_scope)) {
                $condition[] = implode(' OR ', $type_in_scope);
            }
            if (is_array($fields_in_scope)) {
                $condition[] = 'Field IN (\'' . implode('\',\'', $fields_in_scope) . '\')';
            }
            if (0 === count($condition)) {
                return '';
            }
            // TODO: SQL verbessern
            $qry = 'SHOW FIELDS FROM ' . $sql->escapeIdentifier($target_table) . ' WHERE (' . implode(') AND (', $condition) . ')';
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
