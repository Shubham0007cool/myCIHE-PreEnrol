<?php
require_once 'includes/admin_header.php';
require_once 'backend/admin_operations.php';

// Get current year
$currentYear = date('Y');
$nextYear = $currentYear + 1;
?>

<style>
    .main {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .card-header {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .card-header h2 {
        margin: 0;
        color: #333;
        font-size: 1.5em;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-group textarea {
        height: 100px;
        resize: vertical;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .submit-button {
        background: #3498db;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s;
    }

    .submit-button:hover {
        background: #2980b9;
    }

    .units-container {
        max-height: 600px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .unit-option {
        margin-bottom: 8px;
        padding: 8px;
        border: 1px solid #eee;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .unit-option:hover {
        background-color: #f8f9fa;
    }

    .unit-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .unit-code {
        font-weight: bold;
        color: #2c3e50;
    }

    .unit-name {
        color: #34495e;
    }

    .prerequisite {
        font-size: 0.8em;
        color: #7f8c8d;
        margin-left: 20px;
        font-style: italic;
    }
</style>

<div class="main">
    <!-- Add New Unit Form -->
    <div class="card">
        <div class="card-header">
            <h2>Add New Unit</h2>
        </div>
        <form id="addUnitForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="unit_code">Unit Code</label>
                    <input type="text" id="unit_code" name="unit_code" required>
                </div>
                <div class="form-group">
                    <label for="unit_name">Unit Name</label>
                    <input type="text" id="unit_name" name="unit_name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="credits">Credits</label>
                    <input type="number" id="credits" name="credits" min="1" max="20" required>
                </div>
                <div class="form-group">
                    <label for="course">Course</label>
                    <select id="course" name="course" required>
                        <option value="">Select a course</option>
                        <option value="1">Bachelor of IT</option>
                        <option value="2">Master of IT</option>
                        <option value="3">Bachelor of Early Childhood Education</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="submit-button">Add Unit</button>
        </form>
    </div>

    <!-- Assign Units to Program -->
    <div class="card">
        <div class="card-header">
            <h2>Assign Units to Program</h2>
        </div>
        <form id="assignUnitsForm">
            <div class="form-group">
                <label for="program">Select Program</label>
                <select id="program" name="program" required onchange="loadUnits()">
                    <option value="">Select a program</option>
                    <option value="BICT">Bachelor of IT</option>
                    <option value="MIT">Master of IT</option>
                    <option value="BECE">Bachelor of Early Childhood Education</option>
                </select>
            </div>

            <div class="units-container" id="unitsContainer">
                <!-- Units will be loaded here dynamically -->
            </div>

            <button type="submit" class="submit-button">Assign Units</button>
        </form>
    </div>
</div>

<script>
// Handle new unit form submission
document.getElementById('addUnitForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'add_unit');
    
    try {
        const response = await fetch('backend/unit_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Unit added successfully');
            this.reset();
            // Reload units if a program is selected
            if (document.getElementById('program').value) {
                loadUnits();
            }
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while adding the unit.');
    }
});

// Handle unit assignment form submission
document.getElementById('assignUnitsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'assign_units');
    
    try {
        const response = await fetch('backend/unit_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Units assigned successfully');
            // Reload units to show updated assignments
            loadUnits();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while assigning units.');
    }
});

async function loadUnits() {
    const program = document.getElementById('program').value;
    if (!program) return;
    
    try {
        const response = await fetch('backend/unit_operations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'get_units',
                program: program
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('unitsContainer');
            container.innerHTML = '';
            
            // Group units by level
            const levels = {
                'Level 1': [],
                'Level 2': [],
                'Level 3': [],
                'Electives': []
            };
            
            data.units.forEach(unit => {
                const level = unit.code.startsWith('ICT1') ? 'Level 1' :
                            unit.code.startsWith('ICT2') ? 'Level 2' :
                            unit.code.startsWith('ICT3') ? 'Level 3' : 'Electives';
                levels[level].push(unit);
            });
            
            // Create sections for each level
            Object.entries(levels).forEach(([level, units]) => {
                if (units.length > 0) {
                    const section = document.createElement('div');
                    section.className = 'level-section';
                    section.setAttribute('data-program', program);
                    
                    const title = document.createElement('div');
                    title.className = 'level-title';
                    title.textContent = `${program} - ${level}`;
                    section.appendChild(title);
                    
                    // Group units by semester
                    const semester1 = units.filter(u => u.code.match(/[13579]$/));
                    const semester2 = units.filter(u => u.code.match(/[24680]$/));
                    
                    // Create semester groups
                    [semester1, semester2].forEach((semesterUnits, index) => {
                        if (semesterUnits.length > 0) {
                            const semesterGroup = document.createElement('div');
                            semesterGroup.className = 'semester-group';
                            semesterGroup.setAttribute('data-semester', `semester${index + 1}`);
                            
                            const semesterTitle = document.createElement('div');
                            semesterTitle.className = 'semester-title';
                            semesterTitle.textContent = `Semester ${index + 1}`;
                            semesterGroup.appendChild(semesterTitle);
                            
                            semesterUnits.forEach(unit => {
                                const unitOption = document.createElement('div');
                                unitOption.className = 'unit-option';
                                
                                const unitInfo = document.createElement('div');
                                unitInfo.className = 'unit-info';
                                
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.id = unit.code;
                                checkbox.name = 'units[]';
                                checkbox.value = unit.id;
                                checkbox.checked = unit.is_assigned;
                                
                                const label = document.createElement('label');
                                label.phpFor = unit.code;
                                
                                const codeSpan = document.createElement('span');
                                codeSpan.className = 'unit-code';
                                codeSpan.textContent = unit.code;
                                
                                const nameSpan = document.createElement('span');
                                nameSpan.className = 'unit-name';
                                nameSpan.textContent = ` - ${unit.name}`;
                                
                                label.appendChild(codeSpan);
                                label.appendChild(nameSpan);
                                
                                unitInfo.appendChild(checkbox);
                                unitInfo.appendChild(label);
                                unitOption.appendChild(unitInfo);
                                
                                if (unit.prerequisites && unit.prerequisites.length > 0) {
                                    const prereq = document.createElement('div');
                                    prereq.className = 'prerequisite';
                                    prereq.textContent = `Prerequisites: ${unit.prerequisites.join(', ')}`;
                                    unitOption.appendChild(prereq);
                                }
                                
                                semesterGroup.appendChild(unitOption);
                            });
                            
                            section.appendChild(semesterGroup);
                        }
                    });
                    
                    container.appendChild(section);
                }
            });
        }
    } catch (error) {
        console.error('Error loading units:', error);
    }
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('program');
    programSelect.addEventListener('change', loadUnits);
});
</script>


