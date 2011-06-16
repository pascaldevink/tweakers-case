<?php

require_once realpath(dirname(__FILE__)) . '/../main/conf/Configuration.php';

$con = new PDO('mysql:host=' . Configuration::DATABASE_HOST . ';dbname=' . Configuration::DATABASE_NAME,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASS);

$dummyAuthor = 'Jan de Schrijver';
$dummyUser = 'Piet met Commentaar';
$dummyTitle = 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit';
$dummyText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras nec est non ante volutpat auctor at et massa.
              Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam
              elementum pulvinar leo et mattis. Donec dapibus laoreet mauris, at scelerisque enim egestas ut. Proin
              eleifend augue dapibus est commodo facilisis. Proin quis orci sed odio ultrices imperdiet vitae sit amet
              nulla. Aliquam orci nisl, faucibus eu adipiscing nec, dapibus eget nunc. Quisque dapibus magna at lorem
              venenatis ultrices. Cras orci ante, venenatis tincidunt venenatis nec, volutpat sit amet enim. Nam laoreet
              mauris tempor dolor volutpat dictum. Proin eros lacus, elementum mollis malesuada non, porta at elit.
              Integer malesuada, nulla in rutrum blandit, odio nibh imperdiet lorem, a lobortis metus turpis vel sapien.
              Sed nec velit dolor, sit amet viverra quam. Fusce in fermentum mi. Nunc lacinia hendrerit lacus nec
              tincidunt. Duis mattis ornare odio in rutrum. Duis eget arcu lorem.";
$dummyDate = date('Y-m-d H:i:s');

$numberOfArticles = 100000;
$numberOfComments = 1000000;

for ($i = 0; $i <= $numberOfArticles; $i++)
{
    $sql = 'INSERT INTO article (author, title, text, created_at) VALUES (:author, :title, :text, :createdAt)';
    $statement = $con->prepare($sql);
    $statement->bindValue(':author', $dummyAuthor, PDO::PARAM_STR);
    $statement->bindValue(':title', $dummyTitle, PDO::PARAM_STR);
    $statement->bindValue(':text', $dummyText, PDO::PARAM_STR);
    $statement->bindValue(':createdAt', $dummyDate, PDO::PARAM_STR);

    $result = $statement->execute();
    if ($result == false) {
        echo "Something went wrong after $i iterations of inserting articles:";
        echo $statement->errorInfo();
        break;
    }
}

$y = 0;
$articleId = 1;
for ($i = 0; $i <= $numberOfComments; $i++)
{
    if ($y < ($numberOfComments / $numberOfArticles)) {
        $y++;
    } else {
        $articleId++;
        $y = 0;
    }

    if (($parentId = $numberOfComments / $numberOfArticles + $i) % 10 == 0) {
        $parentId = null;
    } else {
        $parentId -= 10;
    }

    $sql = 'INSERT INTO comment (article_id, parent_id, user, text, average_score, created_at) VALUES (:articleId, :parentId, :user, :text, :averageScore, :createdAt)';
    $statement = $con->prepare($sql);
    $statement->bindValue(':articleId', $articleId, PDO::PARAM_INT);
    $statement->bindValue(':parentId', $parentId, PDO::PARAM_INT);
    $statement->bindValue(':user', $dummyUser, PDO::PARAM_STR);
    $statement->bindValue(':text', $dummyText, PDO::PARAM_STR);
    $statement->bindValue(':averageScore', 0, PDO::PARAM_INT);
    $statement->bindValue(':createdAt', $dummyDate, PDO::PARAM_STR);

    $result = $statement->execute();
    if ($result == false) {
        echo "Something went wrong after $i iterations of inserting comments:";
        echo $statement->errorInfo();
        break;
    }
}