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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
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

        $data['events'] = $this->eventService->getEventList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['events']->firstItem();
        $data['events']->withPath(url()->current().$param);

        return view('backend.events.index', compact('data'), [
            'title' => __('module/event.title'),
            'breadcrumbs' => [
                __('module/event.caption') => '',
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

        $data['events'] = $this->eventService->getEventList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['events']->firstItem();
        $data['events']->withPath(url()->current().$param);

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
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_form'] = (bool)$request->hide_form;
        $data['lock_form'] = (bool)$request->lock_form;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $data['meeting_url'] = $request->meeting_url;
        $data['meeting_id'] = $request->meeting_id;
        $data['meeting_passcode'] = $request->meeting_passcode;
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
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_form'] = (bool)$request->hide_form;
        $data['lock_form'] = (bool)$request->lock_form;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $data['meeting_url'] = $request->meeting_url;
        $data['meeting_id'] = $request->meeting_id;
        $data['meeting_passcode'] = $request->meeting_passcode;
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
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['event_id'] = $eventId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('status', '') != '') {
            $filter['status'] = $request->input('status');
        }
        if ($request->input('exported', '') != '') {
            $filter['exported'] = $request->input('exported');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['event'] = $this->eventService->getEvent(['id' => $eventId]);
        if (empty($data['event']))
            return abort(404);

        $data['forms'] = $this->eventService->getFormList($filter, true, 10, false, [], [
            'submit_time' => 'DESC'
        ]);
        $data['no'] = $data['forms']->firstItem();
        $data['forms']->withPath(url()->current().$param);
        $data['fields'] = $this->eventService->getFieldList(['event_id' => $eventId], false);

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
        ], false);

        $filter['event_id'] = $eventId;
        if ($request->input('status', '') != '') {
            $filter['status'] = $request->input('status');
        }
        if ($request->input('exported', '') != '') {
            $filter['exported'] = $request->input('exported');
        }

        $data = $this->eventService->getFormList($filter, false);

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
        $data['events'] = $this->eventService->getEventList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);

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

        if ($data['read']['config']['is_detail'] == 0) {
            
            return redirect()->route('home');
        }

        if ($data['read']['public'] == 0 && App::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        $this->eventService->recordHits(['id' => $data['read']['id']]);

        //data
        if (!empty($data['read']['unique_fields'])) {
            $form = $data['read']->forms()->firstWhere('fields->'.$data['read']['unique_fields'][0], 
                $request->input($data['read']['unique_fields'][0], ''));
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
        $data['cover'] = $data['read']->coverSrc();
        $data['banner'] = $data['read']->bannerSrc();

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
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".url()->full().
            "&title=".$data['read']->fieldLang('name')."";
        $data['share_twitter'] = 'https://twitter.com/intent/tweet?text='.
            str_replace('#', '', $data['read']->fieldLang('name')).'&url='.url()->full();
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('name').
            " ".url()->full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            url()->full()."&title=".$data['read']->fieldLang('name')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $data['cover']."&url=".url()->full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

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

        if ($event['config']['hide_form'] == true) {
            return redirect()->back();
        }

        $start = $event['start_date'];
        $end = $event['end_date'];
        $now = now()->format('Y-m-d H:i');

        if (!empty($start) && $now >= $start->format('Y-m-d H:i') || !empty($end) && $now <= $end->format('Y-m-d H:i'))
            return abort(404);

        $start = $event['start_date'];
        $end = $event['end_date'];
        $now = now()->format('Y-m-d H:i');

        if (!empty($start) && $now < $start->format('Y-m-d H:i'))
            return redirect()->back()->with('warning', __('module/event.form.form_open_warning'));

        if (!empty($end) && $now > $end->format('Y-m-d H:i'))
            return redirect()->back()->with('warning', __('module/event.form.form_close_warning'));

        if (!empty($event['unique_fields'])) {

            $unique =  $event->forms();
            foreach ($event['unique_fields'] as $key => $value) {
                $unique->where('fields->'.$value, $request->input($value, ''));
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

        $this->eventService->recordForm($formData);
        
        if (config('cms.module.feature.notification.email.event') == true && !empty($event['email'])) {
            Mail::to($event['email'])->send(new \App\Mail\EventFormMail($data));
        }

        if (config('cms.module.feature.notification.apps.event') == true) {
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
                'link' => 'admin/event/'.$id.'/form?q='.$request->email.'&',
            ]);
        }

        if ($event['config']['lock_form'] == true) {
            Cookie::queue($event['slug'], $event->fieldLang('name'), 120);
        }

        $message = __('module/event.form.submit_success');

        $redirect['slugEvent'] = $event['slug'];
        if (!empty($event['unique_fields'])) {
            $redirect[$event['unique_fields'][0]] = $request->input($event['unique_fields'][0]);
        }

        return redirect()->route('event.read', $redirect)->with('success', $message);
    }
}
