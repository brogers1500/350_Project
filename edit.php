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

    <!-- Connect to Server -->
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

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check which submit button was pressed and insert data
            if (isset($_POST['game_insert'])) {
                echo "game_insert set";
            } else if (isset($_POST['platform_insert'])) {
                echo "<p>platform_insert set</p>";
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    echo "$name";
                    $result = mysqli_query($connection, "SELECT name FROM Platform WHERE name = '$name'");
                    $row = mysqli_fetch_assoc($result);
                    $name2;
                    if (is_null($row)) {
                        if (mysqli_query($connection, "INSERT INTO Platform (name) VALUES ('$name')")) {
                            echo "<p> Inserted platform in database";
                        } else {
                            echo "<p> Error: platform could not be inserted</p>";
                        }
                    } else {
                        echo "<p>Platform already in database</p>";
                    }
                }
            } else if (isset($_POST['developer_insert'])) {
                echo "<p>developer_insert set</p>";
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    echo "$name";
                    $result = mysqli_query($connection, "SELECT name FROM Developer WHERE name = '$name'");
                    $row = mysqli_fetch_assoc($result);
                    $name2;
                    if (is_null($row)) {
                        if (mysqli_query($connection, "INSERT INTO Developer (name) VALUES ('$name')")) {
                            echo "<p> Inserted developer in database";
                        } else {
                            echo "<p> Error: developer could not be inserted</p>";
                        }
                    } else {
                        echo "<p>Developer already in database</p>";
                    }
                }
            } else if (isset($_POST['publisher_insert'])) {
                echo "<p>publisher_insert set</p>";
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    echo "$name";
                    $result = mysqli_query($connection, "SELECT name FROM Publisher WHERE name = '$name'");
                    $row = mysqli_fetch_assoc($result);
                    $name2;
                    if (is_null($row)) {
                        if (mysqli_query($connection, "INSERT INTO Publisher (name) VALUES ('$name')")) {
                            echo "<p> Inserted publisher in database";
                        } else {
                            echo "<p> Error: publisher could not be inserted</p>";
                        }
                    } else {
                        echo "<p>Publisher already in database</p>";
                    }
                }
            } else if (isset($_POST['review_insert'])) {
                echo "review_insert set";
            }
        }
    ?>

    <!-- Multiple forms used to insert or edit each table within database -->
    <!-- Need to figure out how to know which form is being submitted to determine which SQL query to execute.
    name for submit inputs could be used. If submit with name 'game_insert' is pressed, execute query to
    INSERT into Game table. Will need to include Edit and maybe Delete submits later on -->
	<h2>Edit</h2>
    
    <!-- Game Form -->
	<form action="edit.php" method="post">
        <fieldset>
        <legend>Game</legend>
	    <table>
		<tr>
	        <td><label>Title</label></td>
		    <td><input type="text" name="title"><br></td>
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
	    <tr>
		    <td><label>Release Date</label></td>
	        <td><input type="date" name="release"><br></td>
		</tr>
		<tr>
	        <td><label>Singleplayer</label></td>
			<td><input type="checkbox" name="singleplayer"><br></td>
        </tr>
        <tr>
			<td><label>Multiplayer</label></td>
		    <td><input type="checkbox" name="multiplayer"><br></td>
	    </tr>
        <tr>
            <td><input type="submit" name="game_insert" value="Insert"></td>
        </tr>
		</table>
        </fieldset>
	</form>

    <!-- Platform Form -->
    <form action="edit.php" method="post">
        <fieldset>
        <legend>Platform</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name"><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="platform_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Developer Form -->
    <form action="edit.php" method="post">
        <fieldset>
        <legend>Developer</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name"><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="developer_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Publisher Form -->
    <form action="edit.php" method="post">
        <fieldset>
        <legend>Publisher</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name"><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="publisher_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Review Form -->
    <form action="edit.php" method="post">
        <fieldset>
        <legend>Review</legend>
        <table>
        <tr>
            <td><label>Reviewer</label></td>
            <td><input type="text" name="name"><br></td>
        </tr>
        <tr>
            <td><label>Review</label></td>
            <td><input type="text" name="name"><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="review_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>
    </body>

</html>
