<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * HBteamHome Form Field class for the HBteamHome component
 */
class JFormFieldHBteamHome extends JFormFieldList
{
        /**
         * The field type.
         *
         * @var         string
         */
        protected $type = 'HBteamHome';
 
        /**
         * Method to get a list of options for a list input.
         *
         * @return      array           An array of JHtml options.
         */
        protected function getOptions() 
        {
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('teamkey, team');
                $query->from('hb_teams');
                $db->setQuery((string)$query);
                $messages = $db->loadObjectList();
                $options = array();
                if ($messages)
                {
                        foreach($messages as $message) 
                        {
                                $options[] = JHtml::_('select.option', $message->teamkey, $message->team);
                        }
                }
                $options = array_merge(parent::getOptions(), $options);
                return $options;
        }
}