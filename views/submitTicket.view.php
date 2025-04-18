<?php require_once __DIR__ . '/components/clientheader.php'; ?>

<main class="container py-5">
<?php require_once __DIR__ . '/components/homegrid.php'; ?>
  <div class="" role="alert" id="error"></div>
  <div class="" role="alert" id="success"></div>
    
  <!-- Register Form action="register/post" -->
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="text-center mb-4">Submit Ticket</h3>
            <form id="ticketForm" method="POST" enctype="multipart/form-data">
              
              <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control <?= isset($_GET['subject_error']) ? 'is-invalid' : '' ?>" id="subject" name="subject" >
                <div class="invalid-feedback" id='subject_error'></div>
              </div>

              <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select <?= isset($_GET['priority_error']) ? 'is-invalid' : '' ?>" id="priority" name="priority">
                  <option value="high">High</option>
                  <option value="medium">Medium</option>
                  <option value="low">Low</option>
                </select>
                <div class="invalid-feedback" id='priority_error'></div>
              </div>

              <div class="mb-3">
                <label for="topic" class="form-label">Topic</label>
                <input type="text" class="form-control <?= isset($_GET['topic_error']) ? 'is-invalid' : '' ?>" id="topic" name="topic" >
                <div class="invalid-feedback" id='topic_error'></div>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control <?= isset($_GET['description_error']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="4"></textarea>
                <div class="invalid-feedback" id='description_error'></div>
              </div>

              <div class="mb-3">
                <label for="attachment" class="form-label">Attachment</label>
                <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*">
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
const jwtToken = "<?= $_SESSION['jwt_token'] ?? '' ?>";
document.getElementById('ticketForm').addEventListener('submit', async function (event) {
  event.preventDefault();
<?php
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk-0.2/login?error=" . urlencode("Please log in again."));
  exit;
}?>

  const form = event.target;
  const formData = new FormData(form);

  // Reset error/success states
  ['error', 'success', 'description_error', 'subject_error', 'topic_error', 'priority_error' ].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.textContent = '';
      el.classList.remove('alert', 'alert-danger', 'alert-success', 'text-danger');
    }
  });
// 'Authorization': `Bearer ${jwtToken}`
  try {
    const response = await fetch('newTicket/post', {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
      },
      body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      const successEl = document.getElementById('success');
      successEl.classList.add('alert', 'alert-success');
      successEl.textContent = result.message;
      form.reset(); // optional
    } else {
      const errors = result.message;
      if (errors && typeof errors === 'object') {
        for (const [key, msg] of Object.entries(errors)) {
          const el = document.getElementById(key);
          if (el) {
            if (key === 'error') {
              el.classList.add('alert', 'alert-danger');
            } 
            el.textContent = msg;
            const inputEl = document.getElementById(key.replace('_error', ''));
                    if (inputEl) {
                        inputEl.classList.add('is-invalid');
                    }
          }
        }
      } else {
        const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent = result.message || 'An unknown error occurred.';
      }
    }
  } catch (error) {
    const errorEl = document.getElementById('error');
    errorEl.classList.add('alert', 'alert-danger');
    errorEl.textContent = 'Submission failed. Please try again.';
    console.error('Error submitting form:', error);
  }
});
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
