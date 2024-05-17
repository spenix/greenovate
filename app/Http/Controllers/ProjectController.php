<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Project/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'statuses' => ['Pending','Ongoing','Done','Cancelled']
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::get();
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
    public function store(StoreProjectRequest $request)
    {
        try {
            $payload = ['name' => $request->project, 'location' => $request->location, 'client_name' => $request->client];
            //code...
            $isExist = Project::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'project' => 'The project was already exist.',
                ])->onlyInput('project');
            }
            $payload['description'] = $request->description;
            $payload['project_cost'] = $request->project_cost;
            $payload['start_date'] = $request->start_date;
            $payload['end_date'] = $request->end_date;
            $payload['status'] = $request->status;
            Project::create($payload);
            return Redirect::route('project');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, $id)
    {
        $data = $project::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->project, 'location' => $request->location, 'client_name' => $request->client];
            //code...
            $isExist = Project::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'project' => 'The project was already exist.',
                ])->onlyInput('project');
            }
            $payload['description'] = $request->description;
            $payload['project_cost'] = $request->project_cost;
            $payload['start_date'] = $request->start_date;
            $payload['end_date'] = $request->end_date;
            $payload['status'] = $request->status;
            Project::find($id)->update($payload);
            return Redirect::route('project');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, $id)
    {
        try {
            $project::find($id)->delete();
            return Redirect::route('project');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
