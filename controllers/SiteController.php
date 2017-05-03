<?php

namespace app\controllers;

use app\models\Article;
use app\models\Category;
use app\models\CommentForm;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $data = Article::getAll(2);
        $popular = Article::getPopular();
        $recent = Article::getRecent();
        $categories = Category::getAll();

        return $this->render('index', [
            'pagination' => $data['pagination'],
            'articles' => $data['articles'],
            'popular' => $popular,
            'recent' => $recent,
            'categories' => $categories
        ]);
    }

    public function actionView($id)
    {

        $article = Article::findOne($id);
        if (!empty($article))
        {
            $tags = ($article) ? $article->tags : 'Error';
            $popular = Article::getPopular();
            $recent = Article::getRecent();
            $categories = Category::getAll();
            $comments = $article->getArticleComments();
            $commentForm = new CommentForm();

            $article->viewedCounter();


            return $this->render('single', [
                'article' => $article,
                'popular' => $popular,
                'recent' => $recent,
                'categories' => $categories,
                'tags' => $tags,
                'comments' => $comments,
                'commentForm' => $commentForm
            ]);
        }

        return $this->redirect('index');

    }

    public function actionCategory($id)
    {
        $category = Category::findOne($id);
        $data = Category::getArticlesById($id);
        $popular = Article::getPopular();
        $recent = Article::getRecent();
        $categories = Category::getAll();

        return $this->render('category', [
            'pagination' => $data['pagination'],
            'articles' => $data['articles'],
            'popular' => $popular,
            'recent' => $recent,
            'categories' => $categories,
            'category' => $category
        ]);
    }


    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionComment($id)
    {
        $model = new CommentForm();

        if (Yii::$app->request->isPost)
        {
            $model->load(Yii::$app->request->post());
            if ($model->saveComment($id))
            {
                Yii::$app->getSession()->setFlash('comment', 'Comment sena and will be added soon or never :)');
                return $this->redirect(['site/view', 'id' => $id]);
            }
        }
    }
}
