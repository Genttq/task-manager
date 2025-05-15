<?php
include 'includes/db.php';

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        header("Location: index.php?delete_success=1");
        exit();
    } catch (PDOException $e) {
        die("Error deleting task: " . $e->getMessage());
    }
}
?>