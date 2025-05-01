<?php
require_once 'config.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expenses'])) {
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $expenses = $_POST['expenses'];
        $insertCount = 0;
        
        foreach ($expenses as $expense) {
            // Sanitize inputs
            $expenseCode = sanitize($conn, $expense['expenseCode']);
            $userCode = sanitize($conn, $expense['userCode']);
            $compCode = sanitize($conn, $expense['compCode']);
            $expSubChannel = sanitize($conn, $expense['expSubChannel']);
            $expcost = (float) $expense['expcost'];
            $expdescrptn = sanitize($conn, $expense['expdescrptn']);
            $exdate = sanitize($conn, $expense['exdate']);
            $biztypeexp = sanitize($conn, $expense['biztypeexp']);
            $addedby = sanitize($conn, $expense['addedby']);
            
            // Prepare and execute the SQL statement
            $sql = "INSERT INTO expenses (expenseCode, userCode, compCode, expSubChannel, expcost, expdescrptn, exdate, biztypeexp, addedby) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssdssss", 
                $expenseCode, 
                $userCode, 
                $compCode, 
                $expSubChannel, 
                $expcost, 
                $expdescrptn, 
                $exdate, 
                $biztypeexp, 
                $addedby
            );
            
            if ($stmt->execute()) {
                $insertCount++;
            } else {
                throw new Exception("Error inserting expense: " . $stmt->error);
            }
            
            $conn->close();
            exit;
        }
    }
}
?>