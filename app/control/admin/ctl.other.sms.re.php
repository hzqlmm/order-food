<?php
class ctl_other_sms_re extends mod_other_sms_re {

	//Ĭ�����ҳ
	function act_default() {
		$this->arr_list = $this->get_list();
		return $this->get_view(); //��ʾҳ��
	}

	//�������
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
}
?>