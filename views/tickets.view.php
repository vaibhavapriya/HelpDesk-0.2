<?php 
require_once __DIR__ . '/components/header.php'; 
require_once __DIR__ . '/components/sidebar.php';
if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: /HelpDesk2/login?error=" . urlencode("Please log in again."));
  exit;
}?>

<main class="container py-5">
  <div class="container">

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php endif; ?>

    <h2 class="mb-4"> Tickets</h2>

    <div id="status" class="mb-3 text-muted"></div>
    <div id="ticketCardContainer" class="row gy-4">
      <!-- Cards will be dynamically inserted here -->
    </div>
  </div>
</main>

<script>
 async function fetchTickets() {
  try {
    const response = await fetch('tickets/get', {
      method: 'GET',
      headers: {
        'Authorization': 'Bearer <?= $_SESSION['jwt_token'] ?>'
      }
    });

    const result = await response.json();

    const statusDiv = document.getElementById('status');
    const cardContainer = document.getElementById('ticketCardContainer'); // ✅ DECLARED HERE
    cardContainer.innerHTML = ''; // Clear previous cards

    if (result.status === 'success') {
      const tickets = result.data;

      if (tickets.length === 0) {
        statusDiv.textContent = "No tickets found.";
      } else {
        tickets.forEach(ticket => {
          const card = document.createElement('div');
          card.className = 'col-md-6 col-lg-4';

          card.innerHTML = `
            <div class="card h-100 shadow-sm border-0 hover-shadow bg-lights" style="cursor: pointer;">
              <div class="card-body">
                <h5 class="card-title">${ticket.subject}</h5>
                <p class="card-text">
                  <strong>Ticket ID:</strong> ${ticket.id}<br>
                  <strong>Status:</strong> ${ticket.status}<br>
                  <strong>Last Replier:</strong> ${ticket.last_replier ?? '-'}<br>
                  <strong>Last Activity:</strong> ${ticket.last_activity}
                </p>
              </div>
              <div class="card-footer text-muted small">
                Requested by: ${ticket.requester}
              </div>
            </div>
          `;

          cardContainer.appendChild(card);
        });
      }
    } else {
      statusDiv.className = 'text-danger';
      statusDiv.textContent = result.message || 'Something went wrong.';
    }

  } catch (error) {
    console.error(error);
    document.getElementById('status').textContent = 'Server error. Please try again.';
  }
}


  // Call the function on load
  fetchTickets();
</script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
<!-- onclick="window.location.href='/HelpDesk2/clientTicket?id=${ticket.id}'" -->