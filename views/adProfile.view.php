<?php 
require_once __DIR__ . '/components/header.php'; 
require_once __DIR__ . '/components/sidebar.php';
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk2/login?error=" . urlencode("Please log in again."));
  exit;
}?>

<main class="container py-5">
  <div class="container">
    <h2 class="mb-4">Edit Profile</h2>

    <!-- Status Alerts -->
    <div id="success" role="alert"></div>
    <div id="error" role="alert"></div>

    <div class="row">
      <!-- Update Profile -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">Update Profile</h5>
            <form action="profile/post" method="POST" id="profieform">
              <div class="mb-3">
                <label for="name" class="form-label">Name 
                  <i class="fa-solid fa-pen ms-2 edit-icon" onclick="makeEditable('name')"></i>
                </label>
                <input type="text" class="form-control" id="name" name="name" readonly>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email 
                  <i class="fa-solid fa-pen ms-2 edit-icon" onclick="makeEditable('email')"></i>
                </label>
                <input type="email" class="form-control" id="email" name="email" readonly>
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Phone No 
                  <i class="fa-solid fa-pen ms-2 edit-icon" onclick="makeEditable('phone')"></i>
                </label>
                <input type="text" class="form-control" id="phone" name="phone" readonly>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Update Profile</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Change Password -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">Change Password</h5>
            <form action="profile/password/post" method="POST" id= 'changepassword'>
              <div class="mb-3">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password" class="form-control" name="old_password" required>
              </div>
              <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" required>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-warning" >Change Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
function makeEditable(fieldId) {
  const input = document.getElementById(fieldId);
  input.removeAttribute("readonly");
  input.focus();
}

// Show status messages from GET parameters
const urlParams = new URLSearchParams(window.location.search);
const successMsg = urlParams.get('success');
const errorMsg = urlParams.get('error');

if (successMsg) {
  const successEl = document.getElementById('success');
  successEl.classList.add('alert', 'alert-success');
  successEl.textContent = successMsg;
}

if (errorMsg) {
  const errorEl = document.getElementById('error');
  errorEl.classList.add('alert', 'alert-danger');
  errorEl.textContent = errorMsg;
}

function resetStatusMessages() {
  ['success', 'error'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.textContent = '';
      el.classList.remove('alert', 'alert-success', 'alert-danger');
    }
  });
}
  async function fetchProfile() {
    try {
      const response = await fetch('profile/get', {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
        }
      });

      const result = await response.json();
      console.log(result);

      if (result.status === "success") {
        const user = result.data;
        document.getElementById("name").value = user.name;
        document.getElementById("email").value = user.email;
        document.getElementById("phone").value = user.phone || "";
      } else {
        const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent = "Could not load user data.";
      }
    }catch (error) {
      console.error(error);
      const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent = 'Server error. Please try again.';
    }
    
  }
  fetchProfile();
  document.getElementById('profieform').addEventListener('submit', async function(event) {
  event.preventDefault();
  resetStatusMessages();

  const formData = new FormData(this);

  try {
    const response = await fetch('profile/post', {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
      },
      body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      const successEl = document.getElementById("success");
      successEl.classList.add("alert", "alert-success");
      successEl.textContent = result.message ?? "Profile updated successfully!";
    } else {
      const errorEl = document.getElementById("error");
      errorEl.classList.add("alert", "alert-danger");
      errorEl.textContent = result.message ?? "Profile update failed.";
    }
  } catch (error) {
    console.error('Error:', error);
    const errorEl = document.getElementById('error');
    errorEl.classList.add("alert", "alert-danger");
    errorEl.textContent = "Server error. Please try again.";
  }
});

// Handle password change
document.getElementById('changepassword').addEventListener('submit', async function(event) {
  event.preventDefault();
  resetStatusMessages();

  const formData = new FormData(this);

  try {
    const response = await fetch('profile/password/post', {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
      },
      body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      const successEl = document.getElementById("success");
      successEl.classList.add("alert", "alert-success");
      successEl.textContent = result.message ?? "Password changed successfully!";
      this.reset(); // Clear password fields
    } else {
      const errorEl = document.getElementById("error");
      errorEl.classList.add("alert", "alert-danger");
      errorEl.textContent = result.message ?? "Password change failed.";
    }
  } catch (error) {
    console.error('Error:', error);
    const errorEl = document.getElementById('error');
    errorEl.classList.add("alert", "alert-danger");
    errorEl.textContent = "Server error. Please try again.";
  }
   });
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>

