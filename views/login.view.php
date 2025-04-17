<?php 
require_once __DIR__ . '/components/clientheader.php'; ?>
  <main class="container py-5">

        <!-- Success & Error Alerts -->
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
          <use xlink:href="#exclamation-triangle-fill" />
        </svg>
        <div><?= htmlspecialchars($_GET['error']) ?></div>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
          <use xlink:href="#check-circle-fill" />
        </svg>
        <div><?= htmlspecialchars($_GET['success']) ?></div>
      </div>
    <?php endif; ?>

    <!-- Cards Section -->
    <?php require_once __DIR__ . '/components/homegrid.php'; ?>
    <div class="" role="alert" id="error"></div>
    <div class="" role="alert" id="success"></div>

      <!-- Register Form -->
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="text-center mb-4">Login</h3>
            <form method="POST" id="loginForm">

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
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
            </form>
            <div class="text-center mt-3">
              <a href="forgotPassword"><div>Forgot Password ?</div></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
  document.getElementById('loginForm').addEventListener('submit', async function(event) {
  event.preventDefault(); // Prevent the default form submission
  const formData = new FormData(this);

  ['email_error', 'password_error', 'error', 'success'].forEach(id => {
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
    const response = await fetch('login/post', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      const successEl= document.getElementById("success");
      successEl.classList.add("alert", "alert-success");
      successEl.textContent ="Logged in sucessfully";
      localStorage.setItem("jwt_token", result.message);
      if (result.role === 'admin') {
        window.location.href = '/HelpDesk2/adminhome?message=' + encodeURIComponent("Welcome");
      } else {
        window.location.href = '/HelpDesk2/home?message=' + encodeURIComponent("Logged in successfully");
      }

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