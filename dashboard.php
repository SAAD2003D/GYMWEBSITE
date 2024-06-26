<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php'; ?>
  <link rel="stylesheet" href="stylesheets/dashboard.css">
  <title>Dashboard</title>
</head>
<body>
<?php
  session_start();
  if (!isset($_SESSION['gym_name']) || !isset($_SESSION['gym_id'])) {
      header("Location: login.php");
      exit();
  }
  include 'counting.php';

  $members = $db->select('member', ['gym_id' => $_SESSION['gym_id']]);
?>
  <?php include 'owner-sidebar.php'; 
    ?>
  <main>
    <h1><?php echo htmlspecialchars("{$_SESSION['gym_name']} Dashboard"); ?></h1>
    <h3>Statistics</h3>
    <div class="statistics">
      <div class="statistics-card">
        <i class="fa-solid fa-users fa-xl stat-icon"></i>
        <div class="stat-card-body">
          <div class="stat-label">Members</div>
          <div class="stat-count"><?php echo count_table('member', $_SESSION['gym_id']); ?></div>
        </div>
      </div>
      <div class="statistics-card">
        <i class="fa-solid fa-dumbbell fa-xl stat-icon"></i>
        <div class="stat-card-body">
          <div class="stat-label">Trainers</div>
          <div class="stat-count"><?php echo count_table('trainer', $_SESSION['gym_id']); ?></div>
        </div>
      </div>
      <div class="statistics-card">
        <i class="fa-solid fa-book fa-xl stat-icon"></i>
        <div class="stat-card-body">
          <div class="stat-label">Courses</div>
          <div class="stat-count"><?php echo count_table('course', $_SESSION['gym_id']); ?></div>
        </div>
      </div>
    </div>

    <h3>Members <button class="icon-btn modal-btn" data-id="add"><i class="fa-solid fa-plus"></i></button></h3>
    <table class="members-table">
  <tbody class="table-wrapper">
    <tr>
      <th>Full Name</th>
      <th>Age</th>
      <th>Gender</th>
      <th>Subscription  </th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php 
    foreach ($members as $data) { 
      // Check if sub_id is null
      if (is_null($data['sub_id'])) {
        $subscription_name = 'None';
        $status = '';
        $display_color = '#000'; // Default color for no subscription
      } else {
        // Fetch the subscription data
        $sub_data = $db->select('subscription', array('sub_id' => $data['sub_id']))[0];
        $subscription_name = $sub_data['sub_name'];
        $display_color = $sub_data['display_color'];

        // Determine status
        $current_date = date('Y-m-d H:i:s');
        $status = ($data['sub_end_date'] >= $current_date) ? 'Active' : 'Inactive';
      }
    ?>
      <tr>
        <td><?php echo htmlspecialchars($data['first_name'] . ' ' . $data['last_name']); ?></td>
        <td><?php echo htmlspecialchars($data['age']); ?></td>
        <td><?php echo htmlspecialchars($data['gender']); ?></td>
        <td style="color: <?php echo htmlspecialchars($display_color); ?>;"><?php echo htmlspecialchars($subscription_name); ?></td>
        <td><?php echo htmlspecialchars($status); ?></td>
        <td>
          <button data-id="edit" class="icon-btn modal-btn" onclick="editMember(<?php echo htmlspecialchars(json_encode($data)); ?>)"><i class="fa-solid fa-pen"></i></button>
          <form method="POST" action="form-processing/member.php" style="display:inline;">
            <input type="hidden" name="member_id" value="<?php echo $data['member_id']; ?>">
            <button type="submit" class="icon-btn" name="delete_member"><i class="fa-solid fa-trash"></i></button>
          </form>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>


    <div id="add" class="modal">
      <div class="container">
        <h1>Add Member</h1>
        <form method="POST" action="form-processing/member.php">
          <input type="hidden" name="gym_id" value="<?php echo $_SESSION['gym_id']; ?>">
          <input placeholder="First Name" type="text" id="first_name" name="first_name" required>
          <input placeholder="Last Name" type="text" id="last_name" name="last_name" required>
          <input placeholder="Age" type="number" id="age" name="age" required>
          <select placeholder="Gender" type="text" id="gender" name="gender" required>
            <option hidden selected disabled value="">Gender</option>
            <option value="M">M</option>
            <option value="F">F</option>
          </select>
          <input type="email" placeholder="Email" id="email" name="email">
          <button type="submit" class="modal-btn form-btn" name="add_member">Add Member</button>
        </form>
      </div>
    </div>

    <div class="modal" id="edit">
      <div class="container">
        <h1>Edit Member</h1>
        <form method="POST" action="form-processing/member.php">
          <input type="hidden" id="edit_member_id" name="member_id">
          <input placeholder="First Name" type="text" id="edit_first_name" name="first_name" required>
          <input placeholder="Last Name" type="text" id="edit_last_name" name="last_name" required>
          <input placeholder="Age" type="number" id="edit_age" name="age" required>
          <select placeholder="Gender" type="text" id="gender" name="gender" required>
            <option hidden selected disabled value="">Gender</option>
            <option value="M">M</option>
            <option value="F">F</option>
          </select>
          <button type="submit" class="modal-btn form-btn" name="update_member">Update</button>
        </form>
      </div>
    </div>

    <script>
      function editMember(data) {
        document.getElementById('edit_member_id').value = data.member_id;
        document.getElementById('edit_first_name').value = data.first_name;
        document.getElementById('edit_last_name').value = data.last_name;
        document.getElementById('edit_age').value = data.age;
        document.getElementById('edit_gender').value = data.gender;
      }
    </script>
  </main>
</body>
</html>
