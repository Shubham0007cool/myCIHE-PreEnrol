-- Create database
CREATE DATABASE IF NOT EXISTS cihe_db;
USE cihe_db;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    program_id INT,
    contact_number VARCHAR(20),
    emergency_contact VARCHAR(100),
    reset_token VARCHAR(64),
    reset_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(64),
    reset_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create teachers table
CREATE TABLE IF NOT EXISTS teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create programs table
CREATE TABLE IF NOT EXISTS programs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    program_code VARCHAR(20) UNIQUE NOT NULL,
    program_name VARCHAR(100) NOT NULL,
    description TEXT,
    duration INT NOT NULL,
    program_type ENUM('undergraduate', 'postgraduate', 'diploma') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT NOT NULL,
    program_id INT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL
);

-- Create units table
CREATE TABLE IF NOT EXISTS units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_code VARCHAR(20) UNIQUE NOT NULL,
    unit_name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT NOT NULL,
    course_id INT,
    teacher_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
);

-- Create course_units table
CREATE TABLE IF NOT EXISTS course_units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT NOT NULL,
    unit_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
    UNIQUE KEY unique_program_unit (program_id, unit_id)
);

-- Create unit_prerequisites table
CREATE TABLE IF NOT EXISTS unit_prerequisites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_id INT NOT NULL,
    prerequisite_unit_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_unit_id) REFERENCES units(id) ON DELETE CASCADE
);

-- Create enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    unit_id INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255),
    `read` BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create chat_messages table
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id VARCHAR(50) NOT NULL,
    receiver_id VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    sender_name VARCHAR(255),
    sender_type ENUM('student', 'admin') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin account
