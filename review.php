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
         include "credentials.php";
         $servername = "localhost";
         $db = "Arapaima";
         $connection = mysqli_connect($servername, $username, $password, $db);         
         if (mysqli_connect_errno()){
             echo "<p>Failed to connect to the server!</p>";
         }
        ?>
             <h1 class="center">Video Games</h1>
     <div class="button-container">
          <a href="home.php"><button>Home</button></a>
          <a href="search.php"><button>Search</button></a>
          <button class="button disabled"> Review </button>
         <?php
              if (isset($_SESSION['admin'])) {
                  echo "<a href=\"edit.php\"><button>Edit</button></a>";
              }
          ?>
          <a href="login.php"><button>Login</button></a>
 </div>    <h2>Reviews</h2>
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
             mysqli_stmt_bind_param($prepared, "s", $game);
             mysqli_stmt_execute($prepared);
             mysqli_stmt_bind_result($prepared, $gameRow, $reviewerRow, $reviewRow);
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
        }?>
</body>
</html>

