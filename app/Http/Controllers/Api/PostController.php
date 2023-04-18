<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /**
     * Retourner la liste de tous les articles
     *
     * @return string
     */
    public function index(Request $request)
    {

        $query = Post::query();
        $search = $request->input('search');

        $result = Post::all();

        if($search)
        {
            $query->whereRaw("title LIKE '%" . $search . "%'");   
            $result = $query->get();
        }


        try
        {
            return response()->json([
                'status_conde' => 200,
                'message' => 'success posts',
                'data' => $result
            ]);

        } catch(Exception $e)
        {
            // error server
            return response()->json($e);
        }
    }

    public function show(Post $post)
    {
        try
        {
            // $post = Post::findOrfail($id);

            return response()->json([
                'status_code' => 200,
                'message' => 'post showed',
                'data' => $post
            ]);
        } catch(Exception $e)
        {
            return response()->json($e);
        }
    }
    
    /**
     * ajouter un article
     *
     * @param CreatePostRequest $request pour mettre en place les système d'autorisation
     * @return void
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
    

    /**
     * editer un article
     *
     * @param EditPostRequest $request
     * @param Post $post
     * @return void
     */
    public function edit(EditPostRequest $request, Post $post)
    {
        try
        {
            // $post = Post::findOrfail($id);

            $post->title = $request->title;
            $post->content = $request->content; 

            $post->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'post updated',
                'data' => $post
            ]);
        } catch(Exception $e)
        {
            return response()->json($e);
        }
    }

    public function delete(DeletePostRequest $request, Post $post) 
    {
        try
        {
            $post->delete();

            return response()->json([
                'status_code' => 200,
                'message' => 'post deleted'
            ]);
                       
        } catch(Exception $e)
        {
            return response()->json($e);
        }

    }
}
