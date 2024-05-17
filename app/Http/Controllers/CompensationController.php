<?php

namespace App\Http\Controllers;

use App\Models\{Compensation, ParamBenefit, Employee};
use App\Http\Requests\{StoreCompensationRequest, UpdateCompensationRequest};
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\{Redirect, Validator};
class CompensationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('Compensation/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'benefits' => ParamBenefit::where('status', 'Y')->get(),
            'employees' => Employee::selectRaw('id, CONCAT(firstname, " ", lastname) as employee_name')
            ->whereNull('termination_date')
            ->get()
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = ParamBenefit::selectRaw('param_benefits.*, IF(param_benefits.status = "Y", "Active", "Inactive") status')->get();
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
            ->whereNull('termination_date')
            ->get();
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function show_compensation_manage_table(Request $request)
    {
        if ($request->ajax()) {
            $data = Compensation::join('employees', 'employees.id', 'compensation.employee_id')
            ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('departments', 'departments.id', 'employees.department_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw('compensation.id, compensation.start_date, compensation.end_date, employee_types.name as employee_type, departments.name as department, designations.name as designation, CONCAT(employees.firstname, " ", employees.lastname) as employee_name')
            ->get();
            if ($request->id) {
                $data->where('compensation.param_benefit_id', $request->id);
            }
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
    public function store(StoreCompensationRequest $request)
    {
        try {
            $payload = ['name' => $request->compensation];
            //code...
            $isExist = ParamBenefit::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'compensation' => 'The compensation has already been taken.',
                ])->onlyInput('compensation');
            }
            $payload['short_code'] = $request->short_code;
            $payload['amount'] = $request->amount;
            $payload['status'] = $request->status;
            ParamBenefit::create($payload);
            return Redirect::route('compensations');
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
                'benefit' => $request->benefit,
                'employee' => $request->employee,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'amount' => str_replace(",", "", $request->amount),
                'isPresent' => $request->isPresent,
            ], [
                'benefit' => 'required|integer|exists:param_benefits,id',
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
                'param_benefit_id' => $validated['benefit'],
                'employee_id' => $validated['employee'],
            ];
            //code...
            $isExist = Compensation::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'employee' => 'The employee has already been setup.',
                ])->onlyInput('employee');
            }
            $payload['start_date'] = $validated['start_date'];
            $payload['end_date'] = $validated['end_date'];
            Compensation::create($payload);
            return Redirect::route('compensations');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ParamBenefit $compensation, $id)
    {
        $data = $compensation::with(['emp_with_benefits'])->find($id);
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

    public function show_employee_setup(Compensation $compensation, $id)
    {
        $data = $compensation::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParamBenefit $compensation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompensationRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->compensation];
            //code...
            $isExist = ParamBenefit::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'compensation' => 'The compensation has already been taken.',
                ])->onlyInput('compensation');
            }
            $payload['status'] = $request->status;
            ParamBenefit::find($id)->update($payload);
            return Redirect::route('compensations');
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
                'benefit' => $request->benefit,
                'employee' => $request->employee,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'amount' => str_replace(",", "", $request->amount),
                'isPresent' => $request->isPresent,
            ], [
                'benefit' => 'required|integer|exists:param_benefits,id',
                'employee' => 'required|integer|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'amount' => 'required|numeric',
                'isPresent' => 'required|boolean',
            ])->validate();
            $payload = [
                'param_benefit_id' => $validated['benefit'],
                'employee_id' => $validated['employee'],
                'end_date' => $validated['end_date']
            ];
            if (!$validated['isPresent'] && is_null($validated['end_date'])) {
                return back()->withErrors([
                    'end_date' => 'The end date is required.',
                ])->onlyInput('end_date');
            } else {
                if ($validated['isPresent'] && !is_null($validated['end_date']))
                    $payload['end_date'] = null;
            }
            //code...
            $isExist = Compensation::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'employee' => 'The employee has already been setup.',
                ])->onlyInput('employee');
            }
            $payload['start_date'] = $validated['start_date'];
            
            Compensation::find($id)->update($payload);
            return Redirect::route('compensations');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParamBenefit $compensation, $id)
    {
        try {
            $compensation::find($id)->delete();
            return Redirect::route('compensations');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function destroy_compensation(Compensation $compensation, $id)
    {
        try {
            $compensation::find($id)->delete();
            return Redirect::route('compensations');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
