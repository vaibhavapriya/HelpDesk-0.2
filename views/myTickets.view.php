<?php require_once __DIR__ . '/components/clientheader.php';
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

    <h2 class="mb-4">My Tickets</h2>

    <div id="status" class="mb-3 text-muted"></div>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="ticketTable">
        <thead class="table-dark">
          <tr>
            <th scope="col">Ticket ID</th>
            <th scope="col">Subject</th>
            <th scope="col">Status</th>
            <th scope="col">Last Replier</th>
            <th scope="col">Last Activity</th>
          </tr>
        </thead>
        <tbody id="ticketTableBody">
          <!-- JS will populate rows here -->
        </tbody>
      </table>
    </div>

    <!-- <div id="ticketCardContainer" class="row gy-4">
    </div> -->
  </div>
</main>

<script>
  function getStatusBadge(status) {
    switch (status.toLowerCase()) {
      case 'open': return 'primary';
      case 'in progress': return 'warning';
      case 'closed': return 'success';
      case 'pending': return 'secondary';
      default: return 'dark';
    }
  }

  async function fetchMyTickets() {
    try {
      const response = await fetch('myTickets/get', {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      const statusDiv = document.getElementById('status');
      const tableBody = document.getElementById('ticketTableBody');
      tableBody.innerHTML = '';

      if (result.status === 'success') {
        const tickets = result.data;

        if (tickets.length === 0) {
          statusDiv.textContent = "No tickets found.";
        } else {
          tickets.forEach(ticket => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            row.onclick = () => window.location.href = `/HelpDesk2/clientTicket?id=${ticket.id}`;

            row.innerHTML = `
              <td>${ticket.id}</td>
              <td>${ticket.subject}</td>
              <td><span class="badge bg-${getStatusBadge(ticket.status)}">${ticket.status}</span></td>
              <td>${ticket.last_replier ?? '-'}</td>
              <td>${ticket.last_activity}</td>
            `;

            tableBody.appendChild(row);
          });
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

  fetchMyTickets();
</script>


<?php require_once __DIR__ . '/components/footer.php'; ?>
