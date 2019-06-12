<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Entity\User;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(PostRepository $repo)
    {
        return $this->render('post/index.html.twig', [
            'posts' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/post/{id}/like", name="post_like")
     * @param Post $post
     * @param ObjectManager $manager
     * @param PostLikeRepository $likeRepo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function like(Post $post, ObjectManager $manager, PostLikeRepository $likeRepo){

        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403 ,
            'message' => 'Unauthorized'],
            403);

        if ($post->isLikedByUser($user)){

            $like = $likeRepo->findOneBy([
               'post' => $post,
                'user' => $user
            ]);


            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'like bien supprimé',
                'likes' => $likeRepo->count(['post' => $post])
            ], 200 );
        }


        $like = new PostLike();

        $like->setPost($post);
        $like->getUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200 ,
            'message' => 'ca marche super bien !'],
            200);
    }

}
