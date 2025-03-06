<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Halaman untuk user
$routes->get('/', 'Pages::index'); // Halaman utama
$routes->get('/home', 'Pages::index'); // Halaman home
$routes->get('pages/profile', 'Pages::profile'); // Halaman profile user
$routes->get('pages/article', 'Pages::article'); // Halaman article di user
$routes->get('pages/publication', 'Pages::publication'); // Halaman work instruction di user
$routes->get('pages/view/(:num)', 'Pages::view/$1');
$routes->post('/pages/profile/updated', 'Pages::updateProfile'); // Update profile user
$routes->get('pages/createTicket', 'Pages::createTicket'); // Membuat ticket
$routes->post('/pages/Ticket', 'Pages::storeTicket'); // Menyimpan setelah buat ticket
// Monitoring ticket
$routes->match(['get', 'post'], 'pages/monitoringTicket', 'Pages::monitoringTicket');
$routes->post('pages/searchTicket', 'Pages::searchTicket');
$routes->add('pages/monitoring_ticket/(:any)', 'Pages::monitoring_ticket/$1');

// Halaman untuk template/index
$routes->get('/corpIT', 'Pages::index'); // Akses tanpa login

// Untuk melakukan login dan register
$routes->get('/login', 'Home::index');
$routes->get('/register', 'Home::register');

// Halaman admin & superadmin
$routes->get('/user', 'User::index'); // Halaman profile admin & superadmin
$routes->post('/user/update', 'User::updateProfile'); // Update profile admin & superadmin
$routes->post('user/upload_ttd', 'User::uploadTtd');
$routes->post('/user/delete_ttd', 'User::deleteTtd');

//untuk halaman absensi admin
$routes->get('absensi', 'Absensi\Absensi::index');
$routes->post('absensi/tapIn', 'Absensi\Absensi::tapIn');
$routes->post('absensi/tapOut', 'Absensi\Absensi::tapOut');
$routes->post('absensi/submitPreAbsensi', 'Absensi\Absensi::submitPreAbsensi');
$routes->post('absensi/submitKegiatan', 'Absensi\Absensi::submitKegiatan');
$routes->get('absensi/history', 'Absensi\Absensi::history');
$routes->post('absensi/correctTapOut', 'Absensi\Absensi::correctTapOut');
$routes->post('absensi/start_new_attendance', 'Absensi\Absensi::start_new_attendance');
$routes->get('absensi/superadmin/history', 'Absensi\Absensi::superadminHistory');
$routes->get('/absensi/exportExcel', 'Absensi\Absensi::exportExcel');
$routes->get('/absensi/superadminExportExcel', 'Absensi\Absensi::superadminExportExcel');
$routes->get('/absensi/preview', 'Absensi\Absensi::preview');
$routes->get('/absensi/exportPdfsuper', 'Absensi\Absensi::exportPdfsuper');
$routes->get('/absensi/previewPdf', 'Absensi\Absensi::previewPdf');
$routes->post('/absensi/signPdf', 'Absensi\Absensi::signPdf');
$routes->get('/absensi/superadmin/history', 'Absensi\Absensi::superadminHistory');

//categories absensi yang dicontrol dari superadmin
$routes->get('/Absensi/categories', 'Absensi\AbsenCategory::index');
$routes->get('/Absensi/categories/create', 'Absensi\AbsenCategory::create');
$routes->post('/Absensi/categories/store', 'Absensi\AbsenCategory::store');
$routes->get('/Absensi/categories/edit/(:num)', 'Absensi\AbsenCategory::edit/$1');
$routes->post('/Absensi/categories/update/(:num)', 'Absensi\AbsenCategory::update/$1');
$routes->post('/Absensi/categories/delete/(:num)', 'Absensi\AbsenCategory::delete/$1');


//halaman untuk user list
$routes->get('/admin', 'Admin::index',);
$routes->get('/admin/index', 'Admin::index',);
$routes->post('/admin/update/(:num)', 'Admin::update/$1',);
$routes->get('/admin/(:num)', 'Admin::detail/$1',);
$routes->post('/admin/update/(:num)', 'Admin::update/$1',);
$routes->get('/admin/detail/(:num)', 'Admin::detail/$1',);

