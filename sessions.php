<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php'?>
  <link rel="stylesheet" href="stylesheets/courses.css">
  <link rel="stylesheet" href="stylesheets/sessions.css">
  <title>Sessions</title>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['gym_name']) || !isset($_SESSION['gym_id'])) {
  header("Location: login.php");
  exit();
}
include 'owner-sidebar.php';
include 'db.php';
?>
<main>
  <h1>Upcoming Sessions <button data-id="add" class="icon-btn modal-btn"><i class="fa-solid fa-plus"></i></button></h1>
  <div class="sessions-wrapper">
    <?php
    // Fetch sessions for the current gym sorted by date
    $sessions = $db->select('session', array('gym_id' => $_SESSION['gym_id']), ['date']);
    $sessions = array_filter($sessions, function ($session) {return $session['date'] >= date('Y-m-d H:i:s');});

    // Function to find course by ID
    function find_course($course_id) {
        global $db;
        $course_data = $db->select('course', array('course_id' => $course_id));
        return $course_data[0];
    }

    // Function to find trainer by ID
    function find_trainer($trainer_id) {
        global $db;
        $trainer_data = $db->select('trainer', array('trainer_id' => $trainer_id));
        return $trainer_data[0];
    }

    // Iterate through each session and display the details
    foreach ($sessions as $session) {
        $course = find_course($session['course_id']);
        $trainer = find_trainer($session['trainer_id']);
        $trainer_name = $trainer['first_name'] . ' ' . $trainer['last_name'];
        $session_date = date('d/m', strtotime($session['date']));
        $session_time = date('H:i', strtotime($session['date']));
        $capacity_class = ($session['current_capacity'] >= $session['max_capacity']) ? 'full' : '';
    ?>
    <div class="session-card">
      <div class="session-date"><?php echo $session_date; ?></div>
      <div class="session-hour"><?php echo $session_time; ?></div>
      <div class="session-course"><?php echo $course['course_name']; ?></div>
      <div class="actions">
        <button data-id="edit" class="icon-btn modal-btn" onclick="editSession(<?php echo htmlspecialchars(json_encode($session)); ?>)"><i class="fa-solid fa-pen"></i></button>
        <form method="POST" action="form-processing/session.php" style="display:inline;">
          <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
          <button class="icon-btn" type="submit" name="delete_session"><i class="fa-solid fa-trash"></i></button>
        </form>
      </div>
      <div class="session-capacity <?php echo $capacity_class; ?>"><?php echo $session['current_capacity'] . '/' . $session['max_capacity']; ?></div>
      <div class="session-duration <?php echo strlen($session['duration']) > 2 ? 'small-text' : 'normal-text'; ?>">
        <?php echo $session['duration']; ?> min
      </div>
      <div class="session-trainer"><?php echo $trainer_name; ?></div>
    </div>
    <?php } ?>
  </div>

  <!-- <div style="text-align: center; margin-top: 20px;">
    <a href="form-processing/reservations.php" class="btn-reserve">Reserve a Session</a>
  </div> -->

  <div class="modal" id="add">
    <div class="container">
      <h1>Add Session</h1>
      <form method="POST" action="form-processing/session.php">
        <input type="hidden" name="gym_id" value="<?php echo $_SESSION['gym_id']; ?>">
        <input placeholder="Date" type="datetime-local" id="session_date" name="session_date" required>
        <input placeholder="Max Capacity" type="number" id="max_capacity" name="max_capacity" required>
        <input placeholder="Duration (minutes)" type="number" id="duration" name="duration" required>
        <select id="course_id" name="course_id" required>
          <?php
          $courses = $db->select('course', ['gym_id' => $_SESSION['gym_id']]);
          foreach ($courses as $course) {
              echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
          }
          ?>
        </select>
        <select id="trainer_id" name="trainer_id" required>
          <?php
          $trainers = $db->select('trainer', ['gym_id' => $_SESSION['gym_id']]);
          foreach ($trainers as $trainer) {
              $trainer_name = $trainer['first_name'] . ' ' . $trainer['last_name'];
              echo "<option value='{$trainer['trainer_id']}'>{$trainer_name}</option>";
          }
          ?>
        </select>
        <button class="modal-btn form-btn" type="submit" name="add_session">Add Session</button>
      </form>
    </div>
  </div>

  <div class="modal" id="edit">
    <div class="container">
      <h1>Edit Session</h1>
      <form method="POST" action="form-processing/session.php">
        <input type="hidden" id="edit_session_id" name="session_id">
        <input type="datetime-local" id="edit_session_date" name="session_date" required>
        <input type="number" id="edit_max_capacity" name="max_capacity" required>
        <input type="number" id="edit_duration" name="duration" required>
        <select id="edit_course_id" name="course_id" required>
          <?php
          foreach ($courses as $course) {
              echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
          }
          ?>
        </select>
        <select id="edit_trainer_id" name="trainer_id" required>
          <?php
          foreach ($trainers as $trainer) {
              $trainer_name = $trainer['first_name'] . ' ' . $trainer['last_name'];
              echo "<option value='{$trainer['trainer_id']}'>{$trainer_name}</option>";
          }
          ?>
        </select>
        <button class="modal-btn form-btn" type="submit" name="update_session">Update Session</button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
            const input = document.getElementById('session_date');
            const editInput = document.getElementById('edit_session_date');
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const today = `${year}-${month}-${day}T${hours}:${minutes}`;
            input.min = today;
            input.value = today;
            editInput.value = today;
            editInput.min = today;
        });
    function formatDateWithoutSeconds(dateString) {
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function editSession(data) {
      document.getElementById('edit_session_id').value = data.session_id;
      document.getElementById('edit_session_date').value = formatDateWithoutSeconds(data.date);
      document.getElementById('edit_max_capacity').value = data.max_capacity;
      document.getElementById('edit_max_capacity').min = data.current_capacity;
      document.getElementById('edit_duration').value = data.duration;
      document.getElementById('edit_course_id').value = data.course_id;
      document.getElementById('edit_trainer_id').value = data.trainer_id;
    }
</script>

</main>
</body>
</html>
