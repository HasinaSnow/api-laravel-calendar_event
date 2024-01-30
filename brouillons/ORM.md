
class Post extends Model
{
    protected $table = 't_posts';
    protected $primaryKey = 'id_post';
    protected $incrementing = 'false';
    protected $keyType = 'string';
    ...
}

# RECUPERER LES DONNEES
=> tous les posts qui sont en db (instance collection)
    - $posts = Post::all();
    - foreach ($posts as $post)
        {{ $post->champ }}

=> un post à partir de son id (instance collection)
    - $post = post::find($id)
    - $post = Post::findOrFail($id)
        {{ $post->champ }}

=> un post à partir d'autre champ 
    $post = Post::where('champ', 'value')->get() (array collection)
    $post = Post::where('champ', 'value')->firstOrFail() (instance collection)

# ENREGISTRER LES DONNEES
=> un post à partir de Request 
    $post = new Post()
    $post->champ = request->champ


# higher load
-> lorsqu'on récupère des données qui proviennent de la table relation (1:1, 1:n, n:n)
* with : 
    $events = Event::with('services')->get(['date'])->toArray();
    $events = Event::with(['type:id,name'])->get()->toArray();
    $events = Event::withCount('services')->with('services')->get()->toArray(); => creation de nouveau champ 'services_count'
    $events = Event::withSum('services', 'event_services.service_id')->get()->toArray(); => creation de nouveau champ 'services_sum_event_servicesservice_id'

* orderBy & groupBy :  
    $events = Event::selectRaw('id, DATE(date) AS date_event, DATE(created_at) AS date_creation')
        ->orderBy('date_event')
        ->orderByDesc('date_creation')
        ->get()
        ->groupBy('date_event')->toArray();

* fonction imbriqué
-> la somme dest factures qui sont après la date '2022-03-02' pour les users de mes livres
    $books = Book::with(
        [
            'user'=> function($query){
                $query->withSum(
                    [
                        'invoices' => function($query){
                            $query->where('created_at', '>', Carbon::parse('2022-03-02'));
                        }
                    ], 'amount');
            }
        ]
    )->get()->toArray();


## SEED
- creation de 10 users, chacun va avoir 3 orders, et 
User::factory()
    ->count(10)
    ->has(
        Order::factory()
            ->count(3)
            ->hasAttached(
                Product::factory()
                    ->count(5),
                [
                    'total_price' => rand(100, 500),
                    'total_quuantity' => rand(1, 3)
                ]
            )
    ) ->create();

## one to one
- student have one profile and profile belongs to student    
<!-- define in student class -->
pubilc function profile()
{
return $this->hasOne(Profile::class);
}

<!-- define in profile class -->
{
return $this->belongsTo(Student::class);
}

<!-- query -->
$student = Student::find(1);
$profile = Profile::find(1);

$student->profile;
$profile->student;

$profile->student()->associate($student)->save();
$profile->student()->dissociate($student)->save();

$students = Student::has('profile')->get();
$students = Student::doesntHave('profile')->get();

$students = Student::whereHas('profile', function($q){
    $q->where('email', 'like', '%data%');
})->get();

$student->profile()->create([
'attribut' => 'value', ...
])
$student->profile()->update([
'attribut' => 'value', ...
])

## one to many
-> Query to fetch all the comments of one student
$student = Student::find(1)
$comments = $student->comments

-> Query to fetch students who do not have any comments
$students = Student::doesntHave('comments')

-> Query to fetch students wich have comments
$students = Student::has('comments')->get()

-> Query to fetch students with more than 3 comments
$students = Student::has('comments', '>=', 3)->get()

- Query to fecth student along with all comments in desc order by id
$student = Student::where('id', 1)->with('comments' => function($query){
    $query->orderBy('id', 'desc');
})->get();

- Query to fecth student with specific condition in Comments table
$student = Student::whereHas(
    [
        'recommendation', function($query){
            $query->where('comments', 'like', '%student');
        }
    ]
)->get();

