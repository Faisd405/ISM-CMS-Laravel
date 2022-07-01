<?php

namespace App\Http\Controllers\Module\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Gallery\GalleryAlbumRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Master\TemplateService;
use App\Services\Module\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GalleryAlbumController extends Controller
{
    private $galleryService, $languageService, $templateService, $configService;

    public function __construct(
        GalleryService $galleryService,
        LanguageService $languageService,
        TemplateService $templateService,
        ConfigurationService $configService
    )
    {
        $this->galleryService = $galleryService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;
        $this->configService = $configService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('category_id', '') != '') {
            $filter['gallery_category_id'] = $request->input('category_id');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['albums'] = $this->galleryService->getAlbumList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['albums']->firstItem();
        $data['albums']->withPath(url()->current().$param);
        $data['categories'] = $this->galleryService->getCategoryList([], false, 0);

        return view('backend.galleries.album.index', compact('data'), [
            'title' => __('module/gallery.album.title'),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.album.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('category_id', '') != '') {
            $filter['gallery_category_id'] = $request->input('category_id');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['albums'] = $this->galleryService->getAlbumList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['albums']->firstItem();
        $data['albums']->withPath(url()->current().$param);
        $data['categories'] = $this->galleryService->getCategoryList([], false, 0);

        return view('backend.galleries.album.trash', compact('data'), [
            'title' => __('module/gallery.album.title').' - '.__('global.trash'),
            'routeBack' => route('gallery.album.index'),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.album.caption') => route('gallery.album.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'gallery_album'], false, 0);
        $data['categories'] = $this->galleryService->getCategoryList([], false, 0);

        return view('backend.galleries.album.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/gallery.album.caption')
            ]),
            'routeBack' => route('gallery.album.index', $request->query()),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.album.caption') => route('gallery.album.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(GalleryAlbumRequest $request)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $album = $this->galleryService->storeAlbum($data);
        $data['query'] = $request->query();

        if ($album['success'] == true) {
            return $this->redirectForm($data)->with('success', $album['message']);
        }

        return redirect()->back()->with('failed', $album['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['album'] = $this->galleryService->getAlbum(['id' => $id]);
        if (empty($data['album']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'gallery_album'], false, 0);
        $data['categories'] = $this->galleryService->getCategoryList([], false, 0);

        return view('backend.galleries.album.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/gallery.album.caption')
            ]),
            'routeBack' => route('gallery.album.index', $request->query()),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.album.caption') => route('gallery.album.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(GalleryAlbumRequest $request, $id)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $album = $this->galleryService->updateAlbum($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($album['success'] == true) {
            return $this->redirectForm($data)->with('success', $album['message']);
        }

        return redirect()->back()->with('failed', $album['message']);
    }

    public function publish($id)
    {
        $album = $this->galleryService->statusAlbum('publish', ['id' => $id]);

        if ($album['success'] == true) {
            return back()->with('success', $album['message']);
        }

        return redirect()->back()->with('failed', $album['message']);
    }

    public function approved($id)
    {
        $album = $this->galleryService->statusAlbum('approved', ['id' => $id]);

        if ($album['success'] == true) {
            return back()->with('success', $album['message']);
        }

        return redirect()->back()->with('failed', $album['message']);
    }

    public function position(Request $request, $id, $position)
    {
        $album = $this->galleryService->positionAlbum(['id' => $id], $position);

        if ($album['success'] == true) {
            return back()->with('success', $album['message']);
        }

        return redirect()->back()->with('failed', $album['message']);
    }

    public function softDelete($id)
    {
        $album = $this->galleryService->trashAlbum(['id' => $id]);

        return $album;
    }

    public function permanentDelete(Request $request, $id)
    {
        $album = $this->galleryService->deleteAlbum($request, ['id' => $id]);

        return $album;
    }

    public function restore($id)
    {
        $album = $this->galleryService->restoreAlbum(['id' => $id]);

        if ($album['success'] == true) {
            return redirect()->back()->with('success', $album['message']);
        }

        return redirect()->back()->with('failed', $album['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('gallery.album.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    /**
     * frontend
     */
    public function read(Request $request)
    {
        $slug = $request->route('slugAlbum');

        $data['read'] = $this->galleryService->getAlbum(['slug' => $slug]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 || $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        if ($data['read']['config']['is_detail'] == 0) {
            return redirect()->route('home');
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        $this->galleryService->recordAlbumHits(['id' => $data['read']['id']]);

        //limit
        $filePerpage = $this->configService->getConfigValue('content_limit');
        if ($data['read']['file_perpage'] > 0) {
            $filePerpage = $data['read']['file_perpage'];
        }

        //data
        $data['files'] = $this->galleryService->getFileList([
            'gallery_album_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1
        ], true, $filePerpage, false, [], [
            'position' => 'ASC'
        ]);

        $data['fields'] = $data['read']['custom_fields'];

        $data['creator'] = $data['read']['createBy']['name'];
        $data['image_preview'] = $data['read']->imgPreview();
        $data['banner'] = $data['read']->bannerSrc();

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');

        $data['meta_description'] = $this->configService->getConfigValue('meta_description');
        if (!empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        //share
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".url()->full().
            "&title=".$data['read']->fieldLang('name')."";
        $data['share_twitter'] = 'https://twitter.com/intent/tweet?text='.
            str_replace('#', '', $data['read']->fieldLang('name')).'&url='.url()->full();
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('name').
            " ".url()->full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            url()->full()."&title=".$data['read']->fieldLang('name')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $data['image_preview']."&url=".url()->full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

        $blade = 'album.detail';
        if (!empty($data['read']['template_id'])) {
            $blade = 'album.custom.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        } elseif (!empty($data['read']['gallery_category_id']) && !empty($data['read']['category']['template_detail_id'])) {
            $blade = 'category.detail.'.Str::replace('.blade.php', '', $data['read']['category']['templateDetail']['filename']);
        }

        return view('frontend.galleries.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
