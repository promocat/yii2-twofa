Extension for Yii2 providing Two Factor Authentication
================================
Provides TOTP and QR codes for use with an authenticator like the one from Google or Authy

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist promocat/yii2-twofa "*"
```

or add

```
"promocat/yii2-twofa": "*"
```

to the require section of your `composer.json` file.

Setup
------------

Update the user component in your config file to use the class
```
promocat\twofa\User::class
```
It should sorta look like this
```
'components' => [
    'user' => [
        'class' => promocat\twofa\User::class,
        'identityClass' => 'common\models\User',
        'enableAutoLogin' => true,
        'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
    ]
]
```

Also add the "twoFa" component:
```
'twoFa' => ['class' => promocat\twofa\TwoFa::class]
```

Next, add the TwoFaBehavior to your User model
```
public function behaviors() {
    return [
        'two_fa' => ['class' => TwoFaBehavior::class]
    ];
}
```

Usage
------------

Congratulations, you can now, for example, call
```
Yii::$app->twofa->generateSecret()
```
or
```
Yii::$app->twofa->checkCode($secret, $code);
```

Use
```
promocat\models\TwoFaForm
```
for the 2FA activation and verification forms. Or at least let is be an example. 

QR Code Widget
------------
```
<?= TwoFaQr::widget([
    'accountName' => $model->user->username,
    'secret' => $model->secret,
    'issuer' => Yii::$app->params['twoFaIssuer'],
    'size' => 300
]); ?>
```

How to functionally implement:
------------
See "example.php" to get started.