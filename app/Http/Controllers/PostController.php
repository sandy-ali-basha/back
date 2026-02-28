<?php

namespace App\Http\Controllers;

use App\Http\Data\AddPostData;
use App\Http\Data\UpdatePostData;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Lunar\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{

    public PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    
    {
        $posts        = $this->postService->getAllPosts();
        $postResource = new PostCollection($posts);
        $response     = new SuccessResponse($postResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data                  = AddPostData::from($request);
        $post                  = $this->postService->createPost($data);
        $post->withTranslation = true;
        $postResource          = new PostResource($post);
        $response              = new SuccessResponse($postResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function addImageToPost($id, Request $request)
    {
        $post = $this->postService->getById($id);

        if (!$post) {
            $response = new ErrorResponse('Post not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        if ($request->hasFile('image')) {

            $imagePath = Storage::disk('posts')->put($id, $request->file('image'));
            DB::update("update `posts` set `image` = ? where `id` = ?", [url("test/public/uploads/posts/$imagePath"), $id]);
        }

        $response = new SuccessResponse("post image uploaded", Response::HTTP_OK);

        return response()->success($response);
    }
    public function deletePostImage($id)
    {

        $post = $this->postService->getById($id);
        if (!$post){
            $response = new ErrorResponse('Image not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        Storage::disk('posts')->deleteDirectory($post->id);
        DB::update("update `posts` set `image` = ? where `id` = ?", [null, $id]);
        $response = new SuccessResponse("image is deleted", Response::HTTP_OK);

        return response()->success($response);
    }
    public function show($id, Request $request)
    {
        
        $postService = $this->postService->getById($id);
        if (!$postService) {
            $response = new ErrorResponse('Post not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $postService->withTranslation = $request->headers->has('translations');
        $postServiceResource          = new PostResource($postService);
        $response                     = new SuccessResponse($postServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $data                         = UpdatePostData::from($request);
        $postService                  = $this->postService->updatePost($id, $data);
        $postService->withTranslation = true;
        $postServiceResource          = new PostResource($postService);
        $response                     = new SuccessResponse($postServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function destroy($id)
    {
        $this->postService->deletePost($id);
        Storage::disk('posts')->deleteDirectory($id);
        $response = new SuccessResponse("post is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
