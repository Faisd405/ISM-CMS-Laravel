<?php

namespace App\Http\Controllers\Module\Inquiry;

use App\Exports\InquiryFormExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Inquiry\InquiryFormRequest;
use App\Http\Requests\Module\Inquiry\InquiryRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Feature\NotificationService;
use App\Services\Module\InquiryService;
use App\Services\UserService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InquiryController extends Controller
{
    use ApiResponser;

    private $inquiryService, $languageService, $configService, $userService,
        $notifService;

    public function __construct(
        InquiryService $inquiryService,
        LanguageService $languageService,
        ConfigurationService $configService,
        UserService $userService,
        NotificationService $notifService
    )
    {
        $this->inquiryService = $inquiryService;
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

        $data['inquiries'] = $this->inquiryService->getInquiryList($filter, true, 10, false, [], 
            config('cms.module.inquiry.ordering'));
        $data['no'] = $data['inquiries']->firstItem();
        $data['inquiries']->withQueryString();

        return view('backend.inquiries.index', compact('data'), [
            'title' => __('module/inquiry.title'),
            'breadcrumbs' => [
                __('module/inquiry.caption') => '',
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

        $data['inquiries'] = $this->inquiryService->getInquiryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['inquiries']->firstItem();
        $data['inquiries']->withQueryString();

        return view('backend.inquiries.trash', compact('data'), [
            'title' => __('module/inquiry.title').' - '.__('global.trash'),
            'routeBack' => route('inquiry.index'),
            'breadcrumbs' => [
                __('module/inquiry.caption') => route('inquiry.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.inquiries.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/inquiry.caption')
            ]),
            'routeBack' => route('inquiry.index', $request->query()),
            'breadcrumbs' => [
                __('module/inquiry.caption') => route('inquiry.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(InquiryRequest $request)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_body'] = $request->config_show_body;
        $data['config_show_after_body'] = $request->config_show_after_body;
        $data['config_show_banner'] = $request->config_show_banner;
        $data['config_show_map'] = $request->config_show_map;
        $data['config_show_form'] = $request->config_show_form;
        $data['config_lock_form'] = $request->config_lock_form;
        $data['config_send_mail_sender'] = $request->config_send_mail_sender;
        $data['config_show_custom_field'] = $request->config_show_custom_field;
        $inquiry = $this->inquiryService->storeInquiry($data);
        $data['query'] = $request->query();

        if ($inquiry['success'] == true) {
            return $this->redirectForm($data)->with('success', $inquiry['message']);
        }

        return redirect()->back()->with('failed', $inquiry['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['inquiry'] = $this->inquiryService->getInquiry(['id' => $id]);
        if (empty($data['inquiry']))
            return abort(404);
            
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.inquiries.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/inquiry.caption')
            ]),
            'routeBack' => route('inquiry.index', $request->query()),
            'breadcrumbs' => [
                __('module/inquiry.caption') => route('inquiry.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(InquiryRequest $request, $id)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_body'] = $request->config_show_body;
        $data['config_show_after_body'] = $request->config_show_after_body;
        $data['config_show_banner'] = $request->config_show_banner;
        $data['config_show_map'] = $request->config_show_map;
        $data['config_show_form'] = $request->config_show_form;
        $data['config_lock_form'] = $request->config_lock_form;
        $data['config_send_mail_sender'] = $request->config_send_mail_sender;
        $data['config_show_custom_field'] = $request->config_show_custom_field;
        $inquiry = $this->inquiryService->updateInquiry($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($inquiry['success'] == true) {
            return $this->redirectForm($data)->with('success', $inquiry['message']);
        }

        return redirect()->back()->with('failed', $inquiry['message']);
    }

    public function publish($id)
    {
        $inquiry = $this->inquiryService->statusInquiry('publish', ['id' => $id]);

        if ($inquiry['success'] == true) {
            return back()->with('success', $inquiry['message']);
        }

        return redirect()->back()->with('failed', $inquiry['message']);
    }

    public function approved($id)
    {
        $inquiry = $this->inquiryService->statusInquiry('approved', ['id' => $id]);

        if ($inquiry['success'] == true) {
            return back()->with('success', $inquiry['message']);
        }

        return redirect()->back()->with('failed', $inquiry['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->inquiryService->sortInquiry(['id' => $value], $i);
        }
    }

    public function position(Request $request, $id, $position)
    {
        $inquiry = $this->inquiryService->positionInquiry(['id' => $id], $position, $request->parent);

        if ($inquiry['success'] == true) {
            return back()->with('success', $inquiry['message']);
        }

        return redirect()->back()->with('failed', $inquiry['message']);
    }

    public function softDelete($id)
    {
        $inquiry = $this->inquiryService->trashInquiry(['id' => $id]);

        return $inquiry;
    }

    public function permanentDelete(Request $request, $id)
    {
        $inquiry = $this->inquiryService->deleteInquiry($request, ['id' => $id]);

        return $inquiry;
    }

    public function restore($id)
    {
        $inquiry = $this->inquiryService->restoreInqury(['id' => $id]);

        if ($inquiry['success'] == true) {
            return redirect()->back()->with('success', $inquiry['message']);
        }

        return redirect()->back()->with('failed', $inquiry['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('inquiry.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    /**
     * Form
     */
    public function form(Request $request, $inquiryId)
    {
        $filter['inquiry_id'] = $inquiryId;
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

        $data['inquiry'] = $this->inquiryService->getInquiry(['id' => $inquiryId]);
        if (empty($data['inquiry']))
            return abort(404);

        $data['forms'] = $this->inquiryService->getFormList($filter, true, 10, false, [], [
            'submit_time' => 'DESC'
        ]);
        $data['no'] = $data['forms']->firstItem();
        $data['forms']->withQueryString();
        $data['fields'] = $this->inquiryService->getFieldList([
            'inquiry_id' => $inquiryId,
            'publish' => 1,
            'approved' => 1
        ], false, 0);

        return view('backend.inquiries.form.index', compact('data'), [
            'title' => __('module/inquiry.form.title'),
            'routeBack' => route('inquiry.index'),
            'breadcrumbs' => [
                __('module/inquiry.caption') => route('inquiry.index'),
                __('module/inquiry.form.caption') => ''
            ]
        ]);
    }

    public function totalUnread()
    {
        $totalUnread = $this->inquiryService->getFormList(['status' => 0], false, 0)->count();

        return $this->success($totalUnread, 'Load form inquiry Unread successfully');
    }

    public function exportForm(Request $request, $inquiryId)
    {
        $inquiry = $this->inquiryService->getInquiry(['id' => $inquiryId]);
        $field = $this->inquiryService->getFieldList([
            'inquiry_id' => $inquiryId,
            'publish' => 1,
            'approved' => 1,
        ], false, 0);

        $filter['inquiry_id'] = $inquiryId;
        if ($request->input('status', '') != '') {
            $filter['status'] = $request->input('status');
        }
        if ($request->input('exported', '') != '') {
            $filter['exported'] = $request->input('exported');
        }

        $data = $this->inquiryService->getFormList($filter, false, 0);

        if ($data->count() == 0) {
            return back()->with('warning', __('global.data_attr_empty', [
                'attribute' => __('module/inquiry.form.caption')
            ]));
        }

        $fileName = $inquiry['slug'].'.xlsx';

        (new InquiryFormExport($inquiry, $field, $data))->queue($fileName)->onQueue('exports');

        $inquiry->forms()->where('exported', 0)->update([
            'exported' => 1
        ]);

        return response()->download(storage_path('app/' . $fileName));
    }

    public function statusForm($inquiryId, $id)
    {
        $inquiry = $this->inquiryService->statusForm('status', ['id' => $id]);

        return $inquiry;
    }

    public function destroyForm($inquiryId, $id)
    {
        $inquiry = $this->inquiryService->deleteForm(['id' => $id]);

        return $inquiry;
    }

    /**
     * frontend
     */
    public function list(Request $request)
    {
        if (config('cms.module.inquiry.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = $this->configService->getConfigFile('banner_default');
        $limit = $this->configService->getConfigValue('content_limit');

        // inquiry
        $data['inquiries'] = $this->inquiryService->getInquiryList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], config('cms.module.inquiry.ordering'));
        $data['no_inquiries'] = $data['inquiries']->firstItem();
        $data['inquiries']->withQueryString();

        return view('frontend.inquiries.list', compact('data'), [
            'title' => __('module/inquiry.title'),
            'breadcrumbs' => [
                __('module/inquiry.title') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slug');

        $data['read'] = $this->inquiryService->getInquiry(['slug' => $slug]);

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
        $data['fields'] = $this->inquiryService->getFieldList([
            'inquiry_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1,
        ], false, 0, false, [], [
            'position' => 'ASC'
        ]);

        $data['custom_fields'] = $data['read']['custom_fields'];
        $data['creator'] = $data['read']['createBy']['name'];
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
            !empty($data['read']->fieldLang('body'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('body')), 155);
        } elseif (empty($data['read']['seo']['description']) && 
            empty($data['read']->fieldLang('body')) && !empty($data['read']->fieldLang('after_body'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('after_body')), 155);
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
            $this->configService->getConfigFile('cover_default')."&url=".URL::full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

        // record hits
        $this->inquiryService->recordHits(['id' => $data['read']['id']]);

        return view('frontend.inquiries.'.$data['read']['slug'], compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }

    public function submitForm(InquiryFormRequest $request)
    {
        $id = $request->route('id');
        $inquiry = $this->inquiryService->getInquiry(['id' => $id]);

        if ($inquiry['config']['show_form'] == false) {
            return abort(404);
        }

        $fields = $this->inquiryService->getFieldList([
            'inquiry_id' => $id,
            'publish' => 1,
            'approved' => 1,
            'is_unique' => 1
        ], false, 0);

        if ($fields->count()) {

            $unique =  $inquiry->forms();
            foreach ($fields as $key => $value) {
                $unique->where('fields->'.$value['name'], $request->input($value['name'], ''));
            }

            if ($unique->count() > 0) {
                return redirect()->back()->with('failed', __('module/inquiry.form.unique_warning'));
            }
        }

        $data = [
            'title' => $inquiry->fieldLang('name'),
            'inquiry' => $inquiry,
            'request' => $request->all(),
            'webname' => $this->configService->getConfigValue('website_name'),
        ];

        $formData = $request->all();
        $formData['inquiry_id'] = $id;
        $firstField = $this->inquiryService->getField([
            'inquiry_id' => $id,
            'publish' => 1,
            'approved' => 1
        ]);

        $this->inquiryService->recordForm($formData);
        

        if ($this->configService->getConfigValue('notif_apps_inquiry') == 1) {
            $this->notifService->sendNotif([
                'user_from' => null,
                'user_to' => $this->userService->getUserList(['role_in' => [1, 2, 3]], false)
                    ->pluck('id')->toArray(),
                'attribute' => [
                    'icon' => 'las la-envelope',
                    'color' => 'success',
                    'title' => __('feature/notification.inquiry.title'),
                    'content' =>  __('feature/notification.inquiry.text', [
                        'attribute' => $inquiry->fieldLang('name')
                    ]),
                ],
                'read_by' => [],
                'link' => 'admin/inquiry/'.$id.'/form?q='.$formData[$firstField['name']].'&',
            ]);
        }

        if ($inquiry['config']['lock_form'] == true) {
            Cookie::queue($inquiry['slug'], $inquiry->fieldLang('name'), 120);
        }

        $message = __('module/inquiry.form.submit_success');
        if (!empty($inquiry->fieldLang('after_body'))) {
            $message = strip_tags($inquiry->fieldLang('after_body'));
        }

        try {
            
            if ($this->configService->getConfigValue('notif_email_inquiry') == 1 && !empty($inquiry['email'])) {
                Mail::to($inquiry['email'])->send(new \App\Mail\InquiryFormMail($data));
            }

            if ($inquiry['config']['send_mail_sender'] == true && $request->input('email', '') != '') {
                Mail::to($request->input('email'))->send(new \App\Mail\InquirySenderMail($data));
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            return back()->with('warning', $e->getMessage());
        }
    }
}
