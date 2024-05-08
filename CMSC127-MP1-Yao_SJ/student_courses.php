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
										WHERE students.Name LIKE :search_query 
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
	}

	include 'top.html';

	include 'enrolled.php';

	include 'bottom.html';
?>