<?php session_start();
    if (!isset($_SESSION['gym_id'])) {
      header("Location: login.php");
      exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php'?>
  <link rel="stylesheet" href="stylesheets/sessions.css">
  <link rel="stylesheet" href="stylesheets/member-homepage.css">
  <title>Home</title>
</head>
<body>
  <?php include 'member-sidebar.php';
include 'db.php';
$member = $db->select('member', array('member_id' => $_SESSION['member_id']))[0];
$subscription_ended = $member['sub_end_date'] < date('Y-m-d H:i:s');
$sub = null;
if (isset($member['sub_id'])) {
    $sub = $db->select('subscription', array('sub_id' => $member['sub_id']))[0];
}
$reservations = $db->select('reservations', array('member_id' => $_SESSION['member_id']));
$res_data = [];
foreach ($reservations as $reservation) {
  // Fetch the session details
  $session = $db->select('session', array('session_id' => $reservation['session_id']))[0];
  $course = $db->select('course', array('course_id' => $session['course_id']))[0];
  $trainer = $db->select('trainer', array('trainer_id' => $session['trainer_id']))[0];

  $key = $reservation['member_id'] . '_' . $reservation['session_id'];
  $res_data[$key] = array(
      'course_name' => $course['course_name'],
      'trainer_first_name' => $trainer['first_name'],
      'trainer_last_name' => $trainer['last_name'],
      'session_duration' => $session['duration'],
      'session_date' => $session['date']
  );
}
?>
  <main>
    <h1><?php echo "Welcome, " . $_SESSION['first_name'] . "!"; ?></h1>
    <h3>Your Subscription Status</h3>
  <?php 
  if ($member['sub_id'] === null) {
      echo "<h4 style=\"margin-bottom: 16px\">You have no active subscription. Click <a href=\"member-subscriptions.php\">here</a> to see available memberships.</h4>";
  } else {
      $subInfoMsg = $subscription_ended ? "Your subscription has ended on ".$member['sub_end_date'].". Click <a href=\"member-subscriptions.php\">here</a> to renew." : "Valid from <span class=\"sub-date\">" . $member['sub_start_date'] . " </span> until <span class=\"sub-date\">" . $member['sub_end_date']."</span>";
    ?>
    <div class="sub-wrapper">
        <div class="sub-card <?php if ($subscription_ended) echo 'subscription-ended' ?>">
          <p class="sub-name" style="<?php if(!$subscription_ended) echo "color: ".$sub['display_color'] ?>"><?php echo $sub['sub_name'] ?></p>
          <p><?php echo $subInfoMsg ?></p>
        </div>
    </div>
  <?php } ?>
    <h3>Booked Sessions</h3>
    <?php if (empty($reservations)) { ?>
      <h4>Your booked sessions will appear here. Click <a href="member-sessions.php">here</a> to see available sessions.</h4>
    <?php } ?>
    <div class="sessions-wrapper">
      <?php
        foreach ($reservations as $reservation) {
          $key = $reservation['member_id'] . '_' . $reservation['session_id'];
          $res = $res_data[$key];
          $course = $res['course_name'];
          $trainer_name = $res['trainer_first_name'] . ' ' . $res['trainer_last_name'];
          $session_duration = $res['session_duration'];
          $session_date = date('d/m', strtotime($res['session_date']));
          $session_time = date('H:i', strtotime($res['session_date']));
      ?>
      <div class="session-card">
        <div class="session-date"><?php echo $session_date; ?></div>
        <div class="session-hour"><?php echo $session_time; ?></div>
        <div class="session-course"><?php echo $course ?></div>
        <div class="session-duration <?php echo strlen($session_duration) > 2 ? 'small-text' : 'normal-text'; ?>">
          <?php echo $session_duration; ?> min
        </div>
        <div class="session-trainer"><?php echo $trainer_name; ?></div>
      </div>
      <?php } ?>
    </div>
  </main>
</body>
</html>