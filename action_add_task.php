<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $due_date, $status]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding task: " . $e->getMessage());
    }
}
?>