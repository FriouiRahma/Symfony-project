<?php

namespace App\Controller;
use App\Entity\Comment;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
     /**
     * @Route("/addmv", name="add_movie")
     */
    public function addednewmovie(){
        if ($this->getUser()){ //user connecté
         $iduser = $this->getUser()->getId();
        }else{ //user non connecté
           return $this->redirect("/");
        }
       
       
    return $this->render('movies/addmovie.html.twig',['iduser'=>$iduser]);
 }

    /**
     * @Route("/list", name="movies_list")
     */
    public function getallmovie(){


        $repmovie = $this->getDoctrine()->getRepository(Movie::class);
        $allmovies = $repmovie->findAll();
        
        //var_dump($allmovies);

        /*foreach($allmovies as $movie){
            echo "New Movie";
            echo "<br> Title = ".$movie->getTitle();
            echo "<br>";
        }*/
        //return new Response('');

        return $this->render('movies/showliste.html.twig', ['movies' => $allmovies]);

    }
    /**
     * @Route("/addmovie", name="add_new_movie")
     */
    public function addmovie(Request $request){
        // recuperation data from form
        if ($request->ismethod('post')){
            // verifier la methode post du formulaire
            echo "method = POST";

        }
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $poster = "trvrv";
        $datesortie = $request->request->get('datesortie');
        $creator =$request->request->get('creator');
        $created_at = date("Y-m-d h:i:s");
        $modified_at = date('Y-m-d h:i:s');
        
        // ADD DATA TO DATABASE

        $em = $this->getDoctrine()->getManager();

        $movie = new Movie();
        $movie->setTitle($title);
        $movie->setDescription($description);
        $movie->setPoster($poster);
        $movie->setCreator(1);
        $movie->setCreatedAt(new \DateTime($created_at));
        $movie->setModifiedAt(new \DateTime($modified_at));
        $movie->setDatesortie(new \DateTime($datesortie));

        // tells Doctrine you want to (eventually) save the Movie (no queries yet)
        $em->persist($movie);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        //return new Response('Saved new movie with id '.$movie->getId());
        return $this->redirect("/");
         

    }
     /**
     * @Route("/show/{id}", name="show_single_movie")
     */
    public function showSingleMovie($id){

        $repcomment= $this->getDoctrine()->getRepository(Comment::class);
        $commentaires = $repcomment->findByIdmovie($id);
        //var_dump($commentaires);
        $repmovie = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repmovie->find($id);
        //var_dump($movie);
        return $this->render('movies/singlemovie.html.twig',[ 'movie'=> $movie , 'commentaires' => $commentaires  ]);
    }
      /**
     * @Route("/addcomment", name="add_comment")
     */
    public function addcomment(Request $request ){

        // recuperation data from form
        if ($request->ismethod('post')){

            $idreviewer = 1;
            $idmovie = $request->request->get('idmovie');
            $contenu = $request->request->get('contenu');
            $created_at = date("Y-m-d h:i:s");
            $modified_at = date('Y-m-d h:i:s');
            $em = $this->getDoctrine()->getManager();
            $comment = new Comment();
            $comment->setIdreviewer($idreviewer);
            $comment->setIdmovie($idmovie);
            $comment->setContenu($contenu);
            $comment->setCreatedAt(new \DateTime($created_at));
            $comment->setModifiedAt(new \DateTime($modified_at));


            // tells Doctrine you want to (eventually) save the Movie (no queries yet)
            $em->persist($comment);

        // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect("/show/".$idmovie);
        
        
        
        }
        

    }

      /**
     * @Route("/user/movieslist/{iduser}", name="user_movieslist")
     */

public function MoviesUserlist($iduser){

     $repmovies= $this->getDoctrine()->getRepository(Movie::class);
     $allmovies = $repmovies->findBy(array('creator'=>$iduser));

    return $this->render('user/movieslist.html.twig',["movies"=>$allmovies]);

}




}
