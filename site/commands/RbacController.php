<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\FileHelper;
use Yii;

use app\models\User;

class RbacController extends Controller
{
    public function actionInit()
    {

        $dir = Yii::getAlias('@app/rbac');
        if (!file_exists($dir)) {
            FileHelper::createDirectory($dir);
        }

        $auth = Yii::$app->authManager;

        $auth->removeAll();

        foreach (User::ROLES as $key => $descr) {
            $role = $auth->createRole($key);
            $role->description = $descr;
            $auth->add($role);
        }

        // добавляем право .. всё могу ... всё умею ...
        $perm = $auth->createPermission(User::PERM_ALLRULE);
        $perm->description = 'Всё могу всё умею!';
        $auth->add($perm);
        $roleAdmin = $auth->getRole(User::ROLE_ADMIN);
        $auth->addChild($roleAdmin, $perm);

        // назначение прав ...
        foreach (User::getIdsPerRole(User::ROLE_ADMIN) as $uid) {
            $auth->assign($roleAdmin, $uid);
        }
    }

}