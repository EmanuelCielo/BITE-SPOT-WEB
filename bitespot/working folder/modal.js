const modal = document.getElementById("reviewModal");
const addReviewBtn = document.getElementById("addReviewBtn");
const closeBtn = document.querySelector(".close-btn");
const reviewsContainer = document.querySelector(".reviews"); // Get the reviews container

addReviewBtn.onclick = function() {
    modal.style.display = "block";
    document.getElementById("modalContentArea").innerHTML = `
        <h3>Enter your username to add a review</h3>
        <input type="text" id="usernameInput" placeholder="Enter your username">
        <button id="submitUsernameBtn">Submit</button>
    `;
    document.getElementById("submitUsernameBtn").addEventListener("click", handleUsernameSubmit);
};

closeBtn.onclick = function() {
    modal.style.display = "none";
};

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

function handleUsernameSubmit() {
  console.log("handleUsernameSubmit called");
  const username = document.getElementById("usernameInput").value;
  console.log("Username:", username);
  if (username) {
      document.getElementById("modalContentArea").innerHTML = `
          <h3>Add a Review</h3>
          <form id="reviewForm">
              <input type="hidden" name="username" value="${username}">
              <label for="review_text">Review:</label><br>
              <textarea id="review_text" name="review_text"></textarea><br><br>
              <label>Rating:</label><br>
              <div class="star-rating">
                  <input type="radio" id="star5" name="rating" value="5" /><label for="star5">★</label>
                  <input type="radio" id="star4" name="rating" value="4" /><label for="star4">★</label>
                  <input type="radio" id="star3" name="rating" value="3" /><label for="star3">★</label>
                  <input type="radio" id="star2" name="rating" value="2" /><label for="star2">★</label>
                  <input type="radio" id="star1" name="rating" value="1" /><label for="star1">★</label>
              </div><br><br>
              <button type="button" id="submitReview">Submit Review</button>
          </form>
      `;
      console.log("Form inserted");
      document.getElementById("submitReview").addEventListener("click", submitReview);
      console.log("submitReview listener added");
  } else {
      alert("Please enter a username.");
  }
}



function submitReview() {
    const form = document.getElementById("reviewForm");
    const formData = new FormData(form);

    fetch('erros.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse response as JSON
    .then(data => {
        if (data.success) {
            // Create a new review card
            const newReviewCard = document.createElement("div");
            newReviewCard.classList.add("review-card");
            newReviewCard.innerHTML = `
                <p><strong>${data.username}</strong>: ${data.review_text}<br>
                Rating: ${data.rating}<br>
                Date: ${data.timestamp}</p>
            `;

            // Insert the new review at the beginning of the reviews container
            reviewsContainer.insertBefore(newReviewCard, reviewsContainer.firstChild.nextSibling);

            alert("Review added successfully!");
            modal.style.display = "none";
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred.");
    });
}