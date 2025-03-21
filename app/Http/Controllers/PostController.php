<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){

        $postsFromDB = Post::all();

//        $allPosts = [
//            ['id'=>1 , 'title'=>'PHP' , 'posted_by'=>'Youssef' , 'created_at'=>'2025-02-15 09:00:00'],
//            ['id'=>2 , 'title'=>'JAVA' , 'posted_by'=>'Ahmed' , 'created_at'=>'2024-02-15 08:00:00'],
//            ['id'=>3 , 'title'=>'HTML' , 'posted_by'=>'Mohamed' , 'created_at'=>'2023-02-15 07:00:00'],
//            ['id'=>4 , 'title'=>'CSS' , 'posted_by'=>'Khaled' , 'created_at'=>'2022-02-15 06:00:00'],
//        ];

        return view('posts.index' , compact('postsFromDB'));
    }


    public function show(Post $post){

        //$singlePostFromDB = Post::findorFail($postId);        // no need for this line if we used  'Post $post'
        $post->load('comments.user');
        return view('posts.show' , ['post'=>$post]);
    }


    public function create(){

        $user = User::all();
        return view('posts.create',['users'=>$user]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|min:3',
            'description' => 'required|string|max:255|min:10',
        ]);


        Post::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }



    public function edit(Post $post){

        if(auth()->id() !== $post->user_id){
            abort(403, 'Unauthorized action.');
        }
        $users = User::all();

        return view('posts.edit',['post'=>$post,'users'=>$users]);
    }

    public function update($postId){

        request()->validate([
            'title' => ['required','min:3','max:50','min:3'],
            'description' => ['required','min:10','max:255'],
        ]);

        $singlePostFromDB = Post::findorFail($postId);

        $title = request()->title;
        $description = request()->description;

        $singlePostFromDB->update([
            'title'=>$title,
            'description'=>$description,
            'user_id'=>auth()->id(),
        ]);

        return to_route('posts.show',$postId);
    }

    public function destroy(Post $post){

        if (auth()->id() !== $post->user_id) {
            abort(403, 'You are not allowed to delete this post.');
        }

        $post->delete();

        return to_route('posts.index');
    }

}
