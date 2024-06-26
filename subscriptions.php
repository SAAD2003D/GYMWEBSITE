<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php'; ?>
  <link rel="stylesheet" href="stylesheets/courses.css">
  <link rel="stylesheet" href="stylesheets/subscriptions.css">
  <title>Subscriptions</title>
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

// Function to retrieve courses for a subscription
function find_courses($access_data)
{
    global $db;
    $course_data = $db->select('course', array('course_id' => $access_data['course_id']));
    return $course_data[0];
}

// Get subscriptions and associated courses
$subscriptions = $db->select('subscription', array('gym_id' => $_SESSION['gym_id']));
$course_map = array();

foreach ($subscriptions as $sub_data) {
    $courses = array_map('find_courses', $db->select('subscription_access', array('sub_id' => $sub_data['sub_id'])));
    $course_map[$sub_data['sub_id']] = $courses;
}

// Get all courses from the database
$courses = $db->select('course', array('gym_id' => $_SESSION['gym_id']));

?>
<main>
  <h1>Subscriptions <button data-id="add" class="icon-btn modal-btn"><i class="fa-regular fa-plus"></button></i></h1>
  <?php foreach ($subscriptions as $sub_data) {?>
    <div class="subscription-header" style="color: <?php echo $sub_data['display_color']; ?>">
      <h3 style="color: <?php echo $sub_data['display_color']; ?>">
        <?php echo ucwords(strtolower($sub_data['sub_name'])); ?>
        <!-- Edit subscription -->
        <button data-id="edit" class="icon-btn modal-btn" onclick="editSubscription(<?php echo htmlspecialchars(json_encode($sub_data)); ?>)"><i class="fa-solid fa-pen"></i></button>
        <!-- Delete subscription -->
        <form method="POST" action="form-processing/subscription.php" style="display:inline;">
          <input type="hidden" name="sub_id" value="<?php echo $sub_data['sub_id']; ?>">
          <button class="icon-btn" type="submit" name="delete_subscription"><i class="fa-solid fa-trash"></i></button>
        </form>
      </h3>
    </div>
    <div class="course-wrapper">
      <!-- Display courses for this subscription -->
      <?php foreach ($course_map[$sub_data['sub_id']] as $course_data) {?>
        <div class="course-card"><?php echo $course_data['course_name']; ?>
          <!-- Delete course from subscription -->
          <form method="POST" action="form-processing/subscription.php" style="display:inline;">
            <input type="hidden" name="sub_id" value="<?php echo $sub_data['sub_id']; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_data['course_id']; ?>">
            <button class="icon-btn" type="submit" name="delete_course_from_subscription"><i class="fa-solid fa-trash"></i></button>
          </form>
        </div>
      <?php }?>
      <!-- Add course to this subscription -->
      <div class="course-card add-new" onclick="(() => toggleModal('add_<?php echo $sub_data['sub_id']; ?>'))()"><i class="fa-regular fa-plus"></i> Add Course</div>
    </div>
  <?php }?>
  <div class="modal" id="add">
    <div class="container">
      <h1>Add Subscription</h1>
      <form method="POST" action="form-processing/subscription.php">
        <input type="hidden" name="gym_id" value="<?php echo $_SESSION['gym_id']; ?>">
        <input placeholder="Subscription Name" type="text" id="sub_name" name="sub_name" required>
        <input type="number" name="price" id="price" placeholder="Price">
        <input placeholder="Display Color" type="color" id="display_color" name="display_color" required>
        <button data-id="add" class="modal-btn form-btn" type="submit" name="add_subscription">Confirm</button>
      </form>
    </div>
  </div>

  <?php foreach ($subscriptions as $sub_data) {?>
    <div class="modal" id="add_<?php echo $sub_data['sub_id']; ?>">
      <div class="container">
        <h1>Add Course to <?php echo ucwords(strtolower($sub_data['sub_name'])); ?></h1>
        <form method="POST" action="form-processing/subscription.php">
          <input type="hidden" name="sub_id" value="<?php echo $sub_data['sub_id']; ?>">
          <select name="course_id" required>
            <option value="" disabled selected>Select Course</option>
            <?php foreach ($courses as $course) {
                $courseId = $course['course_id'];
                $courseName = $course['course_name'];
                $found = false;
                foreach ($course_map[$sub_data['sub_id']] as $c) {
                    if ($c['course_id'] == $courseId) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {?>
                  <option value="<?php echo $courseId; ?>"><?php echo $courseName; ?></option>
            <?php }
            }?>
          </select>
          <button class="form-btn" type="submit" name="add_course_to_subscription">Add Course</button>
        </form>
      </div>
    </div>
  <?php }?>
  <div class="modal" id="edit">
    <div class="container">
      <h1>Edit Subscription</h1>
      <form method="POST" action="form-processing/subscription.php">
        <input type="hidden" id="edit_sub_id" name="sub_id">
        <input placeholder="Subscription Name" type="text" id="edit_sub_name" name="sub_name" required>
        <input type="number" name="price" id="edit_price" placeholder="Price">
        <input placeholder="Display Color" type="color" id="edit_display_color" name="display_color" required>
        <button class="form-btn" type="submit" name="update_subscription">Update</button>
      </form>
    </div>
  </div>

</main>

<!-- Script for editing subscription -->
<script>
  function editSubscription(subscription) {
    var subIdInput = document.getElementById('edit_sub_id');
    var nameInput = document.getElementById('edit_sub_name');
    var priceInput = document.getElementById('edit_price');
    var colorInput = document.getElementById('edit_display_color');

    subIdInput.value = subscription.sub_id;
    nameInput.value = subscription.sub_name;
    priceInput.value = subscription.price
    colorInput.value = subscription.display_color;
  }
</script>

</body>
</html>

