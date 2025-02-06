<?php

//バリデーションルール用のファイル読み込み
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class User {
    private $User;

    // is_privilegedの対応
    public const PRIVILEGE_NORMAL = 0; // 通常ユーザー
    public const PRIVILEGE_ADMIN = 1;  // 特権ユーザー

    // is_deletedの対応
    public const DELETED_NO = 0;  // 未削除
    public const DELETED_YES = 1; // 削除済み

    //is_privilegedのラベル
    public static array $privilegeLabels = [
        self::PRIVILEGE_NORMAL => '通常ユーザー',
        self::PRIVILEGE_ADMIN => '特権ユーザー',
    ];

    //is_deletedのラベル
    public static array $deletedLabels = [
        self::DELETED_NO => '未削除',
        self::DELETED_YES => '削除済み',
    ];

    /**
     * このクラス内でデータベース接続できるように
     * 
     * 今回のプロジェクトでは
     * クラス内でのDB接続用のオブジェクト(pdoオブジェクト)は
     * $this->クラス名で固定させるルールでやりましょう（このクラスはUserなので$this->User）
     */
    public function __construct() {
        $this->User = Database::getInstance()->getConnection();
    }

    /**
     * アカウント一覧ページ用
     * 全ページのデータを取得
     * get_deleted_userがtrueの場合は削除済みのユーザー情報も取得する
     * (引数が渡されなかった場合はデフォルトで削除済みユーザーは取得しない)
     * 
     * @param bool $get_deleted_user 削除済みユーザーを取得するかどうか
     * @return array
     */
    public function getAllUsers(bool $get_deleted_user = false): array {
        $query = "SELECT * FROM users";
        if(!$get_deleted_user){
            $query .= " WHERE is_deleted = 0"; //WHERE の前に空白スペース入れないとエラーになる
        }
        $query .= " ORDER BY id DESC";
        $stmt = $this->User->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * userのidからusersテーブルの全データを取得する
     *
     * @param  mixed $login_id
     * @return bool | array
     */
    public function getById(int $login_id) : bool | array {
        $query_id = $this->User->prepare("SELECT * FROM users WHERE id=?");
        $query_id->execute(array(
            $login_id
        ));
       return $query_id->fetch();
    }   

    public function deleteUser(int $delete_id) : bool | array {
    $array = $this->User->prepare('UPDATE users SET is_deleted = 1 WHERE id=?');
    $array->execute(array(
        $delete_id
    ));
    return $array->fetch();
    }

    /**
     * ログイン処理
     * 渡されたlogin_nameとpasswordを使ってデータを取得
     * パスワードはconfig.phpに記載してあるUSERS_PASSWORD_SALTを引っ付けて
     * sha1でhash化
     * 
     * @param string $login_name 入力されたログイン名
     * @param string $password　入力されたパスワード
     * @return bool|array データ取得できたらそのデータを配列で、失敗したらfalseを返す 
     */
    public function login(string $login_name , string $password): bool|array {
        $login = $this->User->prepare('SELECT * FROM users WHERE login_name=? AND password=?');
        $login->execute(
            array(
                $login_name,
                sha1($password.USERS_PASSWORD_SALT)
            )
         );
        return $login->fetch();
    }





        
    /**
     * ログイン名のバリデーション（3～12文字、英数字と記号のみ）
     *
     * @param  mixed $login_name
     * @return bool|string
     */
    public function validateLoginName(string $login_name): bool|string {
        if (!ValidationHelper::validateLength($login_name, 3, 12)) {
            return "ログイン名は3文字以上12文字以下にしてください。";
        }

        if (!ValidationHelper::validateAlphaNumSymbols($login_name)) {
            return "ログイン名は英数字と記号のみ使用できます。";
        }

        return true;
    }

        
    /**
     * パスワードのバリデーション（6～20文字）
     *
     * @param  mixed $password
     * @return bool
     */
    public function validatePassword(string $password): bool|string {
        if (!ValidationHelper::validateLength($password, 6, 20)) {
            return "パスワードは6文字以上20文字以下にしてください。";
        }

        return true;
    }

        
    /**
     * ログインフォーム用バリデーション
     *
     * @param  mixed $login_name
     * @param  mixed $password
     * @return array
     */
    public function validateLogin(string $login_name, string $password): array {
        $errors = [];

        $loginValidation = self::validateLoginName($login_name);
        if ($loginValidation !== true) {
            $errors['login_name'] = $loginValidation;
        }

        $passwordValidation = self::validatePassword($password);
        if ($passwordValidation !== true) {
            $errors['password'] = $passwordValidation;
        }

        return $errors;
    }

}