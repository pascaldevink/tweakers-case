<?php

class Score {

    private $id;
    private $commentId;
    private $score;

    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;
    }

    public function getCommentId()
    {
        return $this->commentId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }

    public function getScore()
    {
        return $this->score;
    }
}
