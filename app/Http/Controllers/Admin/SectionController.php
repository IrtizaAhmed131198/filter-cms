<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(StoreSectionRequest $request, $pageId)
    {
        $page = Page::findOrFail($pageId);
        $section = $page->sections()->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Section created successfully.',
            'section' => $section
        ]);
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Section deleted successfully.'
        ]);
    }
}
