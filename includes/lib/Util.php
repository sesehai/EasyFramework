<?php
/**
 * 扩展功能函数类
 */
class Util{

    /**
     * 能自动去除空元素explode
     *
     * @return Array
     */
    public static function explode($seperator,$str) {
        if(!$str) return array();
        else{
            $result=explode($seperator,$str);
            if(!$result) return $result;
            $temp=array();
            for($i=0;$i<count($result);$i++){
                if($result[$i]=='') continue;
                $temp[]=$result[$i];
            }
            return $temp;
        }
    }

    /**
     * 获取文件扩展名
     *
     * @param String $val
     * @return String
     */
    public static function getFileExtName( $fn ) {
        return substr(strrchr($fn,'.'), 1);
    }

    /**
     * 字符串截取
     * @param string $str
     * @param int $strlen
     * @param int $other
     * @return string
     */
    public static function doStrOut($str,$strlen=10,$other=0)
    {
        if(empty($str)){return $str;}
        $str=@iconv('UTF-8','GBK',$str);
        $j=0;
        for($i=0;$i<$strlen;$i++){
            if(ord(substr($str,$i,1))>0xa0){
                $j++;
            }
        }
        if($j%2!=0){
            $strlen++;
        }
        $rstr=@substr($str,0,$strlen);
        $rstr=@iconv('GBK','UTF-8',$rstr);
        if (strlen($str)>$strlen && $other){
            $rstr.='…';
        }
        return $rstr;
    }

    /**
     * 获取文件第一个. 之后的名字
     *
     * @param String $val
     * @return String
     */
    public static function getFileFirstExtName( $fn ) {
        return substr($fn, strpos($fn, '.') + 1);
    }

