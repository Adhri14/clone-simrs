<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use App\Models\BlogAdmin;
use App\Models\CategoryAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $post_count = BlogAdmin::count();
        $posts = BlogAdmin::where('title', 'like', '%' . $search . '%')->simplePaginate();
        return view('vendor.blog.index', ['posts' => $posts, 'post_count' => $post_count]);
    }

    public function create(Request $request)
    {
        $categories = CategoryAdmin::get();
        return view('vendor.blog.create', ['categories' => $categories]);
    }

    public function store(Request $request)
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
        return redirect()->route('blog.index');
    }

    public function destroy(Request $request, $id)
    {
        BlogAdmin::where('id', $id)->delete();
        $request->session()->flash('success', 'Berhasil hapus blog');
        return redirect()->route('blog.index');
    }

    public function updateStatus(Request $request, $id)
    {
        $post = BlogAdmin::find($id);
        $post->update(['status' => $post->status === 'active' ? 'non-active' : 'active']);
        $post->save();
        $request->session()->flash('success', 'Berhasil update status');
        return redirect()->route('blog.index');
    }
}
