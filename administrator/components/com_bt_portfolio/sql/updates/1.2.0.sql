alter table #__bt_portfolios ADD COLUMN alias VARCHAR(255) after title;
update #__bt_portfolios  set alias = LOWER(REPLACE(REPLACE(REPLACE(title,' ','-'),'\'',''),'/',''));
update #__bt_portfolio_categories  set alias = LOWER(REPLACE(REPLACE(REPLACE(title,' ','-'),'\'',''),'/',''));