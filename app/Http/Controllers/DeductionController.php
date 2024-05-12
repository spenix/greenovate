<?php

namespace App\Http\Controllers;

use App\Models\{ParamDeduction, Deduction, Employee};
use App\Http\Requests\{StoreDeductionRequest, UpdateDeductionRequest};
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\{Redirect, Validator};
class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Deductions/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'deductions' => ParamDeduction::where('status', 'Y')->get(),
            'employees' => Employee::selectRaw('id, CONCAT(firstname, " ", lastname) as employee_name')
            ->whereNull('termination_date')
            ->get()
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = ParamDeduction::selectRaw('param_deductions.*, IF(param_deductions.status = "Y", "Active", "Inactive") status')->get();
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }


    public function show_personnel_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Employee::with(['deductions', 'compensations'])
            ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('departments', 'departments.id', 'employees.department_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw('employees.*, employee_types.name as employee_type, departments.name as department, designations.name as designation, CONCAT(employees.firstname, " ", employees.lastname) as employee_name')
            ->get();
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function show_deduction_manage_table(Request $request)
    {
        if ($request->ajax()) {
            $data = Deduction::join('employees', 'employees.id', 'deductions.employee_id')
            ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('departments', 'departments.id', 'employees.department_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw('deductions.id, deductions.date_start start_date,  deductions.date_end end_date, employee_types.name as employee_type, departments.name as department, designations.name as designation, CONCAT(employees.firstname, " ", employees.lastname) as employee_name')
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
    public function store(StoreDeductionRequest $request)
    {
        try {
            $payload = ['name' => $request->deduction];
            //code...
            $isExist = ParamDeduction::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'deduction' => 'The deduction has already been taken.',
                ])->onlyInput('deduction');
            }
            $payload['short_code'] = $request->short_code;
            $payload['amount'] = $request->amount;
            $payload['status'] = $request->status;
            ParamDeduction::create($payload);
            return Redirect::route('deductions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function store_emp_setup(Request $request)
    {
        try {
            $validated = Validator::make([
                'deduction' => $request->deduction,
                'employee' => $request->employee,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'amount' => str_replace(",", "", $request->amount),
                'isPresent' => $request->isPresent,
            ], [
                'deduction' => 'required|integer|exists:param_deductions,id',
                'employee' => 'required|integer|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'amount' => 'required|numeric',
                'isPresent' => 'required|boolean',
            ])->validate();
           
            if (!$validated['isPresent'] && is_null($validated['end_date'])) {
                return back()->withErrors([
                    'end_date' => 'The end date is required.',
                ])->onlyInput('end_date');
            }
            $payload = [
                'param_deduction_id' => $validated['deduction'],
                'employee_id' => $validated['employee'],
            ];
            //code...
            $isExist = Deduction::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'employee' => 'The employee has already been setup.',
                ])->onlyInput('employee');
            }
            $payload['date_start'] = $validated['start_date'];
            $payload['date_end'] = $validated['end_date'];
            Deduction::create($payload);
            return Redirect::route('deductions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ParamDeduction $deduction, $id)
    {
        $data = $deduction::find($id);
        return response()->json($data);
    }

    public function show_employee_details(Employee $employee, $id)
    {
        $data = $employee::with(['compensations.benefit', 'deductions.deduction_details'])
        ->join('designations', 'designations.id', 'employees.designation_id')
        ->join('departments', 'departments.id', 'designations.department_id')
        ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
        ->selectRaw("employees.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_types.name as employee_type, designations.name designation, departments.name department")
        ->find($id);
        return response()->json($data);
    }

    public function show_employee_setup(Deduction $deduction, $id)
    {
        $data = $deduction::find($id);
        return response()->json($data);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParamDeduction $deduction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeductionRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->deduction];
            //code...
            $isExist = ParamDeduction::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'deduction' => 'The deduction has already been taken.',
                ])->onlyInput('deduction');
            }
            $payload['status'] = $request->status;
            ParamDeduction::find($id)->update($payload);
            return Redirect::route('deductions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function update_emp_setup(Request $request, $id)
    {
        try {
            $validated = Validator::make([
                'deduction' => $request->deduction,
                'employee' => $request->employee,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'amount' => str_replace(",", "", $request->amount),
                'isPresent' => $request->isPresent,
            ], [
                'deduction' => 'required|integer|exists:param_deductions,id',
                'employee' => 'required|integer|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'amount' => 'required|numeric',
                'isPresent' => 'required|boolean',
            ])->validate();
            $payload = [
                'param_deduction_id' => $validated['deduction'],
                'employee_id' => $validated['employee'],
                'date_end' => $validated['end_date']
            ];
            if (!$validated['isPresent'] && is_null($validated['end_date'])) {
                return back()->withErrors([
                    'end_date' => 'The end date is required.',
                ])->onlyInput('end_date');
            } else {
                if ($validated['isPresent'] && !is_null($validated['end_date']))
                    $payload['date_end'] = null;
            }
            //code...
            $isExist = Deduction::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'employee' => 'The employee has already been setup.',
                ])->onlyInput('employee');
            }
            $payload['date_start'] = $validated['start_date'];
            Deduction::find($id)->update($payload);
            return Redirect::route('deductions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParamDeduction $deduction, $id)
    {
        try {
            $deduction::find($id)->delete();
            return Redirect::route('deductions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function destroy_deduction(Deduction $deduction, $id)
    {
        try {
            $deduction::find($id)->delete();
            return Redirect::route('deductions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
