<?php

class Comment
{

    private $id;
    private $articleId;
    private $parentId;
    private $user;
    private $text;
    private $averageScore;
    private $createdAt;
    private $comments;
    private $level;

    /**
     * Returns whether this comment has a parent, or is a root comment.
     * 
     * @return bool
     */
    public function hasParent()
    {
        $hasParent = $this->parentId != null;
        return $hasParent;
    }

    /**
     * Order the child comments by $order.
     * 
     * @param  $order
     * @return array
     */
    public function sortComments($order)
    {
        if ($this->comments && count($this->comments) > 0) {
            if ($order == 'asc') {
                uasort($this->comments, function(Comment $a, Comment $b) {
                        if ($a->getCreatedAt() == $b->getCreatedAt()) {
                            return 0;
                        }

                        return ($a->getCreatedAt() < $b->getCreatedAt()) ? -1 : 1;
                    });
            }
            else {
                uasort($this->comments, function(Comment $a, Comment $b) {
                        if ($a->getCreatedAt() == $b->getCreatedAt()) {
                            return 0;
                        }

                        return ($a->getCreatedAt() < $b->getCreatedAt()) ? 1 : -1;
                    });
            }

            foreach ($this->comments as $comment) {
                $comment->sortComments($order);
            }
        }

        return $this->comments;
    }

    /**
     * Filter the child comments by $minScore.
     *
     * @param  $minScore
     * @return array
     */
    public function filterComments($minScore)
    {
        $comments = array_filter($this->comments, function(Comment $comment) use ($minScore) {
                if ($comment->hasComments()) {
                    $comment->filterComments($minScore);
                }
                return $comment->getAverageScore() >= $minScore;
            });
        $this->comments = $comments;
        
        return $comments;
    }

    /**
     * Returns whether the provided comment Id is in the comment tree.
     * 
     * @param  $commentId
     * @return bool
     */
    public function hasComment($commentId)
    {
        if ($this->comments == null || !is_array($this->comments)) {
            return false;
        }
        
        $userData['hasChildComment'] = false;
        $userData['commentId'] = $commentId;

        array_walk($this->comments, function($item, $key, $userData)
            {
                if (!$userData['hasChildComment'] == true) {
                    if ($item->getId() == $userData['commentId']) {
                        $userData['hasChildComment'] = true;
                    }
                    else if ($item->hasComment($userData['commentId'])) {
                        $userData['hasChildComment'] = true;
                    }
                }
            }, &$userData);

        return $userData['hasChildComment'];
    }

    /**
     * Add $comment to one of the children, provided that the parent exists in the current comment tree.
     *
     * @param Comment $comment
     * @return void
     */
    public function addChildComment(Comment $comment)
    {
        array_walk($this->comments, function($item, $key, $comment)
            {
                if ($item->getId() == $comment->getParentId()) {
                    $item->addComment($comment);
                }
                else if ($item->hasComment($comment->getParentId())) {
                    $item->addChildComment($comment);
                }
            }, $comment);
    }

    /**
     * Returns whether this comment has children.
     *
     * @return bool
     */
    public function hasComments()
    {
        $hasComments = ($this->comments != null && count($this->comments) > 0);
        return $hasComments;
    }

    /**
     * Add $comment to the root of the current comments.
     *
     * @param Comment $comment
     * @return void
     */
    public function addComment(Comment $comment)
    {
        if ($this->comments == null) {
            $this->comments = array();
        }

        $this->comments[] = $comment;
    }

    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    }

    public function getArticleId()
    {
        return $this->articleId;
    }

    public function setAverageScore($averageScore)
    {
        $this->averageScore = $averageScore;
    }

    public function getAverageScore()
    {
        return $this->averageScore;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }

}
