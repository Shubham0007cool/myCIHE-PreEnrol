<?php
require_once 'includes/student_header.php';
require_once 'includes/live_chat.php';
require_once 'includes/password_form.php';
?>


<!-- Welcome Note Section -->
<section class="welcome-note">
    <div class="welcome-container">
        <div class="welcome-text">
            <h1>Welcome to CIHE Pre-enrollment Website</h1>
            <h2>Empowering your Future</h2>
            <div class="welcome-message">
                <p>At CIHE Pre-Enrollment Website, we are committed to providing exceptional education path in Information Technology that prepares students for the dynamic digital landscape. Our innovative programs combine theoretical knowledge with practical skills to ensure our graduates are industry-ready.</p>
                
                <p>Whether you're beginning your IT journey or advancing your career, we offer a supportive learning environment with:</p>
                
                <ul>
                    <li><i class="fas fa-check-circle"></i> Industry-experienced faculty</li>
                    <li><i class="fas fa-check-circle"></i> Hands-on learning experiences</li>
                    <li><i class="fas fa-check-circle"></i> Cutting-edge curriculum</li>
                    <li><i class="fas fa-check-circle"></i> Small class sizes for personalized attention</li>
                    <li><i class="fas fa-check-circle"></i> Strong industry connections</li>
                </ul>
                
                <p>Explore our programs and discover how our website can help you achieve your academic and professional goals in the exciting world of technology.</p>
            </div>
            
            <div class="welcome-buttons">
                <a href="#degrees-section" class="btn1-primary">View Programs <i class="fas fa-arrow-right"></i> </a>
            </div>
        </div>
        
        <div class="welcome-image">
            <img src="girl.jpeg" alt="Students learning at CIHE">
        </div>
    </div>
</section>

<section class="slider-section">
    <div class="courseslides">
        <div class="college-heading">
            <h1>Explore...</h1>
        </div>
        <div class="course-track">
            <div class="course-slides">
                <img class="getslide" src="girl.jpeg" alt="Modern Campus">
                <div class="slide-content">
                    <h3>Easy Application Process</h3>
                    <p>Apply online with our streamlined admission portal and get guidance every step of the way.</p>
                </div>
            </div>
            <div class="course-slides">
                <img class="getslide" src="RR.jpeg" alt="Student Life">
                <div class="slide-content">
                    <h3>Scholarships & Aid</h3>
                    <p>Explore our merit-based and need-based scholarships designed to support your academic journey.</p>
                </div>
            </div>
            <div class="course-slides">
                <img class="getslide" src="background.jpg" alt="Technology Labs">
                <div class="slide-content">
                    <h3>Student Support Services</h3>
                    <p>From career counseling to mental health, we're committed to helping you thrive at CIHE.</p>
                </div>
            </div>
            <div class="course-slides">
                <img class="getslide" src="sports2.png" alt="Graduation">
                <div class="slide-content">
                    <h3>Successful Graduates</h3>
                    <p>Join our alumni network of industry professionals</p>
                </div>
            </div>
            
        </div>
        <div class="course-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
        <div class="welcome-buttons" style="justify-content: center;">
            <a href="about.php" class="btn2-secondary">Learn More <i class="fas fa-arrow-right"></i></a>
           </div>
           <style>
            .fa-arrow-right:hover{
                margin-left: 5px;
                transition: transform 0.3s;
            }
           </style>
    </div>
</section>

