<?php
/**
* @package plg_jumultithumb
* @author Denys Nosov (http://denysdesign.com), Joomla! Ukraine (http://joomla-ua.org)
* @copyright (C) 2007-2012 Denys Nosov. All rights reserved.
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/ Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License
* @version 4.0
* @description: Multifunction plugin for creating thumbnails of images, using LightBox libraries and watermark
*/

// No direct access.
defined('_JEXEC') or die;

error_reporting(0);

$app = JFactory::getApplication();

if ($app->getName() != 'site') {
    return true;
}

jimport('joomla.plugin.plugin');

require_once (dirname(__FILE__) . DS . 'img' . DS . 'links.php');
require_once JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';

class plgContentjumultithumb extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();

        $task = JRequest::getVar('task');

        if ($task !=='file.upload') {
            $this->addToHead();
        }
	}

    public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)
    {
        $app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return true;
		}

        $option = JRequest :: getCmd('option');
        $view = JRequest :: getCmd('view');

        $onlyForComContent = $this->params->get('Only_For_Com_Content');
        if ($onlyForComContent && ($option != 'com_content')) {
            return;
        }

        if ($view == 'article') {
            return;
        }

        $onlyFirstImage = $this->params->get('Only_For_First_Image');

        $autolinks = new AutoLinks($imgTitlePrefix, $imgAltPrefix, $onlyFirstImage);

        $link = "";

        if ($article->params->get('access-view')) {
            $link       = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid));
        } else {
            $menu       = JFactory::getApplication()->getMenu();
            $active     = $menu->getActive();
            $itemId     = $active->id;
            $link1      = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
            $returnURL  = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid));
            $link       = new JURI($link1);
            
            $link->setVar('return', base64_encode($returnURL));
        }

        $article->introtext = $autolinks->handleImgLinks($article->introtext, $article->title, $link, $onlyFirstImage);

    }

    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        $app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return true;
		}

        $option = JRequest::getString('option');
    	$view   = JRequest::getString('view');
        $layout = JRequest::getString('layout');

        if( $option!= 'com_content') {
            return true;
    	}

        $document    = & JFactory::getDocument();

        // Gallery
        include (dirname(__FILE__) . DS . 'gallery.php');

        $this->regex="/<img(.*?)>\s*(<\/img>)?/";

        $text = preg_replace('#<img(.*?)mce_src="(.*?)"(.*?)>#s', "<img\\1\\3>", $article->text);
       	$text = preg_replace_callback( $this->regex, array($this,'JUMultithumbReplacer'), $text );

        $text = str_replace(array('alt="-"', 'alt=""'), 'alt="'. $article->title .'"', $text);
        $text = str_replace(array('title="-"', 'title=""'), 'title="'. $article->title .'"', $text);

        if(
            ($this->params->get('only_image_blog') == '1' && ($layout == 'blog')) ||
            ($this->params->get('only_image_category') == '1' && ($view == 'categories' && !($layout))) ||
            ($this->params->get('only_image_featured') == '1' && $view == 'featured')
            ){
            preg_match_all('/(<\s*img\s+src\s*="\s*("[^"]*"|\'[^\']*\'|[^"\s]+).*?>)/i', $text, $result);
            $img = $result[1][0];
            $article->text = $img;
        } else {
            $article->text = $text;
        } 
    }

    function addToHead() {

        $app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return true;
		}

        $option = JRequest::getString('option');
    	$view   = JRequest::getString('view');
        $layout = JRequest::getString('layout');

        $document    = & JFactory::getDocument();

        /*
        if( $option!= 'com_content') {
            return true;
    	} */

        if (
            $this->params->get('uselightbox','1') == '1' &&
            JRequest::getString('print') != '1'
            ) {

            switch ( $this->params->get('selectlightbox') ) {
                case 'jqlightbox':
                case 'colorbox':
                case 'fancybox':
                    if($this->params->get("jujq") == '0') {
                        $jujqinstall = '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script><script type="text/javascript">!window.jQuery AND document.write(unescape(\'%3Cscript src="'. JURI::base() .'plugins/content/jumultithumb/media/jquery-1.7.1.min.js"%3E%3C/script%3E\'))</script>';
                    } else {
                        $jujqinstall = "";
                    }
                break;

                default:
                    $jujqinstall = "";
                break;
            }

            switch ( $this->params->get('selectlightbox') ) {
                case 'customjs':
                    if($this->params->get('customjsparam')){
                        $jsparams = "\n            ". $this->params->get('customjsparam');
                    } else {
                        $jsparams = "\r";
                    }
                    $juhead = $jujqinstall .$jsparams;
                break;

                case 'slimbox':
                    if($this->params->get('jqlbparam')){
                        $jsparams =  str_replace('<br />', '', $this->params->get('slimboxparam'));
                    } else {
                        $jsparams = "\r";
                    }
                    $juhead = '<link type="text/css" rel="stylesheet" href="'. JURI::base() .'plugins/content/jumultithumb/media/slimbox/css/slimbox.css" />
    <script type="text/javascript" src="'. JURI::base() .'plugins/content/jumultithumb/media/slimbox/js/slimbox.js"></script>
    <script type="text/javascript">
    Slimbox.scanPage = function() {
    	$$($$(document.links).filter(function(el) {
    		return el.rel && el.rel.test(/^lightbox/i);
    	})).slimbox({
            '. $jsparams .'
        });
    };
    window.addEvent("domready", Slimbox.scanPage);
    </script>
                    ';
                break;

                case 'jqlightbox':
                    if($this->params->get('jqlbparam')){
                        $jsparams = ",\n            ". str_replace('<br />', '', $this->params->get('jqlbparam'));
                    } else {
                        $jsparams = "\r";
                    }
                    $juhead = '<link type="text/css" rel="stylesheet" href="'. JURI::base() .'plugins/content/jumultithumb/media/jqlightbox/css/lightbox.css" />
    '. $jujqinstall .'
    <script type="text/javascript" src="'. JURI::base() .'plugins/content/jumultithumb/media/jqlightbox/js/jquery.lightbox-0.5.pack.js"></script>
    <script type="text/javascript">
    jQuery.noConflict();
    jQuery(function() {
        jQuery(\'a.lightbox\').lightBox({
            imageLoading: \''. JURI::base() .'plugins/content/jumultithumb/media/jqlightbox/images/lightbox-ico-loading.gif\',
            imageBtnClose: \''. JURI::base() .'plugins/content/jumultithumb/media/jqlightbox/images/lightbox-btn-close.gif\',
            imageBtnPrev: \''. JURI::base() .'plugins/content/jumultithumb/media/jqlightbox/images/lightbox-btn-prev.gif\',
            imageBtnNext: \''. JURI::base() .'plugins/content/jumultithumb/media/jqlightbox/images/lightbox-btn-next.gif\''. $jsparams .'
        });
    });
    </script>
                    ';
                break;

                case 'colorbox':
                    if($this->params->get('colorboxparam')){
                        $jsparams = str_replace('<br />', '', $this->params->get('colorboxparam'));
                    } else {
                        $jsparams = "\r";
                    }
                    $juhead = '<link type="text/css" rel="stylesheet" href="'. JURI::base() .'plugins/content/jumultithumb/media/colorbox/'. $this->params->get('colorboxstyle') .'/colorbox.css" />
    '. $jujqinstall .'
    <script type="text/javascript" src="'. JURI::base() .'plugins/content/jumultithumb/media/colorbox/jquery.colorbox-min.js"></script>
    <script type="text/javascript">
    jQuery.noConflict();
    jQuery(function() {
        jQuery("a.lightbox").colorbox({
            '. $jsparams .'
        });
    });
    </script>
                    ';
                break;

                case 'fancybox':
                    if($this->params->get('fancyboxparam')){
                        $jsparams = str_replace('<br />', '', $this->params->get('fancyboxparam'));
                    } else {
                        $jsparams = "\r";
                    }
                    $juhead  = '<link type="text/css" rel="stylesheet" href="'. JURI::base() .'plugins/content/jumultithumb/media/fancybox/jquery.fancybox.css" />';
                    $juhead .= $jujqinstall;
                    $juhead .= '<script type="text/javascript" src="'. JURI::base() .'plugins/content/jumultithumb/media/fancybox/jquery.fancybox.pack.js"></script>';

                    if($this->params->get('fancyboxbuttons') == '1') {
                        $juhead .= '<link rel="stylesheet" type="text/css" href="'. JURI::base() .'plugins/content/jumultithumb/media/fancybox/helpers/jquery.fancybox-buttons.css" /><script type="text/javascript" src="'. JURI::base() .'plugins/content/jumultithumb/media/fancybox/helpers/jquery.fancybox-buttons.js"></script>';
                    }

                    if($this->params->get('fancyboxthumbs') == '1') {
                        $juhead .= '<link rel="stylesheet" type="text/css" href="'. JURI::base() .'plugins/content/jumultithumb/media/fancybox/helpers/jquery.fancybox-thumbs.css" /><script type="text/javascript" src="'. JURI::base() .'plugins/content/jumultithumb/media/fancybox/helpers/jquery.fancybox-thumbs.js"></script>';
                    }

                    $juhead .= '<script type="text/javascript">
    jQuery.noConflict();
    jQuery(function() {
        jQuery("a.lightbox").fancybox({
            '. $jsparams .'
        });
    });
    </script>
                    ';
                break;

                default:
                case 'jmodal':
                    JHTML::_('behavior.modal');
                break;
            }
            $document->addCustomTag($juhead);
        }

        if ($this->params->get('use_css') == 1 ) {
            $juheadcss = '<link type="text/css" rel="stylesheet" href="'. JURI::base() .'plugins/content/jumultithumb/assets/style.css" />';
            $document->addCustomTag($juheadcss);
        }
    }

    function JUMultithumbReplacer ( &$matches ) {

        $view   = JRequest::getString('view');
        $layout = JRequest::getString('layout');
        $Itemid = JRequest::getString('Itemid');

        switch ( $this->params->get('selectlightbox') ) {
            case 'slimbox':
                $lightbox = 'class="lightbox" rel="lightbox-jumultithumb"';
            break;

            case 'jqlightbox':
                $lightbox = 'class="lightbox" rel="lightbox"';
            break;

            case 'colorbox':
                $lightbox = 'class="lightbox" rel="lightbox[gall]"';
            break;

            case 'fancybox':
                $lightbox = 'class="lightbox" data-fancybox-group="jumultithumb"';
            break;

            case 'jmodal':
                $lightbox = 'class="modal" rel="{handler: \'image\', marginImage: {x: 50, y: 50}}"';
            break;

            default:
            case 'customjs':
                $lightbox = 'class="lightbox"';
            break;
        }

        $juimgresmatche = $matches[1];

        preg_match('#src="(.*?)"#s',$juimgresmatche,$imgsource);
        $imgsource  = $imgsource[1];
        $imgsource  = str_replace(JURI::base(), '', $imgsource);
        $originalsource  = $imgsource;

        if(preg_match('#class="(.*?)"#s',$juimgresmatche,$imgclass)) {
            $imgclass   = $imgclass[1];
        }

        if(preg_match('#alt="(.*?)"#s',$juimgresmatche,$imgalt)) {
            $imgalt    = $imgalt[1];
        }

        if(preg_match('#title="(.*?)"#s',$juimgresmatche,$imgtitle)) {
            $imgtitle   = $imgtitle[1];
        }

        if(preg_match('#align="(.*?)"#s',$juimgresmatche,$imgalign)) {
            $imgalign   = $imgalign[1];
        }

        if(preg_match('#style="(.*?)float:(.*?);(.*?)"#s',$juimgresmatche,$imgstyle)) {
            $imgstyle   = 'float: '. $imgstyle[2] .';';
        }

        if(preg_match('#style="(.*?)float:(.*?);(.*?)"#s',$juimgresmatche,$imgstyle2)) {
            $imgstyle2   = 'float: '. $imgstyle2[2] .';';
        }

        if( $imgalign ){
            $img_class    = ' ju'. str_replace(' ', '', preg_replace('#float:(.*?);#is', '\\1', $imgalign));
        } elseif( $imgstyle ){
            $img_class    = ' ju'. str_replace(' ', '', preg_replace('#float:(.*?);#is', '\\1', $imgstyle2));
        } else {
            $img_class = '';
        }

        // attributes
        $img_class  = $img_class . ' juimg-'. $view;
        $img_alt    = ( $imgalt ? ' alt="'. $imgalt .'"' : ' alt=""' );
        $img_title  = ( $imgtitle ? ' title="'. $imgtitle .'"' : ' title="'. $imgalt .'"' );
        $img_style   = ( $imgstyle ? ' style="'. $imgstyle .'"' : '' );

        $juimgresmatche  = str_replace(' /', '', $juimgresmatche);

        if ( $this->params->get('resall') == '0' ) {

            if ($imgclass != "juimage"){
                $limage = '<img '. $juimgresmatche . $img_style .' class="'. $img_class .'" />';
                return $limage;
            } elseif ($imgclass == "noimage"){
                $limage = '<img '. $juimgresmatche . $img_style .' class="'. ( $img_class ? $img_class .' ' : '' ) .'noimage" />';
                return $limage;
            }

        } else {

            if( $imgclass == "jugallery") {
                //$juimgresmatche  = str_replace(' /', '', $juimgresmatche);
                $limage = '<img '. $juimgresmatche .' />';
                return $limage;
            }

            if (
                $imgclass == "noimage" ||
                $imgclass == "nothumbnail" ||
                $imgclass == $this->params->def('noimage_class')
                ) {
                $juimgresmatche  = str_replace( array(' class="noimage"', ' class="nothumbnail"', ' class="'. $this->params->def('noimage_class') .'"' ), '', $juimgresmatche);
                //$juimgresmatche  = str_replace(' /', '', $juimgresmatche);

                $limage = '<img '. $juimgresmatche . $img_style .' class="'. ( $img_class ? $img_class .' ' : '' ) .'noimage" />';
                return $limage;
            }
        }

        if($view == 'category' && ($layout == 'blog')) {
            if(in_array($Itemid, $this->params->get('menu_item1', array()) )) {
                $b_newwidth       = $this->params->get('b_widthnew1');
                $b_newheight      = $this->params->get('b_heightnew1');
                $b_newcropzoom    = $this->params->get('b_cropzoomnew1');
                $b_aoenew         = $this->params->get('b_aoenew1');
                $b_sxnew          = $this->params->def('b_sxnew1');
                $b_synew          = $this->params->def('b_synew1');
            } elseif(in_array($Itemid, $this->params->get('menu_item2', array()) )) {
                $b_newwidth       = $this->params->get('b_widthnew2');
                $b_newheight      = $this->params->get('b_heightnew2');
                $b_newcropzoom    = $this->params->get('b_cropzoomnew2');
                $b_aoenew         = $this->params->get('b_aoenew2');
                $b_sxnew          = $this->params->def('b_sxnew2');
                $b_synew          = $this->params->def('b_synew2');
            } elseif(in_array($Itemid, $this->params->get('menu_item3', array()) )) {
                $b_newwidth       = $this->params->get('b_widthnew3');
                $b_newheight      = $this->params->get('b_heightnew3');
                $b_newcropzoom    = $this->params->get('b_cropzoomnew3');
                $b_aoenew         = $this->params->get('b_aoenew3');
                $b_sxnew          = $this->params->def('b_sxnew3');
                $b_synew          = $this->params->def('b_synew3');
            } elseif(in_array($Itemid, $this->params->get('menu_item4', array()) )) {
                $b_newwidth       = $this->params->get('b_widthnew4');
                $b_newheight      = $this->params->get('b_heightnew4');
                $b_newcropzoom    = $this->params->get('b_cropzoomnew4');
                $b_aoenew         = $this->params->get('b_aoenew4');
                $b_sxnew          = $this->params->def('b_sxnew4');
                $b_synew          = $this->params->def('b_synew4');
            } elseif(in_array($Itemid, $this->params->get('menu_item5', array()) )) {
                $b_newwidth       = $this->params->get('b_widthnew5');
                $b_newheight      = $this->params->get('b_heightnew5');
                $b_newcropzoom    = $this->params->get('b_cropzoomnew5');
                $b_aoenew         = $this->params->get('b_aoenew5');
                $b_sxnew          = $this->params->def('b_sxnew5');
                $b_synew          = $this->params->def('b_synew5');
            } else {
                $b_newwidth       = $this->params->get('b_width');
                $b_newheight      = $this->params->get('b_height');
                $b_newcropzoom    = $this->params->get('b_cropzoom');
                $b_aoenew         = $this->params->get('b_aoe');
                $b_sxnew          = $this->params->def('b_sx');
                $b_synew          = $this->params->def('b_sy');
            }

            $parsed_url = parse_url($imgsource);
            $imgsrc    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                '&w='. $b_newwidth .
                '&h='. $b_newheight .
                '&q='.$this->params->get('quality') .
                ( $b_newcropzoom == '1' ? '&zc=1' : '' ) .
                ( $b_aoenew == '1' ? '&aoe=1' : '' )
                ;

            //$imgsrcno64 = $imgsrc;
            $imgsrc = base64_encode($imgsrc);

            if($this->params->get('secimg') == '1') {
                $image = JURI::base() .'plugins/content/jumultithumb/img/'. $imgsrc .'.jpg';
            } else {
                $image = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. $imgsrc;
            }

            list($width, $height, $type, $attr) = getimagesize($image);

            $limage = '<img src="'. $image .'"'. $img_alt . $img_title . $img_style .' '. $attr .' class="juimage'. $img_class .'" />';

        } elseif(
            $view == 'article' ||
            $view == 'categories' ||
            ($view == 'category' && (!$layout))
            ) {

            if( $view == 'article' ) {

                if(in_array($Itemid, $this->params->get('menu_item1', array()) )) {
                    $newmaxwidth    = $this->params->get('maxwidthnew1');
                    $newmaxheight   = $this->params->get('maxheightnew1');
                    $newwidth       = $this->params->get('widthnew1');
                    $newheight      = $this->params->get('heightnew1');
                    $newcropzoom    = $this->params->get('cropzoomnew1');
                    $newaoe         = $this->params->get('aoenew1');
                    $newsx          = $this->params->def('sxnew1');
                    $newsy          = $this->params->def('synew1');
                    $newnoresize    = $this->params->get('noresizenew1');
                    $newnofullimg   = $this->params->get('nofullimgnew1');
                } elseif(in_array($Itemid, $this->params->get('menu_item2', array()) )) {
                    $newmaxwidth    = $this->params->get('maxwidthnew2');
                    $newmaxheight   = $this->params->get('maxheightnew2');
                    $newwidth       = $this->params->get('widthnew2');
                    $newheight      = $this->params->get('heightnew2');
                    $newcropzoom    = $this->params->get('cropzoomnew2');
                    $newaoe         = $this->params->get('aoenew2');
                    $newsx          = $this->params->def('sxnew2');
                    $newsy          = $this->params->def('synew2');
                    $newnoresize    = $this->params->get('noresizenew2');
                    $newnofullimg   = $this->params->get('nofullimgnew2');
                } elseif(in_array($Itemid, $this->params->get('menu_item3', array()) )) {
                    $newmaxwidth    = $this->params->get('maxwidthnew3');
                    $newmaxheight   = $this->params->get('maxheightnew3');
                    $newwidth       = $this->params->get('widthnew3');
                    $newheight      = $this->params->get('heightnew3');
                    $newcropzoom    = $this->params->get('cropzoomnew3');
                    $newaoe         = $this->params->get('aoenew3');
                    $newsx          = $this->params->def('sxnew3');
                    $newsy          = $this->params->def('synew3');
                    $newnoresize    = $this->params->get('noresizenew3');
                    $newnofullimg   = $this->params->get('nofullimgnew3');
                } elseif(in_array($Itemid, $this->params->get('menu_item4', array()) )) {
                    $newmaxwidth    = $this->params->get('maxwidthnew4');
                    $newmaxheight   = $this->params->get('maxheightnew4');
                    $newwidth       = $this->params->get('widthnew4');
                    $newheight      = $this->params->get('heightnew4');
                    $newcropzoom    = $this->params->get('cropzoomnew4');
                    $newaoe         = $this->params->get('aoenew4');
                    $newsx          = $this->params->def('sxnew4');
                    $newsy          = $this->params->def('synew4');
                    $newnoresize    = $this->params->get('noresizenew4');
                    $newnofullimg   = $this->params->get('nofullimgnew4');
                } elseif(in_array($Itemid, $this->params->get('menu_item5', array()) )) {
                    $newmaxwidth    = $this->params->get('maxwidthnew5');
                    $newmaxheight   = $this->params->get('maxheightnew5');
                    $newwidth       = $this->params->get('widthnew5');
                    $newheight      = $this->params->get('heightnew5');
                    $newcropzoom    = $this->params->get('cropzoomnew5');
                    $newaoe         = $this->params->get('aoenew5');
                    $newsx          = $this->params->def('sxnew5');
                    $newsy          = $this->params->def('synew5');
                    $newnoresize    = $this->params->get('noresizenew5');
                    $newnofullimg   = $this->params->get('nofullimgnew5');
                } else {
                    $newmaxwidth    = $this->params->get('maxwidth');
                    $newmaxheight   = $this->params->get('maxheight');
                    $newwidth       = $this->params->get('width');
                    $newheight      = $this->params->get('height');
                    $newcropzoom    = $this->params->get('cropzoom');
                    $newaoe         = $this->params->get('aoe');
                    $newsx          = $this->params->def('sx');
                    $newsy          = $this->params->def('sy');
                    $newnoresize    = $this->params->get('noresize');
                    $newnofullimg   = $this->params->get('nofullimg');
                }

            } elseif( $view == 'categories' || ($view == 'category' && (!$layout)) ) {

                if(in_array($Itemid, $this->params->get('cat_menu_item1', array()) )) {
                    $newmaxwidth    = $this->params->get('cat_maxwidthnew1');
                    $newmaxheight   = $this->params->get('cat_maxheightnew1');
                    $newwidth       = $this->params->get('cat_widthnew1');
                    $newheight      = $this->params->get('cat_heightnew1');
                    $newcropzoom    = $this->params->get('cat_cropzoomnew1');
                    $newaoe         = $this->params->get('cat_aoenew1');
                    $newsx          = $this->params->def('cat_sxnew1');
                    $newsy          = $this->params->def('cat_synew1');
                    $newnoresize    = $this->params->get('cat_noresizenew1');
                    $newnofullimg   = $this->params->get('cat_nofullimgnew1');
                } elseif(in_array($Itemid, $this->params->get('cat_menu_item2', array()) )) {
                    $newmaxwidth    = $this->params->get('cat_maxwidthnew2');
                    $newmaxheight   = $this->params->get('cat_maxheightnew2');
                    $newwidth       = $this->params->get('cat_widthnew2');
                    $newheight      = $this->params->get('cat_heightnew2');
                    $newcropzoom    = $this->params->get('cat_cropzoomnew2');
                    $newaoe         = $this->params->get('cat_aoenew2');
                    $newsx          = $this->params->def('cat_sxnew2');
                    $newsy          = $this->params->def('cat_synew2');
                    $newnoresize    = $this->params->get('cat_noresizenew2');
                    $newnofullimg   = $this->params->get('cat_nofullimgnew2');
                } elseif(in_array($Itemid, $this->params->get('cat_menu_item3', array()) )) {
                    $newmaxwidth    = $this->params->get('cat_maxwidthnew3');
                    $newmaxheight   = $this->params->get('cat_maxheightnew3');
                    $newwidth       = $this->params->get('cat_widthnew3');
                    $newheight      = $this->params->get('cat_heightnew3');
                    $newcropzoom    = $this->params->get('cat_cropzoomnew3');
                    $newaoe         = $this->params->get('cat_aoenew3');
                    $newsx          = $this->params->def('cat_sxnew3');
                    $newsy          = $this->params->def('cat_synew3');
                    $newnoresize    = $this->params->get('cat_noresizenew3');
                    $newnofullimg   = $this->params->get('cat_nofullimgnew3');
                } elseif(in_array($Itemid, $this->params->get('cat_menu_item4', array()) )) {
                    $newmaxwidth    = $this->params->get('cat_maxwidthnew4');
                    $newmaxheight   = $this->params->get('cat_maxheightnew4');
                    $newwidth       = $this->params->get('cat_widthnew4');
                    $newheight      = $this->params->get('cat_heightnew4');
                    $newcropzoom    = $this->params->get('cat_cropzoomnew4');
                    $newaoe         = $this->params->get('cat_aoenew4');
                    $newsx          = $this->params->def('cat_sxnew4');
                    $newsy          = $this->params->def('cat_synew4');
                    $newnoresize    = $this->params->get('cat_noresizenew4');
                    $newnofullimg   = $this->params->get('cat_nofullimgnew4');
                } elseif(in_array($Itemid, $this->params->get('cat_menu_item5', array()) )) {
                    $newmaxwidth    = $this->params->get('cat_maxwidthnew5');
                    $newmaxheight   = $this->params->get('cat_maxheightnew5');
                    $newwidth       = $this->params->get('cat_widthnew5');
                    $newheight      = $this->params->get('cat_heightnew5');
                    $newcropzoom    = $this->params->get('cat_cropzoomnew5');
                    $newaoe         = $this->params->get('cat_aoenew5');
                    $newsx          = $this->params->def('cat_sxnew5');
                    $newsy          = $this->params->def('cat_synew5');
                    $newnoresize    = $this->params->get('cat_noresizenew5');
                    $newnofullimg   = $this->params->get('cat_nofullimgnew5');
                } else {
                    $newmaxwidth    = $this->params->get('cat_maxwidth');
                    $newmaxheight   = $this->params->get('cat_maxheight');
                    $newwidth       = $this->params->get('cat_width');
                    $newheight      = $this->params->get('cat_height');
                    $newcropzoom    = $this->params->get('cat_cropzoom');
                    $newaoe         = $this->params->get('cat_aoe');
                    $newsx          = $this->params->def('cat_sx');
                    $newsy          = $this->params->def('cat_sy');
                    $newnoresize    = $this->params->get('cat_noresize');
                    $newnofullimg   = $this->params->get('cat_nofullimg');
                }
            }

            if( $newnoresize == '1' ) {
                $juimgresmatche = str_replace(array(' /', JURI::base()), '', $juimgresmatche);
                $limage = '<img '. JURI::base() . $juimgresmatche . $img_alt . $img_title . $img_style .' class="'. $img_class .'" />';
                return $limage;
            }

            include (dirname(__FILE__) . DS . 'watermark.php');

            if($this->params->get('maxsize_orig') == '1' || $this->params->get('cat_newmaxsize_orig') == '1') {
                if( $view == 'article' ) {
                    $parsed_url = parse_url($imgsource);
                    $imgsrc_orig    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                        '&w='. $newmaxwidth .
                        '&h='. $newmaxheight .
                        '&q=100'.
                        ( $newaoe == '1' ? '&aoe=1' : '' ) .
                        ($this->params->get('a_watermarkgall') == '0' ? $a_watermark_orig : '')
                        ;
                } else {
                    $parsed_url = parse_url($imgsource);
                    $imgsrc_orig    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                        '&w='. $cat_newmaxwidth .
                        '&h='. $cat_newmaxheight .
                        '&q=100'.
                        ( $newaoe == '1' ? '&aoe=1' : '' ) .
                        ($this->params->get('a_watermarkgall') == '0' ? $a_watermark_orig : '')
                        ;
                }
            } else {
                $parsed_url = parse_url($imgsource);
                $imgsrc_orig    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                    '&q=100'.
                    ( $newaoe == '1' ? '&aoe=1' : '' ) .
                    ($this->params->get('a_watermarkgall') == '0' ? $a_watermark_orig : '')
                    ;
            }

            $parsed_url = parse_url($imgsource);
            $imgsrc    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                '&w='. $newwidth .
                '&h='. $newheight .
                '&q='.$this->params->get('quality') .
                ( $newcropzoom == '1' ? '&zc=1' : '' ) .
                ( $newaoe == '1' ? '&aoe=1' : '' ) .
                ( $newsx == '1' ? '&sx=1' : '' ) .
                ( $newsy == '1' ? '&sy=1' : '' ) .
                ($this->params->get('a_watermarkgall_s') == '0' ? $a_watermark_small : '')
                ;

            //$imgsrcno64 = $imgsrc;
            $imgsrc = base64_encode($imgsrc);

            $ext    = explode("&", $imgsrc_orig);

            if($this->params->get('secimg') == '1') {
                $image = JURI::base() .'plugins/content/jumultithumb/img/'. $imgsrc .'.jpg';
                if(
                    $this->params->get('a_watermark') == '1' ||
                    $this->params->get('a_watermarknew1') == '1' ||
                    $this->params->get('a_watermarknew2') == '1' ||
                    $this->params->get('a_watermarknew3') == '1' ||
                    $this->params->get('a_watermarknew4') == '1' ||
                    $this->params->get('a_watermarknew5') == '1' ||
                    $this->params->get('maxsize_orig') == '1' ||
                    $this->params->get('cat_newmaxsize_orig') == '1' ||
                    (substr(strtolower($ext[0]),-4) == 'tiff') ||
                    (substr(strtolower($ext[0]),-3) == 'tif') ||
                    (substr(strtolower($ext[0]),-3) == 'wmf')
                    ) {
                    $imgsource = JURI::base() .'plugins/content/jumultithumb/img/'. base64_encode( $imgsrc_orig ) .'.jpg';
                }
            } else {
                $image = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. $imgsrc;
                if(
                    (
                        $this->params->get('a_watermark') == '1' ||
                        $this->params->get('a_watermarknew1') == '1' ||
                        $this->params->get('a_watermarknew2') == '1' ||
                        $this->params->get('a_watermarknew3') == '1' ||
                        $this->params->get('a_watermarknew4') == '1' ||
                        $this->params->get('a_watermarknew5') == '1'
                    ) ||
                    (
                        $this->params->get('a_watermark_s') == '1' ||
                        $this->params->get('a_watermarknew1_s') == '1' ||
                        $this->params->get('a_watermarknew2_s') == '1' ||
                        $this->params->get('a_watermarknew3_s') == '1' ||
                        $this->params->get('a_watermarknew4_s') == '1' ||
                        $this->params->get('a_watermarknew5_s') == '1'
                    ) ||
                    $this->params->get('maxsize_orig') == '1' ||
                    $this->params->get('cat_newmaxsize_orig') == '1' ||
                    (substr(strtolower($ext[0]),-4) == 'tiff') ||
                    (substr(strtolower($ext[0]),-3) == 'tif') ||
                    (substr(strtolower($ext[0]),-3) == 'wmf')
                    ) {
                    $imgsource = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. base64_encode( $imgsrc_orig );
                }
            }

            if(
                $this->params->get('secimg') == '0' ||
                $this->params->get('a_watermark_s') == '0' ||
                $this->params->get('a_watermark') == '0' ||
                $this->params->get('maxsize_orig') == '1' ||
                $this->params->get('cat_newmaxsize_orig') == '1' ||
                ( isset($ext['extension']) == 'tiff' || isset($ext['extension']) == 'wmf')
                ) {
                $imgsource   = str_replace(JURI::base(), '', $imgsource);
                $parsed_url  = parse_url($imgsource);
                $imgsource   = (isset($parsed_url['host']) ? $imgsource : JURI::base() . $imgsource);
            }

            list($width, $height, $type, $attr) = getimagesize($image);

            if(
                $newnofullimg == '1' ||
                JRequest::getString('print') == '1'
                ){
   		        $limage = '<img src="'. $image .'"' . $img_alt . $img_style . $attr .' class="juimage'. $img_class .'" />';
            } else {
   		        $limage = '<a href="' . $imgsource . '"'. $img_title .' '. $lightbox .'><img src="'. $image .'"' . $img_alt . $img_style .' '. $attr .' class="juimage'. $img_class .'" /></a>';
            }

        } elseif($view == 'featured') {

            $f_newwidth       = $this->params->get('f_width');
            $f_newheight      = $this->params->get('f_height');
            $f_newcropzoom    = $this->params->get('f_cropzoom');
            $f_newaoe         = $this->params->get('f_aoe');
            $f_newsx          = $this->params->def('f_sx');
            $f_newsy          = $this->params->def('f_sy');

            $parsed_url = parse_url($imgsource);
            $imgsrc    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                '&w='. $f_newwidth .
                '&h='. $f_newheight .
                '&q='. $this->params->get('quality') .
                ( $f_newcropzoom == '1' ? '&zc=1' : '' ) .
                ( $f_newaoe == '1' ? '&aoe=1' : '' ) .
                ( $f_newsx == '1' ? '&sx=1' : '' ) .
                ( $f_newsy == '1' ? '&sy=1' : '' )
                ;

            $imgsrc = base64_encode($imgsrc);

            if($this->params->get('secimg') == '1') {
                $image = JURI::base() .'plugins/content/jumultithumb/img/'. $imgsrc .'.jpg';
            } else {
                $image = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. $imgsrc;
            }

            list($width, $height, $type, $attr) = getimagesize($image);

            $limage = '<img src="'. $image .'"'. $img_alt . $img_title . $img_style .' '. $attr .' class="juimage'. $img_class .'" />';

        } elseif($view == 'archive') {

            $a_newwidth     = $this->params->get('a_width');
            $a_newheight    = $this->params->get('a_height');
            $a_newcropzoom  = $this->params->get('a_cropzoom');

            $parsed_url = parse_url($imgsource);
            $imgsrc    = (isset($parsed_url['host']) ? $imgsource : '../../../../' . $imgsource) .
                '&w='. $a_newwidth .
                '&h='. $a_newheight .
                '&q='. $this->params->get('quality') .
                ( $a_newcropzoom == '1' ? '&zc=1' : '' )
                ;

            $imgsrc = base64_encode($imgsrc);

            if($this->params->get('secimg','1') == '1') {
                $image = JURI::base() .'plugins/content/jumultithumb/img/'. $imgsrc .'.jpg';
            } else {
                $image = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. $imgsrc;
            }

            list($width, $height, $type, $attr) = getimagesize($image);

            $limage = '<img src="'. $image .'"'. $img_alt . $img_title . $img_style .' '. $attr .' class="juimage'. $img_class .'" />';

        }

    	return $limage;

    }
}