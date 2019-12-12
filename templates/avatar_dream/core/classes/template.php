<?php
/**
 * @version		$Id: coolfeed.php 100 2012-04-14 17:42:51Z trung3388@gmail.com $
 * @copyright	JoomAvatar.com
 * @author		Nguyen Quang Trung
 * @link		http://joomavatar.com
 * @license		License GNU General Public License version 2 or later
 * @package		Avatar Dream Framework Template
 * @facebook 	http://www.facebook.com/pages/JoomAvatar/120705031368683
 * @twitter	    https://twitter.com/#!/JoomAvatar
 * @support 	http://joomavatar.com/forum/
 */

// No direct access
defined('_JEXEC') or die;

class AvatarTemplate extends JObject {
	
	protected $_path;
	protected $_jtemplate;
	protected $_jparams;
	public $_showcase;
	public $_css;
	public $_google;
	public $_mobile = false;
	public $_optimize = 0;
	
	public function __construct($context) 
	{
		$this->_jtemplate = $context;
		
		parent::__construct();
		
		$this->_jparams = $this->_jtemplate->params;
		$this->_showcase = ($this->_jparams->get('template_showcase')) ? $this->_jparams->get('template_showcase') : 'default';
		$this->_mobile = Avatar::isHandleDevice();
		$this->_optimize = $this->_jparams->get('optimize');
	}
	
	public function render($layout = 'default') 
	{
		// Try to find a favicon by checking the template and showcase root folder
		$favicon = JPATH_ROOT.'/templates/'.$this->_jtemplate->template.'/showcases/'. $this->_showcase.'/favicon.ico';
		
		if (JFile::exists($favicon)) {
			$this->_jtemplate->addFavicon($favicon);
		}
		
		// detect handle devices
		if (($this->_jparams->get('active_mobile') && $this->_mobile) || JRequest::getBool('avatar_mobile')) {
			$layout = $layout . '_mobile';
		}
		
		$this->_path = JPATH_THEMES.DS.$this->_jtemplate->template.DS.'core'.DS.'layouts'.DS.$layout.'.php';
		
		if (JFile::exists($this->_path)) 
		{
			ob_start();
			$template = $this->_jtemplate;
			require_once ($this->_path);
			return ob_get_clean();
		}
		
		return null;
	}
	
	/**
	 * get CSS files 
	 * @return string - link tag <link rel="stylesheet" 
	 */
	public function addCSSFiles()
	{
		
		$arrayCSS = $this->getCSSFiles();
		
		$css = '';
		
		foreach ($arrayCSS as $file) {
			$css .= '<link rel="stylesheet" href="'.$file.'" type="text/css"/>';
		}
		
		return $css;
	} 

	/**
	 * get CSS files
	 * @return array - list css files
	 */
	public function getCSSFiles() 
	{
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/system/css/system.css';
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/system/css/general.css';
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/system/css/editor.css';
		
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/'. $this->_jtemplate->template .'/core/assets/css/reset.css';
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/'. $this->_jtemplate->template .'/core/assets/css/text.css';
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/'. $this->_jtemplate->template .'/core/assets/css/layout.css';
		$this->_css['system'][] = $this->_jtemplate->baseurl .'/templates/'. $this->_jtemplate->template .'/core/assets/css/core_joomla.css';
		
		$this->_css['showcase']['default'][] = $this->_jtemplate->baseurl .'/templates/'.$this->_jtemplate->template.'/showcases/'. $this->_showcase.'/css/template.css';
		$this->_css['showcase']['default'][] = $this->_jtemplate->baseurl .'/templates/'.$this->_jtemplate->template.'/showcases/'. $this->_showcase.'/css/typography.css';
		
		if ($this->_jtemplate->direction == 'rtl') {
			$this->_css['showcase']['default'][] = $this->_jtemplate->baseurl .'/templates/'.$this->_jtemplate->template.'/showcases/'. $this->_showcase.'/css/rtl.css';
		}
		
		$this->_css['showcase']['customize'] = array();
		
		if ($this->_jparams->get('customize_css') != '') 
		{
			$files = explode(',', $this->_jparams->get('customize_css'));
			
			foreach ($files as $file) 
			{
				$path = '/templates/'.$this->_jtemplate->template.'/showcases/'. $this->_showcase.'/css/customize/'.trim($file);
			
				if (JFile::exists(JPATH_ROOT.$path)) {
					$this->_css['showcase']['customize'][] = $this->_jtemplate->baseurl .$path;	
				}
			}
		}
		
		return array_unique(array_merge($this->_css['system'], $this->_css['showcase']['default'], $this->_css['showcase']['customize']));	
	}
	
