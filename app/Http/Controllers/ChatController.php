<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\PortfolioChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function send(Request $request, PortfolioChat $chat)
    {
        $data = $request->validate(['message' => 'required|string|min:2|max:1000']);
        if (! (Setting::get('profile')['chat_enabled'] ?? false) || ! config('portfolio.ai.key') || ! config('portfolio.ai.model') || config('portfolio.ai.provider') !== 'openai-compatible') {
            return response()->json(['message' => 'Chat is currently unavailable. Please use the contact page.'], 503);
        }
        $key = 'chat-budget:'.now()->format('Y-m-d');
        Cache::add($key, 0, now()->endOfDay());
        if (Cache::increment($key) > config('portfolio.ai.daily_limit')) {
            return response()->json(['message' => 'The daily chat limit has been reached. Please use the contact page.'], 429);
        }
        try {
            $answer = $chat->answer($data['message']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Chat is temporarily unavailable. Please try again later or use the contact page.'], 503);
        }
        if (config('portfolio.ai.retention_days') > 0) {
            DB::table('chat_logs')->insert(['question' => $data['message'], 'answer' => $answer, 'created_at' => now(), 'updated_at' => now()]);
        }
        $request->session()->put('chat_answered', true);

        return response()->json(['answer' => $answer]);
    }

    public function feedback(Request $request)
    {
        abort_unless($request->session()->get('chat_answered'), 403);
        $data = $request->validate(['rating' => 'required|in:helpful,unhelpful', 'comment' => 'nullable|string|max:1000']);
        DB::table('chat_feedback')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        $request->session()->forget('chat_answered');

        return response()->json(['message' => 'Thank you for your feedback.']);
    }
}
