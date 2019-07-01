<?php

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

    // Profilbezogenes JS|CSS neu generieren

    include_once( 'functions/cache_markitup_profiles.php');
    echo markitup_cache_update( );
