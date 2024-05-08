<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $studentInfo !== null) {
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
		echo "<div class = 'container'>";
		echo "<br/>&emsp;&emsp;<button id='toggleTableButton' onclick='toggleTable()'>Show Information on Courses Taken</button>";
		echo "</div>";
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
	}elseif ($_SERVER["REQUEST_METHOD"] == "POST" && empty($studentInfo)) {
		echo "<p style='color: red;'>&emsp;&emsp;Student not found in the database.</p>";
	}
?>

<p>
    <?php
		if (!empty($alertMessage)) {
			echo $alertMessage;
		}
    ?>
</p>

<script>
    function toggleTable() {
        var subjectsTable = document.getElementById('subjectsTable');
		subjectsTable.classList.toggle('hidden');
    }
</script>

<div class = "container">
	<button onclick="window.location.href='adminindex.php'">Click for Admin Login</button>
	<button onclick="window.location.href='teacherindex.php'">Click for Teacher Login</button>
</div>
