<?php

// Datenbank-Tabelle für Profile anlegen / anpassen

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

// Erstbetankung mit den Musterprofilen

$tableName = rex::getTable( 'markitup_profiles' );
$sql = rex_sql::factory();

$sql->setTable( $tableName );
$sql->setWhere ( 'id=1 OR id=2' );
$sql->delete();

$sql->setTable( $tableName );
$sql->setValue( 'id', 1 );
$sql->setValue( 'name', 'textile_full' );
$sql->setValue( 'description', 'Textile default configuration' );
$sql->setValue( 'urltype', 'relative' );
$sql->setValue( 'minheight', '300' );
$sql->setValue( 'maxheight', '800' );
$sql->setValue( 'type', 'textile' );
$sql->setValue( 'markitup_buttons', 'bold,code,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],deleted,emaillink,externallink,groupheading[1|2|3|4|5|6],grouplink[file|internal|external|mailto],heading1,heading2,heading3,heading4,heading5,heading6,internallink,italic,media,medialink,orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist' );
try {
    $sql->insert();
} catch (Exception $e) {
}

$sql->setTable( $tableName );
$sql->setValue( 'id', 2 );
$sql->setValue( 'name', 'markdown_full' );
$sql->setValue( 'description', 'Markdown default configuration' );
$sql->setValue( 'urltype', 'relative' );
$sql->setValue( 'minheight', '300' );
$sql->setValue( 'maxheight', '800' );
$sql->setValue( 'type', 'markdown' );
$sql->setValue( 'markitup_buttons', 'bold,code,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],deleted,emaillink,externallink,groupheading[1|2|3|4|5|6],grouplink[file|internal|external|mailto],heading1,heading2,heading3,heading4,heading5,heading6,internallink,italic,media,medialink,orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist' );
try {
    $sql->insert();
} catch (Exception $e) {
}

// Profilbezogenes JS/CSS generieren

include_once( 'functions/cache_markitup_profiles.php');
echo markitup_cache_update( );
