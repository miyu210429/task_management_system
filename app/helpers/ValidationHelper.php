<?php

/**
 * 入力項目のバリデーションで共通化できそうなルールを書き出すためのクラス
 * 
 * バリデーションクラスのメソッドはインスタンス化せずに利用できるようstaticをつけておく
 * （シングルトンで呼び出すこと想定）
 */
class ValidationHelper {
    /**
     * 文字数のバリデーション
     * @param string $value チェックする値
     * @param int $min 最小文字数
     * @param int $max 最大文字数
     * @return bool
     */
    public static function validateLength(string $value, int $min, int $max): bool {
        $length = mb_strlen($value);
        return $length >= $min && $length <= $max;
    }

    /**
     * 英数字と記号のみのバリデーション
     * @param string $value チェックする値
     * @return bool
     */
    public static function validateAlphaNumSymbols(string $value): bool {
        return preg_match('/^[a-zA-Z0-9!@#$%^&*()_+=\-{}\[\]:;"\'<>,.?\/]+$/', $value);
    }

    /**
     * メールアドレスのバリデーション
     * @param string $email チェックするメールアドレス
     * @return bool
     * 
     * return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
     * みたいな書き方もできるが、FILTER_VALIDATE_EMAILでのチェックは非常に厳密にメールアドレス形式をチェックするので
     * 正規表現で柔軟にチェック
     */
    public static function validateEmail(string $email): bool {
        if(!preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD', $email)){
            return false;
       }
        return true;
       
    }
    
    /**
     * パスワードに３回以上同じ文字が続いていないかをチェック
     *
     * @param  mixed $password_overlap
     * @return bool
     */
    public static function validatePasswordOverlap(string $password_overlap): bool {
        if (preg_match('/(.)\1{2,}/', $password_overlap)) {
            return false;
        }
        return true;
    }


}