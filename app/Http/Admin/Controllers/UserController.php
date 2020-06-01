<?php

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\User as UserService;

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

        $roles = $userService->getRoles();

        $this->view->setVar('roles', $roles);
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
     * @Get("/{id:[0-9]+}/show", name="admin.user.show")
     */
    public function showAction($id)
    {

    }

    /**
     * @Get("/add", name="admin.user.add")
     */
    public function addAction()
    {
        $userService = new UserService();

        $roles = $userService->getRoles();

        $this->view->setVar('roles', $roles);
    }

    /**
     * @Post("/create", name="admin.user.create")
     */
    public function createAction()
    {
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
        $roles = $userService->getRoles();

        $this->view->setVar('user', $user);
        $this->view->setVar('account', $account);
        $this->view->setVar('roles', $roles);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.user.update")
     */
    public function updateAction($id)
    {
        $type = $this->request->getPost('type');

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