<section class="enrollment-info">
    <h2 style="text-align: center; margin-bottom: 15px; font-size: 20px;">Enrolment Help</h2></span>
    <div class="enrollment-help">
        <p style="font-size: 19px; text-align: center;">If you get any confusion while enrolling or prior enrolling, simply click on 
           
                <a class="son" href="Support.php">Contact Us </a> 
        to get assistance and seamless enrollment experience.</p> 
    </div>
    <div class="enrollment-container">
        <h2>Enrollment Periods & Deadlines</h2>
        
        <div class="enrollment-cards">
            <!-- Current Enrollment Period -->
            <div class="enrollment-card current">
                <div class="enrollment-header">
                    <h3>Current Enrollment Period</h3>

                    <span class="status-badge">Open Now</span>
                </div>
                <div class="enrollment-details">
                    <p><strong>Semester:</strong> Semester 1, 2025</p>
                    <p><strong>Enrollment Deadline:</strong> April 20, 2025</p>
                    <p><strong>Classes Begin:</strong> April 7, 2025</p>
                    <div class="countdown">
                        <p>Only <span class="days-remaining">13</span> days left to enroll!</p>
                        <div class="progress-bar">
                            <div class="progress" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
                <div class="enrol-button">
                    <a href="subjectreg.php" class="btn4-primary">Enroll Now<i class="fas fa-arrow-right"></i> </a>
                </div>
            </div>

        <!--Enrollment notice-->
        <div class="enrollment-notes">
            <h3>Important Notes:</h3>
            <ul>
                <li><i class="fas fa-exclamation-circle"></i> Late enrollments may incur additional fees</li>
                <li><i class="fas fa-exclamation-circle"></i> Some courses may have earlier deadlines due to high demand</li>
            </ul>
        </div>
</section>


<script>
// Countdown Timer Calculation
document.addEventListener('DOMContentLoaded', function() {
    // Set the deadline date (June 30, 2024)
    const deadline = new Date('2025-04-20');
    const today = new Date();
    
    // Calculate days remaining
    const timeDiff = deadline - today;
    const daysRemaining = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    
    // Update the countdown display
    const daysElement = document.querySelector('.days-remaining');
    if (daysElement) {
        daysElement.textContent = daysRemaining > 0 ? daysRemaining : 0;
    }
    
    // Calculate progress percentage (assuming enrollment opened on June 1)
    const enrollmentStart = new Date('2025-04-07');
    const totalDays = Math.ceil((deadline - enrollmentStart) / (1000 * 60 * 60 * 24));
    const daysPassed = Math.ceil((today - enrollmentStart) / (1000 * 60 * 60 * 24));
    const progressPercent = Math.min(100, Math.max(0, (daysPassed / totalDays) * 100));
    
    // Update progress bar
    const progressBar = document.querySelector('.progress');
    if (progressBar) {
        progressBar.style.width = `${progressPercent}%`;
        
        // Change color based on remaining time
        if (daysRemaining < 7) {
            progressBar.style.backgroundColor = '#e74c3c';
        } else if (daysRemaining < 14) {
            progressBar.style.backgroundColor = '#f39c12';
        }
    }
    
    // Remind button functionality
    const remindBtn = document.querySelector('.remind-btn');
    if (remindBtn) {
        remindBtn.addEventListener('click', function() {
            alert('We will remind you when enrollment opens for Semester 2, 2025!');
        });
    }
});
</script>



