<?php require_once('../includes/core.php'); ?>
<?php

$method = $_SERVER['REQUEST_METHOD'];

$response = array(
  "type" => $method
);

switch($method) {
  case "GET":
    // Check if the session contains a user and return success if it does
    if (!empty($_SESSION["user"])) {
      $response["status"] = "success";
    } else {
      $response["status"] = "error";
    }
  break;
  case "POST":
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
      // Typecast to integer value
      $user_match = intval(User::verify($_POST["username"], $_POST["password"]));

      // It will be 0 if it failed which will mean it will also be validated as empty
      // Otherwise it will have the user id, which is 1 or higher and therefor not empty
      if (!empty($user_match)) {
        $_SESSION["user"] = $user_match;
        $response["status"] = "success";
      } else {
        $response["status"] = "error";
      }
    } elseif (!empty($_POST["logout"]) && $_POST["logout"] == true) {
      // destroy session/log the user out
      session_destroy();
      $response["status"] = "success";
    } else {
      $response["status"] = "error";
    }
  break;
}

echo json_encode($response);

?>
