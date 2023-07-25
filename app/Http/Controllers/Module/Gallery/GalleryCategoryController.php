<?php

namespace App\Http\Controllers\Module\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Gallery\GalleryCategoryRequest;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\Master\TemplateRepository;
use App\Repositories\Module\GalleryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
{
    private $galleryService, $languageService, $templateService;

    public function __construct(
        GalleryRepository $galleryService,
        LanguageRepository $languageService,
        TemplateRepository $templateService
    )
    {
        $this->galleryService = $galleryService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request)
    {
        $filter = [];
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['categories'] = $this->galleryService->getCategoryList($filter, true, 10, false, [],
            config('cms.module.gallery.category.ordering'));
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

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
        $filter = [];
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['categories'] = $this->galleryService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

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
        $data['template_lists'] = $this->templateService->getTemplateList(['type' => 1, 'module' => 'gallery_category'], false, 0);
        $data['template_details'] = $this->templateService->getTemplateList(['type' => 2, 'module' => 'gallery_category'], false, 0);

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
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_album'] = (bool)$request->config_paginate_album;
        $data['config_paginate_file'] = (bool)$request->config_paginate_file;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
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
        $data['template_lists'] = $this->templateService->getTemplateList(['type' => 1, 'module' => 'gallery_category'], false, 0);
        $data['template_details'] = $this->templateService->getTemplateList(['type' => 2, 'module' => 'gallery_category'], false, 0);

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
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_album'] = (bool)$request->config_paginate_album;
        $data['config_paginate_file'] = (bool)$request->config_paginate_file;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $category = $this->galleryService->updateCategory($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->galleryService->sortCategory(['id' => $value], $i);
        }
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
        if (config('cms.module.gallery.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = config('cmsConfig.file.banner_default');
        $limit = config('cmsConfig.general.content_limit');

        // category
        $data['categories'] = $this->galleryService->getCategoryList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], config('cms.module.gallery.category.ordering'));
        $data['no_categories'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        // album
        $data['albums'] = $this->galleryService->getAlbumList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], config('cms.module.gallery.album.ordering'));
        $data['no_albums'] = $data['albums']->firstItem();
        $data['albums']->withQueryString();

        // file
        $data['files'] = $this->galleryService->getFileList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['no_files'] = $data['files']->firstItem();
        $data['files']->withQueryString();

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

        if ($data['read']['detail'] == 0) {
            return redirect()->route('home');
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        // filtring
        $keyword = $request->input('keyword', '');
        if ($keyword != '') {
            $filter['q'] = $keyword;
        }

        $filter['gallery_category_id'] = $data['read']['id'];
        $filter['publish'] = 1;
        $filter['approved'] = 1;

        // album
        $data['albums'] = $this->galleryService->getAlbumList($filter,
            $data['read']['config']['paginate_album'], $data['read']['config']['album_limit'], false,
        [], config('cms.module.gallery.album.ordering'));
        if ($data['read']['config']['paginate_album'] == true) {
            $data['no_albums'] = $data['albums']->firstItem();
            $data['albums']->withQueryString();
        }

        // file
        $data['files'] = $this->galleryService->getFileList($filter,
            $data['read']['config']['paginate_file'], $data['read']['config']['file_limit'], false,
        [], ['position' => 'ASC']);
        if ($data['read']['config']['paginate_file'] == true) {
            $data['no_files'] = $data['files']->firstItem();
            $data['files']->withQueryString();
        }

        $data['fields'] = $data['read']['custom_fields'];
        $data['creator'] = $data['read']['createBy']['name'];
        $data['cover'] = $data['read']['cover_src'];
        $data['banner'] = $data['read']['banner_src'];

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');
        $data['meta_description'] = config('cmsConfig.seo.meta_description');
        if (!empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        //share
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".URL::full().
            "&title=".$data['read']->fieldLang('name')."";
        $data['share_twitter'] = 'https://twitter.com/intent/tweet?text='.
            str_replace('#', '', $data['read']->fieldLang('name')).'&url='.URL::full();
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('name').
            " ".URL::full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            URL::full()."&title=".$data['read']->fieldLang('name')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $data['cover']."&url=".URL::full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

        $blade = 'detail';
        if (!empty($data['read']['template_list_id'])) {
            $blade = 'list.'.Str::replace('.blade.php', '', $data['read']['templateList']['filename']);
        }

        // record hits
        $this->galleryService->recordCategoryHits(['id' => $data['read']['id']]);

        return view('frontend.galleries.category.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
