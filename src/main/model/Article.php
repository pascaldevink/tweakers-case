<?php

class Article {

    private $id;
    private $author;
    private $title;
    private $text;
    private $createdAt;
    private $comments;

    public function __construct()
    {
        $this->comments = array();
    }

    /**
     * Returns whether or not this article has comments.
     *
     * @return bool
     */
    public function hasComments()
    {
        $hasComments = count($this->comments) > 0;
        return $hasComments;
    }

    /**
     * Add a comment to the article.
     *
     * @param Comment $comment
     * @return void
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function getComments()
    {
        return $this->comments;
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

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

}
