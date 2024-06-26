<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php';?>
  <link rel="stylesheet" href="stylesheets/courses.css">
  <title>Courses</title>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['gym_id'])) {
    header("Location: login.php");
    exit();
}
include 'owner-sidebar.php';
include 'db.php';

// Gestion des soumissions du formulaire

$courses = $db->select('course', ['gym_id' => $_SESSION['gym_id']]);
?>
  <main>
    <h1>Courses</h1>
    <div class="course-wrapper">
      <?php foreach ($courses as $data) {?>
        <div class="course-card">
          <h4><?php echo $data['course_name'] ?></h4>
          <div class="actions">
            <button data-id="edit" class="icon-btn modal-btn" onclick="editCourse(<?php echo htmlspecialchars(json_encode($data)); ?>)"><i class="fa-solid fa-pen"></i></button>
            <form method="POST" action="form-processing/course.php" style="display:inline;">
              <input type="hidden" name="course_id" value="<?php echo $data['course_id']; ?>">
              <button class="icon-btn" type="submit" name="delete_course"><i class="fa-solid fa-trash"></i></button>
            </form>
          </div>
        </div>
      <?php }?>
      <div class="course-card add-new" onclick="(() => toggleModal('add'))()"><i class="fa-regular fa-plus"></i> Add New</div>
    </div>

    <div class="modal" id="add">
      <div class="container">
        <h1>Add Course</h1>
        <form method="POST" action="form-processing/course.php">
          <input type="hidden" name="gym_id" value="<?php echo $_SESSION['gym_id']; ?>">
          <input placeholder="Course Name" type="text" id="course_name" name="course_name" required>
          <button data-id="add" class="modal-btn form-btn" type="submit" name="add_course">Confirm</button>
        </form>
      </div>
    </div>

    <div class="modal" id="edit">
      <div class="container">
        <h1>Edit Course</h1>
        <form method="POST" action="form-processing/course.php">
          <input type="hidden" id="edit_course_id" name="course_id">
          <input type="text" id="edit_course_name" name="course_name" required>
          <button class="form-btn" type="submit" name="update_course">Update</button>
        </form>
      </div>
    </div>

  </main>
</body>
</html>
