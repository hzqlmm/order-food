/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
admin.table = new function() {
	this.row_insert = function(){
		var arr = kj.obj("#id_table .pTabRow");
		if(!arr) return;
		if( !('length' in arr) || arr.length < 1 ) return;

		var obj_div = document.createElement("div");
		var str_id = "0" + Math.random();
		str_id = str_id.replace(/\./g, "");
		obj_div.id = str_id;
		obj_div.className = arr[0].className;
		obj_div.innerHTML = arr[0].innerHTML.replace(/THISID/g,obj_div.id);
		kj.obj("#id_table").appendChild(obj_div);
		this.main_scroll();
	}
	this.main_scroll = function() {
		if( kj.h("#id_table") < kj.h("#id_table_list") ) {
			kj.set("#id_table_list","style.overflowY","hidden");
		} else {
			kj.set("#id_table_list","style.overflowY","scroll");
			kj.obj("#id_table_list").scrollTop = kj.h("#id_table");
		}

	}

}
admin.table.list1 = new function() {
	this.moveobj = '';
	this.w = 0;
	this.field = '';
	this.fieldsindex = -1;
	//初始化对象
	this.init = function(o1 , o2) {
		this.objTit = kj.obj(o1);
		this.objList = kj.obj(o2);
		this.autosize();
		arr = kj.obj("#id_table_title .x_split");
		for(i=0;i<arr.length;i++){
			this.move(arr[i] , i);
		}
		//鼠标移动表格效果
		kj.handler("#id_table .pTabRow","mouseover",function(){
			if(this.className.indexOf('pRowSel')<0) kj.addClassName(this,'pRowMove');
		});
		kj.handler("#id_table .pTabRow","mouseout",function(){
			kj.delClassName(this,'pRowMove');
		});
	}
	//自动对齐列表与标题
	this.autosize = function() {
		var tit_li = kj.obj("li" , this.objTit);
		var list_row = kj.obj(".pTabRow" , this.objList);
		var j,arr,lng_x,lng_w,o;
		lng_w=0;
		for(var i = 0 ; i < list_row.length ; i++) {
			arr = kj.obj("li" , list_row[i]);
			for(j = 0 ; j < tit_li.length ; j++ ) {
				lng_x = tit_li[j].offsetWidth -5;
				arr[j].style.width = lng_x +"px";
				kj.w(kj.obj(".autosize" , arr[j]) , lng_x-20);
				if(i==0) lng_w+=lng_x;
			}
		}
		this.w = lng_w;
		this.isscroll();
	}
	this.isscroll = function() {
		var lng_w = kj.toint(kj.w("#id_main"));
		if( (this.w+50) > lng_w) {
			kj.w("#id_table_box",this.w+200+"px");
			kj.set("#id_main","style.overflowX","scroll");
			kj.w("#id_table_list" , this.w+200+"px");
		} else {
			kj.set("#id_main","style.overflowX","hidden");
			kj.w("#id_table_list" , (lng_w-10)+"px");
			kj.obj("#id_main").scrollLeft = "0px";
		}
		//保存当前配置到数据库
		if(this.fieldsindex>=0 && 'save_resize' in this) {
			this.save_resize();
		}
	}
	//定义拖动功能
	this.move = function(divObj , fieldsindex) {
			if (!divObj) return;
			divObj.hasDraged = false;
			// 把鼠标的形状改成移动形
			divObj.style.cursor = "w-resize";
			divObj.coltitli =  kj.parent(divObj,"li");
			var arr =  kj.obj(".x_tit" , divObj.coltitli);
			divObj.coltit = arr[0];
			var obj_div = kj.obj("#id_table_title li");
			divObj.colindex = kj.index(obj_div ,divObj.coltitli);
			divObj.fieldsindex = fieldsindex;
			//firefox下
			if(kj.agent() != 'ie')  {
				kj.handler( document.body , "mouseup" ,function(){
					if(admin.table.list1.moveobj == '') return;
					document.removeEventListener("mousemove",admin.table.list1.moveobj.onmousemove,true);
					admin.table.list1.moveobj.hasDraged = false;
					admin.table.list1.moveobj = "";
				});
			}
			// 定义鼠标按下时的操作
			divObj.onmousedown = function(event) {
				if(kj.agent() == 'ie') event=window.event;
				var ofs = Offset(divObj);
				divObj.X = event.clientX;
				divObj.Y = event.clientY - ofs.t;
				divObj.tdW = divObj.coltit.offsetWidth;
				divObj.tableW = admin.table.list1.w;
				divObj.hasDraged = true;
				admin.table.list1.moveobj = divObj;
				admin.table.list1.fieldsindex = divObj.fieldsindex;
				admin.table.list1.field = divObj.coltitli;
			};
			// 定义鼠标移动时的操作
			divObj.onmousemove = function(event)
			{
				if (!divObj.hasDraged) return;
				if(kj.agent() == 'ie') {
					event=window.event;
					divObj.setCapture();
				} else {
					document.addEventListener("mousemove",divObj.onmousemove,true);
				}
				var lng_x=divObj.tdW + event.clientX - divObj.X;
				if(lng_x <= 0) lng_x = 0;
				admin.table.list1.w = divObj.tableW - divObj.tdW + lng_x;
				divObj.coltit.style.width = lng_x+"px";
				divObj.aligncol(lng_x+10);

			};
			// 定义鼠标提起时的操作
			divObj.onmouseup = function()
			{
				divObj.hasDraged = false;
				if(kj.agent() == 'ie') {
					divObj.releaseCapture();
				} else {
					//document.removeEventListener("mousemove",divObj.onmousemove,true);
				}
				admin.table.list1.isscroll();
			};
			function Offset(e)
			{
				var t = e.offsetTop;
				var l = e.offsetLeft;
				var w = e.offsetWidth;
				var h = e.offsetHeight;
				while(e=e.offsetParent)
				{
					t+=e.offsetTop;
					l+=e.offsetLeft;
				}
				return { t:t, l:l, w:w, h:h }
			};
			divObj.aligncol = function(w) {
				var arr = kj.obj("#id_table .pTabRow");
				var arrli;
				for(var i = 0; i < arr.length; i++ ) {
					arrli = kj.obj("li" , arr[i]);
					arrli[divObj.colindex].style.width = w + "px";
					kj.w(kj.obj(".autosize" , arrli[divObj.colindex]) , w-20);

				}
			}

	}
}