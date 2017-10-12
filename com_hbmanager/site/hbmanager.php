<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('HbManager');

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));


// TODO: Not needed?
// // Require helper file
// JLoader::register('HbmanagerHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/hbmanager.php');

// Redirect if set by the controller
$controller->redirect();
