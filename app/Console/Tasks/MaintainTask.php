<?php

namespace App\Console\Tasks;

use App\Http\Admin\Services\Setting as SettingService;
use App\Library\Utils\Password as PasswordUtil;
use App\Services\Utils\IndexCourseCache as IndexCourseCacheUtil;
use App\Validators\Account as AccountValidator;

class MaintainTask extends Task
{

    /**
     * 重建首页课程缓存
     *
     * @param array $params
     * @command: php console.php maintain rebuild_index_course_cache
     */
    public function rebuildIndexCourseCacheAction($params)
    {
        $section = $params[0] ?? null;

        $util = new IndexCourseCacheUtil();

        $util->rebuild($section);

        echo '------ rebuild index course cache success ------' . PHP_EOL;
    }

    /**
     * 修改密码
     *
     * @param array $params
     * @command: php console.php maintain reset_password 13507083515 123456
     */
    public function resetPasswordAction($params)
    {
        if (empty($params[0])) {
            echo 'account is required' . PHP_EOL;
        }

        if (empty($params[1])) {
            echo 'password is required' . PHP_EOL;
        }

        $validator = new AccountValidator();

        $account = $validator->checkAccount($params[0]);

        $salt = PasswordUtil::salt();
        $hash = PasswordUtil::hash($params[1], $salt);

        $account->salt = $salt;
        $account->password = $hash;

        $account->update();

        echo '------ reset password success ------' . PHP_EOL;
    }

    /**
     * 关闭验证码
     *
     * @command: php console.php maintain disable_captcha
     */
    public function disableCaptchaAction()
    {
        $service = new SettingService();

        $service->updateSettings('captcha', ['enabled' => 0]);

        echo '------ disable captcha success ------' . PHP_EOL;
    }

    /**
     * 启用验证码
     *
     * @command: php console.php maintain enable_captcha
     */
    public function enableCaptchaAction()
    {
        $service = new SettingService();

        $service->updateSettings('captcha', ['enabled' => 1]);

        echo '------ enable captcha success ------' . PHP_EOL;
    }

    /**
     * 关闭站点
     *
     * @command: php console.php maintain disable_site
     */
    public function disableSiteAction()
    {
        $service = new SettingService();

        $service->updateSettings('site', ['status' => 'closed']);

        echo '------ disable site success ------' . PHP_EOL;
    }

    /**
     * 开启站点
     *
     * @command: php console.php maintain enable_site
     */
    public function enableSiteAction()
    {
        $service = new SettingService();

        $service->updateSettings('site', ['status' => 'normal']);

        echo '------ enable site success ------' . PHP_EOL;
    }

}
