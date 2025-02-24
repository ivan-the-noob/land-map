<?php
require '../db.php';
date_default_timezone_set('Asia/Manila');
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

    // Array of blocked words (case insensitive, will match words even if combined)
    $blocked_words = [
        'putang\s*ina\s*mo', 'gago', 'hyp', 'puke', 'tanga\s*ina', 'libag', 'bakla', 'bulag',
        'king\s*ina\s*mo', 'kupal', 'yawa', 'dick', 'inutil', 'tarantado', 'fuck\s*you', 
        'fck', 'pakyu', 'g@go', 'mamatay\s*kana', 'hindut', 'puta', 'deputa'
    ];

    $sql = "SELECT m.*, u.profile, CONVERT_TZ(m.created_at, '+00:00', '+08:00') AS local_created_at 
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
        // Create regex pattern (case insensitive)
        $pattern = '/' . implode('|', $blocked_words) . '/i';

        // Replace blocked words with '**'
        $filtered_message = preg_replace($pattern, '***', $row['message']);
        $row['message'] = $filtered_message;
        $messages[] = $row;
    }

    $totalMessages = count($messages);
    
    for ($i = 0; $i < $totalMessages; $i++) {
        $row = $messages[$i];
        $isUser = ($row['role_type'] === 'user') ? 'user-message' : 'agent-message';
        $profileImage = !empty($row['profile']) ? "../../assets/profile_images/" . $row['profile'] : "../../assets/profile_images/profile.jpg";

        $nextSender = ($i < $totalMessages - 1) ? $messages[$i + 1]['role_type'] : null;
        $showProfile = ($isUser === 'user-message' && $nextSender !== 'user') 
            ? '<img src="' . $profileImage . '" alt="Profile" class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; margin: 0;">' 
            : '<div style="width: 40px;"></div>'; 

        $messageTime = date('h:i A', strtotime($row['local_created_at']));

        echo '<div class="chat-message d-flex ' . $isUser . '" style="gap: 5px; align-items: flex-start;">' . 
                $showProfile . 
                '<div class="message-box">' . nl2br(htmlspecialchars($row['message'])) . 
                '<div class="message-time" style="font-size: 12px; color: gray; margin-top: 2px;">' . $messageTime . '</div>' . 
                '</div>
              </div>';
    }
}
?>
