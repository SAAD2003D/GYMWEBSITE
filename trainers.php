<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php'; ?>
  <link rel="stylesheet" href="stylesheets/dashboard.css">
  <title>Trainers</title>
</head>
<body>
<?php
  session_start();
  if (!isset($_SESSION['gym_id'])) {
      header("Location: login.php");
      exit();
  }
  include 'db.php'; // Inclure le fichier de connexion à la base de données

  $trainers = $db->select('trainer', ['gym_id' => $_SESSION['gym_id']]);
?>
  <?php include 'owner-sidebar.php'; ?>
  <main>
    <h1><?php echo htmlspecialchars("{$_SESSION['gym_name']} Trainer Management"); ?></h1>
    
    <h3>Trainers <button class="icon-btn modal-btn" data-id="add"><i class="fa-solid fa-plus"></i></button></h3>
    <table class="trainers-table">
      <tbody class="table-wrapper trainers">
        <tr>
          <th>Full Name</th>
          <th>Actions</th>
        </tr>
        <?php foreach ($trainers as $data) { ?>
          <tr>
            <td><?php echo htmlspecialchars($data['first_name'] . ' ' . $data['last_name']); ?></td>
            <td>
              <button data-id="edit" class="icon-btn modal-btn" onclick="editTrainer(<?php echo htmlspecialchars(json_encode($data)); ?>)"><i class="fa-solid fa-pen"></i></button>
              <form method="POST" action="form-processing/trainer.php" style="display:inline;">
                <input type="hidden" name="trainer_id" value="<?php echo $data['trainer_id']; ?>">
                <button type="submit" class="icon-btn" name="delete_trainer"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <div id="add" class="modal">
      <div class="container">
        <h1>Add Trainer</h1>
        <form method="POST" action="form-processing/trainer.php">
          <input type="hidden" name="gym_id" value="<?php echo $_SESSION['gym_id']; ?>">
          <input placeholder="First Name" type="text" id="first_name" name="first_name" required>
          <input placeholder="Last Name" type="text" id="last_name" name="last_name" required>
          <button type="submit" class="modal-btn form-btn" name="add_trainer">Add Trainer</button>
        </form>
      </div>
    </div>

    <div id="edit" class="modal">
      <div class="container">
        <h1>Edit Trainer</h1>
        <form method="POST" action="form-processing/trainer.php">
          <input type="hidden" id="edit_trainer_id" name="trainer_id">
          <input placeholder="First Name" type="text" id="edit_first_name" name="first_name" required>
          <input placeholder="Last Name" type="text" id="edit_last_name" name="last_name" required>
          <button type="submit" class="modal-btn form-btn" name="update_trainer">Update Trainer</button>
        </form>
      </div>
    </div>

    <script>
      function editTrainer(data) {
        document.getElementById('edit_trainer_id').value = data.trainer_id;
        document.getElementById('edit_first_name').value = data.first_name;
        document.getElementById('edit_last_name').value = data.last_name;
      }
    </script>
  </main>
</body>
</html>