	/**
	 * add Google Analytics Code
	 * @return string - javascript code
	 */
	public function addGoogleAnalytics() 
	{
		$this->_google->analytics = '';
		if ($this->_jparams->get('google_analytics') != '') {
				$this->_google->analytics = '<script type="text/javascript">
				  var _gaq = _gaq || [];
				  _gaq.push(["_setAccount", "'.$this->_jparams->get('google_analytics').'"]);
				  _gaq.push(["_trackPageview"]);

				  (function() {
					var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
					ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
					var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
				  })();

				</script>';
		}
		
		return $this->_google->analytics;
	}
	
	/**
	 * Load Google Font Library
	 * @return string - link to google
	 */
	public function addGoogleFont()
	{
		if ($this->_jparams->get('google_font_api')) 
		{
			$googleFont 			= 'http://fonts.googleapis.com/css?family=';
			$gFontSubset 			= '';
			$gFontMainMenu 			= json_decode($this->_jparams->get('google_font_main_menu'));
			
			$this->_google->font->mainMenuFamily = '';
			$this->_google->font->subMenuFamily	= '';
			$this->_google->font->pageHeadingFamily = '';
			$this->_google->font->moduleHeadingFamily = '';
			$this->_google->font->contentFamily= '';
			$this->_google->font->contentHeadingFamily = '';
			
			if ($gFontMainMenu) {
				$this->_google->font->mainMenuFamily	= $gFontMainMenu[0];
				$gFontSubset 			= @$gFontMainMenu[2];
				$gFontMainMenu 			= urlencode(@$gFontMainMenu[0]) . ':' . urlencode(@$gFontMainMenu[1]);
				$googleFont 			.= $gFontMainMenu;
			}
			
			$gFontSubMenu 			= json_decode($this->_jparams->get('google_font_submenu'));
			if ($gFontSubMenu) {
				$this->_google->font->subMenuFamily		= $gFontSubMenu[0];
				$gFontSubset 			= @$gFontSubMenu[2];
				$gFontSubMenu			= urlencode(@$gFontSubMenu[0]). ':' . urlencode(@$gFontSubMenu[1]);
				$googleFont 			.= '|'.$gFontSubMenu;
			}
			
			$gFontPageHeading 		= json_decode($this->_jparams->get('google_font_page_heading'));
			if ($gFontPageHeading) {
				$this->_google->font->pageHeadingFamily = $gFontPageHeading[0];
				$gFontSubset 			= @$gFontPageHeading[2];
				$gFontPageHeading		= urlencode(@$gFontPageHeading[0]). ':' . urlencode(@$gFontPageHeading[1]);
				$googleFont 			.= '|'.$gFontPageHeading;
			}
			
			$gFontModuleHeading 		= json_decode($this->_jparams->get('google_font_module_heading'));
			if ($gFontModuleHeading) {
				$this->_google->font->moduleHeadingFamily	= $gFontModuleHeading[0];
				$gFontSubset 				= urlencode(@$gFontModuleHeading[2]);
				$gFontModuleHeading			= urlencode(@$gFontModuleHeading[0]). ':' . urlencode(@$gFontModuleHeading[1]);
				$googleFont 				.= '|'.$gFontModuleHeading;
			}
			
			$gFontContent 			= json_decode($this->_jparams->get('google_font_content'));
			if ($gFontContent) {
				$this->_google->font->contentFamily 	= $gFontContent[0];
				$gFontSubset 			= @$gFontContent[2];
				$gFontContent			= urlencode(@$gFontContent[0]). ':' . urlencode(@$gFontContent[1]);
				$googleFont				.= '|'.$gFontContent;
			}
			
			$gFontContentHeading 		= json_decode($this->_jparams->get('google_font_content_heading'));
			if ($gFontContentHeading) {
				$this->_google->font->contentHeadingFamily 	= $gFontContentHeading[0];
				$gFontSubset				= @$gFontContentHeading[2];
				$gFontContentHeading		= urlencode(@$gFontContentHeading[0]). ':' . urlencode(@$gFontContentHeading[1]);
				$googleFont					.= '|'.$gFontContentHeading;
			}
			
			if ($gFontSubset) {
				$googleFont 			.= '&subset='. $gFontSubset;
			}
			
			$this->_google->font->url = $googleFont;
			
			return '<link href="'.$googleFont.'" rel="stylesheet" type="text/css">';
		}
		
		return '';
	}
	
