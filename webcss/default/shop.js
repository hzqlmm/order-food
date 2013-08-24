var jsshop = new function() {
	//默认加载米饭
	this.rice = false;
	this.cart_show = 0;
	this.cart_lock = false;
	this.cart = [];
	this.index = -1;
	this.total = 0;//合计金额

}
kj.onresize(function() {
	jsshop.resize();
});
kj.onload(function() {
	jsshop.resize();
	kj.show("#id_cart_menu");
});