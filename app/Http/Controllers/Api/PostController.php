<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Transformers\PostTransformer;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->response->paginator(Post::isNotDraft()
            ->orderBy('created_at', 'desc')
            ->select('title', 'id', 'slug', 'excerpt', 'banner', 'user_id', 'category_id', 'created_at')
            ->paginate(), new PostTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $where = is_numeric($id) ? ['id' => $id] : ['slug' => $id];
        $post  = Post::isNotDraft()->where($where)->firstOrFail();

        $post->increment('view_count');

        return $this->response->item($post, new PostTransformer);
    }

    /**
     * 测试, 后续改为 7 天内热门
     *
     * @param  Request $request
     * @return
     */
    public function recommend(Request $request)
    {
        $posts = Post::isNotDraft()
            ->orderBy('view_count', 'desc')
            ->select('title', 'id', 'slug')
            ->limit(10)->get();

        return $this->response->collection($posts, new PostTransformer);
    }

    /**
     * Search post
     *
     * @param  Request $request
     * @return [type]
     */
    public function search(Request $request)
    {
        $result = [];

        if (! empty($keywork = $request->q)) {
            $result = Post::search($keywork)->paginate();

            $data = ($result->map(function ($post) {
                return $post->setAttribute('highlight', $post->highlight)
                    ->setVisible(['id', 'title', 'slug', 'created_at', 'excerpt', 'highlight']);
            }));

            $result->setCollection($data);
        }

        return response()->json($result);
    }
}
