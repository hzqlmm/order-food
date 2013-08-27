/*
 *
 *
 * 2013-03-24
 */
kj.progress = new function() {
}
kj.progress.cssdir = kj.cfg('basecss');
if(kj.progress.cssdir == '') kj.progress.cssdir = "/webcss";
kj.progress.css = '<style>.progress1{float:left;width:auto}.progress1 .x_mid{float:left;height:13px;overflow:hidden;background:url('+kj.progress.cssdir+'/common/images/scroll_1.gif) 0px -39px repeat-x}.progress1 .x_midbg{float:left;background:url('+kj.progress.cssdir+'/common/images/scroll_1.gif) 0px -26px repeat-x;width:0px;height:13px}.progress1 .x_left{float:left;width:3px;height:13px;clear:left;background:url('+kj.progress.cssdir+'/common/images/scroll_1.gif) no-repeat}.progress1 .x_r{float:left;width:4px;height:13px;background:url('+kj.progress.cssdir+'/common/images/scroll_1.gif) 0px -13px no-repeat}.progress1 .x_info{float:left;width:25px;color:#ff8800;overflow:hidden}</style>';
document.write(kj.progress.css);

/** 进度条样式一
 *  obj 传入对象 
 */
kj.progress.show1 = new function( ) {
	this._step = [];
	this._progress = [];
	this._w = [];
	this._objid = [];
	this._stepbase = [];
	this.close = function( id ) {
		var index = this._objid.indexOf(id);
		if(index < 0 ) return;
		kj.remove('#id_progress_' + id);
		this._objid.removeat(index);
		this._step.removeat(index);
		this._progress.removeat(index);
		this._w.removeat(index);
		this._stepbase.removeat(index);
	}
	this.open = function( obj ) {
		var obj_div=document.createElement("div");
		this._objid[this._objid.length] = obj.id;
		obj_div.id="id_progress_" + obj.id;
		var w = 700;
		var size = 100;
		if( 'size' in obj ) size = obj.size;
		if( 'w' in obj ) w = obj.w;
		this._step[this._step.length] = w/size;
		this._progress[this._progress.length] = 0;
		this._stepbase[this._stepbase.length] = 0;
		this._w[this._w.length] = w;
		obj_div.className = 'progress1';
		var h = this._objid.length * 30;
		var str_position = (kj.agent(true) == 'MSIE6.0') ? 'absolute' : 'fixed';
		obj_div.style.cssText = "width:" + (w + 37) + "px;height:13px;position:" + str_position + ";top:" + (kj.top()+h) + "px;left:" +kj.left(w) + "px";
		var tit = "";
		if('title' in obj)  tit = '<li style="float:left;clear:both;width:'+(w+7)+'px;text-align:left;color:#ff8800" id="'+obj_div.id+'_title">' + obj.title + '</li>';
		obj_div.innerHTML = '<div class="progress1">' + tit + '<li class="x_left">&nbsp;</li><li class="x_mid" style="width:' + w + 'px"><span class="x_midbg" id="' + obj_div.id + '_bg"></span></li><li class="x_r"></li><li class="x_info" id="' + obj_div.id + '_info"></li></div>';
		document.body.appendChild(obj_div);
		kj.move("#" + obj_div.id);
	}
	this.step = function( id ,title) {
		var index = this._objid.indexOf(id);
		if( index < 0 ) return;
		if(title) kj.set("#id_progress_" + id + "_title","innerHTML" , title);
		var w = kj.toint(kj.w("#id_progress_" + id + "_bg"));
		this._progress[index] += this._step[index];
		var progress = parseInt( (this._progress[index] / this._w[index]) * 100 );
		if(progress > 100 ) return;
		this._stepbase[index] += this._step[index];
		if( this._stepbase[index] > 1) {
			w += this._stepbase[index];
			this._stepbase[index] = 0;
			kj.w("#id_progress_" + id + "_bg" , w);
			kj.obj("#id_progress_" + id + "_info").innerHTML = progress + "%";
		}
	}
}
