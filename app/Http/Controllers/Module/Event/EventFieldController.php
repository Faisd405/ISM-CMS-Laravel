<?php

namespace App\Http\Controllers\Module\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Event\EventFieldRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class EventFieldController extends Controller
{
    private $eventService, $languageService;

    public function __construct(
        EventService $eventService,
        LanguageService $languageService
    )
    {
        $this->eventService = $eventService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $eventId)
    {
        $filter['event_id'] = $eventId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['event'] = $this->eventService->getEvent(['id' => $eventId]);
        if (empty($data['event']))
            return abort(404);
            
        $data['fields'] = $this->eventService->getFieldList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['fields']->firstItem();
        $data['fields']->withQueryString();

        return view('backend.events.field.index', compact('data'), [
            'title' => __('module/event.field.title'),
            'routeBack' => route('event.index'),
            'breadcrumbs' => [
                __('module/event.caption') => route('event.index'),
                __('module/event.field.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $eventId)
    {
        $filter['event_id'] = $eventId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['event'] = $this->eventService->getEvent(['id' => $eventId]);
        if (empty($data['event']))
            return abort(404);

        $data['fields'] = $this->eventService->getFieldList($filter, true, 10, true, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['fields']->firstItem();
        $data['fields']->withQueryString();

        return view('backend.events.field.trash', compact('data'), [
            'title' => __('module/event.field.title').' - '.__('global.trash'),
            'routeBack' => route('event.field.index', ['eventId' => $eventId]),
            'breadcrumbs' => [
                __('module/event.caption') => 'javascript:;',
                __('module/event.field.caption') => route('event.field.index', ['eventId' => $eventId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $eventId)
    {
        $data['event'] = $this->eventService->getEvent(['id' => $eventId]);
        if (empty($data['event']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.events.field.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/event.field.caption')
            ]),
            'routeBack' => route('event.field.index', array_merge(['eventId' => $eventId], $request->query())),
            'breadcrumbs' => [
                __('module/event.field.caption') => route('event.field.index', ['eventId' => $eventId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(EventFieldRequest $request, $eventId)
    {
        $data = $request->all();
        
        $data['event_id'] = $eventId;
        $data['is_unique'] = (bool)$request->is_unique;
        $field = $this->eventService->storeField($data);
        $data['query'] = $request->query();

        if ($field['success'] == true) {
            return $this->redirectForm($data)->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function edit(Request $request, $eventId, $id)
    {
        $data['event'] = $this->eventService->getEvent(['id' => $eventId]);
        if (empty($data['event']))
            return abort(404);

        $data['field'] = $this->eventService->getField(['id' => $id]);
        if (empty($data['field']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.events.field.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/event.field.caption')
            ]),
            'routeBack' => route('event.field.index', array_merge(['eventId' => $eventId], $request->query())),
            'breadcrumbs' => [
                __('module/event.field.caption') => route('event.field.index', ['eventId' => $eventId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(eventFieldRequest $request, $eventId, $id)
    {
        $data = $request->all();

        $data['event_id'] = $eventId;
        $data['is_unique'] = (bool)$request->is_unique;
        $field = $this->eventService->updateField($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($field['success'] == true) {
            return $this->redirectForm($data)->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function publish($eventId, $id)
    {
        $field = $this->eventService->statusField('publish', ['id' => $id]);

        if ($field['success'] == true) {
            return back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function approved($eventId, $id)
    {
        $field = $this->eventService->statusField('approved', ['id' => $id]);

        if ($field['success'] == true) {
            return back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function position(Request $request, $eventId, $id, $position)
    {
        $field = $this->eventService->positionField(['id' => $id], $position);

        if ($field['success'] == true) {
            return back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function softDelete($eventId, $id)
    {
        $field = $this->eventService->trashField(['id' => $id]);

        return $field;
    }

    public function permanentDelete(Request $request, $eventId, $id)
    {
        $field = $this->eventService->deleteField($request, ['id' => $id]);

        return $field;
    }

    public function restore($eventId, $id)
    {
        $field = $this->eventService->restoreField(['id' => $id]);

        if ($field['success'] == true) {
            return redirect()->back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('event.field.index', array_merge(['eventId' => $data['event_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
