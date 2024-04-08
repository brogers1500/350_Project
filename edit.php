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
                echo "<p>game_insert set</p>";
                $title = $_POST['title'];
                $release = $_POST['release'];
                $singleplayer = $_POST['singleplayer'];
                $multiplayer = $_POST['multiplayer'];
                $rating = $_POST['rating'];
                $developer = $_POST['developer'];
                $publisher = $_POST['publisher'];
                $genre = $_POST['genre'];
                $platform = $_POST['platform'];

                // Check if game is in database
                $query = "SELECT title FROM Game WHERE title = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $title);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $title = NULL;
                        echo "<p>Game is already in database</p>";
                    }
                    mysqli_stmt_close($prepared);
                }
                // Check if developer is in database
                $query = "SELECT id FROM Developer WHERE name = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $developer);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $developer = $col_id;
                    } else {
                        $developer = NULL;
                    }
                    mysqli_stmt_close($prepared);
                }
                echo "<p>dev id = '$developer'</p>";
                // Check if publisher is in database
                $query = "SELECT id FROM Publisher WHERE name = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $publisher);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $publisher = $col_id;
                    } else {
                        $publisher = NULL;
                    }
                    mysqli_stmt_close($prepared);
                }
                echo "<p>pub id = '$publisher'</p>";
                // Check if genre is in database
                $genre = preg_split('/[\s,]+/', $genre);
                for ($i = 0; $i < count($genre); $i++) {
                    echo "<p>$genre[$i]</p>";
                    $query = "SELECT id FROM Genre WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $genre[$i]);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p>$col_id</p>";
                            $genre[$i] = $col_id;
                        } else {
                            echo "<p>Genre '$genre[$i]' is not in database</p>";
                            $genre = NULL;
                            break;
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
                // Check if platform is in database
                $platform = preg_split('/[\s,]+/', $platform);
                for ($i = 0; $i < count($platform); $i++) {
                    echo "<p>$platform[$i]</p>";
                    $query = "SELECT id FROM Platform WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $platform[$i]);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            $platform[$i] = $col_id;
                        } else {
                            echo "<p>Platform '$platform[i]' is not in database</p>";
                            $platform = NULL;
                            break;
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
                // Set the modes 
                if ($multiplayer == "on") {
                    $multiplayer = 1;
                } else {
                    $multiplayer = 0;
                }
                if ($singleplayer == "on") {
                    $singleplayer = 1;
                } else {
                    $singleplayer = 0;
                }
                echo "<pre>";print_r($genre);echo"</pre>";
                echo "<pre>";print_r($platform);echo"</pre>";
                echo "<p>Title = '$title'<br>Release Date = '$release'<br>Singleplayer = '$singleplayer'<br>Multiplayer = '$multiplayer'<br>Rating = '$rating'<br>Developer = '$developer'<br>Publisher = '$publisher'</p>";
                
                if ($title != NULL && $developer != NULL && $publisher != NULL && $genre != NULL && $platform != NULL) {
                    $game_insert = "INSERT INTO Game (title, release_date, developer, publisher, is_singleplayer, is_multiplayer, rating) VALUES (?, ?, ?, ?, ?, ?, ?)";     
                    if ($prepared = mysqli_prepare($connection, $game_insert)) {
                        mysqli_stmt_bind_param($prepared, "ssiiiis", $title, $release, $developer, $publisher, $singleplayer, $multiplayer, $rating);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_close($prepared);
                    }
                    // Get id of newly inserted game
                    $game_id;
                    $query = "SELECT id FROM Game WHERE title = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $title);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            $game_id = $col_id;
                        }
                    }
                    // Insert into Game_Genre table
                    mysqli_stmt_close($prepared);
                    for ($i = 0; $i < count($genre); $i++) {
                        $insert_genre = "INSERT INTO Game_Genre (game_id, genre_id) VALUES (?, ?)";
                        if ($prepared = mysqli_prepare($connection, $insert_genre)) {
                            mysqli_stmt_bind_param($prepared, "ii", $game_id, $genre[$i]);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_close($prepared);
                        }
                    }
                    // Insert into Game_Platform table
                    for ($i = 0; $i < count($platform); $i++) {
                        $insert_platform = "INSERT INTO Game_Platform (game_id, platform_id) VALUES (?, ?)";
                        if ($prepared = mysqli_prepare($connection, $insert_platform)) {
                            mysqli_stmt_bind_param($prepared, "ii", $game_id, $platform[$i]);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_close($prepared);
                        }
                    }
                    echo "<p>Game inserted into database</p>";
                } else {
                    echo "<p>Game could not be inserted into database</p>";
                }
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
                    // Check if game is in database and get id
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
    <h3>Insert</h3> 
    <!-- Game Form -->
	<form action="edit.php" method="post">
        <fieldset>
        <legend>Game</legend>
	    <table>
		<tr>
	        <td><label>Title</label></td>
		    <td><input type="text" name="title" required><br></td>
		</tr>
		<tr>
		    <td><label>Developer</label></td>
		    <td><input type="text" name="developer" required><br></td>
		</tr>
		<tr>
		    <td><label>Publisher</label></td>
		    <td><input type="text" name="publisher" required><br></td>
		</tr>
		<tr>
	        <td><label>Rating</label></td>
	        <td><input type="text" name="rating" required><br></td>
		</tr>
		<tr>
	        <td><label>Platforms</label></td>
	        <td><input type="text" name="platform" required><br></td>
		</tr>
		<tr>
	        <td><label>Genres</label></td>
	        <td><input type="text" name="genre" required><br></td>
		</tr>
	    <tr>
		    <td><label>Release Date</label></td>
	        <td><input type="date" name="release" required><br></td>
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

    <h3>Update</h3>
	<form action="edit.php" method="post">
        <fieldset>
        <legend>Game</legend>
	    <table>
		<tr>
	        <td><label>Title</label></td>
		    <td><input type="text" name="title" required><br></td>
		</tr>
		<tr>
	        <td><label>New Title</label></td>
		    <td><input type="text" name="new_title"><br></td>
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
            <td><input type="submit" name="game_update" value="Update"></td>
        </tr>
		</table>
        </fieldset>
    </body>

</html>
