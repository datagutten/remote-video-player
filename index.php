<?Php
if(isset($config['locale']))
{
	$locale=$config['locale'];
	$locale_path=dirname(__FILE__).'/locale';
	if(!file_exists($file=$locale_path."/$locale/LC_MESSAGES/$domain.mo"))
	{
		$this->error(sprintf(_("No translation found for locale %s. It should be placed in %s"),$locale,$file));
		return false;
	}
	putenv('LC_MESSAGES='.$locale);
	setlocale(LC_MESSAGES,$locale);
	// Specify location of translation tables
	bindtextdomain($domain,$this->locale_path);
	// Choose domain
	textdomain($domain);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?Php echo _('Devices'); ?></title>
</head>

<?Php
require 'config.php';
require 'pdohelper.php';
$db=new pdohelper("mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",$config['db_user'],$config['db_password'],array(PDO::ATTR_PERSISTENT => true));
require 'tools/DOMDocument_createElement_simple.php';
$dom=new DOMDocumentCustom;
$dom->formatOutput=true;
$body=$dom->createElement_simple('body');

if(isset($_POST['submit']))
{
	$st_update=$db->prepare('UPDATE devices SET name=?,description=?,video=? WHERE id=?');
	foreach($_POST['id'] as $id)
	{
		$db->execute($st_update,array($_POST['name'][$id],$_POST['description'][$id],$_POST['video'][$id],$id),false);
	}
}

//var_dump($db);
$st_device=$db->query('SELECT * FROM devices',false);
//$videos_devices=$db->query('SELECT device,file FROM videos','key_pair');
$videopath=$config['videopath'];
if(!file_exists($videopath))
	trigger_error('Video path not found',E_USER_ERROR);
$videos=scandir($videopath);
$videos=array_diff($videos,array('.','..'));

//$datalist=$dom->createElement_simple('datalist',$body,array('id'=>'videos'));


if($st_device->rowCount()==0)
	echo _('No devices are registered');
else
{
	$form=$dom->createElement_simple('form',$body,array('method'=>'post'));
	$table=$dom->createElement_simple('table',$form,array('border'=>'1'));
	$fields=array('mac'=>_('MAC address'),'name'=>_('Device name'),'description'=>_('Device description'));

	$tr_header=$dom->createElement_simple('tr',$table);	
	$dom->createElement_simple('th',$tr_header,false,_('MAC address'));
	$dom->createElement_simple('th',$tr_header,false,_('Device name'));
	$dom->createElement_simple('th',$tr_header,false,_('Device description'));
	$dom->createElement_simple('th',$tr_header,false,_('Video file'));
	$dom->createElement_simple('th',$tr_header,false,_('Last message'));


	while($row=$st_device->fetch(PDO::FETCH_ASSOC))
	{
		$tr=$dom->createElement_simple('tr',$table);
	
		$td_mac=$dom->createElement_simple('td',$tr,false,$row['mac']);
		
		$td_name=$dom->createElement_simple('td',$tr);
		$input=$dom->createElement_simple('input',$td_name,array('type'=>'text','name'=>sprintf('name[%s]',$row['id']),'value'=>$row['name']));

		$td_description=$dom->createElement_simple('td',$tr);
		$input=$dom->createElement_simple('input',$td_description,array('type'=>'text','name'=>sprintf('description[%s]',$row['id']),'size'=>'50','value'=>$row['description']));
		
		$td_video=$dom->createElement_simple('td',$tr);
		//$input=$dom->createElement_simple('input',$td_video,array('type'=>'text','name'=>sprintf('video[%s]',$row['id']),'size'=>'100','list'=>'videos','value'=>$row['video']));
		$videoselect=$dom->createElement_simple('select',$td_video,array('name'=>sprintf('video[%s]',$row['id'])));
		foreach($videos as $video)
		{
			$option=$dom->createElement_simple('option',$videoselect,array('value'=>$video),$video);
			if($video==$row['video'])
				$option->setAttribute('selected','selected');
		}


		$td_message=$dom->createElement_simple('td',$tr,false,$row['message']);
		
		
		$dom->createElement_simple('input',$form,array('type'=>'hidden','name'=>sprintf('id[%s]',$row['id']),'value'=>$row['id']));
	}
	$dom->createElement_simple('input',$form,array('type'=>'submit','name'=>'submit','value'=>_('Save changes')));
	echo $dom->saveXML($body);
}
?>

</html>