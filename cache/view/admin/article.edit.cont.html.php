<script>
//保存时交验规则
admin.rule['save'] =[
    { name : 'article_title' , rule:'empty' , tips:'文章标题不能为空'}
];
kj.onload(function(){
	<?php if(in_array('fonw-weight:bold' , $editinfo['article_css'])){?>
		kj.event("#id_css_bold","click");
	<?php }?>
	<?php if(in_array('font-style:italic' , $editinfo['article_css'])){?>
		kj.event("#id_css_italic","click");
	<?php }?>
	var color = (kj.obj("#id_css_color")) ? kj.obj("#id_css_color").value : '';
	if(color) kj.set('#id_article_title','style.color',color.replace('color:',''));
});
//店铺选择回调函数
function shop1_callback(o) {
	if("id" in o) kj.obj("#id_article_about_id").value=o.id;
	if("name" in o) kj.obj("#id_article_about").innerHTML = o.name;
	kj.hide("#windivabout_iddiv");
}
</script>
<div class="pMenu" id="id_pMenu">
	<li class="sel" onclick="admin.edittabel(0);">基本信息</li>
	<li onclick="admin.edittabel(1);">扩展信息</li>
</div>
<div class="pMain" id="id_main">
<input type="hidden" name="article_channel_id" value="<?php echo $editinfo['article_channel_id'];?>">
<input type="hidden" name="article_state" value="<?php echo $editinfo['article_state'];?>">
<table class='pEditTable'>
<tr class='pTabTitRow'><td class='pTabTitCol' colspan="2"></td></tr>
<?php if($channel_user_type=='default'){?>
<tr>
	<td class="pTabColName">所属用户：</td><td class="pTabColVal">
		<input type="hidden" name="article_about_id" value="<?php echo $editinfo['article_about_id'];?>" id="id_article_about_id" required ruletips="请选择所属用户" ruletipsmode='1'>
		<div class="more1" onmouseover="kj.windiv({'id':'about_iddiv','fid':this,'src':'common.php?app=sys&app_act=user.dialog1&callback=shop1_callback'});" id="id_article_about" onmouseout="kj.hide('#windivabout_iddiv');"><?php if(empty($editinfo["article_about_name"])){?>选择<?php } else { ?><?php echo $editinfo["article_about_name"];?><?php }?></div>
		<div style="float:left;padding-left:20px"><a href="javascript:master_open({id:'add_user',title:'添加用户',url:'?app_module=sys&app=user&app_act=edit',w:500});" style="color:#ff8800">[创建]</a></div>
	</td>
	</tr>
<?php } else if($channel_user_type=='shop') { ?>
<tr>
	<td class="pTabColName">所属店铺：</td><td class="pTabColVal">
		<input type="hidden" name="article_about_id" value="<?php echo $editinfo['article_about_id'];?>" id="id_article_about_id" required ruletips="请选择所属店铺" ruletipsmode='1'>
		<div class="more1" onmouseover="kj.windiv({'id':'about_iddiv','fid':this,'src':'common.php?app=meal&app_act=shop1&url_mode=2'});" id="id_article_about" onmouseout="kj.hide('#windivabout_iddiv');"><?php if(empty($editinfo["article_about_name"])){?>选择<?php } else { ?><?php echo $editinfo["article_about_name"];?><?php }?></div>
	</td>
	</tr>
<?php }?>
<tr>
	<td class="pTabColName">标&nbsp;&nbsp;题：</td><td class="pTabColVal"><input type="input" name="article_title" id="id_article_title" value="<?php echo $editinfo['article_title'];?>" class='pTxt1 pTxtL300' required ruletips="请输入标题"></td></tr>
<tr>
	<td class="pTabColName">内容：</td><td class="pTabColVal"><textarea name="article_content" id="article_content" cols="60" rows="5" style="display:none"><?php echo $editinfo['article_content'];?></textarea><?php echo fun_get::editor('article_content','admin');?></td></tr>
