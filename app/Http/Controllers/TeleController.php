<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class TeleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): bool|string
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
        return response()->json('ok', Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'text' => 'string',
            'image' => 'file',
        ]);
        $messages = Message::get();
        foreach ($messages as $message) {
            Telegram::sendMessage([
                'chat_id' => $message->chat_id,
                'text' => $data['text'],
            ]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                Telegram::sendPhoto([
                    'chat_id' => $message->chat_id,
                    'photo' => InputFile::create($file->getPathname(), $file->getClientOriginalName()),
                ]);
            }
        }
        return response()->json('ok', Response::HTTP_OK);
    }

}
