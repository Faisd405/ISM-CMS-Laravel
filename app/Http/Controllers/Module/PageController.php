<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\PageRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Master\MediaService;
use App\Services\Master\TemplateService;
use App\Services\Module\PageService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use ApiResponser;

    private $pageService, $mediaService, $languageService, $templateService, $configService;

    public function __construct(
        PageService $pageService,
        MediaService $mediaService,
        LanguageService $languageService,
        TemplateService $templateService,
        ConfigurationService $configService
    )
    {
        $this->pageService = $pageService;
        $this->mediaService = $mediaService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;
        $this->configService = $configService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['parent'] = 0;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['pages'] = $this->pageService->getPageList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['pages']->firstItem();
        $data['pages']->withPath(url()->current().$param);

        return view('backend.pages.index', compact('data'), [
            'title' => __('module/page.title'),
            'breadcrumbs' => [
                __('module/page.caption') => '',
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

        $data['pages'] = $this->pageService->getPageList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['pages']->firstItem();
        $data['pages']->withPath(url()->current().$param);

        return view('backend.pages.trash', compact('data'), [
            'title' => __('module/page.title').' - '.__('global.trash'),
            'routeBack' => route('page.index'),
            'breadcrumbs' => [
                __('module/page.caption') => route('page.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'page'], false);

        if ($request->input('parent', '') != '') {
            $data['parent'] = $this->pageService->getPage(['id' => $request->input('parent')]);
        }

        return view('backend.pages.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/page.caption')
            ]),
            'routeBack' => route('page.index'),
            'breadcrumbs' => [
                __('module/page.caption') => route('page.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(PageRequest $request)
    {
        $data = $request->all();
        $data['parent'] = $request->parent;
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_intro'] = (bool)$request->hide_intro;
        $data['hide_tags'] = (bool)$request->hide_tags;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $page = $this->pageService->store($data);

        if ($page['success'] == true) {
            return $this->redirectForm($data)->with('success', $page['message']);
        }

        return redirect()->back()->with('failed', $page['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['page'] = $this->pageService->getPage(['id' => $id]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'page'], false);
        
        if ($data['page']->tags()->count() > 0) {
            foreach ($data['page']->tags as $key => $value) {
                $tags[$key] = $value->tag->name;
            }
    
            $data['tags'] = implode(',', $tags);
        }

        return view('backend.pages.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/page.caption')
            ]),
            'routeBack' => route('page.index'),
            'breadcrumbs' => [
                __('module/page.caption') => route('page.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(PageRequest $request, $id)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_intro'] = (bool)$request->hide_intro;
        $data['hide_tags'] = (bool)$request->hide_tags;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $page = $this->pageService->update($data, ['id' => $id]);

        if ($page['success'] == true) {
            return $this->redirectForm($data)->with('success', $page['message']);
        }

        return redirect()->back()->with('failed', $page['message']);
    }

    public function publish($id)
    {
        $page = $this->pageService->status('publish', ['id' => $id]);

        if ($page['success'] == true) {
            return back()->with('success', $page['message']);
        }

        return redirect()->back()->with('failed', $page['message']);
    }

    public function approved($id)
    {
        $page = $this->pageService->status('approved', ['id' => $id]);

        if ($page['success'] == true) {
            return back()->with('success', $page['message']);
        }

        return redirect()->back()->with('failed', $page['message']);
    }

    public function position(Request $request, $id, $position)
    {
        $page = $this->pageService->position(['id' => $id], $position, $request->parent);

        if ($page['success'] == true) {
            return back()->with('success', $page['message']);
        }

        return redirect()->back()->with('failed', $page['message']);
    }

    public function softDelete($id)
    {
        $page = $this->pageService->trash(['id' => $id]);

        return $page;
    }

    public function permanentDelete(Request $request, $id)
    {
        $page = $this->pageService->delete($request, ['id' => $id]);

        return $page;
    }

    public function restore($id)
    {
        $page = $this->pageService->restore(['id' => $id]);

        if ($page['success'] == true) {
            return redirect()->back()->with('success', $page['message']);
        }

        return redirect()->back()->with('failed', $page['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('page.index');
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
        if (config('cms.module.page.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = $this->configService->getConfigFile('banner_default');
        $limit = $this->configService->getConfigValue('content_limit');
        $data['pages'] = $this->pageService->getPageList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);

        return view('frontend.pages.list', compact('data'), [
            'title' => __('module/page.title'),
            'breadcrumbs' => [
                __('module/page.title') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slug');

        $data['read'] = $this->pageService->getPage(['slug' => $slug]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 || $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        if ($data['read']['config']['is_detail'] == 0) {
            
            return redirect()->route('home');
            if ($data['read']['parent'] > 0) {
                return redirect()->route('page.read.'.$data['read']->getParent()['slug']);
            }
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        $this->pageService->recordHits(['id' => $data['read']['id']]);

        //data
        $data['childs'] = $this->pageService->getPageList([
            'parent' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1,
        ], false, 0, false, [], [
            'position' => 'ASC'
        ]);
        
        $data['medias'] = $this->mediaService->getMediaList([
            'module' => 'page',
            'mediable_id' => $data['read']['id']
        ], false, 0, false, [], [
            'position' => 'ASC'
        ]);

        $data['fields'] = $data['read']['custom_fields'];
        $data['tags'] = $data['read']->tags();

        $data['creator'] = $data['read']['createBy']['name'];
        $data['cover'] = $data['read']->coverSrc();
        $data['banner'] = $data['read']->bannerSrc();

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('title');
        if (!empty($data['read']['seo']['title'])) {
            $data['meta_title'] = Str::limit(strip_tags($data['read']['seo']['title']), 69);
        }

        $data['meta_description'] = $this->configService->getConfigValue('meta_description');
        if (!empty($data['read']['seo']['description'])) {
            $data['meta_description'] = $data['read']['seo']['description'];
        } elseif (empty($data['read']['seo']['description']) && 
            !empty($data['read']->fieldLang('intro'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('intro')), 155);
        } elseif (empty($data['read']['seo']['description']) && 
            empty($data['read']->fieldLang('intro')) && !empty($data['read']->fieldLang('content'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('content')), 155);
        }

        $data['meta_keywords'] = $this->configService->getConfigValue('meta_keywords');
        if (!empty($data['read']['seo']['keywords'])) {
            $data['meta_keywords'] = $data['read']['seo']['keywords'];
        }

        //share
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".
            URL::full()."&title=".$data['read']->fieldLang('title')."";
        $data['share_twitter'] = "https://twitter.com/intent/tweet?text=".
            $data['read']->fieldLang('title')."&amp;url=".URL::full()."";
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('title')." 
            ".URL::full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            URL::full()."&title=".$data['read']->fieldLang('title')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $data['read']['cover']['filepath']."&url=".URL::full()."&is_video=false&description=".
            $data['read']->fieldLang('title')."";

        $blade = 'detail';
        if (!empty($data['read']['template_id'])) {
            $blade = 'custom.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        }

        return view('frontend.pages.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('title'),
            'breadcrumbs' => [
                $data['read']->fieldLang('title') => ''
            ],
        ]);
    }
}
