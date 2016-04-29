<?php
	if (!class_exists('Netcarver\Textile\Parser')) {
		require_once (rex_path::addon('rex_markitup','vendor/php-textile/src/Netcarver/Textile/Parser.php')); //todo: use $this
		require_once (rex_path::addon('rex_markitup','vendor/php-textile/src/Netcarver/Textile/DataBag.php')); //todo: use $this
		require_once (rex_path::addon('rex_markitup','vendor/php-textile/src/Netcarver/Textile/Tag.php')); //todo: use $this
	}
	
	class rex_markitup_textile extends Netcarver\Textile\Parser {
		public function __construct($doctype = 'xhtml') {
			parent::__construct($doctype);
			$this->unrestricted_url_schemes[] = 'redaxo';
			$this->restricted_url_schemes[] = 'redaxo';
		}
	}
?>