<?php
session_start();
$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];
echo "Hello $firstName $lastName";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];
    $course_time = $_POST['course_time'];
    $trainer_id = $_POST['trainer_id']; // Assuming you have the trainer ID available

    $stmt = $conn->prepare("INSERT INTO courses (course_name, course_time, trainer_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $course_name, $course_time, $trainer_id);

    if ($stmt->execute()) {
        echo "Course added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
