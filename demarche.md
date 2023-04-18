## CREATION DE TABLE POST

# créer le model Post
- php artisan make:model Post -m (pour générer la migration associée)

# configurer la migration (create_posts_table.php)
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->longText('content')->nullable();
        $table->timestamps();
    });
}

# migrer la nouvelle table dans la bd
- php artisan migrate 
-> nb: Demande de creation automatique de la bd si elle n'existe pas encore, on peut configurer son nom dans le fichier .env


## TESTER L'URL API
-lancer le projet: php artisan serve
- localhost:8000/api/

## CREER LES CONTROLLER POUR L'API (pour les routes)
- php artisan make:controller Api/PostController

/**
     * Retourner la liste de tous les articles
     *
     * @return string
     */
    public function index()
    {
        return 'liste des articles';
    }
    
/**
     * Créer une nouvelle article
     *
     * @return httpresponse
     */
    public function add(CreatePostRequest $request)
    {
        try
        {
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;

            $post->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'post added',
                'data' => $post
            ]);
        } catch(Exception $e)
        {
            // error server
            return response()->json($e);
        }

    }

## CREER lES ROUTE API
- Route::get('posts', [PostController::class, 'index']);
-> cette route retournera la methode index qui se trouve dans le PostController
-> methode post,donc utilisationde postman

## CREER UN OBJET REQUEST PERSONNALISE
- php artisan make:request CreatePosTRequest

/**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        return [
            'title' => 'required'
        ];
    }

    /**
     * Get the exception's error of validation
     *
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        // retourner l'exception sous forme de json
        throw new HttpResponseException(response()->json([
            'succes' => false,
            'error' => true,
            'message' => "validation's error!",
            'errorList' => $validator->errors() //array
        ]));
    }