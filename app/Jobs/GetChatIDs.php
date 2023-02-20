<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class GetChatIDs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $updates = Telegram::getUpdates();
        foreach ($updates as $update) {
            $chatId = $update->getMessage()->getChat()->getId();
            if (Message::where('chat_id', $chatId)->get()->isEmpty()) {
                $message = new Message();
                $message->chat_id = $chatId;
                $message->save();
            }
        }
    }
}
