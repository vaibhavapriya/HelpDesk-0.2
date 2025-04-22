<div class="wrapper">
  <?php require_once 'components/header.php'; ?>
  <?php require_once 'components/sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">

        <main>
        <div id="success" role="alert"></div>
        <div id="error" role="alert"></div>
          <div class="d-flex justify-content-between align-items-center mb-3">

            <h2>Email Configuration</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEmailModal">
              <i class="fas fa-plus"></i> Add Email
            </button>
          </div>

          <!-- Table -->
          <div class="card">
            <div class="card-body table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Active</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="emailTableBody">
                  <!-- Dynamically populated emails will appear here -->
                </tbody>
              </table>
            </div>
          </div>
        </main>

      </div>
    </section>
  </div>
</div>

<script>
  async function fetchEmails() {
    try {
      const response = await fetch('mc/get', { // Adjust the endpoint as needed
        method: 'GET',
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();
      const statusDiv = document.getElementById('status');
      const tableBody = document.getElementById('emailTableBody');
      tableBody.innerHTML = '';

      if (result.status === 'success') {
        const emails = result.data;

        if (emails.length === 0) {
          statusDiv.textContent = "No emails found.";
        } else {
          emails.forEach(email => {
            const row = document.createElement('tr');

            row.innerHTML = `
              <td>
                <input type="radio" name="active_email" ${email.active ? 'checked' : ''} data-id="${email.id}" class="set-active-email">
              </td>
              <td>${email.email}</td>
              <td>${email.name}</td>
              <td>
                <button class="btn btn-danger btn-sm delete-email" data-id="${email.id}" >
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            `;

            // Attach change event to the radio button
            row.querySelector('.set-active-email').addEventListener('change', async (e) => {
              const emailId = e.target.getAttribute('data-id');

              try {
                const response = await fetch(`mc/activate`, {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
                  },
                  body: JSON.stringify({ id: emailId })
                });

                const result = await response.json();

                if (result.status === 'success') {
                  const successEl = document.getElementById('success');
                  successEl.classList.add('alert', 'alert-success');
                  successEl.textContent = 'Email marked as active.';
                } else {
                  const errorEl = document.getElementById('error');
                  errorEl.classList.add('alert', 'alert-danger');
                  errorEl.textContent = 'Failed to set active email.';
                }
              } catch (error) {
                const errorEl = document.getElementById('error');
                errorEl.classList.add('alert', 'alert-danger');
                errorEl.textContent = 'Error updating active email.';
              }
            });

            row.querySelector('.delete-email').addEventListener('click', async (e) => {
            const emailId = e.target.closest('button').getAttribute('data-id');
            try {
              const deleteResponse = await fetch(`mc/delete?id=${emailId}`, {
                method: 'GET',
                headers: {
                  'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
                }
              });

              const deleteResult = await deleteResponse.json();
              if (deleteResult.status === 'success') {
                const errorEl = document.getElementById('success');
                errorEl.classList.add('alert', 'alert-success');
                errorEl.textContent = 'Email deleted successfully.';
                fetchEmails(); // Re-fetch the email list after deletion
              } else {
                const errorEl = document.getElementById('error');
                errorEl.classList.add('alert', 'alert-danger');
                errorEl.textContent = 'Failed to delete the email.';
              }
            } catch (error) {
              const errorEl = document.getElementById('error');
              errorEl.classList.add('alert', 'alert-danger');
              errorEl.textContent = 'Error deleting the email. Please try again.';
            }
          });
            tableBody.appendChild(row);
          });
        }
      } else {
        const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent = 'Something went wrong';
      }

    } catch (error) {
      const errorEl = document.getElementById('error');
      errorEl.classList.add('alert', 'alert-danger');
      errorEl.textContent = 'Server error. Please try again';
    }
  }

  // Call the function on page load
  fetchEmails();
</script>
  <?php require_once 'components/footer.php'; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-labelledby="addEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" id="mailform">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Email</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Sender Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>App Password</label>
            <input type="password" name="passcode" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById('mailform').addEventListener('submit', async function (e) {
    e.preventDefault();  // Prevent default form submission

    // Create a new FormData object from the form
    const formData = new FormData(this);

    try {
      // Send the data via a POST request using fetch
      const response = await fetch('mc/post', {
        method: 'POST',
        body: formData,
        headers: {
                  'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
                }
      });

      // Wait for the response from the server
      const result = await response.text();

      // Check if the response is successful
      if (result.includes('success')) {
        $('#addEmailModal').modal('hide'); // Close modal on success
        fetchEmails(); // Re-fetch emails or update table (optional)
        const errorEl = document.getElementById('success');
        errorEl.classList.add('alert', 'alert-success');
        errorEl.textContent =  'email configuration added.';
      } else {
        const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent =  'Failed to add email configuration.';
      }
    } catch (error) {
      const errorEl = document.getElementById('error');
      errorEl.classList.add('alert', 'alert-danger');
      errorEl.textContent = 'Error submitting the form. Please try again.';
    }
  });
</script>

