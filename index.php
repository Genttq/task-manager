<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        .title-divider {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .success-alert {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .title-divider {
            background: RGB(51, 51, 51);
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .tasks-title {
            background: RGB(51, 51, 51);
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .btn-view { background: #000; color: white; }
        .btn-edit { background: #ffa500; color: black; }
        .btn-delete { background: #dc3545; color: white; }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- Main Title with Divider -->
    <div class="title-divider">
        <h2>Task Manager</h2>
    </div>

    <!-- Success Message -->
    <?php if(isset($_GET['delete_success'])): ?>
        <div class="success-alert">
            ✓ Task deleted successfully!
        </div>
    <?php endif; ?>

   <!-- Filter Section -->
<form method="GET" class="filter-section" onsubmit="return validateFilters()">
    <div class="row align-items-center">
        <div class="col-md-4">
            <label><strong>Status:</strong></label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="In Progress" <?= ($_GET['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="Completed" <?= ($_GET['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <div class="col-md-5">
            <label><strong>Search:</strong></label>
            <input type="text" name="search" class="form-control" placeholder="Search..." 
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-dark w-50">Apply Filters</button>
            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-secondary w-50">Clear</a>
        </div>
    </div>
</form>

    <div class="d-flex justify-content-end my-3">
    <a href="add_task.php" class="btn btn-success">Add New Task</a>
</div>

    <!-- "Tasks" Title with Black Background -->
    <div class="d-flex justify-content-between align-items-center tasks-title">
        <h2 class="m-0">Tasks</h2>
    </div>

   <!-- Tasks Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Title</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch tasks with filters
        $status_filter = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $sql = "SELECT * FROM tasks WHERE 1=1";
        $params = [];

        if ($status_filter) {
            $sql .= " AND status = ?";
            $params[] = $status_filter;
        }

        if ($search) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Always use prepared statements
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) :
        ?>
        <tr>
            <td><?= htmlspecialchars($task['title']) ?></td>
            <td><?= date('M d, Y', strtotime($task['due_date'])) ?></td>
            <td>
                <span class="badge 
                    <?= $task['status'] === 'Pending' ? 'bg-warning' : '' ?>
                    <?= $task['status'] === 'In Progress' ? 'bg-primary' : '' ?>
                    <?= $task['status'] === 'Completed' ? 'bg-success' : '' ?>
                ">
                    <?= $task['status'] ?>
                </span>
            </td>
            <td>
                <a href="view_task.php?id=<?= $task['id'] ?>" class="btn btn-view btn-sm">View</a>
                <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-edit btn-sm">Edit</a>
                <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-delete btn-sm delete-btn" data-id="<?= $task['id'] ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
<script>
function validateFilters() {
    const status = document.querySelector('[name="status"]').value.trim();
    const search = document.querySelector('[name="search"]').value.trim();

    if (!status && !search) {
        alert("Please enter a search term or select a status.");
        return false;
    }
    return true;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault(); // prevent the default link behavior
        const taskId = this.getAttribute('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to delete this task?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `delete_task.php?id=${taskId}`;
            }
        });
    });
});
</script>

</body>
</html>