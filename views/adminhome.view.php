<div class="wrapper">
  <?php require_once 'components/header.php'; ?>
  <?php require_once 'components/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">
        <!-- Your main content here -->
        <main>
          <div class="row">
            <!-- Example Card 1 -->
            <div class="col-md-4 mb-4">
              <a  href="userprofile?id=<?php echo urlencode($_SESSION['email']);?>"class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-user fa-2x text-primary me-3"></i>
                    <h5 class="mb-0">User Profile</h5>
                  </div>
                </div>
              </a>
            </div>

            <!-- Example Card 2 -->
            <div class="col-md-4 mb-4">
              <a href="tickets?status=Open" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-ticket-alt fa-2x text-success me-3"></i>
                    <h5 class="mb-0">Open Tickets</h5>
                  </div>
                </div>
              </a>
            </div>

            <!-- Example Card 2
            <div class="col-md-4 mb-4">
              <a href="mytickets" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-ticket-alt fa-2x text-success me-3"></i>
                    <h5 class="mb-0">My Tickets</h5>
                  </div>
                </div>
              </a>
            </div> -->

            <!-- Example Card 2
            <div class="col-md-4 mb-4">
              <a href="mytickets" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-ticket-alt fa-2x text-success me-3"></i>
                    <h5 class="mb-0">Unreplied tickets</h5>Tickets</h5>
                  </div>
                </div>
              </a>
            </div> -->

            <!-- Example Card 3 -->
            <div class="col-md-4 mb-4">
              <a href="errorlog" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-bug fa-2x text-warning me-3"></i>
                    <h5 class="mb-0">Error logs</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </main>

      </div>
    </section>
  </div>
  <?php require_once __DIR__ . '/components/footer.php'; ?>
</div>

