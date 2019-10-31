<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HB Manager component helper.
 */

abstract class HbArticle
{
	// TODO add category
	public static function writeArticle($model, $alias, $title, $content)
	{
		$db = JFactory::getDBO();
		
		$table = JTable::getInstance('Content', 'JTable', array());

		$data = array(
				'alias' => $alias,
				'title' => $title,
				'introtext' => $content,
				// for text that appears by clicking on 'more'
				//'fulltext' => '',
				'state' => 1,
				'catid' => 2,
				'featured' => 1,
				'language' => '*'
		);

		// Bind data
		if (!$table->bind($data))
		{
			$model->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$model->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$model->setError($table->getError());
			return false;
		}

		//To reorder the category
		//$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');

		// put article on the frontpage

		// get content_ID
		$query = $db->getQuery(true);
		$query->select($db->qn('id'));
		$query->from($db->qn('#__content'));
		$query->where($db->qn('alias').' = '.$db->q($alias));
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$contentID = $db->loadResult();
		//echo '=> model->$contentID<br><pre>'; 
		//print_r($contentID); echo "</pre>";

		// increment the order of the articles that are already on the frontpage
		$query = $db->getQuery(true);
		$query->update($db->qn('#__content_frontpage'));
		$query->set($db->qn('ordering').' = '.$db->qn('ordering').'+1');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		}
		catch (Exception $e) {
			// catch any database errors.
		}

		// insert in frontpage DB table
		$columns = array('content_id', 'ordering');
		$values = array($db->q($contentID), 1);
		$query = $db->getQuery(true);
		$query->insert($db->qn('#__content_frontpage'));
		$query->columns($db->qn($columns));
		$query->values(implode(',', $values));
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			$result = $db->query();
		}
		catch (Exception $e) {
			// catch any database errors.
		}
	}
	
}
