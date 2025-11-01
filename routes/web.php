<?php




use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BooksController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\UsersController;

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
Route::post('newsletter-submit','HomeController@newsletterSubmit')->name('newsletterSubmit');
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

Route::resource('admin/category', 'Admin\\CategoryController');

Route::resource('admin/category', 'Admin\\CategoryController');
Route::resource('admin/attributes', 'Admin\\AttributesController');
Route::resource('admin/attributes-value', 'Admin\\AttributesValueController');
Route::post('admin/get-attributes', 'Admin\\AttributesValueController@getdata')->name('get-attributes');
Route::post('admin/pro-img-id-delet', 'Admin\\AttributesValueController@img_delete')->name('pro-img-id-delet');
Route::post('admin/delete-product-variant', 'Admin\\AttributesValueController@deleteProVariant')->name('delete.product.variant');
Route::resource('admin/testimonial', 'Admin\\TestimonialController');
Route::resource('about/about', 'Admin, User\\AboutController');

Route::resource('traning-videos', 'TraningVideosController');
Route::resource('upcomingclasses', 'UpcomingclassesController');

//===================== Admin Routes =====================//

Route::middleware(['auth', 'role:1'])->prefix('admin')->group(function () {

    Route::get('/','Admin\AdminController@dashboard');

    Route::get('/dashboard','Admin\AdminController@dashboard')->name('admin.dashboard');

    Route::get('account/settings','Admin\UsersController@getSettings');
    Route::post('account/settings','Admin\UsersController@saveSettings');

    Route::get('project', function () {
        return view('dashboard.index-project');
    });

    Route::get('analytics', function () {
        return view('admin.dashboard.index-analytics');
    });


    Route::get('logo/edit','Admin\AdminController@logoEdit')->name('admin.logo.edit');
    Route::post('logo/upload','Admin\AdminController@logoUpload')->name('logo_upload');

    Route::get('favicon/edit','Admin\AdminController@faviconEdit')->name('admin.favicon.edit');

    Route::post('favicon/upload','Admin\AdminController@faviconUpload')->name('favicon_upload');

    Route::get('config/setting', 'Admin\AdminController@configSetting')->name('admin.config.setting');

    Route::get('contact/inquiries','Admin\AdminController@contactSubmissions');
    Route::get('contact/inquiries/{id}','Admin\AdminController@inquiryshow');
    Route::get('newsletter/inquiries','Admin\AdminController@newsletterInquiries');

    Route::any('contact/submissions/delete/{id}','Admin\AdminController@contactSubmissionsDelete');
    Route::any('newsletter/inquiries/delete/{id}','Admin\AdminController@newsletterInquiriesDelete');

    /* Config Setting Form Submit Route */
    Route::post('config/setting','Admin\AdminController@configSettingUpdate')->name('config_settings_update');

    //==================== Error pages Routes ====================//
    Route::get('403',function (){
        return view('pages.403');
    });

    Route::get('404',function (){
        return view('pages.404');
    });

    Route::get('405',function (){
        return view('pages.405');
    });

    Route::get('500',function (){
        return view('pages.500');
    });
    //============================================================//

    #Permission management
    Route::get('permission-management','PermissionController@getIndex');
    Route::get('permission/create','PermissionController@create');
    Route::post('permission/create','PermissionController@save');
    Route::get('permission/delete/{id}','PermissionController@delete');
    Route::get('permission/edit/{id}','PermissionController@edit');
    Route::post('permission/edit/{id}','PermissionController@update');

    #Role management
    Route::get('role-management','RoleController@getIndex');
    Route::get('role/create','RoleController@create');
    Route::post('role/create','RoleController@save');
    Route::get('role/delete/{id}','RoleController@delete');
    Route::get('role/edit/{id}','RoleController@edit');
    Route::post('role/edit/{id}','RoleController@update');

    #CRUD Generator
    Route::get('/crud-generator', ['uses' => 'ProcessController@getGenerator']);
    Route::post('/crud-generator', ['uses' => 'ProcessController@postGenerator']);

    #User Management routes
    // Route::get('users','Admin\\UsersController@Index');
    // Route::get('user/create','Admin\\UsersController@create');
    // Route::post('user/create','Admin\\UsersController@save');
    // Route::get('user/edit/{id}','Admin\\UsersController@edit');
    // Route::post('user/edit/{id}','Admin\\UsersController@update');
    // Route::get('user/delete/{id}','Admin\\UsersController@destroy');
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


    Route::resource('product', 'Admin\\ProductController');
    Route::get('product/{id}/delete', ['as' => 'product.delete', 'uses' => 'Admin\\ProductController@destroy']);
    Route::get('order/list', ['as' => 'order.list', 'uses' => 'Admin\\ProductController@orderList']);
    Route::get('order/detail/{id}', ['as' => 'order.list.detail', 'uses' => 'Admin\\ProductController@orderListDetail']);

     //Order Status Change Routes//
    Route::get('status/completed/{id}','Admin\\ProductController@updatestatuscompleted')->name('status.completed');
    Route::get('status/pending/{id}','Admin\\ProductController@updatestatusPending')->name('status.pending');

    // Pages (resource)
    Route::get('pages/data', [PageController::class, 'getData'])->name('admin.pages.data');
    // Route::post('page/{page}/toggle-status', [PageController::class, 'toggleStatus'])->name('admin.page.toggleStatus');
    // Route::get('page/trash', [PageController::class, 'trash'])->name('admin.page.trash');
    // Route::get('page/trash/data', [PageController::class, 'getTrashedData'])->name('admin.page.trash.data');
    // Route::post('page/{id}/restore', [PageController::class, 'restore'])->name('admin.page.restore');
    // Route::delete('page/{id}/force-delete', [PageController::class, 'forceDelete'])->name('admin.page.forceDelete');
    Route::resource('pages', PageController::class)->names('admin.pages');

    //sections within pages
    Route::post('pages/{page}/sections', [SectionController::class, 'store'])->name('admin.sections.store');
    Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('admin.sections.destroy');

    // Banners (resource)
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

    // News (resource)
    Route::get('news/data', [NewsController::class, 'getData'])->name('admin.news.data');
    Route::post('news/{news}/toggle-status', [NewsController::class, 'toggleStatus'])->name('admin.news.toggleStatus');
    Route::get('news/trash', [NewsController::class, 'trash'])->name('admin.news.trash');
    Route::get('news/trash/data', [NewsController::class, 'getTrashedData'])->name('admin.news.trash.data');
    Route::post('news/{id}/restore', [NewsController::class, 'restore'])->name('admin.news.restore');
    Route::delete('news/{id}/force-delete', [NewsController::class, 'forceDelete'])->name('admin.news.forceDelete');
    Route::resource('news', NewsController::class)->names('admin.news');
});
require __DIR__.'/auth.php';
