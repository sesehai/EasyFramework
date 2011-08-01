<?php
/**
 * iparea.lib.php 根据ip地址获取ip所在地区的类
 */
class iparea {
	/**
	 * 构造函数
	 *
	 */
	public function __construct($datatype = 'letvg3' ) {
		if( $datatype == 'letvg3' ){
			$this->func = 'letvg3';
		}
	}

	
	/**
	 * 使用letv g3接口取得ip数据所在地
	 * @param string $ip
	 * @return string $result NOTCN (国外ip)/国内ip所在地
	 */
	private function letvg3($ip){
		$result = '';
		$url = "http://example.com?uip=$ip";
		$strJson = file_get_contents($url);
		$array_result = json_decode($strJson,TRUE);
		
		print_r($array_result);
		if( isset($array_result['geo']) && !empty($array_result['geo']) ){
			if( strpos(strtolower($array_result['geo']),'cn') === FALSE ){
				$result = 'NOTCN';
			}else{
				$result = $array_result['desc'];
			}
		}
		return $result;
	}

}
?>