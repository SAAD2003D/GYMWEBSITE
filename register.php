<?php 
session_start();
  include 'db.php';
  $db = new Database();
  if(isset($_POST['sign-up'])){
    $gym_name = $_POST['gym-name'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $password_confirm = $_POST['password-confirm'];
    $address = $_POST['gym-address'];
    $checkEmail = $db->select('gym', array('email' => $email));
    if(count($checkEmail) > 0){
      $message = "This email already exists";
    } elseif ($password != $password_confirm)  {
        $message = "Passwords do not match";  
    }
     else {
        $data = array('gym_name' => $gym_name,
                      'email' => $email,
                      'password' => md5($password),
                      'gym_address' => $address);
        $db->insert('gym', $data);
        $gym_id = $db->select('gym', array('password' => md5($password)))[0]['gym_id'];
        $_SESSION['gym_name'] = $gym_name;
        $_SESSION['gym_id'] = $gym_id;
        header("Location: dashboard.php");
     }
     echo "$password $password_confirm";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./stylesheets/reset.css">
    <link rel="stylesheet" href="./stylesheets/common.css">
    <link rel="stylesheet" href="./stylesheets/login.css" />
    <link rel="stylesheet" href="stylesheets/messaging.css">
</head>
<body style="position: relative;">
<?php if(isset($message)) { ?>
    <p class="message failure"><?php echo $message ?></p>   
<?php } ?>
    <nav>
        <div class="nav-wrapper">
        <a href="index.html"><img src="./assets/logo_icon.png" alt="Logo" class="logo-icon" /></a>
          </div>
    </nav>
    <main>
        <div class="container  registration-container">
            <h1>Sign Up</h2>
            <form class="registration" action="" method="POST">
                <div class="gym-name">
                    <label for="gym-name">Gym Name:</label>
                    <input type="text" id="gym-name" name="gym-name" >
                </div>

                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" >
                </div>


                <div>
                    <label for="gym-address">Gym Address:</label>
                    <input type="text" id="gym-address" name="gym-address" >
                </div>
                
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" >
                </div>

                <div>
                    <label for="password-confirm">Confirm Password:</label>
                    <input type="password" id="password-confirm" name="password-confirm" >
                </div>
        
                <button name="sign-up" class="form-btn" type="submit">Sign Up</button>
            </form>
            <a href="login.php" class="back-link">Already have an account? Sign in!</a>
        </div>
    </main>
</body>
</html>
