<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');
// Load env and create connection
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$host = $_ENV['DB_HOST'];
$databaseName = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

$connection = new mysqli($host, $username, $password, $databaseName);
if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
} else {
  $errors = [];
  $title = "";
  $content = "";
  if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    if (empty($title)) {
      $errors['title'] = "Title or content can not be empty";
    } else if (empty($content)) {
      $errors['content'] = "Content can not be empty";
    } else {
      $connection->query("INSERT INTO tasks (title, content) VALUES ('$title', '$content')");
      header('location: index.php');
    }
  }
}

if (isset($_GET['del_task'])) {
  $id = $_GET['del_task'];
  $connection->query("DELETE FROM tasks WHERE id = '$id'");
  header('location: index.php');
}

if (isset($_GET['com_task'])) {
  $id = $_GET['com_task'];
  $connection->query("update tasks set completed = 1 WHERE id = '$id'");
  header('location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style.css">
  <title>To do app</title>
</head>

<body>
  <header class="header">
    <div class="header__inner container">
      <h1 class="header__title">PHP Todo-list</h1>
      <form class="todo-form" method="POST" action=<?php echo $_SERVER['PHP_SELF'] ?>>
        <?php
        if (isset($errors['title'])) { ?>
          <p style="color:red"><?php echo $errors['title'] ?></p>
        <?php } ?>
        <div class="todo-form__group">

          <input type="text" name="title" placeholder="Todo title" class="todo-form__input" placeholder="Todo Title..." />
        </div>
        <?php
        if (isset($errors['content'])) { ?>
          <p style="color:red"><?php echo $errors['content'] ?></p>
        <?php } ?>
        <div class="todo-form__group">

          <textarea name="content" class="todo-form__textarea" placeholder="Todo Description..."></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn--primary">Submit</button>

      </form>
    </div>
  </header>
  <!-- end header -->

  <!-- start body -->
  <div class="body">
    <div class="container">
      <div class="cards">
        <div class="cards__row" id="cards_row">
          <?php
          $tasks = $connection->query("SELECT * FROM tasks");

          while ($row = $tasks->fetch_assoc()) { ?>
            <div class="card">
              <div class="card__header">
                <h3 class="card__title"><?php echo $row['title'] ?></h3>
                <div class="card__remove-icon">
                  <a href="index.php?del_task=<?php echo $row['id'] ?>"><span class="material-icons-outlined"> clear </span></a>
                </div>
              </div>
              <div class="card__body">
                <p class="card__text"><?php echo $row['content'] ?></p>
              </div>
              <div>
                <?php if ($row['completed'] == 1) { ?>
                  <p style="color: white; font-size: small; background-color: green; width: 70px;text-align:center;  border-radius :5px">completed</p> <?php } 
                  else { ?>
                    <form action=<?php echo $_SERVER['PHP_SELF'] ?> method="GET">
                    <a href="index.php?com_task=<?php echo $row['id'] ?>"> <p style="color: white; font-size: small; background-color: #007FFF; width: 130px ; height = 70px ;text-align:center;  border-radius :5px">Mark as completed</p></a>
                    </form>
               <?php
                  } ?>
              </div>
            </div>
          <?php  } ?>
        </div>
      </div>
    </div>
  </div>
</body>

</html>