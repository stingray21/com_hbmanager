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
 
class JFormFieldGame extends JFormFieldList
{

    public $type = 'game';
    public $teamkey = 'M-1';

	// TODO set default to current season

	protected function getOptions()
    {
        // Initialize variables.
        $options = array();
                
        
        // Get the database object.
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
        $query->select('spielIdHvw AS gameId, heim, gast, DATE(`datumZeit`) AS `datum`');
        $query->from('hb_spiel');
        $query->leftJoin($db->qn('hb_spielbericht').' USING ('.$db->qn('spielIdHvw').')');
        $query->leftJoin($db->qn('hb_spielbericht_details').' USING ('.$db->qn('spielIdHvw').')');
        $query->group('hb_spiel.spielIdHvw, hb_spiel.datumZeit,spielberichtId');
        $query->where('hb_spiel.'.$db->qn('kuerzel').' = '.$db->q($this->teamkey));
        $query->where($db->qn('eigenerVerein').' = 1');
        $query->where('DATE('.$db->qn('datumZeit').') < NOW() ');
        $query->order($db->qn('datumZeit').' ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        // echo __FILE__.'('.__LINE__.'):<pre>';print_r($items);echo'</pre>';
        
        $timezone = false; //false: server time
		
        // Build the field options.
        if (!empty($items))
        {
        	foreach ($items as $item)
            {
                $key = $item->gameId;
                $value = JHtml::_('date', $item->datum, 'd.m.Y', $timezone).' '.$item->heim.'-'.$item->gast.' ('.$item->gameId.')';
				$options[] = JHtml::_('select.option', $key, $value);
            }
        }
 
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
 
        return $options;
    }
}