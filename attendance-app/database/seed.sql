USE attendance_system;

INSERT INTO users (role, full_name, email, password_hash, status) VALUES
('admin','System Admin','admin@demo.local','$2y$10$YjzKxWr0BEl9wRKqGZo4ceNZQ9R7h2xjv0WcfP3N6QYkP82LPgSg2','active'),
('instructor','Ajarn Somchai','somchai@demo.local','$2y$10$YjzKxWr0BEl9wRKqGZo4ceNZQ9R7h2xjv0WcfP3N6QYkP82LPgSg2','active'),
('student','Napat Student','napat@demo.local','$2y$10$YjzKxWr0BEl9wRKqGZo4ceNZQ9R7h2xjv0WcfP3N6QYkP82LPgSg2','active'),
('student','Mali Student','mali@demo.local','$2y$10$YjzKxWr0BEl9wRKqGZo4ceNZQ9R7h2xjv0WcfP3N6QYkP82LPgSg2','active');

INSERT INTO instructors(user_id,instructor_code) VALUES (2,'INS-001');
INSERT INTO students(user_id,student_id,class_group,profile_photo) VALUES
(3,'65010001','CS1/1','student_65010001.jpg'),
(4,'65010002','CS1/1','student_65010002.jpg');

INSERT INTO courses(course_code,course_name,section_name,instructor_user_id) VALUES
('CS101','Introduction to Programming','A',2),
('CS201','Database Systems','A',2);

INSERT INTO enrollments(student_user_id,course_id) VALUES
(3,1),(4,1),(3,2),(4,2);

INSERT INTO attendance_sessions(course_id,session_date,start_time,late_after,end_time,session_token,geo_lat,geo_lng,geo_radius_m,created_by_user_id)
VALUES
(1,CURDATE(),'08:50:00','09:10:00','09:30:00','DEMO1234',13.736717,100.523186,300,2),
(2,CURDATE(),'10:50:00','11:10:00','11:30:00','DEMO5678',NULL,NULL,NULL,2);

INSERT INTO system_settings(setting_key,setting_value) VALUES
('site_title','Classroom Attendance System'),
('allow_geolocation','1');
