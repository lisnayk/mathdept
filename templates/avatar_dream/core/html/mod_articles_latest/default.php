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
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :  ?>
	<li>
		<?php 
			$itemParams = $item->params; 
		?>
		<?php if ($itemParams->get('show_title')) : ?>
			<h2>
				<?php if ($itemParams->get('link_titles') && $itemParams->get('access-view')) : ?>
					<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
					<?php echo $item->title; ?></a>
				<?php else : ?>
					<?php echo $item->title; ?>
				<?php endif; ?>
			</h2>
		<?php endif; ?>
		<p>
		<?php if ($itemParams->get('show_author') && !empty($item->author )) : ?>
			<?php $author =  $item->author; ?>
			<?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>
			<?php if (!empty($item->contactid ) &&  $itemParams->get('link_author') == true):?>
				<?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
						 JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$item->contactid), $author)); ?>
			<?php else :?>
				<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
			<?php endif; ?>
			
		<?php endif; ?>
		
		<?php if ($itemParams->get('show_create_date')) : ?>
			<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		<?php endif; ?>
		
		<?php if ($itemParams->get('show_modify_date')) : ?>
			<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		<?php endif; ?>
		
		<?php if ($itemParams->get('show_publish_date')) : ?>
			<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		<?php endif; ?>
		</p>
	</li>
<?php endforeach; ?>
</ul>
