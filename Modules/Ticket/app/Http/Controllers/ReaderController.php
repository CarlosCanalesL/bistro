<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ticket\Http\Requests\ReaderRequest;
use Inertia\Inertia;

class ReaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Ticket/Reader/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(ReaderRequest $request)
    {
        return view('ticket::create');
    }
}
