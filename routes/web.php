<?php

use App\Http\Controllers\Company;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\CustomerBatchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocterController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AdminManagesController;
use App\Http\Controllers\CorporateBatchController;
use App\Http\Controllers\VertigoReportController;
use App\Http\Controllers\ConsumerHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/user-reset-password/{token}', [PasswordResetLinkController::class, 'resetPassword'])->name('resetuserpassword');
Route::post('/make-reset-password', [PasswordResetLinkController::class, 'resetPasswordPost'])->name('reset.password.post');

  
Route::get('/dashbord', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashbord');


Route::get('/consumer-history', [ConsumerHistoryController::class, 'showReport']);

Route::any('/consumer-history-report/{consumertype?}',[ConsumerHistoryController::class,'consumerHistoryReport'])->name('consumer-history-report');


Route::middleware('auth')->group(function () {


    //company routes
    Route::middleware('permission:corporateid')->group(function () {
        Route::get('/corporateID', [CorporateController::class, 'showCorporateID']);
        Route::post('/corporateID', [CorporateController::class, 'filterCorporateID']);

        Route::post('/addCorporateID', [CorporateController::class, 'addCorporateID']);
        Route::get('/updateStatusCorporateID/{type}/{id}', [CorporateController::class, 'updateStatusCorporateID']);
        Route::post('/corporateID/delete', [CorporateController::class, 'deleteCorporateID']);
        Route::post('/corporateID/update', [CorporateController::class, 'updateCorporateID']);
    });
    //end company routes


    // company routes
    Route::middleware('permission:addcompany')->group(function () {
        Route::get('/add-company', [CompanyController::class, 'showCompany']);
        Route::post('/add-company', [CompanyController::class, 'filterCompany']);
        Route::post('/create-company', [CompanyController::class, 'CreateCompany'])->name('create_company');
        Route::post('/company/delete', [CompanyController::class, 'deleteCompany']);
        Route::post('/company/update', [CompanyController::class, 'updateCompany']);
        Route::get('/updateStatusCompany/{type}/{id}', [CompanyController::class, 'updateStatusCompany']);
    });
    // end company routes



    // Doctor routes
    Route::middleware('permission:adddoctor')->group(function () {
        Route::get('/add-docter', [DoctorController::class, 'showDocter']);
        Route::post('/add-docter', [DoctorController::class, 'filterDocter']);
        Route::post('/adddocter', [DoctorController::class, 'addDocter']);
        Route::post('/doctor/delete', [DoctorController::class, 'deleteDoctor']);
        Route::get('/updateStatusDoctor/{type}/{id}', [DoctorController::class, 'updateStatusDoctor']);
    });
    // end Docter Routes



    //custmer routes
    Route::get('/customer-profile', [CustomerController::class, 'showCustomer']);
    Route::post('/customer-profile', [CustomerController::class, 'filterCustomer']);

    Route::get('/updateStatusCustomer/{type}/{id}', [CustomerController::class, 'updateStatusCustomer']);
    Route::post('/customer/delete', [CustomerController::class, 'deleteCustomer']);


    //end custmer routes


    // feedback routes
    Route::middleware('permission:feedback')->group(function () {
        Route::get('/feedback', [FeedbackController::class, 'showFeedback']);
        Route::post('/feedback/delete', [FeedbackController::class, 'deleteFeedback']);
    });
    // end feedback routes


    // complaint routes
    Route::middleware('permission:complaints')->group(function () {
        Route::get('/complaint', [ComplaintController::class, 'showComplaint']);
        Route::post('/complaint/delete', [ComplaintController::class, 'deleteComplaint']);
    });
    // end complaint routes



    // Notifications
    Route::middleware('permission:notification')->group(function () {
        Route::get('/customer-notification', [NotificationController::class, 'customerNotification']);
        Route::post('/sendNotification', [NotificationController::class, 'sendNotification']);
    });
    //end Notification



    // batch routes
    Route::middleware('permission:customerbatch')->group(function () {
        Route::get('/customer-batch', [CustomerBatchController::class, 'customerBatch']);
        Route::post('/customer-batch-filterdata', [CustomerBatchController::class, 'customerfilterData']);

        Route::post('/customer-data-based-id', [CustomerBatchController::class, 'customerBatchUserData']);
        Route::post('/add-customer-batch', [CustomerBatchController::class, 'customerBatchSave'])->name('addcustomerbatch');
        Route::post('/customer-batch-edit', [CustomerBatchController::class, 'customerBatchEdit'])->name('editcustomerbatch');
        Route::post('/customer-batch-update', [CustomerBatchController::class, 'customerBatchUpdate'])->name('updatecustomerbatch');
        Route::post('/customer-batch-delete', [CustomerBatchController::class, 'customerBatchDelete'])->name('deletecustomerbatch');
    });
    // end batch routes
    //corporate batch

    Route::middleware('permission:corporatebatch')->group(function () {
        Route::get('/corporate-batch', [CorporateBatchController::class, 'corporateBatch']);
        Route::post('/corporate-batch-filterdata', [CorporateBatchController::class, 'corporatefilter']);
        // Route::post('/corporate-data-based-id', [CorporateBatchController::class, 'corporateBatchUserData']);
        Route::post('/company-data-based-id', [CorporateBatchController::class, 'corporateBatchCompanyData']);
        Route::post('/add-corporate-batch', [CorporateBatchController::class, 'corporateBatchSave'])->name('addcorporatebatch');
        Route::post('/corporate-batch-edit', [CorporateBatchController::class, 'corporateBatchEdit'])->name('editcorporatebatch');
        Route::post('/corporate-batch-update', [CorporateBatchController::class, 'corporateBatchUpdate'])->name('updatecorporatebatch');
        Route::post('/corporate-batch-delete', [CorporateBatchController::class, 'corporateBatchDelete'])->name('deletecorporatebatch');
    });

    //vertigo report doctor-data-based-id
    Route::middleware('permission:vertigoreport')->group(function () {
        Route::get('/customers-vertigo-report', [VertigoReportController::class, 'customersvertigoreports'])->name('customer-vertigo-report');
        Route::get('/consumer/test/{id?}', [VertigoReportController::class, 'consumertestprofile']);
        Route::any('customer/consumer/{id?}', [VertigoReportController::class, 'customerconsumervertigoreports'])->name('consumer-customer-vertigo-report');
        Route::post('consumer-test-result', [VertigoReportController::class, 'consumertestresult'])->name('consumer-customer-test-result');
        Route::post('doctor-data-based-id', [VertigoReportController::class, 'doctordatabasesid'])->name('doctor-data-base-id');
        Route::get('/corporate-vertigo-report', [VertigoReportController::class, 'corporatevertigoreports'])->name('corporate-vertigo-report');
        Route::any('corporate/consumer/{id?}', [VertigoReportController::class, 'corporateconsumervertigoreports'])->name('consumer-corporate-vertigo-report');
        Route::get('/qrcode', [VertigoReportController::class, 'show']);
        Route::post('/doctor-final-test-result', [VertigoReportController::class, 'doctorfinalresult'])->name('doctor-final-test-result');
    });




    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Admin Manges//
    Route::name('admin.')->namespace('Admin')->middleware('permission:adminmanages')->group(function () {


        Route::match(['get', 'post'], '/adminmanages', [AdminManagesController::class, 'adduser'])->name('addusers');
        Route::get('/showusers', [AdminManagesController::class, 'viewUserList'])->name('showuserlist');
        Route::post('/edit-users', [AdminManagesController::class, 'editUser'])->name('edituserlist');
        Route::post('/update-users', [AdminManagesController::class, 'updateUser'])->name('updateuserlist');


    });


});




require __DIR__ . '/auth.php';
