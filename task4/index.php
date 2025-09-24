<?php   session_start();  ?>

<html>
  <head>
       <title> Home </title>
  </head>
  <body>
<?php
      if(!isset($_SESSION['use'])) 
       {
           header("Location:login.php");  
       }

          echo $_SESSION['use'] .'<br>';

          echo "Login Success";

          echo "<a href='logout.php'> Logout</a> "; 
?>
</body>
</html>