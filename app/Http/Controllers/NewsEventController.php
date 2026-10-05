<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\StoresPublicImages;
use App\Http\Requests\Admin\NewsEventRequest;
use App\Http\Resources\AdminNewsEventResource;
use App\Http\Resources\NewsEventResource;
use App\Models\NewsEvent;
use App\Support\Paginated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

/**
 * News & events: the admin list with its editor panel, and the public events page.
 */
class NewsEventController extends Controller
{
    use StoresPublicImages;

    // The admin list. The editor panel opens on the right for a new or an existing post.
    public function index(): Response
    {
        return $this->adminPage(null);
    }

    public function create(): Response
    {
        return $this->adminPage('new');
    }

    public function store(NewsEventRequest $request): RedirectResponse
    {
        $post = new NewsEvent($request->safe()->only(['title', 'description', 'event_date']));
        $this->saveWithMedia($post, $this->uploads($request));

        return redirect()->route('news-events.index')->with('success', 'Post published. It shows on the public News & events page.');
    }

    // There is no separate admin page for one post; open it in the editor.
    public function show(NewsEvent $newsEvent): RedirectResponse
    {
        return redirect()->route('news-events.edit', $newsEvent);
    }

    public function edit(NewsEvent $newsEvent): Response
    {
        return $this->adminPage($newsEvent);
    }

    public function update(NewsEventRequest $request, NewsEvent $newsEvent): RedirectResponse
    {
        $newsEvent->fill($request->safe()->only(['title', 'description', 'event_date']));

        if (! $request->hasFile('eventimage') && $request->boolean('remove_image')) {
            $newsEvent->eventimage = null;
        }

        $this->saveWithMedia($newsEvent, $this->uploads($request));

        return redirect()->route('news-events.index')->with('success', 'Post updated.');
    }

    public function destroy(NewsEvent $newsEvent): RedirectResponse
    {
        $newsEvent->delete();
        $this->deleteUnusedPublicImages($newsEvent->eventimage);

        return redirect()->route('news-events.index')->with('success', 'Post deleted.');
    }

    // Cards for User Events
    public function index3()
    {
        // Public News & events page, newest first.
        return Inertia::render('Events', [
            'events' => NewsEventResource::collection(
                NewsEvent::orderByDesc('event_date')->orderByDesc('id')->get()
            )->resolve(),
        ]);
    }

    /**
     * The post's image; stored under news/{id}/images/.
     *
     * @return array<string, array{0: ?UploadedFile, 1: string, 2: string}>
     */
    private function uploads(NewsEventRequest $request): array
    {
        return ['eventimage' => [$request->file('eventimage'), 'images', 'image']];
    }

    /**
     * @param  NewsEvent|'new'|null  $editing
     */
    private function adminPage(NewsEvent|string|null $editing): Response
    {
        $posts = NewsEvent::orderByDesc('event_date')->orderByDesc('id')->paginate(15);

        return Inertia::render('Admin/News', [
            'posts' => Paginated::make($posts, AdminNewsEventResource::class),
            'editing' => $editing instanceof NewsEvent ? (new AdminNewsEventResource($editing))->resolve() : $editing,
            'indexUrl' => route('news-events.index'),
            'createUrl' => route('news-events.create'),
            'storeUrl' => route('news-events.store'),
            'publicUrl' => route('news-events.events'),
        ]);
    }
}
