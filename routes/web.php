<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\SystemUserGroupController;
use App\Http\Controllers\CoreKecamatanController;
use App\Http\Controllers\CoreKelurahanController;
use App\Http\Controllers\CoreMessagesController;
use App\Http\Controllers\CoreSectionController;
use App\Http\Controllers\CoreServiceController;
use App\Http\Controllers\CoreServiceGeneralParameterController;
use App\Http\Controllers\DashboardReviewController;
use App\Http\Controllers\PrintServiceController;
use App\Http\Controllers\PrintServiceGeneralController;
use App\Http\Controllers\ScanQRController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TransLetterOutController;
use App\Http\Controllers\TransServiceRequisitionController;
use App\Http\Controllers\TransServiceDispositionController;
use App\Http\Controllers\TransServiceDispositionApprovalController;
use App\Http\Controllers\TransServiceDispositionReviewController;
use App\Http\Controllers\TransServiceDispositionFundsController;
use App\Http\Controllers\TransServiceGeneralController;
use App\Http\Controllers\TransServiceGeneralApprovalController;
use App\Http\Controllers\HomeCollectionController;
use App\Http\Controllers\MustahikWorksheetResultController;
use App\Http\Controllers\CustomerSatisfactionReportController;
use App\Http\Controllers\TransServiceZisController;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/home-collection', [HomeCollectionController::class, 'index'])->name('home-collection');




Route::get('/section', [CoreSectionController::class, 'index'])->name('section');
Route::get('/section/add', [CoreSectionController::class, 'addCoreSection'])->name('add-section');
Route::post('/section/elements-add', [CoreSectionController::class, 'addElementsCoreSection'])->name('add-section-elements');
Route::get('/section/reset-add', [CoreSectionController::class, 'addReset'])->name('add-reset-section');
Route::post('/section/process-add-section', [CoreSectionController::class, 'processAddCoreSection'])->name('process-add-section');
Route::get('/section/edit/{section_id}', [CoreSectionController::class, 'editCoreSection'])->name('edit-section');
Route::post('/section/process-edit-section', [CoreSectionController::class, 'processEditCoreSection'])->name('process-edit-section');
Route::get('/section/delete-section/{section_id}', [CoreSectionController::class, 'deleteCoreSection'])->name('delete-section');

Route::get('/messages', [CoreMessagesController::class, 'index'])->name('messages');
Route::get('/messages/add', [CoreMessagesController::class, 'addCoreMessages'])->name('add-messages');
Route::post('/messages/elements-add', [CoreMessagesController::class, 'addElementsCoreMessages'])->name('add-messages-elements');
Route::get('/messages/reset-add', [CoreMessagesController::class, 'addReset'])->name('add-reset-messages');
Route::post('/messages/process-add-messages', [CoreMessagesController::class, 'processAddCoreMessages'])->name('process-add-messages');
Route::get('/messages/edit/{messages_id}', [CoreMessagesController::class, 'editCoreMessages'])->name('edit-messages');
Route::post('/messages/process-edit-messages', [CoreMessagesController::class, 'processEditCoreMessages'])->name('process-edit-messages');
Route::get('/messages/delete-messages/{messages_id}', [CoreMessagesController::class, 'deleteCoreMessages'])->name('delete-messages');
Route::get('/messages/activate/{messages_id}', [CoreMessagesController::class, 'activateCoreMessages'])->name('activate-messages');
Route::get('/messages/non-activate/{messages_id}', [CoreMessagesController::class, 'nonActivateCoreMessages'])->name('non-activate-messages');

Route::get('/kecamatan', [CoreKecamatanController::class, 'index'])->name('kecamatan');
Route::get('/kecamatan/add', [CoreKecamatanController::class, 'addCoreKecamatan'])->name('add-kecamatan');
Route::get('/kecamatan/reset-add', [CoreKecamatanController::class, 'addReset'])->name('add-reset-kecamatan');
Route::post('/kecamatan/process-add-kecamatan', [CoreKecamatanController::class, 'processAddCoreKecamatan'])->name('process-add-kecamatan');
Route::get('/kecamatan/edit/{kecamatan_id}', [CoreKecamatanController::class, 'editCoreKecamatan'])->name('edit-kecamatan');
Route::post('/kecamatan/process-edit-kecamatan', [CoreKecamatanController::class, 'processEditCoreKecamatan'])->name('process-edit-kecamatan');
Route::get('/kecamatan/delete-kecamatan/{kecamatan_id}', [CoreKecamatanController::class, 'deleteCoreKecamatan'])->name('delete-kecamatan');

