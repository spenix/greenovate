<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use App\Models\Employee;
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
        $data = Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')
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
            $payload = [];
            foreach ($request->payroll_records as $key => $value) {
                $payload[] = [
                    "employee_id" => $value['employee_id'],
                    "payment_rate" => $value['payment_rate'],
                    "payment_start" => $value['payment_start'],
                    "payment_period" => $value['payment_period'],
                    "reg_hours" => $value['reg_hours'],
                    "reg_hour_rate" => $value['reg_hour_rate'],
                    "ot_hours" => $value['ot_hours'],
                    "ot_hour_rate" => $value['ot_hour_rate'],
                    "philhealth" => $value['philhealth'],
                    "tin" => $value['tin'],
                    "sss" => $value['sss'],
                    "pag_ibig" => $value['pag_ibig'],
                    "quarterly" => $value['quarterly'],
                    "year_end" => $value['year_end'],
                    "created_at" => now(),
                ];
            }

            // //code...
            // $isExist = Payroll::where($payload)->count();
            // if ($isExist) {
            //     return back()->withErrors([
            //         'errorMessage' => 'The leave type has already been taken.',
            //     ]);
            // }
            Payroll::insert($payload);
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
        $data = $payroll::join('employees', 'employees.id', 'payrolls.employee_id')
            ->selectRaw("payrolls.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_no")
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
            foreach ($request->payroll_records as $key => $value) {
                $payload = [
                    "employee_id" => $value['employee_id'],
                    "payment_rate" => $value['payment_rate'],
                    "payment_start" => $value['payment_start'],
                    "payment_period" => $value['payment_period'],
                    "reg_hours" => $value['reg_hours'],
                    "reg_hour_rate" => $value['reg_hour_rate'],
                    "ot_hours" => $value['ot_hours'],
                    "ot_hour_rate" => $value['ot_hour_rate'],
                    "philhealth" => $value['philhealth'],
                    "tin" => $value['tin'],
                    "sss" => $value['sss'],
                    "pag_ibig" => $value['pag_ibig'],
                    "quarterly" => $value['quarterly'],
                    "year_end" => $value['year_end'],
                    "created_at" => now(),
                ];
                Payroll::find($id)->update($payload);
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
    public function destroy(Payroll $payroll)
    {
        //
    }
}
