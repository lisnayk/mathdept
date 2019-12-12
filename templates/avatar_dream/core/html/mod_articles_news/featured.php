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
<div class="avatar-module-featured">
	<?php  if ($list[0]) {
		$item = $list[0];
		require dirname(__FILE__).DS.'_item.php';
	} ?>
	<ul>
	<?php for ($i = 1, $n = count($list); $i < $n; $i ++) :
		$item = $list[$i]; ?>
		<li>
		<?php if ($params->get('item_title')) : ?>
			<h4>
				<a href="<?php echo $item->link;?>">
					<?php echo $item->title;?></a>
			</h4>
		<?php endif; ?>	
		</li>
	<?php endfor; ?>
	</ul>
</div>