- Query to fetch students and get count of comments for each student
$student = Student::withCount('comments)->get();

- Query to get count of students wich meet certain criteria
$students = Student::withCount(
    [
        'comments' => function($query){
            $query->where('comments', 'like', '%student%');
        }
    ]
)->get();

## many to many

<!-- define  -->
public function subjects (){
    return $this->belongToMany(Student::class)->withPivot(['created_by', 'updated_by'])
}
public function students (){
    return $this->belongToMany(Subject::class)->withPivot(['created_by', 'updated_by'])
}
<!-- query -->
$student = Student::find(2);

% associate the subject (subject_id=1) to the student (student_id=2)
$student->subjects()->attach([1]);
$student->refresh();
$student->subjects;

% dessociate the subject (subject_id=1,3) to the student (student_id=2)
$student->subjects()->detach(['1', '3']) // removing association using detach
$student->refresh();
$student->subjects;

% adding and removing association with Sync() (change the association)
$student->subjects()->sync([1,2,4])

% adding and removing association with toggle() (remove if have and add if have not)
$student->subjects()->toggle([1,2,3,4,5])

% add/update data into the attribut pivot
$subject = Subject::find(2)
$student->courses()->save($subject, ['attribut_pivot' => 'data'])
$student->courses()->updateExistingPivot($subject, ['attribut_pivot' => 'data'])

% Query to fetch students wich to 'have/not have/have particular' courses
$Students = Student::has('courses')->get();
$Students = Student::doesntHave('courses')->get();
$Students = Subject::with('students')->where('id', 5)->get();

%  Query to fetch student number of courses each student has
$student = Student::withcount('courses')->get(); 

%  Query to fetch student with 2 or more courses
$student = Student::has('courses', '>=', '2')->get();

% Query to fetch student with contidion on additional column in pivot table
$student = Student::with('courses')->whereHas('courses', function($query){
    $query->where('marks', '>', 20)
})->get();

## has one through
- student has one profile has one detail
<!-- define in student class -->
public function profile()
{
    return $this->hasOne(Profile::class);
}

public function detail()
{
    return $this->hasOneThrough(Detail::class, Profile::class)
}

<!-- define in profile class -->
public function student()
{
    return $this->belongsTo(Student::class);
}
public function detail()
{
    return $this->hasOne(Detail::class);
}

<!-- define in detail class -->
public fucntion profile()
{
    return $this->belongsTo(Profile::class);
}

<!-- query -->
$student = student::find(1);

$student->detail;
$student->profile->detail;

## has many through
- 'student' [has many] 'comments' [has many] 'likes'

<!-- define in student class -->
public function profile()
{
    return $this->hasMany(Comment::class);
}

public function likes()
{
    return $this->hasManyThrough(Like::class, Profile::class)
}

<!-- define in comment class -->
public function student()
{
    return $this->belongsTo(Student::class);
}

public function likes()
{
    return $this->hasMany(Like::class);
}

<!-- define in like class -->
public fucntion comment()
{
    return $this->belongsTo(Comment::class);
}

<!-- query -->
$student = Student::find(1);
$student->likes

## one to one polymorph
- student has one profile and teachers has one profile (profileable_id, profileable_type)
<!-- define in profile class -->
public function profilable()
{
    return $this->morphTo();
} 

<!-- define in student class -->
public fucntion profile()
{
    return $this->morphOne(Profile::class, 'profileable');
}

<!-- define in teacher class -->
public function profile()
{
    return $this->morphOne(Profile::class, 'profileable');
}

<!-- query -->
$student = Student::find(1);
$student->profile()->create([
    'email' => 'student@gmail.com',
    'phone' => '22 65 894'
    ]);

$teacher = Teacher::find(1);
$teacher->profile()->create([
    'email' => 'teacher@gmail.com',
    'phone' => '22 65 895'
]);

$profile = Profile::find(1);
$profile->profileable;

<!-- custom -->
- Query to fetch student which 'have/not have/specific email' profile
$student::has('profile')->get();
$student::doesnthave('profile')->get();

Profile::whereHasMorph('profileable', 'App\Student', function($query){
    $query->where('email', 'like', '%@gmail.com%')
})->get();

- Query to fetch teacher which specific email profile
Profile::whereHasMorph(
    'profileable',
    'App\Tacher',
    function($query){
        $query->where('phone', 'like', '%333%')
    }
)->get();

- Query to fetch teacher or student wich specific email profile
Profile::whereHasMorph(
    'profileable',
    '*',
    function($query){
        $query->where('email', 'like', '%gmail.com%')
    }
)->get();

## one to many polymorph
- student has many comments and teachers has many comments (commentable_type, commentable_id)
<!-- define in comment class -->
public function commentable()
{
    return $this->morphTo();
} 

<!-- define in student class -->
public fucntion comments()
{ 
    return $this->morphMany(Comment::class, 'profileable');
}
 
<!-- define in teacher class -->
public function comments()
{
    return $this->morphMany(Comment::class, 'profileable');
}

<!-- query -->
- saving relationship
$student = Student::find(1);
$student->comments()->save(new Comment([
    'comment' => 'data'
]))
$student->comments()->saveMany([
    new Comment('comment' => data1),
    new Comment('comment' => data2),
])

- fetch students with/no comments
$students = student::has('comments')->get();
$students = student::doesnthave('comments')->get();

- fetch specific comment related to Student/Teacher
Comment::whereHasMorph(
    'commentable',
    '*',
    function($query){
        $query->where('comment', 'like', '%good%')
    }
)->get();

## many to many polymorph
- student/teacher belongs to many subjects and subject belongs to many students/taechers
<!-- define in subject class -->
public function students()
{
    return $this->morphByMany(Student::class, 'courseable');
} 
public function teachers()
{
    return $this->morphByMany(teacher::class, 'courseable');
} 

<!-- define in student class -->
public fucntion subjects()
{
    return $this->morphToMany(Subject::class, 'courseable');
}
 
<!-- define in teacher class -->
public function subjects()
{
    return $this->morphToMany(Subject::class, 'courseable');
}

<!-- query -->
$student = Student::find(1);
$student->subjects()->attach([1, 3, 4])