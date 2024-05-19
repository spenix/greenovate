<?php

namespace App\Http\Controllers;

use App\Models\{AttendanceRecord, Department, ShiftCode, Attendance};
use App\Http\Requests\StoreAttendanceRecordRequest;
use App\Http\Requests\UpdateAttendanceRecordRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\{Redirect, Validator};
use Carbon\Carbon;

class AttendanceRecordController extends Controller
{
    public function __construct()
    {
        $this->dt = Carbon::now()->setTimezone('Asia/Manila');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dt = $this->dt;
        return Inertia::render('AttendanceRecord/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'departments' => Department::where('status', 'Y')->get(),
            'shift_codes' => ShiftCode::join('shifts', 'shifts.id', 'shift_codes.shift_id')
            ->where('shift_codes.status', 'Y')
            ->selectRaw('shift_codes.id, shifts.name')
            ->get(),
            'date_today' => $dt->toDateString()
        ]);
    }

    public function get_employees_with_shift(Request $request)
    {
        try{
            $request->validate([
                'department' => 'nullable|integer|exists:departments,id',
                'shift_code' => 'nullable|integer|exists:shift_codes,id',
                'filter_date' => 'required|date',
            ]);
            $where = [];
            if ($request->department) {
                $where['employees.department_id'] = $request->department;
            }

            if ($request->shift_code) {
                $where['attendances.shift_code_id'] = $request->shift_code;
            }
           
            $dt = $this->dt;
            $data = Attendance::join('employees', 'employees.id', 'attendances.employee_id')
            ->join('departments', 'departments.id', 'employees.department_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->join('shift_codes', 'shift_codes.id', 'attendances.shift_code_id')
            ->join('shifts', 'shifts.id', 'shift_codes.shift_id')
            ->selectRaw('
                attendances.employee_id, 
                employees.employee_no, 
                CONCAT(employees.firstname, " ", employees.lastname) as employee_name, 
                TIME_FORMAT(shift_codes.clock_in, "%h:%i %p") clock_in,
                TIME_FORMAT(shift_codes.break_out, "%h:%i %p") break_out,
                TIME_FORMAT(shift_codes.break_in, "%h:%i %p") break_in,
                TIME_FORMAT(shift_codes.clock_out, "%h:%i %p") clock_out,
                shift_codes.days,
                shifts.name,
                departments.name as department,
                designations.name as designation
            ');
            if (count($where)) {
                $data->where($where);
            }
            if ($request->filter_date) {
                $filter_dt = new Carbon($request->filter_date);
                $data->where('shift_codes.days', 'like', '%'.$filter_dt->format('l').'%');
            }
            $result = [];
            foreach ($data->get() as $key => $value) {
                $att_rec = AttendanceRecord::where('employee_id', $value['employee_id'])
                            ->whereDate('date_in', $request->filter_date)
                            ->selectRaw('TIME_FORMAT(clock_in, "%h:%i %p") clock_in, TIME_FORMAT(break_out, "%h:%i %p") break_out, TIME_FORMAT(break_in, "%h:%i %p") break_in, TIME_FORMAT(clock_out, "%h:%i %p") clock_out')
                            ->first();
                $log_record = [];
                if ($att_rec) {
                    $log_record = $att_rec;
                }
                $value['log_record'] = $log_record;
                $result[] = $value;
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function log_process(Request $request)
    {
        $dt = $this->dt;
        $msg = "";
        $db_column = "";
        try{
            $code = ['TI' => 'Time-In', 'BO' => 'Break-Out', 'BI' => 'Break-In', 'TO' => 'Time-Out'];
            if ($request->log_action == 'TI') {
                $db_column = "clock_in";
            }
            if ($request->log_action == 'BO') {
                $db_column = "break_out";
            }
            if ($request->log_action == 'BI') {
                $db_column = "break_in";
            }
            if ($request->log_action == 'TO') {
                $db_column = "clock_out";
            }
            $att_rec = AttendanceRecord::where(['employee_id' => $request->emp_id, 'date_in' => $request->filter_date]);
            if ($att_rec->count()) {
                $att_rec->update([ $db_column => $dt->toTimeString() ]);
            } else {
                $payload = [ 'employee_id' => $request->emp_id, 'date_in' => $dt->toDateString(), $db_column =>  $dt->toTimeString()];
                AttendanceRecord::create($payload);
            }
            $msg = $code[$request->log_action]." was successfully log.";
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
        }

        return response()->json(['msg' => $msg]);
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
    public function store(StoreAttendanceRecordRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceRecord $attendanceRecord, $id)
    {
        $data = Attendance::join('employees', 'employees.id', 'attendances.employee_id')
        ->join('departments', 'departments.id', 'employees.department_id')
        ->join('designations', 'designations.id', 'employees.designation_id')
        ->join('shift_codes', 'shift_codes.id', 'attendances.shift_code_id')
        ->join('shifts', 'shifts.id', 'shift_codes.shift_id')
        ->selectRaw('
            attendances.employee_id, 
            employees.employee_no, 
            CONCAT(employees.firstname, " ", employees.lastname) as employee_name, 
            TIME_FORMAT(shift_codes.clock_in, "%h:%i %p") clock_in,
            TIME_FORMAT(shift_codes.break_out, "%h:%i %p") break_out,
            TIME_FORMAT(shift_codes.break_in, "%h:%i %p") break_in,
            TIME_FORMAT(shift_codes.clock_out, "%h:%i %p") clock_out,
            shift_codes.days,
            shifts.name shift,
            departments.name as department,
            designations.name as designation
        ')->where('attendances.employee_id', $id)->first();
        return response()->json($data);
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
    public function update(UpdateAttendanceRecordRequest $request, AttendanceRecord $attendanceRecord)
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
