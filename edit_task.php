<?php
include 'includes/db.php';

// Get task ID
$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    echo "Task ID is missing.";
    exit;
}

// Fetch task
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "Task not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $status = $_POST['status'] ?? '';

    // Basic validation (could add more)
    if ($title && $due_date && $status) {
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $description, $due_date, $status, $task_id]);
        header("Location: index.php?update_success=1");
        exit;
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
        }
        .edit-card {
            max-width: 700px;
            margin: auto;
            margin-top: 60px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card edit-card shadow p-4">
        <h3 class="text-center mb-4">✏️ Edit Task</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label"><strong>Title</strong></label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label"><strong>Description</strong></label>
                <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label"><strong>Due Date</strong></label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="<?= htmlspecialchars($task['due_date']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label"><strong>Status</strong></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>

            <div class="text-end mt-4">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Task</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
