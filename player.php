<?Php
if(empty($_GET['mac'])) //All request must contain the device mac address
{
	http_response_code(400);
	trigger_error('Request without MAC address',E_USER_ERROR);
}
require 'config.php';
require 'pdohelper.php';
$db=new pdohelper("mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",$config['db_user'],$config['db_password'],array(PDO::ATTR_PERSISTENT => true));
$mac=$_GET['mac'];

$st_device=$db->prepare('SELECT * FROM devices WHERE mac=?');
$st_videos=$db->prepare('SELECT * FROM videos WHERE device=?');
$st_insert_device=$db->prepare('INSERT INTO devices (mac,description) VALUES (?,?)');
$st_update_message=$db->prepare('UPDATE devices SET message=? WHERE id=?');
$st_update_ip=$db->prepare('UPDATE devices SET ip=? WHERE id=?');

$st_device->execute(array($mac));
if($st_device->rowCount()==0)
{
	$st_insert_device->execute(array($mac,sprintf('First connection %s from %s',date('Y-m-d H:i'),$_SERVER['REMOTE_ADDR'])));
	/*$im=imagecreatetruecolor(500,500);
	$red=imagecolorallocate($im,255,0,0);
	$white=imagecolorallocate($im,255,255,255);
	imagefill($im,0,0,$red);
	$string=sprintf('Unkown device with mac %s inserted with id %s',$mac,$db->lastInsertId());
	imagestring($im,5,100,100,$string,$white);
	header('Content type: image/png');
	imagepng($im);*/
}
else
{
	$row=$st_device->fetch(PDO::FETCH_ASSOC); //Fetch device info

	if($st_update_ip->execute(array($_SERVER['REMOTE_ADDR'],$row['id']))===false) //Update IP for device
	{
		$errorinfo=$db->errorInfo();
		trigger_error(sprintf('Unable to update IP for %s, SQL error: %s',$mac,$errorinfo[2]));
	}

	$db->execute($st_videos,array($row['id'])); //Get videos for device
	
	if($st_videos->rowCount()==0) //No videos assigned
	{
		$msg=_('No videos assigned to device');
		http_response_code(404);
	}
	elseif(empty($_GET['video'])) //Videos assigned, but no file requested. Create playlist
	{
		while($row_video=$st_videos->fetch(PDO::FETCH_ASSOC))
		{
			echo 'http://videoserver/player.php?'.http_build_query(array('mac'=>$mac,'video'=>$row_video['file']))."\n";
		}
		$msg=sprintf(_('Delivered playlist with %s elements'),$st_videos->rowCount());
	}
	elseif(!empty($_GET['video']) && file_exists($file=$config['videopath'].'/'.basename($_GET['video']))) //Video requested
	{
		$mime=mime_content_type($file);
		header('Content-type: '.$mime);
		header('Content-Length: '.filesize($file));

		$fp=fopen($file,'r');

		$st_update_message->execute(array(date('Y-m-d H:i').': '.sprintf(_('Successfully delivered %s'),$file),$row['id']));

		if($fp===false)
			trigger_error($msg=sprintf('Error opening file %s',$file),E_USER_ERROR);
		fpassthru($fp);
		fclose($fp);
	}
	else
		http_response_code(404);
	if(isset($msg))
		$st_update_message->execute(array(date('Y-m-d H:i').': '.$msg,$row['id']));

}
function msg($msg)
{
	$st_update_message->execute(array(date('Y-m-d H:i').': '.$msg,$row['id']));
}