<nav class="sidebar">
    <img class="logo" src="assets/logo_icon.png" alt="Gym+ logo">
    <h2>Main Menu</h2>
    <ul>
      <li><a  href="dashboard.php" <?php if (basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
    echo 'class="active"';
}
?>><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
      <li><a href="trainers.php" <?php if (basename($_SERVER['PHP_SELF']) == 'trainers.php') {
    echo 'class="active"';
}
?>><i class="fa-solid fa-dumbbell"></i>Trainers</a></li>
      <li><a href="subscriptions.php" <?php if (basename($_SERVER['PHP_SELF']) == 'subscriptions.php') {
    echo 'class="active"';
}
?>><i class="fa-solid fa-credit-card"></i>Subscriptions</a></li>
      <li><a href="courses.php" <?php if (basename($_SERVER['PHP_SELF']) == 'courses.php') {
    echo 'class="active"';
}
?>><i class="fa-solid fa-notes-medical"></i></i>Courses</a></li>
      <li><a href="sessions.php" <?php if (basename($_SERVER['PHP_SELF']) == 'sessions.php') {
    echo 'class="active"';
}
?>><i class="fa-solid fa-calendar-plus"></i>Sessions</a></li>
    </ul>
    <h2>Settings</h2>
    <ul>
    <li><a href="change-password.php" <?php if (basename($_SERVER['PHP_SELF']) == 'change-password.php') echo 'class="active"'; ?> ><i class="fa-solid fa-key"></i> Change Password</a></li>
      <li><a href="form-processing/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i>Log Out</a></li>
    </ul>
  </nav>