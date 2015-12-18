DROP TABLE IF EXISTS `%TABLE_PREFIX%markitup_profiles`;

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%markitup_profiles` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL,
  `markitup_buttons` text NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `%TABLE_PREFIX%markitup_profiles` (`id`, `name`, `description`, `type`, `markitup_buttons`) VALUES
(1, 'full', 'Standard MarkItUp-Konfiguration', 'textile', 'bold,code,deleted,clips[Snippetname1=Snippettext1|Snippetname2=Snippettext2],heading1,heading2,heading3,heading4,heading5,heading6,image,italic,link[internal|external|mailto],orderedlist,paragraph,quote,sub,sup,table,underline,unorderedlist');

ALTER TABLE `%TABLE_PREFIX%markitup_profiles`
 ADD PRIMARY KEY (`id`);
 
ALTER TABLE `%TABLE_PREFIX%markitup_profiles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;