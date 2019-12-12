<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_news
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="avatar-module-featured-image">
	<ul>
	<?php for ($i = 1, $n = count($list); $i < $n; $i ++) :
		$item = $list[$i];
		var_dump($item->images);die();
		$images = json_decode($item->images); 
	?>
		<li>
		<?php  if (isset($images->image_intro) and !empty($images->image_intro)) : ?>
			<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>
		
			<div class="img-intro-<?php echo htmlspecialchars($imgfloat); ?>">
			<img
				<?php if ($images->image_intro_caption):
					echo 'class="caption"'.' title="' .htmlspecialchars($images->image_intro_caption) .'"';
				endif; ?>
				src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
			</div>
		<?php endif; ?>
		<?php if ($params->get('item_title')) : ?>
			<<?php echo $params->get('item_heading'); ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
				<a href="<?php echo $item->link;?>"><?php echo $item->title;?></a>
			</<?php echo $params->get('item_heading'); ?>>
		<?php endif; ?>
		<p><?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?></p>
		</li>
	<?php endfor; ?>
	</ul>
</div>