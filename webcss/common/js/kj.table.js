/*
 *
 *
 * 2013-03-24
 */
kj.table = new function(){
	this.row_insert = function(table , perms1 , defaultval , index ,cols){
		var i = 0 , x = "" , str;
		table = kj.obj(table);
		if(!table) return false;
		if(!index) index = table.rows.length;
		if(!cols) {
			i = parseInt(table.rows.length)-1;
			if(i == index) i = 0;
			cols = table.rows[i].cells.length;
		}
		var objRow = table.insertRow(index);
		var strid = "id_row_"+Math.random();
		strid = strid.replace(".","");
		objRow.id = strid;
		for( i = 0 ; i < cols ; i++) {
			x = objRow.insertCell(i);
			strid="id_col_" + cols + "_" + Math.random();
			strid = strid.replace(".","");
			x.id = strid;

			objperms = defaultval;
			if(perms1 && perms1.length>i) {
				objperms = perms1[i];
			}
			//是否有属性
			if( objperms && 'attribute' in objperms) {
				for(attribute in objperms.attribute) {
					if(typeof(objperms.attribute[attribute]) == 'string') {
						str = objperms.attribute[attribute].replace(/\'/g , "\\\'");
					} else {
						str = objperms.attribute[attribute]
					}
					kj.set(x , attribute , str);
				}
			}
			//是否有事件
			if( objperms && 'handler' in objperms) {
				for(val in objperms.handler) {
					kj.handler(x , val , objperms.handler[val]);
				}
			}
		}
		return objRow;
	}
	this.row_del = function(table , row){
		var table = kj.obj(table);
		if(!table) return;
		var index = -1;
		if(typeof(row) == 'number') {
			index = row;
		} else {
			var row = kj.obj(row);
			if(row) index = row.rowIndex;
		}
		if(index || index == 0) table.deleteRow(index);
	}
	/* 插入列
	 * table : 表对象 , index 为新列位置 ， perms1 为对象组成的数组，数组索引对应行 ，defaultval 为默认列属性，当perms1没有指定时有效
	 */
	this.col_insert = function(table,index,perms1,defaultval){
		table = kj.obj(table);
		if(!index && index!=0) index = table.rows[0].cells.length;
		var str_x="" , str;
		var x,j,objperms,strid;
		for(var i = 0 ;i < table.rows.length ; i++) {
			x = table.rows[i].insertCell(index);
			strid="id_col_" + i + "_" + Math.random();
			strid = strid.replace(".","");
			x.id = strid;
			objperms = defaultval;
			if(perms1 && perms1.length>i) {
				objperms = perms1[i];
			}
			//是否有属性
			if( objperms && 'attribute' in objperms) {
				for(attribute in objperms.attribute) {
					str = objperms.attribute[attribute].replace(/\'/g , "\\\'");
					kj.set(x , attribute , str);
				}
			}
			//是否有事件
			if( objperms && 'handler' in objperms) {
				for(val in objperms.handler) {
					kj.handler(x , val , objperms.handler[val]);
				}
			}
		}
	}
	this.col_del = function(table,col){
		col = kj.obj(col);
		table = kj.obj(table);
		var len = table.rows.length; 
		var index = col.cellIndex;
		for(var i = 0;i < len;i++){
			table.rows[i].deleteCell(index);
		}
	}
}