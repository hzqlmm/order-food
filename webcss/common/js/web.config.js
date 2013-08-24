/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
var web_config = {
	domain : "",
	baseurl : "",
	basecss : "http://css.bawangfan.com",
	cookie_pre : "kj_",
	rule_uname : '/^[a-z0-9\u4E00-\u9FA5_-]+$/i',
	rule_uname_tips : "用户名只能是字母，数字，下划线,长度在4-20之间",
	rule_pwd : '/^.{4,20}$/i',
	rule_pwd_tips : "密码长度在4-20之间"
}

var web_config = {
	domain : 'http://meal3.com',
	baseurl : 'http://meal3.com'
	,basecss : '/webcss',
	cookie_pre : 'meal_',
	rule_uname : '/^[a-z0-9_\x{4e00}-\x{9fa5}\.]{2,16}$/uis',
	rule_uname_tips : '账号只能为字母、数字、下划线，并且长度在2-16字符之间',
	rule_pwd : '/^.{4,16}$/is',
	rule_pwd_tips : '密码长度在4-16之间'
	}