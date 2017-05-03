<?php


namespace app\modules\admin\controllers;


use app\models\Comment;
use Yii;
use yii\web\Controller;

class CommentController extends Controller
{
    public function actionIndex()
    {
        $comments = Comment::find()->orderBy('id desc')->all();

        return $this->render('index', [
            'comments' => $comments
        ]);
    }

    public function actionDelete($id)
    {
        $comment = Comment::findOne($id);

        if ($comment->delete())
        {
            Yii::$app->getSession()->setFlash('comment', 'Comment was deleted successfully :)');
            return $this->redirect(['comment/index']);
        }
    }

    public function actionAllow($id)
    {
        $comment = Comment::findOne($id);
        if ($comment->allow())
        {
            Yii::$app->getSession()->setFlash('comment', 'Allowed :)');
            return $this->redirect(['index']);
        }
    }

    public function actionDisallow($id)
    {
        $comment = Comment::findOne($id);
        if ($comment->disallow())
        {
            Yii::$app->getSession()->setFlash('comment', 'Disallowed :(');
            return $this->redirect(['index']);
        }
    }
}