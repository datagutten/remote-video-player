<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Videos</title>
<script type="text/javascript" src="add_row.js"></script>

</head>

<?Php
require 'common.php';
require 'tools/DOMDocument_createElement_simple.php';
$dom=new DOMDocumentCustom;
$dom->formatOutput=true;
$body=$dom->createElement_simple('body');
$videopath=$config['videopath'];
if(!file_exists($videopath))
	trigger_error(sprintf('Video path %s not found',$videopath),E_USER_ERROR);
$videos=scandir($videopath);
$videos=array_diff($videos,array('.','..'));


if(!isset($_GET['device']) || !is_numeric($_GET['device']))
{
	$st_devices=$db->query('SELECT * FROM devices',false);
	$ul=$dom->createElement_simple('ul',$body);
	while($row=$st_devices->fetch(PDO::FETCH_ASSOC))
	{
		$li=$dom->createElement_simple('li',$ul);
		$dom->createElement_simple('a',$li,array('href'=>'?device='.$row['id']),trim(sprintf('%s %s %s',$row['name'],$row['mac'],$row['description'])));
	}
}
else
{
	if(!empty($_POST))
	{
		$st_update=$db->prepare('UPDATE videos SET file=?, position=? WHERE id=?');
		$st_insert=$db->prepare('INSERT INTO videos (file, position,device) VALUES (?,?,?)');
		$st_delete=$db->prepare('DELETE FROM videos WHERE id=?');

		print_r($_POST);
		foreach($_POST['videos'] as $id=>$video)
		{
			if(is_numeric($id))
			{
				if(empty($video['file']))
					$db->execute($st_update,array($video['file'],$video['position'],$id),false);
				else
					$db->execute($st_delete,array($id),false);
			}
			elseif(!empty($video['file']))
				$db->execute($st_insert,array($video['file'],$video['position'],$_GET['device']),false);
		}
	}
	
	$st_videos_device=$db->prepare('SELECT * FROM videos WHERE device=? ORDER BY position');
	$db->execute($st_videos_device,array($_GET['device']),false);
	$form=$dom->createElement_simple('form',$body,array('method'=>'post'));

	$table=$dom->createElement_simple('table',$form,array('border'=>'1','id'=>'table'));
	$tr=$dom->createElement_simple('tr',$table);
	$th=$dom->createElement_simple('th',$tr,false,_('File'));
	$th=$dom->createElement_simple('th',$tr,false,_('Order'));
	while($row=$st_videos_device->fetch(PDO::FETCH_ASSOC))
	{
		$tr=$dom->createElement_simple('tr',$table);
		$td_video=$dom->createElement_simple('td',$tr);
		$videoselect=$dom->createElement_simple('select',$td_video,array('name'=>sprintf('videos[%s][file]',$row['id'])));
		$option=$dom->createElement_simple('option',$videoselect,array('value'=>''),_('Select video...'));
		foreach($videos as $video)
		{
			$option=$dom->createElement_simple('option',$videoselect,array('value'=>$video),$video);
			if($video==$row['file'])
				$option->setAttribute('selected','selected');
		}
		$td_order=$dom->createElement_simple('td',$tr);
		$input_order=$dom->createElement_simple('input',$td_order,array('type'=>'text','name'=>'videos[extra_1][order]','size'=>2,'value'=>$row['position']));

	}
	$tr=$dom->createElement_simple('tr',$table,array('id'=>'extra_row_1')); //Create first extra row
	$td_video=$dom->createElement_simple('td',$tr);
	$videoselect=$dom->createElement_simple('select',$td_video,array('id'=>'file_extra_1','name'=>'videos[extra_1][file]','onchange'=>'add_row()'));
	$option=$dom->createElement_simple('option',$videoselect,array('value'=>''),_('Select video...'));
	foreach($videos as $video)
	{
		$option=$dom->createElement_simple('option',$videoselect,array('value'=>$video),$video);
	}
	$td_order=$dom->createElement_simple('td',$tr);
	$input_order=$dom->createElement_simple('input',$td_order,array('type'=>'text','id'=>'position_extra_1','name'=>'videos[extra_1][position]','size'=>2));

	$dom->createElement_simple('input',$form,array('type'=>'hidden','name'=>'lastrow','id'=>'last_row','value'=>'1'));
	$dom->createElement_simple('input',$form,array('type'=>'submit','name'=>'submit','value'=>_('Save changes')));
}
	

echo $dom->saveXML($body);
?>

</html>