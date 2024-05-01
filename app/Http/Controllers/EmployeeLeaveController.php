<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeave;
use App\Http\Requests\StoreEmployeeLeaveRequest;
use App\Http\Requests\UpdateEmployeeLeaveRequest;
use App\Models\Employee;
use App\Models\EmployeeLeaveCredit;
use App\Models\LeaveCredit;
use App\Models\LeaveType;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EmployeeLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw("employees.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_types.name as employee_type, designations.name designation")->get();
        $leaveTypes = LeaveType::join('leave_entitlements', 'leave_entitlements.id', 'leave_types.leave_entitlement_id')
            ->selectRaw("leave_types.*, leave_entitlements.name leave_entitlement, leave_entitlements.id leave_entitlement_id")
            ->get();
        return Inertia::render('EmployeeLeave/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'employees' => $data,
            'leaveTypes' => $leaveTypes
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = EmployeeLeave::join('employees', 'employees.id', 'employee_leaves.employee_id')
                ->join('leave_types', 'leave_types.id', 'employee_leaves.leave_type_id')
                ->join('leave_entitlements', 'leave_entitlements.id', 'leave_types.leave_entitlement_id')
                ->selectRaw("
                    employee_leaves.*, 
                    leave_types.name leave_type, 
                    leave_entitlements.name leave_entitlement,
                    employees.employee_no,
                    CONCAT(employees.firstname, ' ', employees.lastname) as employee_name
                ");
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
    public function store(StoreEmployeeLeaveRequest $request)
    {
        try {

            $EmpLeaveCredit = EmployeeLeaveCredit::where(['year_applicable' => date('Y'), "employee_id" => $request->employee_name])->first();
            $payload = [
                "employee_id" => $request->employee_name,
                "leave_type_id" => $request->leave_type,
                "leave_entitlement_id" => $request->leave_entitlement_id,
                "date_start" => $request->date_start,
                "date_end" => $request->date_end,
                "leave_days" => $request->leave_days,
                "running_balance" => $EmpLeaveCredit->leave_credit - $request->leave_days,
            ];
            //code...
            // check Leave Credit Balance
            if (date('Y', strtotime($request->date_start)) != $EmpLeaveCredit->year_applicable) {
                return back()->withErrors([
                    'errorMessage' => 'Oops, employee leave credit is not set for the year ' . date('Y', strtotime($request->date_start)),
                ]);
            }

            if ($request->leave_days > $EmpLeaveCredit->leave_credit) {
                return back()->withErrors([
                    'leave_days' => 'Oops, employee available balance is only ' . $EmpLeaveCredit->leave_credit . ' ' . ($EmpLeaveCredit->leave_credit > 1 ? 'days.' : 'day.'),
                ])->onlyInput('leave_days');
            }
            $isExist = EmployeeLeave::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The employee leave form was already exists.',
                ]);
            }

            if ($request->date_start > $request->date_end) {
                return back()->withErrors([
                    'errorMessage' => 'Invalid date inputs, please check and try again.',
                ]);
            }
            $data = EmployeeLeave::create($payload);
            if ($data->id) {
                EmployeeLeaveCredit::where([
                    'year_applicable' => date('Y'),
                    "employee_id" => $data->employee_id
                ])->update(['leave_credit' => $data->running_balance]);
            }

            return Redirect::route('employee-leaves');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeLeave $employeeLeave)
    {
        $data = EmployeeLeave::join('employees', 'employees.id', 'employee_leaves.employee_id')
            ->join('leave_types', 'leave_types.id', 'employee_leaves.leave_type_id')
            ->join('leave_entitlements', 'leave_entitlements.id', 'leave_types.leave_entitlement_id')
            ->selectRaw("
            employee_leaves.*, 
            leave_types.name leave_type, 
            leave_entitlements.name leave_entitlement,
            employees.employee_no,
            CONCAT(employees.firstname, ' ', employees.lastname) as employee_name
        ");

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeLeave $employeeLeave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeLeaveRequest $request, EmployeeLeave $employeeLeave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeLeave $employeeLeave, $id)
    {
        try {
            $employeeLeave::find($id)->delete();
            return Redirect::route('employee-leaves');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
