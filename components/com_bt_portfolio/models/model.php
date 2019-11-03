<?php
defined('_JEXEC') or die ;
jimport('joomla.application.component.model');

if (version_compare(JVERSION, '3.0', 'ge'))
{
    class BtPortfolioModel extends JModelLegacy
    {
        public static function addIncludePath($path = '', $prefix = '')
        {
            return parent::addIncludePath($path, $prefix);
        }

    }

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
    class BtPortfolioModel extends JModel
    {
        public static function addIncludePath($path = '', $prefix = '')
        {
            return parent::addIncludePath($path, $prefix);
        }

    }

}
else
{
    class BtPortfolioModel extends JModel
    {
        public function addIncludePath($path = '', $prefix = '')
        {
            return parent::addIncludePath($path);
        }

    }

}
?>
