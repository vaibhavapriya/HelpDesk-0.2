
        <?php if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) { ?>

      <div class="row g-4 mb-5">
        <div class="col-md-3 ">
          <a href="register" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm bg-light">
              <div class="card-body">
                <i class="fa-regular fa-pen-to-square fa-2x mb-2"></i>
                <h5 class="card-title">Register</h5>
              </div>
            </div>
          </a>
        </div>
        <?php } else echo '<div class="row g-3 mb-5">'; ?>

        <!-- Card 2 -->
        <div class="col-md-3">
          <a href="newTicket" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm bg-light">
              <div class="card-body ">
                <i class="fa-solid fa-rectangle-list fa-2x mb-2"></i>
                <h5 class="card-title">Submit Ticket</h5>
              </div>
            </div>
          </a>
        </div>

        <!-- Card 3 -->
        <div class="col-md-3">
          <a href="myTickets" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm bg-light">
              <div class="card-body">
                <i class="fa-regular fa-newspaper fa-2x mb-2"></i>
                <h5 class="card-title">My Ticket</h5>
              </div>
            </div>
          </a>
        </div>

        <!-- Card 4 -->
        <div class="col-md-3">
          <a href="knowledgeBase" class="text-decoration-none text-dark ">
            <div class="card text-center shadow-sm bg-light">
              <div class="card-body">
                <i class="fa-solid fa-lightbulb  fa-2x mb-2"></i>
                <h5 class="card-title">Knowledgebase</h5>
              </div>
            </div>
          </a>
        </div>
        
      </div>
