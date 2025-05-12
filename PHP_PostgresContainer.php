<?php 
$dsn = 'pgsql:host=training2025-db-instance-1.c25mkwu8gg8k.us-east-1.rds.amazonaws.com;port=5432;dbname=salesdb';
$user = 'furutaa';
$password = 'training2025-furutaa';

$error = '';
$success = '';

try{
    header('Content-Type: text/html; charset=UTF-8');
    $pdo = new PDO($dsn, $user, $password);
    //ヘッダー情報を設定
    //echo  "PostgreSQLに接続できました！"."\n";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        //名前とコメントが入力されているか確認
        if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['comment']) && !empty($_POST['comment'])){
            //入力された名前・コメントを取得
            $name = $_POST['username'] ?? '';
            $comment = $_POST['comment'] ?? '';
            //入力されたときの時間を取得
            $DateTime = new DateTime();
            $dateTime = $DateTime->format('Y-m-d H:i:s');

            //最大のidを取得
            $stmt = $pdo->query("SELECT MAX(id) FROM postss");
            $maxId = $stmt->fetchColumn();
            //新しいidの設定
            $newId = $maxId + 1;

            try{
                //データベースにコメントを挿入
                $sql = 'INSERT INTO  postss (id,comment, created_by, created_at) VALUES(:id,:comment, :created_by, :created_at)';
                $stmt = $pdo->prepare($sql);
                $stmt -> execute([
                    ':id' => $newId,
                    ':comment' => $comment,
                    ':created_by' => $name,
                    ':created_at' => $dateTime,
                ]);
                $success = 'コメントが正常に追加されました。';
        
                //コメントを追加した後にリダイレクト
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;

                    
            }catch(PDOException $e){
                $error = 'コメントの追加に失敗しました:'.$e->getMessage();
            }
        }else{
            echo "接続エラー：".$e->getMessage();
        }
    }
    
    //SQLクエリを実行して，データを取得
    $sql = 'SELECT * FROM postss WHERE deleted_at IS NULL';
    $stmt = $pdo->query($sql);
    
    
    // //結果を表示
    echo "<h1>Postsテーブルのデータ</h1>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr>
    <th>comment</th>
    <th>created_by</th>
    <th>created_at</th>
    <th>update</th>
    <th>delete</th>
    </tr>";
    
    //各行をテーブルの行として表示
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo "<tr>";
        //echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        //echo "<td>" . htmlspecialchars($row['updated_at'] ?? '') . "</td>";
        //echo "<td>" . htmlspecialchars($row['deleted_at'] ?? '') . "</td>";
        //updateボタン
        echo "<td>
            <form action='update.php' method='GET'>
            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
            <input type='submit' value='Update'>
            </form>
            </td>";
        //deleteボタン
        echo "<td>
            <form action='delete.php' method='GET'>
            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
            <input type='submit' value='Delete'>
            </form>
            </td>";
            }
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        //echo $row['id']."\n";
        echo $row['comment']."\n";
        echo $row['created_by']."\n";
        echo $row['created_at']."\n";
        // echo $row['updated_at']."\n";
        // echo $row['deleted_at']."\n";
  
    }
}catch(PDOException $e){
    echo "接続エラー：".$e->getMessage();   
}
    
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8">

        <h2>コメント入力</h2>
        <?php if ($error): ?><p style = "color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        

        <form method = "post" action = "">
            <label>名前<input type = "text" name = "username" required></label>
            <br><br>
            <label style = "color:blue">コメント</label>
            <br><br>
            <textarea name="comment" cols="50" rows="10"></textarea>
            <br><br>
            <input class = "button" type = "submit" value = "OK" style ="color: green">
            <br>
        </form>
    </head>

</html>