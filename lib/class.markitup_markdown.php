<?php
    if (!class_exists('Parsedown')) {
        require_once(rex_path::addon('markitup', 'vendor/parsedown/Parsedown.php')); //todo: use $this
    }
    
    class markitup_markdown extends Parsedown
    {
    }
