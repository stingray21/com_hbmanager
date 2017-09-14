<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
 
defined('JPATH_PLATFORM') or die;
 
JFormHelper::loadFieldClass('list');
 
class JFormFieldSeason extends JFormFieldList
{

    public $type = 'season';

	// TODO set default to current season

	protected function getOptions()
    {
        // Initialize variables.
        $options = array();
                
        
        // Get the database object.
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
        
       	$query->select("DISTINCT saison");
		$query->from('hb_spiel');
		//$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		
		$query->order($db->qn('saison').' ASC');
		$db->setQuery($query);

        
        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadColumn();
        //echo __FILE__.'('.__LINE__.'):<pre>';print_r($items);echo'</pre>';
        
		$year = strftime('%Y');
		if (strftime('%m') < 8) {
			$year = $year-1;
		}
		$currentSeason = $year.'-'.($year+1);
		if (!in_array($currentSeason, $items)) {
			array_unshift($items, $currentSeason);
		}
		
        // Build the field options.
        if (!empty($items))
        {
        	foreach ($items as $item)
            {
				$options[] = JHtml::_('select.option', $item, $item);
            }
        }
 
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
 
        return $options;
    }
}