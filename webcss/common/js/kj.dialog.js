/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 增加 dialog 相关功能
 */

kj.dialog = function(o){
	//统一对象前缀
	o.id = 'win' + o.id;
	if(kj.dialog.objid.indexOf(o.id)>=0) {
		kj.dialog.show("#"+o.id);
		return;
	}
	//设置按钮
	if(!('showbtnclose' in o)) o.showbtnclose=true;
	if(!('showbtnmax' in o)) o.showbtnmax=true;
	if(kj.dialog.objid.indexOf(o.id) < 0) {
		switch(o.type) {
			case "iframe":
				//设置宽高值
				if(!("w" in o)) {
					o.w = kj.w()-200;
					if(o.w < 300) o.w = 300;
				}
				if(!("h" in o)) {
					o.h = document.documentElement.clientHeight;
					if(o.h < 300) o.h = 300;
				}
				if('min_w' in o && o.w<o.min_w) o.w=o.min_w;//最小宽
				if('min_h' in o && o.h<o.min_h) o.h=o.min_h;//最小高
				if('max_w' in o && o.w>o.max_w) o.w=o.max_w;//最大宽
				if('max_h' in o && o.h>o.max_h) o.h=o.max_h;//最大高
				kj.dialog.iframe(o.id,o.title,o.w,o.h,o.showbtnclose,o.showbtnhide , o.showbtnmax);
				window.open(o.url, o.id + "_iframe");
				break;
			case "html":
				if(!('html' in o)) o.html = '';
				kj.dialog.html(o.id,o.title,o.html,o.w,o.h,o.showbtnclose,o.showbtnhide , o.showbtnmax);
				break;
			default:
		}
	}
	if(!("left" in o)) o.left='';
	if(!("top" in o)) o.top='';
	kj.dialog.objs['#'+o.id] = o;
	kj.handler('#'+o.id+'','click',function(){
		kj.dialog.show(this);
	});

	kj.dialog.open('#'+o.id,o.top,o.left);

};
kj.dialog.objs = [];

//取系统样式路径
var str_cssdir = kj.cfg('basecss');
if(str_cssdir == '') str_cssdir = "./webcss";
str_cssdir += "/common/images/";
var str_position = (kj.agent(true) == 'MSIE6.0') ? 'absolute' : 'fixed';
kj.dialog.css = '<style>.divDialog{position:'+str_position+';left:0px;overflow:hidden;padding:8px;background:url('+str_cssdir+'transparent.png);text-align:left}.divDialog .x_body{float:left;background:#fff;padding:1px;overflow:hidden}.divDialog .x_title{background:#F2F2F2;padding:5px 0px 0px 0px;float:left;width:100%;font-size:14px;color:#333333;height:25px}.divDialog .x_title li{float:left;padding:0px 0px 0px 5px;list-style-type:none}.divDialog .x_val{width:100%;float:left;padding:0px}.divDialog .x_val li{float:left;margin-top:8px;margin-left:5px;list-style-type:none;list-style-type:none}.divDialog .x_val frame{float:left}.divDialog .table{width:420px;border:1px #000000 solid}.divDialog .table td{height:20px}.divDialog .table .x_title{font-weight:bold;background:#D2EAFF}.divDialog .btnrow{float:left;width:100%;margin-top:0px;text-align:center}.divDialog .pubinfo{float:left;width:480px;padding:10px}.divDialog .pubinfo li{float:left;width:100px;padding:10px}.divDialog .x_title .x_close{text-align:right;float:right;padding-right:10px}.divDialog .x_title .x_close a{text-decoration:none;background:url('+str_cssdir+'btn_close.gif) no-repeat;width:20px;height:20px;float:left}.divDialog .x_title .x_close a:hover {text-decoration:none;background:url('+str_cssdir+'btn_close.gif) no-repeat 0px -20px;width:20px;height:20px;float:left}.divDialog .x_title .x_hide{text-align:right;float:right;padding-right:10px}.divDialog .x_title .x_hide a{text-decoration:none;background:url('+str_cssdir+'btn_hide.gif) no-repeat;width:20px;height:20px;float:left}.divDialog .x_title .x_hide a:hover {text-decoration:none;background:url('+str_cssdir+'btn_hide.gif) no-repeat 0px -20px;width:20px;height:20px;float:left}.divDialog .x_title .x_max{text-align:right;float:right;padding-right:10px}.divDialog .x_title .x_max a{text-decoration:none;background:url('+str_cssdir+'btn_max.gif) no-repeat;width:20px;height:20px;float:left}.divDialog .x_title .x_max a:hover {text-decoration:none;background:url('+str_cssdir+'btn_max.gif) no-repeat 0px -20px;width:20px;height:20px;float:left}</style>';
document.write(kj.dialog.css);
kj.dialog.objid = [];
kj.dialog.index = 0;
kj.dialog.showindex = 0;
/* iframe 形式对话框
 *
 */
