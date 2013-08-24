function admin_row_sel(ischeck,id){
	obj=document.getElementById("id_row_"+id);
	if(obj){
		if(ischeck){
			obj.className="pRowSel";
		}else{
			obj.className="pTabRow";
		}
	}
}
function admin_row_selall(form,selall,selname)
{
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name.match(selname)) {
			e.checked = form.elements[selall].checked;
			admin_row_sel(e.checked,e.value)
		}
	}
}
function admin_row_mouseover(id){
	obj=document.getElementById("id_row_"+id);
	if(obj){
		if(obj.className=="pTabRow"){
			obj.className="pRowMove";
		}
	}
}
function admin_row_mouseout(id){
	obj=document.getElementById("id_row_"+id);
	if(obj){
		if(obj.className=="pRowMove"){
			obj.className="pTabRow";
		}
	}
}