// user role permission yang dicontrol oleh admin
$routes->get('/admin/user_roles', 'UserRoleManagement::index',);
$routes->get('/admin/user_roles/edit/(:num)', 'UserRoleManagement::edit/$1',);
$routes->post('/admin/user_roles/update', 'UserRoleManagement::updateRole',);

$routes->group('admin', function ($routes) {
    //halaman role yang dicontrol oleh superadmin
    $routes->get('roles', 'RoleController::index');
    $routes->get('roles/create', 'RoleController::create');
    $routes->post('roles/store', 'RoleController::store');
    $routes->get('roles/edit/(:num)', 'RoleController::edit/$1');
    $routes->post('roles/update/(:num)', 'RoleController::update/$1');
    $routes->post('roles/delete/(:num)', 'RoleController::delete/$1');

    //halaman article
    $routes->get('article', 'ArticleController::index');
    $routes->post('article/update/(:num)', 'ArticleController::update/$1');
    $routes->post('article/store', 'ArticleController::store');

    //halaman categories untuk article dan wi yang dicontrol oleh superadmin
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/create', 'CategoryController::create');
    $routes->post('categories/store', 'CategoryController::store');
    $routes->get('categories/edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoryController::update/$1');
    $routes->delete('categories/delete/(:num)', 'CategoryController::delete/$1');

    //halaman work instruction
    $routes->get('fileuploads', 'FileUploadController::index');
    $routes->get('fileupload/create', 'FileUploadController::create');
    $routes->post('fileuploads/store', 'FileUploadController::store');
    $routes->get('fileupload/edit/(:num)', 'FileUploadController::edit/$1');
    $routes->post('fileupload/update/(:num)', 'FileUploadController::update/$1');
    $routes->get('fileupload/delete/(:num)', 'FileUploadController::delete/$1');
    $routes->post('fileupload/delete/(:num)', 'FileUploadController::delete/$1');

    //halaman hirarki directorate yang dicontrol oleh superadmin
    $routes->get('hirarki/directorate', 'Hirarki\DirectorateController::index');
    $routes->get('hirarki/directorate/create', 'Hirarki\DirectorateController::create');
    $routes->post('hirarki/directorate/store', 'Hirarki\DirectorateController::store');
    $routes->get('hirarki/directorate/edit/(:segment)', 'Hirarki\DirectorateController::edit/$1');
    $routes->post('hirarki/directorate/update/(:segment)', 'Hirarki\DirectorateController::update/$1');
    $routes->post('hirarki/directorate/delete/(:segment)', 'Hirarki\DirectorateController::delete/$1');

    // halaman hirarki division yang dicontrol oleh superadmin
    $routes->get('hirarki/division', 'Hirarki\DivisionController::index');
    $routes->get('hirarki/division/create', 'Hirarki\DivisionController::create');
    $routes->post('hirarki/division/store', 'Hirarki\DivisionController::store');
    $routes->get('hirarki/division/edit/(:segment)', 'Hirarki\DivisionController::edit/$1');
    $routes->post('hirarki/division/update/(:segment)', 'Hirarki\DivisionController::update/$1');
    $routes->post('hirarki/division/delete/(:segment)', 'Hirarki\DivisionController::delete/$1');

    // halaman departments yang dicontrol oleh superadmin
    $routes->get('hirarki/departement', 'Hirarki\DepartementController::index');
    $routes->get('hirarki/departement/create', 'Hirarki\DepartementController::create');
    $routes->post('hirarki/departement/store', 'Hirarki\DepartementController::store');
    $routes->get('hirarki/departement/edit/(:segment)', 'Hirarki\DepartementController::edit/$1');
    $routes->post('hirarki/departement/update/(:segment)', 'Hirarki\DepartementController::update/$1');
    $routes->post('hirarki/departement/delete/(:segment)', 'Hirarki\DepartementController::delete/$1');

    // halaman sub departements yang dicontrol oleh superadmin
    $routes->get('hirarki/subdepart', 'Hirarki\SubdepartController::index');
    $routes->get('hirarki/subdepart/create', 'Hirarki\SubdepartController::create');
    $routes->post('hirarki/subdepart/store', 'Hirarki\SubdepartController::store');
    $routes->get('hirarki/subdepart/edit/(:segment)', 'Hirarki\SubdepartController::edit/$1');
    $routes->post('hirarki/subdepart/update/(:segment)', 'Hirarki\SubdepartController::update/$1');
    $routes->post('hirarki/subdepart/delete/(:segment)', 'Hirarki\SubdepartController::delete/$1');

    //halaman tracking activity untuk admin
    $routes->group('activity', function ($routes) {
        $routes->get('/', 'Activity\AdminActivity::index');
        $routes->get('create', 'Activity\AdminActivity::create');
        $routes->post('store', 'Activity\AdminActivity::store');
        $routes->get('edit/(:num)', 'Activity\AdminActivity::edit/$1');
        $routes->post('update/(:num)', 'Activity\AdminActivity::update/$1');
        $routes->post('delete/(:num)', 'Activity\AdminActivity::delete/$1');
        $routes->get('export/(:alpha)', 'Activity\AdminActivity::export/$1');
        $routes->get('previewPdf', 'Activity\AdminActivity::previewPdf');
        $routes->get('previewHistoryPdf', 'Activity\AdminActivity::previewHistoryPdf');
        $routes->get('downloadHistoryPdf', 'Activity\AdminActivity::downloadHistoryPdf');
        $routes->get('bulk-upload', 'Activity\AdminActivity::bulkUpload');
        $routes->get('lembur.xlsx', 'Activity\AdminActivity::downloadTemplate');
        $routes->post('process-bulk-upload', 'Activity\AdminActivity::processBulkUpload');
        $routes->get('history', 'Activity\AdminActivity::history');
        $routes->get('exportsuper/(:any)', 'Activity\AdminActivity::exportsuper/$1');
        $routes->post('sign', 'Activity\AdminActivity::sign');
        $routes->get('signed/(:num)/(:num)/(:num)', 'Activity\AdminActivity::viewSignedDocument/$1/$2/$3');
    });

    $routes->get('activity/sign', 'SignPdfController::index');
    $routes->get('activity/sign_admin', 'SignPdfController::admin_sign');
    $routes->get('signpdf/downloadPdfsZip', 'SignPdfController::downloadPdfsZip');
});

