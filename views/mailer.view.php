<div class="wrapper">
  <?php require_once 'components/header.php'; ?>
  <?php require_once 'components/sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">

        <main>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Email Configuration</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEmailModal">
              <i class="fas fa-plus"></i> Add Email
            </button>
          </div>

          <!-- Table -->
          <div class="card">
            <div class="card-body table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Active</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Example data - dynamically generate from DB -->
                  <?php
                  // Sample: Replace with real DB results
                  $emails = [
                    ['id' => 1, 'email' => 'user1@gmail.com', 'name' => 'Support', 'active' => true],
                    ['id' => 2, 'email' => 'user2@gmail.com', 'name' => 'Billing', 'active' => false],
                  ];
                  foreach ($emails as $email) {
                  ?>
                    <tr>
                      <td>
                        <input type="radio" name="active_email" <?= $email['active'] ? 'checked' : '' ?> data-id="<?= $email['id'] ?>" class="set-active-email">
                      </td>
                      <td><?= htmlspecialchars($email['email']) ?></td>
                      <td><?= htmlspecialchars($email['name']) ?></td>
                      <td>
                        <button class="btn btn-danger btn-sm delete-email" data-id="<?= $email['id'] ?>">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </main>

      </div>
    </section>
  </div>

  <?php require_once 'components/footer.php'; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-labelledby="addEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="email_add_handler.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Email</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Sender Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>App Password</label>
            <input type="password" name="passcode" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
