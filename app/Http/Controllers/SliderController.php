<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SliderController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Slider';

        $this->middleware(['permission:View Slider'])->only('index', 'show');
        $this->middleware(['permission:Create Slider'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Slider'])->only('edit', 'update');
        $this->middleware(['permission:Delete Slider'])->only('destroy');
    }

    public function index()
    {
        $sliders = Slider::latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $createLink = route('slider.create');

        $title = $this->title;

        return view('sliders.index', compact('sliders', 'createLink', 'title'));
    }

    public function create()
    {
        $actionLink = route('slider.store');
        $indexLink = route('slider.index');

        $title = $this->title;

        return view('sliders.form', compact('actionLink', 'indexLink', 'title'));
    }

    public function show(Slider $slider)
    {
        $editLink = route('slider.edit', $slider);
        $deleteLink = route('slider.destroy', $slider);
        $indexLink = route('slider.index');

        $title = $this->title;

        return view('sliders.show', compact('slider', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(SliderRequest $request)
    {
        $slider = Slider::create($request->all());

        insert_picture($request->picture, $slider);

        toast(alert_created_text($this->title),'success');
        return redirect()->route('slider.index');
    }

    public function edit(Slider $slider)
    {
        $actionLink = route('slider.update', $slider);
        $indexLink = route('slider.index');

        $title = $this->title;

        return view('sliders.form', compact('actionLink', 'indexLink', 'slider', 'title'));
    }

    public function update(SliderRequest $request, Slider $slider)
    {
        $slider->update($request->all());

        if ($request->picture) {
            insert_picture($request->picture, $slider);
        }

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('slider.index');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('slider.index');
    }
}
