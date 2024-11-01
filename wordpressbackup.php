<?php
/*
Plugin Name: WordPress Online Automated Backup
Plugin URI: http://www.WordPressBackup.com
Description: Plugin that allows you to backup and restore your wordpress blog very easily. Read <a href="http://www.wordpressbackup.com/how-it-works/" target="_blank">how it works</a> before installing. Requires an account on <a target="_blank" href="http://wordpressbackup.com">WordPressBackup.com</a> (free acounts available). 
Version: 0.8.2
Author: LiquidStudios
Author URI: http://www.liquidstudios.ro/en/services.html#software-web
*/


function wpbdc_menu_integration() {
	$imgsuffix='';
	$lb=get_option('wpbdc_lastbackups');
	$max=0;
	if(is_array($lb) && count($lb)>0)
	{
		$max=$lb[ count($lb)-1 ];
	}
	if(get_option('wpbdc_allowrestore')=='yes') 
	{ 
		$imgsuffix='_allowrestore'; 
	} elseif(is_array($lb)&&count($lb)>0 && (mktime()-$max<3600*24)) {
		$imgsuffix='_backuped';
	} elseif(is_array($lb)&&count($lb)>0) {
		$imgsuffix='_backuped_old';
	} elseif(!is_array($lb) || count($lb)==0)  {
		$imgsuffix='';
	}
	
	add_menu_page('wpBackup','wpBackup',2,__FILE__,'wpbdc_dashboard','../wp-content/plugins/wordpressbackup/wpbdc'.$imgsuffix.'.png');
	add_submenu_page(__FILE__,'wpBackup Options','Options',10,'wpdc-options','wpbdc_page_options');
	add_submenu_page(__FILE__,'wpBackup Encryption','Encryption',10,'wpbdc-encryption','wpdbc_page_encryption');
	if(get_option('wpbdc_encryption')=='yes')
	{
		add_submenu_page(__FILE__,'wpBackup Decrypt','Decrypt',10,'wpbdc-decrypt','wpdbc_page_decrypt');
	}
	add_submenu_page(__FILE__,'wpBackup Database Tables','Database',10,'wpbdc-tables','wpbdc_dbtables');
	add_submenu_page(__FILE__,'wpBackup Self Test','SelfTest',10,'wpbdc-selftest','wpbdc_self_test');
}

function wpbdc_random_key()
{
	update_option('wpbdc_key',
		md5(uniqid().__FILE__.bloginfo('name'))
		.md5(uniqid())
		.md5(mktime())
		);
}

function wpbdc_install_or_update()
{
	update_option('wpbdc_enabled','yes');
	add_option('wpbdc_key',md5(md5(__FILE__.'-'.bloginfo('name'))).md5(uniqid()).md5(mktime()),null,false);
	add_option('wpbdc_lastbackups',array(),null,false);
	add_option('wpbdc_allowrestore','no',null,false);
	add_option('wpbdc_alltables','yes',null,false);
	add_option('wpbdc_tablelist',array(),null,false);
	add_option('wpbdc_timelimit','no',null,false);
	add_option('wpbdc_memorylimit','no',null,false);
	add_option('wpbdc_encryption','no',null,false);
	add_option('wpbdc_encryption_password','',null,false);
	add_option('wpbdc_encryption_iv','',null,false);
}

function wpbdc_deactivate()
{
	update_option('wpbdc_enabled','no');
}

