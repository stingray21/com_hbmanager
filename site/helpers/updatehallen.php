<?php  

  //--------------------------------------------------------------------------
  // Example php script for fetching data from mysql database
  //--------------------------------------------------------------------------
  /*
  $host = "localhost";
  $user = "root";
  $pass = "root";

  $databaseName = "ajax01";
  $tableName = "variables";

  //--------------------------------------------------------------------------
  // 1) Connect to mysql database
  //--------------------------------------------------------------------------
  include 'DB.php';
  $con = mysql_connect($host,$user,$pass);
  $dbs = mysql_select_db($databaseName, $con);

  //--------------------------------------------------------------------------
  // 2) Query database for data
  //--------------------------------------------------------------------------
  $result = mysql_query("SELECT * FROM $tableName");          //query
  $array = mysql_fetch_row($result);                          //fetch result    
*/
  //--------------------------------------------------------------------------
  // 3) echo result as json 
  //--------------------------------------------------------------------------

define( '_JEXEC', 1 );
define( 'DS', '/' );
define( 'JPATH_BASE', $_SERVER[ 'DOCUMENT_ROOT' ].DS.'handball'.DS.'hb' );

require_once( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
require_once( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
require_once( JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php' );


$mainframe =& JFactory::getApplication('site');


$jinput = JFactory::getApplication()->input;
$teamkey = $jinput->get('teamkey', 'all');

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('DISTINCT '.$db->qn('hallenNummer').', '.$db->qn('halleID').', '.
		$db->qn('kurzname').', '.$db->qn('name').', '.
		$db->qn('plz').', '.$db->qn('stadt').', '.$db->qn('strasse').', '.
		$db->qn('telefon').', '.$db->qn('haftmittel'));
if ($teamkey == 'allGyms') $query->from('aaa_halle') ;
else {
	$query->from('aaa_spiel');
	if ($teamkey != 'all') $query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
	$query->join('INNER',$db->qn('aaa_halle').' USING ('.$db->qn('hallenNummer').')');
}
$query->order($db->qn('hallenNummer'));

//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
$db->setQuery($query);
$hallen = $db->loadObjectList();

//echo "<pre>"; print_r($hallen); echo "</pre>";

echo json_encode($hallen);

?>
