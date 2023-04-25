<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\EventAssetController;
use App\Http\Controllers\EventBudgetJournal;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventEquipementController;
use App\Http\Controllers\EventEquipementJournalController;
use App\Http\Controllers\EventJournalController;
use App\Http\Controllers\EventServiceController;
use App\Http\Controllers\EventTaskController;
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

Route::group(['middleware' => 'isAuthJWT'], function () {

        // EVENTS
        Route::resource('events', EventController::class);

        Route::get('events/{event}/taskList', [EventTaskController::class, 'showTaskListAttached']);
        Route::post('events/{event}/tasks/attachList', [EventTaskController::class, 'attachTaskList']);
        Route::delete('events/{event}/tasks/detachList', [EventTaskController::class, 'detachTaskList']);
        Route::put('events/{event}/tasks/{task}/attribute', [EventTaskController::class, 'attributeTask']);
        Route::put('events/{event}/tasks/attributeList', [EventTaskController::class, 'attributeTaskList']);
        Route::put('events/{event}/tasks/expirationList', [EventTaskController::class, 'expirationTaskList']);
        Route::put('events/{event}/tasks/{task}/check', [EventTaskController::class, 'checkTask']);

        Route::get('events/{event}/equipementList', [EventEquipementController::class, 'showEquipementListAttached']);
        Route::post('events/{event}/equipements/attachList', [EventEquipementController::class, 'attachEquipementList']);
        Route::delete('events/{event}/equipements/detachList', [EventEquipementController::class, 'detachEquipementList']);
        Route::put('events/{event}/equipements/{equipement}/update', [EventEquipementController::class, 'updateEquipementEvent']);
        
        Route::get('events/{event}/equipements/{equipement}/journals', [EventEquipementJournalController::class, 'indexEquipementJournals']);
        Route::get('events/{event}/budgets/{budget}/journals', [EventBudgetJournal::class, 'indexBudgetJournals']);

        Route::get('events/{event}/assets', [EventAssetController::class, 'indexAssets']);
        Route::get('events/{event}/assets/{asset}', [EventAssetController::class, 'showAsset']);

        Route::get('events/{event}/journals', [EventJournalController::class, 'indexJournals']);
        Route::post('events/{event}/journals', [EventJournalController::class, 'writeJournal']);
        Route::put('events/{event}/journals/{journal}', [EventJournalController::class, 'rectifyJournal']);
        Route::delete('events/{event}/journals/{journal}', [EventJournalController::class, 'removeJournal']);

        // EQUIPEMENTS
        Route::apiResource('equipements', EquipementController::class);
        
        // TASKS
        Route::apiResource('tasks', TaskController::class);

        Route::apiResource('budgets', BudgetController::class);
        // Route::post('budgets/{budget}/payments/initialize', [BudgetPaymentController::class, 'initializePayment']);
        // Route::delete('budgets/{budget}/payments/remove', [BudgetPaymentController::class, 'removePayments']);
        // Route::delete('budgets/{budget}/payments/removeDeposit', [BudgetPaymentController::class, 'removePaymentDeposit']);

        Route::apiResource('payments', PaymentController::class);
        Route::put('payments/{payment}/checkPaid', [PaymentController::class, 'checkPaidPayment']);

        Route::apiResource('clients', ClientController::class);
        Route::apiResource('places', PlaceController::class);
        Route::apiResource('types', TypeController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('confirmations', ConfirmationController::class)->name('get', 'confirmations');

        Route::apiResource('service_users', ServiceUserController::class);
        Route::apiResource('event_services', EventServiceController::class);
        Route::apiResource('deposits', DepositController::class);
});
