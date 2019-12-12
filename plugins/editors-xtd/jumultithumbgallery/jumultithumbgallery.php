<?php
/**
* @package plg_jumultithumb
* @author Denys Nosov (http://denysdesign.com), Joomla! Ukraine (http://joomla-ua.org)
* @copyright (C) 2007-2012 Denys Nosov. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License version 2 or later
* @version 4.0
* @description: Multifunction plugin for creating thumbnails of images, using LightBox libraries and watermark
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgButtonJUmultithumbgallery extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onDisplay($name)
	{
        $lang =& JFactory::getLanguage();
        $lang->load('plg_content_jumultithumb', JPATH_ADMINISTRATOR);

		$link = '../plugins/editors-xtd/jumultithumbgallery/form.php';

		JHtml::_('behavior.modal');

		$button = new JObject;
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_JUMULTITHUMB').' - '.JText::_('COM_PLUGINS_GALLERY_FIELDSET_LABEL'));
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 580, y: 190}}");

		return $button;
	}
}
