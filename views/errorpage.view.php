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

    <h2 class="mb-4">Error Logs</h2>

    <div id="status" class="mb-3 text-muted"></div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="ticketTable" style="display: none;">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Error Message</th>
            <th>Error File</th>
            <th>Error Line</th>
            <th>Created At</th>
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
  async function fetchErrors() {
    try {
      const response = await fetch('errorlog/get', {
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
          statusDiv.textContent = "No errors found.";
        } else {
          tickets.forEach(ticket => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>#${ticket.id}</td>
              <td>${ticket.error_message}</td>
              <td>${ticket.error_file}</td>
              <td>${ticket.error_line}</td>
              <td>${ticket.created_at}</td>
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
  fetchErrors();
</script>
<?php require_once __DIR__ . '/components/footer.php'; ?>