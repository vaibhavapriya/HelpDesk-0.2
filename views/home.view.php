<?php 
require_once __DIR__ . '/components/clientheader.php'; ?>
  <main class="d-flex align-items-center justify-content-center ">
    <div class="container py-5">
  <?php 
  
        // Display error messages from GET parameters
        if (isset($_GET['error'])) {
            // echo "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($_GET['error']) . "</div>";
            ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                <div>
                    <?php echo htmlspecialchars($_GET['error']) ?>
                </div>
            </div>
            <?php
        }
        if (isset($_GET['success'])) {
            // echo "<div class='alert alert-success' role='alert'>" . htmlspecialchars($_GET['success']) . "</div>";
            ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                <div>
                    <?php echo htmlspecialchars($_GET['success']) ?>
                </div>
            </div>
            <?php
        }
        require_once __DIR__ . '/components/homegrid.php'; 
    ?>
    </div>
  </main>
    
<?php require_once __DIR__ . '/components/footer.php'; ?>