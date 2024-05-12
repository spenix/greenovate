<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use App\Models\Employee;
use Illuminate\Support\Facades\Redirect;

class LeaveHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('LeaveHistory/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Employee::join('employee_leave_credits', 'employee_leave_credits.employee_id', 'employees.id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->join('departments', 'departments.id', 'designations.department_id')
            ->selectRaw("
                employees.*, 
                CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, 
                designations.name designation, 
                departments.name department,
                employee_leave_credits.leave_credit running_balance
            ")
            ->get();
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee, string $id)
    {
        $data = $employee::with(['leave_records.leave_type', 'leave_records.leave_entitlement'])
        ->join('designations', 'designations.id', 'employees.designation_id')
        ->join('departments', 'departments.id', 'designations.department_id')
        ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
        ->selectRaw("employees.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_types.name as employee_type, designations.name designation, departments.name department")
        ->find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
