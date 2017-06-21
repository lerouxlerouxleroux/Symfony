<?php
 
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//classes servant à la sérialisation (passage d'objet à la chaine de caractères)
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Player;


//2ieme étape d'ajout de joueur, contient toutes les propriétés et methodes de Player


class PlayerController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */

    // la syntaxe Request $request équivaut à $request = new Request()
    public function indexAction(Request $request) // cette methode se declencher quand le client fait get url entréee, requete http ver$Route, puis return,  
    {
        // replace this example code with whatever you need
        $title = 'Liste des joueurs';
        $joueur1 = ['nom' => 'Bonnuci', 'prenom' => 'Leo', 'age' => 29];
        $joueur2 = ['nom' => 'Chiellini', 'prenom' => 'Giorgio', 'age' => 34];
        $joueur3 = ['nom' => 'Barzagli', 'prenom' => 'Andrea', 'age' => 36];
        
        $joueurs = [$joueur1, $joueur2, $joueur3];// tab dans tab qu'on onverra à la vu pour y faire l'itération

       
        //chargement des joueurs depuis la bdd
        // Récupération du réposit
        //$repository est un instrument, un objet permettant de récupérer les données
        //il propose de nombreuses méthodes de récupération de données (exemple: findAll(: FindById(), etc.)
        //uniquement pour les opérations en lecture, autrement, pour les autres opérations du CRUD on se sert de Manager
        


        /*$repository = $this
                            ->getDoctrine()
                            ->getManager()
                            ->getRepository('AppBundle:Player');*/

        //$players = $repository->findAll(); //fournit par framework, renvoie les lignes de Player
       

        $em = $this->getDoctrine()->getManager(); // a partir de ceci on peut charger d'autres class et les fonctions que Repository met en disposition
        $playerRepo = $em->getRepository('AppBundle:Player');

      // listing peut être appelé avec ou sans argument
        //Sans argument, la valeur par défaut (100) sera appliquée
        $players = $playerRepo->listing();

        //$players = $playerRepo->listing($age = 30);
        

           
            /*$query = $em->createQuery('
            SELECT p, t.nom 
            FROM AppBundle:Player p
            LEFT JOIN AppBundle:Team t
            WHERE p.equipe = t.id
            '); */ //A COMPLETER LE NOM DE L'EQUIPE

           // $players = $query->getResult();
            //var_dump($players); //pas d'équipe

       

        return $this->render('player/index.html.twig', array(
            'title'     => $title, 
            'message'   => 'Symfony semble formidable',
            'joueur1'   => $joueur1, // associer la clé à la variable
            'joueurs'   => $joueurs,
            'players'   => $players
            
        ));
    }


/**
     * @Route("/json/players")
     */
