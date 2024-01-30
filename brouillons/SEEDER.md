## comment seeder les tables en relations
- user/event (one to many)
- tag/event (many to many)

|>tag
-> id, name

|>event
-> id, name, user_id

|>event_tag
-> event_id, tag_id

|>user
-> id, name

$tags = TAb::factory(8)->create();
User::factory(10)->create()
    <!-- pour chaque user crée -->
    ->each(function($user) use ($tags) {
        <!-- on cree entre 2 et 5 event avec la foreignkey-->
        Event::factory(rand(2,5))->create(['user_id' => $user->id])
            <!-- pour chaque event crée -->
            ->each(function($event) use ($tags) {
                <!-- on appelle sa relation avec tag et on attache aux tags (chaque event aura 3 tags) -->
                $event->tags()->attach($tags->random(3))
            });
    })

## exemple 
 public function run(): void
    {

        $users = User::factory(10)->create();
        $permissions = Permission::factory(5)
            ->sequence(
                ['name' => 'role_admin'],
                ['name' => 'role_permission_user_manager'],
                ['name' => 'role_service_user_manager'],
                ['name' => 'role_event_manager'],
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
        $events = Event::factory(10)->create()
            ->each(function($event) use($services, $equipements, $tasks) {
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
                ])
                ->each(function($budget) {
                    // payment (one to many)
                    Payment::factory(rand(0,2))->create([
                        'budget_id' => $budget->id,
                        'created_by' => $budget->created_by
                    ]) 
                    ->each(function($payment) {
                        // deposit (one to one)
                        Deposit::factory(rand(0, 1))->create([
                            'payment_id' => $payment->id,
                            'created_by' => $payment->created_by
                        ]);
                        // remainder (one to one)
                        Remainder::factory(1)->create([
                            'payment_id' => $payment->id,
                            'created_by' => $payment->created_by
                        ]);
                    });
                });
            });

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call([
        //     ClientSeeder::class
        // ]);

        
        // $users = User::factory()
        //     ->times(5)
        //     ->hasAttached(
        //         // permission_user
        //         $permissions,
        //         // attributs
        //         [
        //             'created_at' => now(),
        //             'updated_at' => now()
        //         ]
        //     )
        //     ->create();
        
        

        // $events = Event::factory(9)->create();
        // $services = Service::factory(4)->create();

        // chaque event est attaché à chaque service
        // $events = Event::factory(10)
        //     ->hasAttached(
        //         Service::factory(4)->create(),
        //         [
        //             'created_by' => User::all()->random()->id,
        //             'updated_by' => User::all()->random()->id,
        //             'created_at' => now(),
        //             'updated_at' => now()
        //         ],
        //         'services'
        //     )->create();
    

        // Budget::factory(3)->create();
        // Deposit::factory(3)->create();
        // Payment::factory(6)->create();

        // Task::factory(3)
        //     ->hasAttached(
        //         Event::factory(3),
        //         [
        //             'check' => fake()->boolean(),
        //             'attribute_to' => User::all()->random()->id,
        //             'created_by' => User::all()->random()->id,
        //             'updated_by' => User::all()->random()->id
        //         ]
        //     )->create();
        // Equipement::factory(10)->create();
        // // EquipementEvent::factory(10)->create();
        // Invoice::factory(3)->create();
    }
}