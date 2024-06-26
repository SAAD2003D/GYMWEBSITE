<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id']) && !isset($_SESSION['gym_name'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $id = null;
    $entity = null;
    if (isset($_SESSION['member_id'])) {
        $id = $_SESSION['member_id'];
        $entity = $db->select('member', array('member_id' => $id))[0];
    } else {
        $id = $_SESSION['gym_id'];
        $entity = $db->select('gym', array('gym_id' => $id))[0];
    }
    
    if (md5($current_password) ==  $entity['password']) {
        if ($new_password === $confirm_password) {
            $new_password_hash = md5($new_password);
            if (isset($_SESSION['member_id'])) {
                $db->update('member', array('password' => $new_password_hash), array('member_id' => $id));
            } else {
                $db->update('gym', array('password' => $new_password_hash), array('gym_id' => $id));
            }
            $message = "Password changed successfully!";
            $class = 'success';
        } else {
            $message = "New passwords do not match.";
            $class = 'failure';
        }
    } else {
        $message = "Current password is incorrect.";
        $class = 'failure';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_template.php'; ?>
    <link rel="stylesheet" href="stylesheets/change-password.css">
    <title>Change Password</title>
</head>
<body>
    <?php if(isset($_SESSION['member_id'])) include 'member-sidebar.php';
        if(isset($_SESSION['gym_name'])) include 'owner-sidebar.php'; ?>
    <main>
        <h1>Change Password</h1>
        <?php if (isset($message)) { echo "<p class=\"message $class\">$message</p>"; } ?>
        <div class="container">
            <form method="POST" action="change-password.php">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            
                <button type="submit" class="form-btn">Submit</button>
            </form>
        </div>
    </main>
</body>
</html>
