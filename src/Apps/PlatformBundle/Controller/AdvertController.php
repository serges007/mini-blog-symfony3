<?php
namespace Apps\PlatformBundle\Controller;

use Apps\PlatformBundle\Entity\Advert;
use Apps\PlatformBundle\Form\AdvertEditType;
use Apps\PlatformBundle\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Description of AdvertController
 *
 * @author mbele
 */
class AdvertController extends Controller{
    public function indexAction($page) {
        if ($page < 1) {
          throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // Ici je fixe le nombre d'annonces par page à 3
        // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
        $nbPerPage = 4;

        // On récupère notre objet Paginator
        $listAdverts = $this->getDoctrine()
          ->getManager()
          ->getRepository('AppsPlatformBundle:Advert')
          ->getAdverts($page, $nbPerPage)
        ;

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
          throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }

        // On donne toutes les informations nécessaires à la vue
        return $this->render('AppsPlatformBundle:Advert:index.html.twig', array(
          'listAdverts' => $listAdverts,
          'nbPages'     => $nbPages,
          'page'        => $page,
        ));
    }

    public function viewAction($id) {
        $em = $this->getDoctrine()->getManager();

        // Pour récupérer une seule annonce, on utilise la méthode find($id)
        $advert = $em->getRepository('AppsPlatformBundle:Advert')->find($id);

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id n'existe pas, d'où ce if :
        if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // Récupération de la liste des candidatures de l'annonce
        $listApplications = $em
          ->getRepository('AppsPlatformBundle:Application')
          ->findBy(array('advert' => $advert))
        ;

        // Récupération des AdvertSkill de l'annonce
        $listAdvertSkills = $em
          ->getRepository('AppsPlatformBundle:AdvertSkill')
          ->findBy(array('advert' => $advert))
        ;

        return $this->render('AppsPlatformBundle:Advert:view.html.twig', array(
          'advert'           => $advert,
          'listApplications' => $listApplications,
          'listAdvertSkills' => $listAdvertSkills,
        ));
    }

    public function addAction(Request $request){
        $advert = new Advert();
        $form   = $this->get('form.factory')->create(AdvertType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('apps_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('AppsPlatformBundle:Advert:add.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request){
      $em = $this->getDoctrine()->getManager();

      $advert = $em->getRepository('AppsPlatformBundle:Advert')->find($id);

      if (null === $advert) {
        throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }

      $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        // Inutile de persister ici, Doctrine connait déjà notre annonce
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

        return $this->redirectToRoute('apps_platform_view', array('id' => $advert->getId()));
      }

      return $this->render('AppsPlatformBundle:Advert:edit.html.twig', array(
        'advert' => $advert,
        'form'   => $form->createView(),
      ));
    }

    public function deleteAction(Request $request, $id) {
      $em = $this->getDoctrine()->getManager();

      $advert = $em->getRepository('AppsPlatformBundle:Advert')->find($id);

      if (null === $advert) {
        throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }

      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->get('form.factory')->create();

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $em->remove($advert);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

        return $this->redirectToRoute('apps_platform_home');
      }

      return $this->render('AppsPlatformBundle:Advert:delete.html.twig', array(
        'advert' => $advert,
        'form'   => $form->createView(),
      ));
    }


//  public function menuAction($limit)
//  {
//    $em = $this->getDoctrine()->getManager();
//
//    $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
//      array(),                 // Pas de critère
//      array('date' => 'desc'), // On trie par date décroissante
//      $limit,                  // On sélectionne $limit annonces
//      0                        // À partir du premier
//    );
//
//    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
//      'listAdverts' => $listAdverts
//    ));
//  }
    
    public function menuAction($limit){
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository('AppsPlatformBundle:Advert')->findBy(
            array(),                 // Pas de critère
            array('date' => 'desc'), // On trie par date décroissante
            $limit,                  // On sélectionne $limit annonces
            0                        // À partir du premier
        );

        $categories = $em->getRepository('AppsPlatformBundle:Category')->findBy(
            array(),                 // Pas de critère
            array(), // On trie par date décroissante    
            $limit,                  // On sélectionne $limit annonces
            1                        // À partir du premier                        
        );
        $tags = $em->getRepository('AppsPlatformBundle:Skill')->findBy(
            array(),                 // Pas de critère
            array(), // On trie par date décroissante    
            10,                  // On sélectionne $limit annonces
            1                        // À partir du premier                        
        );
        return $this->render('AppsPlatformBundle:Advert:menu.html.twig', array(
          // Tout l'intérêt est ici : le contrôleur passe
          // les variables nécessaires au template !
          'listAdverts' => $listAdverts,
          'categories'  => $categories,
          'tags'        => $tags
        ));
    }    
    
    // Méthode facultative pour tester la purge
    public function purgeAction($days, Request $request){
      // On récupère notre service
      $purger = $this->get('apps_platform.purger.advert');

      // On purge les annonces
      $purger->purge($days);

      // On ajoute un message flash arbitraire
      $request->getSession()->getFlashBag()->add('info', 'Les annonces plus vieilles que '.$days.' jours ont été purgées.');

      // On redirige vers la page d'accueil
      return $this->redirectToRoute('apps_platform_home');
    }    
    
    public function testAction() {
        $advert = new Advert();
        
        $advert->setDate(new \Datetime());
        $advert->setTitle('abc');
        $advert->setAuthor('A');
        //on recupere le service validator
        $validator = $this->get('validator');
        $listErrors = $validator->validate($advert);
        // Si $listErrors n'est pas vide, on affiche les erreurs
        if(count($listErrors) > 0) {
            // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
            return new Response((string) $listErrors);
        } else {
            return new Response("L'annonce est valide !");
        }
    }
}
