function openOverlay() {
    document.getElementById("overlay").style.display = "flex";
}

function closeOverlay() {
    document.getElementById("overlay").style.display = "none";
}

const sleep = (delay) => new Promise((resolve) => setTimeout(resolve, delay))
        

function openProfileOverlay() {
  if(document.getElementById("profileOverlay").style.display == "none")
      document.getElementById("profileOverlay").style.display = "flex";
  else
      document.getElementById("profileOverlay").style.display = "none";
}

function selAccount(accountType) {
    if (accountType === 'customer') {
      // redirectToRegistrationPage(accountType) 
      window.location.href = 'signUp/registerCustomer.html';
    } else if (accountType === 'rentalAgency') {
      // redirectToRegistrationPage(accountType)
      window.location.href = 'signUp/registerAgency.html';
    }
  // alert("Selected account type: " + accountType);
  // closeOverlay(); // Close the overlay after selection (You can perform further actions here)
}





document.getElementById("registrationForm").addEventListener("submit", function(event) {
  event.preventDefault();

  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("confirmPassword").value;
  var passwordError = document.getElementById("passwordError");

  if (password !== confirmPassword) {
      passwordError.style.display = "inline";
  } else {
      passwordError.style.display = "none";
      // Here, you might proceed with form submission since passwords match
      // For example: this.submit();
  }
});



document.getElementById("searchForm").addEventListener("submit", function (event) {
  event.preventDefault();
  var searchValue = document.getElementById("searchInput").value;
  // Perform AJAX call based on search criteria
  // Update the UI with search results
});



// Function to fetch data using AJAX
function fetchDataFromUsersTable() {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
          var usersData = JSON.parse(this.responseText);
          // Process the retrieved data
          // E.g., display data in HTML elements
      }
  };
  xhr.open("GET", "getUsers.php", true);
  xhr.send();
}







// document.getElementById("register_Btn").addEventListener("click", function(event) {
//   event.preventDefault();
//   openOverlay();
// });


// asdasdasda
// function redirectToRegistrationPage(accountType) {
//   // Create a form element
//   const form = document.createElement('form');
//   form.method = 'POST';
//   form.action = 'register.html'; // Your registration page URL

//   // Create an input field to send account type as POST data
//   const input = document.createElement('input');
//   input.type = 'hidden';
//   input.name = 'type';
//   input.value = accountType;

//   // Append the input field to the form
//   form.appendChild(input);

//   // Append the form to the body and submit it
//   document.body.appendChild(form);
//   form.submit();
// }


// asdasdasdad


// document.addEventListener("DOMContentLoaded", function() {
//   const params = new URLSearchParams(window.location.search);
//   const accountType = params.get('type');

//   if (accountType === 'rentalAgency') {
//       const rentalAgencyFields = document.getElementById("rentalAgencyFields");
//       rentalAgencyFields.style.display = "block";
//   }
// });