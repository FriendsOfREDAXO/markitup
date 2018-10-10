<?php

rex_sql_table::get(rex::getTable('markitup_profiles'))
    ->drop();

rex_sql_table::get(rex::getTable('markitup_snippets'))
    ->drop();