$routes->get('pj-assignment', 'PjAssignmentController::index');
$routes->get('pj-assignment/get-users/(:num)', 'PjAssignmentController::getUsers/$1');
$routes->get('pj-assignment/get-pj/(:num)', 'PjAssignmentController::getPj/$1');

$routes->get('activity/history', 'Activity\AdminActivity::history'); //halaman history tracking activity di superadmin

$routes->get('guest-visitor', 'GuestVisitorController::index');
$routes->get('guest-visitor/create', 'GuestVisitorController::create');
$routes->post('guest-visitor/store', 'GuestVisitorController::store');
$routes->get('guest-visitor/history', 'GuestVisitorController::history');

// halaman category permission untuk user yang dicontrol oleh superadmin
$routes->group('admin/category-permissions', function ($routes) {
    $routes->get('/', 'CategoryPermissionController::index');
    $routes->get('create', 'CategoryPermissionController::create');
    $routes->post('store', 'CategoryPermissionController::store');
    $routes->get('edit/(:num)', 'CategoryPermissionController::edit/$1');
    $routes->post('update/(:num)', 'CategoryPermissionController::update/$1');
    $routes->delete('delete/(:num)', 'CategoryPermissionController::delete/$1');
});

//halaman article
$routes->get('article', 'ArticleController::index');
$routes->get('article/view/(:num)', 'ArticleController::view/$1');
$routes->get('/', 'ArticleController::userIndex');
$routes->get('article', 'ArticleController::userIndex');
$routes->get('article/create', 'ArticleController::create');
$routes->get('article/edit/(:num)', 'ArticleController::edit/$1');
$routes->post('article/delete/(:num)', 'ArticleController::delete/$1');
$routes->post('article/uploadImage', 'Article::uploadImage');

//percobaan menjalankan api (latihan)
$routes->group('api/categories', ['namespace' => 'App\Controllers\API'], function ($routes) {
    $routes->get('', 'CategoryApiController::index');
    $routes->get('(:num)', 'CategoryApiController::show/$1');
    $routes->post('', 'CategoryApiController::store');
    $routes->put('(:num)', 'CategoryApiController::update/$1');
    $routes->delete('(:num)', 'CategoryApiController::delete/$1');
});
