<?php

declare(strict_types=1);

use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Route;
use Lightit\Appointments\App\Controllers\DeleteAppointmentController;
use Lightit\Appointments\App\Controllers\ListAppointmentController;
use Lightit\Appointments\App\Controllers\StoreAppointmentController;
use Lightit\Appointments\App\Controllers\UpdateAppointmentController;
use Lightit\Authentication\App\Controllers\LoginController;
use Lightit\Authentication\App\Controllers\LogoutController;
use Lightit\Authentication\App\Controllers\RefreshController;
use Lightit\Patients\App\Controllers\DeletePatientController;
use Lightit\Patients\App\Controllers\GetPatientController;
use Lightit\Patients\App\Controllers\ListPatientController;
use Lightit\Patients\App\Controllers\StorePatientController;
use Lightit\Patients\App\Controllers\UpdatePatientController;
use Lightit\Clinics\App\Controllers\AssignDoctorsToClinicController;
use Lightit\Clinics\App\Controllers\DeleteClinicController;
use Lightit\Clinics\App\Controllers\GetClinicController;
use Lightit\Clinics\App\Controllers\ListClinicController;
use Lightit\Clinics\App\Controllers\StoreClinicController;
use Lightit\Clinics\App\Controllers\UpdateClinicController;
use Lightit\Doctors\App\Controllers\AssignClinicsToDoctorController;
use Lightit\Doctors\App\Controllers\DeleteDoctorController;
use Lightit\Doctors\App\Controllers\GetDoctorController;
use Lightit\Doctors\App\Controllers\ListDoctorController;
use Lightit\Doctors\App\Controllers\StoreDoctorController;
use Lightit\Doctors\App\Controllers\UpdateDoctorController;
use Lightit\Users\App\Controllers\DeleteUserController;
use Lightit\Users\App\Controllers\GetUserController;
use Lightit\Users\App\Controllers\ListUserController;
use Lightit\Users\App\Controllers\StoreUserController;
use Lightit\Users\App\Controllers\UpdateUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')
    ->get('/me', fn(
        #[CurrentUser] $user
    ) => response()->json([
        'data' => $user,
    ]));

/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users')
    ->group(static function (): void {
        Route::get('/', ListUserController::class);
        Route::post('/', StoreUserController::class);
        Route::prefix('{user}')->group(static function (): void {
            Route::get('/', GetUserController::class)->withTrashed();
            Route::put('/', UpdateUserController::class);
            Route::delete('/', DeleteUserController::class);
        })->whereNumber('user');
    });

/*
|--------------------------------------------------------------------------
| Patients Routes
|--------------------------------------------------------------------------
*/
Route::prefix('patients')
    ->group(static function (): void {
        Route::get('/', ListPatientController::class);
        Route::post('/', StorePatientController::class);
        Route::prefix('{patient}')->group(static function (): void {
            Route::get('/', GetPatientController::class);
            Route::put('/', UpdatePatientController::class);
            Route::delete('/', DeletePatientController::class);
        })->whereNumber('patient');
    });

/*
|--------------------------------------------------------------------------
| Clinics Routes
|--------------------------------------------------------------------------
*/
Route::prefix('clinics')
    ->group(static function (): void {
        Route::get('/', ListClinicController::class);
        Route::post('/', StoreClinicController::class);
        Route::prefix('{clinic}')->group(static function (): void {
            Route::get('/', GetClinicController::class);
            Route::put('/', UpdateClinicController::class);
            Route::delete('/', DeleteClinicController::class);
            Route::post('/doctors', AssignDoctorsToClinicController::class);
        })->whereNumber('clinic');
    });

/*
|--------------------------------------------------------------------------
| Doctors Routes
|--------------------------------------------------------------------------
*/
Route::prefix('doctors')
    ->group(static function (): void {
        Route::get('/', ListDoctorController::class);
        Route::post('/', StoreDoctorController::class);
        Route::prefix('{doctor}')->group(static function (): void {
           Route::get('/', GetDoctorController::class);
           Route::put('/', UpdateDoctorController::class);
           Route::delete('/', DeleteDoctorController::class);
           Route::post('/clinics', AssignClinicsToDoctorController::class);
        })->whereNumber('doctor');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', LoginController::class);
Route::middleware(['auth'])->group(static function (): void {
    Route::post('/logout', LogoutController::class);
    Route::post('/refresh', RefreshController::class);
});

/*
|--------------------------------------------------------------------------
| Appointments Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(static function (): void {
    Route::prefix('appointments')
        ->group(static function (): void {
            Route::get('/', ListAppointmentController::class);
            Route::post('/', StoreAppointmentController::class);
            Route::prefix('{appointment}')->group(static function (): void {
                Route::delete('/', DeleteAppointmentController::class);
                Route::put('/', UpdateAppointmentController::class);
            })->whereNumber('appointment');
        });
});
