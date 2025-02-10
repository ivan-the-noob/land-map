<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="container">

    <!--Logo Head-->

    <div class="az-header-left">
        <a href="../frontend/frontend_users/user_page.php" class="az-logo">Land<span class="text-primary">Map</span></a>
        <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
    </div>

    <!--Logo Tail-->

    <div class="az-header-menu">

        <!-- Hamburger Logo Head -->

        <div class="az-header-menu-header">
            <a href="index.html" class="az-logo"><span></span> azia</a>
            <a href="" class="close">&times;</a>
        </div>

        <!-- Hamburger Logo Tail -->


        <!--Navigation Bar Head-->

        <ul class="nav">
            <li class="">
                <a href="../frontend_users/admin_page.php" class="nav-link"><i class="typcn typcn-home-outline"></i> Dashboard</a>
            </li>

            <li class="">
                <a href="../frontend_users/admin_properties.php" class="nav-link"><i class="typcn typcn-location-outline"></i> Land Properties</a>
            </li>

            <li class="">
                <a href="../frontend_users/agent_developers.php" class="nav-link"><i class="fas fa-building"></i> Developers</a>
            </li>

            <li class="">
                <a href="../frontend_users/admin_agents.php" class="nav-link"><i class="fas fa-user-tie"></i> Brokers</a>
            </li>

            <li class="">
                <a href="../frontend_users/admin_control.php" class="nav-link"><i class="fas fa-user-cog"></i> Admin Control</a>
            </li>

           
        </ul>

        <!--Navigation Bar Tail-->

    </div>

    <div class="az-header-right">
        <a href="" class="az-header-search-link"><i class="fas fa-handshake"></i></a>
        <div class="az-header-message">
            <a href="../frontend_users/admin_chat.php"><i class="typcn typcn-messages"></i></a>
        </div><!-- az-header-message -->
        <div class="dropdown az-header-notification">
            <a href="" class="new"><i class="typcn typcn-bell"></i></a>
            <div class="dropdown-menu">
                <div class="az-dropdown-header mg-b-20 d-sm-none">
                    <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                </div>
                <h6 class="az-notification-title">Notifications</h6>
                <p class="az-notification-text">You have 3 unread notification</p>
                <div class="az-notification-list">
                    <div class="media new">
                        <div class="az-img-user"><img src="../landMap_V6/assets/images/person_1.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>New Land Property</strong> added in Batangas City</p>
                            <span>Mar 15 12:32pm</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media new">
                        <div class="az-img-user online"><img src="../landMap_V6/assets/images/person_1.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>Agricultural Land</strong> now available in Laguna</p>
                            <span>Mar 13 04:16am</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media new">
                        <div class="az-img-user"><img src="../landMap_V6/assets/images/person_1.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>Commercial Lot</strong> listed in Cavite</p>
                            <span>Mar 13 02:56am</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media">
                        <div class="az-img-user"><img src="../landMap_V6/assets/images/person_1.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>Residential Lot</strong> price updated in Quezon City</p>
                            <span>Mar 12 10:40pm</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                </div><!-- az-notification-list -->
                <div class="dropdown-footer"><a href="">View All Notifications</a></div>
            </div><!-- dropdown-menu -->
        </div><!-- az-header-notification -->
        <div class="dropdown az-profile-menu">
            <a href="" class="az-img-user"><img src="../landMap_V6/assets/images/person_1.jpg" alt=""></a>
            <div class="dropdown-menu">
                <div class="az-dropdown-header d-sm-none">
                    <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                </div>
                <h6 class="az-notification-title">Notifications</h6>
                <p class="az-notification-text">You have 3 unread notification</p>
                <div class="az-header-profile">
                    <div class="az-img-user">
                        <img src="../landMap_V6/assets/images/person_1.jpg" alt=""> <!-- Optionally replace with dynamic image -->
                    </div><!-- az-img-user -->
                    <h6 class="text-nowrap"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?></h6>
                    <span><?php echo isset($_SESSION['role_type']) ? htmlspecialchars($_SESSION['role_type']) : 'No Role'; ?></span>
                    <span><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'No Email'; ?></span>
                </div><!-- az-header-profile -->

                <a href="" class="dropdown-item" style="display: flex; align-items: center; padding: 8px 15px; color: #1b2e4b; transition: all 0.2s ease;">
                    <i class="typcn typcn-user-outline" style="margin-right: 10px; font-size: 18px;"></i>
                    <span>My Profile</span>
                </a>
                <a href="" class="dropdown-item" style="display: flex; align-items: center; padding: 8px 15px; color: #1b2e4b; transition: all 0.2s ease;">
                    <i class="typcn typcn-edit" style="margin-right: 10px; font-size: 18px;"></i>
                    <span>Edit Profile</span>
                </a>
                <a href="#" class="dropdown-item" style="display: flex; align-items: center; padding: 8px 15px; color: #1b2e4b; transition: all 0.2s ease;">
                    <i class="typcn typcn-cog-outline" style="margin-right: 10px; font-size: 18px;"></i>
                    <span>Account Settings</span>
                </a>

                <button id="signOutButton" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none; cursor: pointer; display: flex; align-items: center; padding: 8px 15px;">
                    <i class="typcn typcn-power-outline" style="margin-right: 10px; color: #dc3545;"></i>
                    <span style="color: #dc3545;">Sign Out</span>
                </button>
            </div><!-- dropdown-menu -->
        </div>
    </div><!-- az-header-right -->
</div><!-- NAVBAR -->