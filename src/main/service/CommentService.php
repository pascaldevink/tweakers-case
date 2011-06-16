<?php

class CommentService
{

    /**
     * Retrieve the comment for $commentId.
     *
     * @param  $commentId
     * @return Comment
     */
    public function getComment($commentId)
    {
        $sql = 'SELECT comment.comment_id,comment.article_id,comment.parent_id,comment.user,comment.text as comment_text,comment.average_score,comment.created_at as comment_created_at FROM comment WHERE comment.comment_id = :commentId';
        $con = Configuration::getConnection();
        $statement = $con->prepare($sql);
        $statement->bindValue(':commentId', $commentId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($result == false) {
            return null;
        }

        $comment = new Comment();
        $comment->setId(htmlentities(htmlspecialchars_decode($result[0]['comment_id'])));
        $comment->setArticleId(htmlentities(htmlspecialchars_decode($result[0]['article_id'])));
        $comment->setParentId(htmlentities(htmlspecialchars_decode($result[0]['parent_id'])));
        $comment->setUser(htmlentities(htmlspecialchars_decode($result[0]['user'])));
        $comment->setText(htmlentities(htmlspecialchars_decode($result[0]['comment_text'])));
        $comment->setAverageScore(htmlentities(htmlspecialchars_decode($result[0]['average_score'])));
        $comment->setCreatedAt(htmlentities(htmlspecialchars_decode($result[0]['comment_created_at'])));

        return $comment;
    }

    /**
     * Add a new comment.
     *
     * @param  $articleId
     * @param  $user
     * @param  $text
     * @param null $parentId
     * @return bool
     */
    public function addComment($articleId, $user, $text, $parentId = null)
    {
        $sql = 'INSERT INTO comment (article_id, parent_id, user, text, average_score, created_at) VALUES (:articleId, :parentId, :user, :text, :averageScore, :createdAt)';
        $con = Configuration::getConnection();
        $statement = $con->prepare($sql);
        $statement->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $statement->bindValue(':parentId', $parentId, PDO::PARAM_INT);
        $statement->bindValue(':user', $user, PDO::PARAM_STR);
        $statement->bindValue(':text', $text, PDO::PARAM_STR);
        $statement->bindValue(':averageScore', 0, PDO::PARAM_INT);
        $statement->bindValue(':createdAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $result = $statement->execute();

        return $result;
    }

    /**
     * Add a score for a comment and recalculate the average score for the comment.
     *
     * @throws Exception
     * @param Comment $comment
     * @param  $score
     * @return bool
     */
    public function rateComment(Comment $comment, $score) {
        $con = Configuration::getConnection();
        
        $sql = 'INSERT INTO score (comment_id, score) VALUES (:commentId, :score)';
        $statement = $con->prepare($sql);
        $statement->bindValue(':commentId', $comment->getId(), PDO::PARAM_INT);
        $statement->bindValue(':score', $score, PDO::PARAM_INT);
        $result = $statement->execute();
        if ($result == false) {
            throw new Exception('Something went wrong while inserting the score');
        }

        $sql = 'SELECT AVG(score) FROM score WHERE comment_id = :commentId';
        $statement = $con->prepare($sql);
        $statement->bindValue(':commentId', $comment->getId(), PDO::PARAM_INT);
        $statement->execute();
        $averageScore = $statement->fetchAll();
        $averageScore = intval($averageScore[0][0]);

        $sql = 'UPDATE comment SET average_score=:averageScore WHERE comment_id=:commentId';
        $statement = $con->prepare($sql);
        $statement->bindValue(':commentId', $comment->getId(), PDO::PARAM_INT);
        $statement->bindValue(':averageScore', $averageScore, PDO::PARAM_INT);
        $result = $statement->execute();

        return $result;
    }
}
