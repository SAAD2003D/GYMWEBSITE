<?php session_start();
if (!isset($_SESSION['gym_id'])) {
    $redirect_url = "login.php"; // Replace param_name and param_value with your desired parameter name and value
    header("Location: $redirect_url");
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head_template.php' ?>
  <link rel="stylesheet" href="stylesheets/sessions.css">
  <link rel="stylesheet" href="stylesheets/courses.css">
  <link rel="stylesheet" href="stylesheets/member-homepage.css">
  <link rel="stylesheet" href="stylesheets/member-subscriptions.css">
  <title>Subscriptions</title>
</head>
<body>
  <?php include 'member-sidebar.php';
  include 'db.php';
  $member = $db->select('member', array('member_id' => $_SESSION['member_id']))[0];
  $subscription_ended = $member['sub_end_date'] < date('Y-m-d H:i:s');
  $user_sub = null;
  if (isset($member['sub_id'])) {
      $user_sub = $db->select('subscription', array('sub_id' => $member['sub_id']))[0];
  }
  $subs = $db->select('subscription', array('gym_id' => $_SESSION['gym_id']));

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

  ?>
  <main>
    <h1>Subscriptions</h1>
    <?php 
  if ($member['sub_id'] === null) {
  } else {
      echo "<h3>Your Subscription Status</h3>";
      $subInfoMsg = $subscription_ended ? "Your subscription has ended on ".$member['sub_end_date']."." : "Valid from <span class=\"sub-date\">" . $member['sub_start_date'] . " </span> until <span class=\"sub-date\">" . $member['sub_end_date']."</span>";
    ?>
    <div class="sub-wrapper">
        <div class="sub-card <?php if ($subscription_ended) echo 'subscription-ended' ?>">
          <p class="sub-name" style="<?php if(!$subscription_ended) echo "color: ".$user_sub['display_color'] ?>"><?php echo $user_sub['sub_name'] ?></p>
          <p><?php echo $subInfoMsg ?></p>
        </div>
    </div>
  <?php } ?>
    <h3>Available Subscriptions</h3>
    <h4>These are other membership options offered by the gym administration. Click on a card for more details.</h4>
    <div class="sub-wrapper available-subs">
      <?php foreach($subs as $sub) { ?>
        <div data-id="<?php echo $sub['sub_name']."-modal" ?>" class="sub-card modal-btn <?php echo $sub['sub_name'] ?>" style="--hover-color: <?php echo $sub['display_color'] ?>">
          <p class="sub-name" style="color: <?php echo $sub['display_color'] ?>"><?php echo $sub['sub_name']?></p>
          <p class="sub-price"><?php echo $sub['price']?></p>
        </div>
      <?php } ?>
    </div>
  </main>
  <?php foreach($subs as $sub) { 
    $courses = $course_map[$sub['sub_id']];
  ?>
    <div class="modal sub-modal" id="<?php echo $sub['sub_name']."-modal" ?>">
      <div class="container">
        <h1 class="sub-name" style="color: <?php echo $sub['display_color'] ?>"><?php echo $sub['sub_name']?></h1>
        <h4>The following courses are included with this membership:</h4>
        <div class="course-wrapper">
          <?php foreach($courses as $course) { ?>
            <div class="course-card"><?php echo $course['course_name']; ?></div>
          <?php } ?>
        </div>
        <button class="form-btn" onclick="showPaymentModal('<?php echo $sub['sub_id']; ?>', '<?php echo $sub['sub_name']."-modal"; ?>')">Subscribe</button>
      </div>
    </div>
  <?php } ?>
  
  <!-- Payment modal -->
  <div class="modal" id="payment-modal">
    <div class="container">
      <h1>Payment Information</h1>
      <form method="POST" action="form-processing/subscribe.php">
        <input type="hidden" id="payment-sub-id" name="sub_id">
        <label for="card-number">Card Number</label>
        <input type="text" id="card-number" name="card_number" required>
        <label for="expiry-date">Expiry Date</label>
        <input type="date" id="expiry-date" name="expiry_date" required>
        <label for="cvv">CVV</label>
        <input type="number" id="cvv" name="cvv" inputmode="numeric" maxlength="3" required>
        <label for="months">Amount of months</label>
        <input type="number" name="months" step="1" default="1">
        <button name="subscribe" type="submit" class="form-btn submit-btn">Submit Payment</button>
        <button type="button" class="form-btn cancel-btn" onclick="hidePaymentModal()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    function showPaymentModal(subId, subModalId) {
      document.getElementById('payment-sub-id').value = subId;
      document.getElementById('payment-modal').style.display = 'block';
      document.getElementById(subModalId).style.display = 'none';
    }

    function hidePaymentModal() {
      document.getElementById('payment-modal').style.display = 'none';
    }

    // Script for showing subscription modals
    document.querySelectorAll('.modal-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const modalId = btn.getAttribute('data-id');
        document.getElementById(modalId).style.display = 'block';
      });
    });

    // Script for hiding all modals when clicking outside
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }
  </script>
</body>
</html>
