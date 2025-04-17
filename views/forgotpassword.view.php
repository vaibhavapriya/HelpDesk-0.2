<?php require_once __DIR__ . '/components/clientheader.php'; ?>

<main class="container py-5">
  <?php require_once __DIR__ . '/components/homegrid.php'; ?>
    <!-- Alert containers -->
  <div class="" role="alert" id="error"></div>
  <div class="" role="alert" id="success"></div>

  <!-- Forgot Password Form -->
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="text-center mb-4">Forgot Password</h3>
          <form method="POST" id="forgotForm">
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email">
              <div class="invalid-feedback" id="email_error"></div>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Send Token</button>
            </div>
          </form>
          <div class="text-center mt-3">
            <a href="login">Back to Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- AJAX Logic -->
<script>
document.getElementById('forgotForm').addEventListener('submit', async function (event) {
  event.preventDefault();

  const formData = new FormData(this);

  ['email_error', 'error', 'success'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.textContent = '';
      if (id === 'error') el.classList.remove('alert', 'alert-danger');
      if (id === 'success') el.classList.remove('alert', 'alert-success');
      const input = document.getElementById(id.replace('_error', ''));
      if (input) input.classList.remove('is-invalid');
    }
  });

  try {
    const response = await fetch('forgotPassword/post', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      const successEl = document.getElementById('success');
      successEl.classList.add('alert', 'alert-success');
      successEl.textContent = result.message;
    } else {
      const errors = result.message;
      if (errors && typeof errors === 'object') {
        for (const [key, message] of Object.entries(errors)) {
          const errorEl = document.getElementById(key);
          if (key === 'error') errorEl.classList.add('alert', 'alert-danger');
          if (errorEl) {
            errorEl.textContent = message;
            const inputEl = document.getElementById(key.replace('_error', ''));
            if (inputEl) inputEl.classList.add('is-invalid');
          }
        }
      } else {
        console.error('Unexpected error format:', result);
      }
    }
  } catch (error) {
    console.error('Error:', error);
  }
});
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
