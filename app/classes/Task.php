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
    public static function getProgressLabels(?int $progress_number = null): string|array {
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
        if(isset($post_task_info['category_id']) && $post_task_info['category_id'] == '') {
            $post_task_info['category_id'] = NULL;
        }
        $created_query = $this->Task->prepare(
            "INSERT INTO tasks SET category_id=?, name=?, detail=?, user_id=?, progress=0,
             deadline=?, created_at=NOW(), updated_at=NOW()
            ");
        $created_query->execute(array(
            $post_task_info['category_id'],
            $post_task_info['name'],
            $post_task_info['detail'],
            $post_task_info['user_id'],
            $post_task_info['deadline'],
        ));
        return $created_query->fetch();
    }

    
    /**
     * タスクの情報を更新する
     *
     * @param  int $primary_key
     * @param  array $update_task
     * @return bool | array
     */
    public function update(int $primary_key, array $update_task): bool|array {
        if(isset($update_task['category_id']) && $update_task['category_id'] == '' ){
            $update_task['category_id'] = NULL;
        }
        $update_query = $this->Task->prepare(
            "UPDATE tasks SET category_id=?, name=?, detail=?, user_id=?, progress=?, deadline=?, updated_at=NOW() WHERE id=?"
        );
        $update_query->execute(array(
            $update_task['category_id'],
            $update_task['name'],
            $update_task['detail'],
            $update_task['user_id'],
            $update_task['progress'],
            $update_task['deadline'],
            $primary_key
        ));
        return $update_query->fetch();
    }

    
    /**
     * タスク一覧ページ用
     * 全ページのタスクを取得
     * 
     * @return array
     */
    public function getAllTasks(): array {
        $query = "SELECT * FROM tasks ORDER BY deadline ASC";
        $stmt = $this->Task->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * 完了以外のタスクの数を取得する
     *
     * @return array|bool
     * 
     */
    public function getUnfinishedTaskCount(): array|bool {
        $query = "SELECT COUNT(*) as task_count FROM tasks WHERE progress!=3";
        $stmt = $this->Task->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    /**
     * １ページに表示できる件数のタスクを取得
     *
     * @param  int $start_number
     * @return bool | array
     */
    public function getTaskPage(int $start_number): bool|array{
        $task_page = $this->Task->prepare("SELECT * FROM tasks WHERE progress!=3 ORDER BY deadline ASC LIMIT ?, 5");
        $task_page->bindParam(1, $start_number, PDO::PARAM_INT);
        $task_page->execute();
        return $task_page->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * タスク一覧の検索システム
     * １ページに表示できる数分の検索されたタスクを取得する
     *
     * @param  array $params
     * @param  int $start_number
     * @return array
     */
    public function search(array $params,int $start_number): array {
        // baseとなるSQL。WHERE 1=1 とすることで後続の AND 条件を組みやすくする。
        $query = "SELECT * FROM tasks WHERE 1=1";
        
        //カテゴリを検索
        if(isset($params['category_id']) && $params['category_id'] == 0) {
            $query .= " AND category_id IS NULL";

        } elseif (!empty($params['category_id'])) {
            $query .= " AND category_id = ".trim($params['category_id'])."";
        }

        // 担当者をuser_idで検索
        if (!empty($params['user_id'])) {
           $query .= " AND user_id = ".trim($params['user_id'])."";
        }
 
        // 進捗で検索
        if (!empty($params['progress']) || $params['progress'] == 0) {
            $query .= " AND progress = ".trim($params['progress'])."";
        } 
        if (empty($params['progress'])) {
            $query .= " AND progress IN(0,1,2)";
        }
 
        // 締め切り期限の範囲
        if (!empty($params['start_date'])) {
             $query .= " AND deadline >= '".trim($params['start_date'])."'";
        }
 
        // 締め切り期限の範囲
        if (!empty($params['end_date'])) {
            $query .= " AND deadline <= '".trim($params['end_date'])."'";
        }
 
        //期限順になるように
        $query .= " ORDER BY deadline ASC LIMIT $start_number,5";

        $stmt = $this->Task->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
     
    }
    
    
    /**
     * 検索にひっかかったタスクの数を取得する
     *
     * 
     * @param  array $params
     * @return bool|array
     */
    public function searchCount(array $params): bool|array {
        // baseとなるSQL。WHERE 1=1 とすることで後続の AND 条件を組みやすくする。
        $query = "SELECT COUNT(*) as task_count FROM tasks WHERE 1=1";
  
        //カテゴリを検索
        if(isset($params['category_id']) && $params['category_id'] == 0) {
            $query .= " AND category_id IS NULL";
        } elseif (!empty($params['category_id'])) {
            $query .= " AND category_id = ".trim($params['category_id'])."";
        }

        // 担当者をuser_idで検索
        if (!empty($params['user_id'])) {
           $query .= " AND user_id = ".trim($params['user_id'])."";
        }
 
        // 進捗で検索
        if (!empty($params['progress']) || $params['progress'] == 0) {
            $query .= " AND progress = ".trim($params['progress'])."";
        }
        if (empty($params['progress'])) {
            $query .= " AND progress IN(0,1,2)";
        }
 
        // 締め切り期限の範囲
        if (!empty($params['start_date'])) {
            $query .= " AND deadline >= '".trim($params['start_date'])."'";
        }
 
        // 締め切り期限の範囲
        if (!empty($params['end_date'])) {
            $query .= " AND deadline <= '".trim($params['end_date'])."'";
        }
 
        //期限順になるように
        $query .= " ORDER BY deadline ASC";

        $stmt = $this->Task->query($query);
        return $stmt->fetch();
     
    }

    
    /**
     * taskのidからそのレコードのすべてのカラムの情報を取得する
     *
     * @param  int $task_id
     * @return bool | array
     */
    public function getByTaskId(int $task_id) : bool|array {
        $query_task_id = $this->Task->prepare("SELECT * FROM tasks WHERE id=?");
        $query_task_id->execute(array(
            $task_id
        ));
        return $query_task_id->fetch();
    }

    
    /**
     * tasksテーブルからレコードを削除する
     *
     * @param  int $task_id
     * @return bool
     */
    public function deleteTask(int $task_id) : bool {
        $delete_query = $this->Task->prepare("DELETE FROM tasks WHERE id=?");
        $delete_query->execute(array(
            $task_id
        ));
        return true;
    }


    
    /**
     * タスクのバリデーションチェック
     * insert用
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


        /**
     * タスクのバリデーションチェック
     * update用
     *
     * @param  array $targetInput
     * @return array
     */
    public function validateUpdateInput(array $targetInput): array {
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

       
        if ((isset($targetInput['progress']) && $targetInput['progress'] == '') || !isset($targetInput['progress'])) {
            $errors['progress'] = '入力してください';
        }
        
    
        if ((isset($targetInput['deadline']) && $targetInput['deadline'] == '') || !isset($targetInput['deadline'])) {
            $errors['deadline'] = '入力してください';
        }
    

        return $errors;
    }


}