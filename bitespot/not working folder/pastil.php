<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_reviews";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $review_text = $_POST["review_text"];
    $rating = $_POST["rating"];
    $restaurantId = $_POST["restaurant_id"];
    $restaurant_id=2;

    if ($rating < 1 || $rating > 5) {
        echo "<script>alert('Rating must be between 1 and 5.'); window.location.href = window.location.href;</script>";
        return;
    } else {
        $user_check_sql = "SELECT user_id FROM users WHERE username = ?";
        $user_check_stmt = $conn->prepare($user_check_sql);
        $user_check_stmt->bind_param("s", $username);
        $user_check_stmt->execute();
        $user_check_stmt->store_result();

        if ($user_check_stmt->num_rows > 0) {
            $user_check_stmt->bind_result($user_id);
            $user_check_stmt->fetch();
            $user_check_stmt->close();

            $user_update_sql = "UPDATE users SET restaurant_id = ? WHERE user_id = ?";
            $user_update_stmt = $conn->prepare($user_update_sql);
            $user_update_stmt->bind_param("ii", $restaurantId, $user_id);
            $user_update_stmt->execute();
            $user_update_stmt->close();
        } else {
            $user_check_stmt->close();
            $user_insert_sql = "INSERT INTO users (username, restaurant_id) VALUES (?, ?)";
            $user_insert_stmt = $conn->prepare($user_insert_sql);
            $user_insert_stmt->bind_param("si", $username, $restaurantId);
            $user_insert_stmt->execute();
            $user_id = $user_insert_stmt->insert_id;
            $user_insert_stmt->close();
        }

        $sql = "INSERT INTO reviews (user_id, review_text, rating, timestamp, restaurant_id) VALUES (?, ?, ?, NOW(), ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $user_id, $review_text, $rating, $restaurant_id);

        if ($stmt->execute()) {
            echo "<script>document.getElementById('modalContentArea').innerHTML = ''; setTimeout(function(){ document.getElementById('reviewModal').style.display = 'none'; }, 2000);</script>";
        } else {
            echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "'); window.location.href = window.location.href;</script>";
        }

        $stmt->close();
    }
}

$restaurant_id = isset($_GET['restaurant_id']) ? $_GET['restaurant_id'] : 2;

$sql = "SELECT reviews.review_text, reviews.rating, reviews.timestamp, users.username FROM reviews JOIN users ON reviews.user_id = users.user_id WHERE reviews.restaurant_id = ? ORDER BY reviews.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BITESPOT - Eros Carinderia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
/* General reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Background and font settings */
body, html {
  background-color: #fff;
  font-family: monts;

}
header {
  background-color: #ffffff;
  padding: 5px 5px;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  display: flex; /* Add flexbox */
  align-items: center; /* Vertically align items */
  justify-content: space-between; /* Space between header and nav */
  border-bottom: 1.5px solid #1f1f1f4f; /* Add border only below */
  transition: box-shadow 0.4s ease-in-out; /* Adjusted transition */
}

header a {
  text-decoration: none;
}

header.shadow { /* Added class selector */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.239);
}

header h1 {
  margin: 0;
  color: #FF9202;
  font-size: 2rem;
  font-family: monts;
  padding-left: 20px;
}


nav { 
    display: flex;
    align-items: center;
}

nav ul {
  list-style-type: none;
  padding: 10px;
  background-color: #ffffff; 
  text-align: right;
  position: static; 
  width: auto;
  font-size: small;
  font-family: monts;
  transition: box-shadow 0.3s ease;
  display: flex; 
  margin: 0; 
}

nav ul li {
  margin-left: 20px;
}

nav ul li a {
  text-decoration: none;
  color: #737373;
  font-weight: bold;
  border-radius: 5px;
  padding: 4px;
  margin-right: 20px;
}

nav ul li a:hover{
  background-color: #ff960ddd; /* Change background color on hover */
  color: white;
}


/* Back button styling */
.back-button {
  position: flex;
  margin-top: 60px;
  margin-left: 30px;
  font-size: 2rem;
}

.back-button a {
  text-decoration: none;
  color: #5e5e5e;
  transition: color 0.3s ease;
}

.back-button a:hover {
  color: #161616;
}

/* Restaurant Container */
.restaurant-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  margin-top: -55px;
}

/* Horizontal Card Layout */
.restaurant {
  background-color: #e7e7e75a;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.352); /* Create a card shadow */
  display: flex;
  background-color: #f4f4f4;
  padding: 20px;
  width: 80%; /* Takes 80% of the screen width */
  text-align: left;
  transition: transform 0.3s ease;
  text-decoration: none;
  margin-bottom: 20px;
  border-style: solid;
  border-width: 1px;
  border-color: #1f1f1f2e;
}

