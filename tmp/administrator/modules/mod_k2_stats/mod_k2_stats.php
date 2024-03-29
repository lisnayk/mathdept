<?php
/**
 * @version		$Id: mod_k2_stats.php 1650 2012-09-27 10:40:04Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

if (K2_JVERSION != '15')
{
    $language = JFactory::getLanguage();
    $language->load('mod_k2.j16', JPATH_ADMINISTRATOR);
}

require_once (dirname(__FILE__).DS.'helper.php');

if ($params->get('latestItems', 1))
{
    $latestItems = modK2StatsHelper::getLatestItems();
}
if ($params->get('popularItems', 1))
{
    $popularItems = modK2StatsHelper::getPopularItems();
}
if ($params->get('mostCommentedItems', 1))
{
    $mostCommentedItems = modK2StatsHelper::getMostCommentedItems();
}
if ($params->get('latestComments', 1))
{
    $latestComments = modK2StatsHelper::getLatestComments();
}
if ($params->get('statistics', 1))
{
    $statistics = modK2StatsHelper::getStatistics();
}

require (JModuleHelper::getLayoutPath('mod_k2_stats'));
