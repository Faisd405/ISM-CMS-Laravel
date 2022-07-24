<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\WidgetRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\BannerService;
use App\Services\Module\WidgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class WidgetController extends Controller
{
    private $widgetService, $languageService, $apiService;

    public function __construct(
        WidgetService $widgetService,
        LanguageService $languageService
    )
    {
        $this->widgetService = $widgetService;
        $this->languageService = $languageService;

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
        if ($request->input('widget_set', '') != '') {
            $filter['widget_set'] = $request->input('widget_set');
        }
        if ($request->input('widget_type', '') != '') {
            $filter['type'] = $request->input('widget_type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['widgets'] = $this->widgetService->getWidgetList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['widgets']->firstItem();
        $data['widgets']->withQueryString();

        return view('backend.widgets.index', compact('data'), [
            'title' => __('module/widget.title'),
            'breadcrumbs' => [
                __('module/widget.caption') => '',
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
        if ($request->input('widget_set', '') != '') {
            $filter['widget_set'] = $request->input('widget_set');
        }
        if ($request->input('widget_type', '') != '') {
            $filter['type'] = $request->input('widget_type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['widgets'] = $this->widgetService->getWidgetList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['widgets']->firstItem();
        $data['widgets']->withQueryString();

        return view('backend.widgets.trash', compact('data'), [
            'title' => __('module/widget.title').' - '.__('global.trash'),
            'routeBack' => route('widget.index'),
            'breadcrumbs' => [
                __('module/widget.caption') => route('widget.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $type)
    {
        $data['type'] = $type;
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['banners'] = App::make(BannerService::class)->getBannerList([
            'publish' => 1,
            'approved' => 1
        ], false, 0, false, [], []);

        return view('backend.widgets.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/widget.caption')
            ]),
            'routeBack' => route('widget.index', $request->query()),
            'breadcrumbs' => [
                __('module/widget.caption') => route('widget.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(WidgetRequest $request, $type)
    {
        $data = $request->all();
        $data['type'] = $type;
        $data['global'] = (bool)$request->global;
        $data['locked'] = (bool)$request->locked;
        $data['post_selected'] = (bool)$request->post_selected;
        $data['post_hits'] = (bool)$request->post_hits;
        $data['config_order_by'] = $request->config_order_by;
        $data['config_order_type'] = $request->config_order_type;
        $data['config_show_image'] = (bool)$request->config_show_image;
        $data['config_show_url'] = (bool)$request->config_show_url;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $widget = $this->widgetService->storeWidget($data);
        $data['query'] = $request->query();

        if ($widget['success'] == true) {
            return $this->redirectForm($data)->with('success', $widget['message']);
        }

        return redirect()->back()->with('failed', $widget['message']);
    }

    public function edit(Request $request, $type, $id)
    {
        $data['widget'] = $this->widgetService->getWidget(['id' => $id]);
        if (empty($data['widget']))
            return abort(404);

        $data['widget']['module'] = $this->widgetService->getModuleData($data['widget']);
        $data['type'] = $type;
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['banners'] = App::make(BannerService::class)->getBannerList([
            'publish' => 1,
            'approved' => 1
        ], false, 0, false, [], []);

        return view('backend.widgets.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/widget.caption')
            ]),
            'routeBack' => route('widget.index', $request->query()),
            'breadcrumbs' => [
                __('module/widget.caption') => route('widget.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(WidgetRequest $request, $type, $id)
    {
        $data = $request->all();
        $data['type'] = $type;
        $data['global'] = (bool)$request->global;
        $data['locked'] = (bool)$request->locked;
        $data['post_selected'] = (bool)$request->post_selected;
        $data['post_hits'] = (bool)$request->post_hits;
        $data['config_order_by'] = $request->config_order_by;
        $data['config_order_type'] = $request->config_order_type;
        $data['config_show_image'] = (bool)$request->config_show_image;
        $data['config_show_url'] = (bool)$request->config_show_url;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $widget = $this->widgetService->updateWidget($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($widget['success'] == true) {
            return $this->redirectForm($data)->with('success', $widget['message']);
        }

        return redirect()->back()->with('failed', $widget['message']);
    }

    public function publish($id)
    {
        $widget = $this->widgetService->statusWidget('publish', ['id' => $id]);

        if ($widget['success'] == true) {
            return back()->with('success', $widget['message']);
        }

        return redirect()->back()->with('failed', $widget['message']);
    }

    public function approved($id)
    {
        $widget = $this->widgetService->statusWidget('approved', ['id' => $id]);

        if ($widget['success'] == true) {
            return back()->with('success', $widget['message']);
        }

        return redirect()->back()->with('failed', $widget['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->widgetService->sortWidget(['id' => $value], $i);
        }
    }

    public function position(Request $request, $id, $position)
    {
        $widget = $this->widgetService->positionWidget(['id' => $id], $position);

        if ($widget['success'] == true) {
            return back()->with('success', $widget['message']);
        }

        return redirect()->back()->with('failed', $widget['message']);
    }

    public function softDelete($id)
    {
        $widget = $this->widgetService->trashWidget(['id' => $id]);

        return $widget;
    }

    public function permanentDelete(Request $request, $id)
    {
        $widget = $this->widgetService->deleteWidget($request, ['id' => $id]);

        return $widget;
    }

    public function restore($id)
    {
        $widget = $this->widgetService->restoreWidget(['id' => $id]);

        if ($widget['success'] == true) {
            return redirect()->back()->with('success', $widget['message']);
        }

        return redirect()->back()->with('failed', $widget['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('widget.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
