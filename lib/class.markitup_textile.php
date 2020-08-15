<?php
    if (!class_exists('Netcarver\Textile\Parser')) {
        require_once(rex_path::addon('markitup', 'vendor/php-textile/src/Netcarver/Textile/Parser.php')); //todo: use $this
        require_once(rex_path::addon('markitup', 'vendor/php-textile/src/Netcarver/Textile/DataBag.php')); //todo: use $this
        require_once(rex_path::addon('markitup', 'vendor/php-textile/src/Netcarver/Textile/Tag.php')); //todo: use $this
    }

    class markitup_textile extends Netcarver\Textile\Parser
    {
        private static $instances = [];

        public function __construct($doctype = 'xhtml')
        {
            parent::__construct($doctype);
            $this->unrestricted_url_schemes[] = 'redaxo';
            $this->restricted_url_schemes[] = 'redaxo';
            $this->unrestricted_url_schemes[] = 'yform';
            $this->restricted_url_schemes[] = 'yform';
        }

        public static function custom_parse($code, $restricted = false, $doctype = 'xhtml')
        {
            $instance = self::getInstance($doctype);
            return $restricted ? $instance->setRestricted(true)->parse($code) : $instance->setRestricted(false)->parse($code);
        }

        private static function getInstance($doctype = 'xhtml')
        {
            if (!isset(self::$instances[$doctype])) {
                self::$instances[$doctype] = new markitup_textile($doctype);
            }

            return self::$instances[$doctype];
        }
    }
