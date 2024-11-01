<div style="font-size:9px; font-family:arial;">
<?php
if(isset($_GET['url']))
{
	if(!isset($_GET['ok']))
	{
		echo '<form method="get" action="">';
		echo '<input type="hidden" name="url" id="url" value="'.$_GET['url'].'">';
		echo '<input type="submit" name="ok" id="ok" value="Self-test">';
		echo '</form>';
	} else {
		echo '<a href="'.$_GET['url'].'" target="_blank">'.$_GET['url'].'</a><br>';
		$handle = @fopen( $_GET['url'], "r", false);
		if($handle)
		{
			$data = '';
			while ( !feof( $handle ) )
			    $data .= fread( $handle, 8192 );
			fclose( $handle );
		} else { 
			echo '@no-reply';
			die();
		}
		if(trim(substr($data,0,4))=='DIE:') { 
			echo $data;
			die();	
		} else {
			echo 'REPLY: '.substr($data,0,50);
			die();
		}
	}
} else {
	echo 'no url ??';
}
?></div>