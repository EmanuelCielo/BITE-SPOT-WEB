const modal = document.getElementById("reviewModal");
const addReviewBtn = document.getElementById("addReviewBtn");
const closeBtn = document.querySelector(".close-btn");
const submitReviewBtn = document.getElementById("submitReviewBtn");

addReviewBtn.onclick = function() {
  modal.style.display = "block";
};

closeBtn.onclick = function() {
  modal.style.display = "none";
};

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

submitReviewBtn.onclick = function() {
  const username = document.getElementById("usernameInput").value;
  if (username) {
    alert(`Username: ${username} has submitted a review.`);
    modal.style.display = "none";
    
    
  } else {
    alert("Please enter a username.");
  }
};
