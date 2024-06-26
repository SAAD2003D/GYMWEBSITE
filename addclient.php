<?php
  session_start();
  $firstName = $_SESSION['first_name'];
  $lastName = $_SESSION['last_name'];
  echo "Hello $firstName $lastName";

  
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_client'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $gym_id = $_POST['gym_id']; // Assuming you have a gym ID available
    $subscription_number = $_POST['subscription_number'];
    $subscription_type = $_POST['subscription_type'];

    $stmt = $conn->prepare("INSERT INTO clients (full_name, email, gym_id, subscription_number, subscription_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $full_name, $email, $gym_id, $subscription_number, $subscription_type);

    if ($stmt->execute()) {
        echo "Client added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}


?>