INSERT INTO admins (email, password) VALUES 
('admin@admin.com', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK');

-- Insert sample students
INSERT INTO students (student_id, first_name, last_name, email, password, program_id, contact_number, emergency_contact) VALUES
('S001', 'John', 'Doe', 'john@student.cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 1, '1234567890', 'Jane Doe'),
('S002', 'Jane', 'Smith', 'jane@student.cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 1, '9876543210', 'John Smith');

-- Insert sample programs
INSERT INTO programs (program_code, program_name, description, duration, program_type) VALUES
('BICT', 'Bachelor of Information and Communication Technology', 'A comprehensive program covering IT and communication technologies', 3, 'undergraduate'),
('MIT', 'Master of Information Technology', 'Advanced IT program focusing on enterprise systems and security', 2, 'postgraduate'),
('BECE', 'Bachelor of Early Childhood Education', 'Comprehensive program in early childhood education', 3, 'undergraduate');

-- Insert sample teachers
INSERT INTO teachers (teacher_id, first_name, last_name, email, password, department) VALUES
('T001', 'Pema', 'Dolkar', 'pema.dolkar@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 'Business'),
('T002', 'Raju', 'Dhakal', 'raju.dhakal@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 'IT'),
('T003', 'Raju', 'Khan', 'raju.khan@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 'IT'),
('T004', 'Thinley', 'Ramsey', 'thinley.ramsey@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 'IT'),
('T005', 'Dr.', 'Ramu', 'ramu.tiwari@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 'IT'),
('T006', 'Dr.', 'Nagarjun', 'nagarjun@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', 'IT');

-- Insert sample courses
INSERT INTO courses (course_code, course_name, description, credits, program_id, image_path) VALUES
('BICT', 'Bachelor of Information Technology', 'Bachelor of Information Technology program', 144, 1, 'program.jpeg'),
('MIT', 'Master of Information Technology', 'Master of Information Technology program', 96, 2, 'degree.jpeg'),
('BECE', 'Bachelor of Early Childhood Education', 'Bachelor of Early Childhood Education program', 144, 3, 'sports1.png');

-- Insert sample units for Bachelor of IT and store their IDs
SET @ICT101_ID = 0;
SET @ICT103_ID = 0;
SET @BUS101_ID = 0;
SET @BUS102_ID = 0;
SET @BUS107_ID = 0;
SET @ICT104_ID = 0;
SET @ICT102_ID = 0;
SET @ICT201_ID = 0;
SET @ICT202_ID = 0;
SET @ICT203_ID = 0;
SET @ICT206_ID = 0;
SET @ICT208_ID = 0;
SET @ICT205_ID = 0;
SET @ICT204_ID = 0;
SET @ICT301_ID = 0;
SET @ICT313_ID = 0;
SET @ICT309_ID = 0;
SET @ICT307_ID = 0;
SET @ICT305_ID = 0;
SET @ICT306_ID = 0;
SET @ICT308_ID = 0;
SET @ICT310_ID = 0;
SET @BUS201_ID = 0;
SET @ICT207_ID = 0;
SET @BUS301_ID = 0;
SET @BUS307_ID = 0;
SET @ICT304_ID = 0;
SET @ICT302_ID = 0;
SET @ICT311_ID = 0;
SET @ICT312_ID = 0;

-- Insert units and store their IDs
INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT101', 'Introduction to Information Technology', 'Fundamentals of IT concepts and structures', 10, 1, 2);
SET @ICT101_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT103', 'Programming', 'Introduction to programming concepts and practices', 10, 1, 3);
SET @ICT103_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('BUS101', 'Business Communication', 'Effective communication in business context', 10, 1, 1);
SET @BUS101_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('BUS102', 'Management Principles', 'Core management concepts and practices', 10, 1, 1);
SET @BUS102_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('BUS107', 'Business Ethics in Digital Age', 'Ethical considerations in digital business', 10, 1, 1);
SET @BUS107_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT104', 'Fundamentals of Computability', 'Basic concepts of computation and algorithms', 10, 1, 3);
SET @ICT104_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT102', 'Networking', 'Introduction to computer networks', 10, 1, 4);
SET @ICT102_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT201', 'Database Systems', 'Fundamentals of database design and implementation', 10, 1, 6);
SET @ICT201_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT202', 'Cloud Computing', 'Cloud computing concepts and applications', 10, 1, 4);
SET @ICT202_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT203', 'Web Application Development', 'Modern web development practices', 10, 1, 3);
SET @ICT203_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT206', 'Software Engineering', 'Software development methodologies', 10, 1, 5);
SET @ICT206_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT208', 'Algorithms and Data Structures', 'Advanced algorithms and data structures', 10, 1, 3);
SET @ICT208_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT205', 'Mobile Application Development', 'Mobile app development for iOS and Android', 10, 1, 3);
SET @ICT205_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT204', 'Cyber Security', 'Fundamentals of cybersecurity', 10, 1, 4);
SET @ICT204_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT301', 'Information Technology Project Management', 'IT project management methodologies', 10, 1, 5);
SET @ICT301_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT313', 'Big Data for Software Development', 'Working with large datasets', 10, 1, 5);
SET @ICT313_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT309', 'Information Technology Governance, Risk and Compliance', 'IT governance and compliance', 10, 1, 5);
SET @ICT309_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT307', 'Project 1 (Analysis and Design)', 'First part of capstone project', 10, 1, 5);
SET @ICT307_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT305', 'Topics in IT', 'Advanced topics in information technology', 10, 1, 2);
SET @ICT305_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT306', 'Advanced Cyber Security', 'Advanced security concepts and practices', 10, 1, 4);
SET @ICT306_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT308', 'Project 2 (Programming and Testing)', 'Second part of capstone project', 10, 1, 5);
SET @ICT308_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT310', 'Information Technology Services Management', 'IT service management practices', 10, 1, 5);
SET @ICT310_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('BUS201', 'Organisational Behaviour', 'Study of human behavior in organizations', 10, 1, 1);
SET @BUS201_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT207', 'Knowledge Management', 'Managing organizational knowledge', 10, 1, 5);
SET @ICT207_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('BUS301', 'The Digital Economy', 'Digital transformation and business', 10, 1, 1);
SET @BUS301_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('BUS307', 'Work-Integrated Learning', 'Professional internship experience', 10, 1, 1);
SET @BUS307_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT304', 'Distributed Computing', 'Distributed systems and applications', 10, 1, 4);
SET @ICT304_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT302', 'Secure Software Development', 'Security-focused software development', 10, 1, 3);
SET @ICT302_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT311', 'Software Defined Networks', 'Modern network architecture', 10, 1, 4);
SET @ICT311_ID = LAST_INSERT_ID();

INSERT INTO units (unit_code, unit_name, description, credits, course_id, teacher_id) VALUES
('ICT312', 'Advanced Topics in Web Development', 'Advanced web technologies', 10, 1, 3);
SET @ICT312_ID = LAST_INSERT_ID();

-- Insert unit prerequisites using the stored IDs
INSERT INTO unit_prerequisites (unit_id, prerequisite_unit_id) VALUES
-- Level 1 prerequisites
(@ICT104_ID, @ICT103_ID),  -- ICT104 requires ICT103
(@ICT102_ID, @ICT101_ID),  -- ICT102 requires ICT101

-- Level 2 prerequisites
(@ICT202_ID, @ICT102_ID),  -- ICT202 requires ICT102
(@ICT203_ID, @ICT103_ID), -- ICT203 requires ICT103
(@ICT203_ID, @ICT201_ID), -- ICT203 requires ICT201
(@ICT206_ID, @ICT103_ID), -- ICT206 requires ICT103
(@ICT208_ID, @ICT104_ID), -- ICT208 requires ICT104
(@ICT205_ID, @ICT103_ID), -- ICT205 requires ICT103
(@ICT205_ID, @ICT201_ID), -- ICT205 requires ICT201
(@ICT204_ID, @ICT102_ID), -- ICT204 requires ICT102
(@ICT204_ID, @ICT101_ID), -- ICT204 requires ICT101
(@ICT301_ID, @BUS101_ID), -- ICT301 requires BUS101
(@ICT301_ID, @BUS102_ID), -- ICT301 requires BUS102
(@ICT301_ID, @ICT206_ID), -- ICT301 requires ICT206

-- Level 3 prerequisites
(@ICT313_ID, @ICT103_ID), -- ICT313 requires ICT103
(@ICT313_ID, @ICT201_ID), -- ICT313 requires ICT201
(@ICT307_ID, @ICT203_ID), -- ICT307 requires ICT203
(@ICT307_ID, @ICT201_ID), -- ICT307 requires ICT201
(@ICT307_ID, @ICT206_ID), -- ICT307 requires ICT206
(@ICT306_ID, @ICT204_ID), -- ICT306 requires ICT204
(@ICT308_ID, @ICT307_ID), -- ICT308 requires ICT307

-- Elective prerequisites
(@BUS201_ID, @BUS102_ID), -- BUS201 requires BUS102
(@ICT302_ID, @ICT103_ID), -- ICT302 requires ICT103
(@ICT302_ID, @ICT104_ID), -- ICT302 requires ICT104
(@ICT302_ID, @ICT208_ID), -- ICT302 requires ICT208
(@ICT311_ID, @ICT102_ID), -- ICT311 requires ICT102
(@ICT311_ID, @ICT103_ID), -- ICT311 requires ICT103
(@ICT312_ID, @ICT203_ID); -- ICT312 requires ICT203

-- Insert sample units for Master of IT
INSERT INTO units (unit_code, unit_name, description, credits, course_id) VALUES
('ICT910', 'Enterprise Systems Security', 'Advanced concepts in securing enterprise systems', 10, 2),
('ICT911', 'Database Management Systems', 'Advanced database concepts and administration', 10, 2),
('ICT920', 'Management Information Systems', 'Strategic use of information systems in organizations', 10, 2),
('ICT912', 'Programming', 'Advanced programming techniques and paradigms', 10, 2),
('ICT931', 'Cybersecurity Incident Response', 'Managing and responding to cybersecurity incidents', 10, 2),
('ICT921', 'Applied Software Engineering', 'Practical software engineering methodologies', 10, 2),
('ICT913', 'Networking', 'Advanced networking concepts and implementations', 10, 2);

-- Insert sample units for Bachelor of Early Childhood Education
INSERT INTO units (unit_code, unit_name, description, credits, course_id) VALUES
('EC100', 'Learning and Development 1', 'Fundamentals of child learning and development', 10, 3),
('EC103', 'Global and Contemporary Perspectives in Early Childhood', 'Contemporary issues in early childhood education', 10, 3),
('ECP001', 'Professional Experience 1 – Community Engagement', 'Practical community engagement experience', 10, 3),
('EC106', 'Early Childhood Curriculum Planning and Evaluation 1', 'Curriculum development for early childhood', 10, 3),
('EC201', 'Designing Early Learning Environments', 'Creating effective learning environments for children', 10, 3),
('EC202', 'Numeracy and Mathematics 1', 'Teaching numeracy and mathematics to young children', 10, 3),
('EC200', 'Health', 'Health and wellbeing in early childhood settings', 10, 3); 


