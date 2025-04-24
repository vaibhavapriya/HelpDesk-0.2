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
                <td><span class="editable" data-field="name" onclick="showLoadingAndRedirect('/HelpDesk-0.2/userprofile?id=${user.email}')">${user.name}</span></td>
                <td><span class="editable" data-field="role">${user.role}</span></td>
                <td><span class="editable" data-field="email" onclick="showLoadingAndRedirect('/HelpDesk-0.2/userprofile?id=${user.email}')">${user.email}</span></td>
                <td><span class="editable" data-field="phone">${user.phone}</span></td>
                <td><i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEditing(this, ${user.userid})"></i></td>
                <td><i class="fa-solid fa-trash text-danger" onclick="deleteUser(${user.userid})"></i></td>
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

  function enableEditing(icon, userid) {
    const row = icon.closest('tr');
    const fields = row.querySelectorAll('.editable');

    fields.forEach(cell => {
      const field = cell.getAttribute('data-field');
      const value = cell.textContent;

      let inputEl;

      if (field === 'role') {
        inputEl = document.createElement('select');
        ['admin', 'agent', 'client'].forEach(role => {
          const option = document.createElement('option');
          option.value = role;
          option.text = role.charAt(0).toUpperCase() + role.slice(1);
          if (role === value.toLowerCase()) option.selected = true;
          inputEl.appendChild(option);
        });
      } else {
        inputEl = document.createElement('input');
        inputEl.type = 'text';
        inputEl.value = value;
      }

      inputEl.classList.add('form-control', 'form-control-sm');
      inputEl.setAttribute('name', field);
      cell.innerHTML = '';
      cell.appendChild(inputEl);
    });

    // Replace edit icon with save icon
    icon.classList.remove('fa-pen-to-square');
    icon.classList.add('fa-floppy-disk');
    icon.onclick = () => saveRow(row, userid);
  }
  async function saveRow(row, userid) {
    const inputs = row.querySelectorAll('input, select');
    const updatedData = { userid };

    inputs.forEach(input => {
      updatedData[input.name] = input.value;
    });

    try {
      const response = await fetch('editUser/post', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        },
        body: JSON.stringify(updatedData)
      });

      const result = await response.json();
      document.getElementById('status').textContent = result.message;

    } catch (error) {
      console.error(error);
      document.getElementById('status').textContent = 'Server error. Please try again.';
    }

    // Replace inputs with plain text
    inputs.forEach(input => {
      const span = document.createElement('span');
      span.classList.add('editable');
      span.setAttribute('data-field', input.name);
      span.textContent = input.value;
      input.parentNode.replaceWith(span);
    });

    // Replace save icon with edit icon
    const saveIcon = row.querySelector('.fa-floppy-disk');
    saveIcon.classList.remove('fa-floppy-disk');
    saveIcon.classList.add('fa-pen-to-square');
    saveIcon.onclick = () => enableEditing(saveIcon, userid);
  }
  async function deleteUser(userid) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    try {
      const response = await fetch(`deleteUser/post?id=${userid}`, {
        method: 'DELETE', // Or 'POST' if your backend requires
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      if (result.status === 'success') {
        document.getElementById('status').textContent = 'User deleted successfully.';
        fetchUsers(); // Refresh the table
      } else {
        document.getElementById('status').textContent = result.message || 'Failed to delete user.';
      }
    } catch (error) {
      console.error(error);
      document.getElementById('status').textContent = 'Server error. Please try again.';
    }
  }

</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
<!-- onclick="window.location.href='/HelpDesk2/clientTicket?id=${ticket.id}'" -->