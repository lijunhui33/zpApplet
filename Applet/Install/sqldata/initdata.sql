--
-- 转存表中的数据 `qs_config`
--

INSERT INTO `qs_config` (`name`, `value`, `note`, `type`) VALUES 
('weixinapp_appid', '', '微信小程序appid', 'Applet'),
('weixinapp_appsecret', '', '微信小程序appsecret', 'Applet'),
('weixinapp_template_id', '', '微信小程序订阅模板id', 'Applet'),
('baidu_appkey', '', '百度小程序appkey', 'Applet'),
('baidu_appsecret', '', '百度小程序密钥', 'Applet'),
('baidu_template_id', '', '百度小程序订阅模板id', 'Applet'),
('applet_map_key', '', '小程序百度地图AK', 'Applet'),
('weixinapp_qrcode', '', '微信小程序二维码', 'Applet');