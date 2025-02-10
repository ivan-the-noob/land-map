
<div class="container">
    <!-- Logo Head -->
    <div class="az-header-left">
        <a href="../frontend/frontend_users/user_page.php" class="az-logo">Land<span class="text-primary">Map</span></a>
        <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
    </div>
    <!-- Logo Tail -->

    <div class="az-header-menu">
        <!-- Hamburger Logo Head -->
        <div class="az-header-menu-header">
            <a href="index.html" class="az-logo"><span></span> azia</a>
            <a href="" class="close">&times;</a>
        </div>
        <!-- Hamburger Logo Tail -->

        <!-- Navigation Bar Head -->
        <ul class="nav">
            <li><a href="../frontend_users/super_adminPage.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="../frontend_users/super_admin_userManagement.php" class="nav-link"><i class="fa fa-users"></i> User Management</a></li>
            <li><a href="../frontend_users/super_admin_control.php" class="nav-link"><i class="fa fa-cogs"></i> Control Panel</a></li>
            <li><a href="../frontend_users/admin_properties.php" class="nav-link"><i class="fa fa-file"></i> Records</a></li>
        </ul>
        <!-- Navigation Bar Tail -->
    </div>

    <div class="az-header-right">
    <a href="" class="az-header-search-link"><i class="fas fa-search"></i></a>
    <div class="az-header-message">
        <a href="../frontend_users/user_chat.php"><i class="typcn typcn-messages"></i></a>
    </div><!-- az-header-message -->
    <div class="dropdown az-header-notification">
        <a href="" class="new"><i class="typcn typcn-bell"></i></a>
        <div class="dropdown-menu">
            <!-- Notifications content here -->
        </div><!-- dropdown-menu -->
    </div><!-- az-header-notification -->
    
    <!-- Profile Section -->
    <div class="dropdown az-profile-menu">
        <!-- Display user image -->
        <a href="" class="az-img-user">
            <img src="<?php echo $profile_image; ?>" alt="User Profile">
        </a>
        <div class="dropdown-menu">
            <div class="az-dropdown-header d-sm-none">
                <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
            </div>
            <h6 class="az-notification-title">Notifications</h6>
            <p class="az-notification-text">You have 3 unread notifications</p>
            <div class="az-header-profile">
                <div class="az-img-user">
                    <img src="<?php echo $profile_image; ?>" alt="User Profile">
                </div><!-- az-img-user -->
                <h6 class="text-nowrap"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?></h6>
                <span><?php echo isset($_SESSION['role_type']) ? htmlspecialchars($_SESSION['role_type']) : 'No Role'; ?></span>
                <span><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'No Email'; ?></span>
            </div><!-- az-header-profile -->

            <a href="../frontend_users/super_admin_profile_page.php" class="dropdown-item">
                <i class="typcn typcn-user-outline"></i>
                <span>Profile Page</span>
            </a>
            <a href="#" class="dropdown-item">
                <i class="typcn typcn-cog-outline"></i>
                <span>Account Settings</span>
            </a>

            <button id="signOutButton" class="dropdown-item">
                <i class="typcn typcn-power-outline" style="color: #dc3545;"></i>
                <span style="color: #dc3545;">Sign Out</span>
            </button>
        </div><!-- dropdown-menu -->
    </div>
</div><!-- az-header-right -->

</div><!-- NAVBAR -->