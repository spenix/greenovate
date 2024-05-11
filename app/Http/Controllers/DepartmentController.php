<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('CodeLibrary/Department/Index', [
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
            $data = Department::selectRaw('id, name, IF(status = "Y", "Active", "Inactive") status')->get();
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
    public function store(StoreDepartmentRequest $request)
    {
        try {
            $payload = ['name' => $request->department];
            //code...
            $isExist = Department::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'department' => 'The department has already been taken.',
                ])->onlyInput('department');
            }
            $payload['status'] = $request->status;
            Department::create($payload);
            return Redirect::route('departments');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department, $id)
    {
        $data = $department::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->department];
            //code...
            $isExist = Department::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'department' => 'The department has already been taken.',
                ])->onlyInput('department');
            }
            $payload['status'] = $request->status;
            Department::find($id)->update($payload);
            return Redirect::route('departments');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department, $id)
    {
        try {
            $department::find($id)->delete();
            return Redirect::route('departments');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
