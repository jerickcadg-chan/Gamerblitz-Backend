<?php

namespace App\Http\Controllers;

use App\Models\PageDescription;
use Illuminate\Http\Request;

class PageDescriptionController extends Controller
{
    /**
     * @var string
     */
    private string $title;

    public function __construct()
    {
        $this->title = 'Page Description';

        $this->middleware(['permission:View Blog'])->only('index', 'show');
        $this->middleware(['permission:Create Blog'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Blog'])->only('edit', 'update');
        $this->middleware(['permission:Delete Blog'])->only('destroy');
    }

    public function index()
    {
        $title = $this->title;

        $pageDescriptions = PageDescription::query()
            ->with(['content'])
            ->latest()
            ->paginate(20);

        return view('page_description.index', compact('pageDescriptions', 'title'));
    }

    public function create()
    {
        $pageDescription = new PageDescription();
        $formAction = route('page-descriptions.store');
        $isEdit = false;

        return view('page_description.forms', compact('pageDescription', 'formAction', 'isEdit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'title' => 'nullable',
            'content' => 'nullable',
        ]);

        $data = $request->all();

        $pageDescription = PageDescription::create($data);

        if ($data['title'] && $data['content']) {
            $pageDescription->content()->create([
                'title' => $data['title'],
                'type' => 'content',
                'content' => $data['content'],
            ]);
        }

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('page-descriptions.index');
    }

    public function edit(PageDescription $pageDescription)
    {
        $formAction = route('page-descriptions.update', $pageDescription);
        $isEdit = true;

        return view('page_description.forms', compact('pageDescription', 'formAction', 'isEdit'));
    }

    public function update(Request $request, PageDescription $pageDescription)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'title' => 'nullable',
            'content' => 'nullable',
        ]);

        $data = $request->all();

        $pageDescription->update($data);

        $hasContentInput = $request->filled(['title', 'content']);
        $content = $pageDescription->content;

        if ($hasContentInput) {
            // Create or update
            $pageDescription->content()->updateOrCreate(
                ['page_description_id' => $pageDescription->id],
                [
                    'title'   => $data['title'],
                    'type'    => 'content',
                    'content' => $data['content'],
                ]
            );
        } else {
            // Remove content if exists
            if ($content) {
                $content->delete();
            }
        }

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('page-descriptions.index');
    }

    public function destroy(PageDescription $pageDescription)
    {
        $pageDescription->content()->delete();
        $pageDescription->delete();

        toast(alert_deleted_text($this->title), 'success');

        return back();
    }
}
