<?php
  session_start();
  $firstName = $_SESSION['first_name'];
  $lastName = $_SESSION['last_name'];
  echo "Hello $firstName $lastName";

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_trainer'])) {
    $full_name = $_POST['full_name'];
    $task = $_POST['task'];
    $email = $_POST['email'];
    $gym_id = $_POST['gym_id']; // Assuming you have a gym ID available

    
    $stmt->bind_param("sssi", $full_name, $task, $email, $gym_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Trainer added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    
    $stmt->close();}
?>