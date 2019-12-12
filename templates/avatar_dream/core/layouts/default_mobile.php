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
?>
<!DOCTYPE HTML>
<!-- <?php echo Avatar::getTemplateInfo(); ?> -->

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $template->language; ?>" lang="<?php echo $template->language; ?>" dir="<?php echo $template->direction; ?>" >
	<head>
		<jdoc:include type="head" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<?php 
			echo $this->addHead();
			echo $this->addGoogleAnalytics(); 
		?>
	</head>
	<body id="avatar-template">
		<header id="avatar-mobile-header">	
			<?php $mPosHeader = $template->params->get('mobile_pos_header');
			 if ($mPosHeader && $template->countModules($mPosHeader)): ?>
			 	<div class="avatar-pos-wrap">
			 		<jdoc:include type="modules" name="<?php echo $mPosHeader; ?>" style="avatarmodule" />
			 	</div>	
			 <?php endif; ?>	
		</header>
	
		<section id="avatar-mobile-content-top">
			<?php $mPosContentTop = $template->params->get('mobile_pos_content_top');
			 if ($mPosContentTop && $template->countModules($mPosContentTop)): ?>
			 	<div class="avatar-pos-wrap">
					<jdoc:include type="modules" name="<?php echo $mPosContentTop; ?>" style="avatarmodule" />
				</div>	
			 <?php endif; ?>
		</section>
		
		<section id="avatar-mobile-content">
			<?php $mPosContent = $template->params->get('mobile_pos_content');
			 if ($mPosContent && $template->countModules($mPosContent)): ?>
			 	<div class="avatar-pos-wrap">
			 		<jdoc:include type="modules" name="<?php echo $mPosContent; ?>" style="avatarmodule" />
			 	</div>	
			 <?php endif; ?>
			 <div id="avatar-pos-main-body" class="avatar-pos-wrap">
			 	<jdoc:include type="component" />
			 </div>	
		</section>
		
		<section id="avatar-mobile-content-bottom">
			<?php $mPosContentBottom = $template->params->get('mobile_pos_content_bottom');
			 if ($mPosContentBottom && $template->countModules($mPosContentBottom)): ?>
			 	<div class="avatar-pos-wrap">
			 		<jdoc:include type="modules" name="<?php echo $mPosContentBottom; ?>" style="avatarmodule" />
			 	</div>	
			 <?php endif; ?>
		</section>
		
		<footer> 
			<?php $mPosFooter = $template->params->get('mobile_pos_footer');
			 if ($mPosFooter && $template->countModules($mPosFooter)): ?>
			 	<div class="avatar-pos-wrap">
			 		<jdoc:include type="modules" name="<?php echo $mPosFooter; ?>" style="avatarmodule" />
			 	</div>	
			 <?php endif; ?>
		</footer>
	</body>
</html>