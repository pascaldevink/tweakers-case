<html>
<head>
    <title>Tweakers.net</title>
    <link type="text/css" href="/css/main.css" rel="stylesheet"/>
</head>
<body>

<div class="container">
    <?php if ($modelAndView->article != null) : ?>
    <div class="article">
        <h1><?php echo $modelAndView->article->getTitle(); ?></h1>

        <div class="intro">Door <?php echo $modelAndView->article->getAuthor(); ?>, op <?php echo $modelAndView->article->getCreatedAt(); ?></div>
        <div class="text"><?php echo $modelAndView->article->getText(); ?></div>
    </div>

    <div class="comments">
        <?php if ($modelAndView->article->hasComments()) : ?>
        <form action="index.php" method="get">
            <input type="hidden" name="articleId" value="<?php echo $modelAndView->article->getId(); ?>" />
            <select name="order">
                <option value="desc">Nieuwste eerst</option>
                <option value="asc">Oudste eerst</option>
            </select>
            <select name="minScore">
                <option>-1</option>
                <option>0</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
            </select>
            <input type="submit" value="Sortering aanpassen">
        </form>

        <?php foreach ($modelAndView->article->getComments() as $comment) : ?>
            <?php renderComment($comment); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <hr />

    <div>
        <form action="/addcommentsave.php" method="post">
            <input type="hidden" name="articleId" id="articleId" value="<?php echo $modelAndView->article->getId(); ?>">

            <label for="user">Gebruikersnaam: </label>
            <input type="text" name="user" id="user"><br/>

            <label for="text">Bericht:</label>
            <textarea name="text" id="text"></textarea><br/>

            <input type="submit" value="Versturen" />
        </form>
    </div>
</div>

<?php
function renderComment(Comment $comment)
{
    echo '<div class="comment" id="' . $comment->getId() . '">';
    echo '<div class="comment-header">';
    echo '<a href="addcomment.php?articleId=' . $comment->getArticleId() . '&parentId=' . $comment->getId() . '"><img src="/img/addcomment.png" alt="Reageer op deze reactie" /></a>';
    echo 'Door ' . $comment->getUser() . ', ' . $comment->getCreatedAt() . ' ';
    echo 'Score: ' . $comment->getAverageScore() . ' ';
    echo ' Geef score: ';
    echo '<form action="ratecomment.php" method="post">';
    echo '<input type="hidden" name="articleId" value="'.$comment->getArticleId().'" />';
    echo '<input type="hidden" name="commentId" value="'.$comment->getId().'" />';
    echo '<select name="score">';
    echo '<option>3</option>';
    echo '<option>2</option>';
    echo '<option>1</option>';
    echo '<option>0</option>';
    echo '<option>-1</option>';
    echo '</select>';
    echo '<input type="submit" value="Geef door" />';
    echo '</form>';
    echo '</div>';
    echo '<div class="comment-content">' . $comment->getText() . '</div>';

    if ($comment->hasComments()) {
        echo '<div class="inner-comment">';
        foreach ($comment->getComments() as $innerComment) {
            renderComment($innerComment);
        }
        echo '</div>';
    }

    echo '</div>';
}
?>

</body>
</html>