public function jsonIndexAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();
    $playerRepo = $em->getRepository('AppBundle:Player');
    $players = $playerRepo->listing();

    //Impératif: encoder le tableau d'objets Player
    // en json
    $encoders = array(new JsonEncoder());
    $normalizers = array(new ObjectNormalizer());
    $serializer = new Serializer($normalizers, $encoders);

    $jsonPlayers = $serializer->serialize($players, 'json');

    // test (étape du début)
    //$t = ["nom" => "toto", "age" => 99]; // il faut mettre en json
    $res = new Response();
    

    //on autorise les requêtes provenant d'une origine différente (cross-domain).
    //Ici, symfony "tourne" sur le port 8000, on autorise le traitement de requêtes
    // provenant du port 80 (localhost:80)
    $res->headers->set('Access-Control-Allow-Origin', 'http://localhost');
    // json_encode fonctions sur les tableaux associatifs mais ne parviennent pas 
    // à encoder correctement un objet
    $res->setContent($jsonPlayers);

    return $res;
}



    /**
     * @Route("/test/player/add", name="testaddplayer")
     */

    // la syntaxe Request $request équivaut à $request = new Request()

    public function testaddAction(Request $request) //request capté les données entrantes, en url 
    
    {
        $player = new Player(); //étape 1// nouvel objet de type player
        $player->setNom("Diego Armando");//3ieme étape
        $player->setPrenom("Maradona");// set car c'est la methode privé
        $player->setAge("54");
        $player->setNumMaillot(10);
  

        $em = $this->getDoctrine()->getManager(); //4ieme etape getD renvoie un objet qui dispose de sa propre methode (getManager permettant d'interagir avec la bdd mettre à jour, supp, etc)

        // étape1a: on "persiste", c.t.d enregistre les données en bdd
        $em->persist($player);

        // étape 2b : nettoyage
        $em->flush();

        // on doit retourner une réponse HTPP au client en faisant return
        return new Response('joueur ajouté avec succès'); // Reponse au @ROOT, puis on va sur le navigateur et aprs localhost:8000 on fait/player/add
    }
    
    /**
     * @Route("/player/add", name="addplayer")
     */
    public function addAction(Request $request) // on va retourner au client le formulaire
    {   
        //déterminer si cette route a été demandée en POST ou en GET
        if ($request->isMethod('POST')) {
            $player = new Player();
            $player->setNom($request->get('nom'));//3ieme étape
            $player->setPrenom($request->get('prenom'));// set car c'est la methode privé
            $player->setAge($request->get('age'));
            $player->setNumMaillot($request->get('num_maillot'));
            $player->setEquipe($request->get('equipe'));

            $em = $this->getDoctrine()->getManager();  
            $em->persist($player);
            $em->flush();

            //redirection vers la page d'accueil
            return $this->redirectToRoute('homepage'); // homepage c'est name dans Route de la page d'accueil, après l'enregistrement on arrive sur cette page (par methode get)



        } else {
            //obtenir la liste des équipes pour la transmettre au template
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('AppBundle:Team');
            $teams = $repo->findAll();


            // si la route est demandé en GET, on renvoie un formmulaire
            return $this->render('player/forms/add.html.twig', array(
                'teams' => $teams
           
         )); //click sur la page ajouter un jouer, et c'est cette ligne, methode post
        }

        
    }

    


    /**
     * @Route("/player/{id}", name="detail_player")
     */
    public function detailAction($id) 
    {
    
    $em = $this
            ->getDoctrine() //ORM Doctrine renvoie l'objet Manager, 
            ->getManager(); //Manager (outil pour opération en écriture), add, insertion, modifications et de la suppresion
              
              $playerRepo = $em->getRepository('AppBundle:Player'); // en argument on indique la classe que l'on veut gérer, et bundle, outil   pour opération e lecture
             // $teamRepo = $em->getRepository('AppBundle:Team'); 

              // En l'abscence de relation OneToOne spécifiée au niveau de la classe Player, il faut manuellement récupérer les données de l'équipe en fonction de l'identification du joueur.
              // Sinon, si la relation OneToOne est définie, symfony se charge des jointures; de l'instanciation, de l'hydratation, etc.
        // récupération de l'id
    //$id = $request->query->get('id');
    //var_dump($id);
        


        // trouver le joueur correspondant en base de données
    $player     = $playerRepo->find($id); // cherche par colonne id// find() == findById()
    
   /* $teamId     = $player->getEquipe();

    if ($teamId != 0) 

    {
   
    $teamName   = $teamRepo->find($teamId)->getNom;
    
    } else {
    
    $teamName= 'Sans équipe';
    
    }*/





        // afficher les information via une vue/template (fichier twig)
    // render () associe une vue (fichier.twig) passé en premier argument avec un tableau associatif passé en dexième argument
    // Les données que le controller fournit à la vue seront accessible (affichables, itérables, etc) par cette dernière
    return $this->render('player/detail.html.twig', array(
        'player' => $player
        //'teamName' =>$teamName
        ));
           
    }

 
 


 }

    
