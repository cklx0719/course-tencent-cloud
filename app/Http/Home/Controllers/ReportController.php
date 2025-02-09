<?php

namespace App\Http\Home\Controllers;

use App\Services\Logic\Report\ReasonList as ReasonListService;
use App\Services\Logic\Report\ReportCreate as ReportCreateService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/report")
 */
class ReportController extends Controller
{

    /**
     * @Get("/add", name="home.report.add")
     */
    public function addAction()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $service = new ReasonListService();

        $reasons = $service->handle();

        $this->view->setVar('reasons', $reasons);
    }

    /**
     * @Post("/create", name="home.report.create")
     */
    public function createAction()
    {
        $service = new ReportCreateService();

        $service->handle();

        return $this->jsonSuccess(['msg' => '举报成功']);
    }

}