	/**
	 * 
	 * add CSS when using the Google Font
	 */
	public function addGoogleFontCSS() 
	{
		$css = '';
		
		if ($this->_jparams->get('google_font_api')) 
		{
			if ($this->_jparams->get('google_font_content')) {
			  	$css .= 'body{ font-family: '.$this->_google->font->contentFamily.'}'."\n";
			}
			
			if ($this->_jparams->get('google_font_content_heading')) {
				$css .= 'h1,h2,h3,h4,h5,h6{ font-family: '. $this->_google->font->contentHeadingFamily .';}'."\n";
			}
			
			if ($this->_jparams->get('google_font_main_menu')) {
				$css .= '.avatar-main-menu { font-family: '. $this->_google->font->mainMenuFamily.';}'."\n";
			}
			
			if ($this->_jparams->get('google_font_submenu')) {
				$css .= '.avatar-submenu, .avatar-tree-menu, .avatar-slide-menu { font-family: '. $this->_google->font->subMenuFamily .';}'."\n";
			}
			if ($this->_jparams->get('google_font_page_heading')) {
				$css .= '.avatar-page-heading { font-family: '. $this->_google->font->pageHeadingFamily .';}'."\n";
			}
			if ($this->_jparams->get('google_font_module_heading')) {
			 	$css .= '.avatar-module-heading { font-family: '. $this->_google->font->moduleHeadingFamily .';}'."\n";
			}
		}		
		
		return $css;
	}
	
	/**
	 *  add CSS code base on params
	 */
	public function addCSSCode()
	{
		$css = '';
		
		if ($this->_jparams->get('link_color') != ''){
			$css = 'a:link, a:visited { color:'. $this->_jparams->get('link_color'). ';}'."\n";
		}
		
		if ($this->_jparams->get('hover_color') != '') {
			$css .= 'a:hover { color: '. $this->_jparams->get('hover_color').';}'."\n";
		}
		
		if ($this->_jparams->get('body_font')) {
			$css .= 'body{ font-family: '. $this->_jparams->get('body_font') .';}'."\n";
		}
		
		if ($this->_jparams->get('menu_font')) {
			$css .= '.avatar-main-menu{ font-family: '. $this->_jparams->get('menu_font'). ';}'."\n";
		}
		
		$css .= $this->addGoogleFontCSS();
		
		if ($this->_jparams->get('heading_1')) {
			$css .= 'h1{' . $this->_jparams->get('heading_1') .';}'."\n";
		}
		
		if ($this->_jparams->get('heading_2')) {
			$css .= 'h2{' . $this->_jparams->get('heading_2') .';}'."\n";
		}
		
		if ($this->_jparams->get('heading_3')) {
			$css .= 'h3{'. $this->_jparams->get('heading_3') .';}'."\n";
		}
		
		if ($this->_jparams->get('heading_4')) {
			$css .= 'h4{' . $this->_jparams->get('heading_4') .';}'."\n";
		}
		
		if ($this->_jparams->get('heading_5')) {
			$css .= 'h5{' . $this->_jparams->get('heading_5') .';}'."\n";
		}
		
		if ($this->_jparams->get('heading_6')) {
			$css .= 'h6{' . $this->_jparams->get('heading_6') .';}'."\n";
		}
		
		return $css;
	}

	/**
	 * 
	 * hide main component base on ItemID
	 */
	public function hideComponentBaseOnItemID() 
	{
		if ($this->_jparams->get('hide_menu_items')) 
		{	
			$menu = JSite::getMenu();
			
			if (isset($menu->getActive()->id)) 
			{
				$itemID = $menu->getActive()->id;
				$hideMenuItems = $this->_jparams->get('hide_menu_items');
				
				if ($hideMenuItems == '') return false;
				
				if (!is_array($hideMenuItems)) $hideMenuItems = array($hideMenuItems);
				
				if (in_array($itemID, $hideMenuItems)) return true;
			} 
		}	
		
		return false;
	}
	
	/**
	 *  add header data
	 */
	public function addHead() 
	{
		$head = '';
		
		if ($this->_optimize == 0 || $this->_optimize == 2) {
			$head = $this->addCSSFiles();
		}
		
		$head .= $this->addGoogleFont();
		$head .= '<style type="text/css">'.$this->addCSSCode().'</style>';
			
		return $head;
	}
	
	/**
	 * optimize the css and js
	 */
	public function optimize() 
	{
		if ($this->_optimize) {
			Avatar::import('core.helpers.compress');
			$compressObj = new AvatarCompress($this);
			$compressObj->compress();	
		}
	}
	
	/**
	 *  get Doctype
	 */
	public function getDocType() 
	{
		$doctype = '';
		switch ($this->_jparams->get('doctype')) {
			case '1':
				$doctype = '<!DOCTYPE html>';
				break;
			case '2':
				$doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
				break;
			case '3':
				$doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
				break;
			case '4':
				$doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
				break;
			case '5':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
				break;
			case '6':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				break;
			case '7':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
				break;
			case '8':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
				break;
		}

		return $doctype;	
	}
}