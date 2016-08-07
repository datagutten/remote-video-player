<?Php
require 'common.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?Php echo _('Devices'); ?></title>
</head>

<?Php
require 'tools/DOMDocument_createElement_simple.php';
$dom=new DOMDocumentCustom;
$dom->formatOutput=true;
$body=$dom->createElement_simple('body');

if(isset($_POST['submit']))
{
	$st_update=$db->prepare('UPDATE devices SET name=?,description=? WHERE id=?');
	foreach($_POST['id'] as $id)
	{
		if(!empty($_POST['name'][$id]) || !empty($_POST['description'][$id]))
			$db->execute($st_update,array($_POST['name'][$id],$_POST['description'][$id],$id),false);
	}
}

//var_dump($db);
$st_device=$db->query('SELECT * FROM devices',false);
//$videos_devices=$db->query('SELECT device,file FROM videos','key_pair');

//$datalist=$dom->createElement_simple('datalist',$body,array('id'=>'videos'));


if($st_device->rowCount()==0)
	echo _('No devices are registered');
else
{
	$form=$dom->createElement_simple('form',$body,array('method'=>'post'));
	$table=$dom->createElement_simple('table',$form,array('border'=>'1'));
	$fields=array('mac'=>_('MAC address'),'name'=>_('Device name'),'description'=>_('Device description'));
	$st_count_videos=$db->prepare('SELECT count(id) FROM videos WHERE device=?');

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
		$dom->createElement_simple('a',$td_video,array('href'=>'videolist.php?device='.$row['id']),_('Select videos'));


		$td_message=$dom->createElement_simple('td',$tr,false,$row['message']);
		
		
		$dom->createElement_simple('input',$form,array('type'=>'hidden','name'=>sprintf('id[%s]',$row['id']),'value'=>$row['id']));
	}
	$dom->createElement_simple('input',$form,array('type'=>'submit','name'=>'submit','value'=>_('Save changes')));
	echo $dom->saveXML($body);
}
?>

</html>