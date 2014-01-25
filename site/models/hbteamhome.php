<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class HBteamHomeModelHBteamHome extends JModelItem
{
	/**
	 * @var array messages
	 */
	protected $messages;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param       type    The table type to instantiate
	 * @param       string  A prefix for the table class name. Optional.
	 * @param       array   Configuration array for model. Optional.
	 * @return      JTable  A database object
	 * @since       2.5
	 */
	public function getTable($type = 'HBteamHome', $prefix = 'HBteamHomeTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @param  string The corresponding id of the message to be retrieved
	 * @return string The message to be displayed to the user
	 */
	public function getMsg($teamkey = "noteam")
	{
		if (!is_array($this->messages))
		{
			$this->messages = array();
		}
		
		if (!isset($this->messages[$teamkey]))
		{
			//request the selected teamkey
			$menuitemid = JRequest::getInt('Itemid');
			if ($menuitemid)
			{
				$menu = JFactory::getApplication()->getMenu();
				$menuparams = $menu->getParams($menuitemid);
			}
			$teamkey = $menuparams->get('teamkey');
			
			// Get a TableHBteamHome instance
			$table = $this->getTable();

			// Load the message
			$table->load($teamkey);

			// Assign the message
			$this->messages[$teamkey] = $table->mannschaft;
		}

		return $this->messages[$teamkey];
	}
}