<?php

namespace App\Http\Controllers;

use App\Models\{ParamDeduction, Deduction, Employee};
use App\Http\Requests\{StoreDeductionRequest, UpdateDeductionRequest};
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\Redirect;
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

    /**
     * Display the specified resource.
     */
    public function show(ParamDeduction $deduction, $id)
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
}