function wpbdc_dashboard() {
	echo '<div class="wrap">';
	echo '<h2>WordPressBackup<sup>.com</sup> <span style="font-size:0.5em;">(<a target="_blank" href="http://www.wordpressbackup.com/backup/">open site</a>)</span></h2>';
	echo '<p>This plugin allows you to backup and restore your wordpress blog very easily. Read <a href="http://www.wordpressbackup.com/how-it-works/" target="_blank">how it works</a> before installing. Requires an account on <a target="_blank" href="http://wordpressbackup.com">WordPressBackup.com</a>. We intend to give out as many free acounts as we can but the service will be limited on people donations or help.</p>';

	echo '<h2><img src="../wp-content/plugins/wordpressbackup/wpbdc_backuped.png">Last backup(s)</h2>';
	$lb=get_option('wpbdc_lastbackups');
	if(is_array($lb) && count($lb)>0)
	{
		echo '<ul>';
		foreach($lb as $key=>$value)
		{
			echo '<li>'.date('d m Y @ H:i:s',$value).'</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No backups.. yet.</p>';
	}
	
	echo '<h2>Oh, the images in the menu, you say? Have you seen them changing? They do mean something...</h2>';
	echo '<p><img src="../wp-content/plugins/wordpressbackup/wpbdc.png"> Plugin installed but unused.</p>';
	echo '<p><img src="../wp-content/plugins/wordpressbackup/wpbdc_allowrestore.png"> Restore turned on. Be carefull!</p>';
	echo '<p><img src="../wp-content/plugins/wordpressbackup/wpbdc_backuped.png"> Site has been backuped in the last 24 hours. We love this image ;).</p>';
	echo '<p><img src="../wp-content/plugins/wordpressbackup/wpbdc_backuped_old.png"> One or more backups have been made, buut not in the last 24 hours. Hmm..</p>';
	
	echo '<h2>Wishes..</h2>';
	echo '<p>The WordPressBackup.com team wishes you a year with less worries ;).</p>';

	echo '</div>';
}

function wpbdc_page_options()
{
	if(isset($_POST['wpdbc_key']))
	{
		$value=trim($_POST['wpdbc_key']);
		update_option('wpbdc_key',$value);
	}elseif(isset($_POST['wpbdc_regenrandom'])) {
		wpbdc_random_key();
	}
	
	if(isset($_POST['allowrestore']))
	{
		if($_POST['allowrestore']=='yes')
		{
			update_option('wpbdc_allowrestore','yes');
		} else {
			update_option('wpbdc_allowrestore','no');
		}
		
	} 
	
	if(isset($_POST['wpbdc_changemem']))
	{
		if($_POST['wpbdc_mem']=="no" || is_numeric($_POST['wpbdc_mem']))
		{
			update_option('wpbdc_memorylimit',$_POST['wpbdc_mem']);
		}
	}
	
	if(isset($_POST['wpbdc_changetime']))
	{
		if($_POST['wpbdc_time']=="no" || is_numeric($_POST['wpbdc_time']))
		{
			update_option('wpbdc_timelimit',$_POST['wpbdc_time']);
		}
	}
	
	echo '<div class="wrap">';
	echo '<h2>Options for WordPressBackup<sup>.com</sup> <span style="font-size:0.5em;">(<a target="_blank" href="http://www.wordpressbackup.com/backup/">open site</a>)</span></h2>';
	
	echo '<h2><img src="../wp-content/plugins/wordpressbackup/wpbdc_backuped_old.png">Site key *</h2>';
	echo '<p>* Also known as "wordpress key" or "backup key", this is the key that allows the wordPressBackup.com site to download and upload the data on your blog.</p>';
	echo '<p><b>It is HIGLY RECOMMENDED that you write one yourself. It can be as long as 256 characters.<br> 
	One is automatically created for you when you install the plugin but most people put a pass-phrase they can remember if needed.<br>
	Spaces at the begining and the end are automatically cut.</b></p>';
	echo '<p><form style="display:inline;" method="post" action=""><label for="wpdbc_key"><span class="text">Key: <input type="text" name="wpdbc_key" value="'.get_option('wpbdc_key').'" style="width:90%;">
	<input type="submit" name="wpbdc_changekey" id="wpbdc_changekey" value="Click here to save the key after you write a new one"></form> or <form style="display:inline;" method="post" action=""><input type="submit" name="wpbdc_regenrandom" id="wpbdc_regenrandom" value="Generate a new random key"></form></p>';
	echo '<p>After you change it here, you need to write it on the WordPress.com site, too.</p>';
		  
	echo '<h2><img src="../wp-content/plugins/wordpressbackup/wpbdc_allowrestore.png">Restoring data</h2>';
	echo '<form style="display:inline;" method="post" action=""><label for="allowrestore">Allow restore of data from WordPressBackup? <select style="display:inline;"  name="allowrestore" id="allowrestore">';
	
	$selyes=''; $selno='';
	if(get_option('wpbdc_allowrestore')=='yes') { $selyes='selected'; } 
	else { $selno='selected'; }
	
	echo '<option '.$selno.' value="no">no</option>';
	echo '<option '.$selyes.' value="yes">yes</option>';
	
	echo '</label><input style="display:inline;"  type="submit" value="Save sellection">';
	echo '</form>';
	echo '<p>Set this to YES to allow restore of data. If this is set to no, data can only be backed up. Use it to prevent accidental restoring of data.<p>';
	
	echo '<h2>Memory Limit</h2>';
	echo '<p><form style="display:inline;" method="post" action=""><label for="wpbdc_mem"><span class="text">Memory (in MB, enter "1", "2", .. or "no" without quotes): <input type="text" name="wpbdc_mem" value="'.get_option('wpbdc_memorylimit').'" style="width:90%;">
	<input type="submit" name="wpbdc_changemem" id="wpbdc_changemem" value="Save"></form></p>';
	
	echo '<h2>Time Limit</h2>';
	echo '<p><form style="display:inline;" method="post" action=""><label for="wpbdc_time"><span class="text">Maximum seconds the backup script can run (enter "60", "120", etc. or "no" to disable option): <input type="text" name="wpbdc_time" value="'.get_option('wpbdc_timelimit').'" style="width:90%;">
	<input type="submit" name="wpbdc_changetime" id="wpbdc_changemem" value="Save"></form></p>';
	
	
	echo '<h2><img src="../wp-content/plugins/wordpressbackup/blog_url.png">Blog url</h2>';
	$bu=get_option('siteurl');
	if($bu[strlen($bu)-1]!='/')
	{
		$bu=$bu.'/';
	}
	$bu=str_replace('http://','',$bu);
	$bu=str_replace('//','/',$bu);
	$bu='http://'.$bu;
	echo '<p>Your blog url on the wordPressBackup.com site should be <input style="display:block; width:90%;" type="text" value="',$bu,'"></p>';

	echo '</div>';
}

function wpbdc_self_test()
{
	echo '<div class="wrap">';
	echo '<h2>Self-test for WordPressBackup<sup>.com</sup> <span style="font-size:0.5em;">(<a target="_blank" href="http://www.wordpressbackup.com/backup/">open site</a>)</span></h2>';

	echo '<p>Backup might work even if the self-test does not.</p>';
	$bu=get_option('siteurl');
	if($bu[strlen($bu)-1]!='/')
	{
		$bu=$bu.'/';
	}
	$bu=str_replace('http://','',$bu);
	$bu=str_replace('//','/',$bu);
	$bu='http://'.$bu;
	$surl=str_replace('http://','',$bu);
	if($surl[strlen($surl)-1]!='/')
	{
		$surl.='/';
	}
	$BURL='http://'.$surl.'wp-content/plugins/wordpressbackup/wpbdc.php?do=test&key='.get_option('wpbdc_key');
	echo '<h3>Normal <b>Plugin API</b> method</h3><iframe style="display:block; overflow:scroll; height:100px; width:100%; border:1px solid black;" src="../wp-content/plugins/wordpressbackup/wpbdc_test_url.php?url='.urlencode( $BURL ).'"></iframe>';
	echo '<p>If you don`t see <b>REPLY: {"test-reply":"ok"}</b> above you might try the </p>';
	
	$BURL='http://'.$surl.'wpbdc.php?do=test&key='.get_option('wpbdc_key');
?><h3>Blog <b>Root File API</b> method</h3>

<p>Steps to enable it:</p>
 <ul>
   <li>Create a file named 'wpbdc.php'</li>
   <li>Put the following code in it: 
<pre style="border:1px solid red; padding:3px;">
&lt;?php
if(is_file('wp-content/plugins/wordpressbackup/wpbdc.php'))
{ 
	define('WP_USE_THEMES', false);
	if ( !isset($wp_did_header) ) {
		$wp_did_header = true;
	}
	require_once('wp-load.php');
	require_once('wp-content/plugins/wordpressbackup/wpbdc.php');
}
?&gt;
</pre>
	</li>
	<li>Upload it to your blog root folder (where wp-config.php is located)</li>
	<li>Use the test button bellow to test it. Even if it fails here you might want to try setting the API type to <b>Root File Api</b> on the WordPressBackup site.</li>
 </ul>
<?php 
	echo '<iframe style="display:block; overflow:scroll; height:100px; width:100%; border:1px solid black;" src="../wp-content/plugins/wordpressbackup/wpbdc_test_url.php?url='.urlencode( $BURL ).'"></iframe>';
	
	require_once(str_replace('wordpressbackup.php','wpbdc_encryption.php',__FILE__));
	if(function_exists('wpbdc_encryption_list'))
	{
		echo '<h3>Encryption</h3>';
		if(get_option('wpbdc_encryption','no')=='yes' && wpbdc_encryption_list()==true && wpbdc_encryption_ok()==true)
		{
			$pass=get_option('wpbdc_encryption_pass');
			$iv=get_option('wpbdc_encryption_iv');
			
			$tx=array();
			$tx['text']=md5(uniqid().mktime());
			$tx['encrypted']=wpbdc_encrypt($pass,$iv,$tx['text']);
			$tx['decrypted']=wpbdc_decrypt($pass,$iv,$tx['encrypted']);
			
			echo '<ul>';
			foreach($tx as $nm => $value)
			{
				echo '<li>'.$nm;
				if($nm=='encrypted')
				{
					echo '(urlencoded)';	
				}
				echo ': '.urlencode($value).'</li>';
			}
			echo '</ul>';
			if($tx['text']===$tx['decrypted'])
			{
				echo '<p style="color:blue;">WORKS!</p>';
			} else {
				echo '<p style="color:red;">TEXT and DECRYPTED don`t match</p>';	
			}
		} else {
			echo '<p>.. disabled or misconfigured. Check <a href="admin.php?page=wpbdc-encryption">encryption settings</a>.</p>';	
		}
	}
	
	echo '</div>';
	
}

function wpbdc_dbtables()
{
	global $wpdb;
	if(isset($_POST['alltables']))
	{
		if($_POST['alltables']=='yes')
		{
			update_option('wpbdc_alltables','yes');
		} else {
			update_option('wpbdc_alltables','no');
		}
		
	} 
	
	if(isset($_POST['setencryption']))
	{
		update_option('wpbdc_encryption',$_POST['setencryption']);
	} 
	
	if(isset($_POST['savetablelist']))
	{
		$result=mysql_query('SHOW TABLES FROM `'.DB_NAME.'` LIKE "'.$wpdb->prefix.'%"');
		if($result)
		{
			while($row=mysql_fetch_array($result))
			{
				$tables[]=$row[0];
			}
		}
		$selected=array();
		foreach($tables as $key=>$value)
		{
			if(isset($_POST['tname_'.$value]))
			{
				$selected[$value]=true;
			}
		}
		update_option('wpbdc_tablelist',$selected);
		unset($selected,$result,$row,$tables,$key,$value);
	}
	
	echo '<div class="wrap">';
	echo '<h2>Database backup options for WordPressBackup<sup>.com</sup> <span style="font-size:0.5em;">(<a target="_blank" href="http://www.wordpressbackup.com/backup/">open site</a>)</span></h2>';
	
	echo '<form style="display:inline;" method="post" action=""><label for="allowrestore">What to backup: <select style="display:inline;"  name="alltables" id="alltables">';
	$selyes=''; $selno='';
	if(get_option('wpbdc_alltables')=='yes') { $selyes='selected'; } 
	else { $selno='selected'; }
	echo '<option '.$selno.' value="no">selected tables</option>';
	echo '<option '.$selyes.' value="yes">all tables</option>';

	echo '</label><input style="display:inline;"  type="submit" value="Change">';
	echo '</form>';
	
	if(get_option('wpbdc_alltables')=='yes')
	{ ?>
		<p><strong>Now</strong>: Backing up all WordPress-related tables.</p>
	<?php } else { ?>
		<p><strong>Now</strong>: Backing up only selected tables.</p>
	<?php }
	
	if(get_option('wpbdc_alltables')=='no')
	{
		echo '<h2>Table list</h2>';
		$sel=get_option('wpbdc_tablelist');
		
		$result=mysql_query('SHOW TABLES FROM `'.DB_NAME.'` LIKE "'.$wpdb->prefix.'%"');
		if($result)
		{
			while($row=mysql_fetch_array($result))
			{
				$tables[]=$row[0];
			}
		}
		if(count($tables)==0)
		{
			echo '<p>No tables, sorry...</p>';
		} else {
			echo '<form method="post" action=""><ul>';

			foreach($tables as $key=>$value)
			{
				if(isset($sel[$value]))
				{
					$ch=' CHECKED ';	
				} else {
					$ch='';	
				}
				echo '<li><label for="tname_'.$value.'"><input '.$ch.' type="checkbox" id="tname_'.$value.'" name="tname_'.$value.'">'.$value.'</label></li>';		
			}
			
			echo '</ul><input style="display:inline;"  type="submit" name="savetablelist" value="Save changes."></form>';
		}
	}
	echo '</div>';
}

function wpdbc_page_encryption()
{
	
	require_once(str_replace('wordpressbackup.php','wpbdc_encryption.php',__FILE__));

	echo '<div class="wrap">';
	echo '<h2>Encryption for WordPressBackup<sup>.com</sup> <span style="font-size:0.5em;">(<a target="_blank" href="http://www.wordpressbackup.com/backup/">open site</a>)</span></h2>';
	
	if(wpbdc_encryption_list()==false)
	{
		echo '<p><strong>Sorry, your website host does not support php encryption (ask for "php mcrypt extension").</strong></p>';
		echo '<p>We`re working on a host-independent encryption method but it will take a bit of time.</p>';
	} else {
		
		//echo get_option('wpbdc_encryption','no').'<hr>';
		
		if(isset($_POST['saveencryption']))
		{
			$zx=array();
			$zx['enabled']=$_POST['enc_enabled'];
			$zx['pass']=$_POST['enc_pass'];
			$zx['iv']=$_POST['enc_iv'];
			
			if($zx['enabled']=='yes')
			{
				update_option('wpbdc_encryption','yes');
			} else {
				update_option('wpbdc_encryption','no');
			}
			
			$err=array();
			if(strlen($zx['pass'])>wpbdc_max_password())
			{
				$err[]='Maximum password #1 length: '.wpbdc_max_password().', now is '.strlen($zx['pass']);
			} 
			update_option('wpbdc_encryption_password',$zx['pass']);

			if(strlen($zx['pass'])!==wpbdc_min_iv())
			{
				$err[]='Password #2 length should be '.wpbdc_min_iv();
			}
			update_option('wpbdc_encryption_iv',$zx['iv']);
		} 
		
		if(!isset($err) || !is_array($err) || count($err)>0)
		{
			$err=array();
			if(get_option('wpbdc_encryption')=='yes')
			{
				if(strlen(get_option('wpbdc_encryption_password'))>wpbdc_max_password())
				{
					$err[]='Maximum password #1 length: '.wpbdc_max_password().', now is '.strlen(get_option('wpbdc_encryption_password'));
				}
				if(strlen(get_option('wpbdc_encryption_iv'))!=wpbdc_min_iv())
				{
					$err[]='Password #2 length should be '.wpbdc_min_iv().', now you only have '.strlen(get_option('wpbdc_encryption_iv')).' characters.';
				}
			}
		}
		
		if(isset($err) && count($err)>0)
		{
			echo '<div style="border:1px solid red; padding:6px;"><p><strong>Errors:</strong></p><ul>';
			echo '<li style="color:red;">If these requirements aren`t met, the encryption process will fail.</li>';
			foreach($err as $nr=>$text)
			{
				echo '<li>'.$text.'</li>';
			}
			echo '</ul></div>';
		} else {
			echo '<p>Seems ok, use <a href="admin.php?page=wpbdc-selftest">SelfCheck Page</a> to see if encryption works<p>';
		}
		
		echo '<p>Some warnings, first..</p>';
		echo '<div  style="color:red; padding-left:20px;">';
		echo '<ul>
				<li>If you choose encryption and forget the password there is NO WAY to get the password except by tring all combinations (wich could literally take hundreds of years)</li>
				<li>WordPressBackup can`t check encrypted data. There`s no way to check it without decrypting it and we do not receive.</li>
				<li>The <b>ENCRYPTION PASSWORD AND IV</b> below is kept on the blog only. The WordPressBackup site does not see it</li>
			</u>';
		echo '</div>';
		echo '<p>Encryption used: rijndael-256 also known as AES (<a href="http://en.wikipedia.org/wiki/Advanced_Encryption_Standard" target="_blank">read about it on wikipedia</a>)</p>';
		echo '<form method="post" action="" style="padding:3px;">';
		echo '<label for="enc_enabled">Encrypt backups?<select name="enc_enabled">';
		
		$selyes=''; $selno='';
		if(get_option('wpbdc_encryption')=='yes') { $selyes='selected'; } 
		else { $selno='selected'; }
		echo '<option '.$selno.' value="no">no</option>';
		echo '<option '.$selyes.' value="yes">yes</option>';
		
		echo '</select></label><br />';
		echo '<label for="enc_pass"><span style="font-size:10px;">Password #1:</span> <input style="display:block; width:100%;" type="text" value="'.get_option('wpbdc_encryption_password').'" id="enc_pass" name="enc_pass" /></label><br />';
		echo '<label for="enc_iv"><span style="font-size:10px;">Password #2 (use a different one from #1):</span> <input style="display:block; width:100%;" type="text" value="'.get_option('wpbdc_encryption_iv').'" id="enc_iv" name="enc_iv" /></labe><br>';
		echo '<input style="display:inline;"  type="submit" name="saveencryption" value="Save encryption settings"></form>';
	}
	echo '</div>';
}

function wpdbc_page_decrypt()
{
	require_once(str_replace('wordpressbackup.php','wpbdc_encryption.php',__FILE__));
	echo '<div class="wrap">';
	echo '<h2>Decryption for WordPressBackup<sup>.com</sup> <span style="font-size:0.5em;">(<a target="_blank" href="http://www.wordpressbackup.com/backup/">open site</a>)</span></h2>';
	
	if(wpbdc_encryption_list()==false)
	{
		echo '<p><strong>Sorry, your website host does not support php encryption (ask for "php mcrypt extension").</strong></p>';
	} else {
		echo '<p>Download the archived backup, extract the .sql file from it and uploade it using the form below. Do not upload the archive (.zip file)!</p>';
		echo '<iframe src="../wp-content/plugins/wordpressbackup/wpbdc_decryptform.php" style="height:300px; width:100%;" border:0px; padding:0px; margin:0px;"></iframe>';
	}
	echo '</div>';
}

add_action('admin_menu', 'wpbdc_menu_integration');
register_activation_hook(__FILE__,'wpbdc_install_or_update');
register_deactivation_hook(__FILE__,'wpbdc_deactivate')
?>