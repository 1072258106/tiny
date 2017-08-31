<?php

namespace App\Http\Controllers\Backend\Api;


use App\Http\Controllers\ApiController;
use App\Http\Requests\Backend\PostCreateRequest;
use App\Http\Requests\Backend\PostUpdateRequest;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Transformers\Backend\PostTransformer;
use Illuminate\Http\Request;
use Auth;

class PostsController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 获取文章列表
     * @param Request $request
     * @return \App\Support\TransformerResponse
     */
    public function index(Request $request)
    {
        $posts = Post::applyFilter($request)
            ->paginate($this->perPage());
        return $this->response()->paginator($posts, new PostTransformer());
    }

    public function store(PostCreateRequest $request, PostRepository $postRepository)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $postRepository->create($data);
        return $this->response()->noContent();
    }

    public function update(Post $post, PostUpdateRequest $request, PostRepository $postRepository)
    {
        $postRepository->update($request->validated(), $post);
        return $this->response()->noContent();
    }

    public function show(Post $post)
    {
        return $this->response()->item($post, new PostTransformer());
    }

    /**
     * 软删除
     * @param Post $post
     * @return \App\Support\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $this->response()->noContent();
    }

    /**
     * 真删除
     * @param $postId
     * @return \App\Support\Response
     */
    public function realDestroy($postId)
    {
        Post::where('id', $postId)->forceDelete();
        return $this->response()->noContent();
    }


    /**
     * 恢复软删除
     * @param $postId
     * @return mixed
     */
    public function restore($postId)
    {
        Post::withTrashed()->where('id', $postId)->restore();
        return $this->response()->noContent();
    }

    public function templates()
    {
        return config('tiny.templates');
    }
}