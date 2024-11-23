let a = document.getElementById("loginBtn");
let b = document.getElementById("registerBtn");
let x = document.getElementById("login");
let y = document.getElementById("register");

function myMenuFunction() {
  let i = document.getElementById("navMenu");
  if (i.className === "nav-menu") {
    i.className += " responsive";
  } else {
    i.className = "nav-menu";
  }
}

function login() {
  x.style.left = "50px";
  y.style.right = "-520px";
  a.className += " white-btn";
  b.className = "btn";
  x.style.opacity = 1;
  y.style.opacity = 0;
}
function register() {
  x.style.left = "-510px";
  y.style.right = "50px";
  a.className = "btn";
  b.className += " white-btn";
  x.style.opacity = 0;
  y.style.opacity = 1;
}


function loginn() {
  
  const loginForm = document.getElementById('loginn');
  const formData = new FormData(loginForm);
  
  try{
    fetch('../backend/index.php', {
      method: 'POST',
      body: formData,
    })
      .then(response => response.json()) 
      .then(data => {
        if (data.error === 'WrongPass') {
          alert('Incorrect password. Please try again.');
        } else if (data.error === 'NotFound') {
          alert('User not found. Please register.');
        } else if (data.status === 'success') {
          window.location.href = '../frontend/dashboard.html';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
      });
  
   
    return false;
  }catch(error){
    console.log(error);
  }
  
}


function registerr() {
  const registerForm =document.getElementById('registerr');
  const formData = new FormData(registerForm);

 
  fetch('../backend/index.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.error === 'Duplicate entry') {
        alert('Username or email already exists. Please choose another.');
      } else if (data.error === 'Database error') {
        alert('There was an issue with the registration. Please try again.');
      } else if (data.status === 'success') {
        window.location.href = '../frontend/dashboard.html';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred. Please try again later.');
    });

 
  return false;
}

document.getElementById('loginBtn').addEventListener('click', function() {
  login(); 
});
document.getElementById('registerBtn').addEventListener('click', function() {
  register();
});


document.getElementById('loginn').addEventListener('submit', function(event) {
  event.preventDefault();
  
  loginn();
});

document.getElementById('registerr').addEventListener('submit', function(event) {
  event.preventDefault();
  registerr(); 
});