Route::get('/kelurahan', [CoreKelurahanController::class, 'index'])->name('kelurahan');
Route::get('/kelurahan/add', [CoreKelurahanController::class, 'addCoreKelurahan'])->name('add-kelurahan');
Route::get('/kelurahan/reset-add', [CoreKelurahanController::class, 'addReset'])->name('add-reset-kelurahan');
Route::post('/kelurahan/process-add-kelurahan', [CoreKelurahanController::class, 'processAddCoreKelurahan'])->name('process-add-kelurahan');
Route::get('/kelurahan/edit/{kelurahan_id}', [CoreKelurahanController::class, 'editCoreKelurahan'])->name('edit-kelurahan');
Route::post('/kelurahan/process-edit-kelurahan', [CoreKelurahanController::class, 'processEditCoreKelurahan'])->name('process-edit-kelurahan');
Route::get('/kelurahan/delete-kelurahan/{kelurahan_id}', [CoreKelurahanController::class, 'deleteCoreKelurahan'])->name('delete-kelurahan');

Route::get('/service', [CoreServiceController::class, 'index'])->name('service');
Route::get('/service/add', [CoreServiceController::class, 'addCoreService'])->name('add-service');
Route::post('/service/elements-add', [CoreServiceController::class, 'addElementsCoreService'])->name('add-service-elements');
Route::get('/service/reset-add', [CoreServiceController::class, 'addReset'])->name('add-reset-service');
Route::post('/service/process-add-parameter-array', [CoreServiceController::class, 'processAddArrayCoreServiceParameter'])->name('add-service-parameter-array');
Route::post('/service/process-add-term-array', [CoreServiceController::class, 'processAddArrayCoreServiceTerm'])->name('add-service-term-array');
Route::get('/service/delete-add-term-array/{record_id}', [CoreServiceController::class, 'deleteAddArrayCoreServiceTerm'])->name('delete-add-term-array-service');
Route::get('/service/delete-add-parameter-array/{record_id}', [CoreServiceController::class, 'deleteAddArrayCoreServiceParameter'])->name('delete-add-parameter-array-service');
Route::post('/service/process-add-service', [CoreServiceController::class, 'processAddCoreService'])->name('process-add-service');

Route::get('/service/detail/{service_id}', [CoreServiceController::class, 'detailCoreService'])->name('detail-service');
Route::get('/service/delete-service/{service_id}', [CoreServiceController::class, 'deleteCoreService'])->name('void-service');

Route::get('/service/edit/{service_id}', [CoreServiceController::class, 'editCoreService'])->name('edit-service');
Route::get('/service/reset-edit/{service_id}', [CoreServiceController::class, 'editReset'])->name('edit-reset-service');
Route::post('/service/process-edit-term-array', [CoreServiceController::class, 'processEditArrayCoreServiceTerm'])->name('edit-service-term-array');
Route::get('/service/delete-edit-term-array/{record_id}/{service_id}', [CoreServiceController::class, 'deleteEditArrayCoreServiceTerm'])->name('delete-edit-term-array-service');
Route::post('/service/process-edit-parameter-array', [CoreServiceController::class, 'processEditArrayCoreServiceParameter'])->name('edit-service-parameter-array');
Route::get('/service/delete-edit-parameter-array/{record_id}/{service_id}', [CoreServiceController::class, 'deleteEditArrayCoreServiceParameter'])->name('delete-edit-parameter-array-service');
Route::post('/service/process-edit-service', [CoreServiceController::class, 'processEditCoreService'])->name('process-edit-service');

