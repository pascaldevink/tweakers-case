<?php

require_once realpath(dirname(__FILE__)) . '/../conf/Configuration.php';
require_once realpath(dirname(__FILE__)) . '/../model/Article.php';
require_once realpath(dirname(__FILE__)) . '/../model/Comment.php';

class ArticleService
{

    /**
     * Get the article specified by the $articleId and its comments. The comments can be ordered by $order and a
     * $minScore can be given to filter on.
     *
     * The params from the database are first decoded with htmlspecialchars_decode and then encoded again with
     * htmlentities. The reason for this is because we do not trust data that comes into our application.
     * Htmlentities is used as it is very flexible, in that it can be passed a list of allowed tags. This makes it
     * easy to extend later.
     *
     * @param  $articleId
     * @param null $order
     * @param null $minScore
     * @return Article
     */
    public function getArticle($articleId, $order = null, $minScore = null)
    {
        // Get article from database
        $sql = 'SELECT article.article_id,article.author,article.title,article.text,article.created_at,comment.comment_id,comment.parent_id,comment.user,comment.text as comment_text,comment.average_score,comment.created_at as comment_created_at FROM article LEFT JOIN comment ON article.article_id = comment.article_id WHERE article.article_id = :articleId';
        $con = new PDO('mysql:host=' . Configuration::DATABASE_HOST . ';dbname=' . Configuration::DATABASE_NAME,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASS);
        $statement = $con->prepare($sql);
        $statement->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($result == false) {
            return null;
        }

        // Setup the article
        $article = new Article();
        $article->setId(htmlentities(htmlspecialchars_decode($result[0]['article_id'])));
        $article->setAuthor(htmlentities(htmlspecialchars_decode($result[0]['author'])));
        $article->setTitle(htmlentities(htmlspecialchars_decode($result[0]['title'])));
        $article->setText(htmlentities(htmlspecialchars_decode($result[0]['text'])));
        $article->setCreatedAt(htmlentities(htmlspecialchars_decode($result[0]['created_at'])));

        // Get the comments
        $comments = array();
        foreach ($result as $commentResult) {
            $comment = new Comment();
            if ($commentResult['comment_id'] != null) {
                $comment->setId(htmlentities(htmlspecialchars_decode($commentResult['comment_id'])));
                $comment->setArticleId(htmlentities(htmlspecialchars_decode($commentResult['article_id'])));
                $comment->setParentId(htmlentities(htmlspecialchars_decode($commentResult['parent_id'])));
                $comment->setUser(htmlentities(htmlspecialchars_decode($commentResult['user'])));
                $comment->setText(htmlentities(htmlspecialchars_decode($commentResult['comment_text'])));
                $comment->setAverageScore(htmlentities(htmlspecialchars_decode($commentResult['average_score'])));
                $comment->setCreatedAt(htmlentities(htmlspecialchars_decode($commentResult['comment_created_at'])));
                $comments[$comment->getId()] = $comment;
            }
        }

        // Sort the comments
        $comments = $this->sortComments($comments, array());

        if ($minScore != null) {
            // Filter the comments
            $comments = $this->filterComments($comments, $minScore);
        }

        if ($order) {
            // Order the comments
            $comments = $this->orderComments($comments, $order);
        }
        $article->setComments($comments);

        return $article;
    }

    private $timed = 0;
    /**
     * Sorts the comments in a tree. Provide all raw comments in the $parkedComments if used for the first time.
     * Returns an array of root comments. Their ancestors are accessible through the Comment object.
     *
     * @param array $processedComments
     * @param array $parkedComments
     * @return array
     */
    protected function sortComments($parkedComments, $processedComments = array())
    {
        // Process the parked comments
        foreach ($parkedComments as $comment) {
            if ($comment->getParentId() == null) {
                $processedComments[$comment->getId()] = $comment;
                array_shift($parkedComments);
            }
            else if (isset($processedComments[$comment->getParentId()])) {
                $processedComments[$comment->getParentId()]->addComment($comment);
                array_shift($parkedComments);
            }
            else {
                foreach ($processedComments as $processedComment) {
                    if ($processedComment->hasComment($comment->getParentId())) {
                        $processedComment->addChildComment($comment);
                        array_shift($parkedComments);
                    }
                }
            }
        }

        // If there are still parked comments left, process them the same way
        if (count($parkedComments) > 0) {
            $this->timed++;
            $processedComments = $this->sortComments($parkedComments, $processedComments);
        }

        return $processedComments;
    }

    /**
     * Orders the comments by $order. Does a deep order, so child comments are also ordered by $order.
     * 
     * @param  $comments
     * @param  $order
     * @return array
     */
    protected function orderComments($comments, $order)
    {
        if ($order == 'asc') {
            uasort($comments, function(Comment $a, Comment $b)
                {
                    if ($a->getCreatedAt() == $b->getCreatedAt()) {
                        return 0;
                    }

                    return ($a->getCreatedAt() < $b->getCreatedAt()) ? -1 : 1;
                });
        }
        else {
            uasort($comments, function(Comment $a, Comment $b)
                {
                    if ($a->getCreatedAt() == $b->getCreatedAt()) {
                        return 0;
                    }

                    return ($a->getCreatedAt() < $b->getCreatedAt()) ? 1 : -1;
                });
        }

        foreach ($comments as $comment) {
            $comment->sortComments($order);
        }

        return $comments;
    }

    /**
     * Filters the comments by $minScore. Does a deep filter, so children with lower average scores then $minScore are
     * also not shown. Neither are children of parents which average score is lower then $minScore.
     * 
     * @param  $comments
     * @param  $minScore
     * @return array
     */
    protected function filterComments($comments, $minScore)
    {
        $comments = array_filter($comments, function(Comment $comment) use ($minScore) {
                if ($comment->hasComments()) {
                    $comment->filterComments($minScore);
                }
                return $comment->getAverageScore() >= $minScore;
            });

        return $comments;
    }
}
