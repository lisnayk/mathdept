<?php
/**
 * @version		$Id: view.html.php 1618 2012-09-21 11:23:08Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class K2ViewSettings extends K2View {

	function display($tpl = null) {

		JHTML::_('behavior.tooltip');
		jimport('joomla.html.pane');
		$model = &$this->getModel();
		$params = &$model->getParams();
		$this->assignRef('params', $params);
		$pane = & JPane::getInstance('Tabs');
		$this->assignRef('pane', $pane);
		parent::display($tpl);
	}

}
