<?php
	require "model/database.php";

/*	function get_all_students() {
		global $database;

		//Get all students.
		$result = $database->query("SELECT * FROM student");

		//Echo all students.
		if ($result == false) {
			echo "An error occurred while fetching the student data.";
		}
		else if ($result !== true) {
			if ($result->num_rows > 0) {
				while ($student = $result->fetch_assoc()) {
					echo "
					<tr>
						<td>" . $student["student_id"] . "</td>
						<td>" . $student["name"] . "</td>
						<td>" . $student["age"] . "</td>
					</tr>";
				}
			}
		}
	}*/

	function create_new_invited($name, $age, $dinner) {
		global $database;

		$name = strip_tags($name);
		$name = addslashes($name);
		$age = intval($age);
        $dinner = addslashes($dinner);

		$result = $database->query("INSERT INTO `event`(name, age, dinner) VALUES('$name', $age, '$dinner')");

		if (!$result) {
			$error = array(
				"message" => "An error occurred while saving the quest data.",
				"error" => $database->error
			);
			echo json_encode($error);
		}
		else {
			$message = array(
				"message" => "The invited was successfully created."
			);
			echo json_encode($message);
		}
	}

	function remove_invited($invited_id) {
		global $database;

		$invited_id = intval($invited_id);

		$result = $database->query("DELETE FROM `event` WHERE invited_id = $invited_id");

		if (!$result) {
			echo "An error occurred while deleting the invited data.";
		}
		else {
			echo "The invited was successfully deleted.";
		}
	}

	function get_student($invited_id) {
		global $database;

		$result = $database->query("SELECT * FROM `event` WHERE invited_id = $invited_id");

		if (!$result) {
			http_response_code(500);
			$error = array("message" => "An error occurred while fetching the invited.");
			echo json_encode($error);
		}
		else if ($result === true || $result->num_rows == 0) {
			http_response_code(404);
			$error = array("message" => "No invited found for the ID " . $invited_id . ".");
			echo json_encode($error);
		}
		else {
			$student = $result->fetch_assoc();

			return $invited;
		}
	}

	function update_invited($invited_id, $name, $age, $dinner) {
		global $database;

		$result = $database->query("UPDATE `event` SET name = '$name', age = $age, dinner = '$dinner' WHERE invited_id = $invited_id");

		if (!$result) {
			echo "An error occurred while saving the student data.";
		}
		else {
			echo "The student was successfully updated.";
		}
	}

    ?>