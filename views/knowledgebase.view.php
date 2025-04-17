<?php 
require_once __DIR__ . '/components/clientheader.php'; ?>

  <main class="d-flex align-items-center justify-content-center ">
    <div class="container">
      <h2 class="mb-4 text-center">Service Desk Knowledge Base</h2>

      <div class="accordion" id="faqAccordion">

        <!-- FAQ 1 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
              How to Submit a Support Ticket?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              <p>1. Log in to the E-commerce Admin Panel.</p>
              <p>2. Navigate to Help Center → Submit a Ticket.</p>
              <p>3. Fill in details (Order ID, issue type, etc.) and submit.</p>
            </div>
          </div>
        </div>

        <!-- FAQ 2 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
              Order Stuck in Processing
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              <p>Possible reasons:</p>
              <ul>
                <li>Payment Not Confirmed – Verify with the payment gateway.</li>
                <li>Out of Stock – Notify the customer.</li>
                <li>System Delay – Refresh the order after 30 minutes.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- FAQ 3 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingThree">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
              Refund Not Processed
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              <p>Check if the refund was initiated from the admin panel. If pending, verify with the payment gateway.</p>
              <p>Customers should expect refunds within 5–7 business days.</p>
            </div>
          </div>
        </div>

        <!-- FAQ 4 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingFour">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
              Website Not Loading
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              <p>1. Check if the server is running via System Status.</p>
              <p>2. Clear cache and try again.</p>
              <p>3. If the issue persists, check CDN and hosting provider status.</p>
            </div>
          </div>
        </div>

      </div>

      <div class="alert alert-info mt-4" role="alert">
        <strong>Need more support?</strong> If you did not find an answer, please raise a ticket describing the issue.
      </div>
    </div>
  </main>
    
<?php require_once __DIR__ . '/components/footer.php'; ?>