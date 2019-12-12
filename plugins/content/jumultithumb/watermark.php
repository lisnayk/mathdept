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

/* Big watermark */
if(
    ($this->params->get('a_watermarkgall') == '1') ||
    ($this->params->get('a_watermark') == '1') ||
    ($this->params->get('a_watermarknew1') == '1') ||
    ($this->params->get('a_watermarknew2') == '1') ||
    ($this->params->get('a_watermarknew3') == '1') ||
    ($this->params->get('a_watermarknew4') == '1') ||
    ($this->params->get('a_watermarknew5') == '1')
    ) {

    switch ( $this->params->get('wmposition') ) {
        case 'top':
            $wmp    =   'T';
        break;
        case 'bottom':
            $wmp    =   'B';
        break;
        case 'left':
            $wmp    =   'L';
        break;
        case 'right':
            $wmp    =   'R';
        break;
        case 'center':
            $wmp    =   'C';
        break;
        case 'topleft':
            $wmp    =   'TL';
        break;
        case 'topright':
            $wmp    =   'TR';
        break;
        case 'bottomleft':
            $wmp    =   'BL';
        break;
        case 'bottomright':
            $wmp    =   'BR';
        break;
    }

    if(is_file(JPATH_SITE.'/plugins/content/jumultithumb/img/watermark/w.png')){
        $watermark = 'watermark/w.png';
    } else {
        $watermark = 'watermark/juw.png';
    }

    $a_watermark_orig   = '&fltr[]=wmi|'. $watermark .'|'. $wmp . ( $this->params->get('wmopst') ? '|'. $this->params->get('wmopst') : '' );
} else {
    $a_watermark_orig   = '';
}

/* Small watermark */
if(
    ($this->params->get('a_watermarkgall_s') == '1') ||
    ($this->params->get('a_watermark_s') == '1') ||
    ($this->params->get('a_watermarknew1_s') == '1') ||
    ($this->params->get('a_watermarknew2_s') == '1') ||
    ($this->params->get('a_watermarknew3_s') == '1') ||
    ($this->params->get('a_watermarknew4_s') == '1') ||
    ($this->params->get('a_watermarknew5_s') == '1')
    ) {

    switch ( $this->params->get('wmposition_s') ) {
        case 'top':
$wmps    =   'T';
        break;
        case 'bottom':
$wmps    =   'B';
        break;
        case 'left':
$wmps    =   'L';
        break;
        case 'right':
$wmps    =   'R';
        break;
        case 'center':
$wmps    =   'C';
        break;
        case 'topleft':
$wmps    =   'TL';
        break;
        case 'topright':
$wmps    =   'TR';
        break;
        case 'bottomleft':
$wmps    =   'BL';
        break;
        case 'bottomright':
$wmps    =   'BR';
        break;
    }

    if(is_file(JPATH_SITE.'/plugins/content/jumultithumb/img/watermark/ws.png')){
        $watermark_s = 'watermark/ws.png';
    } else {
        $watermark_s = 'watermark/juws.png';
    }

    $a_watermark_small = '&fltr[]=wmi|'. $watermark_s .'|'. $wmps . ( $this->params->get('wmopst_s') ? '|'. $this->params->get('wmopst_s') : '' );

} else {

    $a_watermark_small = '';

}