Route::get('/trans-service-requisition', [TransServiceRequisitionController::class, 'index'])->name('service-requisition');
Route::get('/trans-service-requisition/reset/{service_id}', [TransServiceRequisitionController::class, 'addReset'])->name('add-reset-service-requisition');
Route::get('/trans-service-requisition/search', [TransServiceRequisitionController::class, 'search'])->name('search-service-requisition');
Route::post('/trans-service-requisition/filter', [TransServiceRequisitionController::class, 'filter'])->name('filter-service-requisition');
Route::get('/trans-service-requisition/add/{service_requisition_id}', [TransServiceRequisitionController::class, 'addTransServiceRequisition'])->name('add-service-requisition');
Route::get('/trans-service-requisition/detail/{service_requisition_id}', [TransServiceRequisitionController::class, 'detailTransServiceRequisition'])->name('detail-service-requisition');
Route::get('/trans-service-requisition/edit/{service_requisition_id}', [TransServiceRequisitionController::class, 'editTransServiceRequisition'])->name('edit-service-requisition');
Route::get('/trans-service-requisition/delete/{service_requisition_id}', [TransServiceRequisitionController::class, 'deleteTransServiceRequisition'])->name('delete-service-requisition');
Route::get('/trans-service-requisition/document-requisition/{service_requisition_id}', [TransServiceRequisitionController::class, 'documentRequisitionTransServiceRequisition'])->name('service-document-requisition');
Route::post('/trans-service-requisition/process-add', [TransServiceRequisitionController::class, 'processAddTransServiceRequisition'])->name('process-add-service-requisition');
Route::post('/trans-service-requisition/process-edit', [TransServiceRequisitionController::class, 'processEditTransServiceRequisition'])->name('process-edit-service-requisition');
Route::post('/trans-service-requisition/process-delete', [TransServiceRequisitionController::class, 'processDeleteTransServiceRequisition'])->name('process-delete-service-requisition');
Route::post('/trans-service-requisition/process-document-requisition', [TransServiceRequisitionController::class, 'processDocumentRequisitionTransServiceRequisition'])->name('process-document-requisition-service-requisition');
Route::get('/trans-service-requisition/download-term/{id1}/{id2}', [TransServiceRequisitionController::class, 'downloadTransServiceRequisitionTerm'])->name('download-term-service-requisition');
Route::get('/trans-service-requisition/print/{service_requisition_id}', [TransServiceRequisitionController::class, 'print'])->name('print-service-requisition');
Route::post('/trans-service-requisition/check-nik', [TransServiceRequisitionController::class, 'checkNIK'])->name('check-nik-service-requisition');
Route::post('/trans-service-requisition/check-name', [TransServiceRequisitionController::class, 'checkName'])->name('check-name-service-requisition');
Route::post('/trans-service-requisition/check-address', [TransServiceRequisitionController::class, 'checkAddress'])->name('check-address-service-requisition');
Route::post('/get-core-kelurahan', [TransServiceRequisitionController::class, 'getCoreKelurahan'])->name('get-core-kelurahan');

Route::get('/trans-service-disposition', [TransServiceDispositionController::class, 'index'])->name('service-disposition');
Route::get('/trans-service-disposition/search', [TransServiceDispositionController::class, 'search'])->name('search-service-disposition');
Route::post('/trans-service-disposition/filter', [TransServiceDispositionController::class, 'filter'])->name('filter-service-disposition');
Route::get('/trans-service-disposition/add/{service_requisition_id}', [TransServiceDispositionController::class, 'addTransServiceDisposition'])->name('add-service-disposition');
Route::get('/trans-service-disposition/detail/{service_requisition_id}', [TransServiceDispositionController::class, 'detailTransServiceDisposition'])->name('detail-service-disposition');
Route::get('/trans-service-disposition/edit/{service_requisition_id}', [TransServiceDispositionController::class, 'editTransServiceDisposition'])->name('edit-service-disposition');
Route::post('/trans-service-disposition/process-add', [TransServiceDispositionController::class, 'processAddTransServiceDisposition'])->name('process-add-service-disposition');
Route::post('/trans-service-disposition/process-edit', [TransServiceDispositionController::class, 'processEditTransServiceDisposition'])->name('process-edit-service-disposition');
Route::post('/trans-service-disposition/process-document-requisition', [TransServiceDispositionController::class, 'processDocumentRequisitionTransServiceDisposition'])->name('process-document-requisition-service-disposition');
Route::post('/trans-service-disposition/process-document-requisition-edit', [TransServiceDispositionController::class, 'processDocumentRequisitionTransServiceDispositionEdit'])->name('process-document-requisition-service-disposition-edit');
Route::get('/trans-service-disposition/reset-add', [TransServiceDispositionController::class, 'addReset'])->name('add-reset-trans-service-disposition');
Route::get('/trans-service-disposition/download-term/{id1}/{id2}', [TransServiceDispositionController::class, 'downloadTransServiceDispositionTerm'])->name('download-term-service-disposition');

