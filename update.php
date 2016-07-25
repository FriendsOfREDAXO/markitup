<?php
	rex_sql_table::get(rex::getTable('markitup_profiles'))
	->ensureColumn(new rex_sql_column('urltype', 'varchar(50)'))
	->ensureColumn(new rex_sql_column('minheight', 'smallint(5)'))
	->ensureColumn(new rex_sql_column('maxheight', 'smallint(5)'))
	->alter();
?>