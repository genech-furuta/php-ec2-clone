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
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $id = $_GET["id"] ?? '';
        //IDが有効な場合，そのレコードを取得
        $sql = 'SELECT * FROM postss WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //レコードが見つからない場合
        if(!$post){
            $error = '指定された投稿が見つかりません';
        }
    }else{
        $error = 'IDが取得できません';
    }

    //フォームが送信されているか確認
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_POST['comment']) && !empty($_POST['comment'])){

            $comment = $_POST['comment'] ?? '';
            //呼び出されたときの時間を取得
            $dateTime = new DateTime();
            $updateTime = $dateTime->format('Y-m-d H:i:s');
            
            //データベースにコメントを更新
            try{
                $sql = 'UPDATE postss SET comment = :comment, updated_at = :updated_at WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt ->execute([
                    ':comment' => $comment,
                    ':updated_at' => $updateTime,
                    ':id' => $id
                ]);
                $success = 'コメントは正常に変更されました.';
                
                //コメントを変更した後に元のページにリダイレクト
                header("Location: PHP_PostgresContainer.php");
                exit;
            }catch(PDOException $e){
                echo 'コメントの追加に失敗しました:'.$e->getMessage();
            }
        }else{
            $error = 'コメントが入力されていません';
        }  

    }

}catch(PDOException $e){
    echo 'コメントの変更に失敗しました'.$e->getMessage();
}

?>

<!DOCTYPE html>
<html>
    <body>
        <meta charset = "UTF-8">
        <h1>コメント変更</h2>
        <?php if($error): ?><p style = "color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        
        <h2>コメント入力</h2>
        <form method = "post" action = "">
            <br><br>
            <textarea name = "comment" cols = "50" rows = "10"></textarea>
            <br><br>
            <input class = "button" type = "submit" value = "OK">
            <br>
        </form>
    </body>

</html>