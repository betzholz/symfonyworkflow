<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\MarkingStore\MultipleStateMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

class DefaultController extends Controller {
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction( Request $request ) {
        $em    = $this->getDoctrine()->getManager();
        $posts = $em->getRepository( 'AppBundle:Post' )->findAll();

        return $this->render( 'post/index.html.twig', array (
            'posts' => $posts,
        ) );
    }

    /**
     * @Route("/new", name="newpost")
     */
    public function newpost( Request $request ) {
        $post = new Post();
        $post->setName( uniqid() );
        $workflow = $this->get( 'workflow.blog_publishing' );
        $workflow->can( $post, 'publish' ); // False
        $workflow->can( $post, 'to_review' ); // True
// Update the currentState on the post
        try {
            $workflow->apply( $post, 'to_review' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        //dump($post->getState());
// See all the available transition for the post in the current state
        //$transitions = $workflow->getEnabledTransitions( $post );
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/publish/{id}", name="publishpost")
     */
    public function publischpost( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            $workflow->apply( $post, 'publish' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/checkinfo/{id}", name="checkinfopost")
     */
    public function checkinfopost( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            $workflow->apply( $post, 'checkinfo' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/rejectpublisch/{id}", name="rejectpublischpost")
     */
    public function rejectpublischpost( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            ( $workflow->apply( $post, 'rejectpub' ) );
            //dump( $post->getState() );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        //dump( $post->getState() );
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/rejectpost/{id}", name="rejectpost")
     */
    public function rejectpost( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            $workflow->apply( $post, 'reject' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/checkpubpost/{id}", name="checkpubpost")
     */
    public function checkpubpost( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            $workflow->apply( $post, 'checkpub' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/repost/{id}", name="repost")
     */
    public function repost( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            $workflow->apply( $post, 'repost' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }

    /**
     * @Route("/to_review2/{id}", name="to_review2")
     */
    public function to_review2( Post $post, Request $request ) {
        $workflow = $this->get( 'workflow.blog_publishing' );
        try {
            $workflow->apply( $post, 'to_review2' );
        } catch ( LogicException $e ) {
            // ...
            dump( $e );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist( $post );
        $em->flush( $post );

        return $this->redirectToRoute( 'homepage' );
    }


    /**
     * @Route("/dump", name="dump")
     */
    public function dump( Request $request ) {
        $workflow   = $this->get( 'workflow.blog_publishing' );
        $definition = $workflow->getDefinition();
        $dumper     = new GraphvizDumper();
        $dumper->dump( $definition );
        ob_start();
        echo $dumper->dump( $definition );
        $text = ob_get_contents();
        ob_end_clean();
        file_put_contents( './graph.dot', $text );
        exec( 'dot -Tpng graph.dot -o graph.png' );

        return new JsonResponse( realpath( './graph.dot' ) );
    }


}
