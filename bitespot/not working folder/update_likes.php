<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_reviews";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the review ID from POST request
    $user_id = isset($_POST["user_id"]) ? intval($_POST["user_id"]) : 0;

    if ($user_id > 0) { // Changed to user_id check
        // Increment like count. Changed to use likes table.
        $restaurant_id = isset($_POST["restaurant_id"]) ? intval($_POST["restaurant_id"]) : 0; //get restaurant id

        if($restaurant_id > 0){
          // Check if the like already exists
          $like_check_sql = "SELECT id FROM likes WHERE restaurant_id = ? AND user_id = ?";
          $like_check_stmt = $conn->prepare($like_check_sql);
          $like_check_stmt->bind_param("ii", $restaurant_id, $user_id);
          $like_check_stmt->execute();
          $like_check_stmt->store_result();

          if ($like_check_stmt->num_rows > 0) {
              // Like exists, remove it
              $like_delete_sql = "DELETE FROM likes WHERE restaurant_id = ? AND user_id = ?";
              $like_delete_stmt = $conn->prepare($like_delete_sql);
              $like_delete_stmt->bind_param("ii", $restaurant_id, $user_id);
              $like_delete_stmt->execute();
              $like_status = false;
          } else {
              // Like does not exist, add it
              $like_insert_sql = "INSERT INTO likes (restaurant_id, user_id) VALUES (?, ?)";
              $like_insert_stmt = $conn->prepare($like_insert_sql);
              $like_insert_stmt->bind_param("ii", $restaurant_id, $user_id);
              $like_insert_stmt->execute();
              $like_status = true;
          }

          // Get the current like count
          $count_sql = "SELECT COUNT(*) FROM likes WHERE restaurant_id = ?";
          $count_stmt = $conn->prepare($count_sql);
          $count_stmt->bind_param("i", $restaurant_id);
          $count_stmt->execute();
          $count_stmt->bind_result($like_count);
          $count_stmt->fetch();

          echo json_encode(["success" => true, "message" => "Like updated", "count"=>$like_count, "liked"=>$like_status]);
        }else{
          echo json_encode(["success" => false, "message" => "Invalid restaurant ID"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    }
}

$conn->close();
?>