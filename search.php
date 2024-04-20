<?php
    session_start();
?>
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
         <?php
             if (isset($_SESSION['admin'])) {
                 echo "<a href=\"edit.php\"><button>Edit</button></a>";
             }
         ?>
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
            </form>
		</tr>
	    </table>
	</form>
        <table>
        <tr><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Game.title">Title</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Game.is_multiplayer">Is Multiplayer?</a></th><th class="sth"><a href=https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Game.is_singleplayer>Is Singleplayer?</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Developer.name">Developer</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Publisher.name">Publisher</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Platform.name">Platform</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Genre.name">Genre</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Game.rating">ESRB Rating</a></th><th class="sth"><a href="https://cpsc.umw.edu/~asatterl/350_Project/search.php?sort=Game.release_date">Release Date</a></th></tr>
        <?php
            if (!empty($_GET['sort'])){
                $sortBy= " ORDER BY " .$_GET['sort'];
            }
            else{
                $sortBy="";
            }
            $select_all= "SELECT Game.title, Game.is_singleplayer, Game.is_multiplayer, Game.rating, Game.release_date, Genre.name, Platform.name, Developer.name, Publisher.name FROM Game_Genre INNER JOIN Game ON Game.id = Game_Genre.game_id INNER JOIN Genre ON Game_Genre.genre_id = Genre.id INNER JOIN Game_Platform ON Game_Platform.game_id = Game.id INNER JOIN Platform ON Platform.id = Game_Platform.platform_id INNER JOIN Publisher ON Game.publisher = Publisher.id INNER JOIN Developer ON Developer.id = Game.developer".$sortBy;
            if (empty($title)){
            $sql_select = $select_all;
        }
           else {
                $sql_select = $select_all . " WHERE Game.title LIKE ?";
        }
        if($prepared = mysqli_prepare($connection, $sql_select)){
                 if (!empty($title) && empty($dev) && empty($pub) && empty($plat) && empty($genre)){
                        mysqli_stmt_bind_param($prepared, "s", $title);
                    }
                     mysqli_stmt_execute($prepared);
                     mysqli_stmt_bind_result($prepared, $colTitle, $colSing, $colMult, $colRating, $colDate, $colGenre, $colPlat, $colDev, $colPub);         

        }
        while(mysqli_stmt_fetch($prepared)){
                 echo "<tr> <td>" . $colTitle . "</td><td> " .$colMult."</td><td> ".$colSing."</td><td> " . $colDev . "</td><td> " .$colPub . "</td><td> " .$colPlat . "</td><td> " .$colGenre ."</td><td>" . $colRating ."</td><td>" . $colDate ."</td></tr>";
        }
        ?>
        </table>
    <h2>Reviews</h2>
     <form action="" method="post">
 <table>  <td><label>Game</label></td>
 <td colspan="8"><input type="text" name="game" size="117"><br></td>
  </form>
</tr>
</table>
</form>
<table>
<tr><th class="sth">Game</th><th class="sth">Reviewer</th><th class="sth">Review</th></tr>
    <?php
        $sql_select = "SELECT Game.title, Review.reviewer, Review.review FROM Game INNER JOIN Review ON Review.game = Game.id";
         if ($_SERVER["REQUEST_METHOD"] == "POST"){
         if (!empty($_POST['game'])){
            // Add % symbols to both sides of the game title
            $game = '%'.$_POST['game'].'%';
            $sql_select= $sql_select . " WHERE Game.title LIKE ?";
        if($prepared = mysqli_prepare($connection, $sql_select)){
            echo "<p>$sql_select<\p>";
            echo "<p>$game</p>";
             mysqli_stmt_bind_param($prepared, "s", $game);
             mysqli_stmt_execute($prepared);
             mysqli_stmt_bind_result($prepared, $gameRow, $reviewerRow, $reviewRow);
            echo "HERE";
            while(mysqli_stmt_fetch($prepared)){
            echo "<tr><td>". $gameRow . "</td><td>" .$reviewerRow . "</td><td>" .$reviewRow . "</td></tr>";
            }
        }
      }
      }    
         else{
             if($prepared = mysqli_prepare($connection, $sql_select)){
                mysqli_stmt_execute($prepared);
                mysqli_stmt_bind_result($prepared, $gameRow, $reviewerRow, $reviewRow);
                while(mysqli_stmt_fetch($prepared)){
                echo "<tr><td>". $gameRow . "</td><td>" .$reviewerRow . "</td><td>" .$reviewRow . "</td></tr>";

                }
            }
        }
    
    
      //  else{
      // $results = mysqli_query($connection, $sql_select);
        // echo mysqli_num_rows($results);
        // if (mysqli_num_rows($results)==0){
          //  echo "<p>There are no results :(";
        // }}
        // if (mysqli_num_rows($results) > 0) {
          //  while ($row = mysqli_fetch_assoc($results)) {
            //    echo "<tr><td>" . $row['title'] . "</td><td> " . $row['reviewer'    ] . "</td><td>" . $row['review'] . "</td></tr>";
        
        
       // }
       // }
    ?>
</table>
    </body>

</html>
