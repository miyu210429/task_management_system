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
 * ページャーの数字の取得と条件（5ページ分まで表示）
 * display_pagesページに満たない場合は最大ページ数分を
 * display_pagesページに達している場合1~display_pagesページであれば1~display_pagesまでは
 * そうでなければ$current_pageを中心にdisplay_pagesをベースに前後の数値足した分を（display_pages5であれば2ページずつ）
 * current_pageがページャーの後半部分になる場合（currentが中央にこない場合）は最後のページから表示ページ数分を取得する
 * 
 * @param  int $current_page //現在のページ
 * @param  int $maxmax_page //最大ページ
 * @param  int $display_pages //ページャーで表示するページ数デフォルト5 表示上の問題で奇数しか認めない
 * @return array
 */
function getPaginationRange(int $current_page,int $max_page,int $display_pages = 5): array  {
    $range = [];

    // 表示ページ数が偶数の場合は+1して奇数にする（現在ページを中央に配置するため）
    if ($display_pages % 2 === 0) {
        $display_pages++;
    }

    // 範囲の前後ページ数を計算
    $half_range = floor($display_pages / 2);

    if ($max_page <= $display_pages) {
        // 最大ページ数が表示ページ数以下ならすべて表示
        $range = range(1, $max_page);
    } elseif ($current_page <= $half_range + 1) {
        // 現在ページが前半部分（中央に寄せると最初のページを超える場合）
        $range = range(1, $display_pages);
    } elseif ($current_page >= $max_page - $half_range) {
        // 現在ページが後半部分（中央に寄せると最後のページを超える場合）
        $range = range($max_page - $display_pages + 1, $max_page);
    } else {
        // 現在ページを中心に表示
        $range = range($current_page - $half_range, $current_page + $half_range);
    }

    return $range;
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
    $maxPage = ceil($tasks_count / 10);
    $page = min($page, $maxPage);
    $maxPage == 0 ? $maxPage=1 : $maxPage=$maxPage;

    if (isset($request_page) && $request_page < $page || isset($request_page) && $request_page > $maxPage || !isset($request_page)) {
        $page = 1;
    }
    $start = ($page - 1) * 10;

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


/**
 * 現在の時間に応じて挨拶を返す
 *
 * @return string
 */
function get_greeting() :string {
    $hour = (int) date('G'); // 24時間表記（0〜23）

    if ($hour >= 5 && $hour < 11) {
        return "おはようございます";
    } elseif ($hour >= 11 && $hour < 18) {
        return "こんにちは";
    } elseif ($hour >= 18 && $hour < 23) {
        return "こんばんは";
    } else {
        return "早く寝よ？";
    }
}


?>