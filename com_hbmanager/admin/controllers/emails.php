<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class HbmanagerControllerEmails extends JControllerAdmin
{

	public function updateEmailTemplate($name = 'emails', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $post ,1).'</pre>';
		
		$emailbody = (isset($post['emailtemplate']['emailbody'])) ? $post['emailtemplate']['emailbody'] : null;
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $emailbody ,1).'</pre>';
		
		$model = $this->getModel('emails'); 
		$model->updateTemplateInDB($emailbody);

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=emails', false));
	}	

}
