<?php
require_once 'includes/admin_header.php';
require_once 'backend/teacher_operations.php';

// Get all units and teachers
$units = getAllUnits();
$teachers = getAllTeachers();
?>

<div class="main-container">
    <div class="content-wrapper">
        <div class="page-header">
            <h1>Teacher Management</h1>
        </div>

        <!-- Add Teacher Form -->
        <div class="card">
            <div class="card-header">
                <h2>Add New Teacher</h2>
            </div>
            <div class="card-body">
                <form id="addTeacherForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="teacher_id">Teacher ID</label>
                            <input type="text" id="teacher_id" name="teacher_id" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Assign Units</label>
                        <div class="units-grid">
                            <?php foreach ($units as $unit): ?>
                            <div class="unit-checkbox">
                                <input type="checkbox" id="unit_<?php echo $unit['id']; ?>" 
                                       name="units[]" value="<?php echo $unit['id']; ?>">
                                <label for="unit_<?php echo $unit['id']; ?>">
                                    <?php echo $unit['unit_code'] . ' - ' . $unit['unit_name']; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Add Teacher</button>
                </form>
            </div>
        </div>

        <!-- Teachers List -->
        <div class="card">
            <div class="card-header">
                <h2>Current Teachers</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Teacher ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Assigned Units</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teacher['teacher_id']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['department']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['units'] ?? 'None'); ?></td>
                                <td>
                                    <button class="btn-danger delete-teacher" data-teacher-id="<?php echo $teacher['id']; ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.main-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.content-wrapper {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.page-header h1 {
    margin: 0;
    color: #333;
    font-size: 24px;
}

.card {
    margin-bottom: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.card-header h2 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.card-body {
    padding: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.units-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 10px;
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.unit-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px;
}

.unit-checkbox input[type="checkbox"] {
    width: auto;
}

.btn-primary {
    background: #3498db;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-danger {
    background: #e74c3c;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.3s;
}

.btn-danger:hover {
    background: #c0392b;
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.data-table td:last-child {
    white-space: nowrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    const addTeacherForm = document.getElementById('addTeacherForm');
    addTeacherForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'add_teacher');
        
        fetch('backend/teacher_operations.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the teacher.');
        });
    });
    
    // Handle teacher deletion
    const deleteButtons = document.querySelectorAll('.delete-teacher');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this teacher?')) {
                const teacherId = this.dataset.teacherId;
                const formData = new FormData();
                formData.append('action', 'delete_teacher');
                formData.append('teacher_id', teacherId);
                
                fetch('backend/teacher_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the teacher.');
                });
            }
        });
    });
});
</script>
