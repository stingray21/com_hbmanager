<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * HBhallenvz Form Field class for the HBteamHome component
 */
class JFormFieldHBhallenvz extends JFormFieldList
{
        /**
         * The field type.
         *
         * @var         string
         */
        protected $type = 'HBhallenvz';
 
        /**
         * Method to get a list of options for a list input.
         *
         * @return      array           An array of JHtml options.
         */
        protected function getOptions() 
        {
                /*
        		$db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('Kuerzel,completeName');
                $query->from('#__HBteams');
                $db->setQuery((string)$query);
                $messages = $db->loadObjectList();
                $options = array();
                if ($messages)
                {
                        foreach($messages as $message) 
                        {
                                $options[] = JHtml::_('select.option', $message->Kuerzel, $message->completeName);
                        }
                }
                $options = array_merge(parent::getOptions(), $options);
                */
        		$options = array('Modus 1', 'Modus 2');
                return $options;
        }
}