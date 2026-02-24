<?php

namespace App\Http\Patterns;

use App\Models\Post;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class AddPost implements IOperations
{
    public  function doOperation(array $data)
    {
        $post = Post::create($data);
        $postTranslation = $post->translations()->get();
        $post->translations = $postTranslation;

        return $post;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Post');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Post IN Post CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
