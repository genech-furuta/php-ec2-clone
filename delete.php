<?php
$dsn = 'pgsql:host=training2025-db-instance-1.c25mkwu8gg8k.us-east-1.rds.amazonaws.com;port=5432;dbname=salesdb';
$user = 'furutaa';
$password = 'training2025-furutaa';

$error = '';
$success = '';

try{
    header('Content-Type: text/html; charset=UTF-8');
    $pdo = new PDO($dsn, $user, $password);

    //GETクエリからIDを取得
    if(isset($_GET['id']) && !empty(['id'])){
        $id = $_GET["id"] ?? '';
        //IDが有効な場合，そのレコードを取得
        $sql = 'SELECT * FROM postss WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt -> execute([':id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        //レコードが見つからない場合
        if(!$post){
            $error = '指定された投稿が見つかりません';
        }

        //削除された時間を取得
        $dateTime = new DateTime();
        $deleteTime = $dateTime->format('Y-m-d H:i:s');

        //データベースに削除された日時を入力
        try{
            $sql = 'UPDATE postss SET deleted_at = :deleted_at WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt -> execute([
                ':deleted_at' => $deleteTime,
                ':id' => $id
            ]);
            $success = 'コメントは正常に削除されました';

            //コメント削除後に元のページにリダイレクト
            header("Location: PHP_PostgresContainer.php" );
            exit;

        }catch(PDOException $e){
            echo 'コメント削除に失敗しました'.$e->getMessage();
        }
    }else{
        $error = 'IDが取得できません';
    }
}catch(PDOException $e){
    echo 'コメントの削除に失敗しました'.$e->getMessage();
}
?>

<!DOCTYPE html>
<html>
    <head>
    </head>
</html>