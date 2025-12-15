<!DOCTYPE html>
<html>

<head>
  <link href="css/style.css" rel="stylesheet" type="text/css">
   <link href="css/form.css" rel="stylesheet" type="text/css">
     <title>Log In</title>
    <style>

        #alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
            margin-bottom: 0;
            z-index: 10;
        }
    </style>
</head>

<body>
  <div class="background-circle circle1"></div>
  <div class="background-circle circle2"></div>
  <div class="background-circle circle3"></div>
  
   <?php
    // Display the success message if the "success" query parameter is present in the URL
    if (isset($_GET["success"]) && $_GET["success"] == "0") {
        echo '
        <div id="alert">
            <strong>Unsuccessful!</strong> Invalid username or password.</div>';
    }
    ?>
  
  <div class="card">

    <form action="login_process.php" method="post">
      <img src="person.png" alt="Avatar" class="avatar" style="height: 150px; width: 150px;">
      <h1>LOGIN</h1>
      
      <label for="uname">User Name</label>
      <input type="text" name="username" id="uname" placeholder="User Name" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Password" required>

      <button type="submit">Login</button>

    </form>
 
  </div>
  <script>
      const myDiv = document.getElementById("alert");
        function makeDivDisappear() {
            myDiv.style.display = "none";
        }

        setTimeout(makeDivDisappear, 10000);
  </script>
  
  
</body>

</html>