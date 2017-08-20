<?php require_once('../includes/core.php'); ?>
<?php

// Get the request method and the user session
$method = $_SERVER['REQUEST_METHOD'];
$user = $_SESSION["user"];

$response = array(
	"type" => $method
);

// If there is no user session return error
if (!empty($user)) {
	switch($method) {
		case "GET":
			$response["status"] = "success";

			$query = db::getInstance()->query("SELECT * FROM tasks WHERE tasks.fk_user_id = ?", array($user));
			if ($query->count()) {
				// Fetch the tasks
				foreach($query->results() as $task) {
					$id = $task->id;
					$title = $task->title;
					$done = $task->done;

					// Set done to true if value is higher than 0
					// Otherwise set done to false
					if ($done > 0) {
						$done = true;
					} else {
						$done = false;
					}

					// Create array and fetch all notes for the corresponding task.
					$notes = array();
					$noteQuery = db::getInstance()->query("SELECT notes.note_id, notes.note_content FROM notes WHERE notes.fk_task_id = ?", array($id));
					if ($noteQuery->count()) {
						foreach($noteQuery->results() as $note) {
							$notes[$note->note_id] = $note->note_content;
						}
					}

					// Add the task info to the response
					$response["content"][] = array(
						"id" => $id,
						"title" => $title,
						"done" => $done,
						"notes" => $notes
					);
				}
			}

		break;
		case "POST":
			// Check whether or not to handle it as a note or task
			if (!empty($_POST["newNote"]) && !empty($_POST["taskId"])) {
				$taskId = $_POST["taskId"];
				$note = $_POST["newNote"];

				// Insert the new note to the related task.
				$query = db::getInstance()->query("INSERT INTO notes(fk_task_id, note_content) VALUES(?, ?)", array($taskId, $note));
				// If the query could be completed return success
				// Otherwise return error and an error message
				if ($query->count()) {
					$response["status"] = "success";
				} else {
					$response["status"] = "error";
					$response["errorMessage"] = "Note could not be saved...";
				}
			} elseif (!empty($_POST["title"])) {
				$title = $_POST["title"];

				// Insert the task and link it with the currently logged in user
				$query = db::getInstance()->query("INSERT INTO tasks(title, fk_user_id) VALUES(?, ?)", array($title, $user));
				// If the query could be completed return success
				// Otherwise return error and an error message
				if ($query->count()) {
					$response["status"] = "success";
				} else {
					$response["status"] = "error";
					$response["errorMessage"] = "Task could not be saved...";
				}
			}
		break;
		case "PUT":
			// Get put values and insert them into $_PUT
			parse_str(file_get_contents("php://input"), $_PUT);

			// Check if the required values are empty
			if (!empty($_PUT["done"]) && !empty($_PUT["taskId"])) {
				// Change done to integers (as the database uses them instead of true/false)
				$done = $_PUT["done"] === "true" ? 1: 0;
				$taskId = (int) $_PUT["taskId"];

				$values["done"] = $done;

				// Update the task with the done value
				$success = db::getInstance()->update("tasks", $taskId, $values);

				// If the query could be completed return success
				// Otherwise return error and an error message
				if ($success) {
					$response["status"] = "success";
				} else {
					$response["status"] = "error";
					$response["errorMessage"] = $query->error();
				}
			} else {
				$response["status"] = "error";
				$response["errorMessage"] = "Could not process the request...";
			}
		break;
		default:
			$response["status"] = "error";
			$response["errorMessage"] = "Unknown error occured...";
		break;
	}
} else {
	$response["status"] = "error";
	$response["errorMessage"] = "Access denied!";
}

echo json_encode($response);

?>
