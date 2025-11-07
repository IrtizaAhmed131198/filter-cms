<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\imagetable;
use Illuminate\Support\Facades\Auth;
use App\inquiry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Traits\FileUploadTrait;

class AdminController extends Controller
{
    use FileUploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return void
     */

    public function index()
    {
        return view('auth.login')->with('title','Josue Francois');;
    }

	public function dashboard()
    {
        return view('admin.dashboard.index');
    }


    public function configSettingUpdate()
    {

        if(isset($_POST)) {

            foreach($_POST as $key=>$value) {
                if($key=='_token') {
                    continue;
                }

                DB::UPDATE("UPDATE m_flag set flag_value = '".$value."',flag_additionalText = '".$value."' where flag_type = '".$key."'");


            }
        }
		session()->flash('message', 'Successfully Updated');
        return redirect('admin/config/setting');

    }

	public function faviconEdit() {

		$user = Auth::user();
		$favicon = DB::table('imagetable')->where('table_name', 'favicon')->first();

		return view('admin.dashboard.index-favicon')->with(compact('favicon'))->with('title',$user->name.' Edit Favicon');

    }

    public function faviconUpload(Request $request)
    {
        // Validate image only if provided
        $validated = $request->validate([
            'image' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:10000',
        ]);

        $imagetable = imagetable::where('table_name', 'favicon')->first();
        $uploadFolder = 'uploads/favicon/';

        $oldData = $imagetable ? $imagetable->toArray() : [];

        // If new file is uploaded
        if ($request->hasFile('image')) {
            // If an old favicon exists, delete it
            if ($imagetable && $imagetable->img_path) {
                $this->deleteFile($imagetable->img_path);
            }

            // Upload new file using the trait
            $filePath = $this->uploadFile($request->file('image'), $uploadFolder, 'favicon');

            // Save or update record
            if (!$imagetable) {
                imagetable::create([
                    'table_name' => 'favicon',
                    'img_path'   => $filePath,
                ]);
                log_activity('create', imagetable::class, $imagetable->id, 'Uploaded new favicon', ['after' => $imagetable->toArray()]);
            } else {
                $imagetable->update(['img_path' => $filePath]);
                log_activity('update', imagetable::class, $imagetable->id, 'Updated favicon', ['before' => $oldData, 'after' => $imagetable->toArray()]);
            }
        }

        session()->flash('message', 'Successfully updated the favicon');
        return redirect('admin/favicon/edit');
    }


	public function logoEdit() {

		$user = Auth::user();

		return view('admin.dashboard.index-logo')->with('title',$user->name.'  Edit Logo');

    }

    public function logoUpload(Request $request)
    {
        // ✅ Validate if image is uploaded
        $validated = $request->validate([
            'image' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:10000',
        ]);

        $imagetable = imagetable::where('table_name', 'logo')->first();
        $uploadFolder = 'uploads/logo/';

        $oldData = $imagetable ? $imagetable->toArray() : [];

        // ✅ If a new image file is uploaded
        if ($request->hasFile('image')) {

            // 🔹 Delete old image if exists
            if ($imagetable && $imagetable->img_path) {
                $this->deleteFile($imagetable->img_path);
            }

            // 🔹 Upload new image using FileUploadTrait
            $filePath = $this->uploadFile($request->file('image'), $uploadFolder, 'logo');

            // 🔹 Save or update in DB
            if (!$imagetable) {
                imagetable::create([
                    'table_name' => 'logo',
                    'img_path'   => $filePath,
                ]);
                log_activity('create', imagetable::class, $imagetable->id, 'Uploaded new logo', ['after' => $imagetable->toArray()]);
            } else {
                $imagetable->update(['img_path' => $filePath]);
                log_activity('update', imagetable::class, $imagetable->id, 'Updated logo', ['before' => $oldData, 'after' => $imagetable->toArray()]);
            }
        }

        session()->flash('message', 'Successfully updated the logo');
        return redirect('admin/logo/edit');
    }


	public function contactSubmissions() {
	 	$contact_inquiries = DB::table('inquiry')->get();

	 	return view('admin.inquires.contact_inquiries', compact('contact_inquiries'));

	}

	public function contactSubmissionsDelete($id) {

		  $del = DB::table('inquiry')->where('id',$id)->delete();

		  if($del) {
			  return redirect('admin/contact/inquiries')->with('flash_message', 'Contact deleted!');
		  }

	}

    public function inquiryshow($id)
    {
            $inquiry = inquiry::findOrFail($id);
            return view('admin.inquires.inquirydetail', compact('inquiry'));
    }

	public function newsletterInquiries() {

	 	$newsletter_inquiries = DB::table('newsletter')->get();

	 	return view('admin.inquires.newsletter_inquiries', compact('newsletter_inquiries'));

	}

	public function newsletterInquiriesDelete($id) {
		  $del = DB::table('newsletter')->where('id',$id)->delete();

		  if($del) {
			  return redirect('admin/newsletter/inquiries')->with('flash_message', 'Contact deleted!');
		  }

	}

	public function configSetting(){
		return view('admin.dashboard.index-config');
	}

}
