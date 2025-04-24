<?php require_once __DIR__ . '/modules/header.php'; 
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk-0.2/login?error=" . urlencode("Please log in again."));
  exit;
}?>


<section class="content py-5">
  <div class="container">
    <h2 class="mb-4">Ticket Details</h2>

    <!-- Flash messages -->
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php endif; ?>

    <!-- Ticket Info Table -->
    <div class="table-responsive mb-4">
      <table class="table table-bordered table-hover">
        <tbody>
          <tr>
            <th scope="row">Ticket ID</th>
            <td>#<?= htmlspecialchars($ticket['id']) ?></td>
          </tr>
          <tr>
            <th scope="row">Subject</th>
            <td><?= htmlspecialchars($ticket['subject']) ?></td>
          </tr>
          <tr>
            <th scope="row">Message</th>
            <td><?= nl2br(htmlspecialchars($ticket['description'])) ?></td>
          </tr>
          <tr>
            <th scope="row">Last Replier</th>
            <td><?= htmlspecialchars($ticket['last_replier'] ?? 'N/A') ?></td>
          </tr>
          <tr>
            <th scope="row">Status</th>
            <td>
              <span class="badge bg-<?= $ticket['status'] === 'Open' ? 'success' : ($ticket['status'] === 'Closed' ? 'danger' : 'secondary') ?>">
                <?= htmlspecialchars($ticket['status']) ?>
              </span>
            </td>
          </tr>
          <tr>
            <th scope="row">Last Activity</th>
            <td><?= htmlspecialchars($ticket['last_activity']) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Attachment -->
    <h4>Attachment:</h4>
    <?php if (!empty($ticket['attachment'])): ?>
      <img src="/project/image.php?id=<?= htmlspecialchars($ticket['id']) ?>" 
           alt="Ticket Attachment" 
           class="img-fluid rounded border mb-3" 
           style="max-width: 400px;">
    <?php else: ?>
      <div class="alert alert-secondary">No attachment available.</div>
    <?php endif; ?>

    <!-- Back button -->
    <a href="/myTickets" class="btn btn-outline-primary mt-3">
      <i class="bi bi-arrow-left-circle"></i> Back to My Tickets
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/modules/footer.php'; ?>
