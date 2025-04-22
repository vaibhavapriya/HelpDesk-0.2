<?php 
require_once __DIR__ . '/components/header.php'; 
require_once __DIR__ . '/components/sidebar.php';
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk-0.2/login?error=" . urlencode("Please log in again."));
  exit;
}?>

<main style="background-color: #eee;">
  <div class="container py-5">
  <div class="" role="alert" id="error"></div>
  <div class="" role="alert" id="success"></div>
    <div class="row">
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
              class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3">name</h5>
            <p class="text-muted mb-1">role</p>
            <p class="text-muted mb-4">email</p>
            <i class="far fa-edit mb-5"></i>
            <!-- <div class="d-flex justify-content-center mb-2">
              <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary">Follow</button>
              <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-primary ms-1">Message</button>
            </div> -->
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Full Name</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">Johnatan Smith</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Email</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">example@example.com</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Phone</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">(097) 234-5678</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Mobile</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">(098) 765-4321</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Address</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">Bay Area, San Francisco, CA</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const userid = urlParams.get('id');

  async function fetchUser() {
    try {
      const response = await fetch(`replyTicket/get?id=${userid}`, {
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();

      if (result.status === 'success') {
        const user = result.data;
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

  fetchUser();
});
</script>
<?php require_once __DIR__ . '/components/footer.php'; ?>