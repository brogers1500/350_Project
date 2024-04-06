<!DOCTYPE html>
<html>
    <head>
	<meta charset="utf-8">
	<title>Video Games</title>
	<link rel="stylesheet" href="style.css">
    </head>
    <body>
    <?php
        //Connects to the server.
        include "credentials.php";
        $servername = "localhost";
        $db = "Arapaima";
        $connection = mysqli_connect($servername, $username, $password, $db);
        //It will print an error message if something messes up during the connections.
        if (mysqli_connect_errno()){
            echo "<p>Failed to connect to the server!</p>";
        } 
    ?>
	<h1 class="center">Video Games</h1>
    <div class="button-container">
         <a href="home.php"><button>Home</button></a>
         <button class="button disabled"> Search </button>
         <a href="edit.php"><button>Edit</button></a>
         <a href="login.php"><button>Login</button></a>
</div>
    <?php
        //Will pick out specific search sections.
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            if (!empty($_POST["title"])){
                $title = $_POST["title"];
                //echo $title;
            }
            if (!empty($_POST["developer"])){
                $dev =  $_POST["developer"];
               // echo $dev;
            }
            if (!empty($_POST["publisher"])){
                $pub =  $_POST["publisher"];
                //echo $pub;
            }
            if (!empty($_POST["platform"])){
                $plat =  $_POST["platform"];
                //echo $plat;
            }
            if (!empty($_POST["genre"])){
                $genre =  $_POST["genre"];
                //echo $genre;
            }
        }
    ?>
	<h2>Search</h2>
	 <form action="" method="post">
	    <table>
		<tr>
	    	    <td><label>Title</label></td>
		    <td colspan="8"><input type="text" name="title" size="117"><br></td>
		</tr>
	        <tr>
		    <td><label>Developer</label></td>
		    <td><input type="text" name="developer"><br></td>
		    <td><label>Publisher</label></td>
		    <td><input type="text" name="publisher"><br></td>
	            <td><label>Platforms</label></td>
	            <td><input type="text" name="platform"><br></td>
	            <td><label>Genres</label></td>
	            <td><input type="text" name="genre"><br></td>
	            <td><input type="submit" value="Search"></td>
            </form>
		</tr>
	    </table>
	</form>
        <table>
        <tr><th class="sth">Title</th><th class="sth">Is Multiplayer?</th><th class="sth">Is Singleplayer?</th><th class="sth">Developer</th><th class="sth">Publisher</th><th class="sth">Platform</th><th class="sth">Genre</th><th class="sth">ESRB Rating</th><th class="sth">Release Date</th></tr>
        <?php
            $select_all= "SELECT Game.title, Game.is_singleplayer, Game.is_multiplayer, Game.rating, Game.release_date, Genre.name, Platform.name, Developer.name, Publisher.name FROM Game_Genre INNER JOIN Game ON Game.id = Game_Genre.game_id INNER JOIN Genre ON Game_Genre.genre_id = Genre.id INNER JOIN Game_Platform ON Game_Platform.game_id = Game.id INNER JOIN Platform ON Platform.id = Game_Platform.platform_id INNER JOIN Publisher ON Game.publisher = Publisher.id INNER JOIN Developer ON Developer.id = Game.developer";

            if (empty($title) && empty($dev) && empty($pub) && empty($plat) && empty($genre)){
            $sql_select = $select_all;
        }
           // else if (!empty($title) && empty($dev) && empty($pub) && empty($plat)     && empty($genre)){
           // $sql_select = $select_all . " WHERE Game.title LIKE %".$title."%";
       // }


        if($prepared = mysqli_prepare($connection, $sql_select)){
             mysqli_stmt_execute($prepared);
             mysqli_stmt_bind_result($prepared, $colTitle, $colMult, $colSing, $colRating, $colDate, $colGenre, $colPlat, $colDev, $colPub);

        }
        while(mysqli_stmt_fetch($prepared)){
             echo "<tr> <td>" . $colTitle . "</td><td> " .$colMult."</td><td> ".$colSing."</td><td> " . $colDev . "</td><td> " .$colPub . "</td><td> " .$colPlat . "</td><td> " .$colGenre ."</td><td>" . $colRating ."</td><td>" . $colDate ."</td></tr>";
        }
        ?>
        </table>
    </body>

</html>
