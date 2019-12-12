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

$style = '../../assets/style.css';

$newcss = '/*
-----------------------------------------------
File:    style.css
Author:  Denys Nosov at http://www.denysdesign.com
Version: '. date ('d.m.Y') .'

License: GNU General Public License version 2 or later
----------------------------------------------- */

.juimage {
  border: #ccc 1px solid;
  padding: 1px;
}
.juimg-category {

}
.juimg-featured {

}
.juimg-article {

}

.juleft {
  float: left;
  margin: 0 6px 6px 0;
}
.juright {
  float: right;
  margin: 0 0 6px 6px;
}

/* Gallery */
.juphotogallery {
	clear: both;
	padding: 10px 0 6px 10px;
	margin: 10px 0;
	background: #f3f3f3;
	border-top: 3px solid #ccc;
	border-bottom: 3px solid #ccc;
	overflow: hidden;
}
	.juphotogallery h3 {
		margin: 0 0 6px 0!important;
		padding: 0!important;
		font-size: 160%!important;
	}
	.juphotogallery img.jugallery {
		float: left;
		margin: 0 12px 12px 0;
		padding: 3px;
		border: 1px solid #ccc;
		background: #fff;
		border-radius: 5px;
	}

/* Example CSS class (css_class) from tag {gallery folder_path|title|css_class} */
.blue {
	background: #edf7fe;
	border: 0 none;
}
	.blue h3 {
		color: #4682b4!important;
	}
';

if(!file_exists($style)){
    $file = fopen($style, 'w');
    fputs($file, $newcss);
    fclose($file);
    $notice = '
    <dl id="system-message">
        <dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
        <dd class="message notice fade">
           	<ul>
           		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE1') .'</li>
           		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE2') .'</li>
        	</ul>
        </dd>
    </dl>
            ';
}

    if(file_exists($style)){
        if (!empty( $_POST['txt'] )) {
            $file = fopen($style, 'w');
            fputs($file, $_POST['txt']);
            fclose($file);
            $notice = '
            <dl id="system-message">
            <dt class="message">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
            <dd class="message message fade">
               	<ul>
               		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE3') .'</li>
               	</ul>
            </dd>
            </dl>
            ';
        }
    }

    if(filesize($style) < 3){
            $file = fopen($style, 'w');
            fputs($file, $newcss);
            fclose($file);
            $notice = '
            <dl id="system-message">
            <dt class="notice">'. JText::_('PLG_JUMULTITHUMB_NOTICE') .'</dt>
            <dd class="message notice fade">
               	<ul>
               		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE4') .'</li>
               		<li>'. JText::_('PLG_JUMULTITHUMB_NOTICE5') .'</li>
               	</ul>
            </dd>
            </dl>
            ';
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<title><?php echo JText::_('PLG_JUMULTITHUMB_CSS_FRONT'); ?></title>
<link rel="stylesheet" href="../../../../../templates/system/css/system.css" type="text/css" />
<link href="../../../../../administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />
<style type="text/css">
/*<![CDATA[*/
body{ background: transparent; }
fieldset{
  margin: 5px 0!important;
}
.CodeMirror-line-numbers {
    width: 2.2em;
    color: #aaa;
    background-color: #eee;
    text-align: right;
    padding: .4em;
    margin: 0;
    font-family: monospace;
    font-size: 10pt;
    line-height: 1.2em;
}
iframe{
    border-top: 1px solid #ccc!important;
    border-right: 1px solid #ccc!important;
    border-bottom: 1px solid #ccc!important;
}
/*]]>*/
</style>
</head>
<body>
<fieldset class="adminform">
    <legend><?php echo JText::_('PLG_JUMULTITHUMB_CSS_FRONT'); ?></legend>
    <?php
    if(isset($notice)){
      echo $notice;
    }
    ?>
    <form action="css.php" method="post">
        <input name="submit" type="submit" value="<?php echo JText::_('PLG_JUMULTITHUMB_CSS_SAVE'); ?>" /><br /><br />
        <textarea cols="100" rows="30" name="txt" id="code"><?php readfile($style);?></textarea>
    </form>
</fieldset>
<script src="js/codemirror.js" type="text/javascript"></script>
<script type="text/javascript">
  var editor = CodeMirror.fromTextArea('code', {
    height: "350px",
    basefiles: ["util.js", "stringstream.js", "select.js", "undo.js", "editor.js", "tokenize.js"],
    parserfile: "parsecss.js",
    stylesheet: "css/csscolors.css",
    path: "js/",
    tabMode: "shift",
    lineNumbers: true
  });
</script>
</body>
</html>