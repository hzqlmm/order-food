/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
kj.draw = new function() {
	/* 绘制线条
	 * o 为对象；x1:起始x坐标, x2:结束x坐标，y1:起始y坐标，y2:结束y坐标，w:粗细，color:颜色，canvas:绘画环境，指div 对象或默认body
	 * bordertype : 当为模线或坚线时用到 solid 实线 dotted 虚线
	 */
	this.line = function(o) {
		if(!('w' in o)) o.w = 1;
		if(!('color' in o)) o.color = "#000000";
		if(!('x1' in o && 'x2' in o && 'y1' in o && 'y2' in o)) return;//未指定必要参数，跳出
		if('canvas' in o) {
			var offset = kj.offset(o.canvas);
			o.y1 += offset.top;
			o.y2 += offset.top;
			o.x1 += offset.left;
			o.x2 += offset.left;
		}
		var xDirection = (o.x2-o.x1)/Math.abs(o.x2-o.x1);
		var yDirection = (o.y2-o.y1)/Math.abs(o.y2-o.y1);
		var xDistance = o.x2-o.x1;
		var yDistance = o.y2-o.y1;
		var xPercentage = 1/Math.abs(o.x2-o.x1);
		var yPercentage = 1/Math.abs(o.y2-o.y1);
		var i,obj_point,len,bordertype;
		('bordertype' in o) ? bordertype = o.bordertype : bordertype = "solid";
		//坚线
		if( o.x1 == o.x2 ) {
			obj_point=document.createElement("div");
			len = o.y1 - o.y2;
			obj_point.style.cssText = "position:absolute;fontSize:0px;width:0px;height:" + Math.abs(len) + "px;border-left:" + o.w + "px " + o.color + " " + bordertype;
			obj_point.style.left = o.x1 + "px";
			obj_point.style.top = (o.y1<o.y2)? o.y1+"px" : o.y2+"px";
			document.body.appendChild(obj_point);
			return;
		} else if( o.y1 == o.y2 ) {
			//直线
			len = o.x1 - o.x2;
			obj_point=document.createElement("div");
			obj_point.style.cssText = "position:absolute;fontSize:0px;width:" + Math.abs(len) + "px;height:0px;border-bottom:" + o.w + "px " + o.color + " " + bordertype;
			obj_point.style.top = o.y2+"px";
			obj_point.style.left = (o.x1<o.x2)? o.x1+"px" : o.x2+"px";
			document.body.appendChild(obj_point);
			return;
		}
		if(Math.abs(o.x1-o.x2)>=Math.abs(o.y1-o.y2)) {
			var _xnum=Math.abs(xDistance)
			for(i=0;i<=_xnum;i++) {
				obj_point=document.createElement("div");
				obj_point.style.cssText = "background-color:" + o.color + ";position:absolute;fontSize:0px;width:" + o.w + "px;height:" + o.w + "px";
				o.x1+=xDirection;
				obj_point.style.left=o.x1+"px";
				o.y1=o.y1+yDistance*xPercentage;
				obj_point.style.top=o.y1+"px";
				document.body.appendChild(obj_point);
			}
		} else {
			var _ynum=Math.abs(yDistance)
			for(i=0;i<=_ynum;i++) {
				obj_point=document.createElement("div");
				obj_point.style.cssText = "background-color:" + o.color + ";position:absolute;fontSize:0px;width:" + o.w + "px;height:" + o.w + "px";
				o.y1+=yDirection;
				obj_point.style.top=o.y1+"px";
				o.x1=o.x1+xDistance*yPercentage;
				obj_point.style.left=o.x1+"px";
				document.body.appendChild(obj_point);
			}
		}

	}
	/* 绘制矩形
	 * o 为对象；x:起始x坐标, y:起始y坐标，w:宽，h:高，css:样式，canvas:绘画环境，指div 对象或默认body
	 * html :内容
	 */
	this.rect = function(o) {
		if(!('w' in o)) o.w = 1;
		if('htmlW' in o && o.htmlW>o.w) o.w = o.htmlW;
		if(!('x' in o && 'y' in o)) return;//未指定必要参数，跳出
		if('canvas' in o && o.canvas) {
			var offset = kj.offset(o.canvas);
			o.y += offset.top;
			o.x += offset.left;
		}
		var css = '';
		if('css' in o) css = o.css;
		if('h' in o) css += "height:" + o.h + "px";
		//直线
		len = o.x1 - o.x2;
		obj_point=document.createElement("div");
		obj_point.style.cssText = "position:absolute;width:" + o.w + "px;" + css;
		obj_point.style.top = o.y+"px";
		obj_point.style.left = o.x+"px";
		css = '';
		if('h' in o) css += "height:" + o.h + "px;";
		if('htmlW' in o) css += 'width:'+o.htmlW+'px;';
		if('html' in o) obj_point.innerHTML = '<div style="float:left;overflow:hidden;word-break:break-all;'+css+'">' + o.html + '</div>';
		if('id' in o) obj_point.id = o.id;
		document.body.appendChild(obj_point);
		return;
	}
}