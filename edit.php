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
        <button class="button disabled">Edit</button>
        <a href="login.php"><button>Login</button></a>
    </div>

    <?php
        include "credentials.php";
        $servername = "localhost";
        $db = "Arapaima";
        $connection = mysqli_connect($servername, $username, $password, $db);

        if (mysqli_connect_errno()) {
            echo "<p>Failed to connect to the server</p>";
        } else {
            echo "<p>Connected to the server</p>";
        }
    ?>

	<h2>Edit</h2>
	<form action="edit.php" method="post">
	    <table>
		<tr>
	        <td><label>Title</label></td>
		    <td><input type="text" name="title"><br></td>
		</tr>
	    <tr>
		    <td><label>Release Date</label></td>
	        <td><input type="text" name="release"><br></td>
		</tr>
		<tr>
		    <td><label>Developer</label></td>
		    <td><input type="text" name="developer"><br></td>
		</tr>
		<tr>
		    <td><label>Publisher</label></td>
		    <td><input type="text" name="publisher"><br></td>
		</tr>
		<tr>
	        <td><label>Rating</label></td>
	        <td><input type="text" name="rating"><br></td>
		</tr>
		<tr>
	        <td><label>Platforms</label></td>
	        <td><input type="text" name="platform"><br></td>
		</tr>
		<tr>
	        <td><label>Genres</label></td>
	        <td><input type="text" name="genre"><br></td>
		</tr>
        </table>
	    <fieldset>
		<legend>Modes</legend>
		    <table>
			<tr>
	            <td><label>Singleplayer</label></td>
			    <td><input type="checkbox" name="singleplayer"><br></td>
			    <td><label>Multiplayer</label></td>
		        <td><input type="checkbox" name="multiplayer"><br></td>
			</tr>
		    </table>
	    </fieldset>

	    <input type="submit" value="Insert">
	</form>
    </body>

</html>