<section class="degrees-tabs" id="degrees-section">
    <h2 style="text-align: center;">Our IT Degrees and Other Programs</h2>
    <p style="text-align: center;">Explore our IT degree offerings and their curriculum</p>
    
 <div class="tabs-container">
        <div class="tabs">
            <button class="tab-button active" data-tab="bachelor">Bachelor of IT</button>
            <button class="tab-button" data-tab="master">Masters of IT</button>
            <button class="tab-button" data-tab="early">Bachelor of Early Childhood Education</button>
        </div>
        
     <div class="tab-content active" id="bachelor">
            <h3>Bachelor of Information Technology</h3>
            <h4 style="border-bottom: 1px solid #eee; color: grey;">CRICOS Course Code: 105686C</h3>
        <div class="units-grid">
             <div class="year">
                    <h1 style="color: orange; text-transform: uppercase;">Level 1</h1>
                    <table>
                        <thead>
                            <tr>
                                <th style="background-color: orange;"><h4 style="color: white;">Unit Code</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Semester 1</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Core/Elective</h4></th>
                             </tr>
                        </thead>
                    <tbody>
                        <td>ICT101</td>
                        <td>Introduction to Information Technology</td>
                        <td>Core</td>
                    </tbody>
                    <tbody> 
                        <td>ICT103</td>
                        <td>Programming</td>
                        <td>Core</td>
                    </tbody>
                    <tbody>    
                        <td>BUS101</td>
                        <td>Business Communication</td>
                        <td>Core</td>
                    </tbody>
                    <tbody>
                        <td>BUS102</td>
                        <td>Management Principles</td>
                        <td>Core</td>
                    </tbody>
                    </table>
                    <table>
                        <thead>
                            <tr>
                                <th  style="background-color: orange; color: white;">Unit Code</th>
                                <th  style="background-color: orange;"><h4 style="color: white;">Semester 2</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Core/Elective</h4></th>
                            </tr>
                        </thead>
                        <tbody>
                            <td>ICT102</td>
                            <td>Networking</td>
                            <td>Core</td>
                        </tbody>
                        <tbody>
                            <td>ICT201</td>
                            <td>Database Systems</td> 
                            <td>Core</td>
                        </tbody>
                        <tbody>
                            <td>ICT104</td>
                            <td>Fundamentals of Computability
                                <br> <i style="text-shadow: #ddd; color: gray;"> [Prerequisite ICT103 Programming]</i></td>
                                <td>Core</td>
                        </tbody>
                    </table>
                    <h1 style="color: orange; text-transform: uppercase;">Level 2</h1>
                    <table>
                        <thead>
                            <tr>
                                <th style="background-color: orange;"><h4 style="font-size: 15px; color: white;">Unit Code</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Semester 3</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Core/Elective</h4></th>
                             </tr>
                        </thead>
                        <td>ICT202</td>
                        <td>Cloud Computing <br>
                            <i style="text-shadow: #ddd; color: gray;"> [prerequisite: ICT102 Networking]</i></td>
                            <td>Core</td>
                    </tbody>
                    <tbody>
                        <td>ICT203</td>
                        <td>Web Application Development <br>
                            <i style="text-shadow: #ddd; color: gray;"> [prerequisite: ICT103 Programming; ICT201 Database Systems] </i></td> 
                            <td>Core</td>
                    </tbody>
                    <tbody>
                        <td>ICT206</td>
                        <td>Software Engineering
                            <br> <i style="text-shadow: #ddd; color: gray;"> [Prerequisite: ICT103 Programming]</i></td>
                            <td>Core</td>
                    </tbody>
                    <tbody>
                        <td>ICT208</td>
                        <td>Algorithms and Data Structures 
                            <br><i style="text-shadow: #ddd; color: gray;">[Prerequisite: ICT104 Fundamentals of Computability]</i></td>
                            <td>Core</td>
                    </tbody>
                    </table>
                    <table>
                        <thead>
                            <tr>
                                <th  style="background-color: orange; color: white;">Unit Code</th>
                                <th  style="background-color: orange;"><h4 style="color: white;">Semester 4</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Core/Elective</h4></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <td>ICT205</td>
                            <td>Mobile Application Development; <br>
                               <i style="text-shadow: #ddd; color: gray;"> [Prerequisite: ICT103 Programming, ICT201 Database Systems; Pre or co-requisite ICT203 Web Application Development]</i></td>
                               <td>Core</td>
                        </tbody>
                        <tbody>
                            <td>ICT204</td>
                            <td>Cyber Security <br>
                               <i style="text-shadow: #ddd; color: gray;"> [prerequisite: ICT102 Networking; ICT101 Introduction to Information Technology] </i></td> 
                               <td>Core</td>
                        </tbody>
                        <tbody>
                            <td>ICTXXX</td>
                            <td>ICT Eletive</td>
                            <td>Elective</td>
                        </tbody>
                        <tbody>
                            <td>ICT208</td>
                            <td>Algorithms and Data Structures <br>
                                <i style="text-shadow: #ddd; color: gray;"> [Prerequisite: ICT104 Fundamentals of Computability] </i></td>
                                <td>Core</td>
                        </tbody>
                    </table>
                <h1 style="color: orange; text-transform: uppercase;">Level 3</h1>
                <table>
                    <thead>
                        <tr>
                            <th style="background-color: orange;"><h4 style="font-size: 15px; color: white;">Unit Code</h4></th>
                            <th style="background-color: orange;"><h4 style="color: white;">Semester 5</h4></th>
                            <th style="background-color: orange;"><h4 style="color: white;">Core/Elective</h4></th>
                         </tr>
                    </thead>
                    <tbody>
                        <td>ICT313</td>
                        <td>Big Data for Software Development;
                            <br> <i style="text-shadow: #ddd; color: gray;">[Prerequisite: ICT103 Programming , ICT 201 Database Systems] </i></td>
                            <td>Core</td>
                    </tbody>
                    <tbody>
                        <td>ICT309</td>
                        <td>Project 1 (Analysis and Design); <br>
                            <i style="text-shadow: #ddd; color: gray;"> [NB Capstone sequence] [Prerequisite: ICT203 Web Application Development, ICT201 Database Systems, ICT206 Software Engineering] </i></td> 
                    </tbody>
                    <tbody>
                        <td>ICT305</td>
                        <td>Topics in IT [Prerequisite 160 credit points]</td>
                        <td>Core</td>
                    </tbody>
                    </table>

                <table>
                    <thead>
                        <tr>
                            <th  style="background-color: orange;  color: white;">Unit Code</th>
                            <th  style="background-color: orange;"><h4 style="color: white;">Semester 6</h4></th>
                            <th style="background-color: orange;"><h4 style="color: white;">Core/Elective</h4></th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <td>ICT308</td>
                        <td>Project 2 (Programming and Testing) <br>
                            <i style="text-shadow: #ddd; color: gray;"> [NB Capstone sequence] [Prerequisite: Project 1 (Analysis and Design)]</i></td>
                            <td>Core</td>
                    </tbody>
                    <tbody>
                        <td>ICT306</td>
                        <td>Advanced Cyber Security <br>
                            <i style="text-shadow: #ddd; color: gray;"> [Prerequisite: ICT204 Cyber Security] </i></td>
                            <td>Core</td> 
                    </tbody>
                    <tbody>
                        <td>ICTXXX</td>
                        <td>[ICT] Elective or BUS301 The Digital Economy or BUS306 Work Integrated Learning (the internship)</td>
                        <td>Elective</td>
                    </tbody>
                    
                </table>
            </div>
        </div>
     </div>

        <div class="tab-content" id="master">
            <h3 style="color: black;">Masters of Information Technology</h3>
            <h4 style="border-bottom: 1px solid #eee; color: grey;">CRICOS Course Code: 113602A</h4>
            <!-- Similar structure for master's content -->
            <div class="units-grid">
                <div class="year1">
                    <table>
                        <thead>
                            <tr>
                                <th style="background-color: orange;"><h4 style="font-size: 15px; color: white;">Credit Points</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Semester I</h4></th>
                             </tr>
                        </thead>
                        <td>10</td>
                        <td>ICT910 - Enterprise Systems Security <i style="text-shadow: #ddd; color: gray;">(Prerequisite: None)</i></td>
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT911 - Database Management Systems <i style="text-shadow: #ddd; color: gray;">(Prerequisite:  None)</i></td> 
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT912 - Programming  <i style="text-shadow: #ddd; color: gray;">(Prerequisite: None)</i></td>
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT913 - Networking  <i style="text-shadow: #ddd; color: gray;">(Prerequisite: None)</i></td>
                        </tbody>
                        <thead>
                            <tr>
                                <th  style="background-color: orange; color: white;">Credit Points</th>
                                <th  style="background-color: orange;"><h4 style="color: white;">Semester II</h4></th>
                            </tr>
                        </thead>
                        <tbody>
                            <td>10</td>
                            <td>ICT920 - Management Information Systems <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT910 Enterprise Systems Security)</i> </td>
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT921 - Applied Software Engineering <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT912 Programming)</i> </td> 
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT922 - Digital Transformation and Cloud Computing <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT913 Networking)</i></td>
                        </tbody>
                        <thead>
                            <tr>
                                <th  style="background-color: orange; color: white;">Credits Points</th>
                                <th  style="background-color: orange;"><h4 style="color: white;">Semester III (Core Units; then students elect a stream)</h4></th>
                            </tr>
                        </thead>
                        <td>10</td>
                        <td>ICT930 - Advanced Web Application Development<i style="text-shadow: #ddd; color: gray;"> (Prerequisites: ICT911 Database Management Systems, ICT912 Programming, ICT921 Applied Software Engineering)</i></td>
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT931 - Cybersecurity Incident Response <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT910 Enterprise Systems Security)</i></td> 
                        </tbody>
                        <tbody>
                            <td>10</td>
                            <td>ICT934 - Enterprise Systems Integration & Engineering   <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT920 Management Information Systems)</i></td>
                        </tbody>
                        <thead>
                            <tr>
                                <th style="background-color: orange;"><h4 style="color: white;">Semester III <p style="display: inline; font-size: 10px;">(CHOOSE ONE)</p></h4></th>
                                <th  style="background-color: orange;"></th>
                               <tbody>
                               <td style="border: none;"> <i><span style="display: inline; font-size: 10px; color: black;">Pick ICT943 for Software Development Stream, ICT942 for Cybersecurity Stream, or ICT944 if you want to study both streams.</span></i></td>
                               </tbody>
                             </tr>
                        </thead>
                        
                        <tbody>
                            <td style="background-color: red; text-align: center; color: white;">Cyber Security Stream</td>
                            <td>ICT944 - Cybersecurity and Software Development Integrative Project (10 credits)  <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT923 Information Technology Project Management, All first year units)</i></td>
                        </tbody>
                        <tbody>
                            <td style="background-color: blue; text-align: center; color: white;">Software Development Stream</td>
                            <td>ICT943 - Software Development Project (10 Credits) <i style="text-shadow: #ddd; color: gray;">(Prerequisite: ICT923 Information Technology Project Management, all first year units)</i> </td>
                        </tbody>
                        <thead>
                            <tr>
                                <th  style="background-color: orange; color: white;">SEMESTER IV CORE</th>
                                <th  style="background-color: orange;"></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <td >10</td>
                            <td>ICT945 - Professional Practice in Information Technology (10 Credits)</i> </td>
                        </tbody>
                       <tbody>
                        <td>10</td>
                        <td>ICT946 - Capstone Project <i style="text-shadow: #ddd; color: gray;">(Prerequisites: ICT945 Professional Practice in Information Technology, ICT942 Cybersecurity Project/ICT943 Software Development Project/ICT944 Cybersecurity and Software Development Integrative Project, ICT923 Information Technology Project Management, ICT934 Enterprise Systems Integration & Engineering, ICT922 Digital Transformation and Cloud Computing, ICT921 Applied Software Engineering, ICT920 Management Information System)</i> </td>
                       </tbody>
                    </table>
                </div>
            </div>
    </div>
        
        

        <div class="tab-content" id="early">
            <h3>Bachelor of Early Childhood Education
                 <small style="display: inline; border: 1px solid red; border-radius: 20px; font-weight: thin; color: white; font-size: 5px; padding: 10px 5px; cursor: text; background-color: red ;">New</small></h3>
                 <h4 style="border-bottom: 1px solid #eee; color: grey;">CRICOS Course Code: 116232J</h3>

            <!-- Similar structure for diploma content -->
            <div class="units-grid">
                <div class="year3">
                        <h1 style="color: orange; text-transform: uppercase;">Level 1 Semester 1</h1>
                        <table>
                            <thead>
                                <tr>
                                    <th style="background-color: orange;"><h4 style="color: white;">Unit Code</h4></th>
                                    <th style="background-color: orange;"><h4 style="color: white;">Subject Name</h4></th>
                                 </tr>
                            </thead>
                        <tbody>
                            <td>EC100</td>
                            <td>Learning and Development 1</td>
                        </tbody>
                        <tbody> 
                            <td>EC101</td>
                            <td>Play-based Pedagogies</td>
                        </tbody>
                        <tbody>    
                            <td>EC102</td>
                            <td>Language and Literacy 1</td>
                        </tbody>
                        <tbody>
                            <td>EC103</td>
                            <td>Global and Contemporary Perspectives in Early Childhood</td>
                        </tbody>
                     </table>

                     <table>
                        <h1 style="color: orange; text-transform: uppercase;">Level 1 Semester 2</h1>
                        <tbody>
                            <td>EC104</td>
                            <td>Social and Emotional Development and Wellbeing (Pre-Req: EC100)</td>
                        </tbody>
                        <tbody> 
                            <td>ECP001</td>
                            <td>Professional Experience 1 – Community Engagement (Pre-Req: EC100; Co-Req: EC106)</td>
                        </tbody>
                        <tbody>    
                            <td>EC105</td>
                            <td>Visual Arts in Early Childhood</td>
                        </tbody>
                        <tbody>
                            <td>EC106</td>
                            <td>Early Childhood Curriculum Planning and Evaluation 1 (Co-Req: ECP001)</td>
                        </tbody>
                        <thead>
                            <tr>
                                <th style="background-color: orange;"><h4 style="color: white;">Unit Code</h4></th>
                                <th style="background-color: orange;"><h4 style="color: white;">Subject Name</h4></th>
                             </tr>
                        </thead>
                   </table>     
                  
                   <table>
                    <h1 style="color: orange; text-transform: uppercase;">Level 2 Semester 3</h1>
                    <thead>
                        <tr>
                            <th style="background-color: orange;"><h4 style="color: white;">Unit Code</h4></th>
                            <th style="background-color: orange;"><h4 style="color: white;">Subject Name</h4></th>
                         </tr>

                         <tbody>
                            <td>EC200</td>
                            <td>Health</td>
                        </tbody>
                        <tbody>
                            <td>EC201</td>
                            <td>Designing Early Learning Environments</td>
                        </tbody>
                        <tbody>
                            <td>EC202</td>
                            <td>Numeracy and Mathematics 1</td>
                        </tbody>
                    </thead>
                    
                </table>
                </div>
            </div>
        </div>
                </div>
