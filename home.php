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
	<h1 class="center">Video Games</h1>
     <div class="button-container">
         <button class="button disabled">Home</button>
         <a href="search.php"><button> Search </button></a>
         <?php
             if (isset($_SESSION['admin'])) {
                 echo "<a href=\"edit.php\"><button>Edit</button></a>";
             }
         ?>
         <a href="login.php"><button>Login</button></a>
 </div>

	<h2>About</h2>
     <table>
        <td>
        <p>     Video games are a popular pastime all around the world. Have you ever wanted to have all your games in one convenient, easy to search place? Team Arapaima has you covered! With our database, you can search through various popular games to find any information you could want on them!</p>
    <p>     All thanks to a database and edit feature assembled by Brandon Rogers, and a search feature assembled my Marie Satterly, finding games of any specific type you want couldn't be easier. Multiplayer only? You got it! Games published by Capcom? You bet we can find you those. And don't even forget the convient search feature to be found below!<p>
        <p> NOTE: Editing is not accessable without admin permissions. To log on through an admin account, either speak to Brandon Rogers in class, or send an email to thisIsNotReal@fillertext.com!
        </td>
        <td>
            <img src="https://stopxwhispering.files.wordpress.com/2013/07/shelf-closeup-castlevania.jpg" alt="Games on a shelf">
        </td>
    </table>
	<h2>How to Use</h2>
    <table>
    <td>
        <img src="SearchImage.jpg" alt="Someone searching the game title 'Project Diva' in the search page.">

    </td>
    <td>
        <p>Use is simple! Click the ‘SEARCH’ tab at the top of the screen, and it will take you to a table. Type in the title you want, and you’re in business! Tip: Click on the titles of the rows to sort by them! (Does not apply to genre and platform)
</p>
        <p>
        In order to search for reviews of games, you should instead click the "REVIEWS" button. The search here is the same, but it instead lets you know what various reviewers thought of games, how fun!"
        </p>
    </td>
    </table>
	<h2>Check Out This Random Game!</h2>
    </body>

</html>
