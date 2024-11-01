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
	die('Sorry, plugin disabled');
}

require_once('wpbdc_encryption.php');

if(wpbdc_encryption_ok()==false) 
{
	die('Sorry, encryption not set up properly');
}

$wud = wp_upload_dir('');
$wud=$wud['basedir'].'/wordpressbackupdecrypt.sql';
if(is_file($wud)) { unlink($wud); }

if(isset($_POST['decrypt']))
{
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
else
  {
	  move_uploaded_file($_FILES["file"]["tmp_name"], $wud);
	  $handle=@fopen($wud,'r');
	  if($handle)
	  {
	  	$contents = fread($handle, $_FILES["file"]["size"]);
	  	fclose($handle);
	  	if(strlen($contents)>0)
	  	{
		  	$contents=wpbdc_decrypt(get_option('wpbdc_encryption_password'),get_option('wpbdc_encryption_iv'),urldecode($contents));
		  	unlink($wud);
			header("Content-Description: File Transfer"); 
			header("Content-Transfer-Encoding: binary"); 
			header("Content-Disposition: attachment; ".'filename="'.str_replace('.','.decrypted.',$_FILES["file"]["name"]));
			header("Content-Type: application/download");
		  	echo $contents;
		  	die();
	  	} else {
	  		echo "Error reading uploaded file";
	  	}
	  } else {
	  	echo "Error at opening file";
	  }
	  echo '<br>';
  }
}

?>
<form method="post" action="" style="padding:3px;" enctype="multipart/form-data">
<label for="file">File:</label>
<input type="file" name="file" id="file" name="file"/>
<input style="display:inline;"  type="submit" name="decrypt" value="Decrypt!">
</form>
