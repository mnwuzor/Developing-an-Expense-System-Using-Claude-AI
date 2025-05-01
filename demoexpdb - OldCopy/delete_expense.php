<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php', 'Invalid expense ID', 'danger');
}

$id = (int) $_GET['id'];

// Delete the expense
$sql = "DELETE FROM expenses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        // Success
        redirect('index.php', 'Expense deleted successfully', 'success');
    } else {
        // No rows were affected (ID not found)
        redirect('index.php', 'Expense not found', 'warning');
    }
} else {
    // Error
    redirect('index.php', 'Error deleting expense: ' . $stmt->error, 'danger');
}

$stmt->close();
$conn->close();