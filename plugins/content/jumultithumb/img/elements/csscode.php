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


class JFormFieldCSSCode extends JFormField
{

	protected $type = 'CSSCode';

	protected function getInput()
	{

		$html = '<textarea style="width: 350px; height: 250px;">img[style $=\'width\'] {
	width: 150px;
	height: auto!important;
}
img{
	width: 150px!important;
	height: auto!important;
	padding: 2px;
	border: red 1px dashed;
	margin: 3px 18px;
}
img.noimage{
	width: inherit!important;
	height: auto!important;
	border: #0000cd 1px dashed!important;
}</textarea>';

		return $html;
	}
}