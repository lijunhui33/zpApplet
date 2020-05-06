--
-- 删除数据表 `qs_ad_weixinapp`
--

DROP TABLE IF EXISTS `qs_ad_weixinapp`;

--
-- 删除数据 `qs_config`
--

DELETE FROM `qs_config` WHERE `type`='Applet';

--
-- 删除数据表 `qs_weixin_subscribe`
--

DROP TABLE IF EXISTS `qs_weixin_subscribe`;

--
-- 删除数据表 `qs_baidu_subscribe`
--

DROP TABLE IF EXISTS `qs_baidu_subscribe`;
