<?php

namespace App\Services;

use App\Http\Patterns\AddPost;
use App\Http\Patterns\DeletePost;
use App\Http\Patterns\UpdatePost;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;


class PostService
{

    protected Post $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function getAllPosts(): Collection
    {
        return Post::all();
    }

    public function createPost($data)
    {
        $brand = new AddPost();

        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->model->getPostById($id);
    }

    public function updatePost($id, $data)
    {
        $brand = new UpdatePost();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deletePost($id)
    {
        $brand = new DeletePost();

        return $brand->doOperation(['id'=>$id]);
    }

}
