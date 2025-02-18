<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require '../../db.php';
    $query = "SELECT * FROM cms LIMIT 1";
    $result = $conn->query($query);
    $cms = $result->fetch_assoc();
?>



                     
<div class="container">

    <!--Logo Head-->

    <div class="az-header-left">
        <img src="../../assets/images/cms/<?= htmlspecialchars($cms['logo'] ?? '') ?>" alt="Logo" style="width: 60px; height: 60px;">
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

            <!-- <li class="">
                <a href="../frontend_users/agent_developers.php" class="nav-link"><i class="fas fa-building"></i> Developers</a>
            </li> -->

            <li class="">
                <a href="../frontend_users/user_agents_admin.php" class="nav-link"><i class="fas fa-user-tie"></i> Brokers</a>
            </li>

            <li class="">
                <a href="../frontend_users/admin_control.php" class="nav-link"><i class="fas fa-user-cog"></i> Admin Control</a>
            </li>

           
        </ul>

        <!--Navigation Bar Tail-->

    </div>

    <div class="az-header-right">
    <a href="admin_crm.php" class="az-header-search-link"><i class="fas fa-handshake"></i></a>
        <a href="../frontend_users/user_landproperties.php" class="az-header-search-link"><i class="fas fa-search"></i></a>
        <div class="az-header-message">
            <a href="../frontend_users/user_chat.php"><i class="typcn typcn-messages"></i></a>
        </div><!-- az-header-message -->

        <?php
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch notifications with agent profile if agent_id is available
$sql = "SELECT n.notification, n.created_at, u.profile, n.user_id, n.is_seen
        FROM notifications n
        LEFT JOIN users u ON n.agent_id = u.user_id
        WHERE n.user_id = ? OR n.user_id = 0
        ORDER BY n.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Get the count of unseen notifications
$query = "SELECT COUNT(*) AS unseen_count FROM notifications WHERE is_seen = 0";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$unseenCount = $row['unseen_count'];
?>


<div class="dropdown az-header-notification">
    <a href="#" class="notif-bell position-relative">
        <i class="typcn typcn-bell"></i>
        <?php if ($unseenCount > 0): ?>
            <span class="badge badge-danger notif-count"><?= $unseenCount ?></span>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu" style="width: 350px;">
        <div class="az-dropdown-header mg-b-20 d-sm-none">
            <a href="#" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
        </div>
        <h6 class="az-notification-title">Notifications</h6>
        <div class="az-notification-list">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notif): ?>
                    <?php 
                        $profileImage = !empty($notif['profile']) ? "../../assets/profile_images/" . $notif['profile'] : "../img/faces/default.jpgs";
                        
                        // Determine the notification link
                        if ($notif['user_id'] == 0) {
                            $notifLink = "user_landproperties.php#land_property";                      
                        } elseif ($notif['user_id'] == $user_id) {
                            $notifLink = "user_inquiries.php?notif_id=" . urlencode($notif['notification']);
                        } else {
                            $notifLink = "#"; // Default fallback if needed
                        }
                    ?>
                    <a href="<?= $notifLink ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="media <?= ($notif['is_seen'] == 0) ? 'new' : '' ?>">
                            <div class="az-img-user">
                                <img src="<?= $profileImage ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%;">
                            </div>
                            <div class="media-body">
                                <p><strong><?= htmlspecialchars($notif['notification']) ?></strong></p>
                                <span><?= date("M d h:ia", strtotime($notif['created_at'])) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No new notifications</p>
            <?php endif; ?>
        </div><!-- az-notification-list -->
        <div class="dropdown-footer"><a href="user_inquiries.php">View All Notifications</a></div>
    </div><!-- dropdown-menu -->
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $(".notif-bell").click(function() {
        $.ajax({
            url: "../../backend/mark_notifications_seen.php",
            method: "POST",
            success: function() {
                $(".notif-count").fadeOut(); 
                $(".media.new").removeClass("new");
            }
        });
    });
});
</script>

<style>
.badge-danger.notif-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: red;
    color: white;
    font-size: 10px;
    padding: 3px 7px;
    border-radius: 50%;
}
</style>

<?php
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = null;
    }

    $user_id = $_SESSION['user_id'];
    $query = "SELECT profile FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profileImageUser);
    $stmt->fetch();
    $stmt->close();

    $profileImageUser = !empty($profileImageUser) ? "../../assets/profile_images/" . htmlspecialchars($profileImageUser) : "../assets/profile_images/profile.jpg";
?>

        <div class="dropdown az-profile-menu">
            <a href="" class="az-img-user"> <img src="<?= $profileImageUser ?>"></a>
            <div class="dropdown-menu">
                <div class="az-dropdown-header d-sm-none">
                    <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                </div>
                <div class="az-header-profile">
                    <div class="az-img-user">
                       <img src="<?= $profileImageUser ?>"> 
                    </div><!-- az-img-user -->
                    <h6 class="text-nowrap"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?></h6>
                    <span><?php echo isset($_SESSION['role_type']) ? htmlspecialchars($_SESSION['role_type']) : 'No Role'; ?></span>
                    <span><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'No Email'; ?></span>
                </div><!-- az-header-profile -->

                <a href="profile_admin.php" class="dropdown-item" styl\e="display: flex; align-items: center; padding: 8px 15px; color: #1b2e4b; transition: all 0.2s ease;">
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