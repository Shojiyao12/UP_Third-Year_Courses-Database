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
	$subjects = [];

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

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

	include 'top.html';

	include 'manage_student.php';

	include 'bottom.html';
?>