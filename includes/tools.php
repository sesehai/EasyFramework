<?php
require_once ROOT_PATH . '/includes/lib/json.lib.php';
class Tools{
	public static function json_encode($value){
		$result = '';
		if ( function_exists('json_encode') ){
			return json_encode($value);
		}
		Base::import('json.lib');
		$json = new Services_JSON();
		$result = $json->encode($value);
		return $result;
	}

	public static function json_decode($value, $type = 0)
	{
		$result = '';
		if ( function_exists('json_decode') ){
			return json_decode($value,$type);
		}
		if( !empty($value) ){
			Base::import('json.lib');
			if($type){
				$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			}else{
				$json = new Services_JSON();
			}
			$result = $json->decode($value);
		}
		return $result;
	}

	/**
	 * unicode to utf8
	 *
	 * @param string $str
	 * @return string
	 */
	public static function unicode_decode($str)
	{
		$result = preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
		create_function(
		'$matches',
		'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
		),
		$str);
		$result = !empty($result) ? str_replace('\n','',$result):$result;
		return $result;
	}
	/**
	 * utf8 to unicode
	 *
	 * @param string $str
	 * @return string
	 */
	public static function unicode_encode($str) {
		$str=iconv('UTF-8','UCS-2',$str);//PHP默认编码为UCS-2  
		for ($i=0,$len=strlen($str)-1;$i<$len;$i+=2) {
			$char1=$str[$i];
			$char2=$str[$i+1];
			if (ord($char1)>0) {
				$char1=dechex(ord($char1));
				$char2=dechex(ord($char2));
				$s.='\u'.(strlen($char1)>1?'':'0').$char1.(strlen($char2)>1?'':'0').$char2;
			} else {
				$s.=$char2;
			}
		}
		return $s;
	}
	
	/**
	 * 获得客户端的操作系统
	 *
	 * @access   private
	 * @return   void
	 */
	public static function getClientOs($agent){
		$os = '';
		if(eregi('win', $agent) && strpos($agent, '95')){
			$os = 'Windows 95';
		}else if (eregi('win 9x', $agent) && strpos($agent, '4.90')){
			$os = 'Windows ME';
		}else if (eregi('win', $agent) && ereg('98', $agent)){
			$os = 'Windows 98';
		}else if (eregi('win', $agent) && eregi('nt 5.1', $agent)){
			$os = 'Windows XP';
		}else if (eregi('win', $agent) && eregi('nt 5', $agent)){
			$os = 'Windows 2000';
		}else if (eregi('win', $agent) && eregi('nt', $agent)){
			$os = 'Windows NT';
		}else if (eregi('win', $agent) && ereg('32', $agent)){
			$os = 'Windows 32';
		}else if (eregi('sun', $agent) && eregi('os', $agent)){
			$os = 'SunOS';
		}else if (eregi('ibm', $agent) && eregi('os', $agent)){
			$os = 'IBM OS/2';
		}else if (eregi('Macintosh', $agent)){
			$os = 'Macintosh';
		}else if (eregi('iPhone', $agent)){
			$os = 'iPhone';
		}else if (eregi('iPod', $agent)){
			$os = 'iPod';
		}else if (eregi('iPad', $agent)){
			$os = 'iPad';
		}else if (eregi('Android', $agent)){
			$os = 'Android';
		}else if (eregi('PowerPC', $agent)){
			$os = 'PowerPC';
		}else if (eregi('AIX', $agent)){
			$os = 'AIX';
		}else if (eregi('HPUX', $agent)){
			$os = 'HPUX';
		}else if (eregi('NetBSD', $agent)){
			$os = 'NetBSD';
		}else if (eregi('BSD', $agent)){
			$os = 'BSD';
		}else if (ereg('OSF1', $agent)){
			$os = 'OSF1';
		}else if (ereg('IRIX', $agent)){
			$os = 'IRIX';
		}else if (eregi('FreeBSD', $agent)){
			$os = 'FreeBSD';
		}else if (eregi('teleport', $agent)){
			$os = 'teleport';
		}else if (eregi('flashget', $agent)){
			$os = 'flashget';
		}else if (eregi('webzip', $agent)){
			$os = 'webzip';
		}else if (eregi('offline', $agent)){
			$os = 'offline';
		}else{
			$os = 'Unknown';
		}
		return $os;
	}

}
?>