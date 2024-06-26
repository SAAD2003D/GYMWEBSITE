<?php 
session_start();

include 'db.php';

if(isset($_POST['sign-in'])){
   $db = new Database();

   $email=$_POST['email'];
   $password=$_POST['password'];
   $password=md5($password) ;
   
   $table = isset($_POST['gym-id']) ? 'member' : 'gym';
   $conditions = array('password' => $password, 'email' => $email);
   if ($table == 'member') {
    $conditions['gym_id'] = $_POST['gym-id'];
   }
   $userFind = $db->select($table, $conditions);
   
   if(count($userFind) > 0){
    $firstName = $userFind[0][$table == 'gym' ? 'owner_first_name' : 'first_name'];
    $lastName = $userFind[0][$table == 'gym' ? 'owner_last_name' : 'last_name'];
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    if ($table == 'gym') {
      $_SESSION['gym_name'] = $userFind[0]['gym_name'];
    } else {
      $_SESSION['member_id'] = $userFind[0]['member_id'];
    }
    $_SESSION['gym_id'] = $userFind[0]['gym_id'];
    $location = $table == 'gym' ? 'dashboard' : 'member-homepage';
    header("Location: $location.php");
    exit;
   }
   else{
    $message = "Incorrect password or email";
   }

}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in</title>
    <link rel="stylesheet" href="./stylesheets/reset.css">
    <link rel="stylesheet" href="./stylesheets/common.css">
    <link rel="stylesheet" href="./stylesheets/login.css" />
    <link rel="stylesheet" href="stylesheets/messaging.css">
  </head>
  <body>
  <?php if(isset($message)) { ?>
    <p class="message failure"><?php echo $message ?></p>   
<?php } ?>
    <nav>
      <div class="nav-wrapper">
        <a href="index.html"><img src="./assets/logo_icon.png" alt="Logo" class="logo-icon" /></a>
      </div>
    </nav>
    <main>
      <div class="container">
        <h1>Login</h1>
        <p>Sign in to your account</p>
        <form class="login" action="#" method="post">
          <input
            type="email"
            name="email"
            placeholder="Enter your mail address"
            required
          />
          <input
            type="password"
            name="password"
            placeholder="Enter your Password"
            required
          />
          <?php 
          if (isset($_GET["gym-id"])) { 
          ?>
            <input
            type="hidden"
            name="gym-id"
            value = <?php echo $_GET["gym-id"] ?>
          />
          <?php
          }
          ?>
          
          
          <button name="sign-in" type="submit" class="form-btn">Login</button>
        </form>
        <a href="register.php" class="back-link">Don't have an account yet? Sign up!</a>
      </div>
    </main>
  </body>
</html>
