<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Module\ContentRepository;
use App\Repositories\Module\DocumentRepository;
use App\Repositories\Module\EventRepository;
use App\Repositories\Module\GalleryRepository;
use App\Repositories\Module\InquiryRepository;
use App\Repositories\Module\LinkRepository;
use App\Repositories\Module\PageRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ApiModuleController extends Controller
{
    use ApiResponser;

    public function pageList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $pages = App::make(PageRepository::class)->getPageList($filter, false, 5, false, [], []);

        $pageMap = [];
        foreach ($pages as $key => $value) {
            $pageMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('title')
            ];
        }

        return $this->success($pageMap, 'Load page successfully');
    }

    public function contentSectionList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $sections = App::make(ContentRepository::class)->getSectionList($filter, false, 10, false, [], []);

        $sectionMap = [];
        foreach ($sections as $key => $value) {
            $sectionMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($sectionMap, 'Load content section successfully');
    }

    public function contentCategoryList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $categoriees = App::make(ContentRepository::class)->getCategoryList($filter, false, 10, false, [], []);

        $categoryMap = [];
        foreach ($categoriees as $key => $value) {
            $categoryMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($categoryMap, 'Load content category successfully');
    }

    public function contentPostList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $posts = App::make(ContentRepository::class)->getPostList($filter, false, 10, false, [], []);

        $postMap = [];
        foreach ($posts as $key => $value) {
            $postMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('title')
            ];
        }

        return $this->success($postMap, 'Load content post successfully');
    }

    public function galleryCategoryList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $categories = App::make(GalleryRepository::class)->getCategoryList($filter, false, 10, false, [], []);

        $categoryMap = [];
        foreach ($categories as $key => $value) {
            $categoryMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($categoryMap, 'Load gallery category successfully');
    }

    public function galleryAlbumList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $albums = App::make(GalleryRepository::class)->getAlbumList($filter, false, 10, false, [], []);

        $albumMap = [];
        foreach ($albums as $key => $value) {
            $albumMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($albumMap, 'Load gallery album successfully');
    }

    public function documentList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $documents = App::make(DocumentRepository::class)->getDocumentList($filter, false, 10, false, [], []);

        $documentMap = [];
        foreach ($documents as $key => $value) {
            $documentMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($documentMap, 'Load document successfully');
    }

    public function linkList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $links = App::make(LinkRepository::class)->getLinkList($filter, false, 10, false, [], []);

        $linkMap = [];
        foreach ($links as $key => $value) {
            $linkMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($linkMap, 'Load link successfully');
    }

    public function inquiryList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $inquiries = App::make(InquiryRepository::class)->getInquiryList($filter, false, 10, false, [], []);

        $inquiryMap = [];
        foreach ($inquiries as $key => $value) {
            $inquiryMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($inquiryMap, 'Load inquiry successfully');
    }

    public function eventList(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }

        $events = App::make(EventRepository::class)->getEventList($filter, false, 10, false, [], []);

        $eventMap = [];
        foreach ($events as $key => $value) {
            $eventMap[$key] = [
                'id' => $value['id'],
                'title' => $value->fieldLang('name')
            ];
        }

        return $this->success($eventMap, 'Load event successfully');
    }
}
