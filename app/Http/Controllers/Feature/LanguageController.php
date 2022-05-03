<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\LanguageRequest;
use App\Services\Feature\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LanguageController extends Controller
{
    private $languageService;

    public function __construct(
        LanguageService $languageService
    )
    {
        $this->languageService = $languageService;
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['languages'] = $this->languageService->getLanguageList($filter, true);
        $data['no'] = $data['languages']->firstItem();
        $data['languages']->withPath(url()->current().$param);

        return view('backend.features.language.index', compact('data'), [
            'title' => __('feature/language.title'),
            'breadcrumbs' => [
                __('feature/language.caption') => '',
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
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['languages'] = $this->languageService->getLanguageList($filter, true, 10, true, [], [
            'deleted_at' => 'ASC'
        ]);
        $data['no'] = $data['languages']->firstItem();
        $data['languages']->withPath(url()->current().$param);

        return view('backend.features.language.trash', compact('data'), [
            'title' => __('feature/language.title').' - '.__('global.trash'),
            'routeBack' => route('language.index'),
            'breadcrumbs' => [
                __('feature/language.caption') => route('language.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create()
    {
        return view('backend.features.language.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('feature/language.caption')
            ]),
            'routeBack' => route('language.index'),
            'breadcrumbs' => [
                __('feature/language.caption') => route('language.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(LanguageRequest $request)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $language = $this->languageService->store($data);

        if ($language['success'] == true) {
            return $this->redirectForm($data)->with('success', $language['message']);
        }

        return redirect()->back()->with('failed', $language['message']);
    }

    public function edit($id)
    {
        $data['language'] = $this->languageService->getLanguage(['id' => $id]);

        return view('backend.features.language.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('feature/language.caption')
            ]),
            'routeBack' => route('language.index'),
            'breadcrumbs' => [
                __('feature/language.caption') => route('language.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(LanguageRequest $request, $id)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $language = $this->languageService->update($data, ['id' => $id]);

        if ($language['success'] == true) {
            return $this->redirectForm($data)->with('success', $language['message']);
        }

        return redirect()->back()->with('failed', $language['message']);
    }

    public function activate($id)
    {
        $language = $this->languageService->activate(['id' => $id]);

        if ($language['success'] == true) {
            return back()->with('success', $language['message']);
        }

        return redirect()->back()->with('failed', $language['message']);
    }

    public function softDelete($id)
    {
        $language = $this->languageService->trash(['id' => $id]);

        return $language;
    }

    public function permanentDelete(Request $request, $id)
    {
        $language = $this->languageService->delete($request, ['id' => $id]);

        return $language;
    }

    public function restore($id)
    {
        $language = $this->languageService->restore(['id' => $id]);

        if ($language['success'] == true) {
            return redirect()->back()->with('success', $language['message']);
        }

        return redirect()->back()->with('failed', $language['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('language.index');
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
