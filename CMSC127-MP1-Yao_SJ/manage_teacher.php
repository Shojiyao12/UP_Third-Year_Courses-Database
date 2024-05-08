<?php
	if (isset($_POST["logout"])) {

    header("Location: student_courses.php");
    exit();
}

?>

<!-- Search For a Subject -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="searchForm" class = "otherforms">
		<label for="search_query">Search for a Subject/s:</label>
        <input type="text" name="search_query" id="search_query" required>
        <button type="submit" name="search_subject">Search</button><br/><br/>
</form>
<div class = "container">
<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search_query"])) {
		if (!empty($teacherInfo)) {
			// Display Teacher Information
			echo "<h3>Teacher Information:</h3>";
			echo "<table border='1'>
				<tr>
					<th>Name</th>
					<th>Gender</th>
					<th>Email Address</th>
				</tr>";
			echo "<tr>
					<td>{$teacherInfo['TeacherName']}</td>
					<td>{$teacherInfo['TeacherGender']}</td>
					<td>{$teacherInfo['TeacherEmailAddress']}</td>
					</tr>";
			echo "</table><br/>";
		}
		else if (!empty($studentInfo)) {
			echo "<h3>Student Information:</h3>";
			echo "<table border='1'>
					<tr>
						<th>Name</th>
						<th>Gender</th>
						<th>Email Address</th>
					</tr>";
			echo "<tr>
						<td>{$studentInfo['StudentName']}</td>
						<td>{$studentInfo['StudentGender']}</td>
						<td>{$studentInfo['StudentEmailAddress']}</td>
						</tr>";
			echo "</table>";

			// Button to Show/Unshow Information on Courses Taken 
			echo "<br/><br/>&emsp;&emsp;<button id='toggleTableButton' onclick='toggleTable()'>Show Information on Courses Taken</button>";

			echo "<div id='subjectsTable' class='hidden'>"; // Initially hidden
			echo "<h3>Information on Courses Taken:</h3>";
			if (!empty($subjects)) {
				echo "<table border='1'>
						<tr>
							<th>Subject Name</th>
							<th>Class Type (Lec/Lab)</th>
							<th>Day</th>
							<th>Teacher Name</th>
							<th>Teacher Gender</th>
							<th>Teacher Email Address</th>
						</tr>";
				foreach ($subjects as $subject) {
					echo "<tr>
							<td>{$subject['SubjectName']}</td>
							<td>{$subject['ClassType']}</td>
							<td>{$subject['Day']}</td>
							<td>{$subject['TeacherName']}</td>
							<td>{$subject['TeacherGender']}</td>
							<td>{$subject['TeacherEmailAddress']}</td>
							</tr>";
				}
				echo "</table>";
			} else {
				echo "<p>&emsp;&emsp;No subjects found for this student.</p>";
			}
			echo "</div>";
		}
		
		elseif (!empty($resultSubj)) {
			// Display Subjects Information
			echo "<div id='subjectsTable'>";
			echo "<h3>Subject Information:</h3>";
			echo "<table border='1'>
					<tr>
						<th>Name of Subject</th>
						<th>Type of Class (Lec/Lab)</th>
					</tr>";

			foreach ($resultSubj as $row) {
				echo "<tr>
						<td>{$row['Name_of_Subject']}</td>
						<td>{$row['Type_of_Class']}</td>
					</tr>";
			}

			echo "</table></div><br/>";
		}
		else {
			// No teacher or subject information entered yet
			echo "<p style='color: red;'>&emsp;&emsp;No teacher, student or subject/s found for the given search query.</p>";
		}
	}
