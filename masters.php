<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master rego</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>
    <div class="log">
        <p>Logged in as Student (CIHE22580)</p>
        
    </div>
 
    <div class="nav">
        <div class="logo">
            <a href="home.php">
                <img src="image.png" alt="Logo">
            </a>
        </div>
     
        <div class="links">
            <ul>
             <li class="dropdown">
                    <a href="home.php">Dashboard</a>
                    <ul  class="dropdown-menu">
                        <li><a href="profile.php">Profile</a></li>
                    </ul>
                </li>
                <li><a href="subjectreg.php">Subject Registration</a></li>
                <li><a href="Support.php">Support</a></li>
                <li><a href="about.php">About</a></li>
               
                <li class="dropdown"><a href="">Setting</a>
                    <ul class="dropdown-menu">
                          <li><a href="#popForm">Change Password</a></li>
                    </ul>
                </li>
            </ul>
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
   
      <header class="loggedIn">
        <h2>Welcome, Student</h2>
        <div class="buttons">
            <i class="fa-solid fa-bell"></i>
             <button id="destroy" onclick="return showModel()"><a href="logoutpop.php"><i class="fa-solid fa-door-open"></i> Logout</a></button>
         </div>
    </header>
    
<script> 
        function showModel(){
            document.getElementById("logoutMode").style.display = "flex";
            document.body.classList.add("blurred"); /* Blurs the whole body content when clicked on logout button*/
           
            return false;
            }
        function hideModel(){
            document.getElementById("logoutMode").style.display = "none";
            document.body.classList.remove("blurred"); /* remmoves the blur of the whole body content when clicked on NO */
        }
        function confirmLogout(){
        window.location.href = "index.php";
        }
</script>

    <div class="confirm-btns" id="logoutMode" style="display: none; justify-content: center;">
        <div class="comfirm-box">
          <p class="sure">Are you sure to logout?</p>
           <div class="confirm-buttons">
               <button onclick="return confirmLogout()" class="yes-btn">Yes</button>
               <button onclick="return hideModel()" class="no-btn">No</button>
           </div>
        </div>
    </div>

   
    <div class="location1">
        <select>
            <option value="">Select locations</option>
            <option value="Gungahlin">Gungahlin</option>
            <option value="Belconnen">Belconnen</option>
            <option value="City">City</option>
        </select>
    </div>

    <p class="reach" style="padding: 20px;">Please select the subject and desired class session(s) and click Register button.</p>
    <hr>
<div class="subjects">
    <h2 style="text-align: center; font-size: 40px; margin-top: -10px;">Masters of IT</h2>
    <div class="subject1">
        <h3 class="elective" style="text-align: center;">ICT910 - Enterprise Systems Security (Prerequisite: None) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox">ICT910</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>Mani Bahadur</option>
                            <option>Nam HOI</option>
                            <option>Dhiraj GC</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="subject2">
        <h3 class="elective" style="text-align: center;">ICT911 - Database Management Systems (Prerequisite: None) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"> ICT911</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>Ugyen Choden</option>
                            <option>Bishowraj Chaulagain</option>
                            <option>Pema Thinley</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="subject3">
        <h3 class="elective" style="text-align: center;">ICT920 - Management Information Systems (Prerequisite: ICT910 Enterprise Systems Security) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"> BUS920</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>RR Rafeh</option>
                            <option>Omar Sindhi</option>
                            <option>Mani Bakshi</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="subject4">
        <h3 class="elective" style="text-align: center;">ICT912 - Programming (Prerequisite: None) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"> ICT912</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>Dr. Patricia Ng</option>
                            <option>Subham Sharma</option>
                            <option>Jhalak Dhakal</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="subject4">
        <h3 class="elective" style="text-align: center;">ICT931 - Cybersecurity Incident Response (Prerequisite: ICT910 Enterprise Systems Security) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"> ICT931</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>Dr. Patricia Ng</option>
                            <option>Subham Sharma</option>
                            <option>Jhalak Dhakal</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="subject4">
        <h3 class="elective" style="text-align: center;">ICT921 - Applied Software Engineering (Prerequisite: ICT912 Programming) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"> ICT921</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>Dr. Patricia Ng</option>
                            <option>Subham Sharma</option>
                            <option>Jhalak Dhakal</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="subject4">
        <h3 class="elective" style="text-align: center;">ICT913 - Networking (Prerequisite: None) (Core-CP: 10.00)</h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"> ICT913</td>
                    <td>
                        <select>
                            <option>Select teacher...</option>
                            <option>Dr. Patricia Ng</option>
                            <option>Subham Sharma</option>
                            <option>Jhalak Dhakal</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select day...</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option>Select time...</option>
                            <option>8:30am-11:30am</option>
                            <option>6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="btn-container">
    <button id="register" class="button">Register</button>
</div>

<div class="back" style="text-align: center; margin-top: 40px;">
    <span><a  style="color: black; cursor: pointer;" href="subjectreg.php"><i style="margin-right: 10px; cursor: pointer; color: black;" class="fa fa-arrow-left"></i>Back to Sub-registration</a></span>
</div>
<script>
    // Simple JavaScript for the popup form
    document.querySelectorAll('[href="#popForm"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('popForm').style.display = 'flex';
        });
    });
    
    document.querySelector('.close-btn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('popForm').style.display = 'none';
    });
    
    // Notification bell click handler
    document.querySelector('.fa-bell').addEventListener('click', function() {
        alert('You have 3 new notifications');
    });
    
    // Register button click handler
    document.getElementById('register').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select at least one subject to register');
        } else {
            alert('Registration successful!');
            window.location.href = "profile.php";
            return true;
        }
    });

</script>
<footer>
   
    <div>
        &copy; Copyright 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved. Privacy Policy | Terms of Use
    </div>
    </footer>
</body>
</html>
