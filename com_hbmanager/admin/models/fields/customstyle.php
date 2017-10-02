<?php 

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
//JFormHelper::loadFieldClass('hidden');

class JFormFieldCustomstyle extends JFormField {

    protected $type = 'customstyle';

    public function getInput() {
        return '<style>.longUrl{width:90% !important;}</style>';
    }
}