<?php 
/**
 * @version		$Id: archive.php 1618 2012-09-21 11:23:08Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>
<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2ArchivesBlock<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">
  <ul>
    <?php foreach ($months as $month): ?>
    <li>
      <?php if ($params->get('archiveCategory', 0) > 0): ?>
      <a href="<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=date&month='.$month->m.'&year='.$month->y.'&catid='.$params->get('archiveCategory')); ?>">
        <?php echo $month->name.' '.$month->y; ?>
        <?php if ($params->get('archiveItemsCounter')) echo '('.$month->numOfItems.')'; ?>
      </a>
      <?php else: ?>
      <a href="<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=date&month='.$month->m.'&year='.$month->y); ?>">
        <?php echo $month->name.' '.$month->y; ?>
        <?php if ($params->get('archiveItemsCounter')) echo '('.$month->numOfItems.')'; ?>
      </a>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
