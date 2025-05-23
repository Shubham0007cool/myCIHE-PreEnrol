<?php
require_once 'includes/student_header.php';
require_once 'includes/live_chat.php';
require_once 'includes/password_form.php';
?>

    <div class="contactus">
        <h1 class="getme">
        Contact us
        </h1>
    <hr class="holy">
     <div class="support">
        <div class="call">
            <div class="phone">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div class="number"> 
                <h1 class="num">Call us on</h1>
                
                <p class="numy">
                +61 435212167
            </p>
            <p class="hours" style="color: #718096; font-size: 14px; margin-top: 5px;">
                Mon-Fri: 9am-5pm AEST
            </p>
                </h1>
               </div>
        </div>
        <div class="email">
            <h1 class="Email">Email Us</h1>
            <ul>
                <li><i class="fa-solid fa-envelope"></i><b>Admission support:</b> admissions@cihe.edu.au</li>
                <li><i class="fa-solid fa-laptop"></i><b>IT support :</b> itsupport@cihe.edu.au</li>
                <li><i class="fa-solid fa-book"></i><b>Student Support:</b>students@cihe.edu.au</li>
            </ul>
        </div>
        </div>

        <style>
            .find
            {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            margin-bottom: 40px;
                
            }
            .region1, .region2, .region3 {
           border: 1px solid #ccc;
           margin-top: 10px;
           background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 30px;
            flex: 1;
            gap: 0;
            margin-left: 20px;
            margin-right: 20px;
            min-width: 200px;
        }
        .region1:hover, .region2:hover, .region3:hover{
            transform: translateY(-5px);
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .maps{
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        .maps iframe {
            width: 100%;
            height: 250px;
            border: none;
        }
        .canberra, .canberra2, .Sydney {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .location1, .location2, .location3, .subtext{
            color: var(--dark-gray);
            margin-bottom: 15px;
            display: block;
        }
                @media (max-width: 768px) {
            .location1, .location2, .location3 {
            display: block;    
            }
            .support {
                flex-direction: column;
                gap: 60px;
            }
            h1 {
                font-size: 1.2rem;
                justify-content: center;
                text-align: center;
            }

            .subtext {
                font-size: 0.8rem;
                justify-content: center;
                text-align: center;
            }
           .number{
            text-align: left;
            justify-content: center;
           }
            .call, .email {
                width: 85%;
                align-items: center;
                margin-right: 20px;
                margin-left: 20px;
                text-align: left;
                font-size: 10px;
            }
            .phone {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .num, .Email {
                font-size: 20px;
                margin-top: -20px;
            }
            
            .numy {
                font-size: 16px;
            }
            .maps iframe {
                height: 200px;
                display: block;
            }
        }

       
        </style>
       
       <h2 style="text-align: center; justify-content: center; margin-top: 20px; text-transform: uppercase; color: orange;">Our Locations..</h2>
             
             <div class="find">
                <div class="region1">
                    <h1 class="canberra">
                        Canberra
                    </h1>
                    <span class="subtext">Level 1, 5 Tenancy floor, Gungahlin ACT 2912</span>
                    <div class="maps">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3123.456789012345!2d149.12345678901234!3d-35.12345678901234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b17b1234567890%3A0x1234567890abcdef!2sGungahlin%20College!5e0!3m2!1sen!2sau!4v1610000000000" frameborder="3px">
                    </iframe>
                </div>  
                </div>
        
                <div class="region2">
                    <h1 class="canberra2">
                        Canberra
                    </h1>
                    <span class="subtext">Suite 1, Level 4/40 Cameron Ave, Belconnen ACT 2617</span>
                    <div class="maps">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3067.317014459064!2d149.06569667585793!3d-35.23680794983985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b164d905ffdb46b%3A0xdfca92a42d1cf112!2s40%20Cameron%20Ave%2C%20Belconnen%20ACT%202617%2C%20Australia!5e0!3m2!1sen!2snp!4v1682797321898!5m2!1sen!2snp" 
                    
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                 </iframe>
                </div>  
                </div>
        <div class="region3">
            <h1 class="Sydney">
                North Sydney
            </h1>
            <span class="subtext">16 Pacific Hwy, North Sydney NSW 2060</span>
            <div class="maps">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.95373531531882!3d-37.8162794420217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577c6f3e476a6a7!2sFederation%20Square!5e0!3m2!1sen!2sau!4v1617174472621!5m2!1sen!2sau" frameborder="3px">
                </iframe>
            </div>
        </div>
             </div>
              <h1 class="query">Still got a query?</h1>
             <section class="contactUs">
                <div class="formget">
                    <form action="" method="post" id="contactForm">
                        <label for="name">Name:</label>
                        <input type="text" name="name"  id="name">
                        <label for="phone">Phone Number:</label>
                        <input type="number" name="contact"  id="contact">
                        <label for="email">Email:</label>
                        <input type="email" name="gmail" id="gmail">
                        
                       <label for="thought">Query</label>
                        <textarea placeholder="Please describe your question (20-200 characters)" name="thoughts" id="think"  required class="textArea" >
                        </textarea>
                        <div class="bnt8">
                        <input type="submit" value="Send">   
                        </div>
                    </form>
                </div>
             </section>
        
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
