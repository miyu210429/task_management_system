<?php

//バリデーションルール用のファイル読み込み
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class Category {
    private $Category;

    /**
     * このクラス内でデータベース接続できるように
     * 
     * 今回のプロジェクトでは
     * クラス内でのDB接続用のオブジェクト(pdoオブジェクト)は
     * $this->クラス名で固定させるルールでやる（このクラスはCategoryなので$this->Category）
     */
    public function __construct() {
        $this->Category = Database::getInstance()->getConnection();
    }

    
    /**
     * カテゴリ情報をデータベースに挿入する
     *
     * @param  array $category_info
     * @return bool|array
     */
    public function insert(array $category_info): bool|array {
        $query = $this->Category->prepare("INSERT INTO categories SET 
            category_name=?, created_at=NOW(), updated_at=NOW(), last_update_user_id=?");
        $query->execute(array(
            $category_info['category_name'],
            $category_info['last_update_user_id']
        ));
        return $query->fetch();
    }

    
    /**
     * カテゴリ名のバリデーション
     *
     * @param  string $category_name
     * @return bool|string 
     */
    public function validateCategoryName(string $category_name): bool|string {
        if (!ValidationHelper::validateLength($category_name, 2, 15)) {
            return "カテゴリ名は2文字以上15文字以下にしてください。";
        }
        return true;
    }


        
    /**
     * インサートフォーム用のバリデーション
     * 空白かどうかと、カテゴリ名のバリデーションに違反していないかチェック
     *
     * @param  mixed $targetInput
     * @return array
     */
    public function validateInsertInput(array $targetInput): array {
        $errors = [];

        if ((isset($targetInput['category_name']) && $targetInput['category_name'] == '') || !isset($targetInput['category_name'])) {
            $errors['category_name'] = '入力してください';
        } else if(($category_nameValidaton = self::validateCategoryName($targetInput['category_name'])) !== true){ 
            $errors['category_name'] = $category_nameValidaton;          
        }

        return $errors;
    }


}

