<?php
/**
 * @version     $Id: k2extrafields.php 1618 2012-09-21 11:23:08Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die ;

class JFormFieldK2ExtraFields extends JFormField
{

    protected $type = 'K2ExtraFields';

    protected function getInput()
    {
        $document = JFactory::getDocument();
        $document->addScriptDeclaration('var K2BasePath = "'.JURI::root(true).'";');
        $document->addScript(JURI::root(true).'/plugins/josetta_ext/k2item/fields/k2extrafields.js');

        K2Model::addIncludePath(JPATH_SITE.'/components/com_k2/models');
        JLoader::register('K2HelperUtilities', JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
        $model = K2Model::getInstance('Item', 'K2Model');
        $extraFields = $model->getItemExtraFields($this->value);
        $html = '<div id="extraFieldsContainer">';
        if (count($extraFields))
        {
            $html .= '<table class="admintable" id="extraFields">';
            foreach ($extraFields as $extraField)
            {
                $html .= '<tr>
                <td align="right" class="key">'.$extraField->name.'</td>
                <td>'.$extraField->element.'</td>
                </tr>';
            }
            $html .= '</table>';
        }
        else
        {
            $html .= '<span class="k2Note"> '.JText::_('K2_PLEASE_SELECT_A_CATEGORY_FIRST_TO_RETRIEVE_ITS_RELATED_EXTRA_FIELDS').' </span>';
        }
        $html .= '</div>';

        return $html;
    }

}
