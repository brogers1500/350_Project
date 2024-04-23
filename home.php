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
         <a href="review.php"><button>Reviews</button></a>
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
    <table><tr><td>
    <?php
        include "credentials.php";
         $servername = "localhost";
         $db = "Arapaima";
         $connection = mysqli_connect($servername, $username, $password, $db);
         if (mysqli_connect_errno()) {
             echo "<p class=\"red\">Failed to connect to the server</p>";
         } else {
 $query = "SELECT Game.title, Game.release_date, Game.rating, Publisher.name, Developer.name, Game.is_multiplayer, Game.is_singleplayer, Platform.id FROM Game INNER JOIN Developer ON Game.developer = Developer.id INNER JOIN Publisher ON Game.publisher = Publisher.id INNER JOIN Game_Platform ON Game.id = Game_Platform.game_id INNER JOIN Platform ON Game_Platform.platform_id = Platform.id ORDER BY RAND() LIMIT 1";
                 if ($prepared = mysqli_prepare($connection, $query)) {
                     mysqli_stmt_execute($prepared);
                     mysqli_stmt_bind_result($prepared, $tit1e, $date, $rating, $publisher, $dev, $mult, $sing, $platform);
                }
            while(mysqli_stmt_fetch($prepared)){
            echo "<p>" . $tit1e." is a video game developed by ".  $dev ." and published by " .$publisher." on ".$date.". It has a rating of ".$rating.".</p>";
            if ($mult && !$sing){
                echo "<p> It is playable in multiplayer only.</p>";
            }
            else if ($mult && $sing){
                echo "<p> It is playable in singleplayer and multiplayer.</p>";
            }
            else if (!$mult && $sing){
                echo "<p> It is playable in sigleplayer only.</p>";
            }
            else{
                echo "<p> The data must be incomplete, because it says it cannot be played in singleplayer or multiplayer! </p>";
            }
        
        echo " </td><td>";
        if ($platform == 1){
            echo "<img src=\"https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.denofgeek.com%2Fgames%2Flast-nes-game-ever-released-official-unofficial%2F&psig=AOvVaw0ErwkICSa8xUSY7V6dTAgy&ust=1713998241026000&source=images&cd=vfe&opi=89978449&ved=0CBIQjRxqFwoTCKDBkJSz2YUDFQAAAAAdAAAAABAE\" alt=\"An image of a NES console\">";
        }
        else if ($platform == 2){
            echo "<img src=\"\" alt=\"An image of a SNES console.\">";
        }
        else if ($platform == 3){
            echo "<img src=\"https://cdn.mos.cms.futurecdn.net/ZMnrzNes7GgEDpCHJuwvE6-1200-80.jpg\" alt=\"An image of a N64 console.\">";
        }
        else if ($platform == 4){
            echo "<img src=\"https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.timeextension.com%2Fnews%2F2023%2F07%2Frandom-the-gamecubes-lid-holds-a-secret-but-did-you-know-about-it&psig=AOvVaw3qcLuj6ODH6nIT9oskgTfY&ust=1713998444049000&source=images&cd=vfe&opi=89978449&ved=0CBIQjRxqFwoTCMiH2fmz2YUDFQAAAAAdAAAAABAE\" alt=\"An image of a Gamecube console.\">";
        }
        else if ($platform ==5){
            echo "<img src=\https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTEi1jyIT6LGTfnduI1OOk8Hjistdnpp0gLhTNa2Ir56w&s\" alt=\"An image of a DS console.\">";
        }
        else if ($platform ==6){
            echo "<img src=\https://s.yimg.com/ny/api/res/1.2/C1GDAH1ncRuT5uj8meJ3sQ--/YXBwaWQ9aGlnaGxhbmRlcjt3PTY0MDtoPTQyMA--/https://o.aolcdn.com/hss/storage/midas/99d8ab02f24a6349fdae9f1220e1f92c/203606336/ninte.jpg\" alt=\"An image of a 3DS console.\">";
        }
        else if ($platform ==7){
            echo "<img src=\https://hard-drive.net/wp-content/uploads/2022/03/wii.jpg.webp\" alt=\"An image of a Wii console.\">";

        }
        else if ($platform ==8){
            echo "<img src=\"https://static0.gamerantimages.com/wordpress/wp-content/uploads/2023/10/wii-u-official-video-overview.jpg\" alt=\"An image of a Wii U console.\">";

        }
        else if ($platform ==9){
            echo "<img src=\"https://media-cldnry.s-nbcnews.com/image/upload/t_fit-760w,f_auto,q_auto:best/rockcms/2023-11/231117-nintendo-bd-main-da8ffe.jpg\" alt=\"An image of a Switch console.\">";

        }
        else if ($platform ==10){
            echo "<img src=\"https://ae01.alicdn.com/kf/A30ec7bc9b4fb41f0a98d848752ac5d6aC/Original-PS1-with-psio-installed-Unable-to-read-the-CD-ROM-drive-Memory-card-not-included.jpg\" alt=\"An image of a PS1 console.\">";
    
        }
        else if ($platform ==11){
            echo "<img src=\"https://www.cnet.com/a/img/resize/6ebe54fdf33cbfa88aeb344ed044b82b81c2fe40/hub/2014/07/28/34c579b3-48d0-41df-899a-16c542abe508/ait-ps2-promo-1.jpg?auto=webp&width=768\" alt=\"An image of a PS2 console.\">";

        }
        else if ($platform ==12){
            echo "<img src=\"https://www.cnet.com/a/img/resize/cd5feeaca37e1b6138fd3cea573c8a5a407c586e/hub/2012/09/27/3fc673fd-4534-11e3-8bb1-14feb5ca9861/Super_Slim_PS3_35454664_35454666_35454667_35454668_18.jpg?auto=webp&fit=crop&height=900&width=1200\" alt=\"An image of a PS3 console.\">";

        }
        else if ($platform ==13){
            echo "<img src=\"https://media-cldnry.s-nbcnews.com/image/upload/t_fit-1240w,f_auto,q_auto:best/msnbc/Components/Photos/050725/050725_psp_hmed_11a.jpg\" alt=\"An image of a PSP console.\">";

        }
        else if ($platform ==14){
            echo "<img src=\"https://media.wired.com/photos/5933348b714b881cb296a2e3/master/pass/MG_1293.jpeg\" alt=\"An image of a PS Vita console.\">";

        }
        else if ($platform ==15){
            echo "<img src=\"https://www.cnet.com/a/img/hub/2016/10/20/ba0fed19-6997-4851-a0d9-b4c0c52c1c87/playstation-4-pro-ps4-010.jpg\" alt=\"An image of a PS4 Console.\">";

        }
        else if ($platform ==16){
            echo "<img src=\"https://cdn.vox-cdn.com/thumbor/oxuFAw2x5UfGYxvTJUtLoVJVb0M=/0x0:2040x1360/1400x1050/filters:focal(1020x680:1021x681)/cdn.vox-cdn.com/uploads/chorus_asset/file/23194032/akrales_220124_4964_0009.jpg\" alt=\"An image of a XBox console.\">";

        }
        else if ($platform ==17){
            echo "<img src=\"https://cdn.vox-cdn.com/thumbor/70iX4DqE3VlI7enSEph0dAPy8Pw=/0x0:1020x680/1400x1050/filters:focal(510x340:511x341)/cdn.vox-cdn.com/assets/1197762/DSC04414.jpg\" alt=\"An image of a XBox 360 console.\">";

        }
        else if ($platform ==18){
            echo "<img src=\"https://cdn.mos.cms.futurecdn.net/bc672016e619f59de51d7382448cc1f6-1200-80.jpg\" alt=\"An image of a. XBox 1 console\">";

        }
        else if ($platform ==19){
            echo "<img src=\"https://cdn.mos.cms.futurecdn.net/aDtgYbdfaiyMEa4izpeyk3-1200-80.jpg\" alt=\"An image of a XBox Series XS console.\">";

        }
        else if ($platform ==20){
            echo "<img src=\"https://images.timeextension.com/89c577625c60c/sega-saturn.large.jpg\" alt=\"An image of a Sega Saturn console.\">";

        }
        else if ($platform ==21){
            echo "<img src=\"https://cdn.mos.cms.futurecdn.net/oTZrV3efiUoftU9xqVnnhk.jpg\" alt=\"An image of a Dreamcase console.\">";

        }
        else if ($platform ==22){
            echo "<img src=\"https://bandzoogle.com/files/2238/bzblog-20-ways-optimize-win10-pc-production-img01.jpg\" alt=\"An image of a computer running Windows\">";

        }
        else if ($platform ==23){
            echo "<img src=\"https://res.cloudinary.com/canonical/image/fetch/f_auto,q_auto,fl_sanitize,e_sharpen,c_fill,w_555,h_311/https://ubuntu.com/wp-content/uploads/841d/P920-thinkstation-lifestyle_Ubuntu_JPGmax.jpg\" alt=\"An image of a computer running Linux.\">";

        }
        else if ($platform ==24){
            echo "<img src=\"https://techcrunch.com/wp-content/uploads/2016/09/macos-sierra2.jpg\" alt=\"An image of a computer running macOS.\">";

        }
        else if ($platform ==25){
            echo "<img src=\"\https://fscl01.fonpit.de/userfiles/4376948/image/AndroidPIT-best-androdi-games-1.jpg\" alt=\"An image of an Android mobile device.\">";

        }
        else if ($platform ==26){
            echo "<img src=\"https://www.digitaltrends.com/wp-content/uploads/2022/10/iPhone-14-Pro-Gaming.jpg?p=1\" alt=\"An image of an iOS mobile device.\">";

        }
        else if ($platform ==29){
            echo "<img src=\"https://www.cnet.com/a/img/resize/bebef835df90640f9aa2e4a2f2a2699cf53a301f/hub/2020/10/26/b60bfe6f-3193-4381-b0d4-ac628cdcc565/img-1419.jpg?auto=webp&width=1200\" alt=\"An image of a PS5 console.\">";

        }
        else{
            echo "<img src=\"https://img1.cgtrader.com/items/2239022/3b56dbbb48/large/generic-video-game-console-3d-model-max-obj-fbx.jpg\" alt=\"An image of a generic game console.\">";

        }
    }}
    ?>
    </td></tr>
    
    </table>
    </body>

</html>
