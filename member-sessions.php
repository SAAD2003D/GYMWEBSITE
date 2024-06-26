<?php session_start();
    if (!isset($_SESSION['gym_id'])) {
      header("Location: login.php");
      exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php' ?>
  <link rel="stylesheet" href="stylesheets/member-homepage.css">
  <link rel="stylesheet" href="stylesheets/sessions.css">
  <link rel="stylesheet" href="stylesheets/member-sessions.css">
  <title>Sessions</title>
</head>
<body>
  <?php
include 'member-sidebar.php';
include 'db.php';
$member = $db->select('member', array('member_id' => $_SESSION['member_id']))[0];
$subscription_ended = $member['sub_end_date'] < date('Y-m-d H:i:s');
$reservations = $db->select('reservations', array('member_id' => $_SESSION['member_id']));
$booked_sessions = [];
foreach($reservations as $reservation) {
  $booked_sessions[$reservation['session_id']] = $db->select('session', array('session_id' => $reservation['session_id']))[0];
}
$available_courses = array_map(function ($row) {return $row['course_id'];}, $db->select('subscription_access', array('sub_id' => $member['sub_id'])));
?>

  <?php
$sessions = $db->select('session', array('gym_id' => $_SESSION['gym_id']), ['date']);
$sessions = array_filter($sessions, function ($session) {return $session['date'] >= date('Y-m-d H:i:s');});
$available_sessions = array_filter($sessions, function ($session) use ($available_courses, $booked_sessions, $subscription_ended, $member) {
    if ($subscription_ended) {
        return false; // If subscription ended, no sessions are available
    }
    // Filter out sessions starting after subscription end date
    return in_array($session['course_id'], $available_courses) &&
        !array_key_exists($session['session_id'], $booked_sessions) &&
        $session['date'] <= $member['sub_end_date'];
});
$unavailable_sessions = array_filter($sessions, function ($session) use ($available_sessions, $booked_sessions) {
    return !in_array($session, $available_sessions) && !array_key_exists($session['session_id'], $booked_sessions);
});

function find_course($course_id)
{
    global $db;
    $course_data = $db->select('course', array('course_id' => $course_id));
    return $course_data[0];
}
function find_trainer($trainer_id)
{
    global $db;
    $trainer_data = $db->select('trainer', array('trainer_id' => $trainer_id));
    return $trainer_data[0];
}

function echo_sessions($sessions, $available = false, $booked = false)
{
    global $db;
    foreach ($sessions as $session) {
        if ($session['current_capacity'] >= $session['max_capacity']) {
            continue;
        }
        $course = find_course($session['course_id']);
        $trainer = find_trainer($session['trainer_id']);
        $trainer_name = $trainer['first_name'] . ' ' . $trainer['last_name'];
        $session_date = date('d/m', strtotime($session['date']));
        $session_time = date('H:i', strtotime($session['date']));
        $session_id = $session['session_id'];
        $member_id = $_SESSION['member_id'];
        $data = array('course' => $course['course_name'], 'trainer' => $trainer_name, 'session_id' => $session_id, 'member_id' => $member_id, 'date' => $session_date);
        ?>
      <div class="session-card <?php if ($available) echo 'modal-btn' ?>" <?php if ($available) echo 'data-id="book"' ?> onclick="bookSession(<?php echo htmlspecialchars(json_encode($data)); ?>)">
      <?php if($booked) { ?>
        <form id="delete-icon-form" method="POST" action="form-processing/reservations.php" style="display:inline;">
            <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
            <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
            <button type="submit" name="delete_reservation" class="icon-btn">
                <i class="fa-solid fa-circle-minus"></i>
            </button>
        </form>
      <?php } ?>
          <div class="session-date"><?php echo $session_date; ?></div>
          <div class="session-hour"><?php echo $session_time; ?></div>
          <div class="session-course"><?php echo $course['course_name']; ?></div>
          <div class="session-duration <?php echo strlen($session['duration']) > 2 ? 'small-text' : 'normal-text'; ?>">
              <?php echo $session['duration']; ?> min
          </div>
          <div class="session-trainer"><?php echo $trainer_name; ?></div>
      </div>
      <?php
    }
}
?>

  <main>
    <h1>Sessions</h1>
    <?php if (!empty($booked_sessions)) { ?>
    <h3>Booked Sessions</h3>
    <div class="sessions-wrapper"><?php echo_sessions($booked_sessions, booked: true) ?></div>
    <?php } ?>
    <?php if (!$subscription_ended && !empty($available_sessions)) { ?>
    <h3>Available Sessions</h3>
    <div class="available sessions-wrapper">
      <?php echo_sessions($available_sessions, true); ?>
    </div>
    <?php } ?>
    <h3>Locked Sessions</h3>
    <h4>These are planned sessions that your current subscription does not give access to. Check <a href="member-subscriptions.php">here</a> to see other membership options.</h4>
    <div class="sessions-wrapper locked"><?php echo_sessions($unavailable_sessions);?></div>
  </main>

  <div class="modal" id="book">
    <div class="container">
      <h1>Book Session</h1>
      <form method="POST" action="form-processing/reservations.php" id="book-form">
        <input type="hidden" name="member_id">
        <input type="hidden" name="session_id">
        <p>Do you want to book the <span id="course"></span> session with <span id="trainer"></span> on <span id="date"></span>?</p>
        <div class="form-buttons">
          <button name="reserve_session" class="form-btn" type="submit">Confirm</button>
          <button id="cancel" class="form-btn" type="button" onclick="toggleModal('book')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</body>
<script>
  function bookSession(data) {
    console.log(data);
    const form = document.getElementById('book-form');
    const course = document.getElementById('course');
    const trainer = document.getElementById('trainer');
    const date = document.getElementById('date');
    form.elements['member_id'].value = data.member_id;
    form.elements['session_id'].value = data.session_id;
    course.textContent = data.course;
    trainer.textContent = data.trainer;
    date.textContent = data.date;
  }
</script>
</html>
