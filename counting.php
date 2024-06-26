<?php
include 'db.php';

function count_table($table, $gym_id) {
    global $db;
    $query = $db->select($table, array('gym_id' => $gym_id));
    return count($query);
}

function count_trainers($conn) {
    $sql = "SELECT COUNT(*) AS count FROM trainers";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["count"];
    } else {
        return 0;
    }
}

function count_courses($conn) {
    $sql = "SELECT COUNT(*) AS count FROM courses";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["count"];
    } else {
        return 0;
    }
}

?>
