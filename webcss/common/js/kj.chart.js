/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
kj.chart = new function() {
	/* 柱形统计图
	 * o : 为对象，包括以下属性
	 * w : 横坐标值，h : 纵坐标值，splitY : 纵坐标轴数量，canvas : 画布对象，默认为document.body，tagYW : 纵坐标标注
	 * x : 图左上角x坐标，y : 图左上角y坐标，list : 数组[{'sub':'0:00','sup':'x1','size':0},{'sub':'0:00','sup':'x1','size':0}]
	 * sub : 下标 , sup : 上标 , size : 值 , ( bgcolor : 背景色 , borderColor : 边框色 )(可选)
	 * tagColor : 横纵标记字体颜色
	 */
	this.bar = function(o) {
		if(!('canvas' in o)) o.canvas = '';
		if(!('x' in o)) o.x = 0;
		if(!('y' in o)) o.y = 0;
		if(!('w' in o) && o.canvas) o.w = kj.w(o.canvas);
		if(!('h' in o) && o.canvas) o.h = kj.h(o.canvas);
		if(!('borderColor' in o)) o.borderColor = '#888888';
		if(!('tagColor' in o)) o.tagColor = '#000000';
		var i,j,ii,jj,css,x,x1,len,xx;
		if('splitY' in o) {
			o.spacingY = o.h/o.splitY;//算纵间距
			ii = o.splitY;
			//横线
			j = 0;
			if(!('tagYW' in o)) o.tagYW = 40;
			//标注
			kj.draw.rect({'x':o.x-o.tagYW,'y':o.y,'w':o.tagYW ,'h':20 ,'css':'text-align:right;color:'+o.tagColor+';','canvas':o.canvas,'html':ii*o.sizeY});
			ii--;
			for(i = o.y+o.spacingY ; i < o.h ; i=i+o.spacingY) {
				//kj.draw.line( {'x1':o.x , 'y1':i , 'x2':o.x+o.w , 'y2':i , 'canvas':o.canvas , 'color':o.borderColor} );
				(j%2 == 0)? css = 'background:#efefef;' : css = '';
				j++;
				kj.draw.rect({'x':o.x,'y':i,'w':o.w,'h':o.spacingY,'css':'border-top:1px ' + o.borderColor + ' solid;' + css,'canvas':o.canvas});
				//标注
				kj.draw.rect({'x':o.x-o.tagYW,'y':i,'w':o.tagYW ,'h':20 ,'css':'text-align:right;color:'+o.tagColor+';','canvas':o.canvas,'html':ii*o.sizeY});
				ii--;
			}
		}
		//大框
		kj.draw.rect({'x':o.x,'y':o.y,'w':o.w,'h':o.h,'css':'border:1px ' + o.borderColor + ' solid;','canvas':o.canvas});
		len = parseInt(o.w/o.list.length);
		len_split = len/5;
		len = len-len_split;
		x = 0;
		j = 0;
		var arr_bgcolor = ['#AFD8F8','#F6BD0F','#8BBA00','#008E8E','#D64646','#8E468E','#588526','#FFF468','#008ED6','#9D080D','#A186BE','#CC6600','#FDC689','#ABA000','#F26D7D','#FFF200','#0054A6','#F7941C','#CC3300'];
		for(i = 0 ; i < o.list.length ; i++){
			x+=len_split;
			x1 = o.x+x+i*len;
			kj.draw.line( {'x1':x1 , 'y1':o.y+o.h , 'x2':x1+len , 'y2':o.y+o.h , 'canvas':o.canvas , 'color':'#ff0000'} );
			('borderColor' in o.list[i]) ? borderColor = o.list[i].borderColor : borderColor = '#8F8E8D';
			if('bgcolor' in o.list[i]) {
				bgcolor = o.list[i].bgcolor;
			} else {
				bgcolor = arr_bgcolor[j];
				j++;
				if(j>=arr_bgcolor.length) j=0;
			}
			('css' in o.list[i]) ? css = o.list[i].css : css = '';
			kj.draw.rect({'x':x1,'y':o.y+o.h-o.list[i].size,'w':len,'h':o.list[i].size,'css':'border:1px ' + o.borderColor + ' solid;border-bottom:0px;background-color:' + bgcolor+';'+css,'canvas':o.canvas});
			//下标
			if('sub' in o.list[i]) kj.draw.rect({'x':x1,'y':o.y+o.h+5,'w':len,'html':o.list[i].sub,'canvas':o.canvas,'css':'text-align:center;color:'+o.tagColor+';'});
			if('sup' in o.list[i]) {
				xx=o.y+o.h-o.list[i].size;
				if(o.h-o.list[i].size > 25) xx = xx-25;
				kj.draw.rect({'x':x1,'y':xx,'w':len,'html':o.list[i].sup,'canvas':o.canvas,'css':'text-align:center;'});
			}
		}
	}
	/* 线形统计图
	 * o : 为对象，包括以下属性
	 * x : 图左上角x坐标，y : 图左上角y坐标，w : 横坐标值，h : 纵坐标值，canvas : 画布对象，默认为document.body
	 * sizeX : 为x大小 , sizeY : 为y大小 , splitY : 为纵轴分隔数 , splitX : 为横轴分隔数
	 * list : 数组[{'val':100,'tips':'值100'},{'val':200,'tips':'值200'}]
	 * sub : 下标 , subW : 下标宽 , lineColor : 曲线颜色 , dotColor : 曲线上面点颜色 , fontW : 单字节宽度
	 */
	this.line = function(o) {
		if(!('canvas' in o)) o.canvas = '';
		if(!('x' in o)) o.x = 0;
		if(!('y' in o)) o.y = 0;
		if(!('w' in o) && o.canvas) o.w = kj.w(o.canvas) - o.x;
		if(!('h' in o) && o.canvas) o.h = kj.h(o.canvas) - o.y;
		if(!('borderColor' in o)) o.borderColor = '#888888';
		if(!('tagColor' in o)) o.tagColor = '#000000';
		if(!('tipsshow' in o)) o.tipsshow = 'none';
		if(!('fontW' in o)) o.fontW = 8;//文字宽
		if(!('splitX' in o)) o.splitX = o.list.length;
		var i,j,ii,jj,css,x,x1,len,xx,w;
		if('splitY' in o) {
			o.spacingY = o.h/(o.splitY);//算纵间距
			ii = o.splitY;
			//横线
			j = 0;
			x=o.sizeY/o.splitY;
			w = kj.len(ii*x+'') * o.fontW + o.fontW;
			//标注
			kj.draw.rect({'x':o.x-w-5,'y':o.y,'w':w ,'htmlW':w ,'h':20 ,'css':'padding-right:10px;color:'+o.tagColor+';','canvas':o.canvas,'html':ii*x});
			ii--;
			for(i = o.y+o.spacingY ; i < o.y+o.h ; i=i+o.spacingY) {
				(j%2 == 0)? css = 'background:#F7F8F4;' : css = 'background:#F5EEE8';
				j++;
				kj.draw.rect({'x':o.x,'y':i,'w':o.w,'h':o.spacingY,'css':'border-top:1px ' + o.borderColor + ' dotted;' + css,'canvas':o.canvas});
				//标注
				kj.draw.rect({'x':o.x-w-5,'y':i,'w':w,'htmlW':w ,'h':20 ,'css':'text-align:right;padding-right:3px;color:'+o.tagColor+';','canvas':o.canvas,'html':ii*x});
				ii--;
			}
		}
		o.spacingX = parseInt(o.w/o.splitX);//算纵间距
		if(!('sub' in o)) o.sub=[];
		//纵线
		x = 0;
		j = 0;
		w = o.spacingX;
		if('subW' in o) w = o.subW;
		len = w/2;
		for(i = o.x+o.spacingX ; i < o.x+o.w ; i=i+o.spacingX) {
			kj.draw.rect({'x':i,'y':o.y,'w':o.spacingX,'h':o.h,'css':'border-left:1px ' + o.borderColor + ' dotted;','canvas':o.canvas});
		}
		//大框
		kj.draw.rect({'x':o.x,'y':o.y,'w':o.w,'h':o.h,'css':'border:1px ' + o.borderColor + ' solid;','canvas':o.canvas});
		//曲线与下标
		x=o.h/o.sizeY;
		ii = 1;
		var lineColor = '#000000';
		if('lineColor' in o) lineColor = o.lineColor;
		if(!('dotColor' in o)) o.dotColor = '#000000';
		for(i = o.x+o.spacingX ; i < o.x+o.w ; i=i+o.spacingX) {
			if(ii<o.list.length && o.list[ii]) {
				//曲线
				kj.draw.line({'x1':i-o.spacingX,'y1':o.y+o.h-o.list[ii-1].val*x,'x2':i , 'y2':o.y+o.h-o.list[ii].val*x , 'canvas':o.canvas , 'color':lineColor});
			}
			//下标
			if( j < o.sub.length && o.sub[j]) {
				x1 = o.x-1+j*o.spacingX;
				//标注点号
				kj.draw.rect({'x':x1,'y':o.y+o.h-1,'w':3 , 'h':3 , 'canvas':o.canvas , 'css':'background:'+ o.borderColor + ';'});
				//标注文字
				x1 = o.x-len+j*o.spacingX;
				kj.draw.rect({'x':x1,'y':o.y+o.h+5,'w':o.spacingX , 'htmlW':w ,'html':o.sub[j],'canvas':o.canvas,'css':'text-align:center;color:'+o.tagColor+';'});
			}
			j++;
			ii++;
		}
		j = 0;
		ii = 0;
		for(i = o.x ; i < o.x+o.w ; i=i+o.spacingX) {
			if(ii<o.list.length && o.list[ii]) {
				kj.draw.rect({'x':i-2,'y':o.y+o.h-o.list[ii]['val']*x-2,'w':5 , 'h':5 , 'canvas':o.canvas , 'css':'background:' + o.dotColor + ';','id':'id_chart_line_' + ii});
			}
			j++;
			ii++;
		}
		//标点提示
		j = 0;
		ii = 0;
		var tipsshow;
		for(i = o.x ; i < o.x+o.w ; i=i+o.spacingX) {
			if(ii<o.list.length && o.list[ii]) {
				//备注
				if('tips' in o.list[ii] && o.list[ii].tips!='') {
					o.list[ii]['tips']+="";
					arr = o.list[ii]['tips'].split('<br>');
					//计算提示文字长度得div宽度
					w = 0;
					h = 30;
					for(jj = 0;jj<arr.length;jj++) {
						len = kj.len(arr[jj]);
						h+=20;
						if(len>w) w = len;
					}
					h-=20;
					w=w * o.fontW;
					tipsshow = o.tipsshow;
					if(o.list[ii]['val'] == 0) tipsshow = 'none';
					kj.draw.rect({'x':i-20,'y':o.y+o.h-o.list[ii]['val']*x-h,'w':w , 'html':o.list[ii]['tips'],'htmlW':w,'canvas':o.canvas,'css':'padding:5px;background:#ffffff;display:'+tipsshow+';border:1px #cccccc solid;','id':'id_chart_line_' + ii + '_tips'});
					if(o.tipsshow == 'none' || o.list[ii]['val'] == 0) {
						kj.handler('#id_chart_line_' + ii,'mouseover',function(){kj.show('#'+this.id+'_tips');});
						kj.handler('#id_chart_line_' + ii,'mouseout',function(){kj.hide('#'+this.id+'_tips');});
					}
				}
			}
			j++;
			ii++;
		}
	}
}