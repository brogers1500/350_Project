<?php
    session_start();
    if (!isset($_SESSION['admin'])) {
        // Redirect user if not logged in
        header("Location: login.php");
        //exit;
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
         <a href="review.php"><button>Reviews</button></a>
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
            echo "<p class=\"red\">Failed to connect to the server</p>";
        } else {
            echo "<p class=\"green\">Connected to the server</p></p>";
        }


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check which submit button was pressed and insert data
            if (isset($_POST['game_insert'])) {
                $title = $_POST['title'];
                $release = $_POST['release'];
                // Set modes
                if (isset($_POST['singleplayer'])) {
                    $singleplayer = 1;
                } else {
                    $singleplayer = 0;
                }
                if (isset($_POST['multiplayer'])) {
                    $multiplayer = 1;
                } else {
                    $multiplayer = 0;
                }
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
                        echo "<p class=\"red\">Game is already in database</p>";
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
                        echo "<p class=\"red\">Developer is not in database</p>";
                    }
                    mysqli_stmt_close($prepared);
                }
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
                        echo "<p class=\"red\">Publisher is not in database</p>";
                    }
                    mysqli_stmt_close($prepared);
                }
                // Check if genre is in database
                $genre = preg_split('/,+\s*/', $genre);
                for ($i = 0; $i < count($genre); $i++) {
                    $query = "SELECT id FROM Genre WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $genre[$i]);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            $genre[$i] = $col_id;
                        } else {
                            echo "<p class=\"red\">Genre '$genre[$i]' is not in database</p>";
                            $genre = NULL;
                            break;
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
                // Check if platform is in database
                $platform = preg_split('/,+\s*/', $platform);
                for ($i = 0; $i < count($platform); $i++) {
                    $query = "SELECT id FROM Platform WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $platform[$i]);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            $platform[$i] = $col_id;
                        } else {
                            echo "<p class=\"red\">Platform '$platform[$i]' is not in database</p>";
                            $platform = NULL;
                            break;
                        }
                        mysqli_stmt_close($prepared);
                    }
                }

                // If no value is null/ insert to database 
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
                    echo "<p class=\"green\">Game inserted into database</p>";
                } else {
                    echo "<p class=\"red\">Game could not be inserted into database</p>";
                }
            } else if (isset($_POST['platform_insert'])) {
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    // Check if platform is already in database
                    $query ="SELECT name FROM Platform WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p class=\"red\">Platform already in database</p>";
                        } else {
                            // Insert platform into database
                            $insert = "INSERT INTO Platform (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p class=\"green\">Platform inserted into database</p>";
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
            } else if (isset($_POST['developer_insert'])) {
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    // Check if developer is already in database
                    $query ="SELECT name FROM Developer WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p class=\"red\">Developer already in database</p>";
                        } else {
                            // Insert developer into database
                            $insert = "INSERT INTO Developer (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p class=\"green\">Developer inserted into database</p>";
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
            } else if (isset($_POST['publisher_insert'])) {
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    // Check if publisher is already in database
                    $query ="SELECT name FROM Publisher WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p class=\"red\">Publisher already in database</p>";
                        } else {
                            // Insert publisher into database
                            $insert = "INSERT INTO Publisher (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p class=\"green\">Publisher inserted into database</p>";
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
            } else if (isset($_POST['genre_insert'])) {
                $name;
                if (isset($_POST['name'])) {
                    $name = $_POST['name'];
                    // Check if platform is already in database
                    $query ="SELECT name FROM Genre WHERE name = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $name);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_name);
                        if (mysqli_stmt_fetch($prepared)) {
                            echo "<p class=\"red\">Genre already in database</p>";
                        } else {
                            // Insert genre into database
                            $insert = "INSERT INTO Genre (name) VALUES (?)";
                            if ($prepared = mysqli_prepare($connection, $insert)) {
                                mysqli_stmt_bind_param($prepared, "s", $name);
                                mysqli_stmt_execute($prepared);
                            }
                            echo "<p class=\"green\">Genre inserted into database</p>";
                        }
                        mysqli_stmt_close($prepared);
                    }
                }
            } else if (isset($_POST['review_insert'])) {
                $review;
                $reviewer;
                $game;
                if (isset($_POST['game']) && isset($_POST['review']) && isset($_POST['reviewer'])) {
                    $review = $_POST['review'];
                    $reviewer = $_POST['reviewer'];
                    $game = $_POST['game'];
                    // Check if game is in database and get id
                    $query = "SELECT id FROM Game WHERE title = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $game);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            mysqli_stmt_close($prepared);
                            $game = $col_id;
                            // Check if game already has review from reviewer in database
                            $query = "SELECT review, reviewer FROM Review WHERE game = ? AND reviewer = ?";
                            if ($prepared = mysqli_prepare($connection, $query)) {   
                                mysqli_stmt_bind_param($prepared, "is", $game, $reviewer);
                                mysqli_stmt_execute($prepared);
                                mysqli_stmt_bind_result($prepared, $col_review, $col_reviewer);
                                if (mysqli_stmt_fetch($prepared)) {
                                    echo "<p class=\"red\">Review from reviewer for game already in database</p>";
                                    mysqli_stmt_close($prepared);
                                } else {
                                    // Insert review into database
                                    mysqli_stmt_close($prepared);
                                    $insert = "INSERT INTO Review (reviewer, review, game) VALUES (?, ?, ?)";
                                    if ($prepared = mysqli_prepare($connection, $insert)) {
                                        mysqli_stmt_bind_param($prepared, "ssi", $reviewer, $review, $game);
                                        mysqli_stmt_execute($prepared);
                                        echo "<p class=\"green\">Review inserted in database</p>";
                                    }
                                }
                            }
                        } else {
                            echo "<p class=\"red\">Game not in database</p>";
                        }
                    }
                }
            } else if (isset($_POST['game_update'])) {
                $developer = $_POST['developer'];
                $publisher = $_POST['publisher'];
                $platform = $_POST['platform'];

                if (isset($_POST['title'])) {
                    $title = $_POST['title'];
                    // Update developer
                    if (isset($_POST['developer']) && !empty($developer)) {
                        $developer = $_POST['developer'];
                        // Get developer id
                        $query = "SELECT id FROM Developer WHERE name = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $developer);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $developer = $col_id;
                                // Set new developer
                                $developer_update = "UPDATE Game SET developer = ? WHERE title = ?";
                                if ($prepared = mysqli_prepare($connection, $developer_update)) {
                                    mysqli_stmt_bind_param($prepared, "is", $developer, $title);
                                    mysqli_stmt_execute($prepared);
                                    echo "<p class=\"green\">New developer was set</p>";
                                }
                            } else {
                                echo "<p class=\"red\">New developer is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    }
        
                    // Update publisher
                    if (isset($_POST['publisher']) && !empty($publisher)) {
                        $publisher = $_POST['publisher'];
                        // Get publisher id
                        $query = "SELECT id FROM Publisher WHERE name = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $publisher);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $publisher = $col_id;
                                // Set new publisher
                                $publisher_update = "UPDATE Game SET publisher = ? WHERE title = ?";
                                if ($prepared = mysqli_prepare($connection, $publisher_update)) {
                                    mysqli_stmt_bind_param($prepared, "is", $publisher, $title);
                                    mysqli_stmt_execute($prepared);
                                    echo "<p class=\"green\">New publisher was set</p>";
                                }
                            } else {
                                echo "<p class=\"red\">New publisher is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    } 

                    // Update rating
                    if (isset($_POST['rating']) && !empty($_POST['rating'])) {
                        $rating = strtoupper($_POST['rating']);
                        if ($rating === "E" || $rating === "E10" || $rating === "T" || $rating === "M") {
                            $rating_update = "UPDATE Game SET rating = ? WHERE title = ?";
                            if ($prepared = mysqli_prepare($connection, $rating_update)) {
                                mysqli_stmt_bind_param($prepared, "ss", $rating, $title);
                                mysqli_stmt_execute($prepared);
                                mysqli_stmt_close($prepared);
                            }
                            echo "<p class=\"green\">Rating updated</p>";
                        } else {
                            echo "<p class=\"red\">New rating is not valid. Enter E, E10, T, or M.</p>";
                        }
                    }

                    // Update release date
                    if (isset($_POST['release']) && !empty($_POST['release'])) {
                        $release = $_POST['release'];
                        $release_update = "UPDATE Game SET release_date = ? WHERE title = ?";
                        if ($prepared = mysqli_prepare($connection, $release_update)) {
                            mysqli_stmt_bind_param($prepared, "ss", $release, $title);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_close($prepared);
                        }
                        echo "<p class=\"green\">Release date was set to $release</p>";
                    }
                   
                    // Update genre 
                    // Check if genre is in database
                    if (isset($_POST['genre']) && !empty($_POST['genre'])) {
                        $genre = $_POST['genre'];
                        $genre = preg_split('/[\s,]+/', $genre);
                        for ($i = 0; $i < count($genre); $i++) {
                            $query = "SELECT id FROM Genre WHERE name = ?";
                            if ($prepared = mysqli_prepare($connection, $query)) {
                                mysqli_stmt_bind_param($prepared, "s", $genre[$i]);
                                mysqli_stmt_execute($prepared);
                                mysqli_stmt_bind_result($prepared, $col_id);
                                if (mysqli_stmt_fetch($prepared)) {
                                    $genre[$i] = $col_id;
                                }
                            } else {
                                echo "<p class=\"red\">Genre '$genre[$i]' is not in database</p>";
                                $genre = NULL;
                                break;
                            }
                            mysqli_stmt_close($prepared);
                        }
                        // Get game id
                        $game_id;
                        $query = "SELECT id FROM Game WHERE title = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $title);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $game_id = $col_id;
                                $num_rows;
                                $delete = "DELETE FROM Game_Genre WHERE game_id = $game_id";
                                $result = mysqli_query($connection, $delete);
                                for ($i = 0; $i < count($genre); $i++) {
                                    $insert = "INSERT INTO Game_Genre (game_id, genre_id) VALUES ($game_id, $genre[$i])";
                                    $result = mysqli_query($connection, $insert);
                                }
                            }
                        } 
                    }

                    // Update platform
                    if (isset($_POST['platform']) && !empty($_POST['platform'])) {
                        $platform = $_POST['platform'];
                        $platform = preg_split('/[\s,]+/', $platform);
                        for ($i = 0; $i < count($platform); $i++) {
                            $query = "SELECT id FROM Platform WHERE name = ?";
                            if ($prepared = mysqli_prepare($connection, $query)) {
                                mysqli_stmt_bind_param($prepared, "s", $platform[$i]);
                                mysqli_stmt_execute($prepared);
                                mysqli_stmt_bind_result($prepared, $col_id);
                                if (mysqli_stmt_fetch($prepared)) {
                                    $platform[$i] = $col_id;
                                }
                            } else {
                                echo "<p class=\"red\">Genre '$platform[$i]' is not in database</p>";
                                $platform = NULL;
                                break;
                            }
                            mysqli_stmt_close($prepared);
                        }
                        // Get game id
                        $game_id;
                        $query = "SELECT id FROM Game WHERE title = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $title);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $game_id = $col_id;
                                $delete = "DELETE FROM Game_Platform WHERE game_id = $game_id";
                                $result = mysqli_query($connection, $delete);
                                for ($i = 0; $i < count($platform); $i++) {
                                    $insert = "INSERT INTO Game_Platform (game_id, platform_id) VALUES ($game_id, $platform[$i])";
                                    $result = mysqli_query($connection, $insert);
                                }
                            }
                        } 
                    }
                
                    // Update singleplayer
                    if (isset($_POST['singleplayer']) && is_numeric($_POST['singleplayer'])) {
                        $singleplayer = $_POST['singleplayer'];
                        $singleplayer_update = "UPDATE Game SET is_singleplayer = ? WHERE title = ?";
                        if ($prepared = mysqli_prepare($connection, $singleplayer_update)) {
                            mysqli_stmt_bind_param($prepared, "is", $singleplayer, $title);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_close($prepared);
                        }
                        echo "<p class=\"green\">Singleplayer was set to $singleplayer</p>";
                    }

                    // Update multiplayer
                    if (isset($_POST['multiplayer']) && is_numeric($_POST['multiplayer'])) {
                        $multiplayer = $_POST['multiplayer'];
                        $multiplayer_update = "UPDATE Game SET is_multiplayer = ? WHERE title = ?";
                        if ($prepared = mysqli_prepare($connection, $multiplayer_update)) {
                            mysqli_stmt_bind_param($prepared, "is", $multiplayer, $title);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_close($prepared);
                        }
                        echo "<p class=\"green\">Multiplayer was set to $multiplayer</p>";
                    }
                    
                    // Update title
                    if (isset($_POST['new_title']) && !empty($_POST['new_title'])) {
                        $new_title = $_POST['new_title'];
                        $query = "SELECT title FROM Game WHERE title = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $title);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $query = "SELECT title FROM Game WHERE title = ?";
                                if ($prepared = mysqli_prepare($connection, $query)) {
                                    mysqli_stmt_bind_param($prepared, "s", $new_title);
                                    mysqli_stmt_execute($prepared);
                                    mysqli_stmt_bind_result($prepared, $col_title);
                                    if (mysqli_stmt_fetch($prepared)) {
                                        echo "<p class=\"red\">Game in database already has the title $col_title</p>";
                                    } else {
                                        $title_update = "UPDATE Game SET title = ? WHERE title = ?";
                                        if ($prepared = mysqli_prepare($connection, $title_update)) {
                                            mysqli_stmt_bind_param($prepared, "ss", $new_title, $title);
                                            mysqli_stmt_execute($prepared);
                                        }
                                        echo "<p class=\"green\">$title was set to $new_title</p>";
                                    }
                                }
                            } else {
                                echo "<p class=\"red\">Game is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    }
                }
            } else if (isset($_POST['developer_update'])) {
                    $name = $_POST['name'];
                    $new_name = $_POST['new_name'];
                    
                    // Update developer name
                    if (isset($_POST['new_name']) && !empty($_POST['new_name'])) {
                        $new_name = $_POST['new_name'];
                        $query = "SELECT name FROM Developer WHERE name = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $name);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                echo "<p>Developer is in database</p>";
                                mysqli_stmt_close($prepared);
                                $query = "SELECT name FROM Developer WHERE name = ?";
                                if ($prepared = mysqli_prepare($connection, $query)) {
                                    mysqli_stmt_bind_param($prepared, "s", $new_name);
                                    mysqli_stmt_execute($prepared);
                                    mysqli_stmt_bind_result($prepared, $col_title);
                                    if (mysqli_stmt_fetch($prepared)) {
                                        echo "<p class=\"red\">Developer in database already has the name $col_title</p>";
                                    } else {
                                        $name_update = "UPDATE Developer SET name = ? WHERE name = ?";
                                        if ($prepared = mysqli_prepare($connection, $name_update)) {
                                            mysqli_stmt_bind_param($prepared, "ss", $new_name, $name);
                                            mysqli_stmt_execute($prepared);
                                        }
                                        echo "<p class=\"green\">$name was set to $new_name</p>";
                                    }
                                }
                            } else {
                                echo "<p class=\"red\">Developer is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    }
            } else if (isset($_POST['publisher_update'])) {
                    $name = $_POST['name'];
                    $new_name = $_POST['new_name'];
                    
                    // Update publisher name
                    if (isset($_POST['new_name']) && !empty($_POST['new_name'])) {
                        $new_name = $_POST['new_name'];
                        $query = "SELECT name FROM Publisher WHERE name = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $name);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $query = "SELECT name FROM Publisher WHERE name = ?";
                                if ($prepared = mysqli_prepare($connection, $query)) {
                                    mysqli_stmt_bind_param($prepared, "s", $new_name);
                                    mysqli_stmt_execute($prepared);
                                    mysqli_stmt_bind_result($prepared, $col_title);
                                    if (mysqli_stmt_fetch($prepared)) {
                                        echo "<p class=\"red\">Publisher in database already has the name $col_title</p>";
                                    } else {
                                        $name_update = "UPDATE Publisher SET name = ? WHERE name = ?";
                                        if ($prepared = mysqli_prepare($connection, $name_update)) {
                                            mysqli_stmt_bind_param($prepared, "ss", $new_name, $name);
                                            mysqli_stmt_execute($prepared);
                                        }
                                        echo "<p class=\"green\">$name was set to $new_name</p>";
                                    }
                                }
                            } else {
                                echo "<p class=\"red\">Publisher is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    }
            } else if (isset($_POST['platform_update'])) {
                    $name = $_POST['name'];
                    $new_name = $_POST['new_name'];
                    
                    // Update publisher name
                    if (isset($_POST['new_name']) && !empty($_POST['new_name'])) {
                        $new_name = $_POST['new_name'];
                        $query = "SELECT name FROM Platform WHERE name = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $name);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $query = "SELECT name FROM Platform WHERE name = ?";
                                if ($prepared = mysqli_prepare($connection, $query)) {
                                    mysqli_stmt_bind_param($prepared, "s", $new_name);
                                    mysqli_stmt_execute($prepared);
                                    mysqli_stmt_bind_result($prepared, $col_title);
                                    if (mysqli_stmt_fetch($prepared)) {
                                        echo "<p class=\"red\">Platform in database already has the name $col_title</p>";
                                    } else {
                                        $name_update = "UPDATE Platform SET name = ? WHERE name = ?";
                                        if ($prepared = mysqli_prepare($connection, $name_update)) {
                                            mysqli_stmt_bind_param($prepared, "ss", $new_name, $name);
                                            mysqli_stmt_execute($prepared);
                                        }
                                        echo "<p class=\"green\">$name was set to $new_name</p>";
                                    }
                                }
                            } else {
                                echo "<p class=\"red\">Platform is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    }
            } else if (isset($_POST['genre_update'])) {
                    $name = $_POST['name'];
                    $new_name = $_POST['new_name'];
                    
                    // Update genre name
                    if (isset($_POST['new_name']) && !empty($_POST['new_name'])) {
                        $new_name = $_POST['new_name'];
                        $query = "SELECT name FROM Genre WHERE name = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "s", $name);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                mysqli_stmt_close($prepared);
                                $query = "SELECT name FROM Genre WHERE name = ?";
                                if ($prepared = mysqli_prepare($connection, $query)) {
                                    mysqli_stmt_bind_param($prepared, "s", $new_name);
                                    mysqli_stmt_execute($prepared);
                                    mysqli_stmt_bind_result($prepared, $col_title);
                                    if (mysqli_stmt_fetch($prepared)) {
                                        echo "<p class=\"red\">Genre in database already has the name $col_title</p>";
                                    } else {
                                        $name_update = "UPDATE Genre SET name = ? WHERE name = ?";
                                        if ($prepared = mysqli_prepare($connection, $name_update)) {
                                            mysqli_stmt_bind_param($prepared, "ss", $new_name, $name);
                                            mysqli_stmt_execute($prepared);
                                        }
                                        echo "<p class=\"green\">$name was set to $new_name</p>";
                                    }
                                }
                            } else {
                                echo "<p class=\"red\">Genre is not in database</p>";
                            }
                            mysqli_stmt_close($prepared);
                        }
                    }
            } else if (isset($_POST['review_update'])) {
                $game = $_POST['game'];
                $reviewer = $_POST['reviewer'];
                $new_review = $_POST['new_review'];
                    
                if (isset($_POST['new_review']) && !empty($_POST['new_review'])) {
                    // Check if game is in database and get id
                    $query = "SELECT id FROM Game WHERE title = ?";
                    if ($prepared = mysqli_prepare($connection, $query)) {
                        mysqli_stmt_bind_param($prepared, "s", $game);
                        mysqli_stmt_execute($prepared);
                        mysqli_stmt_bind_result($prepared, $col_id);
                        if (mysqli_stmt_fetch($prepared)) {
                            mysqli_stmt_close($prepared);
                            $game = $col_id;
                            $query = "SELECT id FROM Review WHERE game = ? AND reviewer = ?";
                            if ($prepared = mysqli_prepare($connection, $query)) {
                                mysqli_stmt_bind_param($prepared, "ss", $game, $reviewer);
                                mysqli_stmt_execute($prepared);
                                mysqli_stmt_bind_result($prepared, $col_id);
                                if (mysqli_stmt_fetch($prepared)) {
                                    $review_id = $col_id;
                                    mysqli_stmt_close($prepared);
                                    $review_update = "UPDATE Review SET review = ? WHERE id = ?";
                                    if ($prepared = mysqli_prepare($connection, $review_update)) {
                                        mysqli_stmt_bind_param($prepared, "ss", $new_review, $review_id);
                                        mysqli_stmt_execute($prepared);
                                        echo "<p class=\"green\">New review was set</p>";
                                    }
                                } else {
                                    echo "<p class=\"red\">Review for game from reviewer is not in database</p>";
                                }
                            }
                            mysqli_stmt_close($prepared);
                        } else {
                            echo "<p class=\"red\">Game is not in database</p>";
                        }
                    }
                }
            } else if (isset($_POST['game_delete'])) {
                $title = $_POST['title'];
                $game_id;

                $query = "SELECT id FROM Game WHERE title = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $title);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $game_id = $col_id;
                        mysqli_stmt_close($prepared);
                        $delete1 = "DELETE FROM Game_Genre WHERE game_id = $game_id";
                        $delete2 = "DELETE FROM Game_Platform WHERE game_id = $game_id";
                        $delete3 = "DELETE FROM Review WHERE game = $game_id";
                        $delete4 = "DELETE FROM Game WHERE id = $game_id";
                        mysqli_query($connection, $delete1);
                        mysqli_query($connection, $delete2);
                        mysqli_query($connection, $delete3);
                        mysqli_query($connection, $delete4);
                        echo "<p class=\"green\">Game deleted from database</p>";
                    } else {
                        echo "<p class=\"red\">Game is not in database</p>";
                    }
                }
            } else if (isset($_POST['platform_delete'])) {
                $name = $_POST['name'];
                $platform_id;

                $query = "SELECT id FROM Platform WHERE name = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $name);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $platform_id = $col_id;
                        mysqli_stmt_close($prepared);
                        $query = "SELECT platform_id FROM Game_Platform WHERE platform_id = $platform_id";
                        $result = mysqli_query($connection, $query);
                        $num_rows = mysqli_num_rows($result);
                        if ($num_rows > 0) {
                            echo "<p class=\"red\">Could not delete platform, a game is referencing it</p>";
                        } else {
                            $delete = "DELETE FROM Platform WHERE id = $platform_id";
                            mysqli_query($connection, $delete);
                            echo "<p class=\"green\">Platform deleted</p>";
                        }
                    } else {
                        echo "<p class=\"red\">Platform is not in database</p>";
                    }
                }
            } else if (isset($_POST['developer_delete'])) {
                $name = $_POST['name'];
                $developer_id;

                $query = "SELECT id FROM Developer WHERE name = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $name);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $developer_id = $col_id;
                        mysqli_stmt_close($prepared);
                        $query = "SELECT id FROM Game WHERE developer = $developer_id";
                        $result = mysqli_query($connection, $query);
                        $num_rows = mysqli_num_rows($result);
                        if ($num_rows > 0) {
                            echo "<p class=\"red\">Could not delete developer, a game is referencing it</p>";
                        } else {
                            $delete = "DELETE FROM Developer WHERE id = $developer_id";
                            mysqli_query($connection, $delete);
                            echo "<p class=\"green\">Developer deleted</p>";
                        }
                    } else {
                        echo "<p class=\"red\">Developer is not in database</p>";
                    }
                }
            } else if (isset($_POST['publisher_delete'])) {
                $name = $_POST['name'];
                $publisher_id;

                $query = "SELECT id FROM Publisher WHERE name = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $name);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $publisher_id = $col_id;
                        mysqli_stmt_close($prepared);
                        $query = "SELECT id FROM Game WHERE publisher = $publisher_id";
                        $result = mysqli_query($connection, $query);
                        $num_rows = mysqli_num_rows($result);
                        if ($num_rows > 0) {
                            echo "<p class=\"red\">Could not delete publisher, a game is referencing it</p>";
                        } else {
                            $delete = "DELETE FROM Publisher WHERE id = $publisher_id";
                            mysqli_query($connection, $delete);
                            echo "<p class=\"green\">Publisher deleted</p>";
                        }
                    } else {
                        echo "<p class=\"red\">Publisher is not in database</p>";
                    }
                }
            } else if (isset($_POST['genre_delete'])) {
                $name = $_POST['name'];
                $genre_id;

                $query = "SELECT id FROM Genre WHERE name = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $name);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $genre_id = $col_id;
                        mysqli_stmt_close($prepared);
                        $query = "SELECT genre_id FROM Game_Genre WHERE genre_id = $genre_id";
                        $result = mysqli_query($connection, $query);
                        $num_rows = mysqli_num_rows($result);
                        if ($num_rows > 0) {
                            echo "<p class=\"green\">Could not delete genre, a game is referencing it</p>";
                        } else {
                            $delete = "DELETE FROM Genre WHERE id = $genre_id";
                            mysqli_query($connection, $delete);
                            echo "<p class=\"green\">Genre deleted</p>";
                        }
                    } else {
                        echo "<p class=\"red\">Genre is not in database</p>";
                    }
                }
            } else if (isset($_POST['review_delete'])) {
                $game = $_POST['game'];
                $reviewer = $_POST['reviewer'];
                $game_id;
                $review_id;

                $query = "SELECT id FROM Game WHERE title = ?";
                if ($prepared = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($prepared, "s", $game);
                    mysqli_stmt_execute($prepared);
                    mysqli_stmt_bind_result($prepared, $col_id);
                    if (mysqli_stmt_fetch($prepared)) {
                        $game_id = $col_id;
                        mysqli_stmt_close($prepared);
                        $query = "SELECT id FROM Review WHERE game = ? AND reviewer = ?";
                        if ($prepared = mysqli_prepare($connection, $query)) {
                            mysqli_stmt_bind_param($prepared, "is", $game_id, $reviewer);
                            mysqli_stmt_execute($prepared);
                            mysqli_stmt_bind_result($prepared, $col_id);
                            if (mysqli_stmt_fetch($prepared)) {
                                $review_id = $col_id;
                                mysqli_stmt_close($prepared);
                                $delete = "DELETE FROM Review WHERE id = $review_id";
                                mysqli_query($connection, $delete);
                                echo "<p class=\"green\">Review deleted</p>";
                            } else {
                                echo "<p class=\"red\">Review for game from reviewer not in database</p>";
                            }
                        }
                    } else {
                        echo "<p class=\"red\">Game is not in database</p>";
                    }
                }
            }
        }
    ?>

    <!-- Datalists used for autocomplete for text boxes -->
    <datalist id="games">
        <?php
            $query = "SELECT title FROM Game ORDER BY title";
            $result = mysqli_query($connection, $query);
            $num_rows = mysqli_num_rows($result);
            for ($i = 0; $i < $num_rows; $i++) {
                $row = mysqli_fetch_assoc($result);
                $title = $row["title"];
                echo "<option value=\"$title\">$title</option>";
            }
        ?>
    </datalist>
    <datalist id="developers">
        <?php
            $query = "SELECT name FROM Developer ORDER BY name";
            $result = mysqli_query($connection, $query);
            $num_rows = mysqli_num_rows($result);
            for ($i = 0; $i < $num_rows; $i++) {
                $row = mysqli_fetch_assoc($result);
                $name = $row["name"];
                echo "<option value='$name'>$name</option>";
            }
        ?>
    </datalist>
    <datalist id="publishers">
        <?php
            $query = "SELECT name FROM Publisher ORDER BY name";
            $result = mysqli_query($connection, $query);
            $num_rows = mysqli_num_rows($result);
            for ($i = 0; $i < $num_rows; $i++) {
                $row = mysqli_fetch_assoc($result);
                $name = $row["name"];
                echo "<option value='$name'>$name</option>";
            }
        ?>
    </datalist>
    <datalist id="platforms">
        <?php
            $query = "SELECT name FROM Platform ORDER BY name";
            $result = mysqli_query($connection, $query);
            $num_rows = mysqli_num_rows($result);
            for ($i = 0; $i < $num_rows; $i++) {
                $row = mysqli_fetch_assoc($result);
                $name = $row["name"];
                echo "<option value='$name'>$name</option>";
            }
        ?>
    </datalist>
    <datalist id="genres">
        <?php
            $query = "SELECT name FROM Genre ORDER BY name";
            $result = mysqli_query($connection, $query);
            $num_rows = mysqli_num_rows($result);
            for ($i = 0; $i < $num_rows; $i++) {
                $row = mysqli_fetch_assoc($result);
                $name = $row["name"];
                echo "<option value='$name'>$name</option>";
            }
        ?>
    </datalist>
    <datalist id="reviewers">
        <?php
            $query = "SELECT DISTINCT reviewer FROM Review ORDER BY reviewer";
            $result = mysqli_query($connection, $query);
            $num_rows = mysqli_num_rows($result);
            for ($i = 0; $i < $num_rows; $i++) {
                $row = mysqli_fetch_assoc($result);
                $reviewer = $row["reviewer"];
                echo "<option value='$reviewer'>$reviewer</option>";
            }
        ?>
    </datalist>

    <!-- Multiple forms used to insert or edit each table within database -->
	<h2>Edit</h2>
    <h3>Insert</h3> 
    <!-- Game Form -->
	<form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Game</legend>
	    <table>
		<tr>
	        <td><label>Title</label></td>
		    <td><input type="text" name="title" list="games" autocomplete="off" required><br></td>
		</tr>
		<tr>
		    <td><label>Developer</label></td>
		    <td><input type="text" name="developer" list="developers" autocomplete="off" required><br></td>
		</tr>
		<tr>
		    <td><label>Publisher</label></td>
		    <td><input type="text" name="publisher" list="publishers" autocomplete="off" required><br></td>
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
	        <td><label>Rating</label></td>
            <td><select name="rating" required>
                <option value="E">E</option>
                <option value="E10">E10</option>
                <option value="T">T</option>
                <option value="M">M</option>
            </select></td>
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
        <fieldset class="black_bg">
        <legend>Platform</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="platforms" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="platform_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Developer Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Developer</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="developers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="developer_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Publisher Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Publisher</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="developers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="publisher_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Genre Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Genre</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="genres" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="genre_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Review Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Review</legend>
        <table>
        <tr>
            <td><label>Game</label></td>
            <td><input type="text" name="game" list="games" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>Reviewer</label></td>
            <td><input type="text" name="reviewer" list="reviewers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>Review</label></td>
            <td><input type="text" name="review" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="review_insert" value="Insert"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <h3>Update</h3>
	<form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Game</legend>
	    <table>
		<tr>
	        <td><label>Title</label></td>
		    <td><input type="text" name="title" list="games" autocomplete="off" required><br></td>
		</tr>
		<tr>
	        <td><label>New Title</label></td>
		    <td><input type="text" name="new_title"><br></td>
		</tr>
		<tr>
		    <td><label>Developer</label></td>
		    <td><input type="text" name="developer" list="developers"><br></td>
		</tr>
		<tr>
		    <td><label>Publisher</label></td>
		    <td><input type="text" name="publisher" list="publishers"><br></td>
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
	        <td><label>Rating</label></td>
            <td><select name="rating">
                <option value=""></option>
                <option value="E">E</option>
                <option value="E10">E10</option>
                <option value="T">T</option>
                <option value="M">M</option>
            </select></td>
		</tr>
	    <tr>
		    <td><label>Release Date</label></td>
	        <td><input type="date" name="release"><br></td>
		</tr>
		<tr>
	        <td><label>Singleplayer</label></td>
			<td><input type="radio" name="singleplayer" value=1>Yes<br>
			<input type="radio" name="singleplayer" value=0>No<br></td>
        </tr>
		<tr>
	        <td><label>Multiplayer</label></td>
			<td><input type="radio" name="multiplayer" value=1>Yes<br>
			<input type="radio" name="multiplayer" value=0>No<br></td>
        </tr>
        <tr>
            <td><input type="submit" name="game_update" value="Update"></td>
        </tr>
		</table>
        </fieldset>
    </form>

    <!-- Platform Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Platform</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="platforms" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>New Name</label></td>
            <td><input type="text" name="new_name" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="platform_update" value="Update"></td>
        </tr>
        </table>
        </fieldset>
    </form>


    <!-- Developer Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Developer</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="developers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>New Name</label></td>
            <td><input type="text" name="new_name" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="developer_update" value="Update"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Publisher Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Publisher</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="publishers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>New Name</label></td>
            <td><input type="text" name="new_name" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="publisher_update" value="Update"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Genre Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Genre</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="genres" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>New Name</label></td>
            <td><input type="text" name="new_name" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="genre_update" value="Update"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <!-- Review Form -->
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Review</legend>
        <table>
        <tr>
            <td><label>Game</label></td>
            <td><input type="text" name="game" list="games" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>Reviewer</label></td>
            <td><input type="text" name="reviewer" list="reviewers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>New Review</label></td>
            <td><input type="text" name="new_review" required><br></td>
        </tr>
            <td><input type="submit" name="review_update" value="Update"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <h3>Delete</h3>
    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Game</legend>
        <table>
        <tr>
            <td><label>Title</label></td>
            <td><input type="text" name="title" list="games" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="game_delete" value="Delete"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Platform</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="platforms" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="platform_delete" value="Delete"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Developer</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="developers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="developer_delete" value="Delete"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Publisher</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="publishers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="publisher_delete" value="Delete"></td>
        </tr>
        </table>
        </fieldset>
    </form> 

    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Genre</legend>
        <table>
        <tr>
            <td><label>Name</label></td>
            <td><input type="text" name="name" list="genres" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="genre_delete" value="Delete"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    <form action="edit.php" method="post">
        <fieldset class="black_bg">
        <legend>Review</legend>
        <table>
        <tr>
            <td><label>Game</label></td>
            <td><input type="text" name="game" list="games" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><label>Reviewer</label></td>
            <td><input type="text" name="reviewer" list="reviewers" autocomplete="off" required><br></td>
        </tr>
        <tr>
            <td><input type="submit" name="review_delete" value="Delete"></td>
        </tr>
        </table>
        </fieldset>
    </form>

    </body>

</html>
