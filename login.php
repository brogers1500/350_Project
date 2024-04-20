<?php
    session_start();
    if (isset($_SESSION['admin'])) {
        echo "<p>admin is logged in</p>";
    }
?>
<!DOCTYPE html>
<html>
    <head>
	<meta charset="utf-8">
	<title>Video Games</title>
	<link rel="stylesheet" href="style.css">
    </head>
    <body>
	<h1 class="center">Video Games</h1>
    <div class="button-container">
        <a href="home.php"><button>Home</button></a>
        <a href="search.php"><button>Search</button></a>
        <?php
           if (isset($_SESSION['admin'])) {
               echo "<a href=\"edit.php\"><button>Edit</button></a>";
           }
        ?>
        <button class="button disabled">Login</button>
        </div>
	<h2>Login</h2>

    <?php
        include "credentials.php";
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $user = $_POST['username'];
            $pass = $_POST['password'];
            if ($user == $admin_user && $pass == $admin_pass) {
                $_SESSION['admin'] = true;
                header("Location: login.php");
            } else {
            }
        } else if (isset($_POST['logout'])) {
            session_destroy();
            header("Location: login.php");
        }
    ?>
    
    <?php
    if (isset($_SESSION['admin'])) {
        echo "<form action=\"login.php\" method=\"post\">";
        echo "<input type=\"submit\" name=\"logout\" value=\"Logout\">";
        echo "</form>";
    } else {
	    echo "<form action=\"login.php\" method=\"post\">";
	    echo "<table>";
		echo "<tr>";
		echo "<td><label>Username</label></td>";
		echo "<td><input type=\"text\" name=\"username\" required><br></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><label>Password</label></td>";
	    echo "<td><input type=\"password\" name=\"password\" required><br></td>";
        echo "</tr>";
	    echo "</table>";
	    echo "<input type=\"submit\" value=\"Login\">"; 
        echo "</form>";
      
    }
    ?>

    </body>

</html>
