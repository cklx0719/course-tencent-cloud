<?php

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\User as UserService;
use App\Models\Role as RoleModel;

/**
 * @RoutePrefix("/admin/user")
 */
class UserController extends Controller
{

    /**
     * @Get("/search", name="admin.user.search")
     */
    public function searchAction()
    {
        $userService = new UserService();

        $eduRoleTypes = $userService->getEduRoleTypes();
        $adminRoles = $userService->getAdminRoles();

        $this->view->setVar('edu_role_types', $eduRoleTypes);
        $this->view->setVar('admin_roles', $adminRoles);
    }

    /**
     * @Get("/list", name="admin.user.list")
     */
    public function listAction()
    {
        $userService = new UserService();

        $pager = $userService->getUsers();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/add", name="admin.user.add")
     */
    public function addAction()
    {
        $userService = new UserService();

        $adminRoles = $userService->getAdminRoles();

        $this->view->setVar('admin_roles', $adminRoles);
    }

    /**
     * @Post("/create", name="admin.user.create")
     */
    public function createAction()
    {
        $adminRole = $this->request->getPost('admin_role', 'int', 0);

        if ($adminRole == RoleModel::ROLE_ROOT) {
            $this->response->redirect(['action' => 'list']);
        }

        $userService = new UserService();

        $userService->createUser();

        $location = $this->url->get(['for' => 'admin.user.list']);

        $content = [
            'location' => $location,
            'msg' => '新增用户成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.user.edit")
     */
    public function editAction($id)
    {
        $userService = new UserService();

        $user = $userService->getUser($id);
        $account = $userService->getAccount($id);
        $adminRoles = $userService->getAdminRoles();

        if ($user->admin_role == RoleModel::ROLE_ROOT) {
            $this->response->redirect(['for' => 'admin.user.list']);
        }

        $this->view->setVar('user', $user);
        $this->view->setVar('account', $account);
        $this->view->setVar('admin_roles', $adminRoles);
    }

    /**
     * @Get("/{id:[0-9]+}/online", name="admin.user.online")
     */
    public function onlineAction($id)
    {
        $userService = new UserService();

        $pager = $userService->getOnlineLogs($id);

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.user.update")
     */
    public function updateAction($id)
    {
        $adminRole = $this->request->getPost('admin_role', 'int', 0);

        if ($adminRole == RoleModel::ROLE_ROOT) {
            $this->response->redirect(['action' => 'list']);
        }

        $type = $this->request->getPost('type', 'string', 'user');

        $userService = new UserService();

        if ($type == 'user') {
            $userService->updateUser($id);
        } else {
            $userService->updateAccount($id);
        }

        $content = ['msg' => '更新用户成功'];

        return $this->jsonSuccess($content);
    }

}