/* Image on the Left */
.restaurant img {
  width: 40%; /* Set image to take up 40% of the card */
  height: auto;
  border-radius: 10px;
  margin-right: 20px;
  height: auto; /* Keeps the image's aspect ratio */
  object-fit: cover; /* Ensures the image fits inside the container while maintaining its aspect ratio */

}

/* Info on the Right */
.restaurant-info {
  width: 60%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.restaurant-info h3 {
  font-size: 1.5em;
  color: #333;
  margin-bottom: 10px;
}

.restaurant-container .rating {
  color: #ffe100;
  font-size: 1.2em;
  margin-bottom: 10px;
  -webkit-text-stroke: 1px #e2c800; /* Add an outline around the icon */

}

.location {
  margin-bottom: 10px;
}

.location a {
  display: inline-block; /* Makes the link behave like a block-level button */
  padding: 5px 12px; /* Add padding to make it look like a button */
  color: #fff; /* Set the text color to white */
  text-decoration: none; /* Remove the underline */
  background-color: #007bff; /* Set the background color */
  border-radius: 5px; /* Round the button corners */
  transition: background-color 0.3s ease; /* Add a smooth hover effect */
}

.location a:hover {
  background-color: #0056b3; /* Darken the background on hover */
}


.restaurant-info p {
  color: #333;
  margin-bottom: 20px;
}

.heart-icon.liked {
    color: orange; /* Color when liked */
}

/* Action buttons styling */
.actions {
  display: flex;
  justify-content: flex-start;
  gap: 30px;
  margin-top: 10px;
}

.actions button {
  background: none; /* Remove button background */
  border: none; /* Remove button border */
  color: #e7e7e75a; /* Set icon color */
  cursor: pointer;
  font-size: 1.2rem; /* Increase icon size if needed */
  transition: color 0.3s ease; /* Smooth color change on hover */
  -webkit-text-stroke: 2px #333; /* Add an outline around the icon */
  transition: -webkit-text-stroke 0.3s ease;

}

.actions button:hover {
  color: #FF9202;
}

.actions button:focus {
  outline: none;
}
/* Add Review Button Styling */
.add-review {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  width: 90%;
}

.add-review-btn {
  background-color: #FF9202;
  color: white;
  padding: 2px 15px;
  font-size: 1.1em; /* Font size for "Add Review" */
  font-family: monts;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.add-review-btn .plus-sign {
  font-size: 2em; /* Larger font size for the + */
  vertical-align: middle; /* Aligns the + symbol to the middle */
}

.add-review-btn:hover {
  background-color: #e88900;
}


.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  justify-content: center; 
  align-items: center; 
}


.modal-content {
  background-color: white;
  margin: 15% auto;
  border-radius: 10px;
  padding: 20px;
  width: 40%;
  border-radius: 10px;
  color: #333;
}

.close-btn {
  color: rgb(255, 44, 44);
  float: right;
  font-size: 28px;
  cursor: pointer;
  margin-top: -6px; /* Adjust the value as needed */
}


.close-btn:hover,
.close-btn:focus {
  color: rgb(182, 0, 0);
  text-decoration: none;
  cursor: pointer;
}

.star-rating {
  display: flex; /* Use flexbox for centering */
  justify-content: center; /* Center the stars horizontally */
  direction: rtl; /* Reverse the order of the stars */
}

.star-rating input[type="radio"] {
  display: none;
}

.star-rating label {
  font-size: 2em; /* Adjust star size */
  color: #ddd; /* Unselected star color */
  padding: 0 5px;
  cursor: pointer;
}

.star-rating input[type="radio"]:checked ~ label {
  color: #FF9202; /* Selected star color */
}

/* Styling for the review form */
#reviewForm {
display: flex;
flex-direction: column;
margin-top: -10px;
}

#reviewForm label {
margin-top: 10px;
}

#review_text{
  resize: none;

}


#reviewForm textarea,
#reviewForm input[type="number"] {
margin-bottom: 10px;
padding: 8px;
border: 1px solid #ccc;
border-radius: 4px;
}

#reviewForm button {
padding: 10px 15px;
background-color: #FF9202;
color: white;
border: none;
border-radius: 4px;
cursor: pointer;
}

#reviewForm button:hover {
background-color: #e88900;
}


#usernameInput {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 5px solid #ccc; 
  border-width: 5px;
  border-radius: 5px;
  border-color:rgba(3, 3, 3, 0.67);
  font-family: monts;
}

#submitReview, #submitUsernameBtn {
  background-color: #FF9202;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-family: monts;
  display: block; 
}




