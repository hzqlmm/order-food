/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 ** kj 下 alert 对象
 *  需要引用 common/images/alert.css
 *
 */
//增加 ajax 相关功能
kj.alert = function( msg , fun , data) {
	alert(msg);
	if( typeof(fun) == 'function' ) {
		(data)? fun(data) : fun();
	}
}
kj.alert.show = function( msg , fun , data) {
	if(kj.agent(true) == 'MSIE6.0') {
		kj.alert(msg,fun,data);
		return;
	}
	kj.alert.down(msg , fun , data );
}
kj.alert.top_num = 50;// 上下移动次数，即像素值
kj.alert.down = function( msg , fun , data) {
	var obj_div = document.createElement("div");
	var strx = "alert_"+Math.random();
	obj_div.id = strx.replace(".","");
	obj_div.className = "alert_down";
	obj_div.innerHTML = msg;
	obj_div.style.zIndex = 999;
	document.body.appendChild(obj_div);
	kj.set("#"+obj_div.id,"style.top",kj.top("#"+obj_div.id)+"px");
	kj.set("#"+obj_div.id,"style.left",kj.left("#"+obj_div.id)+"px");
	//向上弹出效果
	kj.alert.moveTop( '#'+obj_div.id , -1 , kj.alert.top_num );
	//设置固定时间后，自动消失
	//if(!data) data='""';
	kj.timeout(kj.alert.remove , 1500 , '#'+obj_div.id , fun , data);
}
kj.alert.remove = function(o , fun , data) {
	kj.alert.moveTop( o , 2 , kj.alert.top_num , function(o){ kj.remove(o); } , o );
	if( typeof(fun) == 'function' ) kj.call(fun , data);
}
/**向上移动 , 渐变
 * size 移劝大小像素，num 移动次数 , fun 完成后触发事件
 */
kj.alert.moveTop = function(o , size , num , fun , data) {
	o = kj.obj(o);
	var top = kj.toint(kj.get(o , 'style.top')) + size;
	kj.set(o , "style.top" , top+"px");
	num--;
	var opacity = (100 / kj.alert.top_num) * kj.alert.top_num-num;
	if(size>0) opacity = 100 - opacity;
	if ( num>0 ) {
		kj.opacity ( o , opacity);
		kj.timeout(kj.alert.moveTop , 1 , "#" + o.id , size , num , fun , data);
	} else {
		kj.opacity ( o , 100);
		if( typeof(fun) == 'function' ) kj.call(fun , data);
	}
}