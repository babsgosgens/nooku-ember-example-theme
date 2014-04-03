
-- --------------------------------------------------------
--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `articles_article_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `categories_category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `attachments_attachment_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `locked_by` int(11) unsigned DEFAULT NULL,
  `locked_on` datetime DEFAULT NULL,
  `publish_on` datetime DEFAULT NULL,
  `unpublish_on` datetime DEFAULT NULL,
  `params` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `description` text,
  `access` int(11) unsigned NOT NULL DEFAULT '0',
  `uuid` char(36) NOT NULL,
  PRIMARY KEY (`articles_article_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_access` (`access`),
  KEY `idx_state` (`published`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_catid` (`categories_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;