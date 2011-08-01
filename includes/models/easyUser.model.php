<?php
class EasyModel extends BaseModel {
	public function __construct() {
		parent::__construct();
		
		$privs = 'stat|test|view,default|default|default,default|default|wellcome';
		$privs .= ',sinaweibo|weibouser|list,sinaweibo|weibouser|add,sinaweibo|weibouser|edit,sinaweibo|weibouser|delete';
		$privs .= ',sinaweibo|usertype|list,sinaweibo|usertype|add,sinaweibo|usertype|edit,sinaweibo|usertype|delete';
		$privs .= ',stat|statinfo|list,stat|statinfo|delete';
		$privs .= ',stat|netstat|list,stat|netstat|delete';
		$privs .= ',stat|ipadfeedback|list,stat|ipadfeedback|delete';
		$privs .= ',stat|videostat|list,stat|videostat|delete';
		$privs .= ',all';
		
		$privs_appstat01 = 'stat|info|view,default|default|default,default|default|wellcome';
		
		$letv007 = array(
		'user_id'       => '1',
		'passwd'        => '007',
		'user_name'     => 'letv007',
		'reg_time'      => '2011-06-02',
		'last_login'    => '1',
		'last_ip'       => '',
		'privs'         => $privs,
		);
		
		$appstat01 = array(
		'user_id'       => '2',
		'passwd'        => '001',
		'user_name'     => 'letv007',
		'reg_time'      => '2011-06-02',
		'last_login'    => '1',
		'last_ip'       => '',
		'privs'         => $privs_appstat01,
		);
		
		$this->users = array(
			'letv007'=>$letv007,
			'appstat01'=>$appstat01,
		);
	}

	public function auth($user_name, $password){
		$result = 0;
		if( $user_name == $this->users[$user_name]['user_name'] && $password == $this->users[$user_name]['passwd'] ){
			$result = $this->users[$user_name]['user_name'];
		}
		return $result;
	}
	
	public function get($user_name){
		return $this->users[$user_name];
	}

}
?>