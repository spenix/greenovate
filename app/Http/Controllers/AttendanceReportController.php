<?php

namespace App\Http\Controllers;

use App\Models\{AttendanceRecord, Department, ShiftCode, Employee, Attendance};
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\{Redirect, Validator};

class AttendanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')
        ->join('departments', 'departments.id', 'employees.department_id')
        ->join('designations', 'designations.id', 'employees.designation_id')
        ->selectRaw("
            employees.*, 
            CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, 
            employee_types.name as employee_type, 
            departments.name department,
            designations.name designation
        ")->where('terminate', 'N')->get();
        return Inertia::render('AttendanceReport/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'employees' => $data
        ]);
    }

    public function get_employee_dtr_rec(Request $request)
    {
        try{ 
            $data = AttendanceRecord::where('employee_id', $request->employee)
            ->whereDate('date_in', '>=', $request->period_start)
            ->whereDate('date_in', '<=', $request->period_end)
            ->selectRaw('date_in, TIME_FORMAT(clock_in, "%h:%i %p") clock_in, TIME_FORMAT(break_out, "%h:%i %p") break_out, TIME_FORMAT(break_in, "%h:%i %p") break_in, TIME_FORMAT(clock_out, "%h:%i %p") clock_out')
            ->get();
            return response()->json($data);
        } catch (\Throwable $e) {
            return $e->getMessage();
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
    public function show(AttendanceRecord $attendanceRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceRecord $attendanceRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceRecord $attendanceRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceRecord $attendanceRecord)
    {
        //
    }
}