kj.dialog.iframe = function(id,msgtit,msgwidth,msgheight,isbtnclose,isbtnhide,isbtnmax){
	var x;
	var strWidth=msgwidth;
	var strHeight=msgheight-16;
	var str_content="";
	var strHeight3=strHeight-40;
	str_content+='<div class="x_body" id="' + id + '_body" style="width:'+strWidth+'px;height:'+strHeight+'px;">';
	str_content+='<div class="x_title" id="' + id + '_title">';
	str_content+='<li><b>'+msgtit+'</b></li>';
	if(isbtnclose) str_content+='<li class="x_close"><a href="javascript:kj.dialog.close(\'#'+id+'\');" title="关闭">&nbsp;</a></li>';
	if(isbtnmax) str_content+='<li class="x_max"><a href="javascript:kj.dialog.max(\'#'+id+'\');" title="最大化">&nbsp;</a></li>';
	if(isbtnhide) str_content+='<li class="x_hide"><a href="javascript:kj.dialog.hide(\'#'+id+'\');" title="最小化">&nbsp;</a></li>';
	str_content+='</div>';
	str_content+='<div class="x_val"><iframe name="'+id+'_iframe" id="'+id+'_iframe" src="" width="100%" height="'+strHeight3+'px" frameborder=0></iframe></div>';
	str_content+='</div>';

	var obj_div=document.createElement("div");
	obj_div.id=id;
	obj_div.className="divDialog";
	obj_div.style.cssText="display:none;width:"+(strWidth+2)+"px;height:"+strHeight+"px";
	obj_div.innerHTML=str_content;
	document.body.appendChild(obj_div);
	kj.dialog.objid.push(id);
	lng_len=kj.dialog.objid.length;
	for(var i=0;i<lng_len;i++){
		obj_win=document.getElementById(kj.dialog.objid[i]);
		kj.move(obj_win , obj_win);
	}
}
/* html 形式对话框
 *
 */
