<?php

namespace App\Http\Controllers\Module\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Gallery\GalleryCategoryRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Master\TemplateService;
use App\Services\Module\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
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
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['categories'] = $this->galleryService->getCategoryList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.galleries.category.index', compact('data'), [
            'title' => __('module/gallery.category.title'),
            'routeBack' => route('gallery.album.index'),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.category.caption') => '',
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
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['categories'] = $this->galleryService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.galleries.category.trash', compact('data'), [
            'title' => __('module/gallery.category.title').' - '.__('global.trash'),
            'routeBack' => route('gallery.category.index'),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.category.caption') => route('gallery.category.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['template_lists'] = $this->templateService->getTemplateList(['type' => 1, 'module' => 'gallery_category'], false);
        $data['template_details'] = $this->templateService->getTemplateList(['type' => 2, 'module' => 'gallery_category'], false);

        return view('backend.galleries.category.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/gallery.category.caption')
            ]),
            'routeBack' => route('gallery.category.index', $request->query()),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.category.caption') => route('gallery.category.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(GalleryCategoryRequest $request)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $category = $this->galleryService->storeCategory($data);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['category'] = $this->galleryService->getCategory(['id' => $id]);
        if (empty($data['category']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['template_lists'] = $this->templateService->getTemplateList(['type' => 1, 'module' => 'gallery_category'], false);
        $data['template_details'] = $this->templateService->getTemplateList(['type' => 2, 'module' => 'gallery_category'], false);

        return view('backend.galleries.category.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/gallery.category.caption')
            ]),
            'routeBack' => route('gallery.category.index', $request->query()),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.category.caption') => route('gallery.category.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(GalleryCategoryRequest $request, $id)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $category = $this->galleryService->updateCategory($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function publish($id)
    {
        $category = $this->galleryService->statusCategory('publish', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function approved($id)
    {
        $category = $this->galleryService->statusCategory('approved', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function position(Request $request, $id, $position)
    {
        $category = $this->galleryService->positionCategory(['id' => $id], $position);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function softDelete($id)
    {
        $category = $this->galleryService->trashCategory(['id' => $id]);

        return $category;
    }

    public function permanentDelete(Request $request, $id)
    {
        $category = $this->galleryService->deleteCategory($request, ['id' => $id]);

        return $category;
    }

    public function restore($id)
    {
        $category = $this->galleryService->restoreCategory(['id' => $id]);

        if ($category['success'] == true) {
            return redirect()->back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('gallery.category.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    /**
     * frontend
     */
    public function list(Request $request)
    {
        //data
        $data['banner'] = $this->configService->getConfigFile('banner_default');
        $limit = $this->configService->getConfigValue('content_limit');
        $data['categories'] = $this->galleryService->getCategoryList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['albums'] = $this->galleryService->getAlbumList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['files'] = $this->galleryService->getFileList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);

        return view('frontend.galleries.list', compact('data'), [
            'title' => __('module/gallery.caption'),
            'breadcrumbs' => [
                __('module/gallery.caption') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slugCategory');

        $data['read'] = $this->galleryService->getCategory(['slug' => $slug]);

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

        $this->galleryService->recordCategoryHits(['id' => $data['read']['id']]);

        //limit
        $albumPerpage = $this->configService->getConfigValue('content_limit');
        $filePerpage = $this->configService->getConfigValue('content_limit');
        if ($data['read']['album_perpage'] > 0) {
            $albumPerpage = $data['read']['album_perpage'];
        }
        if ($data['read']['file_perpage'] > 0) {
            $filePerpage = $data['read']['file_perpage'];
        }

        //data
        $data['albums'] = $this->galleryService->getAlbumList([
            'gallery_category_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1
        ], true, $albumPerpage, false, [], [
            'position' => 'ASC'
        ]);

        $data['files'] = $this->galleryService->getFileList([
            'gallery_category_id' => $data['read']['id'],
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

        $blade = 'detail';
        if (!empty($data['read']['template_list_id'])) {
            $blade = 'list.'.Str::replace('.blade.php', '', $data['read']['templateList']['filename']);
        }

        return view('frontend.galleries.category.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
