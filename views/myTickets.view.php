<?php require_once __DIR__ . '/components/clientheader.php';
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
            <th></th>
            <th></th>
            <th></th>
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

            row.innerHTML = `
              <td>${ticket.id}</td>
              <td>${ticket.subject}</td>
              <td><span class="badge bg-${getStatusBadge(ticket.status)}">${ticket.status}</span></td>
              <td>${ticket.last_replier ?? '-'}</td>
              <td>${ticket.last_activity}</td>
              <td style="cursor: pointer;" onclick="showLoadingAndRedirect('/HelpDesk-0.2/clientTicket?id=${ticket.id}')">
                <i class="fa-solid fa-eye"></i>
              </td>
              <td style="cursor: pointer;" onclick="showLoadingAndRedirect('/HelpDesk-0.2/editTicket?id=${ticket.id}')">
                <i class="fa-solid fa-pen-to-square"></i>
              </td>
              <td style="cursor: pointer;" onclick="deleteTicket(${ticket.id})"><i class="fa-solid fa-trash"></i></td>
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
  function deleteTicket($x){
    
  }
</script>


<?php require_once __DIR__ . '/components/footer.php'; ?>
