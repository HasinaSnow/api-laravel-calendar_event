<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventServiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ServiceUserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//         return $request->user();
//     });

/**************************** ARTICLES ***************************/

// /**
//  * recupérer la liste des articles (avec un système de recherche)
//  */
// Route::get('posts', [PostController::class, 'index']);

// /**
//  * recupérer une article en particlier
//  */
// Route::get('posts/{post}', [PostController::class, 'show']);

// /**
//  * ajouter une nouvelle article
//  */
// Route::post('posts/add', [PostController::class, 'add']);

// /**
//  * editer une article
//  */
// Route::put('posts/edit/{post}', [PostController::class, 'edit']);

// /**
//  * supprimer un article
//  */ 
// Route::delete('posts/delete/{post}', [PostController::class, 'delete']);

// // 

// /**************************** UTILISATEURS ***************************/

// /**
//  * inscrire un user
//  */
// Route::post('/register', [UserController::class, 'register']);

// /**
//  * connecter un user
//  */
// Route::post('/login', [UserController::class, 'login']);


// // /************************** AUTHENTIFICATION ***************************/



Route::post('register', [AccountController::class, 'register']);
Route::post('login', [AccountController::class, 'login']);
Route::post('logout', [AccountController::class, 'logout']);

Route::group(['middleware' => 'isAuthJWT'], function() {

        Route::resource('events', EventController::class);

        Route::apiResource('clients', ClientController::class);
        Route::apiResource('places', PlaceController::class);
        Route::apiResource('types', TypeController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('confirmations', ConfirmationController::class)->name('get', 'confirmations');

        Route::apiResource('service_users', ServiceUserController::class);
        Route::apiResource('event_services', EventServiceController::class);
        Route::apiResource('budgets', BudgetController::class);
        Route::apiResource('deposits', DepositController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('equipements', EquipementController::class);

});

