<!DOCTYPE html>
<html>
    <head>
	<meta charset="utf-8">
	<title>Video Games</title>
	<link rel="stylesheet" href="style.css">
    </head>
    <body>
	<h1 class="center">Video Games</h1>
	<nav>
	    <a href="home.php">Home</a>
	    <a href="search.php">Search</a>
	    <a href="edit.php">Edit</a>
	    <a href="login.php">Login</a>
	</nav>
	<h2>Search</h2>
	<form>
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
		</tr>
	    </table>
	</form>
    </body>

</html>
