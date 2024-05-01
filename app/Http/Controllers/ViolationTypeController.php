<?php

namespace App\Http\Controllers;

use App\Models\ViolationType;
use App\Http\Requests\StoreViolationTypeRequest;
use App\Http\Requests\UpdateViolationTypeRequest;
use App\Models\ViolationCategory;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ViolationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = ViolationType::join('violation_categories', 'violation_categories.id', 'violation_types.violation_category_id')
        //     ->selectRaw('violation_types.*, violation_categories.name violation_category');
        return Inertia::render('ViolationType/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            // 'violationTypes' => $data,
            'violationCategories' => ViolationCategory::where('status', 'Y')->get()
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = ViolationType::join('violation_categories', 'violation_categories.id', 'violation_types.violation_category_id')
                ->selectRaw('violation_types.*, violation_categories.name violation_category');
            return datatables::of($data)
                ->addIndexColumn()
                ->filterColumn('violation_category', function ($query, $keyword) {
                    $sql = "violation_categories.name like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
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
    public function store(StoreViolationTypeRequest $request)
    {
        try {
            $payload = ['name' => $request->violation_type, 'violation_category_id' => $request->violation_category];
            $isExist = ViolationType::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'violation_type' => 'The violation type has already been taken.',
                ])->onlyInput('violation_type');
            }
            ViolationType::create($payload);
            return Redirect::route('violation-types');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ViolationType $violationType, $id)
    {
        $data = $violationType::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ViolationType $violationType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateViolationTypeRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->violation_type, 'violation_category_id' => $request->violation_category];
            //code...
            $isExist = ViolationType::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'violation_type' => 'The violation type has already been taken.',
                ])->onlyInput('violation_type');
            }
            ViolationType::find($id)->update($payload);
            return Redirect::route('violation-types');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ViolationType $violationType, $id)
    {
        try {
            $violationType::find($id)->delete();
            return Redirect::route('violation-types');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
