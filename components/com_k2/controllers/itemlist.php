<?php
/**
 * @version		$Id: itemlist.php 1618 2012-09-21 11:23:08Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class K2ControllerItemlist extends K2Controller
{

    public function display($cachable = false, $urlparams = array())
    {
        $model = $this->getModel('item');
        $format = JRequest::getWord('format', 'html');
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $view = $this->getView('itemlist', $viewType);
        $view->setModel($model);
        $user = JFactory::getUser();
        if ($user->guest)
        {
            $cache = true;
        }
        else
        {
            $cache = false;
        }
        parent::display($cache);
    }

    function calendar()
    {
        require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_tools'.DS.'includes'.DS.'calendarClass.php');
        require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_tools'.DS.'helper.php');
        $mainframe = JFactory::getApplication();
        $month = JRequest::getInt('month');
        $year = JRequest::getInt('year');
        $months = array(JText::_('K2_JANUARY'), JText::_('K2_FEBRUARY'), JText::_('K2_MARCH'), JText::_('K2_APRIL'), JText::_('K2_MAY'), JText::_('K2_JUNE'), JText::_('K2_JULY'), JText::_('K2_AUGUST'), JText::_('K2_SEPTEMBER'), JText::_('K2_OCTOBER'), JText::_('K2_NOVEMBER'), JText::_('K2_DECEMBER'), );
        $days = array(JText::_('K2_SUN'), JText::_('K2_MON'), JText::_('K2_TUE'), JText::_('K2_WED'), JText::_('K2_THU'), JText::_('K2_FRI'), JText::_('K2_SAT'), );
        $cal = new MyCalendar;
        $cal->setMonthNames($months);
        $cal->setDayNames($days);
        $cal->category = JRequest::getInt('catid');
        $cal->setStartDay(1);
        if (($month) && ($year))
        {
            echo $cal->getMonthView($month, $year);
        }
        else
        {
            echo $cal->getCurrentMonthView();
        }
        $mainframe->close();
    }

    function module()
    {
        $document = JFactory::getDocument();
        $view = $this->getView('itemlist', 'raw');
        $model = $this->getModel('itemlist');
        $view->setModel($model);
        $model = $this->getModel('item');
        $view->setModel($model);
        $view->module();
    }

}
