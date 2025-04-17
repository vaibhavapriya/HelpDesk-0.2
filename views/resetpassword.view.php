<?php require_once __DIR__ . '/components/clientheader.php'; ?>

<main>
  <div class="container py-5">

    <!-- Alerts -->
    <div id="error" role="alert"></div>
    <div id="success" role="alert"></div>

    <!-- Form -->
    <div class="row justify-content-center mt-5">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="mb-4 text-center">Reset Password</h2>

            <form method="POST" id="resetPasswordForm">
              <input type="hidden" name="token" id="token">

              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  name="email" 
                  placeholder="Enter your registered email">
                <div class="invalid-feedback" id="email_error"></div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input 
                  type="password"
                  class="form-control" 
                  id="password"
                  name="password" 
                  placeholder="Enter new password">
                <div class="invalid-feedback" id="password_error"></div>
              </div>

              <div class="mb-3">
                <label for="password1" class="form-label">Confirm Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password1" 
                  name="password1" 
                  placeholder="Re-enter your password">
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Set Password</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  const token = <?= json_encode($_GET['token'] ?? '') ?>;
  document.getElementById("token").value = token;
document.getElementById('resetPasswordForm').addEventListener('submit', async function(event) {
  event.preventDefault();

  const formData = new FormData(this);

  // Clear previous errors
  ['email_error', 'password_error', 'error', 'success'].forEach(id => {
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
    const response = await fetch('resetPassword/post', {
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
          if (key === 'error') {
            errorEl.classList.add('alert', 'alert-danger');
          }
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