Route::get('/trans-service-disposition-approval', [TransServiceDispositionApprovalController::class, 'index'])->name('service-disposition-approval');
Route::get('/trans-service-disposition-approval/search', [TransServiceDispositionApprovalController::class, 'search'])->name('search-service-disposition-approval');
Route::post('/trans-service-disposition-approval/filter', [TransServiceDispositionApprovalController::class, 'filter'])->name('filter-service-disposition-approval');
Route::get('/trans-service-disposition-approval/add/{service_requisition_id}', [TransServiceDispositionApprovalController::class, 'addTransServiceDispositionApproval'])->name('add-service-disposition-approval');
Route::get('/trans-service-disposition-approval/edit/{service_disposition_id}', [TransServiceDispositionApprovalController::class, 'editTransServiceDispositionApproval'])->name('edit-service-disposition-approval');
Route::get('/trans-service-disposition-approval/detail/{service_requisition_id}', [TransServiceDispositionApprovalController::class, 'detailTransServiceDispositionApproval'])->name('detail-service-disposition-approval');
Route::get('/trans-service-disposition-approval/unapprove/{service_requisition_id}', [TransServiceDispositionApprovalController::class, 'unApproveTransServiceDispositionApproval'])->name('unapprove-service-disposition-approval');
Route::post('/trans-service-disposition-approval/process-add', [TransServiceDispositionApprovalController::class, 'processAddTransServiceDispositionApproval'])->name('process-add-service-disposition-approval');
Route::post('/trans-service-disposition-approval/process-edit', [TransServiceDispositionApprovalController::class, 'processEditTransServiceDispositionApproval'])->name('process-edit-service-disposition-approval');
Route::post('/trans-service-disposition-approval/process-unapprove', [TransServiceDispositionApprovalController::class, 'processUnApproveTransServiceDispositionApproval'])->name('process-unapprove-service-disposition-approval');
Route::post('/trans-service-disposition-approval/process-document-requisition', [TransServiceDispositionApprovalController::class, 'processDocumentRequisitionTransServiceDispositionApproval'])->name('process-document-requisition-service-disposition-approval');
Route::post('/trans-service-disposition-approval/process-document-requisition-edit', [TransServiceDispositionApprovalController::class, 'processDocumentRequisitionTransServiceDispositionApprovalEdit'])->name('process-document-requisition-service-disposition-approval-edit');
Route::get('/trans-service-disposition-approval/reset-add', [TransServiceDispositionApprovalController::class, 'addReset'])->name('add-reset-trans-service-disposition-approval');
Route::get('/trans-service-disposition-approval/download-term/{id1}/{id2}', [TransServiceDispositionApprovalController::class, 'downloadTransServiceDispositionApprovalTerm'])->name('download-term-service-disposition-approval');
Route::post('/trans-service-disposition-approval/process-disapprove', [TransServiceDispositionApprovalController::class, 'processDisapproveTransServiceDispositionApproval'])->name('process-disapprove-service-disposition-approval');
Route::get('/trans-service-disposition-approval/process-funds-received/{id}', [TransServiceDispositionApprovalController::class, 'processFundsReceived'])->name('process-funds-received-service-disposition-approval');

