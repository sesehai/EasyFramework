<?php
class UtilsLib{
	/**
	 * 数据格式化：
	 * print_r($data);
	 * @param mix $data json or array
	 * @param string $type 数据类型：'array' or 'json'
	 * @return void
	 */
	public static function rdump($data,$type){
		echo '<pre>';
		if($type == 'array'){
			print_r($data);
		}
		if($type == 'json'){
			print_r(json_decode($data,true));
		}
		echo '</pre>';
	}

	/**
	 * 使用方法 {/pager start=$start limit=$limit total=$total baseUrl=$baseUrl/}
	 * 四个必须参数 start, limit, total, baseUrl
	 * 五个可选参数
	 *  suffix 后缀(比如 .html)
	 *  pageBlock 每页显示多少个分页链接
	 *  showLastPage 是否显示尾页, 默认为真
	 *  firstPageText 默认 首
	 *  lastPageText 默认 尾
	 */
	public static function pager($params) {
		if ( !function_exists('show_link') ) {
			function show_link($url, $text, $showlink=true) {
				return $showlink ? "<a href='{$url}'>{$text}</a>" : " <span class='current'>{$text}</span>";
			}
		}
		foreach ($params as $key=>$value) {
			$$key = $value;
		}
		if(!isset($suffix)) $suffix = '';
		if(!isset($firstPageText)) $firstPageText = '首页';
		if(!isset($lastPageText))  $lastPageText  = '末页';
		if(!isset($limit)) $limit = 20;
		if(!isset($parentLink)) $parentLink = '';
		if(!isset($pageBlock)) $pageBlock = 5;
		if(!isset($style)) $style = 3;
		#设定是否显示首末页
		if(!isset($showFirstLastPage)) $showFirstLastPage = 0;
		if(!isset($page_extra)) $page_extra = '';
		#设定是否显示上一页下一页
		if(!isset($showNextPage)) $showNextPage = 0;
		#设定是否需要换行  上下页和其他
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
		if(!isset($perPageText))  $perPageText = '上页';
		if(!isset($nextPageText)) $nextPageText = '下页';

		if (!isset($total) || !isset($start) || !isset($baseUrl)) {
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
			case 1:
				/**
				 * 上一页
				 */
				$ret = '';
				if ( $current_page == 2 ){
					$tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1'.$suffix;
				} else {
					$tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
				}
				$ret_pre = $current_page > 1 ? "<span class='next'><a href='".$baseUrl.$page_extra.($current_page - 1).$suffix."' title='上一页'>上一页</a></span>" : "<span class='next end'><a href='javascript:void(0);'>上一页</a></span>" ;

				//下一页
				$ret_next = $current_page < $page_total ? "<span class='next'><a href='".$baseUrl.$page_extra.($current_page + 1).$suffix."' title='下一页'>下一页</a></span>" : '<span class="next end"><a href="javascript:void(0);">下一页</a></span>' ;
				$ret .= $ret_next."</span><small>".$current_page."/".$page_total."</small>".$ret_pre;
				return $ret;
				 
			case 2:
				//上一页
				if ( $current_page == 2 ){
					$tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1'.$suffix;
				} else {
					$tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
				}
				$ret_pre = $current_page > 1 ? show_link( $tlink, $perPageText ) : "<span class='disabled'>上页</span>" ;

				//下一页
				$ret_next = $current_page < $page_total ? show_link( $baseUrl.$page_extra.($current_page + 1).$suffix, '下页' ) : '<span class="disabled">下页</span>' ;

				//当前显示段
				$start = $current_page - floor( ($pageBlock-1)/2 );
				$end = $current_page + floor( ($pageBlock-1)/2 );
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
				if ($showFirstLastPage) {
					$ret .= $ret_first;
				}
				$ret = '<span class="page">'.$ret_pre . $ret_middle . $ret_next.'</span>';
				if ($showFirstLastPage) {
					$ret .= $ret_end;
				}
				return $ret;
				
			case 3:
				//上一页
				if ( $current_page == 2 ){
					$tlink = ( $parentLink ) ? $parentLink : $baseUrl.'1'.$suffix;
				} else {
					$tlink = $baseUrl.$page_extra.($current_page - 1).$suffix;
				}
				$ret_pre = $current_page > 1 ? show_link( $tlink, $perPageText ) : "<span class='disabled'>上页</span>" ;

				//下一页
				$ret_next = $current_page < $page_total ? show_link( $baseUrl.$page_extra.($current_page + 1).$suffix, '下页' ) : '<span class="disabled">下页</span>' ;

				//当前显示段
				$start = $current_page - floor( ($pageBlock-1)/2 );
				$end = $current_page + floor( ($pageBlock-1)/2 );
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
				if ($showFirstLastPage) {
					$ret .= $ret_first;
				}
				$ret_jump = '第 <input type="text" style="width:40px;" name="pnum" id="pnum" value="'.$current_page.'" > 页 <input type="button" value="跳转" onclick="javascript:num=document.getElementById(\'pnum\').value;window.location=\''.$baseUrl.$page_extra.'\'+num+\''.$suffix.'\'"> ';
				$ret = '<span class="page">'.$ret_jump.$ret_pre . $ret_middle . $ret_next.'</span>';
				if ($showFirstLastPage) {
					$ret .= $ret_end;
				}
				return $ret;

		}

	}
}
?>