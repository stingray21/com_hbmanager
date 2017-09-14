<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * hbteam Form Field class for the hbteam component
 */
class JFormFieldhbteam extends JFormFieldList
{
        /**
         * The field type.
         *
         * @var         string
         */
        protected $type = 'hbteam';
 
        /**
         * Method to get a list of options for a list input.
         *
         * @return      array           An array of JHtml options.
         */
        protected function getOptions() 
        {
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('kuerzel, mannschaft');
                $query->from('hb_mannschaft');
                $db->setQuery((string)$query);
                $mannschaften = $db->loadObjectList();
                $options = array();
                if ($mannschaften)
                {
                        foreach($mannschaften as $mannschaft) 
                        {
                                $options[] = JHtml::_('select.option', $mannschaft->kuerzel, $mannschaft->mannschaft);
                        }
                }
                $options = array_merge(parent::getOptions(), $options);
                return $options;
        }
}