Route::get('/trans-service-disposition-review', [TransServiceDispositionReviewController::class, 'index'])->name('service-disposition-review');
Route::get('/trans-service-disposition-review/search', [TransServiceDispositionReviewController::class, 'search'])->name('search-service-disposition-review');
Route::post('/trans-service-disposition-review/filter', [TransServiceDispositionReviewController::class, 'filter'])->name('filter-service-disposition-review');
Route::get('/trans-service-disposition-review/add/{service_requisition_id}', [TransServiceDispositionReviewController::class, 'addTransServiceDispositionReview'])->name('add-service-disposition-review');
Route::get('/trans-service-disposition-review/detail/{service_requisition_id}', [TransServiceDispositionReviewController::class, 'detailTransServiceDispositionReview'])->name('detail-service-disposition-review');
Route::get('/trans-service-disposition-review/unapprove/{service_requisition_id}', [TransServiceDispositionReviewController::class, 'unApproveTransServiceDispositionReview'])->name('unapprove-service-disposition-review');
Route::post('/trans-service-disposition-review/process-add', [TransServiceDispositionReviewController::class, 'processAddTransServiceDispositionReview'])->name('process-add-service-disposition-review');
Route::post('/trans-service-disposition-review/process-unapprove', [TransServiceDispositionReviewController::class, 'processUnApproveTransServiceDispositionReview'])->name('process-unapprove-service-disposition-review');
Route::post('/trans-service-disposition-review/process-document-requisition', [TransServiceDispositionReviewController::class, 'processDocumentRequisitionTransServiceDispositionReview'])->name('process-document-requisition-service-disposition-review');
Route::post('/trans-service-disposition-review/process-document-requisition-edit', [TransServiceDispositionReviewController::class, 'processDocumentRequisitionTransServiceDispositionReviewEdit'])->name('process-document-requisition-service-disposition-review-edit');
Route::get('/trans-service-disposition-review/reset-add', [TransServiceDispositionReviewController::class, 'addReset'])->name('add-reset-trans-service-disposition-review');
Route::get('/trans-service-disposition-review/download-term/{id1}/{id2}', [TransServiceDispositionReviewController::class, 'downloadTransServiceDispositionReviewTerm'])->name('download-term-service-disposition-review');
Route::get('/trans-service-disposition-review/download-sk/{id}', [TransServiceDispositionReviewController::class, 'downloadTransServiceDispositionReviewSK'])->name('download-sk-service-disposition-review');
Route::get('/trans-service-disposition-review/download-fa/{id}', [TransServiceDispositionReviewController::class, 'downloadTransServiceDispositionReviewFA'])->name('download-fa-service-disposition-review');
Route::get('/trans-service-disposition-review/download-fo/{id}', [TransServiceDispositionReviewController::class, 'downloadTransServiceDispositionReviewFO'])->name('download-fo-service-disposition-review');
Route::post('/trans-service-disposition-review/process-disapprove', [TransServiceDispositionReviewController::class, 'processDisapproveTransServiceDispositionReview'])->name('process-disapprove-service-disposition-review');

Route::get('/trans-service-disposition-funds', [TransServiceDispositionFundsController::class, 'index'])->name('service-disposition-funds');
Route::get('/trans-service-disposition-funds/search', [TransServiceDispositionFundsController::class, 'search'])->name('search-service-disposition-funds');
Route::post('/trans-service-disposition-funds/filter', [TransServiceDispositionFundsController::class, 'filter'])->name('filter-service-disposition-funds');
Route::get('/trans-service-disposition-funds/add/{service_disposition_id}', [TransServiceDispositionFundsController::class, 'addTransServiceDispositionFunds'])->name('add-service-disposition-funds');
Route::post('/trans-service-disposition-funds/process-add', [TransServiceDispositionFundsController::class, 'processAddTransServiceDispositionFunds'])->name('process-add-service-disposition-funds');
Route::get('/trans-service-disposition-funds/detail/{service_disposition_id}', [TransServiceDispositionFundsController::class, 'detailTransServiceDispositionFunds'])->name('detail-service-disposition-funds');
Route::get('/trans-service-disposition-funds/print-warrant/{service_disposition_id}', [TransServiceDispositionFundsController::class, 'printWarrant'])->name('print-application-service-disposition-funds');
Route::get('/trans-service-disposition-funds/print-application/{service_disposition_id}', [TransServiceDispositionFundsController::class, 'printApplication'])->name('print-warrant-service-disposition-funds');
Route::get('/trans-service-disposition-funds/print-reg/{service_disposition_id}', [TransServiceDispositionFundsController::class, 'printRegisterNo'])->name('print-reg-service-disposition-funds');


Route::get('/print-service', [PrintServiceController::class, 'index'])->name('print-service');
Route::post('/print-service/filter', [PrintServiceController::class, 'filter'])->name('filter-print-service');
Route::get('/print-service/export/{service_disposition_id}', [PrintServiceController::class, 'export'])->name('export-print-service');
Route::get('/print-service/export-recap', [PrintServiceController::class, 'exportRecap'])->name('export-recap-print-service');
Route::get('/print-service/export-recap-funds-received', [PrintServiceController::class, 'exportRecapFundsReceived'])->name('export-recap-funds-received-print-service');


