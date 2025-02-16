<?php

/**
 * HTMLエスケープ（XSS対策）
 * @param string|null $string エスケープする文字列
 * @return string エスケープ後の文字列
 */
function h(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}


/**
 * ページャーの表示
 *
 * @param  int $current_page //現在のページ
 * @param  int $maxmax_page //最大ページ
 * @return array
 */
function countPage(int $current_page,int $maxmax_page) :array {
    
    if($current_page === 1) {
        $return =[$current_page,$current_page+1,$current_page+2,$current_page+3,$current_page+4]; 
    } elseif($current_page === 2) {
        $return =[$current_page-1,$current_page,$current_page+1,$current_page+2,$current_page+3]; 
    } elseif($current_page == $maxmax_page-1) {
        $return =[$current_page-3,$current_page-2,$current_page-1,$current_page,$current_page+1]; 
    } elseif($current_page == $maxmax_page) {
        $return =[$current_page-4,$current_page-3,$current_page-2,$current_page-1,$current_page]; 
    } else {
        $return =[$current_page-2,$current_page-1,$current_page,$current_page+1,$current_page+2]; 
    }
    return $return;
}



/**
 * URLから?page=と&page=を切り取る
 *
 * @param  string $url
 * @return string|bool
 */
function removeCurrentPage(string $url): string|bool{
    $url = preg_replace('/([&?])page=\d+(&|$)/', '$1', $url);
    return rtrim($url,'?&');
}


?>