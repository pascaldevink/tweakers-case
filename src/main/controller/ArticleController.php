<?php

require_once realpath(dirname(__FILE__)) . '/../service/ArticleService.php';
require_once realpath(dirname(__FILE__)) . '/../service/CommentService.php';

class ArticleController {

    /**
     * Executes the viewing of an article.
     *
     * @throws Exception
     * @param Request $request
     * @return void
     */
    public function executeViewArticle(Request $request)
    {
        $articleId = $request->getGetParameter('articleId');
        if (!$articleId) {
            throw new Exception('No article id given');
        }

        $order = $request->getGetParameter('order');
        $minScore = $request->getGetParameter('minScore');

        $articleService = new ArticleService();
        $article = $articleService->getArticle($articleId, $order, $minScore);

        if (!$article) {
            throw new Exception('No article found with id: ' . $articleId);
        }
        include realpath(dirname(__FILE__)) . '/../view/ArticleView.php';
    }

    /**
     * Execute the add comment form.
     * 
     * @throws Exception
     * @param Request $request
     * @return void
     */
    public function executeAddComment(Request $request) {
        $articleId = $request->getGetParameter('articleId');
        if (!$articleId) {
            throw new Exception('No article id given');
        }

        $articleService = new ArticleService();
        $article = $articleService->getArticle($articleId);

        if (!$article) {
            throw new Exception('No article found with id: ' . $articleId);
        }

        $parentId = $request->getGetParameter('parentId');
        if ($parentId) {

            $commentService = new CommentService();
            $parentComment = $commentService->getComment($parentId);

            if (!$parentComment) {
                throw new Exception('No comment found with id: ' . $parentId);
            }
        }
        
        include realpath(dirname(__FILE__)) . '/../view/AddCommentView.php';
    }

    /**
     * Executes the saving of a new comment.
     * 
     * @throws Exception
     * @param Request $request
     * @return void
     */
    public function executeAddCommentSave(Request $request)
    {
        $articleId = $request->getPostParameter('articleId');
        if (!$articleId) {
            throw new Exception('No article id given');
        }
        
        $articleService = new ArticleService();
        $article = $articleService->getArticle($articleId);

        if (!$article) {
            throw new Exception('No article found with id: ' . $articleId);
        }

        $parentId = $request->getPostParameter('parentId');
        $commentService = new CommentService();
        if ($parentId) {
            $parentComment = $commentService->getComment($parentId);

            if (!$parentComment) {
                throw new Exception('No comment found with id: ' . $parentId);
            }
        }
        
        $user = $request->getPostParameter('user');
        $text = $request->getPostParameter('text');

        if (!$user) {
            throw new Exception('No user given');
        }
        if (!$text) {
            throw new Exception('No text given');
        }

        $commentService->addComment($articleId, $user, $text, $parentId);

        $request->addGetParameter('articleId', $articleId);
        $this->executeViewArticle($request);
    }

    /**
     * Executes the adding of a new score for a comment.
     *
     * @throws Exception
     * @param Request $request
     * @return void
     */
    public function executeRateComment(Request $request)
    {
        $articleId = $request->getPostParameter('articleId');
        if (!$articleId) {
            throw new Exception('No article id given');
        }

        $articleService = new ArticleService();
        $article = $articleService->getArticle($articleId);

        if (!$article) {
            throw new Exception('No article found with id: ' . $articleId);
        }

        $commentId = $request->getPostParameter('commentId');
        if (!$commentId) {
            throw new Exception('No comment id given');
        }

        $commentService = new CommentService();
        $comment = $commentService->getComment($commentId);

        if (!$comment) {
            throw new Exception('No comment found with id: ' . $commentId);
        }

        $score = $request->getPostParameter('score');
        if ($score == null) {
            throw new Exception('No score given');
        }

        if ($score < -1 || $score > 3) {
            throw new Exception('Invalid score given');
        }

        $commentService->rateComment($comment, $score);

        $request->addGetParameter('articleId', $articleId);
        $this->executeViewArticle($request);
    }
}
