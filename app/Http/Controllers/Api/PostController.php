<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    use ApiResponseTrait;

    public function index(){
        $posts = PostResource::collection(Post::all());             // use PostResource::collection  to get Multiple posts

        return $this->apiResponse($posts,'ok',200);

    }

    public function show($id){

        $post = Post::find($id);

        if($post){
            return $this->apiResponse(new PostResource($post),'ok',200);            // use new PostResource to get one post
        }

        return $this->apiResponse(null,'The post not found',404);
    }


    public function store(Request $request){


//        $posts = $request->validate([
//            'title' => 'required',
//            'description' => 'required',
//            'user_id' => 'required',
//        ]);

        $validate = Validator::make($request->all(),[
            'title'=>'required|max:100|min:3',
            'description'=>'required|max:500|min:10',
            //'user_id'=>'required|integer',
        ]);

        if($validate->fails()){
            return $this->apiResponse($validate->errors(),'error',400);
        }

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        if($post){
            return $this->apiResponse(new PostResource($post),'The Post created successfully',201);
        }
        return $this->apiResponse(null,'The Post not created',400);
    }


    public function update(Request $request,$id){

        $validate = Validator::make($request->all(),[
            'title'=> 'required|max:100|min:3',
            'description'=> 'required|max:500|min:10',
        ]);

        if($validate->fails()){
            return $this->apiResponse($validate->errors(),'error',400);
        }

        $post = Post::find($id);

        if($post){
            if ($post->user_id !== Auth::id()){
                return $this->apiResponse('error','You do not have permission to update this post',403);
            }
            $post->update($request->all());
            return $this->apiResponse(new PostResource($post),'The Post updated successfully',200);

        }
        return $this->apiResponse(null,'The Post not Found',404);
    }



    public function destroy($id){

        $post = Post::find($id);

        if($post){
            if ($post->user_id !== Auth::id()){
                return $this->apiResponse('error','You do not have permission to delete this post',403);
            }
            $post->delete();
            return $this->apiResponse(null,'The Post deleted successfully',200);
        }
        return $this->apiResponse(null,'The Post not Found',404);

    }


    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        // Check if the user has already liked the post
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            // If liked, remove like (unlike)
            $like->delete();
            return response()->json(['message' => 'Post unliked', 'likes_count' => $post->likes()->count()]);
        } else {
            // If not liked, add like
            $post->likes()->create(['user_id' => $user->id]);
            return response()->json(['message' => 'Post liked', 'likes_count' => $post->likes()->count()]);
        }
    }




}
