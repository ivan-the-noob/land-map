<div class="container">

    <!--Logo Head-->

    <div class="az-header-left">
        <a href="../frontend/frontend_users/agent_page.php" class="az-logo">Land<span class="text-primary">Map</span></a>
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
                <a href="/frontend/frontend_users/agent_page.php" class="nav-link"><i class="typcn typcn-th-list"></i> My Listing</a>
            </li>
            <!--for user to-->
            <li class="">
                <a href="/home" class="nav-link"><i class="typcn typcn-home"></i>Properties</a>
            </li>

            <li class="">
                <a href="/frontend/frontend_users/chat.php" class="nav-link"><i class="typcn typcn-messages"></i> Chat</a>
            </li>

            <li class="">
                <a href="/forms" class="nav-link"><i class="typcn typcn-"></i> page</a>
            </li>

            <li class="">
                <a href="" class="nav-link with-sub"><i class="typcn typcn-"></i> page</a>
                <div class="az-menu-sub">
                    <div class="container">
                        <div>
                            <nav class="nav">
                                <a href="/buttons" class="nav-link">Buttons</a>
                                <a href="/dropdown" class="nav-link">Dropdown</a>
                                <a href="/icons" class="nav-link">Icons</a>
                                <a href="/table" class="nav-link">Table</a>
                            </nav>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <!--Navigation Bar Tail-->

    </div>

    <div class="az-header-right">
        <a href="https://www.bootstrapdash.com/demo/azia-free/docs/documentation.html" target="_blank"
            class="az-header-search-link"><i class="far fa-file-alt"></i></a>
        <a href="" class="az-header-search-link"><i class="fas fa-search"></i></a>
        <div class="az-header-message">
            <a href="#"><i class="typcn typcn-messages"></i></a>
        </div><!-- az-header-message -->
        <div class="dropdown az-header-notification">
            <a href="" class="new"><i class="typcn typcn-bell"></i></a>
            <div class="dropdown-menu">
                <div class="az-dropdown-header mg-b-20 d-sm-none">
                    <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                </div>
                <h6 class="az-notification-title">Notifications</h6>
                <p class="az-notification-text">You have 2 unread notification</p>
                <div class="az-notification-list">
                    <div class="media new">
                        <div class="az-img-user"><img src="../img/faces/face2.jpg" alt=""></div>
                        <div class="media-body">
                            <p>Congratulate <strong>Socrates Itumay</strong> for work anniversaries</p>
                            <span>Mar 15 12:32pm</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media new">
                        <div class="az-img-user online"><img src="../img/faces/face3.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>Joyce Chua</strong> just created a new blog post</p>
                            <span>Mar 13 04:16am</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media">
                        <div class="az-img-user"><img src="../img/faces/face4.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>Althea Cabardo</strong> just created a new blog post</p>
                            <span>Mar 13 02:56am</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media">
                        <div class="az-img-user"><img src="../img/faces/face5.jpg" alt=""></div>
                        <div class="media-body">
                            <p><strong>Adrian Monino</strong> added new comment on your photo</p>
                            <span>Mar 12 10:40pm</span>
                        </div><!-- media-body -->
                    </div><!-- media -->
                </div><!-- az-notification-list -->
                <div class="dropdown-footer"><a href="">View All Notifications</a></div>
            </div><!-- dropdown-menu -->
        </div><!-- az-header-notification -->
        <div class="dropdown az-profile-menu">
            <a href="" class="az-img-user"><img src="../img/faces/face1.jpg" alt=""></a>
            <div class="dropdown-menu">
                <div class="az-dropdown-header d-sm-none">
                    <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                </div>
                <div class="az-header-profile">
                    <div class="az-img-user">
                        <img src="../img/faces/face1.jpg" alt=""> <!-- Optionally replace with dynamic image -->
                    </div><!-- az-img-user -->
                    <h6 class="text-nowrap"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h6>
                    <span><?php echo htmlspecialchars($_SESSION['role_type']); ?></span>
                    <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                </div><!-- az-header-profile -->

                <a href="/frontend/frontend_users/profile.php" class="dropdown-item"><i class="typcn typcn-user-outline"></i> My Profile</a>
                <a href="/editprofile" class="dropdown-item"><i class="typcn typcn-edit"></i> Edit Profile</a>
                <a href="" class="dropdown-item"><i class="typcn typcn-cog-outline"></i> Account Settings</a>
                <button id="signOutButton" class="dropdown-item"><i class="typcn typcn-power-outline"></i> Sign
                Out</a></button>
            </div><!-- dropdown-menu -->
        </div>
    </div><!-- az-header-right -->
</div><!-- NAVBAR -->