<?php

//バリデーションルール用のファイル読み込み
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class Task {
    private $Task;

    // is_deletedの対応
    public const DELETED_NO = 0;  // 未削除
    public const DELETED_YES = 1; // 削除済み

    // progressの対応
    public const PROGRESS_NO = 0;  // 未着手
    public const PROGRESS_FIRST = 1; // 進行中
    public const PROGRESS_SECOND = 2; // 確認中
    public const PROGRESS_THIRD = 3; // 完了

    //is_deletedのラベル
    public static array $deletedLabels = [
        self::DELETED_NO => '未削除',
        self::DELETED_YES => '削除済み',
    ];

    //progressのラベル
    public static array $pregressLabels = [
        self::PROGRESS_NO => '未着手',
        self::PROGRESS_FIRST => '進行中',
        self::PROGRESS_SECOND => '確認中',
        self::PROGRESS_THIRD => '完了'
    ];

    /**
     * このクラス内でデータベース接続できるように
     * 
     * 今回のプロジェクトでは
     * クラス内でのDB接続用のオブジェクト(pdoオブジェクト)は
     * $this->クラス名で固定させるルールでやりましょう（このクラスはTaskなので$this->Task）
     */
    public function __construct() {
        $this->Task = Database::getInstance()->getConnection();
    }

    
    /**
     * progressラベルの使用
     *
     * @param  int $progress 値が入ってなかったら$pregressLabelsの配列がreturnされる
     * @return int | array
     */
    public static function getPregressLabels(?int $progress_number = null): string|array {
        if($progress_number !== null) {
            return self::$pregressLabels[$progress_number] ?? '不明';
        }
        
        return self::$pregressLabels;
        
    }

    
    /**
     * タスク情報をデータベースに挿入する
     *
     * @param  array $post_task_info
     * @return bool | array
     */
    public function insert(array $post_task_info): bool|array {
        $created_query = $this->Task->prepare(
            "INSERT INTO tasks SET name=?, detail=?, user_id=?, progress=0,
             deadline=?, created_at=NOW(), updated_at=NOW()
            ");
        $created_query->execute(array(
            $post_task_info['name'],
            $post_task_info['detail'],
            $post_task_info['user_id'],
            $post_task_info['deadline']
        ));
        return $created_query->fetch();
    }
    
    /**
     * タスク一覧ページ用
     * 全ページのタスクを取得
     * 
     * @return array
     */
    public function getAllTasks():array {
        $query = "SELECT * FROM tasks ORDER BY deadline ASC";
        $stmt = $this->Task->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    
    /**
     * タスクのバリデーションチェック
     *
     * @param  array $targetInput
     * @return array
     */
    public function validateInsertInput(array $targetInput): array {
        $errors = [];

        /**
         * nameのvalidation nameが入力されているが空白、もしくは存在しない場合はエラー情報が入る
         * 他の項目もすべて同じ感じ
         */ 

        if ((isset($targetInput['name']) && $targetInput['name'] == '') || !isset($targetInput['name'])) {
            $errors['name'] = '入力してください';
        }

        if ((isset($targetInput['detail']) && $targetInput['detail'] == '') || !isset($targetInput['detail'])) {
            $errors['detail'] = '入力してください';
        }

        if ((isset($targetInput['user_id']) && $targetInput['user_id'] == '') || !isset($targetInput['user_id'])) {
            $errors['user_id'] = '入力してください';
        }
    
    
        if ((isset($targetInput['deadline']) && $targetInput['deadline'] == '') || !isset($targetInput['deadline'])) {
            $errors['deadline'] = '入力してください';
        }
    

        return $errors;
    }



}