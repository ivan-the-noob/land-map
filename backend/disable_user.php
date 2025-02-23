<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'] ?? null;

    if (!$property_id) {
        echo json_encode(["success" => false, "error" => "Property ID is missing."]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get the agent (user_id) who owns the reported property
        $query_get_agent = "SELECT user_id FROM properties WHERE property_id = ?";
        $stmt_get_agent = $conn->prepare($query_get_agent);
        $stmt_get_agent->bind_param("i", $property_id);
        $stmt_get_agent->execute();
        $result_get_agent = $stmt_get_agent->get_result();
        $agent = $result_get_agent->fetch_assoc();
        $stmt_get_agent->close();

        if (!$agent) {
            echo json_encode(["success" => false, "error" => "No agent found for this property."]);
            exit;
        }

        $agent_id = $agent['user_id'];

        // Ensure the agent exists
        $check_agent = "SELECT user_id FROM users WHERE user_id = ?";
        $stmt_check = $conn->prepare($check_agent);
        $stmt_check->bind_param("i", $agent_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows === 0) {
            echo json_encode(["success" => false, "error" => "Agent not found."]);
            exit;
        }
        $stmt_check->close();

        // Increment disable_status but limit to 3 max
        $query1 = "UPDATE users SET disable_status = LEAST(disable_status + 1, 3) WHERE user_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("i", $agent_id);
        $stmt1->execute();
        $stmt1->close();

        // Delete all reports related to this agent and property
        $query2 = "DELETE FROM report_properties WHERE property_id = ? AND user_id = ?";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("ii", $property_id, $agent_id);
        $stmt2->execute();
        $stmt2->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    $conn->close();
}
