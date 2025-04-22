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
            <th></th>
            <th></th>
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
  async function deleteTicket(id) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    try {
      const response = await fetch(`deleteTicket/post?id=${id}`, {
        method: 'POST', // Or 'POST' if your backend requires
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      if (result.status === 'success') {
        document.getElementById('status').textContent = 'User deleted successfully.';
        fetchTickets(); // Refresh the table
      } else {
        document.getElementById('status').textContent = result.message || 'Failed to delete user.';
      }
    } catch (error) {
      console.error(error);
      document.getElementById('status').textContent = 'Server error. Please try again.';
    }
  }
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
  async function fetchTickets(search,status) {
    const urlParams = new URLSearchParams(window.location.search); // Get query parameters
    const currentStatus = urlParams.get('status') || status;  // Get 'status' from the URL (or use passed 'status')

    const query = new URLSearchParams({
      status: currentStatus,
      search
    }).toString();
      // const query = new URLSearchParams({
      //   status,
      //   search
      // }).toString();
      try {
        loadingIndicator.style.display = 'block';
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
                <td style="cursor: pointer;" onclick="showLoadingAndRedirect('/HelpDesk-0.2/replyTicket?id=${ticket.id}')">${ticket.subject}</td>
                <td onclick="showLoadingAndRedirect('/HelpDesk-0.2/userprofile?id=${ticket.requester}')">${ticket.requester}</td>
                <td><span class="badge bg-${getPriorityBadge(ticket.priority)}">${ticket.priority}</span></td>
                <td><span class="badge bg-${getStatusBadge(ticket.status)}" onclick="statusview('${ticket.status}')">${ticket.status}</span></td>
                <td onclick="showLoadingAndRedirect('/HelpDesk-0.2/userprofile?id=${ticket.last_replier ?? '-'}')">${ticket.last_replier ?? '-'}</td>
                <td>${ticket.last_activity}</td>
                <td style="cursor: pointer;" onclick="showLoadingAndRedirect('/HelpDesk-0.2/replyTicket?id=${ticket.id}')">
                  <i class="fa-solid fa-pen-to-square"></i>
                </td>
                <td  style="cursor: pointer;" onclick="deleteTicket(${ticket.id})"><i class="fa-solid fa-trash"></i></td>
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
      } finally {
        // Hide loader
        loadingIndicator.style.display = 'none';
      }
  }
  function statusview(status){
      document.getElementById('statusFilter').value = status;
      fetchTickets("",status);
  }
  document.getElementById('searchInput').onchange
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');

    // Initial fetch
    fetchTickets(searchInput.value, statusFilter.value);

    // On search input
    searchInput.addEventListener('input', () => {
      fetchTickets(searchInput.value, statusFilter.value);
    });

    // On status filter change
    statusFilter.addEventListener('change', () => {
      fetchTickets(searchInput.value, statusFilter.value);
    });
  });


</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
<!-- onclick="window.location.href='/HelpDesk2/clientTicket?id=${ticket.id}'" -->