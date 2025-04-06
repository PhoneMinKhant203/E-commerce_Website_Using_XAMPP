<script src="../../logout.js"></script>



<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start bg-gradient-dark" id="sidenav-main">

  <div class="sidenav-header">
    <button class="sidenav-toggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>

    <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" target="_blank">

    </a>
  </div>
  <hr class="horizontal light mt-0 mb-2">
  <div class="collapse navbar-collapse  w-auto  max-height-vh-100" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/index.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-dashboard-3-line" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/products.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-file-list-2-line" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Products</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/promotion.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-discount-percent-fill" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Promotion</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/users.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-account-pin-circle-fill" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Users</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/order.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-list-unordered" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Customer Orders</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/admin_feedback.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-feedback-fill" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Customer Feedback</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/admin_message.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-chat-3-fill" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Messages</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white " href="../../Admin/admin_posting.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ri-signpost-fill" style="font-size: 21px;"></i>
          </div>
          <span class="nav-link-text ms-1">Posting</span>
        </a>
      </li>
    </ul>
  </div>
  <div class="sidenav-footer position-absolute w-100 bottom-0 ">
    <div class="mx-3">
      <a class="btn bg-gradient-danger mt-4 w-100" href="../../logout.php" type="button" onclick="return confirmLogout()">Log Out</a>
    </div>
  </div>

</aside>

<button class="sidenav-toggle" onclick="toggleSidebar()">
  <i class="fas fa-bars"></i>
</button>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidenav-main');
    sidebar.classList.toggle('active');
  }

  function confirmLogout() {
    return confirm("Are you sure you want to log out?");
  }
</script>




<script>
  function confirmLogout() {
    var confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {

      window.location.href = "../../logout.php";
    } else {

      return false;
    }
  }
</script>



<?php ?>