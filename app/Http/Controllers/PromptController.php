<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\View\View;

class PromptController extends Controller
{
    public function index(): View
    {
        $items = Prompt::orderByDesc('id')->paginate(12);

        return view('products.prompt.index', ['items' => $items]);
    }

    public function show(Prompt $prompt): View
    {
        return view('products.prompt.show', ['item' => $prompt]);
    }
}
