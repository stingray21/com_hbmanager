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
        $tz = HbmanagerHelper::getHbTimezone();
        
        // Initialize some field attributes.
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        
        
        // Get the database object.
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
        
       	$query->select("DISTINCT DATE(dateTime) AS ".$db->qn('date'));
		$query->from('#__hb_game');
		$query->where($db->qn('ownClub').' = '.$db->q(1));

		if ($this->timeframe === 'prev')      $query->where($db->qn('dateTime').' < NOW()');
		elseif ($this->timeframe === 'next')  $query->where($db->qn('dateTime').' > NOW()');

		$query->order($db->qn('date').' ASC');
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
                $date = JHTML::_('date', $item->date , 'D, d.m.Y',	$tz);
				// $date ($translate == true) ? JText::_($date) : $date; // TODO: does this work?

                $options[] = JHtml::_('select.option', $item->date, $date);
            }
        }
 
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
 
        return $options;
    }
}