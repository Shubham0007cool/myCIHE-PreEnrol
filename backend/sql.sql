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
    -- FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL -- Add later if programs table is created first
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

-- Add foreign key to students now that programs table exists
ALTER TABLE students
ADD CONSTRAINT fk_student_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL;

-- Create departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    short_code VARCHAR(10) NOT NULL,
    program_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL
);

-- Create teachers table
CREATE TABLE IF NOT EXISTS teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department_id INT,
    reset_token VARCHAR(64),
    reset_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
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

-- Create semesters table
CREATE TABLE IF NOT EXISTS semesters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    level ENUM('Level 1', 'Level 2', 'Level 3', 'Electives', 'Postgraduate', 'Foundation') NOT NULL, -- Added more options for flexibility
    semester_number INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_semester (name, year, level, semester_number)
);

-- Create units table
CREATE TABLE IF NOT EXISTS units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_code VARCHAR(20) UNIQUE NOT NULL,
    unit_name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT NOT NULL,
    course_id INT,
    semester_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL
);

-- Create unit_teachers table for many-to-many relationship
CREATE TABLE IF NOT EXISTS unit_teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_id INT NOT NULL,
    teacher_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_unit_teacher (unit_id, teacher_id)
);

-- Create course_units table (effectively program_units)
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
    semester VARCHAR(20) NOT NULL, -- Consider removing this and deriving from unit.semester_id
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday') NOT NULL,
    time_slot ENUM('8:30am-11:30am', '12:00pm-3:00pm', '3:00pm-6:00pm', '6:00pm-9:00pm') NOT NULL,
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

-- Table for unit selections
CREATE TABLE IF NOT EXISTS unit_selections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    unit_id INT NOT NULL,
    selection_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE, -- Added ON DELETE CASCADE
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,     -- Added ON DELETE CASCADE
    UNIQUE KEY unique_selection (student_id, unit_id)
);

