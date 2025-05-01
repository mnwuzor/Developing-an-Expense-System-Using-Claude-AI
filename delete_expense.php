<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Delete expense
$stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);

// Attempt to execute
if ($stmt->execute()) {
    // Set success message in session
    session_start();
    $_SESSION['message'] = "Expense deleted successfully";
    $_SESSION['message_type'] = "success";
} else {
    // Set error message in session
    session_start();
    $_SESSION['message'] = "Error deleting expense: " . $conn->error;
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();

// Redirect back to index
header('Location: index.php');
exit;
?>