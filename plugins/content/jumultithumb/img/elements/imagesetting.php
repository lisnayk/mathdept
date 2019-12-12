<?php
/**
* @package plg_jumultithumb
* @author Denys Nosov (http://denysdesign.com), Joomla! Ukraine (http://joomla-ua.org)
* @copyright (C) 2007-2012 Denys Nosov. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License version 2 or later
* @version 4.0
* @description: Multifunction plugin for creating thumbnails of images, using LightBox libraries and watermark
*/

error_reporting(0);

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');


class JFormFieldImageSetting extends JFormField
{

	protected $type = 'ImageSetting';

	protected function getInput()
	{
		JHtml::_('behavior.modal', 'a.modal');

		$script = array();
		$script[] = '	function jSelectArticle_'.$this->id.'(id, title, catid, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$html	= array();
        $uri = JFactory::getURI();
		$link	= str_replace('administrator/', '', JURI::base() ).'plugins/content/jumultithumb/img/imgsetting.php';

		$html[] = '<div style="clear: both;"></div><div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '	<a class="modal" title="'.JText::_('PLG_JUMULTITHUMB_IMAGE_CONF').'" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 980, y: 590}}">'.JText::_('PLG_JUMULTITHUMB_IMAGE_CONF').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '	<a class="modal" title="'.JText::_('PLG_JUMULTITHUMB_NOTICE12').'" href="'.$link.'?clearcache=1" rel="{handler: \'iframe\', size: {x: 330, y: 130}}">'.JText::_('PLG_JUMULTITHUMB_NOTICE12').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '	<a class="modal" title="'.JText::_('PLG_JUMULTITHUMB_DONATE').'" href="'. str_replace('administrator/', '', JURI::base() ).'plugins/content/jumultithumb/img/elements/donate.php" rel="{handler: \'iframe\', size: {x: 400, y: 450}}">'.JText::_('PLG_JUMULTITHUMB_DONATE').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		$html[] = '<p style="clear: both;" />';

		return implode("\n", $html);
	}
}