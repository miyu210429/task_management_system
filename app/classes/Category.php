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
     * 更新されたカテゴリ情報をアップデートする
     *
     * @param  array $category_info
     * @return bool|array
     */
    public function update(array $category_info, int $category_id): bool|array {
        $query = $this->Category->prepare("UPDATE categories SET 
            category_name=?,last_update_user_id=?,updated_at=NOW() WHERE id=?");
        $query->execute(array(
            $category_info['category_name'],
            $category_info['last_update_user_id'],
            $category_id

        ));
        return $query->fetch();
    }

       /**
     * すべてのカテゴリを取得する
     * 最終更新人はcategoriesテーブルのlast_update_user_idから、
     * usersテーブルに情報を取りに行くことで取得できる
     *
     * @return bool|array
     */
    public function getAllCategories(): bool|array {
        $query = "SELECT u.id, u.nickname, u.is_deleted, c.* FROM categories c 
            LEFT JOIN users u ON c.last_update_user_id=u.id 
            WHERE c.is_deleted=0 ORDER BY c.id DESC";
        $stmt = $this->Category->query($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * idが$category_idのカテゴリを、カテゴリテーブルからSELECTしてくる
     *
     * @param  int $category_id
     * @return bool|array
     */
    public function getByCategoryId(int $category_id) : bool|array {
        $query_category_id = $this->Category->prepare("SELECT * FROM categories WHERE id=?");
        $query_category_id->execute(array(
            $category_id
        ));
        return $query_category_id->fetch();
    }

    
    /**
     * ユーザー情報を削除するためのもの
     * is_deletedカラムを１にすると削除
     *
     * @param  int $delete_id
     * @return bool | array
     */
    public function deleteCategory(int $delete_id) : bool | array {
        $delete_query = $this->Category->prepare(
            'UPDATE categories c JOIN tasks t SET c.is_deleted = 1, t.category_id=NULL WHERE c.id=?'
        );
        $delete_query->execute(array(
            $delete_id
        ));
        return $delete_query->fetch();
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


    /**
     * アップデートフォーム用のバリデーション
     * 空白かどうかと、カテゴリ名のバリデーションに違反していないかチェック
     *
     * @param  mixed $targetInput
     * @return array
     */
    public function validateUpdateInput(array $targetInput): array {
        $errors = [];

        if ((isset($targetInput['category_name']) && $targetInput['category_name'] == '') || !isset($targetInput['category_name'])) {
            $errors['category_name'] = '入力してください';
        } else if(($category_nameValidaton = self::validateCategoryName($targetInput['category_name'])) !== true){ 
            $errors['category_name'] = $category_nameValidaton;          
        }

        return $errors;
    }



}

