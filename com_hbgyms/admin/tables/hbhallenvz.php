<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Hallen Table class
 */
class HBhallenvzTableHBhallenvz extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
               // parent::__construct('#__HBteams', 'Kuerzel', $db);
        }
}