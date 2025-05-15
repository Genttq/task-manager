<?php
include 'includes/db.php';

// Get task ID from URL
$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    echo "Task ID is missing.";
    exit;
}

// Fetch task details securely
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "Task not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
        }
        .task-card {
            max-width: 700px;
            margin: auto;
            margin-top: 60px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card task-card shadow p-4">
        <h3 class="text-center mb-4">📋 Task Details</h3>

        <div class="mb-3">
            <h5>Title:</h5>
            <p><?= htmlspecialchars($task['title']) ?></p>
        </div>

        <div class="mb-3">
            <h5>Description:</h5>
            <p><?= nl2br(htmlspecialchars($task['description'])) ?></p>
        </div>

        <div class="mb-3">
            <h5>Due Date:</h5>
            <p><?= date('F d, Y', strtotime($task['due_date'])) ?></p>
        </div>

        <div class="mb-3">
            <h5>Status:</h5>
            <span class="badge 
                <?= $task['status'] === 'Pending' ? 'bg-warning text-dark' : '' ?>
                <?= $task['status'] === 'In Progress' ? 'bg-primary' : '' ?>
                <?= $task['status'] === 'Completed' ? 'bg-success' : '' ?>
            ">
                <?= htmlspecialchars($task['status']) ?>
            </span>
        </div>

        <div class="text-end mt-4">
            <a href="index.php" class="btn btn-secondary">← Back to List</a>
        </div>
    </div>
</div>
</body>
</html>
