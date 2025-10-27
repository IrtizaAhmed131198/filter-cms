<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\File;

class NewsController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return view('admin.news.index');
    }

    public function getData(Request $request)
    {
        $items = News::orderByDesc('id')->get();

        return DataTables::of($items)
            ->addColumn('image', function ($row) {
                return '<img src="'.asset($row->image).'" width="120">';
            })
            ->addColumn('status', function ($row) {
                $badgeClass = $row->status ? 'success' : 'danger';
                $text = $row->status ? 'Active' : 'Inactive';
                return '<button class="btn btn-sm btn-' . $badgeClass . ' toggleNewsStatus" data-id="' . $row->id . '">' . $text . '</button>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.url('admin/news/'.$row->id.'/edit').'" class="btn btn-sm btn-info"><i class="la la-pencil"></i></a>';
                $delete = '<button class="btn btn-sm btn-danger deleteNews" data-id="'.$row->id.'"><i class="la la-trash"></i></button>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(StoreNewsRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/news/', 'news');
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('message', 'News created successfully!');
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(UpdateNewsRequest $request, News $news)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteFile($news->image);
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/news/', 'news');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('message', 'News updated successfully!');
    }

    public function destroy(News $news)
    {
        $news->delete();
        return response()->json(['success' => 'News deleted successfully.']);
    }

    public function toggleStatus(News $news)
    {
        $news->status = !$news->status;
        $news->save();

        return response()->json([
            'success' => true,
            'status' => $news->status ? 'Active' : 'Inactive',
        ]);
    }

    public function trash()
    {
        return view('admin.news.trash');
    }

    public function getTrashedData(Request $request)
    {
        $items = News::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($items)
            ->addColumn('image', function ($row) {
                return '<img src="'.asset($row->image).'" width="120">';
            })
            ->addColumn('action', function ($row) {
                $restore = '<button class="btn btn-sm btn-success restoreNews" data-id="'.$row->id.'">
                                <i class="la la-refresh"></i> Restore
                            </button>';
                $delete = '<button class="btn btn-sm btn-danger forceDeleteNews" data-id="'.$row->id.'">
                                <i class="la la-trash"></i> Delete Permanently
                            </button>';
                return $restore . ' ' . $delete;
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $news = News::withTrashed()->findOrFail($id);
        $news->restore();

        return response()->json(['success' => 'News restored successfully!']);
    }

    public function forceDelete($id)
    {
        $news = News::withTrashed()->findOrFail($id);

        if ($news->image && File::exists(public_path($news->image))) {
            File::delete(public_path($news->image));
        }

        $news->forceDelete();

        return response()->json(['success' => 'News permanently deleted.']);
    }
}