.reviews {
  width: 80%;
  margin-top: 20px;

}

.reviews h3 {
  margin-bottom: 15px;
  color: #333;

}

.review-card {
  background-color: #fff;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 15px;
}

.review-card p {
  margin-top: 10px;
  color: #333;

}

.review-card .reviewer {
  font-size: 1em;
  font-style: bold;
  color: #333;
}

.review-timestamp {
    font-size: 0.7em;
    color: #777;
    margin-top: 2px;

}

@font-face {
  font-family: monts;
  src: url(fonts/montserrat-regular.ttf);
}
    </style>
</head>
<body>

        <header>
        <a href="../working folder/homepage.html">
            <h1>BITESPOT</h1>
        </a>
        <nav>
        <ul id="nav-links">
            <li><a href="../working folder/homepage.html">HOME</a></li>
            <li><a href="#restaurants">RESTAURANTS</a></li>
            <li><a href="../working folder/about.html">ABOUT</a></li>
        </ul>
    </nav>
    </header>

    <script>
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 10) {
                header.classList.add('shadow');
            } else {
                header.classList.remove('shadow');
            }
        });
    </script>


    <div class="back-button">
        <a href="javascript:void(0);" onclick="history.back()">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <div class="restaurant-container">
        <div class="restaurant">
            <img src="../working folder/pastil.jpg" alt="Restaurant Image">
            <div class="restaurant-info">
                <h3>Chicken Pastil</h3>
                <p class="rating">★★★★★</p>
                <p class="location">
                    <a href="https://maps.app.goo.gl/e5v2JoKxNCFBwDA99" class="restaurant-location" target="_blank">
                        View Location
                    </a>
                </p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

                <div class="actions">
    <button class="like-btn" data-restaurant-id="1">
        <i class="fas fa-heart heart-icon"></i>
        <span class="like-count" id="like-count-1">0</span>
    </button>
    <button><i class="fas fa-share"></i></button>
</div>
</div>
        </div>

        <div class="add-review">
            <button class="add-review-btn" id="addReviewBtn">
                Add Review <span class="plus-sign">+</span>
            </button>
        </div>

        <div id="reviewModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">×</span>
                <div id="modalContentArea">
                    <form id="reviewForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>

                        <label for="review_text">Review:</label>
                        <textarea id="review_text" name="review_text" rows="5" required></textarea>

                        <label for="rating">Rating (1-5 stars):</label>
                        <input type="number" id="rating" name="rating" min="1" max="5" required>

                        <button type="submit" id="submitReview">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="reviews">
    <h3>Reviews:</h3>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='review-card'>";
            echo "<p class='reviewer'>" . $row["username"] . "</p>";
            echo "<p class='review-timestamp'>" . date('F d, Y | h:i: A', strtotime($row["timestamp"])) . "</p>";
            echo "<p>\"" . $row["review_text"] . "\"</p>";
            echo "<p class='rating'>";
            for ($i = 0; $i < $row["rating"]; $i++) {
                echo "★";
            }
            echo "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No reviews yet.</p>";
    }
    $conn->close();
    ?>
</div>
    </div>

    <script>
function handleLike(restaurantId) {
    fetch('update_likes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'restaurant_id': restaurantId,
            'user_id': 1 // Replace with actual user ID
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('like-count-1').textContent = data.count;
            const heartIcon = document.querySelector('.heart-icon');
            if (data.liked) {
                heartIcon.classList.add('liked');
            } else {
                heartIcon.classList.remove('liked');
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}

document.querySelector('.like-btn').addEventListener('click', function() {
    handleLike(1);
});

function setLikeButtonState(restaurantId, likeButton) {
    fetch('check_like_status.php?restaurant_id=' + restaurantId, {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        const heartIcon = document.querySelector('.heart-icon');
        if (data.liked) {
            heartIcon.classList.add('liked');
        } else {
            heartIcon.classList.remove('liked');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

var likeButton = document.querySelector('.like-btn');
setLikeButtonState(1, likeButton);

    var modal = document.getElementById("reviewModal");
    var btn = document.getElementById("addReviewBtn");
    var span = document.getElementsByClassName("close-btn")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function validateForm() {
        var rating = document.getElementById("rating").value;
        if (rating < 1 || rating > 5) {
            alert("Rating must be between 1 and 5.");
            return false;
        }
        return true;
    }

    window.onscroll = function() {
        var nav = document.querySelector('nav ul');
        if (window.pageYOffset > 50) {
            nav.classList.add('shadow');
        } else {
            nav.classList.remove('shadow');
        }
    };

    // Remove the duplicate like button event listener and AJAX request here
</script></body>
</html>