<?php

/**
 * HTMLエスケープ（XSS対策）
 * @param string|null $string エスケープする文字列
 * @return string エスケープ後の文字列
 */
function h(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}


?>