<?php

use App\Http\Controllers\{
    ProfileController,
    LeaveTypeController,
    LeaveCreditController,
    EmployeeController,
    AttendanceController,
    DepartmentController,
    DesignationController,
    EmployeeLeaveController,
    PayrollController,
    PayslipController,
    PerformanceController,
    ViolationTypeController,
    DeductionController,
    CompensationController,
    EmpBasicSalaryController,
    LeaveHistoryController
};
use App\Models\Attendance;
use App\Models\AttendanceAttachment;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Route::has('login')) {
        return redirect('/login');
    }
    return "Oops, sometheng went wrong please contact your administrator.";
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'systemSetup' => [
            'nameShort' => config('app.name_short', 'Laravel'),
            'appName' => config('app.name_upper', 'Laravel'),
            'appLogo1' => config('app.logo1', 'Laravel'),
        ],
        'totalEmployee' =>  Employee::get()->count(),
        'terminatedEmployee' =>  Employee::where('terminate', 'Y')->get()->count(),
        'regularEmployee' =>  Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')->where('employee_types.id', 1)->count(),
        'onCallEmployee' =>  Employee::join('employee_types', 'employee_types.id', 'employees.employee_type_id')->where('employee_types.id', 2)->count(),
        'onLeaveEmployee' =>  EmployeeLeave::whereDate('date_start', '>=', now())->whereDate('date_end', '<=', now())->count(),
        'attendanceCount' =>  AttendanceAttachment::join('attendances', 'attendances.id', 'attendance_attachments.attendance_id')
            ->whereDate('period_start', '>=', date('Y-m-1'))
            ->whereDate('period_end', '<=', date('Y-m-t'))
            ->where('attendances.deleted_at', null)
            ->get()
            ->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('employees', [EmployeeController::class, 'index'])->name('employees');
    Route::get('employees/show/{id}', [EmployeeController::class, 'show']);
    Route::get('employees/show_table_data', [EmployeeController::class, 'show_table_data']);
    Route::post('employees/store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('employees/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('employees/terminate/{id}/{status}', [EmployeeController::class, 'terminate'])->name('employees.terminate');
    Route::delete('employees/destroy/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('employee-attendance', [AttendanceController::class, 'index'])->name('employee-attendance');
    Route::get('employee-attendance/show/{id}', [AttendanceController::class, 'show']);
    Route::get('employee-attendance/show_table_data', [AttendanceController::class, 'show_table_data']);
    Route::get('employee-attendance/show_dtr_table_data', [AttendanceController::class, 'show_dtr_table_data']);
    Route::post('employee-attendance/store', [AttendanceController::class, 'store'])->name('employee-attendance.store');
    Route::post('upload-attendance', [AttendanceController::class, 'upload_attendance'])->name('upload-attendance');
    Route::put('employee-attendance/update/{id}', [AttendanceController::class, 'update'])->name('employee-attendance.update');
    Route::delete('employee-attendance/destroy/{id}', [AttendanceController::class, 'destroy'])->name('employee-attendance.destroy');

    Route::get('employee-leaves', [EmployeeLeaveController::class, 'index'])->name('employee-leaves');
    Route::get('employee-leaves/show/{id}', [EmployeeLeaveController::class, 'show']);
    Route::get('employee-leaves/show_table_data', [EmployeeLeaveController::class, 'show_table_data']);
    Route::post('employee-leaves/store', [EmployeeLeaveController::class, 'store'])->name('employee-leaves.store');
    Route::put('employee-leaves/update/{id}', [EmployeeLeaveController::class, 'update'])->name('employee-leaves.update');
    Route::delete('employee-leaves/destroy/{id}', [EmployeeLeaveController::class, 'destroy'])->name('employee-leaves.destroy');

    Route::get('leave-types', [LeaveTypeController::class, 'index'])->name('leave-types');
    Route::get('leave-types/show/{id}', [LeaveTypeController::class, 'show']);
    Route::post('leave-types/store', [LeaveTypeController::class, 'store'])->name('leave-types.store');
    Route::get('leave-types/show_table_data', [LeaveTypeController::class, 'show_table_data']);
    Route::put('leave-types/update/{id}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
    Route::delete('leave-types/destroy/{id}', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');

    Route::get('leave-credits', [LeaveCreditController::class, 'index'])->name('leave-credits');
    Route::post('leave-credits/store', [LeaveCreditController::class, 'store'])->name('leave-credits.store');
    Route::put('leave-credits/update/{id}', [LeaveCreditController::class, 'update'])->name('leave-credits.update');
    Route::delete('leave-credits/{role}', [LeaveCreditController::class, 'destroy'])->name('leave-credits.destroy');

    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll');
    Route::get('payroll/show/{id}', [PayrollController::class, 'show']);
    Route::get('payroll/show_table_data', [PayrollController::class, 'show_table_data']);
    Route::post('payroll/store', [PayrollController::class, 'store'])->name('payroll.store');
    Route::put('payroll/update/{id}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::delete('payroll/{role}', [PayrollController::class, 'destroy'])->name('payroll.destroy');

    Route::get('payslip-report', [PayslipController::class, 'index'])->name('payslip-report');
    Route::post('payslip-report/store', [PayslipController::class, 'store'])->name('payslip-report.store');
    Route::put('payslip-report/update/{id}', [PayslipController::class, 'update'])->name('payslip-report.update');
    Route::delete('payslip-report/{role}', [PayslipController::class, 'destroy'])->name('payslip-report.destroy');

    Route::get('performance', [PerformanceController::class, 'index'])->name('performance');
    Route::get('performance/show/{id}', [PerformanceController::class, 'show']);
    Route::get('performance/show_table_data', [PerformanceController::class, 'show_table_data']);
    Route::post('performance/store', [PerformanceController::class, 'store'])->name('performance.store');
    Route::put('performance/update/{id}', [PerformanceController::class, 'update'])->name('performance.update');
    Route::delete('performance/destroy/{id}', [PerformanceController::class, 'destroy'])->name('performance.destroy');

    Route::get('violation-types', [ViolationTypeController::class, 'index'])->name('violation-types');
    Route::get('violation-types/show/{id}', [ViolationTypeController::class, 'show']);
    Route::get('violation-types/show_table_data', [ViolationTypeController::class, 'show_table_data']);
    Route::post('violation-types/store', [ViolationTypeController::class, 'store'])->name('violation-types.store');
    Route::put('violation-types/update/{id}', [ViolationTypeController::class, 'update'])->name('violation-types.update');
    Route::delete('violation-types/destroy/{id}', [ViolationTypeController::class, 'destroy'])->name('violation-types.destroy');

    Route::get('departments', [DepartmentController::class, 'index'])->name('departments');
    Route::get('departments/show/{id}', [DepartmentController::class, 'show']);
    Route::get('departments/show_table_data', [DepartmentController::class, 'show_table_data']);
    Route::post('departments/store', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('departments/update/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('departments/destroy/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    Route::get('leave-history', [LeaveHistoryController::class, 'index'])->name('leave-history');
    Route::get('leave-history/show/{id}', [LeaveHistoryController::class, 'show']);
    Route::get('leave-history/show_table_data', [LeaveHistoryController::class, 'show_table_data']);
    Route::post('leave-history/store', [LeaveHistoryController::class, 'store'])->name('leave-history.store');
    Route::put('leave-history/update/{id}', [LeaveHistoryController::class, 'update'])->name('leave-history.update');
    Route::delete('leave-history/destroy/{id}', [LeaveHistoryController::class, 'destroy'])->name('leave-history.destroy');

    Route::get('positions', [DesignationController::class, 'index'])->name('positions');
    Route::get('positions/show/{id}', [DesignationController::class, 'show']);
    Route::get('positions/show_table_data', [DesignationController::class, 'show_table_data']);
    Route::post('positions/store', [DesignationController::class, 'store'])->name('positions.store');
    Route::put('positions/update/{id}', [DesignationController::class, 'update'])->name('positions.update');
    Route::delete('positions/destroy/{id}', [DesignationController::class, 'destroy'])->name('positions.destroy');

    Route::get('deductions', [DeductionController::class, 'index'])->name('deductions');
    Route::get('deductions/show/{id}', [DeductionController::class, 'show']);
    Route::get('deductions/show_employee_details/{id}', [DeductionController::class, 'show_employee_details']);
    Route::get('deductions/show_employee_setup/{id}', [DeductionController::class, 'show_employee_setup']);
    Route::get('deductions/show_table_data', [DeductionController::class, 'show_table_data']);
    Route::get('deductions/show_deduction_manage_table', [DeductionController::class, 'show_deduction_manage_table']);
    Route::get('deductions/show_personnel_table_data', [DeductionController::class, 'show_personnel_table_data']);
    Route::post('deductions/store', [DeductionController::class, 'store'])->name('deductions.store');
    Route::post('deductions/store_emp_setup', [DeductionController::class, 'store_emp_setup'])->name('deductions.store-emp-setup');
    Route::put('deductions/update/{id}', [DeductionController::class, 'update'])->name('deductions.update');
    Route::put('deductions/update_emp_setup/{id}', [DeductionController::class, 'update_emp_setup'])->name('deductions.update-emp-setup');
    Route::delete('deductions/destroy/{id}', [DeductionController::class, 'destroy'])->name('deductions.destroy');
    Route::delete('deductions/destroy_deduction/{id}', [DeductionController::class, 'destroy_deduction'])->name('compensations.destroy_deduction');

    Route::get('compensations', [CompensationController::class, 'index'])->name('compensations');
    Route::get('compensations/show/{id}', [CompensationController::class, 'show']);
    Route::get('compensations/show_employee_details/{id}', [CompensationController::class, 'show_employee_details']);
    Route::get('compensations/show_employee_setup/{id}', [CompensationController::class, 'show_employee_setup']);
    Route::get('compensations/show_table_data', [CompensationController::class, 'show_table_data']);
    Route::get('compensations/show_compensation_manage_table', [CompensationController::class, 'show_compensation_manage_table']);
    Route::get('compensations/search_employees', [CompensationController::class, 'search_employees']);
    Route::get('compensations/show_personnel_table_data', [CompensationController::class, 'show_personnel_table_data']);
    Route::post('compensations/store', [CompensationController::class, 'store'])->name('compensations.store');
    Route::post('compensations/store_emp_setup', [CompensationController::class, 'store_emp_setup'])->name('compensations.store-emp-setup');
    Route::put('compensations/update/{id}', [CompensationController::class, 'update'])->name('compensations.update');
    Route::put('compensations/update_emp_setup/{id}', [CompensationController::class, 'update_emp_setup'])->name('compensations.update-emp-setup');
    Route::delete('compensations/destroy/{id}', [CompensationController::class, 'destroy'])->name('compensations.destroy');
    Route::delete('compensations/destroy_compensation/{id}', [CompensationController::class, 'destroy_compensation'])->name('compensations.destroy_compensation');

    Route::get('basic-salary', [EmpBasicSalaryController::class, 'index'])->name('basic-salary');
    Route::get('basic-salary/show/{id}', [EmpBasicSalaryController::class, 'show']);
    Route::get('basic-salary/show_table_data', [EmpBasicSalaryController::class, 'show_table_data']);
    Route::post('basic-salary/store', [EmpBasicSalaryController::class, 'store'])->name('basic-salary.store');
    Route::put('basic-salary/update/{id}', [EmpBasicSalaryController::class, 'update'])->name('basic-salary.update');
    Route::delete('basic-salary/destroy/{id}', [EmpBasicSalaryController::class, 'destroy'])->name('basic-salary.destroy');
});

require __DIR__ . '/auth.php';

Route::get('{any}', function () {

    return Inertia::render('PageNotFound');
})->where('any', '.*');
