/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
var admin = new function() {
	this.rule = Array();
	this.ajax_fun = "";
	this.default_app_act = "";
	this.frm_ajax = function(msg,func) {
		if( msg == "del" || (msg != 'delete' && msg.indexOf("del")>=0 ) ){
			if(!confirm("确定要删除吗？")) {
				return;
			}
		}
		if( msg=="delete" || msg.indexOf("delete") >= 0 ){
			if(!confirm("删除将不可恢复，确定要继续吗？")) {
				return;
			}
		}
		if(msg == '') {
			alert("没有指定操作");
			return;
		}
		if(kj.rule.form(document.frm_main) == false) {
			return false;
		}

		if(!func) func="";
		this.ajax_fun=func;
		this.default_app_act = document.frm_main.app_act.value;
		document.frm_main.app_act.value=msg;
		document.frm_main.app_ajax.value=1;

		var arr_obj = kj.obj(":dosubmit");
		if(arr_obj.length>0) {
			arr_obj[0].value = "正在" + arr_obj[0].value + "...";
			arr_obj[0].disabled = true;
		}
		kj.ajax.post(document.frm_main,function(data){
			var obj_data=kj.json(data);
			var arr_obj = kj.obj(":dosubmit");
			if(arr_obj.length>0) {
				var str = arr_obj[0].value;
				arr_obj[0].value = str.substr(2,str.length-5);
				arr_obj[0].disabled = false;
			}
			document.frm_main.app_act.value = admin.default_app_act;
			document.frm_main.app_ajax.value = '';
			if(obj_data.isnull) {
				kj.alert("操作失败" , admin.ajax_fun , obj_data);
			} else {
				if('code' in obj_data && obj_data.code=='0') {
					if("msg" in obj_data && obj_data.msg!=''){
						kj.alert.show(obj_data.msg , function(data) {
							admin._form_ajax(data);
						} , obj_data);
					} else {
						admin._form_ajax(obj_data);
					}
				} else {
					if("msg" in obj_data && obj_data.msg!='') {
						kj.alert(obj_data.msg , admin.ajax_fun , obj_data);
					} else {
						kj.alert("操作失败" , admin.ajax_fun , obj_data);
					}
				}
			}
		});
	}
	this._form_ajax = function (data) {
		//更新当前id值
		var frm = window.parent.kj.obj('#id_frame_main');
		if(frm && typeof frm  == 'object') {
			if('contentWindow' in frm) {
				if(document.frm_main.id.value=="" && data && 'id' in data) {
					if(frm.contentWindow.admin!=admin) document.frm_main.reset();
					frm.contentWindow.admin.refresh();
				} else if(frm.contentWindow.admin == admin) {
					//document.frm_main.reset();//如果主窗口等于当前窗口，则重置条件
					frm.contentWindow.admin.refresh();
				} else {
					frm.contentWindow.admin.refresh();
				}
			} else {
				document.frm_main.reset();
			}
		}
		//回调函数
		if(admin.ajax_fun != '') {
			admin.ajax_fun(data);
			if(typeof(admin) != 'undefined') admin.ajax_fun = '';
			return;
		}

	}
	this.ajax_delete = function(msgid , norefresh , func) {
		this.ajax_url({id:msgid , app_act:'delete' , refresh : !norefresh} , func);
	}

	this.ajax_url = function( o , func) {
		this.ajax_fun=func;
		var url;
		if( !('refresh' in o) ) o.refresh = true;
		this.ajax_url.refresh = o.refresh;
		if('url' in o) {
			url = o.url;
		} else {
			var app = document.frm_main.app.value;
			var app_module = document.frm_main.app_module.value;
			var app_act = '';
			var id = '';
			if('app' in o) app = o.app;
			if('app_module' in o) app_module = o.app_module;
			if('app_act' in o) app_act = o.app_act;
			if('id' in o) id = o.id;
			url="?app=" + app + "&app_module=" + app_module + "&app_act=" + app_act + "&id=" + id;
			if( app_act == "del" || (app_act != 'delete' && app_act.indexOf("del")>=0 ) ){
				if(!confirm("确定要删除吗？")) {
					return;
				}
			} else if( app_act=="delete" || app_act.indexOf("delete") >= 0 ){
				if(!confirm("删除将不可恢复，确定要继续吗？")) {
					return;
				}
			}
		}
		kj.ajax.get(url , function(data) {
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				kj.alert("操作失败");
			} else {
				if(obj_data.code=='0' ) {
					if('msg' in obj_data && obj_data.msg!='') {
						kj.alert.show(obj_data.msg , function(){
							if(admin.ajax_fun) {
								admin.ajax_fun(data);
								admin.ajax_fun = '';
							}
							if(admin.ajax_url.refresh) location.reload();});
					} else {
						if(admin.ajax_fun) {
							admin.ajax_fun(data);
							admin.ajax_fun = '';
						}
						if(admin.ajax_url.refresh) location.reload();
					}
				} else {
					if("msg" in obj_data) kj.alert(obj_data.msg);
				}
			}
		});
	}
	this.refresh = function() {
		var url = window.location.href;
		var arr = url.split('?');
		url = arr[0];
		window.location = url + "?" + this.get_url();
	}
	this.refresh_url = function(arr) {
		var url = window.location.href;
		url = kj.urlencode(url , arr);
		window.location = url;
	}
	this.open = function(obj) {
		var str_url = "?" + this.get_url(obj);
		target = "_self";
		if('target' in obj) target = obj.target;
		window.open(str_url , target);
	}
	this.get_url = function(obj) {
		var url = [];
		var i = 0;
		var app_act = document.frm_main.app_act.value;
		if(obj && 'app_act' in obj) app_act = obj.app_act;
		if(obj && 'url' in obj) url[url.length] = obj.url;
		//默认url参数
		url[url.length]='app=' + document.frm_main.app.value;
		url[url.length]='app_module=' + document.frm_main.app_module.value;
		url[url.length]='app_act=' + app_act;
		url[url.length]='page=' + document.frm_main.page.value;
		//查找相关参数
		var obj_s = kj.obj("input<<name,/^s_/i");
		for(i = 0 ; i < obj_s.length ; i++){
			if(obj_s[i].type == "radio" || obj_s[i].type == "checkbox" ) {
				if( obj_s[i].checked ) url[url.length] = obj_s[i].name + "=" + obj_s[i].value;
			} else {
				if(obj_s[i].value != '') url[url.length] = obj_s[i].name + "=" + obj_s[i].value;
			}
		}
		obj_s = kj.obj("select<<name,/^s_/i");
		for(i = 0 ; i < obj_s.length ; i++){
			if(obj_s[i].value != '') url[url.length] = obj_s[i].name + "=" + obj_s[i].value;
		}
		//自定义相关参数
		obj_s = kj.obj("input<<name,/^url_/i");
		for(i = 0 ; i < obj_s.length ; i++){
			if(obj_s[i].type == "radio" || obj_s[i].type == "checkbox" ) {
				if( obj_s[i].checked ) url[url.length] = obj_s[i].name + "=" + obj_s[i].value;
			} else {
				if(obj_s[i].value != '') url[url.length] = obj_s[i].name + "=" + obj_s[i].value;
			}
		}
		var href = window.location.href;
		var arr_href = href.split('?');
		url = kj.urlencode(arr_href[0] , url);
		arr_href =url.split('?');
		return arr_href[1];
	}
	this.page = function(page) {
		document.frm_main.page.value = page;
		this.refresh();
	}
	//查询
	this.search = function() {
		this.refresh();
	}
	//清空查询
	this.clear_search = function() {
		kj.set("input<<name,/^s_/i",'value','');
		kj.set("select<<name,/^s_/i",'options[0].selected',true);
		document.frm_main.page.value='';
		this.refresh();
	}
	this.menu_display = function(index) {
		var arr = kj.obj('.btnMenuDiv');
		var arr2 = kj.obj('.btnMenu');
		if(arr[index].style.display == 'none') {
			kj.set(arr , 'style.display' , 'none');
			kj.delClassName('.btnMenu' , 'btnMenuSel');
			kj.addClassName(arr2[index] , 'btnMenuSel');
			arr[index].style.display = '';
		} else {
			kj.delClassName(arr2[index] , 'btnMenuSel');
			arr[index].style.display = 'none';
		}
		inc_resize();
	}
	this.edittabel = function(id) {
		kj.hide('.pEditTable<<index,/^[^'+id+']/i');
		kj.set('#id_pMenu li','className','');
		kj.show('.pEditTable<<index,'+id);
		kj.addClassName('#id_pMenu li<<index,'+id,'sel');
	}
	this.selact = function(func) {
		var act = document.frm_main.selact.value;
		admin.frm_ajax(act , func);
	}
	this.act = function(act) {
		document.frm_main.app_act.value=act;
		admin.clear_search();
	}
	this.move = function(divObj) {
			if (!divObj) return;
			divObj.hasDraged = false;
			//this.divObj = divObj;
			// 把鼠标的形状改成移动形
			divObj.style.cursor = "move";
			divObj.pdiv =  kj.parent(divObj,"div");
			// 定义鼠标按下时的操作
			divObj.onmousedown = function(event) {
				if(window.event) event=window.event;
				var ofs = Offset(divObj);
				divObj.X = event.clientX;
				divObj.Y = event.clientY - ofs.t;
				divObj.tdW = divObj.pdiv.offsetWidth;
				divObj.hasDraged = true;
			};

			// 定义鼠标移动时的操作
			divObj.onmousemove = function(event)
			{
				if (!divObj.hasDraged) return;
				if(window.event){
					event=window.event;
					divObj.setCapture();
				}
				var lng_x=event.clientX - divObj.X;
				divObj.pdiv.style.width = divObj.tdW+lng_x+"px";
				//divObj.td.style.width = divObj.pdiv.offsetWidth+"px";
				//admin.autosize2();
				kj.obj("#s_key").value=divObj.tdW;
				kj.obj("#s_regtime1").value=kj.toint(kj.w(divObj.pdiv));
				kj.obj("#s_regtime2").value=kj.toint(lng_x);
			};
			// 定义鼠标提起时的操作
			divObj.onmouseup = function()
			{
				divObj.hasDraged = false;
				if(window.event){
					divObj.releaseCapture();
				}

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
			function init() {
				var table = kj.obj("#id_table_title");
				var cells = table.rows[0].cells;
				var td = kj.parent(divObj.pdiv ,"td");
				var index = kj.index(cells,td);
				var obj_table2 = kj.obj('#id_table');
				var cells2 = obj_table2.rows[0].cells;
				var o = kj.obj(".pMoveTD",cells2[index]);
				divObj.td = o[0];
			}
			init();
	}
	this.autosize = function() {
		//表格标题宽度调整
		var obj_table = kj.obj('#id_table');
		if(obj_table && obj_table.rows.length>0) {;
			var cells = obj_table.rows[0].cells;
			var table2 = kj.obj("#id_table_title");
			var cells2 = table2.rows[0].cells;
			var obj;
			for(var i=0;i<cells.length;i++) {
				obj = kj.obj(".pMoveTitTD" , cells2[i]);
				if(obj && obj.length>0) {
						obj[0].style.width = cells[i].offsetWidth-9+"px";
				} else {
					cells2[i].style.width = cells[i].offsetWidth-10+"px";
				}
				//alert(i);
			}
			cells2[cells2.length-1].style.borderRight="0px";
			obj_table.style.width = table2.offsetWidth + "px";
		}
	}
	this.autosize2 = function() {
		//表格标题宽度调整
		var obj_table = kj.obj('#id_table');
		if(obj_table && obj_table.rows.length>0) {;
			var cells = obj_table.rows[0].cells;
			var table2 = kj.obj("#id_table_title");
			var cells2 = table2.rows[0].cells;
			var obj;
			for(var i=0;i<cells.length;i++) {
				obj = kj.obj(".pMoveTitTD" , cells2[i]);
				obj2 = kj.obj(".pMoveTD" , cells[i]);
				if(obj && obj.length>0) {
						obj2[0].style.width = obj[0].offsetWidth+9+"px";
				} else {
					cells[i].style.width = cells2[i].offsetWidth+10+"px";
				}
				//alert(i);
			}
		}

	}

}