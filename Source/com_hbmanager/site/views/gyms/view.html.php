<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewGyms extends JViewLegacy
{
	/**
	 * Display the HB Manager view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

		// Display the view
		parent::display($tpl);
	}


}


// 		$document = JFactory::getDocument();
// 		// local jquery
// 		//$document->addScript(JURI::Root().'media/com_hbgyms/js/jquery-2.0.3.js);
// 		$document->addScript('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');
// 		JHtml::_('jquery.framework');
		
// 		$document->addScript(JURI::Root().'media/com_hbgyms/js/maps_gyms.js');
// 		if(strpos(JURI::Root(), 'localhost') !== false) {
// 			$document->addScript('./media/com_hbgyms/js/maps_gyms.js');
// 		}
		
// 		$model = $this->getModel('hbgyms');
// 		//$this->assignRef('model', $model);
		
// 		$showMap = true;
// 		$this->assignRef('showMap', $showMap);
// 		//echo "<pre>"; print_r($showMap); echo "</pre>";
		
// 		$teams = $model->getTeams();
// 		$this->assignRef('teams', $teams);
// 		//echo "<pre>"; print_r($teams); echo "</pre>";

// 		$gyms = $model->getGyms('all');
// 		$this->assignRef('gyms', $gyms);
// 		//echo "<pre>"; print_r($gyms); echo "</pre>";
		
// 		// TODO backend option
// 		$start = 'Schloßparkhalle, Schloßplatz, Geislingen, Deutschland';
// 		$this->assignRef('start', $start);
		
// 		//$post = JRequest::get('post');
// 		//echo "<pre>"; print_r($post); echo "</pre>";
// 		//$this->assignRef('post', $post);
		
// 		$jinput = JFactory::getApplication()->input;
// 		$focus = $jinput->get('focus', '');
// 		$this->assignRef('focus', $focus);
// 		//echo __FILE__.'('.__LINE__.'):<pre>'.$focus.'</pre>';
		
// //		$style = '#gym'.$focus.' {
// //			background-color:#900;
// //			}';
// //		$document->addStyleDeclaration( $style );
		
// 		//$document->addStyleSheet('./com_hbgyms/site.stylesheet.css', array(), true);
// 		JHtml::stylesheet('com_hbgyms/site.stylesheet.css', array(), true);

