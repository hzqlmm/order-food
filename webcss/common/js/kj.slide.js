/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 增加 幻灯片 相关功能
 */
kj.slide = new function(){
	this.index = []
	this.list = [];
	this.t = [];
	this.stop = [];
	this.init = function() {
		var arr = kj.obj(".kj_slide");
		var list1 = [];
		var src,url,i,j,arr2;
		for(i = 0; i < arr.length;i++ ) {
			arr2 = kj.obj(".xbtn span" , arr[i]);
			list1 = [];
			for( j = 0 ; j < arr2.length ; j++ ) {
				src = kj.getAttribute(arr2[j],'mysrc');
				url = kj.getAttribute(arr2[j],'myurl');
				if(src!=null) list1[list1.length] = {'src':src , 'url' : url};
			}
			this.stop[i] = false;
			this.index[i] = 0;
			this.list[this.list.length] = list1;
		}
		this.scroll(0);
		kj.handler(".kj_slide .xbtn span","mouseover",function(){
			var p = kj.parent(this,".kj_slide");
			var arr = kj.obj(".xbtn span" , p);
			var index = kj.index(arr , this);
			var ii = kj.index(kj.obj(".kj_slide") , p);
			if(index >= kj.slide.list[ii].length) return;
			kj.slide.index[ii] = index;
			kj.slide.scroll(ii);
			kj.slide.stop[ii] = true;
		});
		kj.handler(".kj_slide .xbtn span","mouseout",function(){
			var p = kj.parent(this,".kj_slide");
			var arr = kj.obj(".xbtn span" , p);
			var index = kj.index(arr , this);
			var ii = kj.index(kj.obj(".kj_slide") , p);
			if(index >= kj.slide.list[ii].length) return;
			kj.slide.stop[ii] = false;
			kj.slide.index[ii] = index;
			kj.slide.scroll(ii);
		});
		kj.handler(".kj_slide .xpic img","mouseover",function(){
			var p = kj.parent(this,".kj_slide");
			var ii = kj.index(kj.obj(".kj_slide") , p);
			kj.slide.stop[ii] = true;
		});
		kj.handler(".kj_slide .xpic img","mouseout",function(){
			var p = kj.parent(this,".kj_slide");
			var ii = kj.index(kj.obj(".kj_slide") , p);
			kj.slide.stop[ii] = false;
			kj.slide.scroll();
		});
	}
	this.scroll = function(ii) {
		if(this.stop[ii] == false) {
			if(this.index[ii] >= this.list[ii].length) return;
			var arr = kj.obj(".kj_slide");
			kj.set(kj.obj(".xpic img" , arr[ii]),'src',this.list[ii][this.index[ii]].src);
			kj.set(kj.obj(".xpic a" , arr[ii]),'href',this.list[ii][this.index[ii]].url);
			arr = kj.obj(".xbtn span" , arr[ii]);
			kj.delClassName(arr,'ysel');
			kj.addClassName(arr[this.index[ii]],'ysel');
			this.index[ii]++;
			if(this.index[ii]>=this.list[ii].length) this.index[ii] = 0;
			if(this.t[ii]) clearTimeout(this.t[ii]);
			this.t[ii] = setTimeout('kj.slide.scroll('+ii+')',3000);
		}
	}
};