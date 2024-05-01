<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Http\Requests\StorePayslipRequest;
use App\Http\Requests\UpdatePayslipRequest;
use Inertia\Inertia;

class PayslipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('PayslipReport/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
        ]);
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
    public function store(StorePayslipRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payslip $payslip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payslip $payslip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePayslipRequest $request, Payslip $payslip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payslip $payslip)
    {
        //
    }
}
