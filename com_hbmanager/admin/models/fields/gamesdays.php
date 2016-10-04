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
 
class JFormFieldGamedays extends JFormFieldList
{

    public $type = 'gamedays';
	protected $timeframe = null;


	protected function getOptions()
    {
        // Initialize variables.
        $options = array();
        
        // Initialize some field attributes.
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        
        
        // Get the database object.
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
        
       	$query->select("DISTINCT DATE(datumZeit) AS ".$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		if ($this->timeframe === 'prev') {
			$query->where($db->qn('datumZeit').' < NOW()');
		}
		elseif ($this->timeframe === 'next') {
			$query->where($db->qn('datumZeit').' > NOW()');
		}
		$query->order($db->qn('datum').' ASC');
		$db->setQuery($query);

        
        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();
        //echo "<pre>"; print_r($items); echo "</pre>";
        
        // Build the field options.
        if (!empty($items))
        {

        	foreach ($items as $item)
            {
                //$date = JHTML::_('date', $item->datum , 'D, d.m.Y
				$date = JHTML::_('date', $item->datum , 'Y-m-d',
						'Europe/Berlin');
//				$date = JFactory::getDate($item->datum, 'Europe/Berlin' )
//						->format('D, d.m.Y', true);
				if ($translate == true)
                {
                    $options[] = JHtml::_('select.option', $item->datum, 
						JText::_($date));
					
                }
                else
                {
                    $options[] = JHtml::_('select.option', $item->datum, 
						$date);
                }
            }
        }
 
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
 
        return $options;
    }
}