?>
    <button id="toggleManageTeacherButton" onclick="toggleManageTeachSubjForm()">Access Teacher Management Form</button>

    <!-- Manage Teacher Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="addTeacherSubjForm" class="hidden">
        <h3 class="manage_header">Manage Teacher:</h3>
        <input type="hidden" name="teacher_id" value="">
        <label for="teacher_name">Full Name (Exclude Middle Name):</label>
        <input type="text" name="teacher_name" id="teacher_name" value="" required>
        <label for="teacher_gender">Gender (Format: Male/Female):</label>
        <input type="text" name="teacher_gender" id="teacher_gender" value="" required>
        <label for="teacher_email">Email Address(Note: This will also be the username for Teacher Login):</label>
        <input type="text" name="teacher_email" id="teacher_email" value="" required>
		<label for="teacher_password">Desired Password for Teacher Account:</label>
        <input type="text" name="teacher_password" id="teacher_email" value="" required>
		
        <!-- Buttons for Adding, Updating, and Deleting a Teacher -->
        <br/><button type="submit" name="add_teacher">Add Teacher</button>&ensp;
        <button type="submit" name="edit_teacher">Update Teacher</button>&ensp;
		<button type="submit" name="delete_teacher">Delete Teacher</button>
    </form>
	
	<button id="toggleManageSubjectButton2" onclick="toggleManageTeachSubjForm2()">Access Subject Management Form (ADD/DELETE)</button>

    <!-- Manage Subject Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="addTeacherSubjForm2" class="hidden">
        <h3 class="manage_header">Manage Subject (Add/Delete):</h3>
        <input type="hidden" name="subject_id_hidden" value="">
		<label for="subject_name">Subject Name (Format: CMSC 121):</label>
		<input type="text" name="subject_name" id="subject_name" required>
		<label for="class_type">Type of Class (Format: Lec/Lab):</label>
		<input type="text" name="class_type" id="class_type" required>
		<label for="schedule_id">Schedule ID (Note: 1-Mon, 2-Tues, 4-Thurs, 5-Fri ONLY):</label>
		<input type="text" name="schedule_id" id="schedule_id" required>
		<label for="student_email">Email Address of Teacher:</label>
        <input type="text" name="teacher_email" id="teacher_email" value="" required>
		
		<!-- Buttons for Adding and Deleting a Subject -->
		<br/><button type="submit" name="add_subject">Add Subject</button>&ensp;
		<button type="submit" name="delete_subject">Delete Subject</button>
	</form>	
	
	<button id="toggleManageSubjectButton3" onclick="toggleManageTeachSubjForm3()">Access Subject Management Form (UPDATE)</button>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="addTeacherSubjForm3" class="hidden">
		<h3 class="manage_header">Manage Subject (Update):</h3>
		<input type="hidden" name="subject_id_hidden" value="">
		<label for="subject_name">Subject Name (Format: CMSC 121):</label>
		<input type="text" name="subject_name" id="subject_name" required>
		<label for="class_type">Type of Class (Format: Lec/Lab):</label>
		<input type="text" name="class_type" id="class_type" required>
		<label for="schedule_id">Schedule ID (Note: 1-Mon, 2-Tues, 4-Thurs, 5-Fri ONLY):</label>
		<input type="text" name="schedule_id" id="schedule_id" required>
		<label for="teacher_email">Email Address of Teacher:</label>
		<input type="text" name="teacher_email" id="teacher_email" value="" required>
		<label for="subject_name2">Subject Name [Updated] (Format: CMSC 121):</label>
		<input type="text" name="subject_name2" id="subject_name2" required>
		<label for="class_type2">Type of Class [Updated] (Format: Lec/Lab):</label>
		<input type="text" name="class_type2" id="class_type2" required>
		<label for="schedule_id2">Schedule ID [Updated] (Note: 1-Mon, 2-Tues, 4-Thurs, 5-Fri ONLY):</label>
		<input type="text" name="schedule_id2" id="schedule_id2" required>
		<label for="teacher_email2">Email Address of Teacher (Updated):</label>
		<input type="text" name="teacher_email2" id="teacher_email2" value="" required>
			
		<!-- Buttons for Updating a Subject -->
		<br/><button type="submit" name="edit_subject">Update Subject</button>
	</form>
	
	<button id="toggleManageStudentButton" onclick="toggleManageStudentForm()">Access Student Management Form</button>

    <!-- Manage Student Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="addStudentForm" class="hidden">
        <h3 class="manage_header">Manage Student:</h3>
        <input type="hidden" name="student_id" value="">
        <label for="student_name">Full Name (Exclude Middle Name):</label>
        <input type="text" name="student_name" id="student_name" value="" required>
        <label for="student_gender">Gender (Format: Male/Female):</label>
        <input type="text" name="student_gender" id="student_gender" value="" required>
        <label for="student_email">Email Address:</label>
        <input type="text" name="student_email" id="student_email" value="" required>

        <!-- Buttons for Adding/Enrolling, Updating, and Deleting a Student -->
        <br/><button type="submit" name="add_student">Add/Enroll Student</button>&ensp;
        <button type="submit" name="edit_student">Update Student</button>&ensp;
		<button type="submit" name="delete_student">Delete Student</button>
    </form>
	
	<br/><br/><button id="toggleManageStudentButton" onclick="toggleManageStudentForm2()">Access Student-Subject Management Form (ENROLL or UNENROLL)</button>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="addStudentForm2" class="hidden">
        <h3 class="manage_header">Manage Subject for a Student:</h3>
        <input type="hidden" name="subject_id" value="">
        <label for="subject_name">Name of the Subject (Format: CMSC 121):</label>
        <input type="text" name="subject_name" id="subject_name" value="" required>
        <label for="student_email">Email Address of Student:</label>
        <input type="text" name="student_email" id="student_email" value="" required>
		<label for="class_type">Type of Class (Format: Lec/Lab):</label>
		<input type="text" name="class_type" id="class_type" required>
		<label for="schedule_id">Schedule ID (Note: 1-Mon, 2-Tues, 4-Thurs, 5-Fri ONLY):</label>
		<input type="text" name="schedule_id" id="schedule_id" required>

        <!-- Buttons for Adding, Updating, and Deleting a Subject for a Student -->
        <br/><button type="submit" name="add_subject_for_student">Enroll a Subject for the Student</button>&ensp;
		<button type="submit" name="delete_subject_for_student">Unenroll a Subject for the Student</button>
    </form>
	
