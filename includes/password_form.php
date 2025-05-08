<?php
// Password change form
?>
<div id="popForm" class="popUp">
    <a href="#" class="close-btn" onclick="closePasswordForm()">✖</a>
    <form class="popMe" action="backend/update_password.php" method="POST" onsubmit="return validatePasswordForm()">
        <div class="password-field">
            <label for="old">Old Password <span class="astrick">*</span></label>
            <div class="password-input-container">
                <input class="user" type="password" id="old_password" name="old_password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('old_password')"></i>
            </div>
        </div>
        
        <div class="password-field">
            <label for="new">New Password <span class="astrick">*</span></label>
            <div class="password-input-container">
                <input class="user" type="password" id="new_password" name="new_password" 
                       
                       minlength="6" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
            </div>
            <small class="password-hint">Password must be 6-10 characters and contain letters, numbers, and @#$?</small>
        </div>
        
        <div class="password-field">
            <label for="confirm">Confirm Password <span class="astrick">*</span></label>
            <div class="password-input-container">
                <input class="user" type="password" id="confirm_password" name="confirm_password" 
                       
                       minlength="6" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
            </div>
        </div>
        
        <div id="passwordError" class="error-message"></div>
        
        <button class="change" type="submit">Change Password</button>
    </form>
</div>

<style>
.popUp {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    z-index: 1000;
}

.close-btn {
    position: absolute;
    right: 10px;
    top: 10px;
    text-decoration: none;
    color: #666;
    cursor: pointer;
}

.popMe {
    display: flex;
    flex-direction: column;
    gap: 15px;
    min-width: 300px;
}

.popMe label {
    font-weight: bold;
}

.password-field {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.password-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-container input {
    width: 100%;
    padding: 8px;
    padding-right: 35px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.toggle-password {
    position: absolute;
    right: 10px;
    cursor: pointer;
    color: #666;
    transition: color 0.3s;
}

.toggle-password:hover {
    color: #e05d00;
}

.astrick {
    color: red;
}

.change {
    background-color: #e05d00;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.change:hover {
    background-color: #c04f00;
}

.password-hint {
    color: #666;
    font-size: 0.8em;
    margin-top: -5px;
}

.error-message {
    color: #dc3545;
    font-size: 0.9em;
    margin-top: -10px;
    display: none;
}

.success-message {
    color: #28a745;
    font-size: 0.9em;
    margin-top: -10px;
    display: none;
}
</style>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function validatePasswordForm() {
    const oldPassword = document.getElementById('old_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorDiv = document.getElementById('passwordError');
    
    // Reset error message
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    
    // Validate old password
    if (!oldPassword) {
        showError('Please enter your current password');
        return false;
    }
    
    // Validate new password
    if (!newPassword) {
        showError('Please enter a new password');
        return false;
    }
    
    // Validate password format
    const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$?])[A-Za-z\d@#$?]{6,30}$/;
    if (!passwordRegex.test(newPassword)) {
        showError('Password must be 6-10 characters and contain letters, numbers, and @#$?');
        return false;
    }
    
    // Validate password confirmation
    if (newPassword !== confirmPassword) {
        showError('New passwords do not match');
        return false;
    }
    
    return true;
}

function showError(message) {
    const errorDiv = document.getElementById('passwordError');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function closePasswordForm() {
    document.getElementById('popForm').style.display = 'none';
    document.body.classList.remove('blurred');
}

// Show success/error messages from PHP session
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success'])): ?>
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.textContent = '<?php echo $_SESSION['success']; ?>';
        successDiv.style.display = 'block';
        document.querySelector('.popMe').insertBefore(successDiv, document.querySelector('.change'));
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        const errorDiv = document.getElementById('passwordError');
        errorDiv.textContent = '<?php echo $_SESSION['error']; ?>';
        errorDiv.style.display = 'block';
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});
</script> 