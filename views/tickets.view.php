<?php 
require_once __DIR__ . '/components/header.php'; 
require_once __DIR__ . '/components/sidebar.php';
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk-0.2/login?error=" . urlencode("Please log in again."));
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

    <div class="row justify-content-center row mb-3">
      <div class="col-md-6">
        <input type="text" id="searchInput" class="form-control" placeholder="Search tickets...">
      </div>
      <div class="col-md-3">
        <select id="statusFilter" class="form-select">
          <option value="all">All Statuses</option>
          <option value="open">Open</option>
          <option value="closed">Closed</option>
        </select>
      </div>
    </div>


    <h2 class="mb-4">Tickets</h2>

    <div id="status" class="mb-3 text-muted"></div>

    <div class="table-responsive">
      <table id="ticketTable" class="table table-bordered table-hover table-light">
        <thead class="table-primary">
          <tr>
            <th>Ticket ID</th>
            <th>Subject</th>
            <th>Requester</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Last Replier</th>
            <th>Last Activity</th>
          </tr>
        </thead>
        <tbody id="ticketTableBody">
          <!-- JS will populate this -->
        </tbody>
      </table>
    </div>

  </div>
</main>

<script>

  document.addEventListener('DOMContentLoaded', function () {
    function getStatusBadge(status) {
      switch (status.toLowerCase()) {
        case 'open': return 'primary';
        case 'in progress': return 'warning';
        case 'closed': return 'success';
        case 'pending': return 'secondary';
        default: return 'dark';
      }
    }

    function getPriorityBadge(priority) {
      switch (priority.toLowerCase()) {
        case 'high': return 'danger';
        case 'medium': return 'warning';
        case 'low': return 'success';
        default: return 'secondary';
      }
    }

    async function fetchTickets() {
      const search = document.getElementById('searchInput').value;
      const status = document.getElementById('statusFilter').value;
      const query = new URLSearchParams({
        status,
        search
      }).toString();
      try {
        const response = await fetch(`tickets/get?${query}`, {
          method: 'GET',
          headers: {
            'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
          }
        });

        const result = await response.json();
        const statusDiv = document.getElementById('status');
        const tableBody = document.getElementById('ticketTableBody');
        tableBody.innerHTML = '';
        statusDiv.textContent = '';
        if (result.status === 'success') {
          const tickets = result.data;

          if (tickets.length === 0) {
            statusDiv.textContent = "No tickets found.";
          } else {
            tickets.forEach(ticket => {
              const row = document.createElement('tr');
              row.style.cursor = 'pointer';
              // row.onclick = () => window.location.href = `/HelpDesk2/clientTicket?id=${ticket.id}`;

              row.innerHTML = `
                <td>${ticket.id}</td>
                <td>${ticket.subject}</td>
                <td>${ticket.requester}</td>
                <td><span class="badge bg-${getPriorityBadge(ticket.priority)}">${ticket.priority}</span></td>
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

    fetchTickets();
    searchInput.addEventListener('input', fetchTickets);
    statusFilter.addEventListener('change', fetchTickets);
  });
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
<!-- onclick="window.location.href='/HelpDesk2/clientTicket?id=${ticket.id}'" -->