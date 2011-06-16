<html>
<head>
    <title>Tweakers.net</title>
    <link type="text/css" href="/css/main.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <form action="/addcommentsave.php" method="post">
            <input type="hidden" name="articleId" id="articleId" value="<?php echo $articleId; ?>">
            <input type="hidden" name="parentId" id="parentId" value="<?php echo $parentId; ?>">

            <label for="user">Gebruikersnaam: </label>
            <input type="text" name="user" id="user"><br />
            
            <label for="text">Bericht:</label>
            <textarea name="text" id="text"></textarea><br />

            <input type="submit" value="Versturen" />
        </form>
    </div>
</body>
</html>