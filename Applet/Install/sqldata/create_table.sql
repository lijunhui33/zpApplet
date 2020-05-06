--
-- 表的结构 `qs_ad_weixinapp`
--

DROP TABLE IF EXISTS `qs_ad_weixinapp`;
CREATE TABLE `qs_ad_weixinapp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(80) NOT NULL,
  `is_display` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(100) NOT NULL,
  `note` varchar(230) NOT NULL,
  `show_order` int(10) unsigned NOT NULL DEFAULT '50',
  `addtime` int(10) unsigned NOT NULL,
  `starttime` int(10) unsigned NOT NULL,
  `deadline` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `explain` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias_starttime_deadline` (`alias`,`starttime`,`deadline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- 表的结构 `qs_weixin_subscribe`
--

DROP TABLE IF EXISTS `qs_weixin_subscribe`;
CREATE TABLE `qs_weixin_subscribe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `intention_jobs_id` varchar(100) NOT NULL,
  `intention_jobs` varchar(255) NOT NULL,
  `wage` smallint(5) NOT NULL,
  `wage_cn` varchar(30) NOT NULL,
  `district` varchar(100) NOT NULL,
  `district_cn` varchar(255) NOT NULL,
  `open_id` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `authorize` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_baidu_subscribe`
--

DROP TABLE IF EXISTS `qs_baidu_subscribe`;
CREATE TABLE `qs_baidu_subscribe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `intention_jobs_id` varchar(100) NOT NULL,
  `intention_jobs` varchar(255) NOT NULL,
  `wage` smallint(5) NOT NULL,
  `wage_cn` varchar(30) NOT NULL,
  `district` varchar(100) NOT NULL,
  `district_cn` varchar(255) NOT NULL,
  `open_id` varchar(100) NOT NULL,
  `scene_id` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;