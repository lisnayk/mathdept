<?php
/**
* @package plg_jumultithumb
* @author Denys Nosov (http://denysdesign.com), Joomla! Ukraine (http://joomla-ua.org)
* @copyright (C) 2007-2012 Denys Nosov. All rights reserved.
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/ Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License
* @version 4.0
* @description: Multifunction plugin for creating thumbnails of images, using LightBox libraries and watermark
*/

error_reporting(0);

define( '_JEXEC', 1 );
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..");
define ("MAX_SIZE","500");

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

$mainframe =& JFactory::getApplication('administrator');
$mainframe->initialise();

$joomlaUser =& JFactory::getUser();

$lang =& JFactory::getLanguage();
$lang->load('plg_content_jumultithumb', JPATH_ADMINISTRATOR);

if ($joomlaUser->get('id') < 1) {
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><link rel="stylesheet" href="../../../../../templates/system/css/system.css" type="text/css" /><link href="../../../../../administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" /></head><body><dl id="system-message"><dt class="error"><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE'); ?></dt><dd class="message error fade"><ul><li><?php echo JText::_('PLG_JUMULTITHUMB_LOGIN'); ?></li></ul></dd></dl></body></html>
<?php
    return;
}

    function recursiveDelete($str) {
        if(is_file($str)){
            return @unlink($str);
        } elseif(is_dir($str)) {
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }

    function getExtension($str) {
        $i = strrpos($str,".");
        if (!$i) { return ""; }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }

    $errors=0;
    if(isset($_POST['Submit'])) {
      	if (isset($_FILES['image']['name'])){
        	$filename = stripslashes($_FILES['image']['name']);
     		$extension = getExtension($filename);
     	   	$extension = strtolower($extension);
            if (($extension != "png")) {
                if(isset($_POST['watermark']) == 'big'){
                    $unknownext = '<dl id="system-message"><dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt><dd class="message notice fade"><ul><li>'. JText::_('PLG_JUMULTITHUMB_NOTICE6') .'</li></ul></dd></dl>';
                } elseif(isset($_POST['watermark']) == 'small') {
                    $unknownext_s = '<dl id="system-message"><dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt><dd class="message notice fade"><ul><li>'. JText::_('PLG_JUMULTITHUMB_NOTICE6') .'</li></ul></dd></dl>';
                }
     		    $errors=1;
     		} else {
                $size=filesize($_FILES['image']['tmp_name']);
                if ($size > MAX_SIZE*1024){
                    if(isset($_POST['watermark']) == 'big'){
                        $limitimg = '<dl id="system-message"><dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt><dd class="message notice fade"><ul><li>'. JText::_('PLG_JUMULTITHUMB_NOTICE7') .'</li></ul></dd></dl>';
                    } elseif(isset($_POST['watermark']) == 'small') {
                        $limitimg_s = '<dl id="system-message"><dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt><dd class="message notice fade"><ul><li>'. JText::_('PLG_JUMULTITHUMB_NOTICE7') .'</li></ul></dd></dl>';
                    }
    	            $errors=1;
                }
                if($_POST['watermark'] == 'big'){
                    $image_name='w.png';
                } elseif($_POST['watermark'] == 'small') {
                    $image_name='ws.png';
                }
                if (!($size > MAX_SIZE*1024)){
                    $newname=$image_name;
                    $copied = copy($_FILES['image']['tmp_name'], $newname);
                    if (!$copied){
                        if(isset($_POST['watermark']) == 'big'){
                            $uploadunsuccessfull = '<dl id="system-message"><dt class="error">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt><dd class="message error fade"><ul><li>'. JText::_('PLG_JUMULTITHUMB_NOTICE8') .'</li></ul></dd></dl>';
                        } elseif(isset($_POST['watermark']) == 'small') {
                            $uploadunsuccessfull_s = '<dl id="system-message"><dt class="error">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt><dd class="message error fade"><ul><li>'. JText::_('PLG_JUMULTITHUMB_NOTICE8') .'</li></ul></dd></dl>';
                        }
        	            $errors=1;
                    }
                }
            }
        }
    }
    if(isset($_POST['Submit']) && !$errors) {
        if(isset($_POST['watermark']) == 'big'){
            $uploadsucess = '
            <dl id="system-message">
            <dt class="message">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
            <dd class="message message fade">
            	<ul>
            		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE9') .'</li>
            	</ul>
            </dd>
            </dl>
            ';
        } elseif(isset($_POST['watermark']) == 'small') {
            $uploadsucess_s = '
            <dl id="system-message">
            <dt class="message">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
            <dd class="message message fade">
            	<ul>
            		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE9') .'</li>
            	</ul>
            </dd>
            </dl>
            ';
        }
    }
    if(JRequest::getString('del') == 'big') {
        if(is_file( JPATH_SITE .'/plugins/content/jumultithumb/img/watermark/w.png')){
            unlink('w.png');
            $noticewb = '
            <dl id="system-message">
            <dt class="message">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
            <dd class="message message fade">
            	<ul>
            		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE10') .'</li>
            	</ul>
            </dd>
            </dl>';
        } else {
            $noticewb = '<dl id="system-message">
        <dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
        <dd class="message notice fade">
           	<ul>
           		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE11') .'</li>
           	</ul>
        </dd>
        </dl>';
        }
    } elseif(JRequest::getString('del') == 'small') {
        if(is_file( JPATH_SITE .'/plugins/content/jumultithumb/img/watermark/ws.png')){
            unlink('ws.png');
            $noticews = '<dl id="system-message">
            <dt class="message">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
            <dd class="message message fade">
            	<ul>
            		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE10') .'</li>
            	</ul>
            </dd>
            </dl>';
        } else {
            $noticews = '<dl id="system-message">
        <dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
        <dd class="message notice fade">
           	<ul>
           		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE11') .'</li>
           	</ul>
        </dd>
        </dl>';
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<title></title>
<link rel="stylesheet" href="../../../../../templates/system/css/system.css" type="text/css" />
<link href="../../../../../administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />
<style type="text/css">
/*<![CDATA[*/
body{ background: transparent; }
fieldset{
  margin: 5px 0!important;
}
img{
  margin: 0 0 6px 0!important;
  background: #C1C1C1;
  border: 1px solid #ccc;
}
/*]]>*/
</style>
</head>
<body>
<fieldset class="adminform">
    <legend><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE12'); ?></legend>
    <?php
    if(isset($_POST['cache']) == '1'){
        recursiveDelete( JPATH_BASE .'/cache/plg_img_jumultithumb');
        recursiveDelete( JPATH_BASE .'/cache/com_content');
        if(is_dir( JPATH_BASE .'/cache/plg_img_jumultithumb')){
        ?>
        <dl id="system-message">
        <dt class="notice"><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE'); ?></dt>
        <dd class="message notice fade">
           	<ul>
           		<li><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE13'); ?></li>
           	</ul>
        </dd>
        </dl>
        <?php
        } else {
        ?>
        <dl id="system-message">
        <dt class="message"><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE'); ?></dt>
        <dd class="message message fade">
           	<ul>
           		<li><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE14'); ?></li>
           	</ul>
        </dd>
        </dl>
        <?php
        }
    }
    ?>
    <p><?php echo JText::_('PLG_JUMULTITHUMB_WATERMARK_CLEAR'); ?></p>
    <form name="cache" method="post" action="">
        <input name="Submit" type="submit" value="<?php echo JText::_('PLG_JUMULTITHUMB_WATERMARK_CLEAR_NOW'); ?>" />
        <input type="hidden" name="cache" value="1" />
    </form>
</fieldset>
<?php
    if(isset($noticewb)){
        echo $noticewb;
    }
    if(isset($noticews)){
        echo $noticews;
    }
    if(isset($uploadsucess)){
        echo $uploadsucess;
    }
    if(isset($unknownext)){
        echo $unknownext;
    }
    if(isset($limitimg)){
        echo $limitimg;
    }
    if(isset($uploadunsuccessfull)){
        echo $uploadunsuccessfull;
    }
    if(isset($uploadsucess_s)){
        echo $uploadsucess_s;
    }
    if(isset($unknownext_s)){
        echo $unknownext_s;
    }
    if(isset($limitimg_s)){
        echo $limitimg_s;
    }
    if(isset($uploadunsuccessfull_s)){
        echo $uploadunsuccessfull_s;
    }
?>
<fieldset class="adminform" style="clear: both; float: left; width: 48%;">
    <legend>Watermark for original image</legend>
    <p>
    <form name="wb" method="post" enctype="multipart/form-data"  action="">
        <input type="file" name="image" />
        <input name="Submit" type="submit" value="<?php echo JText::_('PLG_JUMULTITHUMB_NOTICE15'); ?>" />
        <input type="hidden" name="watermark" value="big" />
    </form>
    </p><p>
    <form name="wbdel" method="post" action="">
        <input name="Submit" type="submit" value="<?php echo JText::_('PLG_JUMULTITHUMB_NOTICE16'); ?>" />
        <input type="hidden" name="del" value="big" />
    </form>
    </p>
    <div style="clear: both; padding-top: 10px;">
    <?php
    if(is_file('w.png')){
        echo '<img src="w.png?v='. time() .'" alt="Watermark" class="img" />';
    } else {
        echo '<a href="http://denysdesign.com" target="_blank" title="Denys Design Studio"><img src="juw.png?v='. time() .'" alt="Watermark" class="img" /></a>';
    }
    ?>
    </div>
</fieldset>

<fieldset class="adminform" style="float: right; width: 48%;">
    <legend>Watermark for thumbnail image</legend>
    <p>
    <form name="ws" method="post" enctype="multipart/form-data"  action="">
        <input type="file" name="image" />
        <input name="Submit" type="submit" value="<?php echo JText::_('PLG_JUMULTITHUMB_NOTICE15'); ?>" />
        <input type="hidden" name="watermark" value="small" />
    </form>
    </p><p>
    <form name="wbdel" method="post" action="">
        <input name="Submit" type="submit" value="<?php echo JText::_('PLG_JUMULTITHUMB_NOTICE16'); ?>" />
        <input type="hidden" name="del" value="small" />
    </form>
    </p>
    <div style="clear: both; padding-top: 10px;">
    <?php
    if(is_file('ws.png')){
        echo '<img src="ws.png?v='. time() .'" alt="Watermark" class="img" />';
    } else {
        echo '<a href="http://www.joomla-ua.org" target="_blank" title="Joomla! Ukraine"><img src="juws.png?v='. time() .'" alt="Watermark" class="img" /></a>';
    }
    ?>
    </div>
</fieldset>
</body>
</html>