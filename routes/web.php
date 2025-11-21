<?php


use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BooksController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\InquiriesController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributesValueController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// User login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

// Admin login
Route::get('/admin/login', [AuthenticatedSessionController::class, 'createAdmin'])->name('admin.login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'storeAdmin'])->name('admin.login.store');

// Logout (shared)
// Normal user logout
Route::get('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');

// Admin logout
Route::get('/admin/logout', [AuthenticatedSessionController::class, 'logout'])->name('admin.logout');

//==============================================================//

//Log Viewer
Route::get('log-viewers', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@index')->name('log-viewers');
Route::get('log-viewers/logs', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@listLogs')->name('log-viewers.logs');
Route::delete('log-viewers/logs/delete', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@delete')->name('log-viewers.logs.delete');
Route::get('log-viewers/logs/{date}', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@show')->name('log-viewers.logs.show');
Route::get('log-viewers/logs/{date}/download', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@download')->name('log-viewers.logs.download');
Route::get('log-viewers/logs/{date}/{level}', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@showByLevel')->name('log-viewers.logs.filter');
Route::get('log-viewers/logs/{date}/{level}/search', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@search')->name('log-viewers.logs.search');
Route::get('log-viewers/logcheck', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@logCheck')->name('log-viewers.logcheck');


Route::get('auth/{provider}/','Auth\SocialLoginController@redirectToProvider');
Route::get('{provider}/callback','Auth\SocialLoginController@handleProviderCallback');
// Auth::routes();


//===================== Account Area Routes =====================//


Route::get('signin','GuestController@signin')->name('signin');
Route::get('signup','GuestController@signup')->name('signup');
Route::get('account','LoggedInController@account')->name('account');
Route::get('orders','LoggedInController@orders')->name('orders');
Route::get('account-detail','LoggedInController@accountDetail')->name('accountDetail');

Route::post('update/account','LoggedInController@updateAccount')->name('update.account');
Route::get('signout', function() {
        Auth::logout();

        Session::flash('flash_message', 'You have logged out  Successfully');
        Session::flash('alert-class', 'alert-success');

        return redirect('signin');
});
// Auth::routes();

Route::get('account/friends','LoggedInController@friends')->name('friends');
Route::get('account/upload','LoggedInController@upload')->name('upload');
Route::get('account/password','LoggedInController@password')->name('password');

Route::get('/success','OrderController@success')->name('success');

Route::post('update/profile','LoggedInController@update_profile')->name('update_profile');
Route::post('update/uploadPicture','LoggedInController@uploadPicture')->name('uploadPicture');


//===================== Front Routes =====================//

Route::get('/','HomeController@index')->name('home');
Route::get('upcoming-classes','HomeController@upcoming_classes')->name('upcoming-classes');
Route::get('online-classes/{id?}','HomeController@online_classes')->name('classes');
Route::get('learn-to-play','HomeController@play')->name('play');
// Route::get('store','HomeController@store')->name('store');
Route::get('contact','HomeController@contact')->name('contact');




Route::post('careerSubmit','HomeController@careerSubmit')->name('contactUsSubmit');
Route::post('update-content','HomeController@updateContent')->name('update-content');

//=================================================================//

Route::get('lang/{lang}', ['as' => 'lang.switch', 'uses' => 'LanguageController@switchLang']);

/*
Route::get('/test', function() {
    App::setlocale('arab');
    dd(App::getlocale());
    if(App::setlocale('arab')) {

    }
});
*/
/* Form Validation */


//===================== Shop Routes Below ========================//

Route::get('store','ProductController@shop')->name('shop');
Route::get('store-detail/{id}','ProductController@shopDetail')->name('shopDetail');
Route::get('category-detail/{id}','ProductController@categoryDetail')->name('categoryDetail');

Route::post('/cartAdd', 'ProductController@saveCart')->name('save_cart');
Route::any('/remove-cart/{id}', 'ProductController@removeCart')->name('remove_cart');
Route::post('/updateCart', 'ProductController@updateCart')->name('update_cart');
Route::get('/cart', 'ProductController@cart')->name('cart');
Route::get('/payment', 'OrderController@payment')->name('payment');
Route::get('invoice/{id}','LoggedInController@invoice')->name('invoice');
Route::get('/payment', 'OrderController@payment')->name('payment');
Route::get('/checkout', 'OrderController@checkout')->name('checkout');
Route::post('/place-order', 'OrderController@placeOrder')->name('order.place');
Route::post('/new-order', 'OrderController@newOrder')->name('new.place');
Route::post('shipping', 'ProductController@shipping')->name('shipping');

/*wishlist*/
Route::get('/wishlist', 'WishlistController@index')->name('customer.wishlist.list');
Route::any('/wishlist/add/{id?}', 'WishlistController@addwishlist')->name('wishlist.add');
Route::any('/wishlist/add/{id?}', 'WishlistController@addwishlist')->name('wishlist.add');
/*wishlist end*/

Route::post('/language-form', 'ProductController@language')->name('language');

//==============================================================//

Route::get('user-ip', 'HomeController@getusersysteminfo');

//===================== New Crud-Generators Routes Will Auto Display Below ========================//
route::get('status/delivered/{id}','admin\\productcontroller@updatestatusdelivered')->name('status.delivered');
route::get('status/cancelled/{id}','admin\\productcontroller@updatestatuscancelled')->name('status.cancelled');

Route::resource('admin/attributes-value', 'Admin\\AttributesValueController');
Route::post('admin/get-attributes', 'Admin\\AttributesValueController@getdata')->name('get-attributes');
Route::post('admin/pro-img-id-delet', 'Admin\\AttributesValueController@img_delete')->name('pro-img-id-delet');
Route::post('admin/delete-product-variant', 'Admin\\AttributesValueController@deleteProVariant')->name('delete.product.variant');
Route::resource('admin/testimonial', 'Admin\\TestimonialController');
Route::resource('about/about', 'Admin, User\\AboutController');

Route::resource('traning-videos', 'TraningVideosController');
Route::resource('upcomingclasses', 'UpcomingclassesController');

//===================== Admin Routes =====================//

Route::middleware(['auth', 'role:1,2']) // Only super_admin & admin can access /admin
    ->prefix('admin')
    ->group(function () {

    // 🧭 Dashboard (Both super admin + admin can see)
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.home');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ⚙️ Account Settings
    Route::get('account/settings', [UsersController::class, 'getSettings']);
    Route::post('account/settings', [UsersController::class, 'saveSettings']);

    // 🖼 Site Config (only super admin)
    Route::middleware('permission:manage_site_config')->group(function () {
        Route::get('config/setting', [AdminController::class, 'configSetting'])->name('admin.config.setting');
        Route::post('config/setting', [AdminController::class, 'configSettingUpdate'])->name('config_settings_update');
    });

    // 🖼 Logo (only super admin)
    Route::middleware('permission:manage_logo')->group(function () {
        Route::get('logo/edit', [AdminController::class, 'logoEdit'])->name('admin.logo.edit');
        Route::post('logo/upload', [AdminController::class, 'logoUpload'])->name('logo_upload');
    });

    // 🖼 Favicon (only super admin)
    Route::middleware('permission:manage_favicon')->group(function () {
        Route::get('favicon/edit', [AdminController::class, 'faviconEdit'])->name('admin.favicon.edit');
        Route::post('favicon/upload', [AdminController::class, 'faviconUpload'])->name('favicon_upload');
    });

    // ✉️ Contact/Newsletter (admin or super admin)
    Route::middleware('permission:view_inquiries')->group(function () {
        Route::get('contact/inquiries', [InquiriesController::class, 'contactSubmissions'])->name('admin.contact.inquiries');
        Route::get('contact/inquiries/data', [InquiriesController::class, 'getContactData'])->name('admin.contact.data');
        Route::get('contact/inquiries/{id}', [InquiriesController::class, 'inquiryshow'])->name('admin.contact.inquiry.show');
        Route::delete('contact/submissions/delete/{id}', [InquiriesController::class, 'contactSubmissionsDelete'])->name('admin.contact.destroy');
        Route::delete('contact/submissions/bulk-delete', [InquiriesController::class, 'contactSubmissionsBulkDelete'])->name('admin.contact.bulkDelete');

        Route::get('newsletter/inquiries', [InquiriesController::class, 'newsletterInquiries'])->name('admin.newsletter.inquiries');
        Route::get('newsletter/inquiries/data', [InquiriesController::class, 'getNewsletterData'])->name('admin.newsletter.data');
        Route::delete('newsletter/submissions/delete/{id}', [InquiriesController::class, 'newsletterInquiriesDelete'])->name('admin.newsletter.destroy');
        Route::delete('newsletter/submissions/bulk-delete', [InquiriesController::class, 'newsletterInquiriesBulkDelete'])->name('admin.newsletter.bulkDelete');
    });

    // 🔐 Role & Permission Management — only super admin
    Route::middleware('role:1')->group(function () {
        # Permission management
        Route::get('permission-management', [PermissionController::class, 'index'])->name('admin.permissions.index');
        Route::post('permission-management', [PermissionController::class, 'store'])->name('admin.permissions.store');
        Route::post('permission/assign', [PermissionController::class, 'assignToRole'])->name('admin.permissions.assign');
        Route::delete('permission/{id}', [PermissionController::class, 'destroy'])->name('admin.permissions.delete');
        Route::get('permissions/role/{id}', [PermissionController::class, 'getRolePermissions'])->name('admin.permissions.role.permissions');

        # Role management
        Route::get('role-management', [RoleController::class, 'getIndex']);
        Route::get('role/create', [RoleController::class, 'create']);
        Route::post('role/create', [RoleController::class, 'save']);
        Route::get('role/edit/{id}', [RoleController::class, 'edit']);
        Route::post('role/edit/{id}', [RoleController::class, 'update']);
        Route::get('role/delete/{id}', [RoleController::class, 'delete']);
    });

    // 👥 User Management (permission-based)
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('users/data', [UsersController::class, 'getData'])->name('admin.users.data');
        Route::post('users/{id}/toggle-status', [UsersController::class, 'toggleStatus'])->name('admin.users.toggleStatus');
        Route::get('users/trash', [UsersController::class, 'trash'])->name('admin.users.trash');
        Route::get('users/trash/data', [UsersController::class, 'getTrashedData'])->name('admin.users.trash.data');
        Route::post('users/{id}/restore', [UsersController::class, 'restore'])->name('admin.users.restore');
        Route::delete('users/{id}/force-delete', [UsersController::class, 'forceDelete'])->name('admin.users.forceDelete');
        Route::delete('users/bulk-delete', [UsersController::class, 'bulkDelete'])->name('admin.users.bulkDelete');
        Route::post('users/bulk-restore', [UsersController::class, 'bulkRestore'])->name('admin.users.bulkRestore');
        Route::delete('users/bulk-force-delete', [UsersController::class, 'bulkForceDelete'])->name('admin.users.bulkForceDelete');
        Route::resource('users', UsersController::class)->names('admin.users');
    });

    // 🛍 Product Management
    Route::middleware('permission:manage_products')->group(function () {
        Route::resource('product', ProductController::class);
        Route::get('product/{id}/delete', [ProductController::class, 'destroy'])->name('product.delete');
        Route::get('order/list', [ProductController::class, 'orderList'])->name('order.list');
        Route::get('order/detail/{id}', [ProductController::class, 'orderListDetail'])->name('order.list.detail');
        Route::get('status/completed/{id}', [ProductController::class, 'updatestatuscompleted'])->name('status.completed');
        Route::get('status/pending/{id}', [ProductController::class, 'updatestatusPending'])->name('status.pending');
    });

    // 📄 Pages & Sections
    Route::middleware('permission:manage_pages')->group(function () {
        Route::get('pages/data', [PageController::class, 'getData'])->name('admin.pages.data');
        Route::resource('pages', PageController::class)->names('admin.pages');
        Route::post('pages/{page}/sections', [SectionController::class, 'store'])->name('admin.sections.store');
        Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('admin.sections.destroy');
    });

    // 🖼 Attribute
    Route::middleware('permission:manage_attribute')->group(function () {
        Route::get('attribute/data', [AttributeController::class, 'getData'])->name('admin.attribute.data');
        Route::post('attribute/{attribute}/toggle-status', [AttributeController::class, 'toggleStatus'])->name('admin.attribute.toggleStatus');
        Route::get('attribute/trash', [AttributeController::class, 'trash'])->name('admin.attribute.trash');
        Route::get('attribute/trash/data', [AttributeController::class, 'getTrashedData'])->name('admin.attribute.trash.data');
        Route::post('attribute/{id}/restore', [AttributeController::class, 'restore'])->name('admin.attribute.restore');
        Route::delete('attribute/{id}/force-delete', [AttributeController::class, 'forceDelete'])->name('admin.attribute.forceDelete');
        Route::delete('attribute/bulk-delete', [AttributeController::class, 'bulkDelete'])->name('admin.attribute.bulkDelete');
        Route::post('attribute/bulk-restore', [AttributeController::class, 'bulkRestore'])->name('admin.attribute.bulkRestore');
        Route::delete('attribute/bulk-force-delete', [AttributeController::class, 'bulkForceDelete'])->name('admin.attribute.bulkForceDelete');
        Route::resource('attribute', AttributeController::class)->names('admin.attribute');
    });

    // 🖼 Attribute Value
    Route::middleware('permission:manage_attribute_value')->group(function () {
        Route::get('attribute-value/data', [AttributesValueController::class, 'getData'])->name('admin.attributesvalue.data');
        Route::post('attribute-value/{attributeValue}/toggle-status', [AttributesValueController::class, 'toggleStatus'])->name('admin.attributesvalue.toggleStatus');
        Route::get('attribute-value/trash', [AttributesValueController::class, 'trash'])->name('admin.attributesvalue.trash');
        Route::get('attribute-value/trash/data', [AttributesValueController::class, 'getTrashedData'])->name('admin.attributesvalue.trash.data');
        Route::post('attribute-value/{id}/restore', [AttributesValueController::class, 'restore'])->name('admin.attributesvalue.restore');
        Route::delete('attribute-value/{id}/force-delete', [AttributesValueController::class, 'forceDelete'])->name('admin.attributesvalue.forceDelete');
        Route::delete('attribute-value/bulk-delete', [AttributesValueController::class, 'bulkDelete'])->name('admin.attributesvalue.bulkDelete');
        Route::post('attribute-value/bulk-restore', [AttributesValueController::class, 'bulkRestore'])->name('admin.attributesvalue.bulkRestore');
        Route::delete('attribute-value/bulk-force-delete', [AttributesValueController::class, 'bulkForceDelete'])->name('admin.attributesvalue.bulkForceDelete');
        Route::resource('attribute-value', AttributesValueController::class)->names('admin.attributesvalue');
    });

    // 🖼 Banner Management
    Route::middleware('permission:manage_banners')->group(function () {
        Route::get('banner/data', [BannerController::class, 'getData'])->name('admin.banner.data');
        Route::post('banner/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('admin.banner.toggleStatus');
        Route::get('banner/trash', [BannerController::class, 'trash'])->name('admin.banner.trash');
        Route::get('banner/trash/data', [BannerController::class, 'getTrashedData'])->name('admin.banner.trash.data');
        Route::post('banner/{id}/restore', [BannerController::class, 'restore'])->name('admin.banner.restore');
        Route::delete('banner/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('admin.banner.forceDelete');
        Route::delete('banner/bulk-delete', [BannerController::class, 'bulkDelete'])->name('admin.banner.bulkDelete');
        Route::post('banner/bulk-restore', [BannerController::class, 'bulkRestore'])->name('admin.banner.bulkRestore');
        Route::delete('banner/bulk-force-delete', [BannerController::class, 'bulkForceDelete'])->name('admin.banner.bulkForceDelete');
        Route::post('admin/banner/sort', [BannerController::class, 'sort'])->name('admin.banner.sort');
        Route::resource('banner', BannerController::class)->names('admin.banner');
    });

    // 🖼 Category Management
    Route::middleware('permission:manage_category')->group(function () {
        Route::get('category/data', [CategoryController::class, 'getData'])->name('admin.category.data');
        Route::post('category/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('admin.category.toggleStatus');
        Route::get('category/trash', [CategoryController::class, 'trash'])->name('admin.category.trash');
        Route::get('category/trash/data', [CategoryController::class, 'getTrashedData'])->name('admin.category.trash.data');
        Route::post('category/{id}/restore', [CategoryController::class, 'restore'])->name('admin.category.restore');
        Route::delete('category/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('admin.category.forceDelete');
        Route::delete('category/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('admin.category.bulkDelete');
        Route::post('category/bulk-restore', [CategoryController::class, 'bulkRestore'])->name('admin.category.bulkRestore');
        Route::delete('category/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])->name('admin.category.bulkForceDelete');
        Route::resource('category', CategoryController::class)->names('admin.category');
    });

    // 🖼 SubCategory Management
    Route::middleware('permission:manage_subcategory')->group(function () {
        Route::get('subcategory/data', [SubCategoryController::class, 'getData'])->name('admin.subcategory.data');
        Route::post('subcategory/{subcategory}/toggle-status', [SubCategoryController::class, 'toggleStatus'])->name('admin.subcategory.toggleStatus');
        Route::get('subcategory/trash', [SubCategoryController::class, 'trash'])->name('admin.subcategory.trash');
        Route::get('subcategory/trash/data', [SubCategoryController::class, 'getTrashedData'])->name('admin.subcategory.trash.data');
        Route::post('subcategory/{id}/restore', [SubCategoryController::class, 'restore'])->name('admin.subcategory.restore');
        Route::delete('subcategory/{id}/force-delete', [SubCategoryController::class, 'forceDelete'])->name('admin.subcategory.forceDelete');
        Route::delete('subcategory/bulk-delete', [SubCategoryController::class, 'bulkDelete'])->name('admin.subcategory.bulkDelete');
        Route::post('subcategory/bulk-restore', [SubCategoryController::class, 'bulkRestore'])->name('admin.subcategory.bulkRestore');
        Route::delete('subcategory/bulk-force-delete', [SubCategoryController::class, 'bulkForceDelete'])->name('admin.subcategory.bulkForceDelete');
        Route::resource('subcategory', SubCategoryController::class)->names('admin.subcategory');
    });

});
require __DIR__.'/auth.php';
