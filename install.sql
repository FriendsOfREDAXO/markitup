DROP TABLE IF EXISTS `%TABLE_PREFIX%markitup_profiles`;

CREATE TABLE `%TABLE_PREFIX%markitup_profiles` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `urltype` varchar(50) NOT NULL,
  `minheight` smallint(5) unsigned NOT NULL,
  `maxheight` smallint(5) unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `markitup_buttons` text NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `%TABLE_PREFIX%markitup_profiles` (`id`, `name`, `description`, `urltype`,`minheight`,`maxheight`,`type`, `markitup_buttons`) VALUES
(1, 'textile_full', 'Standard MarkItUp-Konfiguration', 'relative', '300', '800', 'textile', 'bold,code,deleted,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],heading1,heading2,heading3,heading4,heading5,heading6,image,italic,link[internal|external|mailto],orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist'),
(2, 'markdown_full', 'Standard MarkItUp-Konfiguration', 'relative', '300', '800', 'markdown', 'bold,code,deleted,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],heading1,heading2,heading3,heading4,heading5,heading6,image,italic,link[internal|external|mailto],orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist');

ALTER TABLE `%TABLE_PREFIX%markitup_profiles`
 ADD PRIMARY KEY (`id`);
 
ALTER TABLE `%TABLE_PREFIX%markitup_profiles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;