Route::get('/print-service-general', [PrintServiceGeneralController::class, 'index'])->name('service-general-print');
Route::post('/print-service-general/filter', [PrintServiceGeneralController::class, 'filter'])->name('filter-print-service-general');
Route::get('/print-service-general/export/{service_disposition_id}', [PrintServiceGeneralController::class, 'export'])->name('export-print-service-general');
Route::get('/print-service-general/export-recap', [PrintServiceGeneralController::class, 'exportRecap'])->name('export-recap-print-service-general');


Route::get('/system-user', [SystemUserController::class, 'index'])->name('system-user');
Route::get('/system-user/add', [SystemUserController::class, 'addSystemUser'])->name('add-system-user');
Route::post('/system-user/process-add-system-user', [SystemUserController::class, 'processAddSystemUser'])->name('process-add-system-user');
Route::get('/system-user/edit/{user_id}', [SystemUserController::class, 'editSystemUser'])->name('edit-system-user');
Route::post('/system-user/process-edit-system-user', [SystemUserController::class, 'processEditSystemUser'])->name('process-edit-system-user');
Route::get('/system-user/delete-system-user/{user_id}', [SystemUserController::class, 'deleteSystemUser'])->name('delete-system-user');
Route::get('/system-user/change-password/{user_id}  ', [SystemUserController::class, 'changePassword'])->name('change-password');
Route::post('/system-user/process-change-password', [SystemUserController::class, 'processChangePassword'])->name('process-change-password');


Route::get('/system-user-group', [SystemUserGroupController::class, 'index'])->name('system-user-group');
Route::get('/system-user-group/add', [SystemUserGroupController::class, 'addSystemUserGroup'])->name('add-system-user-group');
Route::post('/system-user-group/process-add-system-user-group', [SystemUserGroupController::class, 'processAddSystemUserGroup'])->name('process-add-system-user-group');
Route::get('/system-user-group/edit/{user_id}', [SystemUserGroupController::class, 'editSystemUserGroup'])->name('edit-system-user-group');
Route::post('/system-user-group/process-edit-system-user-group', [SystemUserGroupController::class, 'processEditSystemUserGroup'])->name('process-edit-system-user-group');
Route::get('/system-user-group/delete-system-user-group/{user_id}', [SystemUserGroupController::class, 'deleteSystemUserGroup'])->name('delete-system-user-group');


Route::get('/dashboard-review', [DashboardReviewController::class, 'index'])->name('dashboard-review');
Route::post('/dashboard-review/filter', [DashboardReviewController::class, 'filter'])->name('filter-dashboard-review');
Route::get('/dashboard-review/tracking', [DashboardReviewController::class, 'tracking'])->name('tracking-dashboard-review');
Route::post('/dashboard-review/search', [DashboardReviewController::class, 'search'])->name('search-dashboard-review');


Route::get('/scan-qr', [ScanQRController::class, 'index'])->name('scan-qr');
Route::get('/scan-qr/reload', [ScanQRController::class, 'reloadAPI'])->name('reload-scan-qr');


Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
Route::post('/tracking/search', [TrackingController::class, 'search'])->name('search-tracking');
Route::post('/tracking/reset-search', [TrackingController::class, 'resetSearch'])->name('reset-tracking');


Route::get('/service-general-parameter', [CoreServiceGeneralParameterController::class, 'index'])->name('service-general-parameter');
Route::get('/service-general-parameter/add', [CoreServiceGeneralParameterController::class, 'addCoreServiceGeneralParameter'])->name('add-service-general-parameter');
Route::post('/service-general-parameter/elements-add', [CoreServiceGeneralParameterController::class, 'addElementsCoreServiceGeneralParameter'])->name('add-service-general-parameter-elements');
Route::get('/service-general-parameter/reset-add', [CoreServiceGeneralParameterController::class, 'addReset'])->name('add-reset-service-general-parameter');
Route::post('/service-general-parameter/process-add-service-general-parameter', [CoreServiceGeneralParameterController::class, 'processAddCoreServiceGeneralParameter'])->name('process-add-service-general-parameter');
Route::get('/service-general-parameter/edit/{service_general_id}', [CoreServiceGeneralParameterController::class, 'editCoreServiceGeneralParameter'])->name('edit-service-general-parameter');
Route::post('/service-general-parameter/process-edit-service-general-parameter', [CoreServiceGeneralParameterController::class, 'processEditCoreServiceGeneralParameter'])->name('process-edit-service-general-parameter');
Route::get('/service-general-parameter/delete/{service_general_id}', [CoreServiceGeneralParameterController::class, 'deleteCoreServiceGeneralParameter'])->name('delete-service-general-parameter');


