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
                    // Check if platform is already in database
                    $query ="SELECT name FROM Platform WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p>Platform already in database</p>";
                        } else {
                            // Insert platform into database
                            $insert = "INSERT INTO Platform (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p>Platform inserted into database</p>";
                        }
                    } else {
                        echo "<p>Error: Could not submit</p>";
                    }
                }
            } else if (isset($_POST['developer_insert'])) {
                echo "<p>developer_insert set</p>";
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    echo "$name";
                    // Check if developer is already in database
                    $query ="SELECT name FROM Developer WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p>Developer already in database</p>";
                        } else {
                            // Insert developer into database
                            $insert = "INSERT INTO Developer (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p>Developer inserted into database</p>";
                        }
                    } else {
                        echo "<p>Error: Could not submit</p>";
                    }
                }
            } else if (isset($_POST['publisher_insert'])) {
                echo "<p>publisher_insert set</p>";
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    echo "$name";
                    // Check if publisher is already in database
                    $query ="SELECT name FROM Publisher WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p>Publisher already in database</p>";
                        } else {
                            // Insert publisher into database
                            $insert = "INSERT INTO Publisher (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p>Publisher inserted into database</p>";
                        }
                    } else {
                        echo "<p>Error: Could not submit</p>";
                    }
                }
            } else if (isset($_POST['review_insert'])) {
                echo "<p>review_insert set</p>";
                $review;
                $reviewer;
                $game;
                if (isset($_POST['game']) && isset($_POST['review']) && isset($_POST['reviewer'])) {
                    $review = $_POST['review'];
                    $reviewer = $_POST['reviewer'];
                    $game = $_POST['game'];
                    echo "Game = " . $game . " - Review = " . $review . " - Reviewer = " . $reviewer;
                    // Check if game is in database
                    $query = "SELECT id FROM Game WHERE title = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $game);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            mysqli_stmt_close($prepared);
                            $game = $col_id;
                            echo "<p>Game ID = '$game'";
                            // Check if game already has review from reviewer in database
                            $query = "SELECT review, reviewer FROM Review WHERE game = ? AND reviewer = ?";
                            if ($prepared = mysqli_prepare($connection, $query)) {   
                                mysqli_stmt_bind_param($prepared, "is", $game, $reviewer);
                                mysqli_stmt_execute($prepared);
                                mysqli_stmt_bind_result($prepared, $col_review, $col_reviewer);
                                if (mysqli_stmt_fetch($prepared)) {
                                    echo "<p>Review from reviewer for game already in database</p>";
                                } else {
                                    // Insert review into database
                                    mysqli_stmt_close($prepared);
                                    $insert = "INSERT INTO Review (reviewer, review, game) VALUES (?, ?, ?)";
                                    if ($prepared = mysqli_prepare($connection, $insert)) {
                                        mysqli_stmt_bind_param($prepared, "ssi", $reviewer, $review, $game);
                                        mysqli_stmt_execute($prepared);
                                        echo "<p>Review inserted in database</p>";
                                    }
                                }
                            }
                        } else {
                            echo "<p>Game not in database</p>";
                        }
                    }
                }
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
            <td><label>Game</label></td>
            <td><input type="text" name="game"><br></td>
        </tr>
        <tr>
            <td><label>Reviewer</label></td>
            <td><input type="text" name="reviewer"><br></td>
        </tr>
        <tr>
            <td><label>Review</label></td>
            <td><input type="text" name="review"><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="review_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>
    </body>

</html>
