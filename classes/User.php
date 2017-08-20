<?php

if (!class_exists("db")) {
  require_once("db.php");
}

class User {
  public static function verify($username = null, $password = null) {
    if (!empty($username) && !empty($password)) {
      // Hash password before check
      $password = hash('sha512', $password . '$hsdfohsdo');

      // Check if the username and hashed password match a user
      $query = db::getInstance()->query("SELECT users.id FROM users WHERE users.username = ? AND users.password = ?", array($username, $password));
      if ($query->count()) {
        foreach($query->results() as $user) {
          $user_id = $user->id;
        }

        // Returns the user id if found
        return $user_id;
      }
    }
    
    // Otherwise return false
    return false;
  }
}

?>
