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
          <h3 class="text-center mb-4">Register</h3>
          <form method="POST" id="registerForm">
            
            <div class="mb-3">
              <label for="user" class="form-label">Username</label>
              <input type="text" class="form-control <?= isset($_GET['user_error']) ? 'is-invalid' : '' ?>" id="user" name="user" placeholder="Enter your username">
              <div class="invalid-feedback" id="user_error"></div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control <?= isset($_GET['email_error']) ? 'is-invalid' : '' ?>" id="email" name="email" placeholder="Enter your email">
              <div class="invalid-feedback" id='email_error'></div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control <?= isset($_GET['password_error']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Enter your password">
              <div class="invalid-feedback" id='password_error'></div>
            </div>

            <div class="mb-3">
              <label for="password1" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="password1" name="password1" placeholder="Re-enter your password">
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Register</button>
            </div>
          </form>
          <div class="text-center mt-3">
            <a href="login">Go to Login Page</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
  document.getElementById('registerForm').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent the default form submission
    const formData = new FormData(this);

    ['user_error', 'email_error', 'password_error', 'error', 'success'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.textContent = '';

      if(id==="error"){ el.classList.remove('alert', 'alert-danger');}
      if(id==='success'){ el.classList.remove('alert', 'alert-success');}
     
      const input = document.getElementById(id.replace('_error', ''));
      if (input) input.classList.remove('is-invalid');
    }
  });
  try {
    const response = await fetch('register/post', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      const successEl= document.getElementById("success");
      successEl.classList.add("alert", "alert-success");
      successEl.textContent =result.message;
        // window.location.href
    } else {
        const errors = result.message;

        // Add this check to prevent Object.entries(null/undefined)
        if (errors && typeof errors === 'object') {
            for (const [key, message] of Object.entries(errors)) {
                const errorEl = document.getElementById(key);
                if(key==='error'){
                  errorEl.classList.add("alert", "alert-danger");
                }
                if (errorEl) {
                    errorEl.textContent = message;

                    const inputEl = document.getElementById(key.replace('_error', ''));
                    if (inputEl) {
                        inputEl.classList.add('is-invalid');
                    }
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
