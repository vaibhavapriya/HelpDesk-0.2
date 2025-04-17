<?php require_once __DIR__ . '/components/clientheader.php'; ?>


<main class="content py-5">
    <div class="container">
        <h1 class="mb-4">Ticket Details</h1>

        <!-- Display error and success messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Ticket Details Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <tbody>
            <tr><th>Ticket ID</th><td id="ticketId"></td></tr>
            <tr><th>Subject</th><td id="subject"></td></tr>
            <tr><th>Description</th><td id="description"></td></tr>
            <tr><th>Last Replier</th><td id="lastReplier"></td></tr>
            <tr><th>Status</th><td id="status"></td></tr>
            <tr><th>Last Activity</th><td id="lastActivity"></td></tr>
            </tbody>

            <!-- Attachment -->
            <div class="mb-4">
            <h3>Attachment:</h3>
            <div id="attachmentContainer"></div>
            </div>

            </table>
        </div>

        <!-- Attachment Section -->
        <div class="mb-4">
            <h3>Attachment:</h3>
            <?php if (!empty($ticket['attachment'])): ?>
                <img src="/project/image.php?id=<?= htmlspecialchars($ticket['id']) ?>" alt="Ticket Attachment" class="img-fluid" style="max-width: 400px;">
            <?php else: ?>
                <p>No attachment available.</p>
            <?php endif; ?>
        </div>

        <!-- Back Button -->
        <a href="/project/myTickets" class="btn btn-secondary">Back to My Tickets</a>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const ticketId = urlParams.get('id');

  async function fetchTicket() {
    try {
      const response = await fetch(`clientTicket/get?id=${ticketId}`, {
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      if (result.status === 'success') {
        const ticket = result.data;
        document.getElementById('ticketId').textContent = `#${ticket.id}`;
        document.getElementById('subject').textContent = ticket.subject;
        document.getElementById('description').innerHTML = ticket.description.replace(/\n/g, '<br>');
        document.getElementById('lastReplier').textContent = ticket.last_replier ?? 'N/A';
        document.getElementById('status').textContent = ticket.status;
        document.getElementById('lastActivity').textContent = ticket.last_activity;

        const attachmentContainer = document.getElementById('attachmentContainer');
        if (ticket.attachment) {
          attachmentContainer.innerHTML = `<img src="/image?id=${ticket.id}" class="img-fluid" style="max-width: 400px;">`;
        } else {
          attachmentContainer.textContent = "No attachment available.";
        }

      } else {
        alert(result.message || "Failed to load ticket.");
      }

    } catch (err) {
      console.error(err);
      alert("Error fetching ticket details.");
    }
  }

  fetchTicket();
});
</script>


<?php require_once __DIR__ . '/components/footer.php'; ?>