    /**
     * 得到当前用户Ip地址
     *
     * @return ip地址
     */
    public static function getRealIp() {
        $pattern = '/(\d{1,3}\.){3}\d{1,3}/';
        if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all($pattern, $_SERVER['HTTP_X_FORWARDED_FOR'], $mat)) {
            foreach ($mat[0] AS $ip) {
                //得到第一个非内网的IP地址
                if ((0 != strpos($ip, '192.168.')) && (0 != strpos($ip, '10.')) && (0 != strpos($ip, '172.16.'))) {
                    return  $ip;
                }
            }
            return $ip;
        } else {
            if (isset($_SERVER["HTTP_CLIENT_IP"]) && preg_match($pattern, $_SERVER["HTTP_CLIENT_IP"])) {
                return $_SERVER["HTTP_CLIENT_IP"];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }
        }
    }

    /**
     * 得到无符号整数表示的ip地址
     */
    public static function getIntIp() {
        return sprintf('%u', ip2long(self::getRealIp()));
    }

    /*
    * 过滤逗号分割的 id 字符串，转换全角字符为半角，去除可能的手误字符
    */
    public static function filterIdStr($idStr) {
        $idStr = trim(self::qj2bj($idStr));
        $intAry = str_split($idStr);
        $ret = array();
        foreach($intAry as $char) {
            if(is_numeric($char) || $char === ',') {
                $ret[] = $char;
            }
        }
        return trim(join('', $ret), ',');
    }

    /**
     * 取合法的 id 字符串或 id 数组
     */
    public static function getValidId($oIdStr, $resultType='string') {
        //排除部分手误影响
        $oIdStr = self::filterIdStr($oIdStr);
        while(false !== strpos($oIdStr, ',,')) {
            $oIdStr = str_replace(',,', ',', $oIdStr);
        }
        $idAry = array_unique(self::explode(',', $oIdStr));
        return ($resultType == 'string') ? join(',', $idAry) : $idAry;
    }

    public static function mkHashDir($basedir, $num=100) {
        $l = 0;
        for($i=0; $i<$num; $i++) {
            for($j=0;$j<$num;$j++) {
                $dir = $basedir . $i .'/' . $j . '/' ;
                mkdir($dir, 0777, true);
                $l++;
            }
        }
        return $l;
    }

    //生成一个17字节长唯一随机文件名
    public static function getRandFileName() {
        return chr(mt_rand(97, 122)) . mt_rand(10000,99999) . time();
    }

    /**
     * 输入一个秒数，返回时分秒格式的字符串
     *
     * @param int $secs
     * @return string
     */
    public static function secToTime($secs) {
        if ($secs < 3600) {
            return sprintf("%02d:%02d", floor($secs / 60), $secs % 60);
        }
        $h = floor($secs / 3600);
        $m = floor(($secs % 3600) / 60);
        $s = $secs % 60;
        return sprintf("%02d:%02d:%02d", $h, $m, $s);
    }

    /**
     * 全角 => 半角
     *
     * @param string $string
     * @return string
     */
    public static function qj2bj($string) {
        $convert_table = Array(
        '０' => '0',
        '１' => '1',
        '２' => '2',
        '３' => '3',
        '４' => '4',
        '５' => '5',
        '６' => '6',
        '７' => '7',
        '８' => '8',
        '９' => '9',
        'Ａ' => 'A',
        'Ｂ' => 'B',
        'Ｃ' => 'C',
        'Ｄ' => 'D',
        'Ｅ' => 'E',
        'Ｆ' => 'F',
        'Ｇ' => 'G',
        'Ｈ' => 'H',
        'Ｉ' => 'I',
        'Ｊ' => 'J',
        'Ｋ' => 'K',
        'Ｌ' => 'L',
        'Ｍ' => 'M',
        'Ｎ' => 'N',
        'Ｏ' => 'O',
        'Ｐ' => 'P',
        'Ｑ' => 'Q',
        'Ｒ' => 'R',
        'Ｓ' => 'S',
        'Ｔ' => 'T',
        'Ｕ' => 'U',
        'Ｖ' => 'V',
        'Ｗ' => 'W',
        'Ｘ' => 'X',
        'Ｙ' => 'Y',
        'Ｚ' => 'Z',
        'ａ' => 'a',
        'ｂ' => 'b',
        'ｃ' => 'c',
        'ｄ' => 'd',
        'ｅ' => 'e',
        'ｆ' => 'f',
        'ｇ' => 'g',
        'ｈ' => 'h',
        'ｉ' => 'i',
        'ｊ' => 'j',
        'ｋ' => 'k',
        'ｌ' => 'l',
        'ｍ' => 'm',
        'ｎ' => 'n',
        'ｏ' => 'o',
        'ｐ' => 'p',
        'ｑ' => 'q',
        'ｒ' => 'r',
        'ｓ' => 's',
        'ｔ' => 't',
        'ｕ' => 'u',
        'ｖ' => 'v',
        'ｗ' => 'w',
        'ｘ' => 'x',
        'ｙ' => 'y',
        'ｚ' => 'z',
        '　' => ' ',
        '：' => ':',
        '。' => '.',
        '？' => '?',
        '，' => ',',
        '／' => '/',
        '；' => ';',
        '［' => '[',
        '］' => ']',
        '｜' => '|',
        '＃' => '#',
        );
        return strtr($string, $convert_table);
    }
    /**
     * 半角 => 全角
     *
     * @param string $string
     * @return string
     */
    public static function bj2qj($string) {
        $convert_table = Array(
					':' => '：',
					//'.' => '。',
					'?' => '？',
					',' => '，',
					'/' => '／',
					';' => '；',
					'"' => '“',
        );
        return strtr($string, $convert_table);
    }


		

    /**
     * 对一个二维数组自定义排序
     *
     * @param array $ary
     * @param string $compareField
     * @param string $seq = 'DESC'|'ASC'
     * @param int $sortFlag = SORT_NUMERIC | SORT_REGULAR | SORT_STRING
     * @return array
     */
    public static function sort(&$ary, $compareField, $seq='DESC', $sortFlag=SORT_NUMERIC) {
        $sortData = array();
        foreach($ary as $key => $value) {
            $sortData[$key] = $value[$compareField];
        }
        ($seq == 'DESC') ? arsort($sortData, $sortFlag) : asort($sortData, $sortFlag);

        $ret = array();
        foreach($sortData as $key => $value) {
            $ret[$key] = $ary[$key];
        }
        $ary = $ret;
        return $ary;
    }

    public static function natsort(&$ary, $compareField) {
        $sortData = array();
        foreach($ary as $key => $value) {
            $sortData[$key] = $value[$compareField];
        }
        natcasesort($sortData);

        $ret = array();
        foreach($sortData as $key => $value) {
            $ret[$key] = $ary[$key];
        }
        $ary = $ret;
        return $ary;
    }

    public static function getCookieMsg($domain='') {
        if(isset($_COOKIE['json'])) {
            $jAry = json_decode($_COOKIE['json'], true) ;
            $msg = (isset($jAry['msg'])) ? $jAry['msg'] : '' ;
        } else {
            $msg = '';
        }
        $jAry['msg'] = '' ;
        $jsonStr = json_encode($jAry) ;
        setcookie('json', $jsonStr, time()+3600*24, '/', '.' . $domain);
        setcookie('json', $jsonStr, time()+3600*24, '/');
        return $msg;
    }

    public static function redirect($url = ''){
        if(!$url) {
            if(isset($_SERVER['HTTP_REFERFER']) && $_SERVER['HTTP_REFERER']) {
                $url = $_SERVER['HTTP_REFERER'];
            } else {
                $url = '/';
            }
        }
        Header("Location: " . $url);
        exit;
    }

    public static function cookieMsgRedirect( $msg, $url='' ) {
        self::setCookieMsg($msg);
        if(!$url) {
            if(isset($_SERVER['HTTP_REFERFER']) && $_SERVER['HTTP_REFERER']) {
                $url = $_SERVER['HTTP_REFERER'];
            } else {
                $url = '';
            }
        }
        header( "Location: $url " );
        exit();
    }

    public static function setCookieMsg($msg, $domain='') {
        if(isset($_COOKIE['json'])) {
            $jAry = json_decode($_COOKIE['json'], true) ;
        }
        $jAry['msg'] = $msg ;
        $jsonStr = json_encode($jAry) ;
        setcookie("json", $jsonStr, time()+3600*24, '/', '.' . $domain);
        setcookie("json", $jsonStr, time()+3600*24, '/');
    }

    public static function setRawCookie($name, $value, $life, $path='/', $domain='') {
        if($life ==0 || $life == ''){
            return setrawcookie($name, $value, time(), $path, '.' . $domain);
        }else{
            return setrawcookie($name, $value, time()+$life, $path, '.' . $domain);
        }
        #return setrawcookie($name, $value, time()+$life, $path);
    }


    public static function setCookie($name, $value, $life = 0, $path='/', $domain='') {
        if($life == 0){
            return setcookie($name, $value, 0, $path, '.' . $domain);
        }
        return setcookie($name, $value, time()+$life, $path, '.' . $domain);
        #return setcookie($name, $value, time()+$life, $path);
    }

    /**
     * 文本入库前的过滤工作
     */
    public static function getSafeText($textString, $htmlspecialchars=true) {
        return $htmlspecialchars
        ? htmlspecialchars(trim(strip_tags(self::qj2bj($textString))))
        : trim(strip_tags(self::qj2bj($textString)))
        ;
    }

    public static function getSafeXml($string) {
        return self::getSafeUtf8(self::getSafeText($string), $_htmlspecialchars=true);
    }

    public static function getSafeUtf8( $content ) {
        $content = mb_convert_encoding($content, 'gbk', 'utf-8');
        $content = mb_convert_encoding($content, 'utf-8', 'gbk');
        $content = preg_replace( '/[\x00-\x08\x0b\x0c\x0e-\x1f]/', '', $content );
        return $content;
    }
    
    public static function getSafeGbk( $content ) {
        $content = mb_convert_encoding($content, 'utf-8', 'gbk');
        $content = preg_replace( '/[\x00-\x08\x0b\x0c\x0e-\x1f]/', '', $content );
        return $content;
    }

    public static function debug($logFile='') {
        if(!$logFile) {
            $logFile = '/tmp/debug.log';
        }
        $fp = fopen('php://stdout', 'w');

        static $__start_time = NULL;
        static $__start_code_line = 0;
        $dtrace = debug_backtrace();
        $call_info = array_shift( $dtrace );
        $code_line = $call_info['line'];
        $fileAry = explode('/', $call_info['file']);
        $file = array_pop( $fileAry);

        if( $__start_time === NULL ) {
            $str = "debug ".$file."> initialize\n";
            fputs($fp, $str);
            self::log($str, $logFile);
            $__start_time = microtime(true);
            $__start_code_line = $code_line;
            fclose($fp);
        } else {
            $str = sprintf("debug %s> code-lines: %d-%d time: %.4f mem: %d KB\n", $file, $__start_code_line, $code_line, (microtime(true) - $__start_time), /*ceil( memory_get_usage()/1024)*/0);
            fputs($fp, $str);
            fclose($fp);
            self::log($str, $logFile);
            $__start_time = microtime(true);
            $__start_code_line = $code_line;
        }
    }

    public static function msgRedirect($msg, $url='', $seconds=4){
        if(!$url) {
            $url = (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        $time = $seconds * 1000;
        $charset = defined('HTML_CHARSET') ? HTML_CHARSET : 'UTF-8';
        //$host = HOST_WWW;
        $html = <<<html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>提示</title>
<style type="text/css">
<!--
body{text-align:center; font-family: Arial, Helvetica, sans-serif; font-size:14px; padding-top:100px;}
a,a:visited {color:#0068b7; text-decoration:underline;}
a:hover { color:#0068b7;text-decoration:none;}
div {width:334px; height:95px; padding-top:15px;
}
div ul { margin:0px; padding:0px; list-style:none; text-align:left; line-height:25px;
}
h1{ font-size:14px;margin:0px; padding:0px; color:#eb610f;}
-->
</style>
</head>

<body>

<div>
<ul>
<li><h1>提示：</h1></li>
<li>{$msg}</li>
<li>如果您的浏览器没有自动跳转，请点<a href='javascript:location.href="{$url}"'>这里</a></li>
</ul>
</div>
<script type="text/javascript">
setTimeout('location.href="{$url}"',$time)
</script>
</body>
</html>
html;
        self::outputExpireHeader(-86400);
        echo $html;
        exit(0);
    }
//父窗打开
   public static function msgRedirect2($msg, $url='', $seconds=4){
        if(!$url) {
            $url = (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        $time = $seconds * 1000;
        $charset = defined('HTML_CHARSET') ? HTML_CHARSET : 'UTF-8';
        //$host = HOST_WWW;
        $html = <<<html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>提示</title>
<style type="text/css">
<!--
body{text-align:center; font-family: Arial, Helvetica, sans-serif; font-size:14px; padding-top:100px;}
a,a:visited {color:#0068b7; text-decoration:underline;}
a:hover { color:#0068b7;text-decoration:none;}
div {width:334px; height:95px; padding-top:15px;
}
div ul { margin:0px; padding:0px; list-style:none; text-align:left; line-height:25px;
}
h1{ font-size:14px;margin:0px; padding:0px; color:#eb610f;}
-->
</style>
</head>

<body>

<div>
<ul>
<li><h1>提示：</h1></li>
<li>{$msg}</li>
<li>如果您的浏览器没有自动跳转，请点<a href='javascript:top.location.href="{$url}"'>这里</a></li>
</ul>
</div>
<script type="text/javascript">
setTimeout('top.location.href="{$url}"',$time)
</script>
</body>
</html>
html;
        self::outputExpireHeader(-86400);
        echo $html;
        exit(0);
    }

	public static function msgWapRedirect($msg, $url='', $seconds=4){
        if(!$url) {
            $url = (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        }
        $time = $seconds * 1000;
        $charset = defined('HTML_CHARSET') ? HTML_CHARSET : 'UTF-8';
        $host = HOST_WAP;
        $html = <<<html
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="refresh" content="{$seconds};URL={$url}" />
<title>提示</title>
<style type="text/css">
<!--
body{text-align:left;font-size:12px; padding-top:10px;}
a,a:visited {color:#0068b7; text-decoration:underline;}
a:hover { color:#0068b7;text-decoration:none;}
div {width:210px; padding:10px 5px;border:1px solid #ccc; padding-bottom:20px;
}
div ul { margin:0px; padding:0px; list-style:none; text-align:left; line-height:20px;
}
h1{ font-size:12px;margin:0px; padding:0px; color:#eb610f;}
-->
</style>
</head>

<body>

<div>
<ul>
<li><h1>提示：</h1></li>
<li>{$msg}</li>
<li>如果您的浏览器没有自动跳转，<br/>请点<a href="{$url}">这里</a></li>
</ul>
</div>
</body>
</html>
html;
        //background:url(/images/tishibg.jpg) no-repeat right bottom;
        self::outputExpireHeader(-86400);
        echo $html;
        exit(0);
    }


    public static function log($msg, $file='') {
        if(!$file && defined('GENERAL_LOG')) {
            $file = GENERAL_LOG;
        }
        $date = date("Y-m-d");
        $m = '[' . date('Y-m-d H:i', time()) . "]\n";
        $d = debug_backtrace();
        foreach($d as $trace) {
            $m .= $trace['file'] . ' : ' . $trace['line'] . "\n";
        }
        $m .= $msg . "\n";
        if(stripos($file, '/tmp/') !== false) {
            $file = $file ?  $file . '.' . $date : '/tmp/tmp.log.' . $date;
            $size = file_exists($file) ? filesize($file) : 0;
            $fp = $size < 1024 * 1024 * 500 ? fopen($file , "a+") : false;
        }else{
            $file = $file ? $file : '/tmp/tmp.log.' . $date;
            $size = file_exists($file) ? filesize($file) : 0;
            $fp = $size < 1024 * 1024 * 500 ? fopen($file , "a+") : false;
        }
        if($fp) {
            fputs($fp, $m);
            fclose($fp);
        }
        if(defined('DEBUG') || isset($_GET['debug'])) {
            self::print_r($msg);
            echo nl2br($m);
            ob_flush();
            flush();
        }
    }
    public static function print_r($var) {
        echo '<pre>' . print_r($var, true) . '</pre>';
    }

    public static function var_export($var) {
        echo '<pre>' . var_export($var, true) . '</pre>';
    }

    /**
     * text2html
     *
     * @return String
     */
    public static function text2html($txt){
        return  nl2br(str_replace(" ", "&nbsp;", htmlspecialchars($txt, ENT_QUOTES)));
    }

    public static function getHumanReadableLastTime($loginLast){
        $period = time() - ((is_numeric($loginLast)) ? $loginLast : strtotime($loginLast));
        if ($period < 0) {
            return "1秒前";
        } elseif ($period < 60) {
            return $period . "秒前";
        } elseif ($period < 3600) {
            return round($period / 60, 0) . "分钟前";
        } elseif ($period < 86400) {
            return round($period / 3600, 0) . "小时前";
        } else {
            return round($period / 86400, 0) . "天前";
        }
    }

    public static function outputExpireHeader( $lifetime=300 ) {
        header("Expires: ".gmdate("D, d M Y H:i:s", time()+$lifetime)." GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Cache-Control: max-age=$lifetime");
    }

    /**
     * send mail
     * @author  zhangyun
     * @since 20081212
     *
     * @param unknown_type $address_to
     * @param unknown_type $subject
     * @param unknown_type $htmlcontent
     * @param unknown_type $plaincontent
     * @param unknown_type $attachment
     * @param unknown_type $address_from
     * @param unknown_type $name
     * @return unknown
     */
    public static function  sendMail($address_to, $subject, $htmlcontent, $plaincontent, $attachment,$address_from,$name) {
        require_once('lib/plugin/phpmailer/class.phpmailer.php');
        //global $admin_name, $admin_email;

        $PHPMailer = new PHPMailer;
        $PHPMailer->From     = $address_from;
        $PHPMailer->FromName = $name;
        $PHPMailer->ClearAllRecipients();
        $PHPMailer->AddAddress($address_to);
        $PHPMailer->Subject  = $subject;
        #$PHPMailer->Body     = $htmlcontent;
        $PHPMailer->MsgHTML($htmlcontent);
        $PHPMailer->AltBody  = $plaincontent;
        $PHPMailer->IsHTML(true);
        while(list(, $v) = each($attachment))
        {
            $PHPMailer->AddAttachment($v['file'],$v['nickname']);
        }
        $status = @$PHPMailer->Send();
        $PHPMailer->ClearAddresses();
        $PHPMailer->ClearAttachments();
        return $status;
    }
    public static function outputHtml( $str, $charset='utf-8' ) {
        self::outputExpireHeader(-86400);
        echo <<<html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
</head>
<body>
{$str}
</body>
</html>
html;
}

public static function addIdToIdStr($idStr, $id, $reverse=false) {
    if($reverse) {
        return $idStr ? $idStr . ',' . $id  : $id;
    }
    return $idStr ? $id . ',' . $idStr : $id;
}

public static function removeIdFromIdStr($idStr, $id)
{
    $idStr = ',' . $idStr . ',';
    return trim(str_replace(",{$id},", ',', $idStr ), ',');
}

public static function replaceIdFromIdStr($idStr, $oldId, $newId)
{
    $idStr = ',' . $idStr . ',';
    return trim(str_replace(",{$oldId},", ",{$newId},", $idStr), ',');
}

public static function isExistInIdStr($idStr, $id)
{
    $idStr = ',' . $idStr . ',';
    return (strpos($idStr, ",{$id},") !== false);
}


public static function isCnc($ip) {
    $cncNetworkData = array (
    '0011101000010000' => 0,
    '00111010000100010' => 0,
    '00111010000100011' => 0,
    '0011101000010010' => 0,
    '0011101000010011' => 0,
    '0011101000010101' => 0,
    '001110100001011' => 0,
    '0011101010010000' => 0,
    '001110101111000' => 0,
    '001110101111010' => 0,
    '001110101111011' => 0,
    '0011110000000' => 0,
    '001111000000100' => 0,
    '001111000000110100' => 0,
    '001111000000110101' => 0,
    '00111100000011011' => 0,
    '001111000000111' => 0,
    '0011110000010' => 0,
    '00111100000110' => 0,
    '0011110000011111' => 0,
    '001111001101101' => 0,
    '00111100110111' => 0,
    '00111101001100' => 0,
    '001111010011010' => 0,
    '0011110100110110' => 0,
    '0011110100110111' => 0,
    '00111101100001010' => 0,
    '001111011000011010' => 0,
    '001111011000011011' => 0,
    '0011110110000111' => 0,
    '001111011000100001' => 0,
    '00111101100010011' => 0,
    '001111011000101000' => 0,
    '001111011000101001' => 0,
    '001111011000101010' => 0,
    '001111011000101110' => 0,
    '001111011001010' => 0,
    '0011110110011100' => 0,
    '00111101100111101' => 0,
    '001111011001111100' => 0,
    '001111011010000100' => 0,
    '00111101101000011' => 0,
    '0011110110100010' => 0,
    '0011110110100011' => 0,
    '0011110110100111' => 0,
    '0011110110101000' => 0,
    '0011110110110000' => 0,
    '0011110110110011' => 0,
    '0011110110110101' => 0,
    '0011110110110110' => 0,
    '00111101101111010' => 0,
    '011011100000011' => 0,
    '01101110000100' => 0,
    '011100000100000' => 0,
    '0111000001010' => 0,
    '0111000001011' => 0,
    '011100000110000' => 0,
    '01110000011011011' => 0,
    '0111000001101111' => 0,
    '011100000111101' => 0,
    '0111000010000100' => 0,
    '01110000110000' => 0,
    '01110000111' => 0,
    '0111000100000' => 0,
    '011100010000100' => 0,
    '011100010011100' => 0,
    '0111000100111010' => 0,
    '01110001001110110' => 0,
    '011100011100001' => 0,
    '011100011100100' => 0,
    '01110001110011' => 0,
    '011100011110' => 0,
    '011100101111' => 0,
    '0111001100101110' => 0,
    '011100110011' => 0,
    '011100110101010111' => 0,
    '011101000000001' => 0,
    '0111010001011111' => 0,
    '011101000111010' => 0,
    '0111010100001' => 0,
    '0111011001001' => 0,
    '011101100101000' => 0,
    '0111011011010100' => 0,
    '01110111000001' => 0,
    '0111011100100100' => 0,
    '0111011100100111' => 0,
    '0111011100110' => 0,
    '0111011100111110' => 0,
    '011101110110110' => 0,
    '0111011101110' => 0,
    '011101111010001' => 0,
    '01110111101001' => 0,
    '011101111011' => 0,
    '01110111111110' => 0,
    '011110000000' => 0,
    '0111100001010' => 0,
    '0111100100010' => 0,
    '01111001000110' => 0,
    '0111100100011111' => 0,
    '011110100110000' => 0,
    '0111101010001' => 0,
    '01111010100111' => 0,
    '01111010110000' => 0,
    '01111011000001' => 0,
    '0111101100001' => 0,
    '011110110111' => 0,
    '0111101110000' => 0,
    '01111011100100' => 0,
    '0111101110010100' => 0,
    '0111101110011' => 0,
    '01111011101111' => 0,
    '011111000100000' => 0,
    '01111100010000100' => 0,
    '0111110001000011' => 0,
    '0111110001011000' => 0,
    '01111100010110010' => 0,
    '01111100010110011' => 0,
    '011111000101101' => 0,
    '01111100010111' => 0,
    '0111110010100000' => 0,
    '0111110010100001' => 0,
    '0111110010100010' => 0,
    '0111110010100011' => 0,
    '01111100101001' => 0,
    '0111110100100000' => 0,
    '0111110100100001' => 0,
    '0111110100100010' => 0,
    '01111101001000111' => 0,
    '01111101001001' => 0,
    '0111110100101' => 0,
    '0111110111010011' => 0,
    '110010100010011010001111' => 0,
    '110010100110000000' => 0,
    '110010100110000001000' => 0,
    '110010100110000001001' => 0,
    '110010100110000110' => 0,
    '110010100110000111100' => 0,
    '11001010011000011111' => 0,
    '110010100110001000000' => 0,
    '110010100110001000001' => 0,
    '1100101001100011010' => 0,
    '110010100110001101100' => 0,
    '1100101001100011100' => 0,
    '110010100110001110100' => 0,
    '110010100110001110101' => 0,
    '11001010011000111011' => 0,
    '11001010011000111101' => 0,
    '110010100110001111101' => 0,
    '11001010011000111111' => 0,
    '110010100110011010000' => 0,
    '110010100110011011100' => 0,
    '110010100110011011101' => 0,
    '11001010011001101111' => 0,
    '1100101001101010' => 0,
    '11001010011010110' => 0,
    '1100101001101100' => 0,
    '110010100110111000' => 0,
    '110010100110111001' => 0,
    '1100101001101111100' => 0,
    '110010110101110100001000' => 0,
    '110010110101110101' => 0,
    '110010110101110111' => 0,
    '110100100000110100' => 0,
    '110100100000110101' => 0,
    '11010010000011011' => 0,
    '1101001000001110101' => 0,
    '1101001000001110110' => 0,
    '1101001000001111001' => 0,
    '1101001000001111011' => 0,
    '110100100000111110' => 0,
    '11010010000101010' => 0,
    '1101001000110011' => 0,
    '11010010001101001' => 0,
    '11010010001101010' => 0,
    '11010010001101011' => 0,
    '110100100101001' => 0,
    '110110100000100' => 0,
    '1101101000001010' => 0,
    '1101101000001011' => 0,
    '1101101000001100' => 0,
    '11011010000101011' => 0,
    '110110100001100' => 0,
    '1101101000011011' => 0,
    '11011010001110' => 0,
    '110110100011110' => 0,
    '11011010010000111' => 0,
    '11011010011010000' => 0,
    '1101101001101000100' => 0,
    '1101101001101000101' => 0,
    '110110100110100011000' => 0,
    '110110100110100011001' => 0,
    '11011010011010001101' => 0,
    '1101101001101000111' => 0,
    '1101101001101001' => 0,
    '110110100110101' => 0,
    '110110111001101' => 0,
    '110110111001110' => 0,
    '11011011100111100' => 0,
    '11011011100111101' => 0,
    '110110111001111100' => 0,
    '11011100111110' => 0,
    '1101110011111100' => 0,
    '110111010000000' => 0,
    '1101110100000010' => 0,
    '11011101000000110' => 0,
    '11011101000000111' => 0,
    '1101110100000100' => 0,
    '11011101000001010' => 0,
    '11011101000001011' => 0,
    '1101110100000110' => 0,
    '1101110100000111000' => 0,
    '1101110100000111001' => 0,
    '1101110100000111010' => 0,
    '1101110100000111011' => 0,
    '11011101000001111' => 0,
    '1101110100001010' => 0,
    '110111010000101110' => 0,
    '1101110100001011111' => 0,
    '11011101000011000' => 0,
    '110111010000110010' => 0,
    '110111010000110100' => 0,
    '1101110100001101010' => 0,
    '11011101000011011' => 0,
    '110111010000111' => 0,
    '110111011100000' => 0,
    '1101110111000010' => 0,
    '1101110111000011' => 0,
    '110111011100010' => 0,
    '1101110111000110' => 0,
    '1101110111000111000' => 0,
    '110111011100011101' => 0,
    '110111011100011110' => 0,
    '11011101110001111100' => 0,
    '1101110111000111111' => 0,
    '11011101110010' => 0,
    '110111011100110' => 0,
    '1101110111001110' => 0,
    '110111011100111100' => 0,
    '110111011100111101' => 0,
    '11011101110011111' => 0,
    '11011101110100' => 0,
    '1101110111010100' => 0,
    '1101110111010101' => 0,
    '1101110111011' => 0,
    '11011110100000' => 0,
    '11011110100001' => 0,
    '1101111010001' => 0,
    '110111101010000' => 0,
    '1101111010100010' => 0,
    '1101111010100011000' => 0,
    '1101111010100011001' => 0,
    '110111101010001101' => 0,
    );

    $ipAry = explode('.', $ip);
    $bin = '';
    foreach($ipAry as $part) {
        $bin .= self::dec2bin($part);
    }

    $cncMaskAry = array (
    0 => 11,
    1 => 12,
    2 => 13,
    3 => 14,
    4 => 15,
    5 => 16,
    6 => 17,
    7 => 18,
    8 => 19,
    9 => 20,
    10 => 21,
    11 => 24,
    );
    foreach($cncMaskAry as $mask) {
        $net = substr($bin, 0, $mask);
        if(isset($cncNetworkData[$net])) {
            return true;
        }
    }
    return false;
}

/**
     * 仅适用于 $dec <= 255 的场合
     */
public static function dec2bin($dec) {
    $r = decbin((int)$dec);
    $n = 8 - strlen($r);
    for($i = 0; $i < $n; $i++) {
        $r = '0' . $r;
    }
    return $r;
}

/**
     * 得到有效的搜索词
     */
public static function getValidSearchKeyword($keyword, $method='AND') {
    $sep = (strtoupper($method) === 'AND') ? ' AND ' : ' ';
    return $keyword ? urlencode(join($sep, array_unique(Util::explode(' ', str_replace('　', ' ', $keyword))))) : '';
}
/**
     * 精确截取字符串
     *
     * @param unknown_type $Str
     * @param unknown_type $Length
     * @return unknown
     * @since 10090519
     * @author zhangyun
     */
public static function ourSubstr($Str, $Length) {//$Str为截取字符串，$Length为需要截取的长度
    //global $s;
    $i = 0;
    $l = 0;
    $ll = strlen($Str);
    $s = $Str;
    $f = true;

    while ($i <= $ll) {
        if (ord($Str{$i}) < 0x80) {
            $l++; $i++;
        } else if (ord($Str{$i}) < 0xe0) {
            $l++; $i += 2;
        } else if (ord($Str{$i}) < 0xf0) {
            $l += 2; $i += 3;
        } else if (ord($Str{$i}) < 0xf8) {
            $l += 1; $i += 4;
        } else if (ord($Str{$i}) < 0xfc) {
            $l += 1; $i += 5;
        } else if (ord($Str{$i}) < 0xfe) {
            $l += 1; $i += 6;
        }

        if (($l >= $Length - 1) && $f) {
            $s = substr($Str, 0, $i);
            $f = false;
        }

        /* if (($l > $Length) && ($i < $ll)) {
        $s = $s . '...'; break; //如果进行了截取，字符串末尾加省略符号“...”
        } */
    }
    return $s;
}

public static function isUtf8($string) {
    $pattern = '%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs';
    return preg_match($pattern, $string);
}

public static function outputWapHtml( $str, $charset='utf-8' ) {
    self::outputExpireHeader(-86400);
    echo <<<html
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
</head>
<body>
{$str}
</body>
</html>
html;
}

public static function getDocumentRoot() {
    return isset($_SERVER['DOCUMENT_ROOT'])
    ? $_SERVER['DOCUMENT_ROOT']
    : str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['PHP_SELF']) ) );
}

public static function APHash( $str ){
    $hash = 0;
    $n = strlen($str);
    for ($i = 0; $i <$n; $i++){
        if (($i & 1 ) == 0 ){
            $hash ^= (($hash <<7 ) ^ ord($str[$i]) ^ ($hash>> 3 ));
        }else{
            $hash ^= ( ~ (($hash <<11 ) ^ ord($str[$i]) ^ ($hash>> 5)));
        }
    }
    return abs($hash % 701819);
}

public static function getTimeOver($timeLast, $timeNext=0) {
    if (!$timeNext) {
        $timeNext = time();
    }
    if ($timeLast === false || $timeNext === false || $timeLast > $timeNext) {
        return "时间异常";
    }

    $iAll = (int)(($timeNext - $timeLast) / 60);

    if ($iAll < 60) {
    	$iAll = $iAll==0?1:$iAll;
        return "{$iAll} 分钟前";
    }
    $hAll = (int)($iAll / 60);
    if ($hAll < 24) {
        return "{$hAll} 小时前";
    }
    $dAll = (int)($hAll / 24);
    if ($dAll < 30) {
        return "{$dAll} 天前";
    }
    if ($dAll < 365) {
        $m = (int)($dAll / 30);
        return "{$m} 月前";
    }
    return date('Y-m-d', $timeLast);
}

//创建目录
public static function makeDir( $dirPath ){
    $dirArr=(split("/",$dirPath));
    $path="/";
    foreach($dirArr as $dir){
        if("" != $dir){
            $path.="$dir/";
            if(!file_exists($path)){
                mkdir($path);
                chmod($path,0777);
            }
        }
    }
}

/**
     * 写入文件
     *
     * @param string $fileName
     * @param string $content
     * return null
     */
public function writeFile( $fileName, $content ){
    $fileHandle = fopen ($fileName, "w");
    fwrite($fileHandle,$content);
    fclose($fileHandle);
    @chmod($fileName,0777);
}

/**
     * 追加写入文件
     *
     * @param string $fileName
     * @param string $content
     * return null
     */
public function appendFile( $fileName, $content ){
    $fileHandle = fopen ($fileName, "a+");
    fwrite($fileHandle,$content);
    fclose($fileHandle);
    @chmod($fileName,0777);
}

/**
 * 利用file_get_contents获取url文件内容
 *
 * @param string $url
 * @param int $timeout
 * @param string $logfile
 * @return string or array
 */
public static function getFileContent( $url, $timeout = 2, $logfile = '' ){
    if ( !empty($url) ) {
        ini_set('default_socket_timeout', $timeout );
        $result     = @file_get_contents( $url );
        if ( empty($result) ) {
            $result = @file_get_contents( $url );
        }

        if( !empty($logfile) || empty($result) ){
            @file_put_contents("/tmp/".$logfile.'_search_time.log.' . date('Y-m-d'), "{$url} " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        }

        return $result;
    }
    return false;
}

public static function starling($type,$id,$action){
	$m=new Memcache();
	$m->addServer('60.28.199.205', '55555');
	//$m->set('static_page',json_encode(array('type'=>$type,'id'=>$id,'action'=>$action)));
	$m->set('static_page_new', json_encode(array('type'=>$type,'id'=>$id,'action'=>$action)));
	return json_encode(array('type'=>$type,'id'=>$id,'action'=>$action));
}

public static function starling_trigger($type,$id,$action){
	$m=new Memcache();
	$m->addServer('60.28.199.205', '55555');
	$m->set('fast_queue',json_encode(array('type'=>$type,'id'=>$id,'action'=>$action)));
	return true;
}

public static function starling_fast($id){
	$m=new Memcache();
	$m->addServer('60.28.199.205','55555');
	return $m->set('index_starling', $id);
}

public static function getStarlingList($key){
	$m=new Memcache();
	$m->addServer('60.28.199.205', '55555');
	while ($value = $m->get($key)) {
		$arr[] = $value;
	}
	return $arr;
}

public static function starling_vrs($type,$id,$action){
	$m=new Memcache();
	$m->addServer('60.28.199.205', '55555');
	$m->set('static_page_vrs',json_encode(array('type'=>$type,'id'=>$id,'action'=>$action)));
	return true;
}
}
?>
