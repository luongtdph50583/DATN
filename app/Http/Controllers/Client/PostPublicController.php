<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Post;
use App\Models\ClubMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostPublicController extends Controller
{
    //

    public function index(Request $request)
    {
        // 1️⃣ Lấy danh sách CLB có bài viết (public hoặc internal)
        $clubIds = Post::where('status', 'approved')
            ->whereIn('visibility', ['public', 'internal'])
            ->groupBy('club_id')
            ->pluck('club_id')
            ->toArray();

        if (empty($clubIds)) {
            return view('client.pages.post_public.index', [
                'posts' => collect([]),
                'selectedClub' => null,
                'recentPosts' => collect([]),
                'featuredPosts' => collect([]),
            ]);
        }

        // 2️⃣ Random thứ tự club
        $randomOrder = collect($clubIds)->shuffle();

        $selectedClub = null;
        $posts = collect([]);

        // ✔ 3️⃣ LẤY MEMBER ID TỪ USER
        $memberId = auth()->check() ? optional(auth()->user()->member)->id : null;

        foreach ($randomOrder as $clubId) {

            // 4️⃣ CHECK user có thuộc CLB không
            $canSeeAllPosts = false;

            if ($memberId) {
                $canSeeAllPosts = ClubMember::where('club_id', $clubId)
                    ->where('member_id', $memberId)
                    ->exists();   // ❗ Không check status để tránh lỗi
            }

            // 5️⃣ Query bài viết
            $query = Post::with('club')
                ->where('club_id', $clubId)
                ->where('status', 'approved');

            if (!$canSeeAllPosts) {
                $query->where('visibility', 'public');
            }

            if ($request->featured == '1') {
                $query->where('is_featured', 1);
            }

            $posts = $query->latest()->paginate(4);

            if ($posts->total() > 0) {
                $selectedClub = Club::find($clubId);
                break;
            }
        }

        if (!$selectedClub) {
            return view('client.pages.post_public.index', [
                'posts' => collect([]),
                'selectedClub' => null,
                'recentPosts' => collect([]),
                'featuredPosts' => collect([]),
            ]);
        }

        // 6️⃣ Recent Posts
        $recentQuery = Post::where('club_id', $selectedClub->id)
            ->where('status', 'approved')
            ->latest()
            ->limit(5);

        if (!$canSeeAllPosts) {
            $recentQuery->where('visibility', 'public');
        }

        $recentPosts = $recentQuery->get();

        // 7️⃣ Featured Posts
        $featuredQuery = Post::where('club_id', $selectedClub->id)
            ->where('status', 'approved')
            ->where('is_featured', 1)
            ->latest()
            ->limit(5);

        if (!$canSeeAllPosts) {
            $featuredQuery->where('visibility', 'public');
        }

        $featuredPosts = $featuredQuery->get();

        return view('client.pages.post_public.index', [
            'posts' => $posts,
            'selectedClub' => $selectedClub,
            'recentPosts' => $recentPosts,
            'featuredPosts' => $featuredPosts,
        ]);
    }







    // GET: /postpublic/club/{club}
    // GET: /postpublic/club/{club}
    public function byClub(Request $request, Club $club)
    {
        // ✔ LẤY MEMBER ID TỪ USER
        $memberId = auth()->check() ? optional(auth()->user()->member)->id : null;

        // CHECK user có thuộc CLB không
        $canSeeAllPosts = false;
        if ($memberId) {
            $canSeeAllPosts = ClubMember::where('club_id', $club->id)
                ->where('member_id', $memberId)
                ->exists();
        }

        // Query bài viết chính
        $query = Post::with('club')
            ->where('club_id', $club->id)
            ->where('status', 'approved');

        if (!$canSeeAllPosts) {
            $query->where('visibility', 'public');
        }

        if ($request->featured == '1') {
            $query->where('is_featured', 1);
        }

        $posts = $query->latest()->paginate(5);

        // Recent Posts
        $recentQuery = Post::where('club_id', $club->id)
            ->where('status', 'approved')
            ->latest()
            ->limit(5);

        if (!$canSeeAllPosts) {
            $recentQuery->where('visibility', 'public');
        }
        $recentPosts = $recentQuery->get();

        // Featured Posts
        $featuredQuery = Post::where('club_id', $club->id)
            ->where('status', 'approved')
            ->where('is_featured', 1)
            ->latest()
            ->limit(5);

        if (!$canSeeAllPosts) {
            $featuredQuery->where('visibility', 'public');
        }
        $featuredPosts = $featuredQuery->get();

        return view('client.pages.post_public.index', [
            'posts' => $posts,
            'selectedClub' => $club,
            'recentPosts' => $recentPosts,
            'featuredPosts' => $featuredPosts,
        ]);
    }


    // GET: /postpublic/{post}
    public function show(Post $post)
    {
        // Tăng lượt xem
        $post->increment('views');

        // Lấy club của bài viết
        $club = $post->club;

        $memberId = auth()->check() ? optional(auth()->user()->member)->id : null;
        $canSeeAllPosts = $memberId
            ? ClubMember::where('club_id', $club->id)->where('member_id', $memberId)->exists()
            : false;

        // Recent posts
        $recentQuery = Post::where('club_id', $club->id)
            ->where('status', 'approved')
            ->latest()
            ->limit(5);
        if (!$canSeeAllPosts) {
            $recentQuery->where('visibility', 'public');
        }
        $recentPosts = $recentQuery->get();

        // Featured posts
        $featuredQuery = Post::where('club_id', $club->id)
            ->where('status', 'approved')
            ->where('is_featured', 1)
            ->latest()
            ->limit(5);
        if (!$canSeeAllPosts) {
            $featuredQuery->where('visibility', 'public');
        }
        $featuredPosts = $featuredQuery->get();

        // Thống kê CLB
        $clubStats = [
            'total_posts' => Post::where('club_id', $club->id)->count(),
        ];

        // Comments của bài viết
        $comments = $post->comments()
            ->whereNull('deleted_at')
            ->where('status', 'visible')
            ->latest()
            ->get();

        return view('client.pages.post_public.show', [
            'post' => $post,
            'selectedClub' => $club,
            'recentPosts' => $recentPosts,
            'featuredPosts' => $featuredPosts,
            'clubStats' => $clubStats,
            'comments' => $comments,
        ]);
    }


}
