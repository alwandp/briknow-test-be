<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
#LOGINS
    #LOGIN-ADDIN
    Route::post('login','Auth\LoginController@login_addin');
    #LOGIN-WEB
    Route::post('login_web','Auth\LoginController@login_web');
    Route::post('proses_bri_stars','Auth\LoginController@proses_api');
#upload Image Attachment
    Route::post('upimgcontent', 'KontribusiController@upimgcontent');
    Route::get('/notifproject','NotifikasiController@indexproject');

    Route::middleware('auth:sanctum')->group(function(){
    #ADDIN-MS-WORD
        Route::post('/cek_auth','Auth\CekauthController');
        Route::post('/logout','Auth\LogoutController'); //web juga pakai ini
        Route::post('/searchproject','DownloadController@searchproject');
        Route::post('/search/','PencarianController@cariall');
        Route::post('/managedocument','DocumentController@index');

    // #WEB
        //#ADMIN
            #Dashboard
                Route::get('/dashboard/project','DashboardController@getProjectList');
                Route::get('/dashboard/visitor','DashboardController@getVisitor');
                Route::get('/dashboard/getalldatagrafik','DashboardController@getalldatagrafik');
                Route::get('/dashboard/getprojectvisitor','DashboardController@getProjectVisitor');
                Route::get('/dashboard/getprojectconsultant','DashboardController@getProjectKonsultan');
                Route::get('/dashboard/getprojectdivisi','DashboardController@getProjectDivisi');
                Route::get('/dashboard/getprojecttahun','DashboardController@getProjectTahun');
                Route::get('/dashboard/getprojectpertahun','DashboardController@getProjectPerTahun');
                Route::get('/dashboard/getprojecttags','DashboardController@getProjectTags');
                Route::get('/dashboard/initiative','DashboardController@dashboardInitiative');
                Route::get('/dashboard/strategic','DashboardController@dashboardStrategic');
                Route::get('/dashboard/implementation','DashboardController@dashboardImplementation');
            #Management User
                Route::get('/manageuser','ManageUserController@index');
                Route::get('/manageuser/{search}','ManageUserController@listuser');
                Route::get('/manageuser/detail/{id}','ManageUserController@detail');
                Route::post('/manageuser/edit/{id}','ManageUserController@edit');
                Route::delete('/manageuser/destroy/{id}','ManageUserController@hapus');
                Route::post('/manageuser/searchpn','ManageUserController@searchpn'); // Free Search user
                Route::post('/manageuser/searchma','ManageUserController@searchma'); // peruntukan mencari user selain admin yang udah existing
                Route::post('/manageuser/searchkcs','ManageUserController@searchkcs'); // peruntukan mencari user checker signer
                Route::post('/manageuser/admin/create','ManageUserController@admin_create');

            #Management Uker
                Route::get('/manageuker','ManageUkerController@index');
                Route::get('/manageuker/{sort}/{sort2}/{search}','ManageUkerController@index_content');
                Route::post('/manageuker/create','ManageUkerController@create');
                Route::get('/manageuker/detail/{id}','ManageUkerController@detail');
                Route::post('/manageuker/edit/{id}','ManageUkerController@update');
                Route::delete('/manageuker/delete/{id}','ManageUkerController@hapus');

            #Management Consultatn
                Route::get('/manageconsultant','ManageConsultantController@index');
                Route::get('/manageconsultant/{sort}/{sort2}/{search}','ManageConsultantController@index_content');
                Route::post('/manageconsultant/create','ManageConsultantController@create');
                Route::get('/manageconsultant/detail/{id}','ManageConsultantController@detail');
                Route::post('/manageconsultant/edit/{id}','ManageConsultantController@update');
                Route::delete('/manageconsultant/delete/{id}','ManageConsultantController@hapus');

            #Management Project
                Route::get('/manageproject','ManageProjectController@index');
                Route::get('/manageproject/review','ManageProjectController@review');
                Route::get('/manageproject/rekomendasi','ManageProjectController@rekomendasi');
                // Route::get('/manageproject/rekomendasi/all','ManageProjectController@all');
                Route::post('/manageproject/review/passing/{tipe}','ManageProjectController@passing');
                Route::post('/manageproject/rekomendasi/add/{id}','ManageProjectController@add');
                Route::post('/manageproject/rekomendasi/remove/{id}','ManageProjectController@hapus_rekomendasi');
                Route::post('/manageproject/review/publish/{id}','ManageProjectController@publish');
                Route::post('/manageproject/review/unpublish/{id}','ManageProjectController@unpublish');
                Route::delete('/manageproject/review/destroy/{id}','ManageProjectController@hapus');
                Route::get('/manageproject/review/{fil_div}/{fil_kon}/{search}','ManageProjectController@review_content');
                Route::get('/manageproject/sort/{by}','ManageProjectController@sort');
                Route::get('/manageproject/sortpass/{by}','ManageProjectController@sortpass'); //untested

            #Manage Lesson Learned
                Route::get('/managelessonlearned', 'LessonLearnedController@getAll');

            #Manage Communication Support
                Route::get('/communicationinitiative/{type}', 'ManageComSupport@getAllComInitiative');
                Route::post('/communicationinitiative/status/{status}/{id}', 'ManageComSupport@setStatusComInit');
                Route::delete('/communicationinitiative/delete/{id}', 'ManageComSupport@deleteComInit');
                Route::get('/strategicinitiative', 'ManageComSupport@getAllStrategic');
                Route::get('/strategicinitiative/project/{slug}', 'ManageComSupport@getStrategicByProject');
                Route::get('/strategicinitiative/project/{slug}/{type}', 'ManageComSupport@getStrategicByProjectAndType');
                Route::get('/implementation/{step}', 'ManageComSupport@getAllImplementation');
                Route::post('/implementation/status/{status}/{id}', 'ManageComSupport@setStatusImplementation');
                Route::delete('/implementation/delete/{id}', 'ManageComSupport@deleteImplementation');

                Route::get('/form_upload/content/{slug}', 'ManageComSupport@form_content');
                Route::get('/form_upload/implementation/{slug}', 'ManageComSupport@form_implementation');
                Route::post('/managecommunication/content/upload/{id}', 'ManageComSupport@createContent');
                Route::post('/managecommunication/implementation/upload/{id}', 'ManageComSupport@createImplementation');
                Route::post('/communication/views/{table}/{id}', 'CommunicationSupportController@viewContent');
                Route::get('/download/attach/{content}/{id}', 'CommunicationSupportController@getFile');
                Route::get('/download/file/project/{id}', 'CommunicationSupportController@getFileProject');

            #Public Communication Support
                Route::get('/communicationinitiative/publish/{type}', 'ManageComSupport@getPublishComInnitiave');
                Route::get('/get/communicationinitiative/publish/{type}', 'CommunicationSupportController@getCommunicationInitiative');
                Route::get('/get/strategic/publish', 'CommunicationSupportController@getStrategic');
                Route::get('/get/strategic/publish/{slug}', 'CommunicationSupportController@getStrategicByProject');
                Route::get('/get/strategic/publish/{slug}/{type}', 'CommunicationSupportController@getStrategicByProjectAndType');
                Route::get('/get/implementation/all/publish/{step}', 'CommunicationSupportController@getAllImplementation');
                Route::get('/get/implementation/publish/{slug}', 'CommunicationSupportController@getOneImplementation');
        #---

        #ALL USER
            #editprofile
            Route::post('/editprofiles','HomeController@editprof');
            #notificaiton
            Route::get('/notif','NotifikasiController@index');
            Route::post('/notif','NotifikasiController@update');
            #gammification congratulation
            // Route::get('/congrats','HomeController@congrats');  //udah g di pake di gabung ke /notif [GET]
            Route::post('/congrats','HomeController@congrats_update');
            #Home
                Route::get('/beranda','HomeController@index');
                Route::get('/suggestionberanda/{key}', 'HomeController@suggestionberanda');
                Route::get('/topfivevendor', 'HomeController@topfive_vendor');
                Route::get('/topfiveproject', 'HomeController@topfive_project');
                Route::get('/profile', 'HomeController@profile');
                Route::get('/profile/{pn}', 'HomeController@profileuser');
                Route::get('/gamification', 'HomeController@gamification');
                Route::post('/changeavatar' ,'HomeController@changeavatar');
                Route::get('/cntlessonbytahap', 'HomeController@countLessonByTahap');
                Route::get('/cntStrategicInitiative', 'HomeController@countInitiative');
                Route::get('/cntComInitiative', 'HomeController@countComInitiative');
                Route::get('/cntImplementation', 'HomeController@countImplementation');
            #laporan
            Route::get('/proyektop5', 'LaporanController@proyektopfive');
            Route::get('/vendortop5', 'LaporanController@vendortopfive');
            Route::get('/getProjectVisitor', 'LaporanController@getProjectVisitor');
            Route::get('/getProjectConsultant', 'LaporanController@getProjectKonsultan');
            Route::get('/getprojectdivisi','LaporanController@getProjectDivisi');
            Route::get('/strategictopfive','LaporanController@strainittopfive');
            Route::get('/allPerformance','LaporanController@allPerformance');
            Route::get('/allData','LaporanController@allData');
            Route::get('/allComSupport','LaporanController@allComSupport');

            #Konsultan
                Route::get('/consultant/{id}','ConsultantController@index');

            #Project
                Route::post('/archieve','ProjectController@archieve');
                Route::get('/project/{slug}','ProjectController@index');
                Route::get('/projectbyid/{id}','ProjectController@projectById');
                Route::get('/projectpreview/{slug}','ProjectController@preview');
                Route::get('/myproject','MyProjectController@index');
                Route::post('/detailproject/{id}','DownloadController@detailproject');

            #My Lesson Learned
                Route::get('/mylessonlearned', 'MyLessonLearnedController@index');
                Route::get('/mylessonlearned/all', 'MyLessonLearnedController@getLessonLearnedPublic');
            #Favorit
                Route::get('/myfavorite','MyFavoriteController@getFavs');
                Route::post('/bookmark_project/{id}','FavoriteProjectController@save');
                Route::post('/bookmark_consultant/{id}','FavoriteConsultantController@save');
                Route::post('/bookmark_comsupport/{table}/{id}','FavoriteComSupportController@save');
                Route::get('/favorite_proj/{sort}','MyFavoriteController@fav_proj');
                Route::get('/favorite_cons/{sort}','MyFavoriteController@fav_cons');
                Route::get('/favorite_com/{sort}','MyFavoriteController@fav_com');
            #Comment

                Route::post('/comment/create','CommentController@create');
                Route::post('/commentforum/create','CommentController@createforum');

            #Divisi
                Route::get('/divisi/all', 'DivisiController@getALlDivisi');
                Route::get('/divisi/{id}','DivisiController@getDivisi');
                Route::get('/divisibydirektorat/{sample}','DivisiController@getDivisiByDirektorat');
                Route::get('/divisi/direktorat/{dir}', 'DivisiController@getDir');

            #Pencarian
                Route::post('/search/{katakunci}','PencarianController@cari');
                Route::post('/detail/{id}','PencarianController@detail');
                Route::get('/proj_divisi/{kunci}/{search}','PencarianController@proj_divisi');
                Route::get('/document_proj/{kunci}/{search}/{sort}','PencarianController@doc_project');
                Route::get('/project_consultant/{kunci}/{search}/{sort}','PencarianController@proj_consultant');
                Route::get('/list/doc/{project}/{id}','DocumentController@listDoc');

            #Katalog
                Route::get('/kat','ProjectController@all');
                Route::get('/bahan_katalog','ProjectController@bahan_filter');
                Route::post('/fit','ProjectController@filterisasi');

            #kontribusi
                # create project
                Route::get('/prepare_form', 'KontribusiController@prepare_form');

                #edit project
                Route::get('/prepare_form/{slug}', 'KontribusiController@prepare_form_edit');

                #upload file
                    Route::post('/up/{kategori}','KontribusiController@uploadphoto');
                    Route::delete('/up/{kategori}','KontribusiController@delete');
                    Route::post('/deleteonleave','KontribusiController@deleteOnLeave');
                    Route::post('/kontribusi/create','KontribusiController@create');
                    Route::post('/kontribusi/update/{id}','KontribusiController@update');
                #bahan
                    Route::get('/getconsultant','ConsultantController@getConsultant');
                    Route::get('/getuser','KontribusiController@getUser');
                #approval alert
                    Route::post('/kontribusi/appr','KontribusiController@approval');
                    Route::post('/kontribusi/hapus','KontribusiController@hapus');
                    Route::post('/kontribusi/send','KontribusiController@send');
                    Route::post('/kontribusi/reject','KontribusiController@reject');

            #Forum
                #prepare form forum
                Route::get('/forum/prepare_form', 'ForumController@prepare_form');

                #list forum
                Route::get('/forum/{search}','ForumController@listforum');

                Route::get('/manageforum/removed/{search}','DashboardController@listremoved');
                Route::get('/manageforum/removed/sort/public/{search}','DashboardController@sortPublicRemoved');
                Route::get('/manageforum/removed/sort/private/{search}','DashboardController@sortPrivateRemoved');

                Route::get('/manageforum/all/{search}','DashboardController@listall');
                Route::get('/manageforum/all/sort/public/{search}','DashboardController@sortPublic');
                Route::get('/manageforum/all/sort/private/{search}','DashboardController@sortPrivate');

                Route::get('/manageforum/all','DashboardController@manageforum_all');
                Route::get('/manageforum/removed','DashboardController@manageforum_removed');
                Route::get('/manageforum/save','DashboardController@adminforumsave');
                Route::post('/manageforum/all/remove/{id}','DashboardController@remove');
                Route::post('/manageforum/removed/restore/{id}','DashboardController@restore');

                #Create forum
                Route::get('/forum', 'ForumController@index');
                Route::post('/forum/create', 'ForumController@create');
                Route::post('/forum/draft/save', 'ForumController@saveDraft');
                Route::get('/forum/draft/{id}', 'ForumController@draft');
                Route::delete('/forum/destroy/{id}', 'ForumController@hapus');
                Route::get('/forum/detail/{slug}', 'ForumController@detail');
                Route::post('/forum/comment/create','ForumController@comment');

            #GAMIFICATION
                Route::get('/managegamification','DashboardController@managegamification');
                Route::post('/managegamification/save/level','DashboardController@gamificationsave_level');
                Route::post('/managegamification/save/activity','DashboardController@gamificationsave_activity');
                Route::post('/managegamification/save/achievement','DashboardController@gamificationsave_achievement');
        #---
    #---
});
