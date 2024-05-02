<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use App\Http\Requests\StorePerformanceRequest;
use App\Http\Requests\UpdatePerformanceRequest;
use App\Models\Employee;
use App\Models\ViolationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use DataTables;

class PerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->selectRaw("employees.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_types.name as employee_type, designations.name designation")->get();
        return Inertia::render('Performance/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'violationTypes' => ViolationType::get(),
            'employees' => $data,
            'attempLimits' => intval(config('app.attempLimits', 3))
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Performance::join('employees', 'employees.id', 'performances.employee_id')
                ->join('designations', 'designations.id', 'employees.designation_id')
                ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
                ->join('violation_types', 'violation_types.id', 'performances.violation_type_id')
                ->join('violation_categories', 'violation_categories.id', 'violation_types.violation_category_id')
                ->selectRaw("
                    performances.*, 
                    violation_types.name violation_type, 
                    violation_categories.name violation_category, 
                    designations.name designation,
                    employee_types.name employee_type,
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
    public function store(StorePerformanceRequest $request)
    {
        try {
            $payload = [
                'employee_id' => $request->employee_id,
                'violation_type_id' => $request->violation_type,
                'attemps' => $request->attemps,
                'date_committed' => $request->date_committed,
                'remark' => $request->remark,
            ];
            $isExist = Performance::where([
                'employee_id' => $request->employee_id,
                'violation_type_id' => $request->violation_type,
                'attemps' => $request->attemps,
            ])->count();

            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The performance data has already been exists.',
                ]);
            }
            Performance::create($payload);
            return Redirect::route('performance');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Performance $performance, $id)
    {

        $data = $performance::join('employees', 'employees.id', 'performances.employee_id')
            ->join('designations', 'designations.id', 'employees.designation_id')
            ->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->join('violation_types', 'violation_types.id', 'performances.violation_type_id')
            ->join('violation_categories', 'violation_categories.id', 'violation_types.violation_category_id')
            ->selectRaw("
            performances.*, 
            violation_types.name violation_type, 
            violation_categories.name violation_category, 
            designations.name designation,
            employee_types.name employee_type,
            employees.employee_no,
            CONCAT(employees.firstname, ' ', employees.lastname) as employee_name
        ")->find($id);;
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Performance $performance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerformanceRequest $request,  $id)
    {
        try {
            $payload = [
                'employee_id' => $request->employee_id,
                'violation_type_id' => $request->violation_type,
                'attemps' => $request->attemps,
                'date_committed' => $request->date_committed,
                'remark' => $request->remark,
            ];

            $isExist = Performance::where([
                'employee_id' => $request->employee_id,
                'violation_type_id' => $request->violation_type,
                'attemps' => $request->attemps,
            ])->where('id', '!=', $id)->count();

            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The performance data has already been exists.',
                ]);
            }
            Performance::find($id)->update($payload);
            return Redirect::route('performance');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Performance $performance, $id)
    {
        try {
            $performance::find($id)->delete();
            return Redirect::route('performance');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
