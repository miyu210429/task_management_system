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
 * ページャーの数字の取得と条件（五ページ分まで表示）
 * $maxmax_pageが５以上の場合と、そうでない場合のパターンがある
 * 
 *
 * @param  int $current_page //現在のページ
 * @param  int $maxmax_page //最大ページ
 * @return array
 */
function countPage(int $current_page, int $maxmax_page) :array {
    
    if($maxmax_page < 5) { //$maxmax_pageが五より小さい場合は、ページャーの表示を$maxmax_pageまでにする
        for($i = 1; $i <= $maxmax_page; $i++){
            $return[] = $i;
        }
        return $return;
    }
    if($current_page === 1) { //現在のページから先の五件
        $return =[$current_page,$current_page+1,$current_page+2,$current_page+3,$current_page+4]; 
    } elseif($current_page === 2) { //現在のページから四件と一つ前のページ
        $return =[$current_page-1,$current_page,$current_page+1,$current_page+2,$current_page+3]; 
    } elseif($current_page == $maxmax_page-1) { //最終ページと現在のページから前に四件
        $return =[$current_page-3,$current_page-2,$current_page-1,$current_page,$current_page+1]; 
    } elseif($current_page == $maxmax_page) { //最終ページから前に五件
        $return =[$current_page-4,$current_page-3,$current_page-2,$current_page-1,$current_page]; 
    } else { //現在のページと前後二件
        $return =[$current_page-2,$current_page-1,$current_page,$current_page+1,$current_page+2]; 
    }
    return $return;
}


/**
 * タスクの最大ページ数、現在のページ、データベースからSELECTする際に必要なタスクのスタートとなる数字を取得
 * URLに表記されたページが存在するものかチェック
 *
 * @param  string $request_page
 * @param  int $tasks
 * @return array
 */
function getPageCount(?string $request_page, int $tasks_count) :array{
    if (isset($request_page)) {
        $page = (int) $request_page;
    } else {
        $page = 1;
    }
    $page = max($page, 1);
    $maxPage = ceil($tasks_count / 5);
    $page = min($page, $maxPage);

    if (isset($request_page) && $request_page < $page || isset($request_page) && $request_page > $maxPage || !isset($request_page)) {
        $page = 1;
    }
    $start = ($page - 1) * 5;

    $return['current_page'] = $page;
    $return['maxPage'] = $maxPage;
    $return['start'] = $start;

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