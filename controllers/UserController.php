<?php

namespace app\controllers;

use app\components\JwtValidationData;
use app\models\User;
use Lcobucci\JWT\Token;
use sizeg\jwt\Jwt;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\rest\Controller;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class UserController extends Controller
{
    public $enableCsrfValidation = false;

    public function verbs(): array
    {
        return array_merge([
            "index" => ['GET', 'POST'],
            'login' => ['POST'],
            'data' => ['POST']
        ],
            parent::verbs());
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login',
                'index'
            ],
        ];
        return $behaviors;
    }

    /**
     * Подготавливает и возвращает jwt токен
     * @param IdentityInterface $user
     * @return Token
     */
    private function generateJwt(IdentityInterface $user): Token
    {
        /** @var $jwt Jwt */
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();
        $jwtParams = Yii::$app->params['jwt'];
        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('uid', $user->getId())
            ->getToken($signer, $key);
    }

    /**
     * Регистрация пользователя при передаче методом POST объекта {"email": "exemple@some.post", "password": "******"}
     *
     * При запросе методом GET при наличии действующего токена в заголовке возвращает объект с данными текущего пользователя
     *
     * @return Response Объект json с данными пользователя (в поле user) и, при запросе через POST, токен (в поле token)
     * @throws UnauthorizedHttpException Возвращает при запросе методом GET и отсутствия валидного токена.
     */
    public function actionIndex(): Response
    {
        if ($this->request->isGet) {
            $user = Yii::$app->user->identity;
            if (!is_null($user)) {
                return $this->asJson([
                    'user' => $user
                ]);
            }
        } else if ($this->request->isPost) {
            $post = $this->request->post();
            $user = new User($post);
            if (!$user->save()) {
                return $this->asJson([
                    'errors' => $user->getErrors()
                ]);
            }
            Yii::$app->user->login($user);
            return $this->asJson([
                "user" => $user,
                "token" => (string)$this->generateJwt($user),
            ]);
        }
        throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
    }

    /**
     * Вход пользователя при передаче методом POST объекта {"email": "exemple@some.post", "password": "******"}
     *
     * @return Response Объект json с данными пользователя (в поле user) и токен (в поле token)
     */
    public function actionLogin(): Response
    {
        $request = $this->request;
        $email = $request->post('email', null);
        $password = $request->post('password', null);
        if (!is_null($email) && !is_null($password)) {
            $user = User::findOne(['email' => $email]);
            if (!is_null($user)) {
                $validate_password = Yii::$app->security->validatePassword(
                    $password,
                    $user->password
                );
                if ($validate_password) {
                    return $this->asJson([
                        'token' => (string)$this->generateJwt($user),
                        'user' => $user
                    ]);
                }
            }
        }
        return $this->asJson([
            'errors' => 'Ошибка. Неверный логин или пароль'
        ]);
    }

    /**
     * При наличии регистрации принимает через метод POST объект {"first_name": "Some", "last_name": "Some", "phone_number": "81234567890"}
     * и изменяет данные текущего пользователя. Поля объекта не обязательны для заполнения.
     *
     * @return Response Объект json с данными пользователя в поле user
     */
    public function actionData(): Response
    {
        $user = Yii::$app->user->identity;
        $user->scenario = User::SCENARIO_ADD_DATA;
        $data = $this->request->post();
        $user->attributes = $data;
        if (!$user->save()) {
            return $this->asJson([
                'errors' => $user->getErrors()
            ]);
        }
        return $this->asJson([
            'user' => $user,
        ]);
    }

}