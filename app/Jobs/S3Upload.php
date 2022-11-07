<?php


namespace App\Jobs;

use App\Models\Users;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 02/07/21
 */
class S3Upload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * base64 encoded string
     *
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $userId;

    public $tries = 1;

    public $type;

    /**
     * Create a new job instance.
     *
     * @param string $content
     * @param string $userId
     */
    public function __construct($content, $userId)
    {
        $this->content = $content;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = Users::find($this->userId);
            $image = $this->content;
            $fileNameToStore = 'avatars/' . $this->userId . ".png";
            Storage::disk('s3')->put($fileNameToStore, base64_decode($image), 'public');
            if ($user->avatar) {
                $publicPath = storage_path() . "/app/public/avatars/$user->user_id/$user->avatar";
                File::delete($publicPath);
            }
            $user->avatar = Storage::disk('s3')->url($fileNameToStore);
            $user->save();
        } catch (\Exception $e) {
            $this->failed($e);
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed($exception = null)
    {
        error_log($exception->getMessage());
        // Send notification
    }

}
