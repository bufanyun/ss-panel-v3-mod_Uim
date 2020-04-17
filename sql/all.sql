-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-04-16 18:09:33
-- 服务器版本： 5.7.29-log
-- PHP 版本： 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `maoli`
--

-- --------------------------------------------------------

--
-- 表的结构 `agent_cash_log`
--

CREATE TABLE `agent_cash_log` (
  `id` int(22) NOT NULL,
  `uid` int(22) NOT NULL COMMENT '代理id',
  `account` varchar(256) DEFAULT NULL COMMENT '支付宝',
  `money` decimal(10,2) NOT NULL COMMENT '提现金额',
  `service_fee` decimal(10,2) NOT NULL COMMENT '服务费',
  `make_money` decimal(10,2) NOT NULL COMMENT '到账金额',
  `sttime` int(22) NOT NULL COMMENT '申请时间',
  `endtime` int(22) NOT NULL COMMENT '到账时间',
  `status` int(2) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现记录表';

-- --------------------------------------------------------

--
-- 表的结构 `agent_config`
--

CREATE TABLE `agent_config` (
  `id` int(22) NOT NULL,
  `uid` int(22) NOT NULL COMMENT '用户id',
  `level` int(11) NOT NULL COMMENT '代理等级',
  `domain` varchar(256) DEFAULT NULL COMMENT '绑定域名',
  `panel` text NOT NULL COMMENT '面板设置',
  `shop` text NOT NULL COMMENT '商品价格',
  `cash` text NOT NULL COMMENT '提现信息',
  `addtime` int(22) NOT NULL COMMENT '开通时间',
  `endtime` int(22) NOT NULL COMMENT '到期时间',
  `status` int(2) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理配置信息表';

--
-- 转存表中的数据 `agent_config`
--

INSERT INTO `agent_config` (`id`, `uid`, `level`, `domain`, `panel`, `shop`, `cash`, `addtime`, `endtime`, `status`) VALUES
(1, 1, 4, 'malio.ms521.cn', '{\"name\":\"\\u5409\\u5409\\u56fd\\u738b\",\"chatra_id\":\"kEPtGmPvGWrWTsF3C\",\"enable_chatra\":\"1\"}', '{\"id1\":{\"p1\":\"5.40\",\"p2\":\"4.80\",\"p3\":\"4.20\",\"p4\":\"3.60\"},\"id2\":{\"p1\":\"9.00\",\"p2\":\"8.00\",\"p3\":\"7.00\",\"p4\":\"6.00\"},\"id3\":{\"p1\":\"18.00\",\"p2\":\"16.00\",\"p3\":\"14.00\",\"p4\":\"12.00\"},\"id4\":{\"p1\":\"27.00\",\"p2\":\"24.00\",\"p3\":\"21.00\",\"p4\":\"18.00\"},\"id5\":{\"p1\":\"16.20\",\"p2\":\"14.40\",\"p3\":\"12.60\",\"p4\":\"10.80\"},\"id6\":{\"p1\":\"26.10\",\"p2\":\"23.20\",\"p3\":\"20.30\",\"p4\":\"17.40\"},\"id7\":{\"p1\":\"53.10\",\"p2\":\"47.20\",\"p3\":\"41.30\",\"p4\":\"35.40\"},\"id8\":{\"p1\":\"80.10\",\"p2\":\"71.20\",\"p3\":\"62.30\",\"p4\":\"53.40\"},\"id9\":{\"p1\":\"27.00\",\"p2\":\"24.00\",\"p3\":\"21.00\",\"p4\":\"18.00\"},\"id10\":{\"p1\":\"44.10\",\"p2\":\"39.20\",\"p3\":\"34.30\",\"p4\":\"29.40\"},\"id11\":{\"p1\":\"89.10\",\"p2\":\"79.20\",\"p3\":\"69.30\",\"p4\":\"59.40\"},\"id12\":{\"p1\":\"134.10\",\"p2\":\"119.20\",\"p3\":\"104.30\",\"p4\":\"89.40\"},\"id13\":{\"p1\":\"48.60\",\"p2\":\"43.20\",\"p3\":\"37.80\",\"p4\":\"32.40\"},\"id14\":{\"p1\":\"80.10\",\"p2\":\"71.20\",\"p3\":\"62.30\",\"p4\":\"53.40\"},\"id15\":{\"p1\":\"161.10\",\"p2\":\"143.20\",\"p3\":\"125.30\",\"p4\":\"107.40\"},\"id16\":{\"p1\":\"242.10\",\"p2\":\"215.20\",\"p3\":\"188.30\",\"p4\":\"161.40\"}}', '{\"name\":\"\\u5b5f\\u5e0511\",\"account\":\"1221122112\",\"img\":\"http:\\/\\/qiniu.ms521.cn\\/qq133814250_agent_panel_312476439_cash_uid1_202004161755329231.png\"}', 0, 1970, 1);

-- --------------------------------------------------------

--
-- 表的结构 `agent_error_log`
--

CREATE TABLE `agent_error_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `conent` varchar(256) NOT NULL,
  `ip` varchar(48) NOT NULL,
  `addtime` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='错误日志';

-- --------------------------------------------------------

--
-- 表的结构 `agent_income`
--

CREATE TABLE `agent_income` (
  `id` int(22) NOT NULL,
  `uid` int(22) NOT NULL COMMENT '代理id',
  `user_id` int(22) NOT NULL COMMENT '用户id',
  `type` varchar(128) DEFAULT NULL,
  `type_id` int(22) NOT NULL COMMENT '套餐id',
  `money` decimal(10,2) NOT NULL COMMENT '支付价格',
  `income` decimal(10,2) NOT NULL COMMENT '代理收益',
  `remark` varchar(256) DEFAULT NULL COMMENT '备注',
  `addtime` int(22) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理收益表';

-- --------------------------------------------------------

--
-- 表的结构 `agent_km`
--

CREATE TABLE `agent_km` (
  `id` int(22) NOT NULL,
  `uid` int(22) NOT NULL COMMENT '代理id',
  `type_id` int(22) NOT NULL COMMENT '套餐id',
  `km` varchar(56) NOT NULL COMMENT '卡密',
  `batch` varchar(128) NOT NULL COMMENT '批量编号',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态0未使用，1已用，2冻结',
  `user_id` int(22) DEFAULT NULL COMMENT '用户id',
  `endtime` int(22) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `addtime` int(22) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='套餐卡密表';

-- --------------------------------------------------------

--
-- 表的结构 `agent_level`
--

CREATE TABLE `agent_level` (
  `id` int(22) NOT NULL,
  `uid` int(22) NOT NULL COMMENT '代理id',
  `level` int(22) NOT NULL COMMENT '等级id',
  `name` varchar(128) NOT NULL COMMENT '名称',
  `price` decimal(10,2) NOT NULL COMMENT '开通价格',
  `status` int(2) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理等级表';

--
-- 转存表中的数据 `agent_level`
--

INSERT INTO `agent_level` (`id`, `uid`, `level`, `name`, `price`, `status`) VALUES
(1, 1, 1, '客户代理', '1.00', 1),
(2, 1, 2, '初级代理', '28.00', 1),
(3, 1, 3, '特级代理', '68.00', 1),
(4, 1, 4, '合作代理', '388.00', 1);

-- --------------------------------------------------------

--
-- 表的结构 `agent_money_log`
--

CREATE TABLE `agent_money_log` (
  `id` int(2) UNSIGNED NOT NULL,
  `uid` int(22) UNSIGNED NOT NULL COMMENT '用户ID',
  `money` decimal(10,2) NOT NULL COMMENT '变动金额',
  `befores` decimal(10,2) NOT NULL COMMENT '变动前',
  `after` decimal(10,2) NOT NULL COMMENT '变动后 ',
  `memo` varchar(256) NOT NULL COMMENT '备注',
  `ip` varchar(50) NOT NULL,
  `useragent` varchar(256) NOT NULL,
  `addtime` int(11) UNSIGNED NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额明细';

-- --------------------------------------------------------

--
-- 表的结构 `alive_ip`
--

CREATE TABLE `alive_ip` (
  `id` bigint(20) NOT NULL,
  `nodeid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `content` longtext NOT NULL,
  `markdown` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `announcement`
--

INSERT INTO `announcement` (`id`, `date`, `content`, `markdown`) VALUES
(1, '2020-04-09 16:35:04', '<p>这是第一条公告 欢迎来到蜻蜓2.0</p>\r\n<p>建立网站，从注册域名开始。腾讯云域名专场特惠，.com域名新用户仅需23元，买就送证书和解析，1小时搭建属于自己的网站</p>\r\n', '这是第一条公告 欢迎来到蜻蜓2.0\r\n\r\n\r\n建立网站，从注册域名开始。腾讯云域名专场特惠，.com域名新用户仅需23元，买就送证书和解析，1小时搭建属于自己的网站\r\n\r\n'),
(2, '2020-04-09 16:34:22', '<p>这是第二条公告</p>\n<p>想得却不可得，你奈人生何。<br>该舍的舍不得，只顾着跟往事瞎扯。<br>等你发现时间是贼了，它早已偷光你的选择。</p>\n', '这是第二条公告\n\n想得却不可得，你奈人生何。\n该舍的舍不得，只顾着跟往事瞎扯。\n等你发现时间是贼了，它早已偷光你的选择。'),
(3, '2020-04-09 16:34:42', '<p>这是第三条公告</p>\n<p>终于我们不再 为了生命狂欢 为爱情狂乱<br>然而青春彼岸 盛夏正要一天一天一天地灿烂</p>\n', '这是第三条公告\n\n终于我们不再 为了生命狂欢 为爱情狂乱\n然而青春彼岸 盛夏正要一天一天一天地灿烂');

-- --------------------------------------------------------

--
-- 表的结构 `auto`
--

CREATE TABLE `auto` (
  `id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `value` longtext NOT NULL,
  `sign` longtext NOT NULL,
  `datetime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `blockip`
--

CREATE TABLE `blockip` (
  `id` bigint(20) NOT NULL,
  `nodeid` int(11) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `bought`
--

CREATE TABLE `bought` (
  `id` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `shopid` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `renew` bigint(11) NOT NULL,
  `coupon` text NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `code`
--

CREATE TABLE `code` (
  `id` bigint(20) NOT NULL,
  `code` text NOT NULL,
  `type` int(11) NOT NULL,
  `number` decimal(11,2) NOT NULL,
  `isused` int(11) NOT NULL DEFAULT '0',
  `userid` bigint(20) NOT NULL,
  `usedatetime` datetime NOT NULL,
  `tradeno` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `config`
--

CREATE TABLE `config` (
  `name` varchar(255) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `config`
--

INSERT INTO `config` (`name`, `value`) VALUES
('AliPay_Cookie', NULL),
('AliPay_QRcode', NULL),
('AliPay_Status', '1'),
('Notice_EMail', NULL),
('Pay_Price', NULL),
('Pay_Xposed', '0'),
('WxPay_Cookie', NULL),
('WxPay_QRcode', NULL),
('WxPay_Status', '1'),
('WxPay_SyncKey', NULL),
('WxPay_Url', 'wx.qq.com');

-- --------------------------------------------------------

--
-- 表的结构 `coupon`
--

CREATE TABLE `coupon` (
  `id` bigint(20) NOT NULL,
  `code` text NOT NULL,
  `onetime` int(11) NOT NULL,
  `expire` bigint(20) NOT NULL,
  `shop` text NOT NULL,
  `credit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `detect_ban_log`
--

CREATE TABLE `detect_ban_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `user_id` int(11) NOT NULL COMMENT '用户 ID',
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱',
  `detect_number` int(11) NOT NULL COMMENT '本次违规次数',
  `ban_time` int(11) NOT NULL COMMENT '本次封禁时长',
  `start_time` bigint(20) NOT NULL COMMENT '统计开始时间',
  `end_time` bigint(20) NOT NULL COMMENT '统计结束时间',
  `all_detect_number` int(11) NOT NULL COMMENT '累计违规次数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审计封禁日志';

-- --------------------------------------------------------

--
-- 表的结构 `detect_list`
--

CREATE TABLE `detect_list` (
  `id` bigint(20) NOT NULL,
  `name` longtext NOT NULL,
  `text` longtext NOT NULL,
  `regex` longtext NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `detect_log`
--

CREATE TABLE `detect_log` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `node_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `disconnect_ip`
--

CREATE TABLE `disconnect_ip` (
  `id` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `email_verify`
--

CREATE TABLE `email_verify` (
  `id` bigint(20) NOT NULL,
  `email` text NOT NULL,
  `ip` text NOT NULL,
  `code` text NOT NULL,
  `expire_in` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `gconfig`
--

CREATE TABLE `gconfig` (
  `key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置值',
  `oldvalue` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '之前的配置值',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名称',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置描述',
  `operator_id` int(11) NOT NULL COMMENT '操作员 ID',
  `operator_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作员名称',
  `operator_email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作员邮箱',
  `last_update` bigint(20) NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='网站配置';

--
-- 转存表中的数据 `gconfig`
--

INSERT INTO `gconfig` (`key`, `value`, `oldvalue`, `name`, `comment`, `operator_id`, `operator_name`, `operator_email`, `last_update`) VALUES
('Telegram.enable.DailyJob', '1', '', '开启 Telegram 群组推送数据库清理的通知', '', 0, '系统默认', '', 1583769605),
('Telegram.enable.DeleteNode', '1', '', '开启 Telegram 群组推送节点删除的通知', '', 0, '系统默认', '', 1584341456),
('Telegram.enable.Diary', '1', '', '开启 Telegram 群组推送系统今天的运行状况', '', 0, '系统默认', '', 1583764203),
('Telegram.enable.NodeOffline', '1', '', '开启 Telegram 群组推送节点掉线的通知', '', 0, '系统默认', '', 1583748004),
('Telegram.enable.NodeOnline', '1', '', '开启 Telegram 群组推送节点上线的通知', '', 0, '系统默认', '', 1584149408),
('Telegram.enable.UpdateNode', '1', '', '开启 Telegram 群组推送节点修改的通知', '', 0, '系统默认', '', 1584341500),
('Telegram.msg.DailyJob', '姐姐姐姐，数据库被清理了，感觉身体被掏空了呢~', '', '自定义向 Telegram 群组推送数据库清理通知的信息', '', 0, '系统默认', '', 1583769605),
('Telegram.msg.DeleteNode', '节点被删除~ %node_name%', '', '自定义向 Telegram 群组推送节点删除通知的信息', '可用变量：\n[节点名称] %node_name%', 0, '系统默认', '', 1584341456),
('Telegram.msg.Diary', '各位老爷少奶奶，我来为大家报告一下系统今天的运行状况哈~\n今日签到人数：%getTodayCheckinUser%\n今日使用总流量：%lastday_total%\n晚安~', '', '自定义向 Telegram 群组推送系统今天的运行状况的信息', '可用变量：\n[今日签到人数] %getTodayCheckinUser%\n[今日使用总流量] %lastday_total%', 0, '系统默认', '', 1583764203),
('Telegram.msg.NodeOffline', '喵喵喵~ %node_name% 节点掉线了喵~', '', '自定义向 Telegram 群组推送节点掉线通知的信息', '可用变量：\n[节点名称] %node_name%', 0, '系统默认', '', 1583748004),
('Telegram.msg.NodeOnline', '喵喵喵~ %node_name% 节点恢复了喵~', '', '自定义向 Telegram 群组推送节点上线通知的信息', '可用变量：\n[节点名称] %node_name%', 0, '系统默认', '', 1584149408),
('Telegram.msg.UpdateNode', '节点信息被修改~ %node_name%', '', '自定义向 Telegram 群组推送节点修改通知的信息', '可用变量：\n[节点名称] %node_name%', 0, '系统默认', '', 1584341500);

-- --------------------------------------------------------

--
-- 表的结构 `link`
--

CREATE TABLE `link` (
  `id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `address` text NOT NULL,
  `port` int(11) NOT NULL,
  `token` text NOT NULL,
  `ios` int(11) NOT NULL DEFAULT '0',
  `userid` bigint(20) NOT NULL,
  `isp` text,
  `geo` int(11) DEFAULT NULL,
  `method` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `link`
--

INSERT INTO `link` (`id`, `type`, `address`, `port`, `token`, `ios`, `userid`, `isp`, `geo`, `method`) VALUES
(214, 11, '', 0, '0wY9NpbFvNhEzPR7', 0, 1, NULL, 0, ''),
(215, 11, '', 0, 'R6wjl0NWTvmaTL76', 0, 1, NULL, 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `login_ip`
--

CREATE TABLE `login_ip` (
  `id` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `login_ip`
--

INSERT INTO `login_ip` (`id`, `userid`, `ip`, `datetime`, `type`) VALUES
(685, 1, '115.60.62.156', 1587031711, 0),
(686, 1, '115.60.62.156', 1587031711, 0),
(687, 1, '115.60.62.156', 1587031742, 0),
(688, 1, '115.60.62.156', 1587031742, 0);

-- --------------------------------------------------------

--
-- 表的结构 `payback`
--

CREATE TABLE `payback` (
  `id` bigint(20) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `ref_by` bigint(20) NOT NULL,
  `ref_get` decimal(12,2) NOT NULL,
  `datetime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `payback`
--

INSERT INTO `payback` (`id`, `total`, `userid`, `ref_by`, `ref_get`, `datetime`) VALUES
(1, '2.00', 61, 32, '0.40', 1576851848),
(2, '5.00', 71, 61, '1.00', 1581318251),
(3, '9.00', 104, 84, '1.80', 1582885934),
(4, '2.00', 123, 84, '0.40', 1585283279),
(5, '3.00', 104, 84, '0.60', 1585567557);

-- --------------------------------------------------------

--
-- 表的结构 `paylist`
--

CREATE TABLE `paylist` (
  `id` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `tradeno` text,
  `datetime` bigint(20) NOT NULL DEFAULT '0',
  `type` int(1) DEFAULT '0',
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `radius_ban`
--

CREATE TABLE `radius_ban` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `relay`
--

CREATE TABLE `relay` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `source_node_id` bigint(20) NOT NULL,
  `dist_node_id` bigint(20) NOT NULL,
  `dist_ip` text NOT NULL,
  `port` int(11) NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `shop`
--

CREATE TABLE `shop` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `content` text NOT NULL,
  `auto_renew` int(11) NOT NULL,
  `auto_reset_bandwidth` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `prefix` varchar(24) NOT NULL COMMENT '前缀',
  `parentid` int(22) NOT NULL COMMENT '父类id',
  `month` varchar(2) NOT NULL COMMENT '月数',
  `agent_price_1` decimal(12,2) DEFAULT NULL,
  `agent_price_2` decimal(12,2) DEFAULT NULL,
  `agent_price_3` decimal(12,2) DEFAULT NULL,
  `agent_price_4` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `shop`
--

INSERT INTO `shop` (`id`, `name`, `price`, `content`, `auto_renew`, `auto_reset_bandwidth`, `status`, `prefix`, `parentid`, `month`, `agent_price_1`, `agent_price_2`, `agent_price_3`, `agent_price_4`) VALUES
(1, 'VIP1 300G 月付', '6.00', '{\"bandwidth\":\"300\",\"expire\":\"31\",\"class\":\"3\",\"class_expire\":\"31\",\"speedlimit\":\"40\",\"connector\":\"2\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u5e73\\u53f0\\u5ba2\\u6237\\u7aef\"}', 0, 0, 1, '1', 1, '1', '5.40', '4.80', '4.20', '3.60'),
(2, 'VIP2  600G 月付', '10.00', '{\"bandwidth\":\"600\",\"expire\":\"31\",\"class\":\"3\",\"class_expire\":\"31\",\"speedlimit\":\"80\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de\"}', 0, 0, 1, '2', 2, '1', '9.00', '8.00', '7.00', '6.00'),
(3, 'VIP3 1024G 月付', '20.00', '{\"bandwidth\":\"1024\",\"expire\":\"31\",\"class\":\"4\",\"class_expire\":\"31\",\"speedlimit\":\"120\",\"connector\":\"4\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de\"}', 0, 0, 1, '3', 3, '1', '18.00', '16.00', '14.00', '12.00'),
(4, 'VIP4 不限量 月付 ', '30.00', '{\"bandwidth\":\"10240\",\"expire\":\"31\",\"class\":\"5\",\"class_expire\":\"31\",\"reset_value\":\"10240\",\"speedlimit\":\"150\",\"connector\":\"6\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de;check-\\u9884\\u8bbe10T\\u6d41\\u91cf\\uff0c\\u53ef\\u91cd\\u7f6e\\uff0c\\u7981\\u6b62\\u6ee5\\u7528\"}', 0, 0, 1, '4', 4, '1', '27.00', '24.00', '21.00', '18.00'),
(5, 'VIP1 300G 季付', '18.00', '{\"bandwidth\":\"300\",\"expire\":\"93\",\"class\":\"3\",\"class_expire\":\"93\",\"reset\":\"31\",\"reset_value\":\"300\",\"reset_exp\":\"93\",\"speedlimit\":\"60\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u5e73\\u53f0\\u5ba2\\u6237\\u7aef\"}', 0, 0, 1, '5', 1, '3', '16.20', '14.40', '12.60', '10.80'),
(6, 'VIP2 600G 季付', '29.00', '{\"bandwidth\":\"600\",\"expire\":\"93\",\"class\":\"3\",\"class_expire\":\"93\",\"reset\":\"31\",\"reset_value\":\"600\",\"reset_exp\":\"93\",\"speedlimit\":\"80\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de\"}', 0, 0, 1, '6', 2, '3', '26.10', '23.20', '20.30', '17.40'),
(7, 'VIP3 1024G 季付', '59.00', '{\"bandwidth\":\"1024\",\"expire\":\"93\",\"class\":\"4\",\"class_expire\":\"93\",\"reset\":\"30\",\"reset_value\":\"1024\",\"reset_exp\":\"93\",\"speedlimit\":\"120\",\"connector\":\"4\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de\"}', 0, 0, 1, '7', 3, '3', '53.10', '47.20', '41.30', '35.40'),
(8, 'VIP4 不限量 季付', '89.00', '{\"bandwidth\":\"10240\",\"expire\":\"93\",\"class\":\"5\",\"class_expire\":\"93\",\"reset\":\"31\",\"reset_value\":\"10240\",\"reset_exp\":\"93\",\"speedlimit\":\"150\",\"connector\":\"6\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de;check-\\u9884\\u8bbe800G\\u6d41\\u91cf\\uff0c\\u53ef\\u91cd\\u7f6e\\uff0c\\u7981\\u6b62\\u6ee5\\u7528\"}', 0, 0, 1, '8', 4, '3', '80.10', '71.20', '62.30', '53.40'),
(9, 'VIP1 300G 半年付', '30.00', '{\"bandwidth\":\"300\",\"expire\":\"186\",\"class\":\"3\",\"class_expire\":\"186\",\"reset\":\"31\",\"reset_value\":\"300\",\"reset_exp\":\"186\",\"speedlimit\":\"60\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u5e73\\u53f0\\u5ba2\\u6237\\u7aef\"}', 0, 0, 1, '9', 1, '6', '27.00', '24.00', '21.00', '18.00'),
(10, 'VIP2 600G 半年付', '49.00', '{\"bandwidth\":\"600\",\"expire\":\"186\",\"class\":\"3\",\"class_expire\":\"186\",\"reset\":\"31\",\"reset_value\":\"600\",\"reset_exp\":\"186\",\"speedlimit\":\"80\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u5e73\\u53f0\\u5ba2\\u6237\\u7aef\"}', 0, 0, 1, '10', 2, '6', '44.10', '39.20', '34.30', '29.40'),
(11, 'VIP3 1024G 半年付', '99.00', '{\"bandwidth\":\"1024\",\"expire\":\"186\",\"class\":\"4\",\"class_expire\":\"186\",\"reset\":\"31\",\"reset_value\":\"1024\",\"reset_exp\":\"186\",\"speedlimit\":\"120\",\"connector\":\"4\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de\"}', 0, 0, 1, '11', 3, '6', '89.10', '79.20', '69.30', '59.40'),
(12, 'VIP4 不限量 半年付', '149.00', '{\"bandwidth\":\"10240\",\"expire\":\"186\",\"class\":\"5\",\"class_expire\":\"186\",\"reset\":\"31\",\"reset_value\":\"10240\",\"reset_exp\":\"186\",\"speedlimit\":\"150\",\"connector\":\"6\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de;check-\\u9884\\u8bbe1000G\\u6d41\\u91cf\\uff0c\\u53ef\\u91cd\\u7f6e\\uff0c\\u7981\\u6b62\\u6ee5\\u7528\"}', 0, 0, 1, '12', 4, '6', '134.10', '119.20', '104.30', '89.40'),
(13, 'VIP1 300G 年付', '54.00', '{\"bandwidth\":\"300\",\"expire\":\"372\",\"class\":\"3\",\"class_expire\":\"372\",\"reset\":\"30\",\"reset_value\":\"300\",\"reset_exp\":\"372\",\"speedlimit\":\"60\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u5e73\\u53f0\\u5ba2\\u6237\\u7aef\"}', 0, 0, 1, '13', 1, '12', '48.60', '43.20', '37.80', '32.40'),
(14, 'VIP2 600G 年付 ', '89.00', '{\"bandwidth\":\"600\",\"expire\":\"372\",\"class\":\"3\",\"class_expire\":\"372\",\"reset\":\"31\",\"reset_value\":\"600\",\"reset_exp\":\"372\",\"speedlimit\":\"80\",\"connector\":\"3\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u5e73\\u53f0\\u5ba2\\u6237\\u7aef\"}', 0, 0, 1, '14', 2, '12', '80.10', '71.20', '62.30', '53.40'),
(15, 'VIP3 1024G 年付', '179.00', '{\"bandwidth\":\"1024\",\"expire\":\"372\",\"class\":\"4\",\"class_expire\":\"372\",\"reset\":\"31\",\"reset_value\":\"1024\",\"reset_exp\":\"372\",\"speedlimit\":\"120\",\"connector\":\"4\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5feb\\u901f\\u5ba2\\u670d\\u54cd\\u5e94;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de\"}', 0, 0, 1, '15', 3, '12', '161.10', '143.20', '125.30', '107.40'),
(16, 'VIP4 不限量 年付', '269.00', '{\"bandwidth\":\"10240\",\"expire\":\"372\",\"class\":\"5\",\"class_expire\":\"372\",\"reset\":\"31\",\"reset_value\":\"10240\",\"reset_exp\":\"372\",\"speedlimit\":\"150\",\"connector\":\"6\",\"content_extra\":\"check-\\u5168\\u7403\\u8282\\u70b9\\u5206\\u5e03;check-\\u5168\\u8282\\u70b9\\u652f\\u6301\\u5948\\u98de;check-\\u9884\\u8bbe800G\\u6d41\\u91cf\\uff0c\\u53ef\\u91cd\\u7f6e\\uff0c\\u7981\\u6b62\\u6ee5\\u7528\"}', 0, 0, 1, '16', 4, '12', '242.10', '215.20', '188.30', '161.40');

-- --------------------------------------------------------

--
-- 表的结构 `speedtest`
--

CREATE TABLE `speedtest` (
  `id` bigint(20) NOT NULL,
  `nodeid` int(11) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `telecomping` text NOT NULL,
  `telecomeupload` text NOT NULL,
  `telecomedownload` text NOT NULL,
  `unicomping` text NOT NULL,
  `unicomupload` text NOT NULL,
  `unicomdownload` text NOT NULL,
  `cmccping` text NOT NULL,
  `cmccupload` text NOT NULL,
  `cmccdownload` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ss_invite_code`
--

CREATE TABLE `ss_invite_code` (
  `id` int(11) NOT NULL,
  `code` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '2016-06-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ss_invite_code`
--

INSERT INTO `ss_invite_code` (`id`, `code`, `user_id`, `created_at`, `updated_at`) VALUES
(55, '8wlf', 1, '2020-04-16 10:08:25', '2016-06-01 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `ss_node`
--

CREATE TABLE `ss_node` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` int(3) NOT NULL,
  `server` varchar(128) NOT NULL,
  `method` varchar(64) NOT NULL,
  `info` varchar(128) NOT NULL,
  `status` varchar(128) NOT NULL,
  `sort` int(3) NOT NULL,
  `custom_method` tinyint(1) NOT NULL DEFAULT '0',
  `traffic_rate` float NOT NULL DEFAULT '1',
  `node_class` int(11) NOT NULL DEFAULT '0',
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `node_connector` int(11) NOT NULL DEFAULT '0',
  `node_bandwidth` bigint(20) NOT NULL DEFAULT '0',
  `node_bandwidth_limit` bigint(20) NOT NULL DEFAULT '0',
  `bandwidthlimit_resetday` int(11) NOT NULL DEFAULT '0',
  `node_heartbeat` bigint(20) NOT NULL DEFAULT '0',
  `node_ip` text,
  `node_group` int(11) NOT NULL DEFAULT '0',
  `custom_rss` int(11) NOT NULL DEFAULT '0',
  `mu_only` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `ss_node`
--

INSERT INTO `ss_node` (`id`, `name`, `type`, `server`, `method`, `info`, `status`, `sort`, `custom_method`, `traffic_rate`, `node_class`, `node_speedlimit`, `node_connector`, `node_bandwidth`, `node_bandwidth_limit`, `bandwidthlimit_resetday`, `node_heartbeat`, `node_ip`, `node_group`, `custom_rss`, `mu_only`) VALUES
(1, '统一验证登陆', 0, 'zhaojin97.cn', 'radius', '统一登陆验证', '可用', 999, 0, 1, 0, '0.00', 0, 0, 0, 0, 0, '', 0, 0, 0),
(2, 'VPN 统一流量结算', 0, 'zhaojin97.cn', 'radius', 'VPN 统一流量结算', '可用', 999, 0, 1, 0, '0.00', 0, 0, 0, 0, 0, NULL, 0, 0, 0),
(3, '澳门', 1, 'vmess://eyJhZGQiOiJhYS5mb2xsdW8ubWUiLCJhaWQiOiIyMzMiLCJob3N0IjoiIiwiaWQiOiI3NWM3ZWRmMi1mZmMwLTQxMmItOTMyNC0wYTQzYjhhZmJjMmQiLCJu', 'aes-256-cfb', '无描述', '可用', 11, 1, 1, 5, '0.00', 0, 0, 0, 1, 0, 'vmess://eyJhZGQiOiJhYS5mb2xsdW8ubWUiLCJhaWQiOiIyMzMiLCJob3N0IjoiIiwiaWQiOiI3NWM3ZWRmMi1mZmMwLTQxMmItOTMyNC0wYTQzYjhhZmJjMmQiLCJu', 100, 1, 1),
(4, '日本 A 日区Netflix - V2ray', 1, 'jp.ls-a.yj2c.com;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '日本 AWS-A-美区Netflix', '可用', 11, 1, 1, 1, '0.00', 0, 20386044114, 1073741824000, 1, 1586359693, '18.176.143.140', 0, 1, 1),
(7, '印度 A 日区Netflix - V2ray', 1, 'in.ls-a.yj2c.com;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 1, 1, '0.00', 0, 292641741, 536870912000, 1, 1586359674, '13.233.195.9', 0, 1, 1),
(9, '新加坡 A 日区Netflix - V2ray', 1, 'sg.ls-a.yj2c.com;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 1, 1, '0.00', 0, 20254534267, 1073741824000, 1, 1586359672, '3.0.98.247', 0, 1, 1),
(11, '美国 A 美区Netflix -V2ray', 1, 'us.ls-a.yj2c.com;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 1, 1, '0.00', 0, 123168817, 1073741824000, 1, 1586359684, '54.202.32.11', 0, 1, 1),
(13, '韩国 A 日区Netflix - V2ray', 1, 'kr.ls-a.yj2c.com;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 1, 1, '0.00', 0, 911545948, 1073741824000, 1, 1586359667, '54.180.157.11', 0, 1, 1),
(15, '俄罗斯 A 俄区Netflix - V2ray', 1, 'ru.jh1.feverss.site;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 0.5, 2, '0.00', 0, 593971352, 0, 1, 1586359687, '45.8.158.172', 0, 1, 1),
(19, '俄罗斯 E 俄区Netflix - V2ray', 1, 'ru.jh2.feverss.site;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 0.5, 2, '0.00', 0, 0, 0, 1, 1586359659, '194.50.170.50', 0, 1, 1),
(21, '美国 圣何塞 HE - V2RAY', 1, 'us.hkss.feverss.site;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 2, 3, '0.00', 0, 11027624149, 0, 1, 1586359683, '38.39.232.172', 0, 1, 1),
(27, '香港 HKT 直连 - V2RAY', 1, 'anyhk.feverss.site;42000;2;ws;;path=/v2ray|host=ajax.microsoft.com|server=ru.jh1.feverss.site', 'aes-256-cfb', '香港HKT直连', '可用', 11, 1, 1, 3, '0.00', 0, 64799196, 0, 1, 1586359674, '220.246.88.193', 0, 1, 1),
(28, '美国 LV HE - V2ray', 1, 'bv.lv1.feverss.site;41000;2;ws;;path=/v2ray|host=ajax.microsoft.com', 'aes-256-cfb', '无描述', '可用', 11, 1, 1, 3, '0.00', 0, 14523234, 0, 1, 1586000298, '209.141.52.41', 0, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `ss_node_info`
--

CREATE TABLE `ss_node_info` (
  `id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `uptime` float NOT NULL,
  `load` varchar(32) NOT NULL,
  `log_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ss_node_online_log`
--

CREATE TABLE `ss_node_online_log` (
  `id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `online_user` int(11) NOT NULL,
  `log_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ss_password_reset`
--

CREATE TABLE `ss_password_reset` (
  `id` int(11) NOT NULL,
  `email` varchar(32) NOT NULL,
  `token` varchar(128) NOT NULL,
  `init_time` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `telegram_session`
--

CREATE TABLE `telegram_session` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `session_content` text NOT NULL,
  `datetime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `telegram_session`
--

INSERT INTO `telegram_session` (`id`, `user_id`, `type`, `session_content`, `datetime`) VALUES
(1869, 0, 1, 'hCOgSSxbMsS5XMb3|530828', 1587031705),
(1870, 0, 1, 'v33hrMSIpfsM5SzF|319515', 1587031738);

-- --------------------------------------------------------

--
-- 表的结构 `telegram_tasks`
--

CREATE TABLE `telegram_tasks` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(8) NOT NULL COMMENT '任务类型',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '任务状态',
  `chatid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Telegram Chat ID',
  `messageid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Telegram Message ID',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '任务详细内容',
  `process` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '临时任务进度',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '网站用户 ID',
  `tguserid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Telegram User ID',
  `executetime` bigint(20) NOT NULL COMMENT '任务执行时间',
  `datetime` bigint(20) NOT NULL COMMENT '任务产生时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Telegram 任务列表';

-- --------------------------------------------------------

--
-- 表的结构 `ticket`
--

CREATE TABLE `ticket` (
  `id` bigint(20) NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `rootid` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `unblockip`
--

CREATE TABLE `unblockip` (
  `id` bigint(20) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `money` decimal(12,2) NOT NULL,
  `is_admin` int(2) NOT NULL DEFAULT '0',
  `user_name` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(32) NOT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `pass` varchar(256) NOT NULL,
  `passwd` varchar(16) NOT NULL,
  `t` int(11) NOT NULL DEFAULT '0',
  `u` bigint(20) NOT NULL,
  `d` bigint(20) NOT NULL,
  `plan` varchar(2) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'A',
  `transfer_enable` bigint(20) NOT NULL,
  `port` int(11) NOT NULL,
  `switch` tinyint(4) NOT NULL DEFAULT '1',
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `detect_ban` int(2) NOT NULL DEFAULT '0',
  `last_detect_ban_time` datetime DEFAULT '1989-06-04 00:05:00',
  `all_detect_number` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `last_get_gift_time` int(11) NOT NULL DEFAULT '0',
  `last_check_in_time` int(11) NOT NULL DEFAULT '0',
  `last_rest_pass_time` int(11) NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL,
  `invite_num` int(8) NOT NULL,
  `ref_by` int(11) NOT NULL DEFAULT '0',
  `expire_time` int(11) NOT NULL DEFAULT '0',
  `method` varchar(64) NOT NULL DEFAULT 'rc4-md5',
  `is_email_verify` tinyint(4) NOT NULL DEFAULT '0',
  `reg_ip` varchar(128) NOT NULL DEFAULT '127.0.0.1',
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `node_connector` int(11) NOT NULL DEFAULT '0',
  `im_type` int(11) DEFAULT '1',
  `im_value` text,
  `last_day_t` bigint(20) NOT NULL DEFAULT '0',
  `sendDailyMail` int(11) NOT NULL DEFAULT '0',
  `class` int(11) NOT NULL DEFAULT '0',
  `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00',
  `expire_in` datetime NOT NULL DEFAULT '2099-06-04 00:05:00',
  `theme` text NOT NULL,
  `ga_token` text NOT NULL,
  `ga_enable` int(11) NOT NULL DEFAULT '0',
  `pac` longtext,
  `remark` text,
  `node_group` int(11) NOT NULL DEFAULT '0',
  `auto_reset_day` int(11) NOT NULL DEFAULT '0',
  `auto_reset_bandwidth` decimal(12,2) NOT NULL DEFAULT '0.00',
  `protocol` varchar(128) DEFAULT 'origin',
  `protocol_param` varchar(128) DEFAULT NULL,
  `obfs` varchar(128) DEFAULT 'plain',
  `obfs_param` varchar(128) DEFAULT NULL,
  `forbidden_ip` longtext,
  `forbidden_port` longtext,
  `disconnect_ip` longtext,
  `is_hide` int(11) NOT NULL DEFAULT '0',
  `is_multi_user` int(11) NOT NULL DEFAULT '0',
  `telegram_id` bigint(20) DEFAULT NULL,
  `discord` bigint(20) DEFAULT NULL,
  `uuid` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `money`, `is_admin`, `user_name`, `email`, `phone`, `pass`, `passwd`, `t`, `u`, `d`, `plan`, `transfer_enable`, `port`, `switch`, `enable`, `detect_ban`, `last_detect_ban_time`, `all_detect_number`, `type`, `last_get_gift_time`, `last_check_in_time`, `last_rest_pass_time`, `reg_date`, `invite_num`, `ref_by`, `expire_time`, `method`, `is_email_verify`, `reg_ip`, `node_speedlimit`, `node_connector`, `im_type`, `im_value`, `last_day_t`, `sendDailyMail`, `class`, `class_expire`, `expire_in`, `theme`, `ga_token`, `ga_enable`, `pac`, `remark`, `node_group`, `auto_reset_day`, `auto_reset_bandwidth`, `protocol`, `protocol_param`, `obfs`, `obfs_param`, `forbidden_ip`, `forbidden_port`, `disconnect_ip`, `is_hide`, `is_multi_user`, `telegram_id`, `discord`, `uuid`) VALUES
(1, '2005.00', 1, 'admin', '133814250@qq.com', NULL, '7f612821e71b30f9f2c4558f5c185f79', 'JRoWKy', 1584188679, 0, 0, 'A', 10995116277760, 40040, 1, 1, 0, '1989-06-04 00:05:00', 0, 1, 0, 1586998926, 0, '2019-10-25 22:51:16', 7, 0, 0, 'aes-128-cfb', 0, '127.0.0.1', '150.00', 6, 4, 'Lostinfever', 0, 0, 5, '2021-04-23 15:22:45', '2107-01-11 22:51:16', 'malio', 'A6ANV4W5JXBTP5DV', 0, NULL, '', 0, 1, '1000.00', 'auth_aes128_md5', '', 'http_simple', 'www.icloud.com', '', '', '', 0, 0, 776417883, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `user_subscribe_log`
--

CREATE TABLE `user_subscribe_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `user_id` int(11) NOT NULL COMMENT '用户 ID',
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱',
  `subscribe_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '获取的订阅类型',
  `request_ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '请求 IP',
  `request_time` datetime DEFAULT NULL COMMENT '请求时间',
  `request_user_agent` text COLLATE utf8mb4_unicode_ci COMMENT '请求 UA 信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户订阅日志';

-- --------------------------------------------------------

--
-- 表的结构 `user_token`
--

CREATE TABLE `user_token` (
  `id` int(11) NOT NULL,
  `token` varchar(256) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_traffic_log`
--

CREATE TABLE `user_traffic_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `u` bigint(20) NOT NULL,
  `d` bigint(20) NOT NULL,
  `node_id` int(11) NOT NULL,
  `rate` float NOT NULL,
  `traffic` varchar(32) NOT NULL,
  `log_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `agent_cash_log`
--
ALTER TABLE `agent_cash_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `status` (`status`),
  ADD KEY `sttime` (`sttime`),
  ADD KEY `account` (`account`(255));

--
-- 表的索引 `agent_config`
--
ALTER TABLE `agent_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `domain` (`domain`(255)),
  ADD KEY `level` (`level`);

--
-- 表的索引 `agent_error_log`
--
ALTER TABLE `agent_error_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- 表的索引 `agent_income`
--
ALTER TABLE `agent_income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `money` (`money`),
  ADD KEY `income` (`income`),
  ADD KEY `addtime` (`addtime`);

--
-- 表的索引 `agent_km`
--
ALTER TABLE `agent_km`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `km` (`km`),
  ADD KEY `status` (`status`),
  ADD KEY `addtime` (`addtime`),
  ADD KEY `batch` (`batch`);

--
-- 表的索引 `agent_level`
--
ALTER TABLE `agent_level`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `status` (`status`),
  ADD KEY `level` (`level`);

--
-- 表的索引 `agent_money_log`
--
ALTER TABLE `agent_money_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `addtime` (`addtime`),
  ADD KEY `memo` (`memo`(255)),
  ADD KEY `ip` (`ip`);

--
-- 表的索引 `alive_ip`
--
ALTER TABLE `alive_ip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `auto`
--
ALTER TABLE `auto`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `blockip`
--
ALTER TABLE `blockip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bought`
--
ALTER TABLE `bought`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `code`
--
ALTER TABLE `code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tradeno` (`tradeno`);

--
-- 表的索引 `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`name`) USING BTREE;

--
-- 表的索引 `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `detect_ban_log`
--
ALTER TABLE `detect_ban_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `detect_list`
--
ALTER TABLE `detect_list`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `detect_log`
--
ALTER TABLE `detect_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `disconnect_ip`
--
ALTER TABLE `disconnect_ip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `email_verify`
--
ALTER TABLE `email_verify`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `gconfig`
--
ALTER TABLE `gconfig`
  ADD PRIMARY KEY (`key`);

--
-- 表的索引 `link`
--
ALTER TABLE `link`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `login_ip`
--
ALTER TABLE `login_ip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `payback`
--
ALTER TABLE `payback`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `paylist`
--
ALTER TABLE `paylist`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `radius_ban`
--
ALTER TABLE `radius_ban`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `relay`
--
ALTER TABLE `relay`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `speedtest`
--
ALTER TABLE `speedtest`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ss_invite_code`
--
ALTER TABLE `ss_invite_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 表的索引 `ss_node`
--
ALTER TABLE `ss_node`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ss_node_info`
--
ALTER TABLE `ss_node_info`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ss_node_online_log`
--
ALTER TABLE `ss_node_online_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ss_password_reset`
--
ALTER TABLE `ss_password_reset`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `telegram_session`
--
ALTER TABLE `telegram_session`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `telegram_tasks`
--
ALTER TABLE `telegram_tasks`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `unblockip`
--
ALTER TABLE `unblockip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `uid` (`id`),
  ADD KEY `email` (`email`);

--
-- 表的索引 `user_subscribe_log`
--
ALTER TABLE `user_subscribe_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_token`
--
ALTER TABLE `user_token`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_traffic_log`
--
ALTER TABLE `user_traffic_log`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `agent_cash_log`
--
ALTER TABLE `agent_cash_log`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `agent_config`
--
ALTER TABLE `agent_config`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- 使用表AUTO_INCREMENT `agent_error_log`
--
ALTER TABLE `agent_error_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `agent_income`
--
ALTER TABLE `agent_income`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47415;

--
-- 使用表AUTO_INCREMENT `agent_km`
--
ALTER TABLE `agent_km`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1678;

--
-- 使用表AUTO_INCREMENT `agent_level`
--
ALTER TABLE `agent_level`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=411;

--
-- 使用表AUTO_INCREMENT `agent_money_log`
--
ALTER TABLE `agent_money_log`
  MODIFY `id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=848;

--
-- 使用表AUTO_INCREMENT `alive_ip`
--
ALTER TABLE `alive_ip`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286493;

--
-- 使用表AUTO_INCREMENT `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `auto`
--
ALTER TABLE `auto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `blockip`
--
ALTER TABLE `blockip`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `bought`
--
ALTER TABLE `bought`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- 使用表AUTO_INCREMENT `code`
--
ALTER TABLE `code`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- 使用表AUTO_INCREMENT `coupon`
--
ALTER TABLE `coupon`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `detect_ban_log`
--
ALTER TABLE `detect_ban_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `detect_list`
--
ALTER TABLE `detect_list`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `detect_log`
--
ALTER TABLE `detect_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `disconnect_ip`
--
ALTER TABLE `disconnect_ip`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `email_verify`
--
ALTER TABLE `email_verify`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- 使用表AUTO_INCREMENT `link`
--
ALTER TABLE `link`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- 使用表AUTO_INCREMENT `login_ip`
--
ALTER TABLE `login_ip`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=689;

--
-- 使用表AUTO_INCREMENT `payback`
--
ALTER TABLE `payback`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `paylist`
--
ALTER TABLE `paylist`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- 使用表AUTO_INCREMENT `radius_ban`
--
ALTER TABLE `radius_ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- 使用表AUTO_INCREMENT `relay`
--
ALTER TABLE `relay`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `shop`
--
ALTER TABLE `shop`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- 使用表AUTO_INCREMENT `speedtest`
--
ALTER TABLE `speedtest`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ss_invite_code`
--
ALTER TABLE `ss_invite_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- 使用表AUTO_INCREMENT `ss_node`
--
ALTER TABLE `ss_node`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- 使用表AUTO_INCREMENT `ss_node_info`
--
ALTER TABLE `ss_node_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2572438;

--
-- 使用表AUTO_INCREMENT `ss_node_online_log`
--
ALTER TABLE `ss_node_online_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2229101;

--
-- 使用表AUTO_INCREMENT `ss_password_reset`
--
ALTER TABLE `ss_password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `telegram_session`
--
ALTER TABLE `telegram_session`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1871;

--
-- 使用表AUTO_INCREMENT `telegram_tasks`
--
ALTER TABLE `telegram_tasks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `unblockip`
--
ALTER TABLE `unblockip`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- 使用表AUTO_INCREMENT `user_subscribe_log`
--
ALTER TABLE `user_subscribe_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_token`
--
ALTER TABLE `user_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_traffic_log`
--
ALTER TABLE `user_traffic_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=349185;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
