alter table #__bt_portfolio_images ADD COLUMN `youid` varchar(200) after filename;
alter table #__bt_portfolio_images ADD COLUMN `youdesc` text after youid;
alter table #__bt_portfolio_images ADD COLUMN `youembed` text after youdesc;