Route::get('/trans-service-general', [TransServiceGeneralController::class, 'index'])->name('service-general');
Route::get('/trans-service-general/reset/{service_id}', [TransServiceGeneralController::class, 'addReset'])->name('add-reset-service-general');
Route::get('/trans-service-general/search', [TransServiceGeneralController::class, 'search'])->name('search-service-general');
Route::post('/trans-service-general/filter', [TransServiceGeneralController::class, 'filter'])->name('filter-service-general');
Route::get('/trans-service-general/add', [TransServiceGeneralController::class, 'addTransServiceGeneral'])->name('add-service-general');
Route::get('/trans-service-general/detail/{service_requisition_id}', [TransServiceGeneralController::class, 'detailTransServiceGeneral'])->name('detail-service-general');
Route::get('/trans-service-general/edit/{service_requisition_id}', [TransServiceGeneralController::class, 'editTransServiceGeneral'])->name('edit-service-general');
Route::get('/trans-service-general/delete/{service_requisition_id}', [TransServiceGeneralController::class, 'deleteTransServiceGeneral'])->name('delete-service-general');
Route::post('/trans-service-general/process-add', [TransServiceGeneralController::class, 'processAddTransServiceGeneral'])->name('process-add-service-general');
Route::post('/trans-service-general/process-edit', [TransServiceGeneralController::class, 'processEditTransServiceGeneral'])->name('process-edit-service-general');
Route::post('/trans-service-general/process-delete', [TransServiceGeneralController::class, 'processDeleteTransServiceGeneral'])->name('process-delete-service-general');
Route::get('/trans-service-general/download/{id1}', [TransServiceGeneralController::class, 'downloadTransServiceGeneralFile'])->name('download-file-service-general');
Route::get('/trans-service-general/print/{service_requisition_id}', [TransServiceGeneralController::class, 'print'])->name('print-service-general');


Route::get('/trans-service-general-approval', [TransServiceGeneralApprovalController::class, 'index'])->name('service-general-approval');
Route::get('/trans-service-general-approval/reset/{service_id}', [TransServiceGeneralApprovalController::class, 'addReset'])->name('add-reset-service-general-approval');
Route::get('/trans-service-general-approval/search', [TransServiceGeneralApprovalController::class, 'search'])->name('search-service-general-approval');
Route::post('/trans-service-general-approval/filter', [TransServiceGeneralApprovalController::class, 'filter'])->name('filter-service-general-approval');
Route::get('/trans-service-general-approval/add/{service_general_id}', [TransServiceGeneralApprovalController::class, 'addTransServiceGeneralApproval'])->name('add-service-general-approval');
Route::get('/trans-service-general-approval/detail/{service_general_id}', [TransServiceGeneralApprovalController::class, 'detailTransServiceGeneralApproval'])->name('detail-service-general-approval');
Route::post('/trans-service-general-approval/process-add', [TransServiceGeneralApprovalController::class, 'processAddTransServiceGeneralApproval'])->name('process-add-service-general-approval');
Route::get('/trans-service-general-approval/download/{id1}', [TransServiceGeneralApprovalController::class, 'downloadTransServiceGeneralApprovalFile'])->name('download-file-service-general-approval');
Route::get('/trans-service-general-approval/download-sk/{id1}', [TransServiceGeneralApprovalController::class, 'downloadTransServiceGeneralApprovalSKFile'])->name('download-sk-file-service-general-approval');
Route::get('/trans-service-general-approval/print/{service_general_id}', [TransServiceGeneralApprovalController::class, 'print'])->name('print-service-general-approval');
Route::post('/trans-service-general-approval/process-revision', [TransServiceGeneralApprovalController::class, 'processRevisionTransServiceGeneralApproval'])->name('process-revision-service-general-approval');
Route::get('/trans-service-general-approval/revision/{service_general_id}', [TransServiceGeneralApprovalController::class, 'revisionTransServiceGeneralApproval'])->name('revision-service-general-approval');
Route::post('/trans-service-general-approval/approve', [TransServiceGeneralApprovalController::class, 'processApproveTransServiceGeneralApproval'])->name('approve-service-general-approval');
Route::post('/trans-service-general-approval/disapprove', [TransServiceGeneralApprovalController::class, 'processDisapproveTransServiceGeneralApproval'])->name('disapprove-service-general-approval');

