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

    <div class="row justify-content-center row mb-3">
      <div class="col-md-6">
        <input type="text" id="searchInput" class="form-control" placeholder="Search Users...">
      </div>
    </div>


    <h2 class="mb-4">Users</h2>

    <div id="status" class="mb-3 text-muted"></div>

    <div class="table-responsive">
      <table id="ticketTable" class="table table-bordered table-hover table-light">
        <thead class="table-primary">
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Phone no</th>
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

    async function fetchUsers() {
      const search = document.getElementById('searchInput').value;
      const query = new URLSearchParams({search}).toString();
      try {
        const response = await fetch(`userinfo/get?${query}`, {
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
          const users = result.data;

          if (users.length === 0) {
            statusDiv.textContent = "No Users found.";
          } else {
            users.forEach(user => {
              const row = document.createElement('tr');
              row.style.cursor = 'pointer';
              // row.onclick = () => window.location.href = `/HelpDesk2/clientTicket?id=${ticket.id}`;

              row.innerHTML = `
                <td>${user.userid}</td>
                <td>${user.name}</td>
                <td>${user.role}</td>
                <td>${user.email}</td>
                <td>${user.phone}</td>
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

    fetchUsers();
    searchInput.addEventListener('input', fetchUsers);
  });
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
<!-- onclick="window.location.href='/HelpDesk2/clientTicket?id=${ticket.id}'" -->