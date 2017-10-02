<?php 

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('url');

class JFormFieldLongurl extends JFormFieldUrl {

    protected $type = 'longurl';

    public function getInput() {
        $input = parent::getInput();
        
        $pattern = 'type="url"';
		$replacement = $pattern.' style="width:90%"';
		return str_replace($pattern, $replacement, $input);
    }

}