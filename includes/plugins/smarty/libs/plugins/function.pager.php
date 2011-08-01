<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {pager} function plugin
 *
 * Type:     function
 * Name:     pager
 * Purpose:  generate page list
 *
 * @author  Lance Lee
 * @param array
 * @param Smarty
 * @return string
 */
/**
 *  使用方法 {/pager start=$start limit=$limit total=$total baseUrl=$baseUrl/}
 * 四个必须参数 start, limit, total, baseUrl
 * 五个可选参数
 *  suffix 后缀(比如 .html)
 *  pageBlock 每页显示多少个分页链接
 *  showLastPage 是否显示尾页, 默认为真
 *  firstPageText 默认 首
 *  lastPageText 默认 尾
 */
function smarty_function_pager($params, &$smarty) {
    if ( !function_exists('show_link') ) {
        function show_link($url, $text, $showlink=true) {
            $class = $showlink ? '' : ' class="on" ';
            return <<<html
<a href="{$url}"{$class}>{$text}</a>
html;
        }
    }

    foreach ($params as $key=>$value) {
        $$key = $value;
//            case 'style':
//            case 'parentLink':
//            case 'pageExtra':
    }


    if(!isset($suffix)) $suffix = '';
    if(!isset($firstPageText)) $firstPageText = '首页';
    if(!isset($lastPageText))  $lastPageText  = '末页';
    if(!isset($limit)) $limit = 20;
    if(!isset($parentLink)) $parentLink = '';
    if(!isset($pageBlock)) $pageBlock = 5;
    if(!isset($style)) $style = 3;
    #设定是否显示首末页
    if(!isset($showLastPage)) $showLastPage = 0;
    if(!isset($page_extra)) $page_extra = '';
    /**
     * add by zhangyun 20090520 add case 4,for wap
     */
    #设定是否显示上一页下一页
    if(!isset($showNextPage)) $showNextPage = 0;
    #设定是否需要换行  上下页和其他之间
    if(!isset($needBr)) $needBr = 0;
    #是否显示上n页下n页
    if(!isset($showBlockPage)) $showBlockPage = 0;
    #设定几页一翻页
    if(!isset($nextBlockNum)) $nextBlockNum = 5;
    #设定下一段的文字
    if(!isset($nextBlockText)) $nextBlockText = '&gt;&gt;';
     #设定上一段的文字
    if(!isset($perBlockText)) $perBlockText = '&lt;&lt;';
    #上下页的文字
    if(!isset($perPageText))  $perPageText = '上一页';
    if(!isset($nextPageText)) $nextPageText = '下一页';


    if (!isset($total) || !isset($start) || !isset($baseUrl)) {
        $smarty->trigger_error("attribute 'total', 'start', 'base_url' required");
        return false;
    }

    $page_total = ceil($total/$limit);
    if ($page_total <= 1) {
        return '';
    }
    $current_page = (int)($start/$limit) + 1;

    $ret = '';

    //首页
    $ret_first = $current_page > $pageBlock ? show_link($baseUrl.'1'.$suffix, $firstPageText) : '';
    //末页
    $url = $baseUrl . $page_total . $page_extra . $suffix;
    $ret_end = $current_page < $page_total ? show_link($url, $lastPageText) : '';

    switch ( $style ) {
        case 2:
            //上一页
            if ( $current_page == 2 ){
                $tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1';
            } else {
                $tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
            }
            $ret_pre = $current_page > 1 ? show_link( $tlink, $firstPageText ) : '' ;

            //下一页
            $ret_next = $current_page < $page_total ? show_link( $baseUrl.$page_extra.($current_page + 1).$suffix, '下一页' ) : '' ;

            //当前显示段
            $start = $current_page - floor( ($pageBlock-1)/2 );
            $end = $current_page + floor( ($pageBlock+1)/2 );
            if( $start < 1 )
            {
                $start = 1 ;
                $end = ( $pageBlock <= $page_total ) ? $pageBlock : $page_total ;
            }
            elseif ($end > $page_total)
            {
                $end = $page_total ;
                $start = ( $page_total - $pageBlock >= 0 ) ? $page_total - $pageBlock + 1 : 1 ;
            }

            //当前段
            $ret_middle = '' ;
            for( $i=$start ; $i<=$end ; $i++ )
            {
                if ( $i == 1 ) $tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1';
                else $tlink = $baseUrl.$page_extra.$i.$suffix;
                $ret_middle .= show_link( $tlink, $i, ( $i==$current_page ) ? false : true ) ;
            }

            //显示
            $ret = $ret_first . $ret_pre . $ret_middle . $ret_next;
            if ($showLastPage) $ret .= $ret_end;
            return $ret;

        case 1:
        case 3:
            if ( !$current_page == 2 )
            {
                $tlink = ( $parentLink ) ? $parentLink : $baseUrl;
            } else {
                $tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
            }
            $ret_pre = $current_page > 1 ? show_link( $tlink, $firstPageText ) : '' ;

            //下一页
            $ret_next = $current_page < $page_total ? show_link( $baseUrl.$page_extra.($current_page + 1).$suffix, '????' ) : '' ;

            //当前显示段
            $start = $current_page - $pageBlock ;
            $end = $current_page + $pageBlock ;
            $total = floor($page_total);

            if( $start < 1 )
            {
                $start = 1 ;
                $end = ( $current_page + $pageBlock <= $page_total ) ? $end : $page_total ;
            }
            elseif ($end > $page_total)
            {
                $end = $page_total ;
                //$start = ( $current_page - $pageBlock >= 0 ) ? $current_page - $pageBlock + 1 : 1 ;
            }
            //当前段
            $ret_middle = '' ;
            for( $i=$start ; $i<=$end ; $i++ )
            {
                if ( $i == 1 ) $tlink = ( $parentLink ) ? $parentLink.$suffix : $baseUrl.'1'.$suffix;
                else $tlink = $baseUrl.$page_extra.$i.$suffix;
                $ret_middle .= show_link( $tlink, $i, ( $i==$current_page ) ? false : true ) ;
            }

            //显示
            //$ret = $ret_first . $ret_pre . $ret_middle . $ret_next;
            $ret = $ret_first .  $ret_middle ;
            if ($showLastPage) $ret .= $ret_end;
            return $ret;
        case 4:
        	#wap显示方式
            #计算上一页下一页的现实
            if ( !$current_page == 2 )
            {
                $tlink = ( $parentLink ) ? $parentLink : $baseUrl;
            } else {
                $tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
            }
            $ret_pre = $current_page > 1 ? show_link( $tlink, $perPageText ) : '' ;

            //下一页
            $ret_next = $current_page < $page_total ? show_link( $baseUrl.$page_extra.($current_page + 1).$suffix, $nextPageText ) : '' ;

            #处理  上n页，下n页
            $retPreBlock = '';
            $retNextBlock = '';
            if($current_page > 1){
            	$numTemp = ($current_page - $nextBlockNum > 0) ? ($current_page - $nextBlockNum) : 1;
                $link = $baseUrl.$page_extra.($numTemp).$suffix;
                $retPreBlock = show_link($link, $perBlockText);
            }
            if($page_total - $nextBlockNum > 0){
                $numTemp = ($current_page + $nextBlockNum < $page_total) ? ($current_page + $nextBlockNum) : $page_total;
            	$link = $baseUrl.$page_extra.($numTemp).$suffix;
                $retNextBlock = show_link($link, $nextBlockText);
            }

            //当前显示段
            $start = $current_page - $pageBlock ;
            $end = $current_page + $pageBlock ;
            $total = floor($page_total);
            if( $start < 1 )
            {
                //$start = 1 ;
                $end = ( $current_page + $pageBlock <= $page_total ) ? $end : $page_total ;
            }
            elseif ($end > $page_total)
            {
                $end = $page_total ;
                //$start = ( $current_page - $pageBlock >= 0 ) ? $current_page - $pageBlock + 1 : 1 ;
            }
            //当前段
            $ret_middle = '' ;
            for( $i=$current_page ; $i<=$end ; $i++ )
            {
                if ( $i == 1 ) $tlink = ( $parentLink ) ? $parentLink.$suffix : $baseUrl.'1'.$suffix;
                else $tlink = $baseUrl.$page_extra.$i.$suffix;
                $ret_middle .= show_link( $tlink, $i, ( $i==$current_page ) ? false : true ) ;
            }

            //显示
            //$ret = $ret_first . $ret_pre . $ret_middle . $ret_next;
            $ret =   $ret_middle ;
            if ($showLastPage) $ret = $ret_first . $ret . $ret_end;
            if($showBlockPage) $ret = $retPreBlock . $ret . $retNextBlock;
            if($needBr) $ret = "<br/>" . $ret;
            if($showNextPage) $ret = $ret_pre . $ret_next . $ret;
            return $ret;

        case 5:
            //上一页
            if ( $current_page == 2 ){
                $tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1'.$suffix;
            } else {
                $tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
            }
            $ret_pre = $current_page > 1 ? show_link( $tlink, $perPageText ) : '';

            //下一页
            $ret_next = $current_page < $page_total ? show_link( $baseUrl.$page_extra.($current_page + 1).$suffix, '下一页' ) : '' ;

            //当前显示段
            $start = $current_page - floor( ($pageBlock-1)/2 );
            $end = $current_page + floor( $pageBlock/2 );
            if( $start < 1 )
            {
                $start = 1 ;
                $end = ( $pageBlock <= $page_total ) ? $pageBlock : $page_total ;
            }
            elseif ($end > $page_total)
            {
                $end = $page_total ;
                $start = ( $page_total - $pageBlock >= 0 ) ? $page_total - $pageBlock + 1 : 1 ;
            }

            //当前段
            $ret_middle = '' ;
            for( $i=$start ; $i<=$end ; $i++ )
            {
                if ( $i == 1 ) $tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1'.$suffix;
                else $tlink = $baseUrl.$page_extra.$i.$suffix;
                $ret_middle .= show_link( $tlink, $i, ( $i==$current_page ) ? false : true ) ;
            }
            //显示
            if($current_page>=5){
            	$ret_first =  show_link($baseUrl.'1'.$suffix, "1") ;

            }else{
            	$ret_first = "";
            }
             if($current_page>=6){
            	$ret_sec = show_link($baseUrl.'2'.$suffix, "2");
            }else{
				$ret_sec="";
            }
            if($current_page>6){
            	$afir = "<a href='javascript:void(0)'>...</a>";
            }else{
            	$afir = "";
            }
            if(($current_page+5)<$page_total){
            	$bfir = "<a href='javascript:void(0)'>...</a>";
            }else{
            	$bfir = "";
            }
            if(($current_page+3)<$page_total){
            	$ret_end =  show_link($url, $page_total);
            }else{
            	$ret_end = "";
            }
            $a = $page_total-1;
            if(($current_page+4)<$page_total){
            	$ret_endnext =  show_link($baseUrl.$a.$suffix,$a);;
            }else{
            	$ret_endnext = "";
            }
            $ret =  $ret_pre .$ret_first.$ret_sec.$afir. $ret_middle .$bfir.$ret_endnext.$ret_end. $ret_next;
            if ($showLastPage) $ret;
            return $ret;

         default:

            //计算分段变量
            $block_num = ceil($page_total / $pageBlock);
            $block_current = ceil( $current_page / $pageBlock );
            $block_start = ( $block_current - 1 ) * $pageBlock + 1;
            $block_end = $block_current * $pageBlock;
            if ( $block_end>$page_total ) $block_end = $page_total;

            //上一段
            $ret_preBlock = $block_start>1 ? show_link( $baseUrl.($block_start-1).$suffix, '上' . $pageBlock . '页' ) : '';

            //当前段
            $ret_middle = '' ;
            for ( $i=$block_start; $i<=$block_end; $i++ )
            {
                if ( $i == 1 ) $tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1';
                else $tlink = $baseUrl.$i.$suffix;
                $ret_middle .= show_link( $tlink, $i, $i==$current_page ? false : true ) ;
            }

            //下一段
            $ret_nextBlock = $block_end<$page_total ? show_link( $baseUrl.($block_end+1).$suffix, '下' . $pageBlock . '页' ) : '';

            $ret = $ret_first.$ret_preBlock.$ret_middle.$ret_nextBlock;
            if ( $showLastPage ) $ret .= $ret_end;
            return $ret;
    }

}

/* vim: set expandtab: */
