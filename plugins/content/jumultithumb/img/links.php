<?php
/**
* @package plg_jumultithumb
* @author Denys Nosov (http://denysdesign.com), Joomla! Ukraine (http://joomla-ua.org)
* @copyright (C) 2007-2012 Denys Nosov. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License version 2 or later
* @version 4.0
* @description: Multifunction plugin for creating thumbnails of images, using LightBox libraries and watermark
*/

defined('_JEXEC') or die;

class AutoLinks {

	function handleImgLinks(&$text, $title, $link, $onlyFirstImage) {

		if(empty($link)) {
			return $text;
		}

		$regex      = "/<img(.*?)>\s*(<\/img>)?/ ";

		$imgTitle   = $title;
		$imgAlt     = $title;

		$this->_replaceImg(NULL,$link,$imgTitle,$imgAlt);

		if($onlyFirstImage) {
			$text= preg_replace_callback($regex,array( &$this, '_replaceImg'), $text, 1);
		} else {
			$text= preg_replace_callback($regex,array( &$this, '_replaceImg'), $text);
		}

		return $text;
	}

	function _replaceImg($matches,$link=NULL,$title=NULL,$alt=NULL){

        static $_link;
		static $_title;
		static $_alt;

		if(isset($link)&&isset($title)) {
			$_link  = $link;

			$title  = str_replace("'",' ',$title);
			$title  = str_replace('"',' ',$title);

			$alt    = str_replace("'",' ',$alt);
			$alt    = str_replace('"',' ',$alt);

			$_alt   = $alt;
			$_title = $title;
		} else {
			$img    = $matches[0];

			if(strpos($img,' alt=')==false){
				$img = str_replace('<img ', '<img alt="'. $_alt .'" ', $img);
			} else {
			    $img = str_replace('<img ', '<img ', $img);
			}

			return '<a href="'. $_link .'" title="'. $_alt .'">'. $img .'</a>';
		}
	}

}