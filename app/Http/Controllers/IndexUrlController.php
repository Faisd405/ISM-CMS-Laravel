<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUrlRequest;
use App\Services\IndexUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IndexUrlController extends Controller
{
    private $indexUrlService;

    public function __construct(
        IndexUrlService $indexUrlService
    )
    {
        $this->indexUrlService = $indexUrlService;
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['urls'] = $this->indexUrlService->getIndexUrlList($filter, true);
        $data['no'] = $data['urls']->firstItem();
        $data['urls']->withPath(url()->current().$param);

        return view('backend.url.index', compact('data'), [
            'title' => __('module/url.title'),
            'breadcrumbs' => [
                __('module/url.caption') => '',
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
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['urls'] = $this->indexUrlService->getIndexUrlList($filter, true, 10, true, [], [
            'deleted_at' => 'ASC'
        ]);
        $data['no'] = $data['urls']->firstItem();
        $data['urls']->withPath(url()->current().$param);

        return view('backend.url.trash', compact('data'), [
            'title' => __('module/url.title').' - '.__('global.trash'),
            'routeBack' => route('url.index'),
            'breadcrumbs' => [
                __('module/url.caption') => route('url.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create()
    {
        return view('backend.url.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/url.caption')
            ]),
            'routeBack' => route('url.index'),
            'breadcrumbs' => [
                __('module/url.caption') => route('url.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(IndexUrlRequest $request)
    {
        $data = $request->all();
        $indexUrl = $this->indexUrlService->store($data);

        if ($indexUrl['success'] == true) {
            return $this->redirectForm($data)->with('success', $indexUrl['message']);
        }

        return redirect()->back()->with('failed', $indexUrl['message']);
    }

    public function edit($id)
    {
        $data['url'] = $this->indexUrlService->getIndexUrl(['id' => $id]);

        return view('backend.url.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('module/url.caption')
            ]),
            'routeBack' => route('url.index'),
            'breadcrumbs' => [
                __('module/url.caption') => route('url.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(IndexUrlRequest $request, $id)
    {
        $data = $request->all();
        $indexUrl = $this->indexUrlService->update($data, ['id' => $id]);

        if ($indexUrl['success'] == true) {
            return $this->redirectForm($data)->with('success', $indexUrl['message']);
        }

        return redirect()->back()->with('failed', $indexUrl['message']);
    }

    public function softDelete($id)
    {
        $indexUrl = $this->indexUrlService->trash(['id' => $id]);

        return $indexUrl;
    }

    public function permanentDelete(Request $request, $id)
    {
        $indexUrl = $this->indexUrlService->delete($request, ['id' => $id]);

        return $indexUrl;
    }

    public function restore($id)
    {
        $indexUrl = $this->indexUrlService->restore(['id' => $id]);

        if ($indexUrl['success'] == true) {
            return redirect()->back()->with('success', $indexUrl['message']);
        }

        return redirect()->back()->with('failed', $indexUrl['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('url.index');
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
