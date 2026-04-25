<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
// services imports
use App\Services\JobTitleService;
// traits imports
use App\Traits\ApiResponseTrait;
// models imports
use App\Models\JobTitle;


class JobTitleController extends Controller
{
    
    use ApiResponseTrait;
    public function __construct(private readonly JobTitleService $jobTitleService)
    {
    }
    public function index() : JsonResponse
    {
        $jobTitles = $this->jobTitleService->getAllJobTitles();
        return $this->successResponse($jobTitles, 'Job titles retrieved successfully');
        
    }

    public function store(Request $request)
    {
        $jobTitle = $this->jobTitleService->createJobTitle($request->all());
        return $this->successResponse($jobTitle, 'Job title created successfully', 201);
        
    }
    public function destroy(int $id)
    {
        $jobTitle = $this->jobTitleService->deleteJobTitle($id);
        return $this->successResponse($jobTitle, 'Job title deleted successfully');
    }
}
