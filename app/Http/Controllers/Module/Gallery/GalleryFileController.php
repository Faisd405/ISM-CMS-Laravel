<?php

namespace App\Http\Controllers\Module\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Gallery\GalleryFileMultipleRequest;
use App\Http\Requests\Module\Gallery\GalleryFileRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryFileController extends Controller
{
    private $galleryService, $languageService;

    public function __construct(
        GalleryService $galleryService,
        LanguageService $languageService
    )
    {
        $this->galleryService = $galleryService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $albumId)
    {
        $filter['gallery_album_id'] = $albumId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['album'] = $this->galleryService->getAlbum(['id' => $albumId]);
        if(empty($data['album']))
            return abort(404);

        $data['files'] = $this->galleryService->getFileList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('backend.galleries.file.index', compact('data'), [
            'title' => __('module/gallery.file.title'),
            'routeBack' => route('gallery.album.index'),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.album.caption') => route('gallery.album.index'),
                __('module/gallery.file.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $albumId)
    {
        $filter['gallery_album_id'] = $albumId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['album'] = $this->galleryService->getAlbum(['id' => $albumId]);
        if(empty($data['album']))
            return abort(404);

        $data['files'] = $this->galleryService->getFileList($filter, true, 10, true, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('backend.galleries.file.trash', compact('data'), [
            'title' => __('module/gallery.file.title').' - '.__('global.trash'),
            'routeBack' => route('gallery.file.index', ['albumId' => $albumId]),
            'breadcrumbs' => [
                __('module/gallery.caption') => 'javascript:;',
                __('module/gallery.file.caption') => route('gallery.file.index', ['albumId' => $albumId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $albumId)
    {
        $data['album'] = $this->galleryService->getAlbum(['id' => $albumId]);
        if(empty($data['album']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.galleries.file.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/gallery.file.caption')
            ]),
            'routeBack' => route('gallery.file.index', array_merge(['albumId' => $albumId], $request->query())),
            'breadcrumbs' => [
                __('module/gallery.file.caption') => route('gallery.file.index', ['albumId' => $albumId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(GalleryFileRequest $request, $albumId)
    {
        $data = $request->all();

        if ($request->hasFile('file_image')) {
            $data['file_image'] = $request->file('file_image');
        }

        if ($request->hasFile('file_video')) {
            $data['file_video'] = $request->file('file_video');
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        if ($request->hasFile('file_youtube')) {
            $data['file_youtube'] = $request->file('file_youtube');
        }
        
        $data['gallery_album_id'] = $albumId;
        $data['image_type'] = $request->image_type ?? null;
        $data['video_type'] = $request->video_type ?? null;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $galleryFile = $this->galleryService->storeFile($data);
        $data['query'] = $request->query();

        if ($galleryFile['success'] == true) {
            return $this->redirectForm($data)->with('success', $galleryFile['message']);
        }

        return redirect()->back()->with('failed', $galleryFile['message']);
    }

    public function storeMultiple(GalleryFileMultipleRequest $request, $albumId)
    {
        $data = $request->all();

        $languages = $this->languageService->getLanguageActive($this->lang);
        foreach ($languages as $key => $value) {
            $data['title_'.$value['iso_codes']] = null;
            $data['description_'.$value['iso_codes']] = null;
        }

        $data['file'] = $request->file('file');
        $data['gallery_album_id'] = $albumId;
        $data['publish'] = 1;
        $data['public'] = 1;
        $data['locked'] = 1;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;

        $galleryFile = $this->galleryService->storeFileMultiple($data);

        return $galleryFile;
    }

    public function edit(Request $request, $albumId, $id)
    {
        $data['album'] = $this->galleryService->getAlbum(['id' => $albumId]);
        if(empty($data['album']))
            return abort(404);

        $data['file'] = $this->galleryService->getFile(['id' => $id]);
        if(empty($data['file']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.galleries.file.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/gallery.file.caption')
            ]),
            'routeBack' => route('gallery.file.index', array_merge(['albumId' => $albumId], $request->query())),
            'breadcrumbs' => [
                __('module/gallery.file.caption') => route('gallery.file.index', ['albumId' => $albumId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(GalleryFileRequest $request, $albumId, $id)
    {
        $data = $request->all();

        if ($request->hasFile('file_image')) {
            $data['file_image'] = $request->file('file_image');
        }

        if ($request->hasFile('file_video')) {
            $data['file_video'] = $request->file('file_video');
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        if ($request->hasFile('file_youtube')) {
            $data['file_youtube'] = $request->file('file_youtube');
        }
        
        $data['gallery_album_id'] = $albumId;
        $data['image_type'] = $request->image_type ?? null;
        $data['video_type'] = $request->video_type ?? null;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $galleryFile = $this->galleryService->updateFile($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($galleryFile['success'] == true) {
            return $this->redirectForm($data)->with('success', $galleryFile['message']);
        }

        return redirect()->back()->with('failed', $galleryFile['message']);
    }

    public function publish($albumId, $id)
    {
        $galleryFile = $this->galleryService->statusFile('publish', ['id' => $id]);

        if ($galleryFile['success'] == true) {
            return back()->with('success', $galleryFile['message']);
        }

        return redirect()->back()->with('failed', $galleryFile['message']);
    }

    public function approved($albumId, $id)
    {
        $galleryFile = $this->galleryService->statusFile('approved', ['id' => $id]);

        if ($galleryFile['success'] == true) {
            return back()->with('success', $galleryFile['message']);
        }

        return redirect()->back()->with('failed', $galleryFile['message']);
    }

    public function position(Request $request, $albumId, $id, $position)
    {
        $galleryFile = $this->galleryService->positionFile(['id' => $id], $position);

        if ($galleryFile['success'] == true) {
            return back()->with('success', $galleryFile['message']);
        }

        return redirect()->back()->with('failed', $galleryFile['message']);
    }

    public function softDelete($albumId, $id)
    {
        $galleryFile = $this->galleryService->trashFile(['id' => $id]);

        return $galleryFile;
    }

    public function permanentDelete(Request $request, $albumId, $id)
    {
        $galleryFile = $this->galleryService->deleteFile($request, ['id' => $id]);

        return $galleryFile;
    }

    public function restore($albumId, $id)
    {
        $galleryFile = $this->galleryService->restoreFile(['id' => $id]);

        if ($galleryFile['success'] == true) {
            return redirect()->back()->with('success', $galleryFile['message']);
        }

        return redirect()->back()->with('failed', $galleryFile['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('gallery.file.index', array_merge(['albumId' => $data['gallery_album_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
