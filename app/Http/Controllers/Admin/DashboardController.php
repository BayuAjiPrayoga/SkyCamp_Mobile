<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    /**
     * Display the admin dashboard
     */
    public function index()
    {
        return view('admin.dashboard', $this->dashboardService->getDashboardData());
    }
}
