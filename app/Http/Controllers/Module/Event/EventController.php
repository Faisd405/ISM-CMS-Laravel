<?php

namespace App\Http\Controllers\Module\Event;

use App\Exports\EventFormExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Event\EventFormRequest;
use App\Http\Requests\Module\Event\EventRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Feature\NotificationService;
use App\Services\Module\EventService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EventController extends Controller
{
    private $eventService, $languageService, $configService, $userService,
        $notifService;

    public function __construct(
        EventService $eventService,
        LanguageService $languageService,
        ConfigurationService $configService,
        UserService $userService,
        NotificationService $notifService
    )
    {
        $this->eventService = $eventService;
        $this->languageService = $languageService;
        $this->configService = $configService;
        $this->userService = $userService;
        $this->notifService = $notifService;

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

        $data['events'] = $this->eventService->getEventList($filter, true, 10, false, [], 
            config('cms.module.event.ordering'));
        $data['no'] = $data['events']->firstItem();
        $data['events']->withQueryString();

        return view('backend.events.index', compact('data'), [
            'title' => __('module/event.title'),
            'breadcrumbs' => [
                __('module/event.caption') => '',
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

        $data['events'] = $this->eventService->getEventList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['events']->firstItem();
        $data['events']->withQueryString();

        return view('backend.events.trash', compact('data'), [
            'title' => __('module/event.title').' - '.__('global.trash'),
            'routeBack' => route('event.index'),
            'breadcrumbs' => [
                __('module/event.caption') => route('event.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.events.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/event.caption')
            ]),
            'routeBack' => route('event.index', $request->query()),
            'breadcrumbs' => [
                __('module/event.caption') => route('event.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(EventRequest $request)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['meeting_url'] = $request->meeting_url;
        $data['meeting_id'] = $request->meeting_id;
        $data['meeting_passcode'] = $request->meeting_passcode;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_form_description'] = (bool)$request->config_show_form_description;
        $data['config_show_register_code'] = (bool)$request->config_show_register_code;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_show_form'] = (bool)$request->config_show_form;
        $data['config_lock_form'] = (bool)$request->config_lock_form;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $event = $this->eventService->storeEvent($data);
        $data['query'] = $request->query();

        if ($event['success'] == true) {
            return $this->redirectForm($data)->with('success', $event['message']);
        }

        return redirect()->back()->with('failed', $event['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['event'] = $this->eventService->getEvent(['id' => $id]);
        if (empty($data['event']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.events.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/event.caption')
            ]),
            'routeBack' => route('event.index'),
            'breadcrumbs' => [
                __('module/event.caption') => route('event.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(EventRequest $request, $id)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['meeting_url'] = $request->meeting_url;
        $data['meeting_id'] = $request->meeting_id;
        $data['meeting_passcode'] = $request->meeting_passcode;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_form_description'] = (bool)$request->config_show_form_description;
        $data['config_show_register_code'] = (bool)$request->config_show_register_code;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_show_form'] = (bool)$request->config_show_form;
        $data['config_lock_form'] = (bool)$request->config_lock_form;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $event = $this->eventService->updateEvent($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($event['success'] == true) {
            return $this->redirectForm($data)->with('success', $event['message']);
        }

        return redirect()->back()->with('failed', $event['message']);
    }

    public function publish($id)
    {
        $event = $this->eventService->statusEvent('publish', ['id' => $id]);

        if ($event['success'] == true) {
            return back()->with('success', $event['message']);
        }

        return redirect()->back()->with('failed', $event['message']);
    }

    public function approved($id)
    {
        $event = $this->eventService->statusEvent('approved', ['id' => $id]);

        if ($event['success'] == true) {
            return back()->with('success', $event['message']);
        }

        return redirect()->back()->with('failed', $event['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->eventService->sortEvent(['id' => $value], $i);
        }
    }

    public function position(Request $request, $id, $position)
    {
        $event = $this->eventService->positionEvent(['id' => $id], $position, $request->parent);

        if ($event['success'] == true) {
            return back()->with('success', $event['message']);
        }

        return redirect()->back()->with('failed', $event['message']);
    }

    public function softDelete($id)
    {
        $event = $this->eventService->trashEvent(['id' => $id]);

        return $event;
    }

    public function permanentDelete(Request $request, $id)
    {
        $event = $this->eventService->deleteEvent($request, ['id' => $id]);

        return $event;
    }

    public function restore($id)
    {
        $event = $this->eventService->restoreEvent(['id' => $id]);

        if ($event['success'] == true) {
            return redirect()->back()->with('success', $event['message']);
        }

        return redirect()->back()->with('failed', $event['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('event.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    /**
     * Form
     */
    public function form(Request $request, $eventId)
    {
        $filter['event_id'] = $eventId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('status', '') != '') {
            $filter['status'] = $request->input('status');
        }
        if ($request->input('exported', '') != '') {
            $filter['exported'] = $request->input('exported');
        }

        $data['event'] = $this->eventService->getEvent(['id' => $eventId]);
        if (empty($data['event']))
            return abort(404);

        $data['forms'] = $this->eventService->getFormList($filter, true, 10, false, [], [
            'submit_time' => 'DESC'
        ]);
        $data['no'] = $data['forms']->firstItem();
        $data['forms']->withQueryString();
        $data['fields'] = $this->eventService->getFieldList([
            'event_id' => $eventId,
            'publish' => 1,
            'approved' => 1
        ], false, 0);

        return view('backend.events.form.index', compact('data'), [
            'title' => __('module/event.form.title'),
            'routeBack' => route('event.index'),
            'breadcrumbs' => [
                __('module/event.caption') => route('event.index'),
                __('module/event.form.caption') => ''
            ]
        ]);
    }

    public function exportForm(Request $request, $eventId)
    {
        $event = $this->eventService->getEvent(['id' => $eventId]);
        $field = $this->eventService->getFieldList([
            'event_id' => $eventId,
            'publish' => 1,
            'approved' => 1,
        ], false, 0);

        $filter['event_id'] = $eventId;
        if ($request->input('status', '') != '') {
            $filter['status'] = $request->input('status');
        }
        if ($request->input('exported', '') != '') {
            $filter['exported'] = $request->input('exported');
        }

        $data = $this->eventService->getFormList($filter, false, 0);

        if ($data->count() == 0) {
            return back()->with('warning', __('global.data_attr_empty', [
                'attribute' => __('module/event.form.caption')
            ]));
        }

        $fileName = $event['slug'].'.xlsx';

        (new EventFormExport($event, $field, $data))->queue($fileName)->onQueue('exports');

        $event->forms()->where('exported', 0)->update([
            'exported' => 1
        ]);

        return response()->download(storage_path('app/' . $fileName));
    }

    public function statusForm($eventId, $id)
    {
        $event = $this->eventService->statusForm('status', ['id' => $id]);

        return $event;
    }

    public function destroyForm($eventId, $id)
    {
        $event = $this->eventService->deleteForm(['id' => $id]);

        return $event;
    }

    /**
     * frontend
     */
    public function list(Request $request)
    {
        if (config('cms.module.event.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = $this->configService->getConfigFile('banner_default');
        $limit = $this->configService->getConfigValue('content_limit');

        // eveet
        $data['events'] = $this->eventService->getEventList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], config('cms.module.event.ordering'));
        $data['no_events'] = $data['events']->firstItem();
        $data['events']->withQueryString();

        return view('frontend.events.list', compact('data'), [
            'title' => __('module/event.title'),
            'breadcrumbs' => [
                __('module/event.title') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slugEvent');

        $data['read'] = $this->eventService->getEvent(['slug' => $slug]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 || $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        if ($data['read']['detail'] == 0) {
            return redirect()->route('home');
        }

        if ($data['read']['public'] == 0 && App::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        //data
        $fields = $this->eventService->getFieldList([
            'event_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1,
            'is_unique' => 1
        ], false, 0);
        if ($fields->count()) {
            $form = $data['read']->forms()->firstWhere('fields->'.$fields[0]['name'], 
                $request->input($fields[0]['name'], ''));
        }

        if (isset($form))
            $data['form'] = $form;

        $data['fields'] = $this->eventService->getFieldList([
            'event_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1,
        ], false, 0, false, [], [
            'position' => 'ASC'
        ]);

        $data['custom_fields'] = $data['read']['custom_fields'];
        $data['creator'] = $data['read']['createBy']['name'];
        $data['cover'] = $data['read']['cover_src'];
        $data['banner'] = $data['read']['banner_src'];

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');
        if (!empty($data['read']['seo']['title'])) {
            $data['meta_title'] = Str::limit(strip_tags($data['read']['seo']['title']), 69);
        }

        $data['meta_description'] = $this->configService->getConfigValue('meta_description');
        if (!empty($data['read']['seo']['description'])) {
            $data['meta_description'] = $data['read']['seo']['description'];
        } elseif (empty($data['read']['seo']['description']) && 
            !empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        $data['meta_keywords'] = $this->configService->getConfigValue('meta_keywords');
        if (!empty($data['read']['seo']['keywords'])) {
            $data['meta_keywords'] = $data['read']['seo']['keywords'];
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

        // record hits
        $this->eventService->recordHits(['id' => $data['read']['id']]);

        return view('frontend.events.'.$data['read']['slug'], compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }

    public function submitForm(EventFormRequest $request)
    {
        $id = $request->route('id');
        $event = $this->eventService->getevent(['id' => $id]);

        if ($event['config']['show_form'] == false) {
            return redirect()->back();
        }

        $fields = $this->eventService->getFieldList([
            'event_id' => $id,
            'publish' => 1,
            'approved' => 1,
            'is_unique' => 1
        ], false, 0);

        if ($fields->count()) {

            $unique =  $event->forms();
            foreach ($fields as $key => $value) {
                $unique->where('fields->'.$value['name'], $request->input($value['name'], ''));
            }

            if ($unique->count() > 0) {
                return redirect()->back()->with('failed', __('module/event.form.unique_warning'));
            }
        }

        $data = [
            'title' => $event->fieldLang('name'),
            'event' => $event,
            'request' => $request->all(),
        ];

        $formData = $request->all();
        $formData['event_id'] = $id;
        $firstField = $this->eventService->getField([
            'event_id' => $id,
            'publish' => 1,
            'approved' => 1
        ]);

        $this->eventService->recordForm($formData);

        if ($this->configService->getConfigValue('notif_apps_event') == 1) {
            $this->notifService->sendNotif([
                'user_from' => null,
                'user_to' => $this->userService->getUserList(['role_in' => [1, 2, 3]], false)
                    ->pluck('id')->toArray(),
                'attribute' => [
                    'icon' => 'las la-calendar',
                    'color' => 'success',
                    'title' => __('feature/notification.event.title'),
                    'content' =>  __('feature/notification.event.text', [
                        'attribute' => $event->fieldLang('name')
                    ]),
                ],
                'read_by' => [],
                'link' => 'admin/event/'.$id.'/form?q='.$formData[$firstField['name']].'&',
            ]);
        }

        if ($event['config']['lock_form'] == true) {
            Cookie::queue($event['slug'], $event->fieldLang('name'), 120);
        }

        $message = __('module/event.form.submit_success');

        $redirect['slugEvent'] = $event['slug'];
        if ($fields->count()) {
            $redirect[$fields[0]['name']] = $request->input($fields[0]['name']);
        }

        try {
            
            if ($this->configService->getConfigValue('notif_email_event') == 1 && !empty($event['email'])) {
                Mail::to($event['email'])->send(new \App\Mail\EventFormMail($data));
            }
            
            return redirect()->route('event.read', $redirect)->with('success', $message);

        } catch (Exception $e) {
            return back()->with('warning', $e->getMessage());
        }
    }
}
