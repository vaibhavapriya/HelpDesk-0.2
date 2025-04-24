<!-- so get ticket info and replies of ticket edit ticket, add reply -->
<!-- reply bot and ticket edit -->
<?php require_once __DIR__ . '/components/clientheader.php';
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk-0.2/login?error=" . urlencode("Please log in again."));
  exit;
}?>

<main class="container py-5">
    <div class="" role="alert" id="error"></div>
    <div class="" role="alert" id="success"></div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
        <h3 class="text-center mb-4">Submit Ticket</h3>
            <form id="ticketForm" method="POST" enctype="multipart/form-data">

                <div class="mb-3 row">
                <label for="subject" class="col-sm-4 col-form-label text-end">Subject</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control <?= isset($_GET['subject_error']) ? 'is-invalid' : '' ?>" id="subject" name="subject">
                    <div class="invalid-feedback" id="subject_error"></div>
                </div>
                </div>

                <div class="mb-3 row">
                <label for="priority" class="col-sm-4 col-form-label text-end">Priority</label>
                <div class="col-sm-8">
                    <select class="form-select <?= isset($_GET['priority_error']) ? 'is-invalid' : '' ?>" id="priority" name="priority">
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                    </select>
                    <div class="invalid-feedback" id="priority_error"></div>
                </div>
                </div>

                <div class="mb-3 row">
                <label for="status" class="col-sm-4 col-form-label text-end">Status</label>
                <div class="col-sm-8">
                    <select class="form-select <?= isset($_GET['priority_error']) ? 'is-invalid' : '' ?>" id="status" name="status">
                    <option value="Open">Open</option>
                    <option value="Closed">Close</option>
                    </select>
                    <div class="invalid-feedback" id="status_error"></div>
                </div>
                </div>

                <div class="mb-3 row">
                <label for="topic" class="col-sm-4 col-form-label text-end">Topic</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control <?= isset($_GET['topic_error']) ? 'is-invalid' : '' ?>" id="topic" name="topic">
                    <div class="invalid-feedback" id="topic_error"></div>
                </div>
                </div>

                <div class="mb-3 row">
                <label for="description" class="col-sm-4 col-form-label text-end">Description</label>
                <div class="col-sm-8">
                    <textarea class="form-control <?= isset($_GET['description_error']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="4"></textarea>
                    <div class="invalid-feedback" id="description_error"></div>
                </div>
                </div>
                <!-- Attachment (view only) -->
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label text-end">Attachment</label>
                    <div class="col-sm-8">
                    <div id="attachmentContainer"><span>No attachment available.</span></div>
                    </div>
                </div>

                <!-- Attachment Upload -->
                <div class="mb-3 row">
                    <label for="attachment" class="col-sm-4 col-form-label text-end">Change Attachment</label>
                    <div class="col-sm-8">
                    <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*">
                    </div>
                </div>

                <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>

            </form>
        </div>
    </div>
    <a href="myTickets" class="btn btn-outline-primary mt-3">
      <i class="bi bi-arrow-left-circle"></i> Back to My Tickets
    </a>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const ticketId = urlParams.get('id');

  async function fetchTicket() {
    try {
      const response = await fetch(`editTicket/get?id=${ticketId}`, {
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      if (result.status === 'success') {
        const ticket = result.data;
        const selects = form.querySelectorAll('select:disabled');
        selects.forEach(select => select.disabled = true);
        document.getElementById('subject').value = ticket.subject;
        document.getElementById('description').value = ticket.description;
        document.getElementById('status').value = ticket.status;
        document.getElementById('topic').value = ticket.topic;
        document.getElementById('priority').value = ticket.priority;
        const attachmentContainer = document.getElementById('attachmentContainer');
        if (!ticket.attachment_type) {
          attachmentContainer.textContent = "No attachment available.";
        }
        else {
          attachmentContainer.innerHTML = `<img src="/project/image.php?id=${ticketId}" 
            alt="Ticket Attachment" style="max-width: 400px; height: auto;">`;
        }

      } else {
        alert(result.message || "Failed to load ticket.");
      }
    } catch (error) {
      const errorEl = document.getElementById('error');
      errorEl.classList.add('alert', 'alert-danger');
      errorEl.textContent = 'Submission failed. Please try again.';
      console.error('Error submitting form:', error);
    }
  }

  const form = document.getElementById('ticketForm');
  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      // ✅ Enable all disabled fields temporarily
      const selects = form.querySelectorAll('select:disabled');
      selects.forEach(select => select.disabled = false);

      // Clear previous messages
      document.getElementById('error').innerHTML = '';
      document.getElementById('success').innerHTML = '';

      const formData = new FormData(this);
      formData.append('id', ticketId);

      // ✅ Restore original disabled state
      selects.forEach(select => select.disabled = true);

      const submitButton = form.querySelector('button[type="submit"]');
      submitButton.disabled = true;

      try {
        const response = await fetch(`editTicket/post`, {
          method: 'POST',
          body: formData,
          headers: {
            'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
          }
        });

        const result = await response.json();
        const msgBox = document.getElementById(result.status === 'success' ? 'success' : 'error');
        if (msgBox) {
          msgBox.textContent = result.message;
          msgBox.className = `alert ${result.status === 'success' ? 'alert-success' : 'alert-danger'} alert-dismissible fade show`;
        }

      } catch (err) {
        console.error(err);
        document.getElementById('error').innerText = err;
      } 
    });
  }

  fetchTicket();
});
</script>
<?php require_once __DIR__ . '/components/footer.php'; ?>
