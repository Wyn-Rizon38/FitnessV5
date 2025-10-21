<!-- Sidebar Navigation -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  body {
    overflow-x: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffffff;
    margin: 0;
  }

  /* ===== Sidebar Base ===== */
  #sidebar {
    width: 240px;
    min-height: 100vh;
    background: #fff;
    border-right: 2px solid #FFD700;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1030;
    color: #444;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.4s ease-in-out;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
  }

  /* Collapsed State */
  #sidebar.collapsed {
    transform: translateX(-100%);
  }

  /* ===== Sidebar Header ===== */
  #sidebar .sidebar-header {
    padding: 1rem;
    text-align: center;
    border-bottom: 2px solid #FFD700;
    background: #464646ff;
  }

  #sidebar .sidebar-header img {
    width: 80px;
    border-radius: 50%;
    border: 2px solid #FFD700;
    box-shadow: 0 0 12px rgba(255, 215, 0, 0.5);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
  }

  #sidebar .sidebar-header img:hover {
    transform: scale(1.1) rotate(3deg);
    box-shadow: 0 0 18px rgba(255, 215, 0, 0.7);
  }

  #sidebar .sidebar-header h5 {
    color: #ffffffff;
    font-weight: bold;
    margin-top: 10px;
    text-shadow: 0 0 8px rgba(255, 215, 0, 0.4);
    font-size: 1.2rem;
    letter-spacing: 1px;
  }

  /* ===== Nav Links ===== */
  #sidebar .nav-link {
    color: #444;
    font-weight: 500;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    padding: 0.7rem 1.2rem;
    transition: all 0.3s ease-in-out;
    border-radius: 8px;
    font-size: 0.95rem;
  }

  #sidebar .nav-link i {
    font-size: 1.3rem;
    margin-right: 12px;
    color: #c09300;
    transition: transform 0.3s ease, color 0.3s ease;
  }

  /* Hover & Active Effects */
  #sidebar .nav-link:hover {
    background: rgba(255, 215, 0, 0.15);
    color: #c09300;
    box-shadow: inset 3px 0 0 #FFD700;
    transform: translateX(5px);
  }

  #sidebar .nav-link:hover i {
    transform: translateX(5px) scale(1.1);
    color: #FFD700;
  }

  #sidebar .nav-link.active {
    background: #FFD700;
    color: #fff;
    font-weight: bold;
    box-shadow: 0 0 12px rgba(255, 215, 0, 0.5);
  }

  #sidebar .nav-link.active i {
    color: #fff;
  }

  /* ===== Footer Buttons ===== */
  #sidebar .sidebar-footer {
    text-align: center;
    padding: 1rem 0;
    border-top: 2px solid #FFD700;
    background: #464646ff;
  }

  #sidebar .btn-dark {
    background-color: #e3c100ff;
    color: #fff;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease-in-out;
  }

  #sidebar .btn-dark:hover {
    background-color: #e6c200;
    transform: translateY(-3px);
    box-shadow: 0 0 15px rgba(255, 215, 0, 0.8);
  }

  #sidebar .btn-danger {
    background-color: #ff4d4d;
    color: #fff;
    font-weight: bold;
    border-radius: 10px;
    border: none;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 0 8px rgba(255, 0, 0, 0.4);
  }

  #sidebar .btn-danger:hover {
    background-color: #e60000;
    transform: translateY(-3px);
    box-shadow: 0 0 12px rgba(255, 0, 0, 0.6);
  }

  /* ===== Hamburger Button ===== */
  #toggle-btn {
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1100;
    background: #FFD700;
    color: #fff;
    font-size: 1.5rem;
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: none;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
  }

  #toggle-btn:hover {
    background: #e6c200;
    box-shadow: 0 0 12px rgba(255, 215, 0, 0.7);
  }

  /* ===== Responsive ===== */
  @media (max-width: 991.98px) {
    #toggle-btn {
      display: block;
    }

    #sidebar {
      transform: translateX(-100%);
    }

    #sidebar.show {
      transform: translateX(0);
    }
  }
</style>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Toggle Button -->
<button id="toggle-btn"><i class="bi bi-list"></i></button>

<!-- SIDEBAR -->
<div id="sidebar">
  <!-- HEADER -->
  <div class="sidebar-header">
    <a href="dashboard.php">
      <img src="image/logo.png" alt="Fitness+ Logo">
    </a>
    <h5>Fitness+</h5>
  </div>

  <!-- NAVIGATION -->
  <ul class="nav flex-column p-3">
    <li class="nav-item">
      <a class="nav-link<?php if($currentPage=='dashboard.php') echo ' active'; ?>" href="dashboard.php">
        <i class="bi bi-house-door"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php if($currentPage=='walkin.php') echo ' active'; ?>" href="walkin.php">
        <i class="bi bi-person-walking"></i> Walk-In
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php if($currentPage=='members.php') echo ' active'; ?>" href="members.php">
        <i class="bi bi-people"></i> Members
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php if($currentPage=='schedule.php') echo ' active'; ?>" href="schedule.php">
        <i class="bi bi-calendar-event"></i> Schedule
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php if($currentPage=='class.php') echo ' active'; ?>" href="class.php">
        <i class="bi bi-journal"></i> Class
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php if($currentPage=='coach.php') echo ' active'; ?>" href="coach.php">
        <i class="bi bi-person-badge"></i> Coach
      </a>
    </li>
  </ul>

  <!-- FOOTER -->
  <div class="sidebar-footer">
    <a href="admin_profile.php" class="btn btn-dark mb-2 w-75">Admin Profile</a><br>
    <a href="logout.php" class="btn btn-danger w-75">Logout</a>
  </div>
</div>

<!-- JavaScript -->
<script>
  const toggleBtn = document.getElementById('toggle-btn');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });
</script>
