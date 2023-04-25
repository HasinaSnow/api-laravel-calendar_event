<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Client;
use App\Models\Confirmation;
use App\Models\Deposit;
use App\Models\Equipement;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Money;
use App\Models\Offer;
use App\Models\Pack;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Place;
use App\Models\Remainder;
use App\Models\Service;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;

use function GuzzleHttp\Promise\each;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $users = User::factory(10)->create();
        $moneys = Money::factory(3)
            ->sequence(
                ['name' => 'cash'],
                ['name' => 'bank'],
                ['name' => 'mobile money']
            )->create();
            
        $permissions = Permission::factory(6)
            ->sequence(
                ['name' => 'role_admin'],
                ['name' => 'role_permission_user_manager'],
                ['name' => 'role_service_manager'],
                ['name' => 'role_event_manager'],
                ['name' => 'role_task_manager'],
                ['name' => 'role_equipement_manager'],
                ['name' => 'role_moderator']
            )->create()
            ->each(function($permission) use($users) {
                // permission_user
                $permission->users()->attach($users->random(3), [
                    'created_by' => User::all()->random()->id,
                ]);
            });
            
        $clients = Client::factory(10)->create();
        $categs = Category::factory(4)->create();
        $places = Place::factory(8)->create();
        $types = Type::factory(3)->create();
        $confirms = Confirmation::factory(3)->create();

        $services = Service::factory(4)->create()
            ->each(function($service) use($users) {
                // service_user
                $service->users()->attach($users->random(3), [
                    'created_by' => User::all()->random()->id,
                ]);
            });

        $equipements = Equipement::factory(10)->create();
        $tasks = Task::factory(50)->create();
        $offers = Offer::factory(40)->create();
        $packs = Pack::factory(3)->create()
            ->each(function($pack) use($offers){
                // pack_offer->belongsToMany (many to many)
                $pack->offers()->attach($offers->random(10), [
                    'created_by' => User::all()->random()->id
                ]);
            });
        $events = Event::factory(10)->create()
            ->each(function($event) use ($services, $equipements, $tasks) {
                // event_service (many to many)
                $event->services()->attach($services->random(3), [
                    'created_by' => User::all()->random()->id
                ]);
                // event_equipemnt (many to many)
                $event->equipements()->attach($equipements->random(3), [
                    'amount' => rand(10000, 100000),
                    'quantity' => rand(0, 50),
                    'created_by' => User::all()->random()->id
                ]);
                // event_task (many to many)
                $event->tasks()->attach($tasks->random(3), [
                    'check' => fake()->boolean(20),
                    'expiration' => fake()->dateTimeBetween('now', '+2 months'),
                    'attribute_to' => User::all()->random()->id,
                    'created_by' => User::all()->random()->id
                ]);
                // budget (one to one)
                Budget::factory(1)->create([
                    'event_id' => $event->id,
                    'created_by' => $event->created_by
                ]);

                // invoice->belongsTo (one to one )
                Invoice::factory(1)->create([
                    'event_id' => $event->id,
                    'reference'=> "invoice_" . $event->id,
                    'created_by' => $event->created_by
                ]);

            });

    }
}

// equipements??