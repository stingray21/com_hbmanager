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

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/fields/gamedays.php';
 
class JFormFieldGamedaysPrev extends JFormFieldGamedays
{

	protected function getOptions()
    {
        $this->timeframe = 'prev';
		
		$options = parent::getOptions();
 
        return $options;
    }
}