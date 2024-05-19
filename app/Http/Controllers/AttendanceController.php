<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\AttendanceAttachment;
use App\Models\Employee;
use App\Models\{Shift, ShiftCode};
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\Redirect;
// use Excel;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw("
                employees.*, 
                CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, 
                employee_types.name as employee_type, 
                designations.name designation
            ")->where('terminate', 'N')->get();
        return Inertia::render('Attendance/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'employees' => $data,
            'shift_codes' => ShiftCode::join('shifts', 'shifts.id', 'shift_codes.shift_id')
            ->where('shift_codes.status', 'Y')
            ->selectRaw('shift_codes.*, shifts.name as shift')
            ->get(),
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Attendance::with(['attachments'])->join('employees', 'employees.id', 'attendances.employee_id')
                ->join('shift_codes', 'shift_codes.id', 'attendances.shift_code_id')
                ->join('shifts', 'shifts.id', 'shift_codes.shift_id')
                ->selectRaw("
                    attendances.*, 
                    employees.employee_no,
                    CONCAT(employees.firstname, ' ', employees.lastname) as employee_name,
                    shifts.name shift
                ");
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function show_dtr_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = AttendanceAttachment::where('attendance_id', $request->attendance_id)->get();
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function upload_attendance(Request $request)
    {
        try {
            $request->validate([
                'period_start' => 'required|date',
                'period_end' => 'required|date',
                'file_upload' => 'required|mimes:pdf,xlsx,xlsm,xls,xml,txt|max:2048',
                'id' => 'required',
            ]);

            $image_path = '';
            if ($request->hasFile('file_upload')) {
                $fileName = time() . '_' . $request->file_upload->getClientOriginalName();
                $image_path = $request->file('file_upload')->storeAs('attendance-attachments', $fileName, 'public');
                $request->file_upload->move(public_path('attendance-attachments'), $fileName);
            }
            $payload = [
                'attendance_id' => $request->id,
                'attachment_path' => $image_path,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
            ];

            AttendanceAttachment::create($payload);
            return Redirect::route('employee-shift');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
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
    public function store(StoreAttendanceRequest $request)
    {
        try {
            $payload = [
                'employee_id' => $request->employee_id,
            ];

            $cnt = Attendance::where($payload)->count();

            if ($cnt) {
                return back()->withErrors([
                    'employee_id' => 'The employee was already exists, please check and try again.',
                ])->onlyInput('employee_id');
            }
            $payload['shift_code_id'] = $request->shift_code;
            Attendance::create($payload);
            return Redirect::route('employee-shift');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance, $id)
    {
        $data = $attendance::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, $id)
    {
        try {
            $payload = [
                'employee_id' => $request->employee_id,
            ];
            //code...
            $isExist = Attendance::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'employee_id' => 'The employee was already exists, please check and try again.',
                ])->onlyInput('employee_id');
            }
            $payload['shift_code_id'] = $request->shift_code;
            Attendance::find($id)->update($payload);
            return Redirect::route('employee-shift');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance, $id)
    {
        try {
            $attendance::find($id)->delete();
            return Redirect::route('employee-shift');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
