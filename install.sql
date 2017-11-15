CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%markitup_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `urltype` varchar(50) NOT NULL,
  `minheight` smallint(5) unsigned NOT NULL,
  `maxheight` smallint(5) unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `markitup_buttons` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `%TABLE_PREFIX%markitup_profiles` (`id`, `name`, `description`, `urltype`,`minheight`,`maxheight`,`type`, `markitup_buttons`) VALUES
(1, 'textile_full', 'Standard MarkItUp-Konfiguration', 'relative', '300', '800', 'textile', 'bold,code,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],deleted,emaillink,externallink,groupheading[1|2|3|4|5|6],grouplink[file|internal|external|mailto],heading1,heading2,heading3,heading4,heading5,heading6,internallink,italic,media,medialink,orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist'),
(2, 'markdown_full', 'Standard MarkItUp-Konfiguration', 'relative', '300', '800', 'markdown', 'bold,code,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],deleted,emaillink,externallink,groupheading[1|2|3|4|5|6],grouplink[file|internal|external|mailto],heading1,heading2,heading3,heading4,heading5,heading6,internallink,italic,media,medialink,orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist');