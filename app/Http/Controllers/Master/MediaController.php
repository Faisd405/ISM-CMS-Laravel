<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\MediaRequest;
use App\Models\Module\Content\ContentPost;
use App\Services\Feature\LanguageService;
use App\Services\Master\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class MediaController extends Controller
{
    private $mediaService, $languageService;

    public function __construct(
        MediaService $mediaService,
        LanguageService $languageService
    )
    {
        $this->mediaService = $mediaService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $moduleId, $moduleType)
    {
        $data['params'] = ['moduleId' => $moduleId, 'moduleType' => $moduleType];
        
        $filter['mediable_id'] = $moduleId;
        $filter['module'] = $moduleType;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['medias'] = $this->mediaService->getMediaList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['medias']->firstItem();
        $data['medias']->withQueryString();

        return view('backend.masters.media.index', compact('data'), [
            'title' => __('master/media.title'),
            'routeBack' => $this->routeBack($moduleId, $moduleType),
            'breadcrumbs' => [
                Str::ucfirst($moduleType) => $this->routeBack($moduleId, $moduleType),
                __('master/media.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $moduleId, $moduleType)
    {
        $data['params'] = ['moduleId' => $moduleId, 'moduleType' => $moduleType];
        
        $filter['module'] = $moduleType;
        $filter['mediable_id'] = $moduleId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['medias'] = $this->mediaService->getMediaList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['medias']->firstItem();
        $data['medias']->withQueryString();

        return view('backend.masters.media.trash', compact('data'), [
            'title' => __('master/media.title').' - '.__('global.trash'),
            'routeBack' => route('media.index', $data['params']),
            'breadcrumbs' => [
                __('master/media.caption') => route('media.index', $data['params']),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $moduleId, $moduleType)
    {
        $data['params'] = ['moduleId' => $moduleId, 'moduleType' => $moduleType];
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.masters.media.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('master/media.caption')
            ]),
            'routeBack' => route('media.index', $data['params']),
            'breadcrumbs' => [
                __('master/media.caption') => route('media.index', $data['params']),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(MediaRequest $request, $moduleId, $moduleType)
    {
        $data = $request->all();
        $data['module_id'] = $moduleId;
        $data['module_type'] = $moduleType;
        $data['is_youtube'] = (bool)$request->is_youtube;
        $media = $this->mediaService->store($data);

        if ($media['success'] == true) {
            return $this->redirectForm($data)->with('success', $media['message']);
        }

        return redirect()->back()->with('failed', $media['message']);
    }

    public function edit(Request $request, $moduleId, $moduleType, $id)
    {
        $data['params'] = ['moduleId' => $moduleId, 'moduleType' => $moduleType];
        $data['param_id'] = ['moduleId' => $moduleId, 'moduleType' => $moduleType, 'id' => $id];
        $data['media'] = $this->mediaService->getMedia(['id' => $id]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        if (empty($data['media']))
            return abort(404);

        return view('backend.masters.media.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('master/media.caption')
            ]),
            'routeBack' => route('media.index', $data['params']),
            'breadcrumbs' => [
                __('master/media.caption') => route('media.index', $data['params']),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(MediaRequest $request, $moduleId, $moduleType, $id)
    {
        $data = $request->all();
        $data['module_id'] = $moduleId;
        $data['module_type'] = $moduleType;
        $data['is_youtube'] = (bool)$request->is_youtube;
        $media = $this->mediaService->update($data, ['id' => $id]);

        if ($media['success'] == true) {
            return $this->redirectForm($data)->with('success', $media['message']);
        }

        return redirect()->back()->with('failed', $media['message']);
    }

    public function position(Request $request, $moduleId, $moduleType, $id, $position)
    {
        $media = $this->mediaService->position(['id' => $id], $position);

        if ($media['success'] == true) {
            return back()->with('success', $media['message']);
        }

        return redirect()->back()->with('failed', $media['message']);
    }

    public function softDelete($moduleId, $moduleType,$id)
    {
        $media = $this->mediaService->trash(['id' => $id]);

        return $media;
    }

    public function permanentDelete(Request $request, $moduleId, $moduleType, $id)
    {
        $media = $this->mediaService->delete($request, ['id' => $id]);

        return $media;
    }

    public function restore($moduleId, $moduleType, $id)
    {
        $media = $this->mediaService->restore(['id' => $id]);

        if ($media['success'] == true) {
            return redirect()->back()->with('success', $media['message']);
        }

        return redirect()->back()->with('failed', $media['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('media.index', ['moduleId' => $data['module_id'], 'moduleType' => $data['module_type']]);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    private function routeBack($moduleId, $moduleType)
    {
        if ($moduleType == 'page') {
            $routeBack = route('page.index');
        }

        if ($moduleType == 'content_post') {
            $post = ContentPost::find($moduleId);
            $routeBack = route('content.post.index', ['sectionId' => $post['section_id']]);
        }

        return $routeBack;
    }
}
