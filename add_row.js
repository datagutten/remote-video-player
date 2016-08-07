function add_row()
{
	var row=document.getElementById('extra_row_1');
	var new_row=row.cloneNode(true);
	new_row.removeAttribute('id');
	var childs=new_row.childNodes;
	/*for(var i=0; i<childs.length; i++)
	{
		var node=childs.item(i);
		console.log(node);
		//if(node.hasAttribute('name'))
		{
			console.log(node.tagName);
		}
	}*/
	
	var row_id=document.getElementById('last_row').value;
	//var file=new_row.getElementById('file_extra_'+row_id);
	var file=new_row.getElementsByTagName('select')[0];
	//var position=new_row.getElementById('position_extra_'+row_id);
	var position=new_row.getElementsByTagName('input')[0];
	
	var new_row_id=row_id;

	new_row_id++;
	document.getElementById('last_row').value=new_row_id; //Write new id
	
	file.removeAttribute('id');
	position.removeAttribute('id');
	
	file.setAttribute('name',file.getAttribute('name').replace('1',new_row_id));
	position.setAttribute('name',position.getAttribute('name').replace('1',new_row_id));
	console.log(file.getAttribute('name').replace(row_id,new_row_id));
	
	
	console.log(new_row_id);
	//input.setAttribute('name',
	
	var table=document.getElementById('table');
	table.appendChild(new_row);

}