-- Insert default admin account
INSERT INTO admins (email, password) VALUES 
('admin@admin.com', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK');

-- Insert sample programs
INSERT INTO programs (program_code, program_name, description, duration, program_type) VALUES
('BICT', 'Bachelor of Information and Communication Technology', 'A comprehensive program covering IT and communication technologies', 3, 'undergraduate'),
('MIT', 'Master of Information Technology', 'Advanced IT program focusing on enterprise systems and security', 2, 'postgraduate'),
('BECE', 'Bachelor of Early Childhood Education', 'Comprehensive program in early childhood education', 3, 'undergraduate');

-- Insert departments based on existing data
INSERT INTO departments (name, short_code, program_id) VALUES
('Information Technology', 'IT', (SELECT id FROM programs WHERE program_code = 'BICT')),
('Information Technology (Masters)', 'MIT_DEPT', (SELECT id FROM programs WHERE program_code = 'MIT')), -- Changed short_code to be unique
('Early Childhood Education', 'ECE', (SELECT id FROM programs WHERE program_code = 'BECE'));

-- Insert sample teachers
INSERT INTO teachers (teacher_id, first_name, last_name, email, password, department_id) VALUES
('T001', 'Pema', 'Dolkar', 'pema.dolkar@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'IT')),
('T002', 'Raju', 'Dhakal', 'raju.dhakal@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'IT')),
('T003', 'Raju', 'Khan', 'raju.khan@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'IT')),
('T004', 'Thinley', 'Ramsey', 'thinley.ramsey@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'IT')),
('T005', 'Dr.', 'Ramu', 'ramu.tiwari@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'IT')),
('T006', 'Dr.', 'Nagarjun', 'nagarjun@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'IT')),
('T007', 'Alice', 'Wonder', 'alice.wonder@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'MIT_DEPT')), -- Teacher for MIT
('T008', 'Bob', 'Builder', 'bob.builder@cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM departments WHERE short_code = 'ECE'));    -- Teacher for ECE

-- Insert sample students
INSERT INTO students (student_id, first_name, last_name, email, password, program_id, contact_number, emergency_contact) VALUES
('S001', 'John', 'Doe', 'john@student.cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM programs WHERE program_code = 'BICT'), '1234567890', 'Jane Doe'),
('S002', 'Jane', 'Smith', 'jane@student.cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM programs WHERE program_code = 'BICT'), '9876543210', 'John Smith'),
('S003', 'Mike', 'Ross', 'mike@student.cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM programs WHERE program_code = 'MIT'), '1122334455', 'Rachel Zane'),
('S004', 'Ella', 'Fitz', 'ella@student.cihe.edu.au', '$2y$10$zCLhf6lH0cxCGOmMPA1naOzAvriEOL8VOH9EQtX64AxfNDQMnAZZK', (SELECT id FROM programs WHERE program_code = 'BECE'), '5566778899', 'Louis Armstrong');

-- Insert sample courses (these are more like specific offerings/majors within programs)
INSERT INTO courses (course_code, course_name, description, credits, program_id, image_path) VALUES
('BICT_Main', 'Bachelor of Information Technology', 'Bachelor of Information Technology program', 144, (SELECT id FROM programs WHERE program_code = 'BICT'), 'program.jpeg'),
('MIT_Main', 'Master of Information Technology', 'Master of Information Technology program', 96, (SELECT id FROM programs WHERE program_code = 'MIT'), 'degree.jpeg'),
('BECE_Main', 'Bachelor of Early Childhood Education', 'Bachelor of Early Childhood Education program', 144, (SELECT id FROM programs WHERE program_code = 'BECE'), 'sports1.png');

-- Store program IDs
SET @BICT_ProgramID = (SELECT id FROM programs WHERE program_code = 'BICT');
SET @MIT_ProgramID = (SELECT id FROM programs WHERE program_code = 'MIT');
SET @BECE_ProgramID = (SELECT id FROM programs WHERE program_code = 'BECE');

-- Store course IDs
SET @BICT_CourseID = (SELECT id FROM courses WHERE course_code = 'BICT_Main');
SET @MIT_CourseID = (SELECT id FROM courses WHERE course_code = 'MIT_Main');
SET @BECE_CourseID = (SELECT id FROM courses WHERE course_code = 'BECE_Main');

-- Store teacher IDs (example)
SET @TeacherPemaID = (SELECT id FROM teachers WHERE email = 'pema.dolkar@cihe.edu.au');
SET @TeacherRajuDID = (SELECT id FROM teachers WHERE email = 'raju.dhakal@cihe.edu.au');
SET @TeacherRajuKID = (SELECT id FROM teachers WHERE email = 'raju.khan@cihe.edu.au');
SET @TeacherThinleyID = (SELECT id FROM teachers WHERE email = 'thinley.ramsey@cihe.edu.au');
SET @TeacherRamuID = (SELECT id FROM teachers WHERE email = 'ramu.tiwari@cihe.edu.au');
SET @TeacherNagarjunID = (SELECT id FROM teachers WHERE email = 'nagarjun@cihe.edu.au');
SET @TeacherAliceID = (SELECT id FROM teachers WHERE email = 'alice.wonder@cihe.edu.au');
SET @TeacherBobID = (SELECT id FROM teachers WHERE email = 'bob.builder@cihe.edu.au');


-- =====================================================================================
-- MOVE SEMESTER INSERTIONS HERE - BEFORE UNITS
-- =====================================================================================
INSERT INTO semesters (name, year, level, semester_number, start_date, end_date) VALUES
-- Current Year
('Semester 1', YEAR(CURRENT_DATE), 'Level 1', 1, DATE_FORMAT(CURRENT_DATE, '%Y-02-01'), DATE_FORMAT(CURRENT_DATE, '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE), 'Level 1', 2, DATE_FORMAT(CURRENT_DATE, '%Y-07-01'), DATE_FORMAT(CURRENT_DATE, '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE), 'Level 2', 1, DATE_FORMAT(CURRENT_DATE, '%Y-02-01'), DATE_FORMAT(CURRENT_DATE, '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE), 'Level 2', 2, DATE_FORMAT(CURRENT_DATE, '%Y-07-01'), DATE_FORMAT(CURRENT_DATE, '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE), 'Level 3', 1, DATE_FORMAT(CURRENT_DATE, '%Y-02-01'), DATE_FORMAT(CURRENT_DATE, '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE), 'Level 3', 2, DATE_FORMAT(CURRENT_DATE, '%Y-07-01'), DATE_FORMAT(CURRENT_DATE, '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE), 'Electives', 1, DATE_FORMAT(CURRENT_DATE, '%Y-02-01'), DATE_FORMAT(CURRENT_DATE, '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE), 'Electives', 2, DATE_FORMAT(CURRENT_DATE, '%Y-07-01'), DATE_FORMAT(CURRENT_DATE, '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE), 'Postgraduate', 1, DATE_FORMAT(CURRENT_DATE, '%Y-02-01'), DATE_FORMAT(CURRENT_DATE, '%Y-06-30')), -- For MIT
('Semester 2', YEAR(CURRENT_DATE), 'Postgraduate', 2, DATE_FORMAT(CURRENT_DATE, '%Y-07-01'), DATE_FORMAT(CURRENT_DATE, '%Y-11-30')), -- For MIT
('Semester 1', YEAR(CURRENT_DATE), 'Foundation', 1, DATE_FORMAT(CURRENT_DATE, '%Y-02-01'), DATE_FORMAT(CURRENT_DATE, '%Y-06-30')), -- Example if needed

-- Next Year
('Semester 1', YEAR(CURRENT_DATE) + 1, 'Level 1', 1, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-02-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE) + 1, 'Level 1', 2, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-07-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE) + 1, 'Level 2', 1, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-02-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE) + 1, 'Level 2', 2, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-07-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE) + 1, 'Level 3', 1, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-02-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE) + 1, 'Level 3', 2, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-07-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE) + 1, 'Electives', 1, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-02-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE) + 1, 'Electives', 2, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-07-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-11-30')),
('Semester 1', YEAR(CURRENT_DATE) + 1, 'Postgraduate', 1, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-02-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-06-30')),
('Semester 2', YEAR(CURRENT_DATE) + 1, 'Postgraduate', 2, DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-07-01'), DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), '%Y-11-30'));

-- Get current year semester IDs (examples)
SET @L1S1_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Level 1' AND semester_number = 1 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @L1S2_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Level 1' AND semester_number = 2 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @L2S1_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Level 2' AND semester_number = 1 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @L2S2_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Level 2' AND semester_number = 2 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @L3S1_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Level 3' AND semester_number = 1 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @L3S2_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Level 3' AND semester_number = 2 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @EleS1_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Electives' AND semester_number = 1 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @EleS2_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Electives' AND semester_number = 2 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @PGS1_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Postgraduate' AND semester_number = 1 AND year = YEAR(CURRENT_DATE) LIMIT 1);
SET @PGS2_CurrentYear_SemID = (SELECT id FROM semesters WHERE level = 'Postgraduate' AND semester_number = 2 AND year = YEAR(CURRENT_DATE) LIMIT 1);


-- Insert units and store their IDs
-- Level 1 Semester 1 Units (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT101', 'Introduction to Information Technology', 'Fundamentals of IT concepts and structures', 10, @BICT_CourseID, @L1S1_CurrentYear_SemID);
SET @ICT101_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT101_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT103', 'Programming', 'Introduction to programming concepts and practices', 10, @BICT_CourseID, @L1S1_CurrentYear_SemID);
SET @ICT103_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT103_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('BUS101', 'Business Communication', 'Effective communication in business context', 10, @BICT_CourseID, @L1S1_CurrentYear_SemID);
SET @BUS101_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @BUS101_ID);

-- Level 1 Semester 2 Units (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('BUS102', 'Management Principles', 'Core management concepts and practices', 10, @BICT_CourseID, @L1S2_CurrentYear_SemID);
SET @BUS102_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @BUS102_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('BUS107', 'Business Ethics in Digital Age', 'Ethical considerations in digital business', 10, @BICT_CourseID, @L1S2_CurrentYear_SemID);
SET @BUS107_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @BUS107_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT104', 'Fundamentals of Computability', 'Basic concepts of computation and algorithms', 10, @BICT_CourseID, @L1S2_CurrentYear_SemID);
SET @ICT104_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT104_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT102', 'Networking', 'Introduction to computer networks', 10, @BICT_CourseID, @L1S2_CurrentYear_SemID);
SET @ICT102_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT102_ID);

-- Level 2 Semester 1 Units (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT201', 'Database Systems', 'Fundamentals of database design and implementation', 10, @BICT_CourseID, @L2S1_CurrentYear_SemID);
SET @ICT201_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT201_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT202', 'Cloud Computing', 'Cloud computing concepts and applications', 10, @BICT_CourseID, @L2S1_CurrentYear_SemID);
SET @ICT202_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT202_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT203', 'Web Application Development', 'Modern web development practices', 10, @BICT_CourseID, @L2S1_CurrentYear_SemID);
SET @ICT203_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT203_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT206', 'Software Engineering', 'Software development methodologies', 10, @BICT_CourseID, @L2S1_CurrentYear_SemID);
SET @ICT206_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT206_ID);

-- Level 2 Semester 2 Units (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT208', 'Algorithms and Data Structures', 'Advanced algorithms and data structures', 10, @BICT_CourseID, @L2S2_CurrentYear_SemID);
SET @ICT208_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT208_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT205', 'Mobile Application Development', 'Mobile app development for iOS and Android', 10, @BICT_CourseID, @L2S2_CurrentYear_SemID);
SET @ICT205_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT205_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT204', 'Cyber Security', 'Fundamentals of cybersecurity', 10, @BICT_CourseID, @L2S2_CurrentYear_SemID);
SET @ICT204_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT204_ID);

-- Level 3 Semester 1 Units (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT301', 'Information Technology Project Management', 'IT project management methodologies', 10, @BICT_CourseID, @L3S1_CurrentYear_SemID);
SET @ICT301_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT301_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT313', 'Big Data for Software Development', 'Working with large datasets', 10, @BICT_CourseID, @L3S1_CurrentYear_SemID);
SET @ICT313_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT313_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT309', 'Information Technology Governance, Risk and Compliance', 'IT governance and compliance', 10, @BICT_CourseID, @L3S1_CurrentYear_SemID);
SET @ICT309_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT309_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT307', 'Project 1 (Analysis and Design)', 'First part of capstone project', 10, @BICT_CourseID, @L3S1_CurrentYear_SemID);
SET @ICT307_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT307_ID);

-- Level 3 Semester 2 Units (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT305', 'Topics in IT', 'Advanced topics in information technology', 10, @BICT_CourseID, @L3S2_CurrentYear_SemID);
SET @ICT305_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT305_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT306', 'Advanced Cyber Security', 'Advanced security concepts and practices', 10, @BICT_CourseID, @L3S2_CurrentYear_SemID);
SET @ICT306_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT306_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT308', 'Project 2 (Programming and Testing)', 'Second part of capstone project', 10, @BICT_CourseID, @L3S2_CurrentYear_SemID);
SET @ICT308_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT308_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT310', 'Information Technology Services Management', 'IT service management practices', 10, @BICT_CourseID, @L3S2_CurrentYear_SemID);
SET @ICT310_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT310_ID);

-- Elective Units Semester 1 (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('BUS201', 'Organisational Behaviour', 'Study of human behavior in organizations', 10, @BICT_CourseID, @EleS1_CurrentYear_SemID);
SET @BUS201_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @BUS201_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT207', 'Knowledge Management', 'Managing organizational knowledge', 10, @BICT_CourseID, @EleS1_CurrentYear_SemID);
SET @ICT207_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT207_ID);

-- Elective Units Semester 2 (BICT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('BUS301', 'The Digital Economy', 'Digital transformation and business', 10, @BICT_CourseID, @EleS2_CurrentYear_SemID);
SET @BUS301_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @BUS301_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('BUS307', 'Work-Integrated Learning', 'Professional internship experience', 10, @BICT_CourseID, @EleS2_CurrentYear_SemID);
SET @BUS307_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @BUS307_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT304', 'Distributed Computing', 'Distributed systems and applications', 10, @BICT_CourseID, @EleS2_CurrentYear_SemID);
SET @ICT304_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT304_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT302', 'Secure Software Development', 'Security-focused software development', 10, @BICT_CourseID, @EleS2_CurrentYear_SemID);
SET @ICT302_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT302_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT311', 'Software Defined Networks', 'Modern network architecture', 10, @BICT_CourseID, @EleS2_CurrentYear_SemID);
SET @ICT311_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT311_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT312', 'Advanced Topics in Web Development', 'Advanced web technologies', 10, @BICT_CourseID, @EleS2_CurrentYear_SemID);
SET @ICT312_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BICT_ProgramID, @ICT312_ID);

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


-- Insert sample units for Master of IT (MIT)
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT910', 'Enterprise Systems Security', 'Advanced concepts in securing enterprise systems', 10, @MIT_CourseID, @PGS1_CurrentYear_SemID);
SET @ICT910_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT910_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT911', 'Database Management Systems', 'Advanced database concepts and administration', 10, @MIT_CourseID, @PGS1_CurrentYear_SemID);
SET @ICT911_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT911_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT920', 'Management Information Systems', 'Strategic use of information systems in organizations', 10, @MIT_CourseID, @PGS1_CurrentYear_SemID);
SET @ICT920_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT920_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT912', 'Programming', 'Advanced programming techniques and paradigms', 10, @MIT_CourseID, @PGS2_CurrentYear_SemID);
SET @ICT912_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT912_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT931', 'Cybersecurity Incident Response', 'Managing and responding to cybersecurity incidents', 10, @MIT_CourseID, @PGS2_CurrentYear_SemID);
SET @ICT931_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT931_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT921', 'Applied Software Engineering', 'Practical software engineering methodologies', 10, @MIT_CourseID, @PGS2_CurrentYear_SemID);
SET @ICT921_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT921_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ICT913', 'Networking', 'Advanced networking concepts and implementations', 10, @MIT_CourseID, @PGS2_CurrentYear_SemID);
SET @ICT913_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@MIT_ProgramID, @ICT913_ID);


-- Insert sample units for Bachelor of Early Childhood Education (BECE)
-- Assuming BECE units follow Level 1, Semester 1/2 structure for simplicity
INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('EC100', 'Learning and Development 1', 'Fundamentals of child learning and development', 10, @BECE_CourseID, @L1S1_CurrentYear_SemID);
SET @EC100_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @EC100_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('EC103', 'Global and Contemporary Perspectives in Early Childhood', 'Contemporary issues in early childhood education', 10, @BECE_CourseID, @L1S1_CurrentYear_SemID);
SET @EC103_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @EC103_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('ECP001', 'Professional Experience 1 – Community Engagement', 'Practical community engagement experience', 10, @BECE_CourseID, @L1S1_CurrentYear_SemID);
SET @ECP001_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @ECP001_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('EC106', 'Early Childhood Curriculum Planning and Evaluation 1', 'Curriculum development for early childhood', 10, @BECE_CourseID, @L1S2_CurrentYear_SemID);
SET @EC106_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @EC106_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('EC201', 'Designing Early Learning Environments', 'Creating effective learning environments for children', 10, @BECE_CourseID, @L1S2_CurrentYear_SemID);
SET @EC201_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @EC201_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('EC202', 'Numeracy and Mathematics 1', 'Teaching numeracy and mathematics to young children', 10, @BECE_CourseID, @L1S2_CurrentYear_SemID);
SET @EC202_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @EC202_ID);

INSERT INTO units (unit_code, unit_name, description, credits, course_id, semester_id) VALUES
('EC200', 'Health', 'Health and wellbeing in early childhood settings', 10, @BECE_CourseID, @L1S2_CurrentYear_SemID);
SET @EC200_ID = LAST_INSERT_ID();
INSERT INTO course_units (program_id, unit_id) VALUES (@BECE_ProgramID, @EC200_ID);


-- Assign teachers to units (multiple teachers per unit)
INSERT INTO unit_teachers (unit_id, teacher_id) VALUES
-- ICT101: Multiple teachers
(@ICT101_ID, @TeacherRajuDID),
(@ICT101_ID, @TeacherRajuKID),

-- ICT103: Multiple teachers
(@ICT103_ID, @TeacherRajuKID),
(@ICT103_ID, @TeacherRamuID),

-- BUS101: Single teacher
(@BUS101_ID, @TeacherPemaID),

-- BUS102: Multiple teachers
(@BUS102_ID, @TeacherPemaID),
(@BUS102_ID, @TeacherRajuDID),

-- ICT104: Multiple teachers
(@ICT104_ID, @TeacherRajuKID),
(@ICT104_ID, @TeacherNagarjunID),

-- ICT102: Single teacher
(@ICT102_ID, @TeacherThinleyID),

-- ICT201: Multiple teachers
(@ICT201_ID, @TeacherNagarjunID),
(@ICT201_ID, @TeacherRajuKID),

-- ICT202: Single teacher
(@ICT202_ID, @TeacherThinleyID),

-- ICT203: Multiple teachers
(@ICT203_ID, @TeacherRajuKID),
(@ICT203_ID, @TeacherRamuID),

-- ICT206: Single teacher
(@ICT206_ID, @TeacherRamuID),

-- ICT208: Multiple teachers
(@ICT208_ID, @TeacherRajuKID),
(@ICT208_ID, @TeacherNagarjunID),

-- ICT205: Single teacher
(@ICT205_ID, @TeacherRajuKID),

-- ICT204: Multiple teachers
(@ICT204_ID, @TeacherThinleyID),
(@ICT204_ID, @TeacherRajuDID),

-- ICT301: Single teacher
(@ICT301_ID, @TeacherRamuID),

-- ICT313: Multiple teachers
(@ICT313_ID, @TeacherRamuID),
(@ICT313_ID, @TeacherNagarjunID),

-- ICT307: Single teacher
(@ICT307_ID, @TeacherRamuID),

-- ICT305: Multiple teachers
(@ICT305_ID, @TeacherRajuDID),
(@ICT305_ID, @TeacherRajuKID),

-- ICT306: Single teacher
(@ICT306_ID, @TeacherThinleyID),

-- ICT308: Multiple teachers
(@ICT308_ID, @TeacherRamuID),
(@ICT308_ID, @TeacherRajuKID),

-- ICT310: Single teacher
(@ICT310_ID, @TeacherRamuID),

-- BUS201: Multiple teachers
(@BUS201_ID, @TeacherPemaID),
(@BUS201_ID, @TeacherRajuDID),

-- ICT207: Single teacher
(@ICT207_ID, @TeacherRamuID),

-- BUS301: Multiple teachers
(@BUS301_ID, @TeacherPemaID),
(@BUS301_ID, @TeacherRajuDID),

-- BUS307: Single teacher
(@BUS307_ID, @TeacherPemaID),

-- ICT304: Multiple teachers
(@ICT304_ID, @TeacherThinleyID),
(@ICT304_ID, @TeacherRajuKID),

-- ICT302: Single teacher
(@ICT302_ID, @TeacherRajuKID),

-- ICT311: Multiple teachers
(@ICT311_ID, @TeacherThinleyID),
(@ICT311_ID, @TeacherRajuDID),

-- ICT312: Single teacher
(@ICT312_ID, @TeacherRajuKID),

-- MIT Units
-- ICT910: Multiple teachers
(@ICT910_ID, @TeacherAliceID),
(@ICT910_ID, @TeacherThinleyID),

-- ICT911: Single teacher
(@ICT911_ID, @TeacherAliceID),

-- ICT920: Multiple teachers
(@ICT920_ID, @TeacherAliceID),
(@ICT920_ID, @TeacherRamuID),

-- ICT912: Single teacher
(@ICT912_ID, @TeacherAliceID),

-- ICT931: Multiple teachers
(@ICT931_ID, @TeacherAliceID),
(@ICT931_ID, @TeacherThinleyID),

-- ICT921: Single teacher
(@ICT921_ID, @TeacherAliceID),

-- ICT913: Multiple teachers
(@ICT913_ID, @TeacherAliceID),
(@ICT913_ID, @TeacherRajuKID),

-- BECE Units
-- EC100: Multiple teachers
(@EC100_ID, @TeacherBobID),
(@EC100_ID, @TeacherPemaID),

-- EC103: Single teacher
(@EC103_ID, @TeacherBobID),

-- ECP001: Multiple teachers
(@ECP001_ID, @TeacherBobID),
(@ECP001_ID, @TeacherRajuDID),

-- EC106: Single teacher
(@EC106_ID, @TeacherBobID),

-- EC201: Multiple teachers
(@EC201_ID, @TeacherBobID),
(@EC201_ID, @TeacherPemaID),

-- EC202: Single teacher
(@EC202_ID, @TeacherBobID),

-- EC200: Multiple teachers
(@EC200_ID, @TeacherBobID),
(@EC200_ID, @TeacherRajuDID);


SELECT 'Database setup and initial data insertion complete.' AS status;

-- Add teacher_id column to enrollments table
ALTER TABLE enrollments ADD COLUMN teacher_id INT NULL AFTER unit_id;
