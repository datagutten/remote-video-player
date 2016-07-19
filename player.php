<?Php
require 'config.php';
$db=new PDO("mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",$config['db_user'],$config['db_password'],array(PDO::ATTR_PERSISTENT => true));
$mac=$_GET['mac'];

$st_device=$db->prepare('SELECT * FROM devices WHERE mac=?');
$st_insert_device=$db->prepare('INSERT INTO devices (mac,description) VALUES (?,?)');
$st_update_message=$db->prepare('UPDATE devices SET message=? WHERE id=?');

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
	trigger_error('New device '.$mac);
}
else
{
	$row=$st_device->fetch(PDO::FETCH_ASSOC);
	if(empty($row['video']))
	{
		trigger_error($msg=sprintf(_('No video assigned to device: %s (%s)'),$row['name'],$row['mac']));
		http_response_code(404);
	}
	elseif(file_exists($file=$config['videopath'].'/'.$row['video']))
	{
		$mime=mime_content_type($file);
		header('Content-type: '.$mime);
		header("Content-Length: " . filesize($file));

		$fp=fopen($file,'r');
		$msg=sprintf(_('Successfully delivered %s'),$file);
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