kj.dialog.html = function(id,msgtit,html,msgwidth,msgheight,isbtnclose,isbtnhide,isbtnmax) {
	var strWidth=msgwidth;
	var strHeight=msgheight;
	var str_content="";
	var style = '';
	if(strWidth) style = "width:" + strWidth + "px;";
	if(strHeight) style += "height:" + strHeight + "px;";

	str_content+='<div class="x_body" id="' + id + '_body" style="' + style + '">';
	str_content+='<div class="x_title" id="' + id + '_title">';
	str_content+='<li><b>'+msgtit+'</b></li>';
	if(isbtnclose) str_content+='<li class="x_close"><a href="javascript:kj.dialog.close(\'#'+id+'\');" title="关闭">&nbsp;</a></li>';
	if(isbtnmax) str_content+='<li class="x_max"><a href="javascript:kj.dialog.max(\'#'+id+'\');" title="最大化">&nbsp;</a></li>';
	if(isbtnhide) str_content+='<li class="x_hide"><a href="javascript:kj.dialog.hide(\'#'+id+'\');" title="最小化">&nbsp;</a></li>';
	str_content+='</div>';
	str_content+='<div class="x_val">'+html+'</div>';
	str_content+='</div>';

	var obj_div=document.createElement("div");
	obj_div.id=id;
	obj_div.className="divDialog";
	obj_div.style.cssText="display:none;" + style;
	obj_div.innerHTML=str_content;
	document.body.appendChild(obj_div);
	kj.dialog.objid.push(id);
	lng_len=kj.dialog.objid.length;
	for(var i=0;i<lng_len;i++){
		obj_win=document.getElementById(kj.dialog.objid[i]);
		kj.move('#'+kj.dialog.objid[i]+"_title" , obj_win);
	}
}
//最大化
kj.dialog.max = function(msgid) {
	var lng_w = kj.w(msgid);
	var new_w = document.documentElement.clientWidth;
	var lng_h = kj.h(msgid);
	var new_h = document.documentElement.clientHeight;
	if(lng_w>=new_w) {
		new_w = kj.dialog.objs[msgid].w;
	}
	var w = new_w - lng_w;
	kj.w(msgid , kj.w(msgid) + w );
	//kj.w(msgid+ "_alpha" , kj.w(msgid+ "_alpha") + w);
	kj.w(msgid+ "_body" , kj.w(msgid+ "_body") + w +2);
	if(lng_h>=new_h) {
		new_h = kj.dialog.objs[msgid].h;
	}
	var h = new_h - lng_h;
	var top = kj.top(msgid);
	if(kj.obj('#id_open_win')) {
		top = top*2 - (kj.h('#id_open_win'));
		if(top<0) top = 0;
		h = h - kj.h('#id_open_win');
	}
	kj.h(msgid , kj.h(msgid) + h );
	//kj.h(msgid+ "_alpha" , kj.h(msgid+ "_alpha") + h);
	//kj.h(msgid+ "_body" , kj.h(msgid+ "_body") + h);
	//kj.h(msgid+ "_iframe" , kj.h(msgid+ "_iframe") + h);
	var idocumentElement = kj.obj(msgid+ "_iframe").contentWindow;
	if(idocumentElement && 'inc_resize' in idocumentElement) idocumentElement.inc_resize();

	//var x = kj.h(msgid+ "_alpha") - 10;
	//kj.set( msgid + "_body" , 'style.top' , "-" + x + 'px' );
	kj.set( msgid , 'style.top' , top + 'px' );
	kj.set( msgid , 'style.left' , kj.left(msgid) + 'px' );
}
//隐藏
kj.dialog.hide = function(msgid) {
	if(kj.obj(msgid).style.display=='') kj.dialog.showindex--;
	kj.hide(msgid);
	if(kj.dialog.showindex < 1) {
		kj.set('select','style.visibility','');
		kj.dialog.showindex = 0;
	}
}
//显示
kj.dialog.show = function(msg_o) {
	var o = kj.obj(msg_o);
	if(kj.dialog.index == 1 && o.style.visibility=='hidden') {
		kj.set('select','style.visibility','hidden');
	}
	var ii=kj.obj(o).style.zIndex;
	var kk=0;
	if( ii!= kj.dialog.index) {
		kj.set(o,'style.zIndex',kj.dialog.index);
		for(var i=0;i<kj.dialog.objid.length;i++) {
			if(kj.dialog.objid[i] != o.id && kj.obj('#'+kj.dialog.objid[i]).style.zIndex>ii) {
				kk=kj.obj('#'+kj.dialog.objid[i]).style.zIndex-1;
				kj.set('#'+kj.dialog.objid[i],'style.zIndex',kk);
			}
		}
	}
	if(kj.obj(o).style.display=='none') kj.dialog.showindex++;
	kj.show(o);

}
//关闭
kj.dialog.close = function(msgid) {
	kj.dialog.showindex--;
	kj.dialog.index--;
	kj.remove(msgid);
	kj.dialog.objid.remove(msgid.substr(1));
	if(kj.dialog.index<1) {
		kj.set('select','style.visibility','');
	}
}
//打开
kj.dialog.open = function (msgid,msg_top,msg_left) {
	if(kj.dialog.index<0) kj.dialog.index = 0;
	kj.dialog.index++;
	obj=kj.obj(msgid);
	if(!obj) return;

	kj.dialog.show(obj);
	lng_top = kj.top(obj,msg_top);
	lng_left2= 0;
	(msg_left && !(msg_left===0) )? lng_left = msg_left : lng_left = kj.w()/2-kj.w(obj)/2;
	if(lng_left<0) lng_left = 0;
	int_height=document.body.scrollTop;
	if(!int_height)int_height=document.documentElement.scrollTop;
	if( kj.platform == "mobile" ) lng_top=lng_top+int_height;
	lng_top=lng_top+"px";
	lng_left=lng_left+"px";
	obj.style.top=lng_top;
	obj.style.left=lng_left;
}