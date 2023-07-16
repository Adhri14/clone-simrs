<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use App\Models\BlogAdmin;
use App\Models\CategoryAdmin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $search = $request->search;
        $post_count = BlogAdmin::count();
        $posts = BlogAdmin::where('title', 'like', '%' . $search . '%')->simplePaginate();
        return view('vendor.blog.index', ['posts' => $posts, 'post_count' => $post_count]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $categories = CategoryAdmin::get();
        return view('vendor.blog.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'status' => 'required',
            'body' => 'required|string'
        ]);

        BlogAdmin::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'slug' => Str::slug($request->title),
            'excerpt' => Str::limit(strip_tags($request->body), 200),
            'body' => $request->body,
            'published_at' => now()
        ]);

        $request->session()->flash('success', 'Berhasil tambah blog');
        return Redirect::route('blog.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $blog = BlogAdmin::find($id);
        return view('vendor.blog.show', ['data' => $blog]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $blog = BlogAdmin::find($id);
        $categories = CategoryAdmin::all();
        return view('vendor.blog.edit', ['data' => $blog, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'status' => 'required',
            'body' => 'required|string'
        ]);

        $blog = BlogAdmin::find($id);
        $blog->update($data);
        $blog->save();

        return Redirect::route('blog.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        BlogAdmin::where('id', $id)->delete();
        $request->session()->flash('success', 'Berhasil hapus blog');
        return Redirect::route('blog.index');
    }
}
