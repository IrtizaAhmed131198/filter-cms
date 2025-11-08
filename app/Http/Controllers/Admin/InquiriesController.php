<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inquiry;
use App\Models\Newsletter;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InquiriesController extends Controller
{
    public function contactSubmissions()
    {
	 	return view('admin.inquires.contact_inquiries');
	}

    public function getContactData(Request $request)
    {
	 	$contact_inquiries = Inquiry::all();

        return DataTables::of($contact_inquiries)
            ->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '<a href="' . route('admin.contact.inquiry.show', $row->id) . '"
                                class="btn btn-sm btn-info" title="View Inquiry">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a> ';
                $actions .= '<button class="btn btn-sm btn-danger deleteContact"
                                data-id="' . $row->id . '" title="Delete Inquiry">
                                <i class="la la-trash"></i>
                            </button>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);

	}

	public function contactSubmissionsDelete($id)
    {

		$del = DB::table('inquiry')->where('id',$id)->delete();

		log_activity('delete', Inquiry::class, $del->id, "Deleted inquiry {$del->email}");
        return response()->json(['success' => 'Inquiry deleted successfully.']);

	}

    public function contactSubmissionsBulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Inquiry::whereIn('id', $ids)->delete();

        log_activity('bulk_delete', Inquiry::class, null, 'Bulk deleted iquiries: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected iquiries deleted successfully.']);
    }

    public function inquiryshow($id)
    {
            $inquiry = inquiry::findOrFail($id);
            return view('admin.inquires.inquirydetail', compact('inquiry'));
    }

	public function newsletterInquiries()
    {
	 	return view('admin.inquires.newsletter_inquiries');
	}

    public function getNewsletterData(Request $request)
    {
	 	$newsletter_inquiries = Newsletter::all();

        return DataTables::of($newsletter_inquiries)
            ->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '<button class="btn btn-sm btn-danger deleteNewsletter"
                                data-id="' . $row->id . '" title="Delete Inquiry">
                                <i class="la la-trash"></i>
                            </button>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);

	}

	public function newsletterInquiriesDelete($id)
    {
        $del = DB::table('newsletter')->where('id',$id)->delete();

		log_activity('delete', Newsletter::class, $del->id, "Deleted newsletter {$del->email}");
        return response()->json(['success' => 'Newsletter deleted successfully.']);
	}
}
