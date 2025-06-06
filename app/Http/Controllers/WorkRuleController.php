<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use App\Models\SopSection;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Make sure Str is imported if you were planning to use it in controller

class WorkRuleController extends Controller
{
    /**
     * Display the work rules and SOPs page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch SOP sections with their items.
        // Eager load items to prevent N+1 query issues.
        // Order sections by title, and items within each section by their title.
        $sopSections = SopSection::with(['items' => function ($query) {
            $query->orderBy('title', 'asc');
        }])->orderBy('title', 'asc')->get();

        // Fetch regulations, ordered by title.
        $regulations = Regulation::orderBy('title', 'asc')->get();

        // The Str::slug($section->title) logic is handled directly in your Blade template,
        // which is fine. If you needed the slug for other logic in the controller or
        // to pass specifically to the view under a different key, you could do it here:
        // $sopSections->each(function ($section) {
        //     $section->slug = Str::slug($section->title);
        // });

        return view('work-rules', [
            'sopSections' => $sopSections,
            'regulations' => $regulations,
        ]);
    }
}
