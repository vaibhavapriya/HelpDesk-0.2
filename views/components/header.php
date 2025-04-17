<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="UTF-8">
    <!--Setting the viewport to make your website look good on all devices:-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./images/favicon.png" />
    <title>HelpDesk</title>
    <link rel="stylesheet" href="/HelpDesk2/views/assets/style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/lato-font/3.0.0/css/lato-font.min.css"
    integrity="sha512-rSWTr6dChYCbhpHaT1hg2tf4re2jUxBWTuZbujxKg96+T87KQJriMzBzW5aqcb8jmzBhhNSx4XYGA6/Y+ok1vQ=="
    crossorigin="anonymous"
  />
  <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      crossorigin="anonymous"
      defer
  />

</head>
<body class="hold-transition sidebar-mini sidebar-collapse sidebar-open layout-fixed">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a href="#" data-widget="pushmenu" role="button" class="nav-link"><i class="fas fa-bars"></i></a>
        <!--  -->
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
          <!-- <span class="badge badge-warning navbar-badge">15</span> -->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="adminProfile" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Profile
          </a>
          <div class="dropdown-divider"></div>
          <a href="adminProfile" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Password
          </a>
          <div class="dropdown-divider"></div>
          <a href="logout" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Logout
          </a>
      </li>
    </ul>
</nav>

<!-- profile and menu icon -->