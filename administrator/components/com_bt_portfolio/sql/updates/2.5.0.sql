alter table #__bt_portfolios ADD COLUMN `image` varchar(255);
alter table #__bt_portfolios ADD COLUMN `youembed` varchar(255);
UPDATE #__bt_portfolios as p SET p.youembed =  (SELECT youembed FROM #__bt_portfolio_images WHERE `default` = 1 and p.id=item_id ),image =  (SELECT filename FROM #__bt_portfolio_images  WHERE `default` = 1 and p.id=item_id)