<br/><br/>
<button id="toggleshowAllForm" onclick="toggleshowAllForm()">SHOW ALL (Teacher/Student/Subject)</button>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="showAll" class="hidden">
    <button type="submit" name="show_all_teachers" onclick = 'toggleTeachersAll'>Show All Teachers</button>
	&ensp;<button type="submit" name = "show_all_students" onclick='toggleStudentsAll'>Show All Students</button>
	&ensp;<button type="submit" name = "show_all_subjects" onclick='toggleSubjectsAll'>Show All Subjects</button>
</form>
<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['show_all_teachers'])) {
			try {
				$allTeachersQuery = $pdo->prepare("SELECT teachers.Name AS TeacherName,
													teachers.Gender AS TeacherGender,
													teachers.Email_Address AS TeacherEmail_Address FROM teachers WHERE Teacher_ID > 0");
				$allTeachersQuery->execute();
				$allTeachers = $allTeachersQuery->fetchAll(PDO::FETCH_ASSOC);

				echo "<div id='teachersTable'>";
				echo "<h3>List of ALL Teachers Information:</h3>";
				echo "<table border='1'>
						<tr>
							<th>Name</th>
							<th>Gender</th>
							<th>Email Address</th>
						</tr>";

				foreach ($allTeachers as $teacher) {
					echo "<tr>
							<td>{$teacher['TeacherName']}</td>
							<td>{$teacher['TeacherGender']}</td>
							<td>{$teacher['TeacherEmail_Address']}</td>
						</tr>";
				}

				echo "</table></div>";
				$allTeachersQuery->closeCursor();
			} catch (PDOException $ex) {
				// Handle the exception (e.g., log it, display an error message)
				echo "An error occurred: " . $ex->getMessage();
			}
		}
		
		if (isset($_POST['show_all_students'])) {
			try{
				$allStudentsQuery = $pdo->prepare("SELECT students.Name AS StudentName,
													students.Gender AS StudentGender,
													students.Email_Address AS StudentEmail_Address FROM students");
				$allStudentsQuery->execute(); 
				$allStudents = $allStudentsQuery->fetchAll(PDO::FETCH_ASSOC);
						
				
				echo "<div id='studentsTable'>";
				echo "<h3>List of ALL Students Information:</h3>";
				echo "<table border='1'>
						<tr>
							<th>Name</th>
							<th>Gender</th>
							<th>Email Address</th>
						</tr>";

				foreach ($allStudents as $student) {
					echo "<tr>
							<td>{$student['StudentName']}</td>
							<td>{$student['StudentGender']}</td>
							<td>{$student['StudentEmail_Address']}</td>
						</tr>";
				}

				echo "</table></div>";
				$allStudentsQuery->closeCursor();
				unset($allStudentsQuery);
			}catch (PDOException $ex) {
				// Handle the exception (e.g., log it, display an error message)
				echo "An error occurred: " . $ex->getMessage();
			}
		}
		
		if (isset($_POST['show_all_subjects'])) {
			try {
				$teachersAndSubjectsQuery = $pdo->prepare("SELECT
															teachers.Name AS TeacherName,
															teachers.Gender AS TeacherGender,
															teachers.Email_Address AS TeacherEmailAddress,
															subjects.*, timecategory.Day AS SubjectDay, subjects_schedule.Schedule_ID
																FROM teachers
																INNER JOIN teaches ON teachers.Teacher_ID = teaches.Teacher_ID
																INNER JOIN subjects ON subjects.Subject_ID = teaches.Subject_ID
																INNER JOIN subjects_schedule ON subjects.Subject_ID = subjects_schedule.Subject_ID
																INNER JOIN timecategory ON subjects_schedule.Schedule_ID = timecategory.Schedule_ID");
				$teachersAndSubjectsQuery->execute(); 
				$teachersAndSubjects = $teachersAndSubjectsQuery->fetchAll(PDO::FETCH_ASSOC);
				echo "<div id='subjectsTable2'>";
				if (!empty($teachersAndSubjects)) {
					echo "<div id = 'subjectsTable'><h3>List of ALL Information on Teachers, Subjects, and Schedules:</h3>";
					echo "<table border='1'>
							<tr>
								<th>Subject Name</th>
								<th>Type of Class (Lec/Lab)</th>
								<th>Subject Day</th>
								<th>Teacher Name</th>
								<th>Teacher Gender</th>
								<th>Teacher Email Address</th>
							</tr>";

					foreach ($teachersAndSubjects as $row) {
						echo "<tr>
								<td>{$row['Name_of_Subject']}</td>
								<td>{$row['Type_of_Class']}</td>
								<td>{$row['SubjectDay']}</td>
								<td>{$row['TeacherName']}</td>
								<td>{$row['TeacherGender']}</td>
								<td>{$row['TeacherEmailAddress']}</td>
							</tr>";
					}

					echo "</table></div><br/>";
							
				} else {
					echo "<p style='color: red;'>&emsp;&emsp;No teachers and subjects found for the given subjects.</p>";
				}
				echo "</div>";
				$teachersAndSubjectsQuery->closeCursor();
				unset($teachersAndSubjectsQuery);
			} catch (PDOException $ex) {
				// Handle the exception (e.g., log it, display an error message)
				echo "An error occurred: " . $ex->getMessage();
			}
		}
	}
?>
</div>
<p>
    <?php
		if (!empty($alertMessage)) {
			echo $alertMessage;
		}
    ?>
</p>

<script>
	function toggleshowAllForm() {
        var form = document.forms["showAll"];
        form.classList.toggle("hidden");
    }
	
	function toggleTeachersAll() {
        var teachersTable = document.getElementById('teachersTable');
		teachersTable.classList.toggle('hidden');
    }
	
	function toggleStudentsAll() {
        var studentsTable = document.getElementById('studentsTable');
		studentsTable.classList.toggle('hidden');
    }
	
	function toggleSubjectsAll() {
        var subjectsTable2 = document.getElementById('subjectsTable2');
		subjectsTable2.classList.toggle('hidden');
    }
	
	function toggleManageTeachSubjForm() {
        var manageTeachSubjForm = document.querySelector('form[name="addTeacherSubjForm"]');
        manageTeachSubjForm.classList.toggle('hidden');
    }
	
	function toggleManageTeachSubjForm2() {
        var manageTeachSubjForm2 = document.querySelector('form[name="addTeacherSubjForm2"]');
        manageTeachSubjForm2.classList.toggle('hidden');
    }
	
	function toggleManageTeachSubjForm3() {
        var manageTeachSubjForm3 = document.querySelector('form[name="addTeacherSubjForm3"]');
        manageTeachSubjForm3.classList.toggle('hidden');
    }
	
	function toggleTable() {
        var subjectsTable = document.getElementById('subjectsTable');
		subjectsTable.classList.toggle('hidden');
    }
	
	function toggleManageStudentForm() {
        var manageStudentForm = document.querySelector('form[name="addStudentForm"]');
        manageStudentForm.classList.toggle('hidden');
    }
	
	function toggleManageStudentForm2() {
        var manageStudentForm2 = document.querySelector('form[name="addStudentForm2"]');
        manageStudentForm2.classList.toggle('hidden');
    }
</script>

<form method="post" action="">
    &emsp;<input type="submit" name="logout" value="Logout">
</form>
