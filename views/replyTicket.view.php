<!-- reples more like chat -->
<!-- api get all replies of the ticket and add reply to the ticket -->
<?php 
require_once __DIR__ . '/components/header.php'; 
require_once __DIR__ . '/components/sidebar.php';
?>
<main class="d-flex align-items-center justify-content-center">
  <div class="container py-5">
    <div class="" role="alert" id="error"></div>
    <div class="" role="alert" id="success"></div>

    <!-- Display error and success messages -->
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_GET['error']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_GET['success']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <!-- Ticket Form -->
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <h3 class="text-center mb-4">Ticket</h3>
        <form id="ticketForm" method="POST" enctype="multipart/form-data">
          <input type="hidden" id="ticketId" name="ticketId">

          <!-- Subject -->
          <div class="mb-3 row align-items-center">
            <label for="subject" class="col-sm-4 col-form-label text-end">Subject</label>
            <div class="col-sm-8 d-flex align-items-center">
              <input type="text" class="form-control me-2 <?= isset($_GET['subject_error']) ? 'is-invalid' : '' ?>" id="subject" name="subject" readonly>
              <i class="fa-solid fa-pen edit-icon" onclick="makeEditable('subject')"></i>
              <div class="invalid-feedback" id="subject_error"></div>
            </div>
          </div>

          <!-- Priority -->
          <div class="mb-3 row align-items-center">
            <label for="priority" class="col-sm-4 col-form-label text-end">Priority</label>
            <div class="col-sm-8 d-flex align-items-center">
              <select class="form-select me-2 <?= isset($_GET['priority_error']) ? 'is-invalid' : '' ?>" id="priority" name="priority" disabled>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
              </select>
              <i class="fa-solid fa-pen edit-icon" onclick="makeEditable('priority')"></i>
              <div class="invalid-feedback" id="priority_error"></div>
            </div>
          </div>

          <!-- Status -->
          <div class="mb-3 row align-items-center">
            <label for="status" class="col-sm-4 col-form-label text-end">Status</label>
            <div class="col-sm-8 d-flex align-items-center">
              <select class="form-select me-2" id="status" name="status" disabled>
                <option value="Open">Open</option>
                <option value="Closed">Close</option>
              </select>
              <i class="fa-solid fa-pen edit-icon" onclick="makeEditable('status')"></i>
              <div class="invalid-feedback" id="status_error"></div>
            </div>
          </div>

          <!-- Topic -->
          <div class="mb-3 row align-items-center">
            <label for="topic" class="col-sm-4 col-form-label text-end">Topic</label>
            <div class="col-sm-8 d-flex align-items-center">
              <input type="text" class="form-control me-2 <?= isset($_GET['topic_error']) ? 'is-invalid' : '' ?>" id="topic" name="topic" readonly>
              <i class="fa-solid fa-pen edit-icon" onclick="makeEditable('topic')"></i>
              <div class="invalid-feedback" id="topic_error"></div>
            </div>
          </div>

          <!-- Description -->
          <div class="mb-3 row align-items-center">
            <label for="description" class="col-sm-4 col-form-label text-end">Description</label>
            <div class="col-sm-8 d-flex align-items-center">
              <textarea class="form-control me-2 <?= isset($_GET['description_error']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="4" readonly></textarea>
              <i class="fa-solid fa-pen edit-icon" onclick="makeEditable('description')"></i>
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

          <!-- Reply -->
          <div class="mb-3 row align-items-center">
            <label for="reply" class="col-sm-4 col-form-label text-end">Reply</label>
            <div class="col-sm-8 d-flex align-items-center">
              <textarea class="form-control me-2" id="reply" name="reply" rows="3" placeholder="Type your reply..."></textarea>
              <div class="invalid-feedback" id="reply_error"></div>
            </div>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
function makeEditable(id) {
  const el = document.getElementById(id);
  if (!el) return;

  if (el.tagName === 'SELECT') {
    el.disabled = false;
  } else {
    el.removeAttribute('readonly');
  }
  el.focus();
}

document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const ticketId = urlParams.get('id');

  async function fetchTicket() {
    try {
      const response = await fetch(`replyTicket/get?id=${ticketId}`, {
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      if (result.status === 'success') {
        const ticket = result.data;
        const selects = form.querySelectorAll('select:disabled');
        selects.forEach(select => select.disabled = true);
        document.getElementById('ticketId').value = ticket.id;
        document.getElementById('subject').value = ticket.subject;
        document.getElementById('description').value = ticket.description;
        document.getElementById('status').value = ticket.status;
        document.getElementById('topic').value = ticket.topic;
        document.getElementById('reply').value = ticket.reply || '';
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
        const response = await fetch(`replyTicket/post`, {
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
        document.getElementById('error').innerText = "Failed to submit the reply.";
      } 
    });
  }

  fetchTicket();
});


</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
