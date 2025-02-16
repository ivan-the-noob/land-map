<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'], $_POST['agent_id'])) {
    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id'];
    $role_type = $_SESSION['role_type']; 
    $agent_id = $_POST['agent_id'];

    if (empty($user_id) || empty($role_type)) {
        echo "error: user not logged in";
        exit;
    }

    $sql = "SELECT m.*, u.profile 
            FROM messages m 
            LEFT JOIN users u ON m.user_id = u.user_id 
            WHERE m.property_id = ? AND (m.user_id = ? OR m.agent_id = ?) 
            ORDER BY m.created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $property_id, $user_id, $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    $totalMessages = count($messages);
    
    for ($i = 0; $i < $totalMessages; $i++) {
        $row = $messages[$i];
        $isUser = ($row['role_type'] === 'agent') ? 'agent-message' : 'user-message';
        $profileImage = !empty($row['profile']) ? "../../assets/profile_images/" . $row['profile'] : "../../assets/profile_images/profile.jpg";

        $nextSender = ($i < $totalMessages - 1) ? $messages[$i + 1]['role_type'] : null;
        $showProfile = ($isUser === 'agent-message' && $nextSender !== 'agent') 
            ? '<img src="' . $profileImage . '" alt="Profile" class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; margin: 0;">' 
            : '<div style="width: 40px;"></div>'; 

        $messageTime = date('H:i', strtotime($row['created_at']));

        echo '<div class="chat-message d-flex ' . $isUser . '" style="gap: 5px; align-items: flex-start;">' . 
                $showProfile . 
                 
                    '<div class="message-box">' . nl2br(htmlspecialchars($row['message'])) . 
                    '<div class="message-time" style="font-size: 12px; color: grey; margin-top: 2px wio;">' . $messageTime . '</div>' . 
              
                '</div>
              </div>';
    }
}
?>
