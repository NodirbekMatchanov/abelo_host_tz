<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pagination;
use App\Models\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

final class PostService
{
    public function __construct(
        private readonly PostRepository     $postRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    /** @return array{posts: Post[], pagination: Pagination} */
    public function getHomePagePosts(string $sort = 'date', int $page = 1): array
    {
        $perPage    = 10;
        $total      = $this->postRepository->countAll();
        $pagination = new Pagination($total, $perPage, $page);

        $posts = $this->postRepository->findAll($sort, $perPage, $pagination->offset);

        return ['posts' => $posts, 'pagination' => $pagination];
    }

    public function getPostDetail(int $id): ?Post
    {
        $post = $this->postRepository->findById($id);

        if ($post === null) {
            return null;
        }

        $this->postRepository->incrementViews($id);

        $categories = $this->categoryRepository->findByPostId($id);

        return new Post(
            id:          $post->id,
            title:       $post->title,
            description: $post->description,
            content:     $post->content,
            viewsCount:  $post->viewsCount + 1,
            createdAt:   $post->createdAt,
            image:       $post->image,
            categories:  $categories,
        );
    }
}
