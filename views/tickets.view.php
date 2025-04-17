<?php 
require_once __DIR__ . '/components/header.php'; 
require_once __DIR__ . '/components/sidebar.php';
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk2/login?error=" . urlencode("Please log in again."));
  exit;
}?>

<main class="container py-5">
  <div class="container">

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

    <h2 class="mb-4"> Tickets</h2>

    <div id="status" class="mb-3 text-muted"></div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="ticketTable" style="display: none;">
        <thead class="table-dark">
          <tr>
            <th scope="col">Ticket ID</th>
            <th scope="col">Subject</th>
            <th scope="col">Requester</th>
            <th scope="col">Status</th>
            <th scope="col">Last Replier</th>
            <th scope="col">Last Activity</th>
          </tr>
        </thead>
        <tbody>
          <!-- Rows will be inserted dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
  async function fetchTickets() {
    try {
      const response = await fetch('tickets/get', {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      const statusDiv = document.getElementById('status');
      const table = document.getElementById('ticketTable');
      const tbody = table.querySelector('tbody');
      tbody.innerHTML = ''; // Clear existing rows

      if (result.status === 'success') {
        const tickets = result.data;

        if (tickets.length === 0) {
          statusDiv.textContent = "No tickets found.";
        } else {
          tickets.forEach(ticket => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${ticket.id}</td>
              <td>${ticket.subject}</td>
              <td>${ticket.requester}</td>
              <td>${ticket.status}</td>
              <td>${ticket.last_replier ?? '-'}</td>
              <td>${ticket.last_activity}</td>
            `;
            tbody.appendChild(row);
          });
          table.style.display = 'table';
        }
      } else {
        statusDiv.className = 'text-danger';
        statusDiv.textContent = result.message || 'Something went wrong.';
      }

    } catch (error) {
      console.error(error);
      document.getElementById('status').textContent = 'Server error. Please try again.';
    }
  }

  // Call the function on load
  fetchTickets();
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>