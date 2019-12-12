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
define('JPATH_BASE', dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..");
define ("MAX_SIZE","500");

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

$mainframe =& JFactory::getApplication('administrator');
$mainframe->initialise();

$joomlaUser =& JFactory::getUser();

$lang =& JFactory::getLanguage();
$lang->load('plg_content_jumultithumb', JPATH_ADMINISTRATOR);

$language = mb_strtolower($lang->getTag());

if ($joomlaUser->get('id') < 1) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../../../../../templates/system/css/system.css" type="text/css" />
<link href="../../../../../administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />
</head>
<body>
<dl id="system-message">
    <dt class="error"><?php echo JText::_('PLG_JUMULTITHUMB_NOTICE'); ?></dt>
    <dd class="message error fade">
        <ul>
            <li><?php echo JText::_('PLG_JUMULTITHUMB_LOGIN'); ?></li>
        </ul>
    </dd>
</dl>
</body>
</html>
<?php
    return;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo JText::_('PLG_JUMULTITHUMB_GALLERY_INSERT_TAG'); ?></title>
<link rel="stylesheet" href="../../../templates/system/css/system.css" type="text/css" />
<link href="../../../administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript">
function insertJUGallery() {
	var folder = document.getElementById("folder").value;
    var title = document.getElementById("title").value;
    var cssclass = document.getElementById("cssclass").value;

    if (folder != '') {
		folder = ""+folder+"";
	}

	if (title == '' && cssclass != '') {
		title = "|";
	} else if (title != '') {
	    title = "|"+title;
	} else {
	    title == "";
	}

	if (cssclass != '') {
		cssclass = "|"+cssclass;
	}

	var tag = "{gallery "+ folder + title+ cssclass +"}";
	window.parent.jInsertEditorText(tag, 'jform_articletext');
	window.parent.SqueezeBox.close();
	return false;
}

  </script>
<style type="text/css">
/*<![CDATA[*/
body{ background: transparent; }
fieldset{
  margin: 5px 0!important;
}

b {
   color: green;
 }

/*]]>*/
</style>
</head>
<body>
<fieldset class="adminform">
    <div id="system-message-container">
    </div>
    <form>
		<table width="100%" align="center">
			<tr width="40%">
				<td class="key" align="right">
					<label for="title">
						<?php echo JText::_('PLG_JUMULTITHUMB_GALLERY_SELECT_FOLDER'); ?>:					</label>
				</td>
				<td>
					<input type="text" size="9" value="/images" disabled="disabled" />

                    <?php
                    $control_name = 'folder';
                    $name = 'folder';
            		$ctrl = $name;
            		$attribs = '';
                    $attribs .= ' class="inputbox"';

                    $options = array();

                    recursiveScanDir( JPATH_ROOT . DS . "images", $options);
                    echo JHTML::_('select.genericlist', $options, $ctrl, trim($attribs), 'value', 'name', $control_name . $name);
                    ?>
				</td>
			</tr>
			<tr width="60%">
				<td class="key" align="right">
					<label for="alias">
						<?php echo JText::_('PLG_JUMULTITHUMB_GALLERY_TITLE'); ?>:					</label>
				</td>
				<td>

					<input type="text" id="title" name="title" size="42" />
				</td>
			</tr>
			<tr width="60%">
				<td class="key" align="right">
					<label for="alias">
						<?php echo JText::_('PLG_JUMULTITHUMB_GALLERY_CSS_CLASS'); ?>:					</label>
				</td>
				<td>

					<input type="text" id="cssclass" name="cssclass" size="42" />
				</td>
			</tr>
			<tr width="60%">
				<td class="key" align="right">
				</td>
				<td>
                    <button onclick="insertJUGallery();"><?php echo JText::_('PLG_JUMULTITHUMB_GALLERY_INSERT_TAG'); ?></button>
				</td>
			</tr>
		</table>
		</form>

</fieldset>
</body>
</html>
<?php
	function recursiveScanDir($dirName, &$dirs = array()){
		$names = scandir($dirName);

		foreach ($names as $name) {
		        $newDir = $dirName . "/" . $name;
			if (is_dir($newDir) && ($name != '..') && ($name != '.') && ($name != 'index.html')){
				$dir = new StdClass;
				$showdirname = str_replace(JPATH_ROOT, "", $newDir);
				$dir->name = str_replace("/images", "", $showdirname);
				$dir->value = str_replace (JPATH_ROOT.'/images/', '',$newDir);
				$dirs[] = $dir;
				recursiveScanDir($newDir, $dirs);
			}
		}
	}
?>