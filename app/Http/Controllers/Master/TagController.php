<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\TagRequest;
use App\Repositories\Master\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    private $tagService;

    public function __construct(
        TagRepository $tagService
    )
    {
        $this->tagService = $tagService;
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
        if ($request->input('flags', '') != '') {
            $filter['flags'] = $request->input('flags');
        }
        if ($request->input('standar', '') != '') {
            $filter['standar'] = $request->input('standar');
        }

        $data['tags'] = $this->tagService->getTagList($filter, true);
        $data['no'] = $data['tags']->firstItem();
        $data['tags']->withQueryString();

        return view('backend.masters.tags.index', compact('data'), [
            'title' => __('master/tags.title'),
            'breadcrumbs' => [
                __('master/tags.caption') => '',
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
        if ($request->input('flags', '') != '') {
            $filter['flags'] = $request->input('flags');
        }
        if ($request->input('standar', '') != '') {
            $filter['standar'] = $request->input('standar');
        }

        $data['tags'] = $this->tagService->getTagList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['tags']->firstItem();
        $data['tags']->withQueryString();

        return view('backend.masters.tags.trash', compact('data'), [
            'title' => __('master/tags.title').' - '.__('global.trash'),
            'routeBack' => route('tags.index'),
            'breadcrumbs' => [
                __('master/tags.caption') => route('tags.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        return view('backend.masters.tags.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('master/tags.caption')
            ]),
            'routeBack' => route('tags.index', $request->query()),
            'breadcrumbs' => [
                __('master/tags.caption') => route('tags.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(TagRequest $request)
    {
        $data = $request->all();
        $data['flags'] = (bool)$request->flags;
        $data['standar'] = (bool)$request->standar;
        $tags = $this->tagService->store($data);
        $data['query'] = $request->query();

        if ($tags['success'] == true) {
            return $this->redirectForm($data)->with('success', $tags['message']);
        }

        return redirect()->back()->with('failed', $tags['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['tag'] = $this->tagService->getTag(['id' => $id]);

        if (empty($data['tag']))
            return abort(404);

        return view('backend.masters.tags.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('master/tags.caption')
            ]),
            'routeBack' => route('tags.index', $request->query()),
            'breadcrumbs' => [
                __('master/tags.caption') => route('tags.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(TagRequest $request, $id)
    {
        $data = $request->all();
        $data['flags'] = (bool)$request->flags;
        $data['standar'] = (bool)$request->standar;
        $tags = $this->tagService->update($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($tags['success'] == true) {
            return $this->redirectForm($data)->with('success', $tags['message']);
        }

        return redirect()->back()->with('failed', $tags['message']);
    }

    public function flags($id)
    {
        $tags = $this->tagService->status('flags', ['id' => $id]);

        if ($tags['success'] == true) {
            return back()->with('success', $tags['message']);
        }

        return redirect()->back()->with('failed', $tags['message']);
    }

    public function standar($id)
    {
        $tags = $this->tagService->status('standar', ['id' => $id]);

        if ($tags['success'] == true) {
            return back()->with('success', $tags['message']);
        }

        return redirect()->back()->with('failed', $tags['message']);
    }

    public function softDelete($id)
    {
        $tag = $this->tagService->trash(['id' => $id]);

        return $tag;
    }

    public function permanentDelete(Request $request, $id)
    {
        $tag = $this->tagService->delete($request, ['id' => $id]);

        return $tag;
    }

    public function restore($id)
    {
        $tag = $this->tagService->restore(['id' => $id]);

        if ($tag['success'] == true) {
            return redirect()->back()->with('success', $tag['message']);
        }

        return redirect()->back()->with('failed', $tag['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('tags.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    public function autocomplete(Request $request)
    {
        $filter['flags'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $tags = $this->tagService->getTagList($filter, false, 5)->pluck('name');

        return response()->json($tags, 200);
    }
}
