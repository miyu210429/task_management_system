<?php

spl_autoload_register(function ($class) {
    // クラスのパスを作成（クラス名 → ファイルパスに変換）
    $file = __DIR__ . "/classes/" . $class . ".php";

    // ファイルが存在する場合のみ読み込む
    if (file_exists($file)) {
        require_once $file;
    }
});