Route::post('/mustahik-requisition/add', [TransServiceDispositionController::class, 'addMustahikRequisition'])->name('add-mustahik-requisition');

Route::get('/mustahik-worksheet-result', [MustahikWorksheetResultController::class, 'index'])->name('mustahik-worksheet-result');
Route::post('/mustahik-worksheet-result/filter', [MustahikWorksheetResultController::class, 'filter'])->name('filter-mustahik-worksheet-result');
Route::get('/mustahik-worksheet-result/print/{worksheet_result_id}', [MustahikWorksheetResultController::class, 'printMustahikWorksheetResult'])->name('print-mustahik-worksheet-result');
Route::get('/mustahik-worksheet-result/scoring/{worksheet_result_id}', [MustahikWorksheetResultController::class, 'scoringMustahikWorksheetResult'])->name('scoring-mustahik-worksheet-result');
Route::get('/mustahik-worksheet-result/print-scoring/{worksheet_result_id}', [MustahikWorksheetResultController::class, 'printScoringMustahikWorksheetResult'])->name('print-scoring-mustahik-worksheet-result');

Route::get('/customer-satisfaction-report', [CustomerSatisfactionReportController::class, 'index'])->name('customer-satisfaction-report');
Route::post('/customer-satisfaction-report/filter', [CustomerSatisfactionReportController::class, 'filter'])->name('filter-customer-satisfaction-report');
Route::get('/customer-satisfaction-report/export', [CustomerSatisfactionReportController::class, 'exportCustomerSatisfactionReport'])->name('export-customer-satisfaction-report');

//zakat-infaq-sedekah
Route::get('/trans-service-zis', [TransServiceZisController::class, 'index'])->name('zis-list');
Route::get('/trans-service-zis/search', [TransServiceZisController::class, 'search'])->name('search-service-zis');
Route::post('/trans-service-zis/filter', [TransServiceZisController::class, 'filter'])->name('filter-service-zis');
Route::get('/trans-service-zis/add', [TransServiceZisController::class, 'addTransServicezis'])->name('add-service-zis');
Route::get('/trans-service-zis/detail/{service_zis_id}', [TransServiceZisController::class, 'detailTransServicezis'])->name('detail-service-zis');
Route::get('/trans-service-zis/edit/{service_zis_id}', [TransServiceZisController::class, 'editTransServicezis'])->name('edit-service-zis');
Route::post('/trans-service-zis/process-add', [TransServiceZisController::class, 'processAddTransServicezis'])->name('process-add-service-zis');
Route::post('/trans-service-zis/process-edit', [TransServiceZisController::class, 'processEditTransServicezis'])->name('process-edit-service-zis');
Route::get('/trans-service-zis/process-delete/{service_zis_id}', [TransServiceZisController::class, 'deleteTransServicezis'])->name('process-delete-service-zis');
Route::post('/trans-service-zis/process-add-array', [TransServiceZisController::class, 'processAddItemArray'])->name('add-service_zis_item');
Route::post('/trans-service-zis/elements-add', [TransServiceZisController::class, 'addElementsServiceZis'])->name('add-service-zis-item-elements');
Route::get('/trans-service-zis/delete-transfer-array/{record_id}', [TransServiceZisController::class, 'deleteItemArray'])->name('delete-item-array-service-zis');
Route::get('/trans-service-zis/export', [TransServiceZisController::class, 'export'])->name('export-service-zis');
Route::get('/trans-service-zis/export-single/{service_zis_id}', [TransServiceZisController::class, 'exportSingle'])->name('export-single-service-zis');
Route::post('/trans-service-zis/getkelurahan', [TransServiceZisController::class, 'getCorekelurahan'])->name('service-zis-get-kelurahan');




