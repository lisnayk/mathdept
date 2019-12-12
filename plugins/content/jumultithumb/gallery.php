<?php
/**
* @package plg_jumultithumb
* @author Denys Nosov (http://denysdesign.com), Joomla! Ukraine (http://joomla-ua.org)
* @copyright (C) 2007-2012 Denys Nosov. All rights reserved.
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/ Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License
* @version 4.0
* @description: Multifunction plugin for creating thumbnails of images, using LightBox libraries and watermark
*/

defined('_JEXEC') or die;

if (strpos($article->text, 'gallery') === false && strpos($article->text, 'gallery') === false) {
    return true;
}

$regex = "/{gallery\s+(.*?)}/i";
preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

if($this->params->get('use_gallery') == '0') {

    foreach ($matches as $match) {
        $article->text = preg_replace($regex, '', $article->text, 1);
    }

    return true;
}

if($this->params->get('a_watermarkgall') == '1' || $this->params->get('a_watermarkgall_s') == '1') {
    include (dirname(__FILE__) . DS . 'watermark.php');
}

if ($matches) {
    foreach ($matches as $match) {

    	$matcheslist =  explode('|',$match[1]);

        $galltitle		= null;
        $gallstyle		= null;
        $img_alt		= null;
        $img_style		= null;
        $attr		    = null;

    	if (!array_key_exists(1, $matcheslist)) {
    		$matcheslist[1] = null;
    	}

    	if (!array_key_exists(2, $matcheslist)) {
    		$matcheslist[2] = $galltitle;
    	}

    	if (!array_key_exists(3, $matcheslist)) {
    		$matcheslist[3] = $gallstyle;
    	}

    	if (!array_key_exists(4, $matcheslist)) {
    		$matcheslist[4] = $newgallwidth;
    	}

    	if (!array_key_exists(5, $matcheslist)) {
    		$matcheslist[5] = $newgallheight;
    	}

    	if (!array_key_exists(6, $matcheslist)) {
    		$matcheslist[6] = $newcrop;
    	}

    	if (!array_key_exists(7, $matcheslist)) {
    		$matcheslist[7] = $newwms;
    	}

    	if (!array_key_exists(8, $matcheslist)) {
    		$matcheslist[8] = $newwmb;
    	}

    	$folder        = trim($matcheslist[0]);
    	$galltitle     = str_replace ('title=', '', trim($matcheslist[1]));
    	$gallstyle     = str_replace ('class=', '', trim($matcheslist[2]));
    	$newgallwidth  = str_replace ('w=', '', $matcheslist[3]);
    	$newgallheight = str_replace ('h=', '', $matcheslist[4]);
    	$newcrop       = str_replace ('zc=', '', $matcheslist[5]);
    	$newwms        = str_replace ('wms=', '', $matcheslist[6]);
    	$newwmb        = str_replace ('wmb=', '', $matcheslist[7]);

        $dir        = JPATH_ROOT .'/images/'. $folder;
        $imgpath    = 'images/'. $folder;



        $gallwidth 		= ( $newgallwidth ? $newgallwidth : $this->params->get('gallwidth') );
        $gallheight		= ( $newgallheight ? $newgallheight : $this->params->get('gallheight') );
        $gallmaxwidth	= $this->params->get('gallmaxwidth');
        $gallmaxheight	= $this->params->get('gallmaxheight');

        if($newcrop == '1') {
            $gallcropzoom = 1;
        } elseif($newcrop == '0') {
            $gallcropzoom = 0;
        } else {
            $gallcropzoom = $this->params->get('gallcropzoom');
        }

        if($newwms == '1') {
            $gallwms = $a_watermark_small;
        } elseif($newwms == '0') {
            $gallwms = '';
        } else {
            $gallwms = ($this->params->get('a_watermarkgall_s') == '1' ? $a_watermark_small : '');
        }

        if($newwmb == '1') {
            $gallwmb = $a_watermark_orig;
        } elseif($newwmb == '0') {
            $gallwmb = '';
        } else {
            $gallwmb = ($this->params->get('a_watermarkgall') == '1' ? $a_watermark_orig : '');
        }

        $rel = str_replace('/', '', $folder);

        switch ( $this->params->get('selectlightbox') ) {
            case 'slimbox':
                $lightbox = 'class="lightbox" rel="lightbox-'. $rel .'"';
            break;

            case 'fancybox':
                $lightbox = 'class="lightbox" data-fancybox-group="'. $rel .'"';
            break;

            case 'jmodal':
                $lightbox = 'class="modal" rel="{handler: \'image\', marginImage: {x: 50, y: 50}}"';
            break;

            default:
            case 'customjs':
            case 'jqlightbox':
            case 'colorbox':
                $lightbox = 'class="lightbox" rel="lightbox['. $rel .']"';
            break;
        }


        $i = -1;

        foreach ($matches[0] as $match) {
            $i++;
            unset($images);
          	$j = 0;

          	if ($jug = opendir($dir)) {

          	    while (($f = readdir($jug)) !== false) {
                    if(
                      (substr(strtolower($f),-3) == 'jpg') ||
                      (substr(strtolower($f),-4) == 'jpeg') ||
                      (substr(strtolower($f),-3) == 'gif') ||
                      (substr(strtolower($f),-3) == 'png') ||
                      (substr(strtolower($f),-3) == 'bmp') ||
                      (substr(strtolower($f),-4) == 'tiff') ||
                      (substr(strtolower($f),-3) == 'tif') ||
                      (substr(strtolower($f),-3) == 'wmf')
                      ) {
              		    $j++;
              			$images[] = array('filename' => $f);
              			array_multisort($images, SORT_ASC, SORT_REGULAR);
              		}
              	}

                closedir($jug);
            }

            if($view == 'article'){
                $img_title = preg_replace("/\"/", "'", $article->title);
            } else {
                $img_title = '';
            }

            if($j) {
                $html  = '<div class="juphotogallery'. (isset($gallstyle) ? ' '. $gallstyle : '') .'">';
                $html .= ($galltitle == '' ? '' : '<h3 class="jutitlegallery">'. $galltitle .'</h3>');
                $html .= '<div class="jugallerybody">';

                for($a = 0; $a < $j; $a++) {
              	    if($images[$a]['filename'] != '') {

                        $imgsrc = '../../../../' . $imgpath .'/'. $images[$a]['filename'] .
                          '&w='. $gallwidth .
                          '&h='. $gallheight .
                          '&q='.$this->params->get('quality') .
                          ( $gallcropzoom == '1' ? '&zc=1' : '' ) .
                          '&aoe=1' .
                          $gallwms
                          ;

                        if($this->params->get('gallmaxsize_orig') == '1') {
                            $imgsrc_orig = '../../../../' . $imgpath .'/'. $images[$a]['filename'] .
                                '&w='. $gallmaxwidth .
                                '&h='. $gallmaxheight .
                                '&q=100' .
                                '&aoe=1' .
                                $gallwmb
                                ;
                        } elseif($this->params->get('a_watermarkgall') == '1') {

                           $imgsrc_orig = '../../../../' . $imgpath .'/'. $images[$a]['filename'] .
                                '&q=100' .
                                '&aoe=1========' .
                                $gallwmb
                                ;
                        } else {
                            $imgsrc_orig = '';
                            $imgsource = JURI::base() . $imgpath .'/'. $images[$a]['filename'];
                        }

                        if($this->params->get('secimg') == '1') {
                            $image = JURI::base() .'plugins/content/jumultithumb/img/'. base64_encode( $imgsrc ) .'.jpg';
                            if(
                                $this->params->get('a_watermarkgall') == '1' ||
                                $this->params->get('gallmaxsize_orig') == '1' ||
                                isset($ext[0]) == 'tiff' ||
                                isset($ext[0]) == 'tif' ||
                                isset($ext[0]) == 'wmf'
                                ) {
                                $imgsource = JURI::base() .'plugins/content/jumultithumb/img/'. base64_encode( $imgsrc_orig ) .'.jpg';
                            }
                        } else {
                            $image = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. base64_encode( $imgsrc );
                            if(
                                $this->params->get('a_watermarkgall') == '1' ||
                                $this->params->get('gallmaxsize_orig') == '1' ||
                                isset($ext[0]) == 'tiff' ||
                                isset($ext[0]) == 'tif' ||
                                isset($ext[0]) == 'wmf'
                                ) {
                                $imgsource = JURI::base() .'plugins/content/jumultithumb/img/img.php?src='. base64_encode( $imgsrc_orig );
                            }
                        }

                        list($width, $height, $type, $attr) = getimagesize($image);

              			//$html .= $imgsource.'<a href="' . ($newwmb == '1' ? $imgsource : JURI::base() . $imgpath .'/'. $images[$a]['filename']) . '" title="'. ($galltitle == '' ? $img_title : $galltitle) .'" '. $lightbox .'><img src="'. $image .'" alt="'. ($galltitle == '' ? $img_title : $galltitle) .'" '. $attr .' class="jugallery" /></a>';
              			$html .= '<a href="' . $imgsource . '" title="'. ($galltitle == '' ? $img_title : $galltitle) .'" '. $lightbox .'><img src="'. $image .'" alt="'. ($galltitle == '' ? $img_title : $galltitle) .'" '. $attr .' class="jugallery" /></a>';

              		}
              	}

                $html .= '</div>';
                $html .= '</div>';
            }
        }

    	$article->text = preg_replace($regex, $html, $article->text, 1);
	}
}