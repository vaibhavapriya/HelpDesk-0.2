<?php
$isGuest = !isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token']);
$cardColClass = $isGuest ? 'col-md-3' : 'col-md-4';
?>

<div class="row g-4 mb-5">
  <?php if ($isGuest) { ?>
    <div class="<?= $cardColClass ?>">
      <a href="register" class="text-decoration-none text-dark">
        <div class="card text-center shadow-sm bg-light">
          <div class="card-body">
            <i class="fa-regular fa-pen-to-square fa-2x mb-2"></i>
            <h5 class="card-title">Register</h5>
          </div>
        </div>
      </a>
    </div>
  <?php } ?>

  <!-- Card 2 -->
  <div class="<?= $cardColClass ?>">
    <a href="newTicket" class="text-decoration-none text-dark">
      <div class="card text-center shadow-sm bg-light">
        <div class="card-body ">
          <i class="fa-solid fa-rectangle-list fa-2x mb-2"></i>
          <h5 class="card-title">Submit Ticket</h5>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 3 -->
  <div class="<?= $cardColClass ?>">
    <a href="myTickets" class="text-decoration-none text-dark">
      <div class="card text-center shadow-sm bg-light">
        <div class="card-body">
          <i class="fa-regular fa-newspaper fa-2x mb-2"></i>
          <h5 class="card-title">My Ticket</h5>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 4 -->
  <div class="<?= $cardColClass ?>">
    <a href="knowledgeBase" class="text-decoration-none text-dark ">
      <div class="card text-center shadow-sm bg-light">
        <div class="card-body">
          <i class="fa-solid fa-lightbulb  fa-2x mb-2"></i>
          <h5 class="card-title">Knowledgebase</h5>
        </div>
      </div>
    </a>
  </div>
</div>

