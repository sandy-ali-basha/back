<?php
namespace App\Http\Patterns;

use App\Models\Post;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdatePost implements IOperations
{
    public  function doOperation(array $data)
    {
        $post = Post::find($data['id']);

        $post->update($data['data']);
        $postTranslation = $post->translations()->get();
        $post->translations = $postTranslation;

        return $post;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Post');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Post IN Post CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
