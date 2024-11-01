<?php
/**
 * LSPL MySql table export class
 * @package lspl_mysql
 * @subpackage export
 * 
 * for php 4+
 */

class lspl_mysql_export_table {
	var $connection=null;
	
	function escapeData($data)
	{
		return mysql_real_escape_string($data);
	}
	
	function connected() 
	{
		return true;
	}
	
	function getTableDescription($tableName=null)
	{
		if(!$this->connected() || $tableName==null) { return false; }
		$result=mysql_query('DESCRIBE '.$tableName);
		if($result) 
		{ 
			$description=array();
			while($row=mysql_fetch_assoc($result))
			{
				$description[]=$row;
			}
		} else {
			$description=false;
		}
		return $description;
	}
	
	function getTableCreateCommand($tableName=null, $dropIfExists=true)
	{
		global $SPLITTER;
		if(!$this->connected() || $tableName==null) { return false; }
		$result=mysql_query('SHOW CREATE TABLE '.$tableName);
		if($result) 
		{ 
			$row=mysql_fetch_assoc($result);
			if(isset($row['Create Table']))
			{
				$showCreate='';
				if($dropIfExists)
				{
					if(!isset($SPLITTER))
					{
						$sep='';
					} else {
						$sep=$SPLITTER;
					}
					$showCreate.='DROP TABLE IF EXISTS `'.$tableName.'`;'."\n\n".$sep;
				}
				$showCreate.=$row['Create Table'].';'."\n";
			} else {
				$showCreate=false;
			}
		} else {
			$showCreate=false;
		}
		return $showCreate;
	}
	
	function numTableRows($tableName)
	{
		if(!$this->connected() || $tableName==null) { return false; }
		$result=mysql_query('SELECT COUNT(*) FROM `'.$tableName.'`');
		if($result)
		{
			$total = mysql_fetch_array($result); 
			return $total[0];
		} else {
			return 0;
		}
	}
	
	function getTableRows($tableName=null, $start=null,$end=null)
	{
		global $SPLITTER;
		if(!$this->connected() || $tableName==null) { return false; }
		
		$description=$this->getTableDescription($tableName);
		if(!is_array($description)) { return false; }
		
		$i=0;
		$colNames=array();
		$colEscapeType=array();
		foreach($description as $dk=>$dv)
		{
			$colNames[$i]='`'.$dv['Field'].'`';
			$int=strpos($dv['Type'],'int');
			if( $int!==false && $int>0 && $int<7 )
			{
				$colEscapeType[$i]='numeric';
			} else {
				$colEscapeType[$i]='normal';
			}
			$i++;
		}
		unset($description,$dk,$dv,$i,$int);
		
		$sql='SELECT * FROM `'.$tableName.'`';
		if($start!=null && $end!=null && is_numeric($start) && is_numeric($end))
		{
			$sql.=' LIMIT '.$start.','.$end;
		}
		$result=mysql_unbuffered_query($sql);
		$output='';
		if($result)
		{
			while($row=mysql_fetch_array($result))
			{
				$sql='INSERT INTO `'.$tableName.'` ('.implode(', ',$colNames).') VALUES (';
				$maxrk=count($colNames)-1;
				foreach($row as $rk=>$rv)
				{
					if(isset($colEscapeType[$rk]))
					{
						switch($colEscapeType[$rk])
						{
							case 'numeric':
								$sql.=$rv;
								break;
								
							case 'normal':
								$sql.='"'.$this->escapeData($rv).'"';
								break;
						}
						if($rk<$maxrk)
						{
							$sql.=', ';
						}
					}
				}
				$sql.='); ';
				$output.=$sql."\n";
				if(isset($SPLITTER))
				{
					$output.=$SPLITTER;
				}
			}
		}
		mysql_free_result($result);
		return $output;
	}
	
	function getFullTableExport($tableName=null)
	{
		global $SPLITTER;
		if(!isset($SPLITTER))
		{
			$sep='';
		} else {
			$sep=$SPLITTER;
		}
		if(!$this->connected() || $tableName==null) { return false; }
		return $this->getTableCreateCommand($tableName).$sep.$this->getTableRows($tableName);
	}
}
?>