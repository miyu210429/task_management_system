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
     * ユーザー情報をデータベースに挿入する
     *
     * @param  array $post_user_info
     * @return bool | array
     */
    public function insert(array $post_user_info): bool|array {
        $created_query = $this->User->prepare(
            "INSERT INTO users SET email=?, login_name=?, nickname=?, password=?,
             is_privileged=?, created_at=NOW(), updated_at=NOW()
            ");
        $created_query->execute(array(
            $post_user_info['email'],
            $post_user_info['login_name'],
            $post_user_info['nickname'],
            sha1($post_user_info['password'].USERS_PASSWORD_SALT),
            $post_user_info['is_privileged']
        ));
        return $created_query->fetch();
    }
    
    /**
     * ユーザー情報をデータベースに更新する
     *
     * @param  array $update_user
     * @return bool | array
     */
    public function update(int $primary_key,array $update_user): bool|array {
        $update_query = $this->User->prepare(
            "UPDATE users SET email=?, nickname=?, is_privileged=?, updated_at=NOW() WHERE id=?"
        );
        $update_query->execute(array(
            $update_user['email'],
            $update_user['nickname'],
            $update_user['is_privileged'],
            $primary_key
        ));
        return $update_query->fetch();
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
     * アカウント一覧の検索システム
     *
     * @param  mixed $params
     * @param  mixed $get_deleted_user
     * @return array
     */
    public function search(array $params, bool $get_deleted_user = false): array {
        // baseとなるSQL。WHERE 1=1 とすることで後続の AND 条件を組みやすくする。
        $query = "SELECT * FROM users WHERE 1=1";
        $bindings = [];
 
        // ログイン名で検索（部分一致）
        if (!empty($params['login_name'])) {
           $query .= " AND login_name LIKE '%".trim($params['login_name']."%'");
        }
 
        // ニックネームで検索（部分一致）
        if (!empty($params['nickname'])) {
            $query .= " AND nickname LIKE '%".trim($params['nickname']."%'");
        }
 
        // 更新日の開始日
        if (!empty($params['updated_at_from'])) {
             $query .= " AND updated_at >= '".trim($params['updated_at_from'])."'";
        }
 
        // 更新日の終了日
        if (!empty($params['updated_at_to'])) {
            $query .= " AND updated_at <= '".trim($params['updated_at_to'])."'";
        }

        //削除済みユーザー取得フラグがtrueでない場合は取得しないように
        if(!$get_deleted_user){
            $query .= " AND is_deleted = 0"; 
        }
 
        //id順になるように
        $query .= " ORDER BY id DESC";

        $stmt = $this->User->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
     
    }
    
    /**
     * userのidからusersテーブルの全データを取得する
     *
     * @param  int $login_id
     * @return bool | array
     */
    public function getById(int $login_id) : bool | array {
        $query_id = $this->User->prepare("SELECT * FROM users WHERE id=?");
        $query_id->execute(array(
            $login_id
        ));
       return $query_id->fetch();
    }   
    
    /**
     * ユーザー情報を削除するためのもの
     * is_deletedカラムを１にすると削除
     *
     * @param  int $delete_id
     * @return bool | array
     */
    public function deleteUser(int $delete_id) : bool | array {
        $delete_query = $this->User->prepare('UPDATE users SET is_deleted = 1 WHERE id=?');
        $delete_query->execute(array(
            $delete_id
        ));
        return $delete_query->fetch();
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
        $stmt = $this->User->prepare('SELECT * FROM users WHERE login_name=? AND password=? AND is_deleted=0');
        $stmt->execute(
            array(
                $login_name,
                sha1($password.USERS_PASSWORD_SALT)
            )
         );
        return $stmt->fetch();
    }
   
    /**
     * メールアドレスでuserstableからデータを取ってくる
     *
     * @param  string $email
     * @return bool | array
     */
    public function getByEmail(string $email) : bool|array {
        $stmt = $this->User->prepare('SELECT * FROM users WHERE email=? AND is_deleted=0');
        $stmt->execute(
            array(
                $email
            )
        );
        return $stmt->fetch();

    }  

        
    /**
     * ログイン名でuserstableからデータを取ってくる 
     * 
     * @param  string $login_name
     * @return bool | array
     */
    public function getByLoginName(string $login_name) : bool|array {
        $stmt = $this->User->prepare('SELECT * FROM users WHERE login_name=? AND is_deleted=0');
        $stmt->execute(
            array(
                $login_name
            )
        );
        return $stmt->fetch();
    }  

        
    /**
     * ニックネームでuserstableからデータを取ってくる
     *
     * @param  string $nickname
     * @return bool | array
     */
    public function getByNickname(string $nickname) : bool|array {
        $stmt = $this->User->prepare('SELECT * FROM users WHERE nickname=? AND is_deleted=0');
        $stmt->execute(
            array(
                $nickname
            )
        );
        return $stmt->fetch();
    }  
    
    /**
     * メールアドレスのバリデーション
     * ユニークかどうかのチェック
     * 
     * @param  string $email
     * @param  string $user_info アップデートのバリデーションで利用する際は編集される人の$emailをいれる
     * @return bool | string
     */
    public function validateEmail(string $email, string $user_info = ''): bool|string {

        if (!ValidationHelper::validateEmail($email)) {
            return "メールアドレスの形式で入力してください。";
        }

        $validate = true;
        if ($email === $user_info) {
            $validate = false;
        } 
        if ($validate && self::getByEmail($email)) {
            return "そのメールアドレスはすでに登録されています。";
        }
        
        
        
        return true;
    }
  
        
    /**
     * ログイン名のバリデーション（3～12文字、英数字と記号のみ）
     * ユニークかどうかのチェック
     *
     * @param  string $login_name
     * @param  string  $user_info アップデートのバリデーションで利用する際は、編集される人のlogin_nameをいれる
     * @return bool|string
     */
    public function validateLoginName(string $login_name, string $user_info = ''): bool|string {

        if (!ValidationHelper::validateLength($login_name, 3, 12)) {
            return "ログイン名は3文字以上12文字以下にしてください。";
        }

        if (!ValidationHelper::validateAlphaNumSymbols($login_name)) {
            return "ログイン名は英数字と記号のみ使用できます。";
        }
            
        $validate = true;
        if ($login_name === $user_info) {
            $validate = false;
        }

        if ($validate && self::getByLoginName($login_name)) {
            return "そのログイン名はすでに登録されています。";
        }
        
        
        return true;
    }

    /**
     * ニックネームのバリデーション　（3~10文字）
     * ユニークかどうかのチェック
     *
     * @param  string $nickname
     * @param  string $user_info アップデートのバリデーションで利用する際は編集される人の$nicknameをいれる
     * @return bool | string
     */
    public function validateNickname(string $nickname, string $user_info = ''): bool|string {
        if (!ValidationHelper::validateLength($nickname, 3, 10)) {
            return "ニックネームは3文字以上10文字以下にしてください。";
        }

        $validate = true;
        if ($nickname === $user_info) {
            $validate = false;    
        }
        if ($validate && self::getByNickname($nickname)) {
            return "そのニックネームはすでに登録されています。";
        }
        
        
         return true;
    }
        
    /**
     * パスワードのバリデーション（6～20文字）
     *
     * @param  string $password
     * @return bool
     */
    public function validatePassword(string $password): bool|string {
        if (!ValidationHelper::validateLength($password, 6, 20)) {
            return "パスワードは6文字以上20文字以下にしてください。";
        }

        if (!ValidationHelper::validatePasswordOverlap($password)){
            return "同じ文字を３回以上連続で使わないでください。";
        }

        return true;
    }

        
    /**
     * インサートフォーム用のバリデーション
     * 空白かどうかは各項目のバリデーションに値をわたさなければ行けない関係でこのメソッド内でチェック
     *
     * @param  array $targetInput
     * @return array
     */
    public function validateInsertInput(array $targetInput): array {
        $errors = [];

        /**
         * emailのvalidation emailが入力されているが空白、もしくは存在しない場合と各種条件を満たしていない場合はエラー情報が入る
         * 条件のどこにも属さなかった場合（elseだった場合）はバリデーション追加
         * 他の項目もすべて同じ感じ
         */ 

        if ((isset($targetInput['email']) && $targetInput['email'] == '') || !isset($targetInput['email'])) {
            $errors['email'] = '入力してください';
        } else if(($emailValidaton = self::validateEmail($targetInput['email'])) !== true){ 
            $errors['email'] = $emailValidaton;          
        }
    


        if ((isset($targetInput['login_name']) && $targetInput['login_name'] == '') || !isset($targetInput['login_name'])) {
            $errors['login_name'] = '入力してください';
        } else if (($LoginNameValidation = self::validateLoginName($targetInput['login_name'])) !== true) {
            $errors['login_name'] = $LoginNameValidation;
        }
    


        if ((isset($targetInput['nickname']) && $targetInput['nickname'] == '') || !isset($targetInput['nickname'])) {
            $errors['nickname'] = '入力してください';
        } else if (($nicknameValidation = self::validateNickname($targetInput['nickname'])) !== true) {
            $errors['nickname'] = $nicknameValidation;
        }
    

        if ((isset($targetInput['password']) && $targetInput['password'] == '') || !isset($targetInput['password'])) {
            $errors['password'] = '入力してください';
        } else if (($passwordValidation = self::validatePassword($targetInput['password'])) !== true) {  
            $errors['password'] = $passwordValidation;
        }
    

        return $errors;
    }

    
    /**
     * アップデート用のバリデーション
     * 空白かどうかは各項目のバリデーションに値をわたさなければ行けない関係でこのメソッド内でチェック
     *　$user_infoを配列として受取り、validateEmailの引数として渡す(ここでは編集される人の情報)
     * 
     * @param  array $targetInput
     * @param  array $user_info
     * @return array
     */
    public function validateUpdateInput(array $targetInput, array $user_info): array {
        $errors = [];

        /**
         * emailのvalidation emailが入力されているが空白、もしくは存在しない場合と各種条件を満たしていない場合はエラー情報が入る
         * 条件のどこにも属さなかった場合（elseだった場合）はバリデーション追加
         * 他の項目もすべて同じ感じ
         */ 

            if ((isset($targetInput['email']) && $targetInput['email'] == '') || !isset($targetInput['email'])) {
                $errors['email'] = '入力してください';
            } else if(($emailValidaton = self::validateEmail($targetInput['email'],$user_info['email'])) !== true){ 
                $errors['email'] = $emailValidaton;          
            }


            if ((isset($targetInput['nickname']) && $targetInput['nickname'] == '') || !isset($targetInput['nickname'])) {
                $errors['nickname'] = '入力してください';
            } else if (($nicknameValidation = self::validateNickname($targetInput['nickname'],$user_info['nickname'])) !== true) {
                $errors['nickname'] = $nicknameValidation;
            }

        return $errors;
    }

}