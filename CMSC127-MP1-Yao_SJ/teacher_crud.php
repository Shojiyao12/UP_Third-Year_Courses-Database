<?php
	//DATABASE PARAMETERS
	$user = 'root';
	$password = 'YAOshawjie122002@';
	$dsn = "mysql:host=localhost:3307;dbname=thirdyear_courses";

	try {
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $ex) {
		echo "Connection failed: " . $ex->getMessage();
	}
	
	$studentInfo = null;
	$teacherInfo = null;
	$subjects = [];

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Manage Teacher Form - SEARCH FOR TEACHER
		if (isset($_POST['search_teacher'])) {
			try {
				$searchQuery = $_POST['search_query'];

				$query = $pdo->prepare("SELECT teachers.Teacher_ID, teachers.Name AS TeacherName, teachers.Gender AS TeacherGender, 
										teachers.Email_Address AS TeacherEmailAddress
										FROM teachers
										LEFT JOIN teaches ON teachers.Teacher_ID = teaches.Teacher_ID
										WHERE teachers.Name LIKE :searchQuery AND teachers.Teacher_ID > 0
										GROUP BY teachers.Teacher_ID");

				$query->bindParam(':searchQuery', $searchQuery, PDO::PARAM_STR);

				$query->execute();

				$result = $query->fetchAll(PDO::FETCH_ASSOC);

				if (!empty($result)) {
					$teacherInfo = $result[0];
					$subjects = array_column($result, 'Name_of_Subject');
				}
			} catch (PDOException $ex) {
				$alertMessage = "&emsp;&emsp;Query failed: " . $ex->getMessage();
			}
			finally {
				// Close the cursor
				if ($query) {
					$query->closeCursor();
				}
			}
		}
		
		
		// Manage Teacher Form - ADD TEACHER
		if (isset($_POST['add_teacher'])) {
			if (!isset($_POST['search_teacher'])) {
				$teacherName = isset($_POST['teacher_name']) ? trim($_POST['teacher_name']) : '';
				$teacherGender = isset($_POST['teacher_gender']) ? trim($_POST['teacher_gender']) : '';
				$teacherEmail = isset($_POST['teacher_email']) ? trim($_POST['teacher_email']) : '';
				$teacherPassword = isset($_POST['teacher_password']) ? trim($_POST['teacher_password']) : '';

				try {
					// Get the current maximum Teacher_ID
					$maxTeacherIdQuery = $pdo->query("SELECT MAX(Teacher_ID) AS max_id FROM teachers");
					$maxTeacherId = $maxTeacherIdQuery->fetchColumn();
					$newTeacherId = $maxTeacherId + 1;

					$insertQuery = $pdo->prepare("INSERT INTO teachers (Teacher_ID, Name, Gender, Email_Address, Password) VALUES (?, ?, ?, ?, ?)");
					$insertQuery->execute([$newTeacherId, $teacherName, $teacherGender, $teacherEmail, $teacherPassword]);

					$alertMessage = "&emsp;&emsp;Teacher added successfully!";
				} catch (PDOException $ex) {
					$alertMessage = "&emsp;&emsp;Failed to add teacher. Error: " . $ex->getMessage();
				}
			}
		}

		// Manage Teacher Form - DELETE TEACHER (based on Email Address)
		if (isset($_POST['delete_teacher'])) {
			if (!isset($_POST['search_teacher'])) {
				try {
					$emailToDelete = isset($_POST['teacher_email']) ? trim($_POST['teacher_email']) : '';

					// Get Teacher_ID for the given email
					$getTeacherIdQuery = $pdo->prepare("SELECT Teacher_ID FROM teachers WHERE Email_Address = ?");
					$getTeacherIdQuery->execute([$emailToDelete]);
					$teacherId = $getTeacherIdQuery->fetchColumn();
					
					$getCourseQuery = $pdo->prepare("SELECT Subject_ID FROM teaches WHERE Teacher_ID = ?");
					$getCourseQuery->execute([$teacherId]);


					$deleteTeachesQuery = $pdo->prepare("DELETE FROM teaches WHERE Teacher_ID = ?");
					$deleteTeachesQuery->execute([$teacherId]);

					// Check if there are results
					if ($getCourseQuery->rowCount() > 0) {
					// Fetch all Subject_IDs
					$subjectIds = $getCourseQuery->fetchAll(PDO::FETCH_COLUMN);

					// Step 2: Update coursetaken table
					$updateCourseQuery = $pdo->prepare("UPDATE coursetaken SET Subject_ID = 0 WHERE Subject_ID IN (" . implode(',', $subjectIds) . ")");
					$updateCourseQuery->execute();

					$updateSchedQuery = $pdo->prepare("DELETE from subjects_schedule WHERE Subject_ID IN (" . implode(',', $subjectIds) . ")");
					$updateSchedQuery->execute();
						
					$DeleteSchedQuery = $pdo->prepare("DELETE from subjects WHERE Subject_ID IN (" . implode(',', $subjectIds) . ")");
					$DeleteSchedQuery->execute();

	
					// Check for errors in the update query
					if ($updateCourseQuery->errorInfo()[0] != "00000") {
						// Handle the error, e.g., log or display an error message
						echo "Error updating records: " . $updateCourseQuery->errorInfo()[2];
					}
					} else {
					echo "No records found for the given teacher ID in the teaches table.";
					}
					
					// Delete records from teaches table for the specified Teacher_ID
					// Delete the teacher from the teachers table
					$deleteTeacherQuery = $pdo->prepare("DELETE FROM teachers WHERE Email_Address = ?");
					$deleteTeacherQuery->execute([$emailToDelete]);
					
					
					
					

					$alertMessage = "&emsp;&emsp;Teacher and associated records deleted successfully!";
				} catch (PDOException $ex) {
					$alertMessage = "&emsp;&emsp;Failed to delete teacher. Error: " . $ex->getMessage();
				}
			}
		}

		// Manage Teacher Form - UPDATE TEACHER (based on Email Address)
		if (isset($_POST['edit_teacher'])) {
			if (!isset($_POST['search_teacher'])) {
				try {
					$sql = "UPDATE teachers SET  
								Name=:name,  
								Gender=:gender, 
								Email_Address=:email,
								Password=:password
								WHERE Email_Address = :email";

					$stmt = $pdo->prepare($sql);

					$stmt->bindParam(':name', $_POST['teacher_name']);
					$stmt->bindParam(':gender', $_POST['teacher_gender']);
					$stmt->bindParam(':email', $_POST['teacher_email']);
					$stmt->bindParam(':password', $_POST['teacher_password']);

					$stmt->execute();
					$alertMessage = "&emsp;&emsp;Teacher updated successfully!";
				} catch (PDOException $ex) {
					$alertMessage = "&emsp;&emsp;Failed to update teacher. Error: " . $ex->getMessage();
				}
			}
		}
		
		// Manage Subject Form - SEARCH FOR SUBJECT
		if (isset($_POST['search_subject'])) {
			try {
				$searchQuery = isset($_POST['search_query']) ? trim($_POST['search_query']) : '';
				$searchQuery = '%' . $searchQuery . '%';

				$query = $pdo->prepare("
					SELECT subjects.*
					FROM subjects
					WHERE LOWER(subjects.Name_of_Subject) LIKE LOWER(:searchQuery)
				");

				$query->bindParam(':searchQuery', $searchQuery, PDO::PARAM_STR);

				if ($query->execute()) {
					$resultSubj = $query->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$alertMessage = "&emsp;&emsp;Query execution failed.";
					echo $query->errorInfo()[2]; // Display the error message
				}
			} catch (PDOException $ex) {
				$alertMessage = "&emsp;&emsp;Query failed: " . $ex->getMessage();
			}
			finally {
				// Close the cursor
				if ($query) {
					$query->closeCursor();
				}
			}
		}
		
		// Manage Subject Form - ADD Subject
		if (isset($_POST['add_subject'])) {
			try {
				$subjectName = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
				$classType = isset($_POST['class_type']) ? trim($_POST['class_type']) : '';
				$scheduleID = isset($_POST['schedule_id']) ? trim($_POST['schedule_id']) : '';
				$teacherEmail = isset($_POST['teacher_email']) ? trim($_POST['teacher_email']) : '';
				
				$maxSubjectIdQuery = $pdo->query("SELECT MAX(Subject_ID) AS max_id FROM subjects");
			    $maxSubjectId = $maxSubjectIdQuery->fetchColumn();
				// Generate a new Subject_ID
				$newSubjectId = $maxSubjectId + 1;
				
				$maxScheduleIdQuery = $pdo->query("SELECT MAX(ID) AS max_id FROM subjects_schedule");
				$maxScheduleId = $maxScheduleIdQuery->fetchColumn();
				$newScheduleId = $maxScheduleId + 1;

				// Check if the subject already exists
				$checkSubjectQuery = $pdo->prepare("SELECT COUNT(*)
													FROM subjects
													WHERE Name_of_Subject = ? AND Type_of_Class = ? AND Subject_ID IN (
														SELECT Subject_ID
														FROM subjects_schedule
														WHERE Schedule_ID = ? 
													)");
				$checkSubjectQuery->execute([$subjectName, $classType, $scheduleID]);
				$subjectExists = $checkSubjectQuery->fetchColumn();
	
				if ($subjectExists) {
					// Condition to check when Subject with the same details already exists
					$alertMessage = "&emsp;&emsp;Failed to add subject. Subject with the same details already exists.";
					// Get the maximum existing Subject_ID
					
				} else {
					$insertQuery = $pdo->prepare("INSERT INTO subjects (Subject_ID, Name_of_Subject, Type_of_Class) VALUES (?, ?, ?)");
					$insertQuery->execute([$newSubjectId, $subjectName, $classType]);

					// Insert into subjects_schedule table
					$insertSubjectScheduleQuery = $pdo->prepare("INSERT INTO subjects_schedule (ID, Subject_ID, Schedule_ID) VALUES (?, ?, ?)");
					$insertSubjectScheduleQuery->execute([$newScheduleId, $newSubjectId, $scheduleID]);

					$alertMessage = "&emsp;&emsp;Subject added successfully!";
				
					$checkTeacherQuery = $pdo->prepare("SELECT Teacher_ID FROM teachers WHERE Email_Address = ?");
					$checkTeacherQuery->execute([$teacherEmail]);
					$teacherInfo = $checkTeacherQuery->fetch(PDO::FETCH_ASSOC);

					if ($teacherInfo) {
						$teacherID = $teacherInfo['Teacher_ID'];
						
						$checkTeachesQuery = $pdo->prepare("SELECT COUNT(*) FROM teaches
															WHERE Teacher_ID = ? AND Subject_ID IN (
																SELECT subjects.Subject_ID
																FROM subjects
																JOIN subjects_schedule ON subjects.Subject_ID = subjects_schedule.Subject_ID
																WHERE subjects_schedule.Schedule_ID = ? 
																	AND subjects.Name_of_Subject = ? 
																	AND subjects.Type_of_Class = ?
															)");		
						
						$checkTeachesQuery->execute([$teacherID, $scheduleID, $subjectName, $classType]);
						$teachesExists = $checkTeachesQuery->fetchColumn();
						
						if (!$teachesExists) {
							$maxteachesIdQuery = $pdo->query("SELECT MAX(ID) AS max_id FROM teaches");
							$maxteachesId = $maxteachesIdQuery->fetchColumn();
							$newteachesId = $maxteachesId + 1;

							$insertQuery = $pdo->prepare("INSERT INTO teaches (ID, Teacher_ID, Subject_ID) VALUES (?, ?, ?)");
							$insertQuery->execute([$newteachesId , $teacherID, $newSubjectId]);

							$alertMessage = "&emsp;&emsp;Subject added for the teacher successfully!";
						} else {
							$alertMessage = "&emsp;&emsp;Subject already exists for the teacher.";
						}
					}else {
						$alertMessage = "&emsp;&emsp;Teacher not found with the provided email address.";
					}
				}
				
				} catch (PDOException $ex) {
					$alertMessage = "&emsp;&emsp;Failed to add subject. Error: " . $ex->getMessage();
				}
		}
		
		// Manage Subject Form - UPDATE SUBJECT (based on Subject_ID)
		if (isset($_POST['edit_subject'])) {
			try {
				
				$subjectName = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
				$classType = isset($_POST['class_type']) ? trim($_POST['class_type']) : '';
				$scheduleID = isset($_POST['schedule_id']) ? trim($_POST['schedule_id']) : '';
				$teacherEmail = isset($_POST['teacher_email']) ? trim($_POST['teacher_email']) : '';
				$subjectName2 = isset($_POST['subject_name2']) ? trim($_POST['subject_name2']) : '';
				$classType2 = isset($_POST['class_type2']) ? trim($_POST['class_type2']) : '';
				$scheduleID2 = isset($_POST['schedule_id2']) ? trim($_POST['schedule_id2']) : '';
				$teacherEmail2 = isset($_POST['teacher_email2']) ? trim($_POST['teacher_email2']) : '';
				
				
				$updateQuery = $pdo->prepare("SELECT subjects.Subject_ID
												FROM subjects
												JOIN subjects_schedule ON subjects.Subject_ID = subjects_schedule.Subject_ID
												WHERE subjects_schedule.Schedule_ID = :scheduleID
													AND subjects.Name_of_Subject = :subjectName
													AND subjects.Type_of_Class = :classType;");
												  
				$updateQuery->bindParam(':scheduleID', $scheduleID, PDO::PARAM_STR);
				$updateQuery->bindParam(':subjectName', $subjectName, PDO::PARAM_STR);
				$updateQuery->bindParam(':classType', $classType, PDO::PARAM_STR);
				$updateQuery->execute();
				$selectedSubjectId = $updateQuery->fetchColumn();
				
				
				$updateQuery = $pdo->prepare("SELECT teacher_ID FROM teachers WHERE Email_Address = ?");
				$updateQuery->execute([$teacherEmail2]);
				$selectedTeacherID = $updateQuery->fetchColumn();
				
				
				$updateQuery = $pdo->prepare("UPDATE teaches SET teacher_ID = ? WHERE Subject_ID = ?");
				$updateQuery->execute([$selectedTeacherID, $selectedSubjectId]);
				$selectedTeacherID = $updateQuery->fetchColumn();
				
				$updateQuery = $pdo->prepare("UPDATE subjects
												JOIN subjects_schedule ON subjects.Subject_ID = subjects_schedule.Subject_ID
												SET subjects_schedule.Schedule_ID = :scheduleID,
													subjects.Name_of_Subject = :subjectName,
													subjects.Type_of_Class = :classType
												WHERE subjects.Subject_ID = :subjectId;");

				$updateQuery->bindParam(':scheduleID', $scheduleID2, PDO::PARAM_STR);
				$updateQuery->bindParam(':subjectName', $subjectName2, PDO::PARAM_STR);
				$updateQuery->bindParam(':classType', $classType2, PDO::PARAM_STR);
				$updateQuery->bindParam(':subjectId', $selectedSubjectId, PDO::PARAM_INT);
				$updateQuery->execute();
				
				

				$alertMessage = "&emsp;&emsp;Subject updated successfully!";
			} catch (PDOException $ex) {
				$alertMessage = "&emsp;&emsp;Failed to update subject. Error: " . $ex->getMessage();
			}
		}
		
		// Manage Subject Form - DELETE SUBJECT (based on Subject_ID)
		if (isset($_POST['delete_subject'])) {
			try {
				$nameOfSubjectToDelete = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
				$classType = isset($_POST['class_type']) ? trim($_POST['class_type']) : '';
				$scheduleID = isset($_POST['schedule_id']) ? trim($_POST['schedule_id']) : '';
				
				$updateQuery = $pdo->prepare("SELECT subjects.Subject_ID
												FROM subjects
												JOIN subjects_schedule ON subjects.Subject_ID = subjects_schedule.Subject_ID
												WHERE subjects_schedule.Schedule_ID = :scheduleID
													AND subjects.Name_of_Subject = :subjectName
													AND subjects.Type_of_Class = :classType;");
												  
										  
												  
		        $updateQuery->bindParam(':scheduleID', $scheduleID, PDO::PARAM_STR);
				$updateQuery->bindParam(':subjectName', $nameOfSubjectToDelete, PDO::PARAM_STR);
				$updateQuery->bindParam(':classType', $classType, PDO::PARAM_STR);

				$updateQuery->execute(); 
				
				$subjectIdToDelete = $updateQuery->fetchColumn();

				// Check if the provided Subject_ID and Name_of_Subject match
				$checkSubjectQuery = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE Subject_ID = ? AND Name_of_Subject = ?");
				$checkSubjectQuery->execute([$subjectIdToDelete, $nameOfSubjectToDelete]);
				$subjectExists = $checkSubjectQuery->fetchColumn();

				if ($subjectExists == 1) {
					$deleteSubjectQuery = $pdo->prepare("DELETE FROM subjects_schedule WHERE Subject_ID = ?");
					$deleteSubjectQuery->execute([$subjectIdToDelete]);
					$deleteSubjectQuery = $pdo->prepare("DELETE FROM teaches WHERE Subject_ID = ?");
					$deleteSubjectQuery->execute([$subjectIdToDelete]);
					// Subject with the provided details exists, proceed with deletion
					$deleteSubjectQuery = $pdo->prepare("DELETE FROM subjects WHERE Subject_ID = ?");
					$deleteSubjectQuery->execute([$subjectIdToDelete]);
					
					$alertMessage = "&emsp;&emsp;Subject deleted successfully!";
				} else {
					// Subject not found
					$alertMessage = "&emsp;&emsp;Failed to delete subject. Subject not found.";
				}
				
			} catch (PDOException $ex) {
				$alertMessage = "&emsp;&emsp;Failed to delete subject. Error: " . $ex->getMessage();
			}
		}
		
		// Manage Student Form - SEARCH FOR STUDENT
		if (isset($_POST['search_student'])) {
			try {
				$search_query = $_POST['search_query'];
				
				$query = $pdo->prepare("SELECT students.Student_ID,
												students.Name AS StudentName,
												students.Gender AS StudentGender,
												students.Email_Address AS StudentEmailAddress,
												subjects.Name_of_Subject,
												subjects.Type_of_Class,
												timecategory.Day,
												GROUP_CONCAT(teachers.Name) AS TeacherName,
												GROUP_CONCAT(teachers.Gender) AS TeacherGender,
												GROUP_CONCAT(teachers.Email_Address) AS TeacherEmailAddress
										FROM students
										LEFT JOIN coursetaken ON students.Student_ID = coursetaken.Student_ID
										LEFT JOIN subjects_schedule ON coursetaken.Subject_ID = subjects_schedule.Subject_ID
										LEFT JOIN subjects ON subjects_schedule.Subject_ID = subjects.Subject_ID
										LEFT JOIN timecategory ON subjects_schedule.Schedule_ID = timecategory.Schedule_ID
										LEFT JOIN teaches ON subjects_schedule.Subject_ID = teaches.Subject_ID
										LEFT JOIN teachers ON teaches.Teacher_ID = teachers.Teacher_ID
										WHERE students.Name LIKE :search_query AND teachers.Teacher_ID > 0
										GROUP BY students.Student_ID, subjects_schedule.Subject_ID, timecategory.Day
										ORDER BY students.Student_ID, subjects_schedule.Subject_ID, timecategory.Day");

				$query->bindParam(':search_query', $search_query, PDO::PARAM_STR);
				$query->execute();

				$result = $query->fetchAll(PDO::FETCH_ASSOC);

				if (!empty($result)) {
					$studentInfo = $result[0];
					foreach ($result as $row) {
						$subjects[] = [
							'SubjectName' => $row['Name_of_Subject'],
							'ClassType' => $row['Type_of_Class'],
							'Day' => $row['Day'],
							'TeacherName' => $row['TeacherName'],
							'TeacherGender' => $row['TeacherGender'],
							'TeacherEmailAddress' => $row['TeacherEmailAddress'],
						];
					}
				}	
			} catch (PDOException $ex) {
				echo "Query failed: " . $ex->getMessage();
			}
			finally {
				// Close the cursor
				if ($query) {
					$query->closeCursor();
				}
			}
		}
		
		// Manage Student Form - ADD/ENROLL STUDENT
		if (isset($_POST['add_student'])) {
			if (!isset($_POST['search_student'])) {
				$studentID = isset($_POST['student_id']) ? $_POST['student_id'] : null;
				$studentName = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
				$studentGender = isset($_POST['student_gender']) ? trim($_POST['student_gender']) : '';
				$studentEmail = isset($_POST['student_email']) ? trim($_POST['student_email']) : '';

				try {
					if (empty($studentID)) {

						$rowCountQuery = $pdo->query("SELECT COUNT(*) FROM students");
						$rowCount = $rowCountQuery->fetchColumn();

						if ($rowCount > 0) {
						$nextIdQuery = $pdo->query("SELECT MAX(Student_ID)+1 AS next_id FROM students");
						$nextId = $nextIdQuery->fetch(PDO::FETCH_ASSOC);
						$nextId = (int)$nextId['next_id'];
						} else {
						$nextId = 1;
						}

						$insertQuery = $pdo->prepare("INSERT INTO students (Student_ID, Name, Gender, Email_Address) VALUES (?, ?, ?, ?)");
						$insertQuery->execute([$nextId, $studentName, $studentGender, $studentEmail]);
						$insertQuery = $pdo->prepare("INSERT INTO coursetaken (ID, Student_ID, Subject_ID) VALUES (?, ?, ?)");
						$subjectStart = 1;
						$subjectEnd = 15;

						for ($subjectID = $subjectStart; $subjectID <= 15; $subjectID++) {
							if ($subjectID <= 11){
								$insertCourseQuery = $pdo->prepare("INSERT INTO coursetaken (ID, Student_ID, Subject_ID) VALUES (?, ?, ?)");
								$insertCourseQuery->execute([15* ($nextId - 1) + $subjectID, $nextId, $subjectID]);
							}
							else{
								$insertCourseQuery = $pdo->prepare("INSERT INTO coursetaken (ID, Student_ID, Subject_ID) VALUES (?, ?, ?)");
								$insertCourseQuery->execute([15* ($nextId - 1) + $subjectID, $nextId, 0]);
							}
						}

						$alertMessage = "&emsp;&emsp;Student added and enrolled successfully!";
					} 
				} catch (PDOException $ex) {
					$alertMessage = "&emsp;&emsp;Failed to add and enroll student. Error: " . $ex->getMessage();
				}
			}
		}
		
		// Manage Student Form - UPDATE STUDENT (based on Email Address)
		if (isset($_POST['edit_student'])) {
			if (!isset($_POST['search_student'])) {
				try {
					$sql = "UPDATE students SET  
							 Name=:name,  
							 Gender=:gender, 
							 Email_Address=:email
							 WHERE Email_Address = :email";

					$stmt = $pdo->prepare($sql);

					$stmt->bindParam(':name', $_POST['student_name']);
					$stmt->bindParam(':gender', $_POST['student_gender']);
					$stmt->bindParam(':email', $_POST['student_email']);

					$stmt->execute();
					$alertMessage = "&emsp;&emsp;Student updated successfully!";
				} catch (PDOException $e) {
					$alertMessage = "&emsp;&emsp;Failed to update student. Error: " . $ex->getMessage();
				}
			}
		}
		
		// Manage Student Form - DELETE STUDENT (based on Email Address)
		if (isset($_POST['delete_student'])) {
			if (!isset($_POST['search_student'])) {
				try {
					$emailToDelete = isset($_POST['student_email']) ? trim($_POST['student_email']) : '';

					$getStudentIdQuery = $pdo->prepare("SELECT Student_ID FROM students WHERE Email_Address = ?");
					$getStudentIdQuery->execute([$emailToDelete]);
					$studentId = $getStudentIdQuery->fetchColumn();
			
					$deleteCourseTakenQuery = $pdo->prepare("DELETE FROM coursetaken WHERE Student_ID = ?");
					$deleteCourseTakenQuery->execute([$studentId]);
					
					$deleteStudentQuery = $pdo->prepare("DELETE FROM students WHERE Email_Address = ?");
					$deleteStudentQuery->execute([$emailToDelete]);
			
					$renumberQuery = $pdo->prepare("SET @new_id := 0; UPDATE students SET Student_ID = @new_id := @new_id + 1;");
					$renumberQuery->execute();

					$alertMessage = "&emsp;&emsp;Student and associated records deleted successfully!";
				} catch (PDOException $ex) {
					$alertMessage = "&emsp;&emsp;Failed to delete student. Error: " . $ex->getMessage();
				}
			}
		}
		
		if (isset($_POST['delete_subject_for_student'])) {
			try {
				$studentEmail = isset($_POST['student_email']) ? trim($_POST['student_email']) : '';
				$subjectName = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';

				// Check if the student with the provided email exists
				$checkStudentQuery = $pdo->prepare("SELECT Student_ID FROM students WHERE Email_Address = ?");
				$checkStudentQuery->execute([$studentEmail]);
				$studentId = $checkStudentQuery->fetchColumn();

				if ($studentId) {
					// Delete the subject for the student
					$deleteQuery = $pdo->prepare("UPDATE coursetaken SET Subject_ID = 0 WHERE Student_ID = ? AND Subject_ID IN (SELECT Subject_ID FROM subjects WHERE Name_of_Subject = ?)");
					$deleteQuery->execute([$studentId, $subjectName]);

					$alertMessage = "&emsp;&emsp;Subject deleted successfully for the student!";
				} else {
					// Student not found
					$alertMessage = "&emsp;&emsp;Failed to delete subject for the student. Student not found.";
				}
			} catch (PDOException $ex) {
				$alertMessage = "&emsp;&emsp;Failed to delete subject for the student. Error: " . $ex->getMessage();
			}
		}
		
		if (isset($_POST['add_subject_for_student'])) {
			try {
				// Retrieve form data
				$subjectName = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
				$studentEmail = isset($_POST['student_email']) ? trim($_POST['student_email']) : '';
				$classType = isset($_POST['class_type']) ? trim($_POST['class_type']) : '';
				$scheduleID = isset($_POST['schedule_id']) ? trim($_POST['schedule_id']) : '';

				// Check if the student email exists in the database
				$checkStudentQuery = $pdo->prepare("SELECT Student_ID FROM students WHERE Email_Address = ?");
				$checkStudentQuery->execute([$studentEmail]);
				$studentInfo = $checkStudentQuery->fetch(PDO::FETCH_ASSOC);

				$updateQuery = $pdo->prepare("
					SELECT subjects.Subject_ID
					FROM subjects
					LEFT JOIN subjects_schedule ON subjects.Subject_ID = subjects_schedule.Subject_ID
					WHERE subjects_schedule.Schedule_ID = :scheduleID
						AND subjects.Name_of_Subject = :subjectName
						AND subjects.Type_of_Class = :classType
				");

				$updateQuery->bindParam(':scheduleID', $scheduleID, PDO::PARAM_STR);
				$updateQuery->bindParam(':subjectName', $subjectName, PDO::PARAM_STR);
				$updateQuery->bindParam(':classType', $classType, PDO::PARAM_STR);

				$updateQuery->execute();

				$subjectId = $updateQuery->fetchColumn();

				if ($studentInfo) {
					// Student exists, get the Student_ID
					$studentID = $studentInfo['Student_ID'];

					// Check if the subject with the same details exists for the student
					$checkSubjectQuery = $pdo->prepare("
						SELECT COUNT(*) 
						FROM coursetaken
						WHERE Student_ID = ? 
							AND Subject_ID = ?
					");

					$checkSubjectQuery->execute([$studentID, $subjectId]);
					$subjectExists = $checkSubjectQuery->fetchColumn();
					
					

					if (!$subjectExists) {
						$checkFindRow = $pdo->prepare ("SELECT ID FROM coursetaken WHERE Student_ID = ? AND Subject_ID = 0");
						$checkFindRow->execute([$studentID]);
						$newcourseId = $checkFindRow->fetchColumn();

						$insertQuery = $pdo->prepare("
							UPDATE coursetaken SET Subject_ID = ? WHERE ID = ? AND Student_ID = ?
						");

						$insertQuery->execute([$subjectId, $newcourseId, $studentID]);
						$affectedRows = $insertQuery->rowCount();
						
						if ($affectedRows > 0){
							$alertMessage = "&emsp;&emsp;Subject added for the student successfully!";
						}
						else {
							$alertMessage = "&emsp;&emsp;Failed to add subject for the student. A student can only enroll in a maximum of 15 subjects.";
						}
					} else {
						$alertMessage = "&emsp;&emsp;Subject already exists for the student.";
					}
				} else {
					$alertMessage = "&emsp;&emsp;Student not found with the provided email address.";
				}
			} catch (PDOException $ex) {
				$alertMessage = "&emsp;&emsp;Failed to add subject for the student. Ask your Administrator to insert that Subject in the Database first. Error: " . $ex->getMessage();
			}
		}
	}
	
	include 'top2.html';
	
	include 'manage_teacher.php';

	include 'bottom.html';
?>


