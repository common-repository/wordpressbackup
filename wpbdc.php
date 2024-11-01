<?php
/* Load wp variables if it does not work */
if(!function_exists('get_option'))
{
	if(!is_file('../../../wp-load.php'))
	{
		die('DIE: Plugin: can`t find wordpress files');
	} 
	$start=mktime();
	define('WP_USE_THEMES', false);
	if ( !isset($wp_did_header) ) {
		$wp_did_header = true;
		require_once( '../../../wp-load.php' );
	}
}

if(get_option('wpbdc_enabled')!='yes') 
{ 
	die('DIE: Plugin: disabled');
}
	

$KEY=get_option('wpbdc_key');
if(!isset($_GET['key']) || $_GET['key']!==$KEY)
{
	header("HTTP/1.0 404 Not Found"); 
	die();
}

if(get_option('wpbdc_timelimit')=='no' | !is_numeric(get_option('wpbdc_timelimit')))
{
	;
} else {
	if(is_numeric(get_option('wpbdc_timelimit'))) {
		set_time_limit(get_option('wpbdc_timelimit'));
	} else {
		set_time_limit(600);
	}
}


if(get_option('wpbdc_memorylimit')=='no' | !is_numeric(get_option('wpbdc_memorylimit')))
{
	;
} else {
	if(is_numeric(get_option('wpbdc_memorylimit'))) {
		ini_set('memory_limit', get_option('wpbdc_memorylimit').'M');
	} else {
		ini_set('memory_limit', '12M');
	}
}

if(get_option('wpbdc_encryption')=='yes')
{
	//plugin
	$encryptfile=str_replace('wpbdc.php','wpbdc_encryption.php',__FILE__);
	if(is_file($encryptfile))
	{
		require_once $encryptfile;
	} else {
		die('@noencryptionfile');
	}
	if(!function_exists('wpbdc_encryption_ok'))
	{
		die('@noencryptionfile');
	} else {
		if(wpbdc_encryption_ok()==false)
		{
			die('@encryptionnotok');
		}
	}
}

$THESTART="\n# THE"."\n# START"."\n";
$SPLITTER="\n# ROW"."\n# SEPARATOR"."\n";
$THEEND="\n# THE"."\n# END"."\n";

if(isset($_GET['do']))
{
	$DO=$_GET['do'];
} else {
	$DO='nothing';
}

require_once('ext/wpb_services_json.class.php');

switch($DO)
{
	case 'lastmodification':
		break;
		
	case 'test':
		$JSON=new Services_JSON;
		echo $JSON->encode(array('test-reply'=>'ok'));
		break;
		
		
	case 'export-tables';
		require_once('lspl/lspl_mysql_export_table.class.php');

		$lb=get_option('wpbdc_lastbackups');
		if(count($lb)>10)
		{
			$lb=array_shift($lb);
		} 
		if(!is_array($lb)) { $lb=array(); }
		$lb[]=mktime();
		update_option('wpbdc_lastbackups',$lb);
		unset($lb);
		$tables=array();
		
		$o_alltables=get_option('wpbdc_alltables');
		$o_selected=get_option('wpbdc_tablelist');
		
		$result=mysql_query('SHOW TABLES FROM `'.DB_NAME.'` LIKE "'.$wpdb->prefix.'%"');
		if($result)
		{
			while($row=mysql_fetch_array($result))
			{
				if($o_alltables=='yes' || isset($o_selected[$row[0]]))
				{
					$tables[]=$row[0];
				}
			}
		}
		if(count($tables)<=0) { die('@notablesselected'); }
		if(get_option('wpbdc_encryption')!='yes')
		{
			echo $SPLITTER;
			echo $THESTART;
			foreach($tables as $tablenr=>$tablename)
			{
				$TABLE=new lspl_mysql_export_table;
				echo $TABLE->getFullTableExport($tablename);
			}
			echo $THEEND;
		} else {
			$export='';
			$export.=$SPLITTER;
			$export.=$THESTART;
			foreach($tables as $tablenr=>$tablename)
			{
				$TABLE=new lspl_mysql_export_table;
				$export.=$TABLE->getFullTableExport($tablename);
			}
			$export.=$THEEND;
			echo urlencode(wpbdc_encrypt(get_option('wpbdc_encryption_password'),get_option('wpbdc_encryption_iv'),$export));
		}
		break;
}
?>