<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Resources\EmployeeResource;

class EmployeeController extends Controller
{
    public function index(Request $request)
{
    $query = Employee::query();
    $query->when($request->filled('search'), function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
    });
    $query->when($request->filled('type'), function($q) use ($request) {
        $type = $request->type;
        $mainTypes = ['full_time', 'part_time', 'internship'];
        if (in_array($type, $mainTypes)) {
            $q->where('employment_type', $type);
        } 
        elseif ($type === 'freelance') {
            $q->where('is_freelance', true);
        }
    });

    $employees = $query->latest()->paginate(15);

    return response()->json([
        'message' => 'Employees fetched successfully',
        'data' => EmployeeResource::collection($employees),
        'meta' => [
            'current_page' => $employees->currentPage(),
            'last_page' => $employees->lastPage(),
            'total' => $employees->total(),
        ]
    ], 200);
}
}
