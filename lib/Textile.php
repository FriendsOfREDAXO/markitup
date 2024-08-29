<?php

namespace FriendsOfRedaxo\MarkItUp;

use Netcarver\Textile\Parser;

class Textile extends Parser
{
    /** @var array<self> */
    private static $instances = [];

    public function __construct($doctype = 'xhtml')
    {
        parent::__construct($doctype);
        $this->unrestricted_url_schemes[] = 'redaxo';
        $this->restricted_url_schemes[] = 'redaxo';
        $this->unrestricted_url_schemes[] = 'yform';
        $this->restricted_url_schemes[] = 'yform';
    }

    /**
     * @api
     */
    public static function custom_parse(string $code, bool $restricted = false, string $doctype = 'xhtml'): string
    {
        $instance = self::getInstance($doctype);
        // TODO: Code übersichtlicher
        return $restricted ? $instance->setRestricted(true)->parse($code) : $instance->setRestricted(false)->parse($code);
    }

    private static function getInstance(string $doctype = 'xhtml'): self
    {
        if (!isset(self::$instances[$doctype])) {
            self::$instances[$doctype] = new self($doctype);
        }

        return self::$instances[$doctype];
    }
}
