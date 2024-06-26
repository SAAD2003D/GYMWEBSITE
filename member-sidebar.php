
  <nav class="sidebar">
    <img class="logo" src="assets/logo_icon.png" alt="Gym+ logo">
    <h2>Main Menu</h2>
    <ul>
        <li><a href="member-homepage.php" <?php if (basename($_SERVER['PHP_SELF']) == 'member-homepage.php') echo 'class="active"'; ?>><i class="fa-solid fa-chart-pie"></i> Home</a></li>
        <li><a href="member-subscriptions.php" <?php if (basename($_SERVER['PHP_SELF']) == 'member-subscriptions.php') echo 'class="active"'; ?>><i class="fa-solid fa-credit-card"></i> Subscriptions</a></li>
        <li><a href="member-sessions.php" <?php if (basename($_SERVER['PHP_SELF']) == 'member-sessions.php') echo 'class="active"'; ?>><i class="fa-solid fa-calendar-plus"></i> Sessions</a></li>
    </ul>
    <h2>Settings</h2>
    <ul>
        <li><a href="change-password.php" <?php if (basename($_SERVER['PHP_SELF']) == 'change-password.php') echo 'class="active"'; ?> ><i class="fa-solid fa-key"></i> Change Password</a></li>
        <li><a href="form-processing/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a></li>
    </ul>
</nav>
