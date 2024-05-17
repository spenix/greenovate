<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use App\Models\{Employee, PayrollCompensation, PayrollDeduction};
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Employee::with(['deductions.deduction_details', 'compensations.benefit', 'leave_records.leave_type', 'leave_records.leave_entitlement', 'attendance_records.attachments'])
            ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw("
                employees.*, 
                CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, 
                employee_types.name as employee_type, 
                designations.name designation
            ")->where('terminate', 'N')->get();
        return Inertia::render('Payroll/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appFullName' => config('app.name', 'Laravel'),
                'appAddress' => config('app.company_address', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'employees' => $data
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Payroll::join('employees', 'employees.id', 'payrolls.employee_id')
                ->selectRaw("payrolls.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_no");
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
    public function store(StorePayrollRequest $request)
    {
        try {
            $payload = [
                'employee_id' => $request->employee,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
            ];
            //code...
            $isExist = Payroll::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The employee payroll already been setup.',
                ]);
            }
            $payload['basic_salary'] = $request->basic_salary ?? 0;
            $payload['working_hours'] = $request->working_hours ?? 0;
            $payload['working_days'] = $request->working_days ?? 0;
            $payload['ot_hours'] = $request->ot_hours ?? 0;
            $payload['ot_compensation'] = $request->ot_compensation ?? 0;
            $payroll = Payroll::create($payload);
            if ($payroll->id) {
                $deductions = [];
                foreach ($request->deductions as $key => $value) {
                    $deductions[] = [
                        'payroll_id' => $payroll->id,
                        'deduction_id' => $value['id'],
                        'amount' => $value['amount'],
                        'created_at' => now()
                    ];
                }
                PayrollDeduction::insert($deductions);
                $compensation = [];
                foreach ($request->compensation as $key => $value) {
                    $compensation[] = [
                        'payroll_id' => $payroll->id,
                        'compensation_id' => $value['id'],
                        'amount' => $value['amount'],
                        'created_at' => now()
                    ];
                }
                PayrollCompensation::insert($compensation);
            }
            return Redirect::route('payroll');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll, $id)
    {
        $data = $payroll::with(['deductions.deduction_details', 'compensations.benefit'])
            ->join('employees', 'employees.id', 'payrolls.employee_id')
            ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw("
                payrolls.*,
                employees.date_hired,
                CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, 
                employee_no,
                employee_types.name as employee_type, 
                designations.name designation
                ")
            ->find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePayrollRequest $request, $id)
    {
        try {
            $payload = [
                'employee_id' => $request->employee,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
            ];
            //code...
            $isExist = Payroll::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The employee payroll already been setup.',
                ]);
            }
            $payload['basic_salary'] = $request->basic_salary ?? 0;
            $payload['working_hours'] = $request->working_hours ?? 0;
            $payload['working_days'] = $request->working_days ?? 0;
            $payload['ot_hours'] = $request->ot_hours ?? 0;
            $payload['ot_compensation'] = $request->ot_compensation ?? 0;
            $payroll = Payroll::find($id)->update($payload);
            if ($payroll) {
                $deductions = [];
                PayrollDeduction::where('payroll_id', $id)->delete();
                foreach ($request->deductions as $key => $value) {
                    $deductions[] = [
                        'payroll_id' => $id,
                        'deduction_id' => $value['id'],
                        'amount' => $value['amount'],
                        'created_at' => now()
                    ];
                }
                PayrollDeduction::insert($deductions);

                $compensation = [];
                PayrollCompensation::where('payroll_id', $id)->delete();
                foreach ($request->compensation as $key => $value) {
                    $compensation[] = [
                        'payroll_id' => $id,
                        'compensation_id' => $value['id'],
                        'amount' => $value['amount'],
                        'created_at' => now()
                    ];
                }
                PayrollCompensation::insert($compensation);
            }
            return Redirect::route('payroll');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll, $id)
    {
        try {
            $payroll::find($id)->delete();
            return Redirect::route('payroll');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
