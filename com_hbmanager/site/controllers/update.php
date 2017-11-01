<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hbmanager Controller
 *
 * @since  0.0.1
 */
class HbmanagerControllerUpdate extends JControllerLegacy
{
	
	// public function getModel($name = 'HelloWorld', $prefix = 'HelloWorldModel', $config = array('ignore_request' => true))
	// {
	// 	$model = parent::getModel($name, $prefix, $config);

	// 	return $model;
	// }
	public function update()
	{
		echo __FILE__.' ('.__LINE__.'):<pre>TEST UPDATE</pre>';
		// // Get the input
		// $input = JFactory::getApplication()->input;
		// // echo __FILE__.'('.__LINE__.'):<pre>';print_r($input);echo'</pre>';
		// $pks = $input->post->get('cid', array(), 'array');

		// // Sanitize the input
		// JArrayHelper::toInteger($pks);

		// Get the model
		$model = $this->getModel();

		// $return = $model->test($pks);

		// Redirect 
		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=cronjob&format=raw&viewoption=plain', false));

	}
}
