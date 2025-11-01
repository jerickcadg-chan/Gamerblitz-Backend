<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use App\Services\PictureService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Blog';

        $this->middleware(['permission:View Blog'])->only('index', 'show');
        $this->middleware(['permission:Create Blog'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Blog'])->only('edit', 'update');
        $this->middleware(['permission:Delete Blog'])->only('destroy');
    }

    public function index()
    {
        $title = $this->title;

        $blogs = Blog::query()
            ->with(['category', 'author'])
            ->when(request()->filled('search'), function ($qb) {
                $s = request()->string('search');
                $qb->where(function($w) use ($s){
                    $w->where('title','like',"%$s%")
                        ->orWhere('slug','like',"%$s%");
                });
            })
            ->when(request()->filled('status'), fn($qb) => $qb->where('status', request()->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('blogs.index', compact('blogs', 'title'));
    }

    public function create()
    {
        $blog = new Blog();
        $categories = BlogCategory::orderBy('name')->get(['id','name']);
        $formAction = route('blog.store');
        $isEdit = false;

        return view('blogs.form', compact('blog','categories','formAction','isEdit'));
    }

    public function store(BlogRequest $request)
    {
        $data = $request->all();

        $data['slug'] = $request->slug ?: Str::slug($request->title);
        $data['published_at'] = $request->status === 'published' ? now() : null;
        $data['user_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = Str::random(10).'_'.$file->getClientOriginalName();
            $data['thumbnail'] = $file->storeAs('blogs/thumbnail', $filename, 'public');
        }

        if ($request->filled('thumbnail_url')) {
            $data['thumbnail'] = (new PictureService())->insertFromUrl('blogs/thumbnail', $request->thumbnail_url);
        }

        $blog = Blog::create($data);

        $this->updateTags($blog, $request);

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('blog.index');
    }

    public function show(Blog $blog)
    {
        $blog->load(['category:id,name','author:id,name']);
        return view('blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::orderBy('name')->get(['id','name']);
        $formAction = route('blog.update', $blog);
        $isEdit = true;

        return view('blogs.form', compact('blog','categories','formAction','isEdit'));
    }

    public function update(BlogRequest $request, Blog $blog)
    {
        $data = $request->all();

        $data['slug'] = $request->slug ?: Str::slug($request->title);

        if ($blog->status !== 'published' && $request->status === 'published') {
            $data['published_at'] = now();
        } elseif ($request->status === 'draft') {
            $data['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = Str::random(10).'_'.$file->getClientOriginalName();
            $path = $file->storeAs('blogs/thumbnail', $filename, 'public');

            if ($blog->thumbnail && Storage::disk('public')->exists($blog->thumbnail)) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $data['thumbnail'] = $path;
        }

        if ($request->filled('thumbnail_url')) {
            $data['thumbnail'] = (new PictureService())->insertFromUrl('blogs/thumbnail', $request->thumbnail_url);
        }

        $blog->update($data);

        $this->updateTags($blog, $request);

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('blog.index');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->thumbnail && Storage::disk('public')->exists($blog->thumbnail)) {
            Storage::disk('public')->delete($blog->thumbnail);
        }
        $blog->delete();

        toast(alert_deleted_text($this->title), 'success');

        return back();
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => ['required','image','max:5120'],
        ]);

        $file = $request->file('file');
        $filename = Str::random(10).'_'.$file->getClientOriginalName();
        $path = $file->storeAs('blogs/content', $filename, 'public');

        return response()->json(['location' => Storage::url($path)]);
    }

    private function updateTags($blog, $request) {
        $tagIds = collect($request->tags)->map(function ($tag) {
            if (is_numeric($tag)) {
                return (int) $tag;
            }

            $newTag = Tag::firstOrCreate(
                ['slug' => Str::slug($tag)],
                ['name' => $tag]
            );
            return $newTag->id;
        });

        $blog->tags()->sync($tagIds);
    }
}
