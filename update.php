<?php

namespace FriendsOfRedaxo\MarkItUp;

use rex;
use rex_dir;
use rex_path;
use rex_sql_column;
use rex_sql_table;

// Datenbank auf den aktuellen Stand bringen

rex_sql_table::get(rex::getTable('markitup_profiles'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(30)', false, ''))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', false, ''))
    ->ensureColumn(new rex_sql_column('urltype', 'varchar(50)'))
    ->ensureColumn(new rex_sql_column('minheight', 'smallint(5) unsigned'))
    ->ensureColumn(new rex_sql_column('maxheight', 'smallint(5) unsigned'))
    ->ensureColumn(new rex_sql_column('type', 'varchar(50)'))
    ->ensureColumn(new rex_sql_column('markitup_buttons', 'text'))
    ->ensure();

// Datenbank-Tabelle für Snippets anlegen / anpassen

rex_sql_table::get(rex::getTable('markitup_snippets'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(30)', false, ''))
    ->ensureColumn(new rex_sql_column('lang', 'varchar(30)', false, ''))
    ->ensureColumn(new rex_sql_column('description', 'text', false, ''))
    ->ensureColumn(new rex_sql_column('content', 'text'))
    ->ensure();

// das ggf. noch vorhandene alte Plugin-Verzeichnis löschen
rex_dir::delete(rex_path::plugin('markitup', 'documentation'));

// Profilbezogenes JS|CSS neu generieren
include __DIR__ . '/lib/Cache.php';
echo Cache::update();
