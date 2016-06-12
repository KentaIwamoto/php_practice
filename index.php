<?php
$dataFile = 'bbs.dat';

//CSRF対策
session_start();

function setToken(){
  $token = sha1(uniqid(mt_rand(), true));
  $_SESSION['token']= $token;
}

function checkToken(){
  if (empty($_SESSION['token'] )|| ($_SESSION['token'] != $_POST['token'])) {
    echo "不正な処理がなされています。";
    exit;
  }
}

function h($s){
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
  isset($_POST['message']) &&
  isset($_POST['user'])
  ){

    checkToken();

  $message = trim($_POST['message']);
  $user = trim($_POST['user']);

  if ($message !== "") {
    $user = ($user === '') ? '名無しさん' : $user;

    $message = str_replace("\t"," ", $message);
    $user = str_replace("\t"," ", $user);

    $minute = 'Y-m-d H:i:s';
    $postedAt = date($minute);

    $newData = $message . "\t" . $user . "\t" . $postedAt . "\n";

    $fp = fopen($dataFile,'a');
    fwrite($fp,$newData);
    fclose($fp);
  }
}else{
  setToken();
}
$posts = file($dataFile, FILE_IGNORE_NEW_LINES);

$posts = array_reverse($posts);



?>

<!DOCTYPE  html>
<html lang="ja">
<head>
  <meta name="name"chareset="utf-8">
  <title>簡易掲示板</title>
</head>
<body>
  <h1>簡易掲示板</h1>
  <form   action="" method="post"    >
    meesage:<input type="text" name="message" value="">
    user:<input type="text" name="user" value="">
    <input type="submit" name="name" value="投稿">
    <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
  </form>
  <h2>投稿一覧 （<?php  echo count($posts); ?>件）</h2>
  <ul>
    <?php if (count($posts))  : ?>
      <?php  foreach ($posts as $post) :  ?>
      <?php  list($message, $user, $postedAt) =  explode("\t", $post)?>
        <li><?php  echo h($message);  ?>(<?php  echo h($user);  ?>) - <?php  echo h($postedAt);  ?></li>
      <?php  endforeach ;?>
    <?php  else: ?>
    <li>まだ投稿はありません。</li>
  <?php  endif; ?>
  </ul>
</body>
</html>