</table>
<!--label 2 end-->
<table class='pEditTable' style='display:none'>
<tr class='pTabTitRow'><td class='pTabTitCol' colspan="2"></td></tr>
<tr>
	<td class="pTabColName">跳转链接：</td><td class="pTabColVal"><input type="checkbox" name="article_islink" value="<?php echo $editinfo['article_islink'];?>" onclick="if(this.checked){kj.show('#id_linkurl');}else{kj.hide('#id_linkurl');}"<?php if($editinfo['article_islink']==1){?> checked<?php }?>>&nbsp;&nbsp;<span id="id_linkurl"<?php if($editinfo['article_islink']!=1){?> style="display:none"<?php }?>><input type="input" name="article_linkurl" value="<?php if(empty($editinfo['article_linkurl'])){?>http://<?php } else { ?><?php echo $editinfo['article_linkurl'];?><?php }?>" class='pTxt1 pTxtL300'></span></td></tr>
<tr>
	<td class="pTabColName">标题样式：</td><td class="pTabColVal" style="line-height:25px"><input type="checkbox" name="article_css[]" value="fonw-weight:bold" onclick="if(this.checked){kj.set('#id_article_title','style.fontWeight','bold');}else{kj.set('#id_article_title','style.fontWeight','normal');}" id="id_css_bold">加粗&nbsp;&nbsp;<input type="checkbox" name="article_css[]" value="font-style:italic" onclick="if(this.checked){kj.set('#id_article_title','style.fontStyle','italic');}else{kj.set('#id_article_title','style.fontStyle','normal');}" id="id_css_italic">斜体&nbsp;&nbsp;
		<select name="article_css[]" id="id_css_color" style="width:50px" onchange="kj.set('#id_article_title','style.color',this.value.replace('color:',''));">
		<option value="" style=""></option>
		<option value="color:#ff0000" style="background:#ff0000" <?php if(in_array('color:#ff0000' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#0000ff" style="background:#0000ff" <?php if(in_array('color:#0000ff' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#ff8800" style="background:#ff8800" <?php if(in_array('color:#ff8800' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#00ff00" style="background:#00ff00" <?php if(in_array('color:#00ff00' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#660099" style="background:#660099" <?php if(in_array('color:#660099' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#999999" style="background:#999999" <?php if(in_array('color:#999999' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#009933" style="background:#009933" <?php if(in_array('color:#009933' , $editinfo['article_css'])){?> selected<?php }?>></option>
		<option value="color:#990099" style="background:#990099" <?php if(in_array('color:#990099' , $editinfo['article_css'])){?> selected<?php }?>></option>
		</select>	
	<span class="pBeta"></span></td>
	</tr>
<tr>
	<td class="pTabColName">状 态：</td>
	<td class="pTabColVal">
		<select name="article_state">
		<?php foreach($arr_state as $item=>$key){ ?>
			<option value="<?php echo $key;?>"<?php if($key==$editinfo['article_state']){?> selected<?php }?>><?php echo $item;?></option>
		<?php }?>
		</select>
	</td></tr>
<tr>
	<td class="pTabColName">属&nbsp;&nbsp;性：</td><td class="pTabColVal">
	<?php foreach($arr_attribute as $item=>$key){ ?>
		<input type="checkbox" name="article_attribute[]" value="<?php echo $item;?>"<?php if(in_array($item , $editinfo['article_attribute'])){?> checked<?php }?>><?php echo $key;?>&nbsp;&nbsp;
	<?php }?>
	</td></tr>
<tr>
	<td class="pTabColName">图片：</td><td class="pTabColVal"><input type="input" name="article_pic_big" id="id_article_pic_big" value="<?php echo $editinfo['article_pic_big'];?>" class='pTxt1 pTxtL300'>&nbsp;<a href="javascript:kj.dialog({id:'dialog_attatch',title:'选择图片',url:'common.php?app=other&app_act=attatch&url_objid=id_article_pic_big',w:600,showbtnhide:false,top:0,type:'iframe'});">选择</a><br><iframe name="frm_article_pic_big" src="common.php?app=other&app_act=upload&small=1" width="300px" height="30px" frameborder=0 scrolling="no"></iframe></td>
	</tr>
<tr>
	<td class="pTabColName">小图：</td><td class="pTabColVal"><input type="input" name="article_pic" id="id_article_pic" value="<?php echo $editinfo['article_pic'];?>" class='pTxt1 pTxtL300'>&nbsp;<a href="javascript:kj.dialog({id:'dialog_attatch',title:'选择图片',url:'common.php?app=other&app_act=attatch&url_objid=id_article_pic',w:600,showbtnhide:false,top:0,type:'iframe'});">选择</a><br><iframe name="frm_article_pic" src="common.php?app=other&app_act=upload&callback=upload_callback_small" width="300px" height="30px" frameborder=0 scrolling="no"></iframe></td>
	</tr>
<tr>
	<td class="pTabColName">简介：</td><td class="pTabColVal"><textarea name="article_intro" rows="5" cols="90"><?php echo $editinfo['article_intro'];?></textarea></td></tr>

<tr>
	<td class="pTabColName">标识符：</td><td class="pTabColVal"><input type="input" name="article_key" value="<?php echo $editinfo['article_key'];?>" class='pTxt1 pTxtL150'>&nbsp;<span class="pBeta">文章唯一标识符，方便程序调用，如注册协议，关于我们，这些网站特定文章用到</span></td></tr>
<tr>
	<td class="pTabColName">作&nbsp;&nbsp;者：</td><td class="pTabColVal"><input type="input" name="article_author" value="<?php echo $editinfo['article_author'];?>" class='pTxt1 pTxtL150'></td></tr>
<tr>
	<td class="pTabColName">来&nbsp;&nbsp;源：</td><td class="pTabColVal"><input type="input" name="article_source" value="<?php echo $editinfo['article_source'];?>" class='pTxt1 pTxtL150'></td></tr>
<tr>
	<td class="pTabColName">标&nbsp;&nbsp;签：</td><td class="pTabColVal"><input type="input" name="article_tag" value="<?php echo $editinfo['article_tag'];?>" class='pTxt1 pTxtL500'><span class="pBeta">用户于搜索优化</span></td></tr>
<tr>
	<td class="pTabColName">文章模板：</td><td class="pTabColVal"><input type="input" name="article_tpl" id="id_article_tpl" value="<?php echo $editinfo['article_tpl'];?>" class='pTxt1 pTxtL300'>&nbsp;<a href="javascript:kj.dialog({id:'dialog_attatch',title:'选择图片',url:'common.php?app=other&app_act=templates&url_objid=id_article_tpl',w:400,showbtnhide:false,top:0,type:'iframe'});">选择</a></td></tr>
</table>
<!--label 3 end-->
</div>
<div class="pFootAct" id="id_pFootAct">
	<li>
	<input type="button" name="dosubmit" value="保存" onclick="thisjs.get_cont();admin.frm_ajax('save_article' , function(){thisjs.clear_cont();});" class="pBtn">
	</li>
</div>
<script>
var thisjs = new function() {
	//因为这里用ajax，fckeditor不会取到内容，所以自己取
	this.get_cont = function() {
		var fckeditor = FCKeditorAPI.GetInstance('article_content');
		kj.obj("#article_content").value = fckeditor.GetHTML(); //这就是内容
	}
	//ajax 提交后，还得清空内容
	this.clear_cont = function() {
		//只有当添加时才要清空
		<?php if(empty($editinfo['article_id'])){?>
			var fckeditor = FCKeditorAPI.GetInstance('article_content');
			fckeditor.EditorDocument.body.innerHTML="";
		<?php }?>
	}
	//切换频道
	this.channel = function(cid) {
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'selectfolder'));?>&cid=" + cid,function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				kj.obj( "#id_folder_td" ).innerHTML = obj_data.cont;
			}
		});
	}
}
</script>