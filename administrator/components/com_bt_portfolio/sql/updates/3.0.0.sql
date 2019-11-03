DROP TABLE IF EXISTS `#__bt_portfolio_extrafields_values`;
CREATE TABLE `#__bt_portfolio_extrafields_values` (
  `portfolio_id` int(11) NOT NULL,
  `extrafields_id` int(11) NOT NULL,
  `value` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
UPDATE #__bt_portfolio_categories as p SET p.extra_fields = concat(',',p.extra_fields,',') where p.extra_fields !='' and p.extra_fields is not null;
UPDATE #__bt_portfolio_categories as p SET p.extra_fields = ',' where p.extra_fields ='' or p.extra_fields is null;