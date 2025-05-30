<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="header">
        <img src="CIHE_logo.png" alt="Logo" class="logo">
        <div class="nav">
            <div class="dropdown">
                <a href="admin.php" class="nav-link active">Dashboard</a>
                <div class="dropdown-content">
                    <a href="search.php">Select Course and Units</a>
                    <a href="add_units.php">Add Units</a>
                </div>
            </div>
            <a href="studentpp.php">Student Profile</a>
            <a href="index.php" id="logout">Logout</a>
            <div class="dropdown">
                <a href="#">Settings</a>
                <div class="dropdown-content">
                    <a href="#popForm">Change Password</a>
                </div>
            </div>
        </div>
    </div>
    <div id="popForm" class="popUp">
        <a href="#" class="close-btn">✖</a>
        <form class="popMe">
          <label for="old">Old Password <span class="astrick">*</span></label>
          <input class="user" type="password" id="password" name="password" required>
          <label for="new">New Passowrd <span class="astrick">*</span></label>
          <input class="user" type="password" id="password" pattern="^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$?])[A-Za-z\d@#$?]{6,10}$" minlength="6" name="password" required>
          <label for="confirm">Confirm-Passowrd <span class="astrick">*</span></label>
          <input class="user" pattern="^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$?])[A-Za-z\d@#$?]{6,10}$" minlength="6" type="password" id="confirmpassword" name="confirmpassword" required>
          
          <button class="change" type="submit">Change</button>
        </form>
      </div>
    

    <div class="course-details-container">
        <h1>ICT305 - Topics in IT</h1>
        
        <div class="course-info-card">
            <h2>Course Information</h2>
            <p><strong>Semester:</strong> 2, 2024</p>
            <p><strong>Teachers:</strong> Omar Shindi, Fareed Ud Din, Md Ashraf Uddin</p>
            <p><strong>Description:</strong> Advanced topics in Information Technology covering current trends and technologies.</p>
            
            <a href="enrollmentstats.php" class="view-enrollment-btn">
                View Enrollment Statistics
            </a>
        </div>
    </div>

    <div class="footer">
        Copyright &copy; 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved.<br>
        Privacy Policy | Terms of Use
    </div>
</body>
</html>
