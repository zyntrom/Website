function dashboard() {
    var userRole;
    var username;
    var email;
    var dateandtime;
  
    try {
      fetch('../backend/index.php')
        .then(response => response.json())
        .then(data => {
          
          if (data.role && data.username) {
            userRole = data.role;
            username = data.username;
            email = data.email;
            dateandtime = data.dateandtime;
            updateUI();
            
          } else {
            userRole='';
            window.location.href = '../frontend/login.html';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          window.location.href = '../frontend/login.html'; 
        });
    } catch (error) {
      console.log(error);
      window.location.href = '../frontend/login.html'; 
    }
  
    const adminControls = document.querySelector('.admin-controls');
    const userDetails = document.querySelector('.user-details');
  
    
    adminControls.style.display = 'none';
    userDetails.style.display = 'none';
  
    function updateUI() {
  
      
      if (userRole === 'admin') {
        userDetails.style.display = 'block';
        adminControls.style.display = 'block';  
        
      } else if (userRole === 'user') {
        adminControls.style.display = 'none';
        userDetails.style.display = 'block';  
      } else {
        
        userDetails.style.display = 'none';
        adminControls.style.display = 'none';
      }
  
      
      document.getElementById("user").innerHTML = "User Name : " + username;
      document.getElementById("mail").innerHTML = "Email : " + email;
      document.getElementById("dateandtime").innerHTML = "Registration Time : " + dateandtime;
    }
  
    function addUser() {
      alert('Add User function called');
    }
  
    function removeUser() {
      alert('Remove User function called');
    }
  }
  
  dashboard();