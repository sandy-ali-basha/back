<?php
namespace App\Http\Patterns;;

use App\Models\Post;
use App\Models\Logs;
use App\Models\PostModel;
use Illuminate\Support\Facades\Log;

class DeletePost implements IOperations
{
    public function doOperation(array $data)
    {
        $post = Post::find($data['id']);
        $post->deleteTranslations();
        return $post->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Post');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Post us IN Post CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