</section>
<section class="cta">
    <div class="journey">
        <h2>Ready to Start Your Journey?</h2>
        <p>Join our community of learners and innovators. Applications for the next semester are now open.</p>
        <div class="hero-buttons">
            <a href="subjectreg.php" class="btn btn-primary">Register Now</a>
            <a href="Support.php" class="btn btn-secondary">Contact Us</a>
        </div>
    </div>
</section>
<style>
    .cta {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), url('cta-bg.jpg') center/cover;
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 30px;
            font-size: 1.1rem;
        }
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            animation: fadeIn 1s ease 0.6s both;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: 1px solid #ccc;
            box-shadow: 0 4px 15px rgba(211, 75, 26, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            text-decoration: none;
            color: white;
            box-shadow: 0 6px 20px rgba(238, 26, 26, 0.799);
            background-color: #e05d00;
        }
        
        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-3px);
            text-decoration: none;
            color: white;
            box-shadow: 0 6px 20px rgba(255, 107, 0, 0.4);
            background-color: #e05d00;
        }
        
</style>

<script>
// JavaScript for tab functionality
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons and contents
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button
        button.classList.add('active');
        
        // Show corresponding content
        const tabId = button.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});
</script>
<footer>
        
        <div>
            &copy; Copyright 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved. Privacy Policy | Terms of Use
        </div>
    </footer>
</body>
</html>
