<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Contact;
use App\Data\SearchData;
use App\Form\AccountType;
use App\Form\ContactType;
use App\Form\ResetPassType;
use App\Entity\PasswordUpdate;
use App\Form\EditPasswordType;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Security\ChangePassword;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Notification\ContactNotification;
use App\Repository\PurchaseOrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Notification\ActivationAccountNotification;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{

    /**
     * @var ActivationAccountNotification
     */
    private $notify_activation;

    public function __construct(ActivationAccountNotification $notify_activation)
    {

        $this->notify_activation = $notify_activation;
    }


    /**
     * @Route("/profil/edition_profil", name="user_profil")  
     * @IsGranted("ROLE_ANONYMOUS")  
     * @return Response
     */

    public function editProfil(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($user);

            // Execute la requête modification en base de donnés
            $entityManager->flush();


            $this->addFlash(
                'success',
                "Les données du profil ont été enregistrée avec succès !"
            );
        }


        return $this->render('security/profil.html.twig', [
            'form' => $form->createView(),
            'user' => $user

        ]);
    }


    /**
     * @Route("/profil/liste_commande", name="purchase_list")
     *  @IsGranted("ROLE_USER")  
     */

    public function purchaseUserList(PurchaseOrderRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $purchaseOrders = $repository->findSearch($data);

        $user = $this->getUser();

        return $this->render('security/purchase_list.html.twig', [
            'purchaseOrders' => $purchaseOrders,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            //On Genere le token d'activation
            $user->setActivationToken(md5(uniqid()));


            $manager->persist($user);
            $user->setRoles(['ROLE_ANONYMOUS']);
            $manager->flush();

            // On envoie le mail
            $this->notify_activation->notify($user);
            $this->addFlash('success', 'Merci pour votre inscriptions ! Un mail de verification à été envoyé à votre boite mail');

            return $this->redirectToRoute('security_login');
        }


        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()

        ]);
    }




    /**
     * @Route("/inscription/mail", name="security_mail_token")
     */
    public function registrationToken(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        

        $activation_token = $user->getActivationToken();


        if ($activation_token  == null) {


            $this->addFlash('danger', 'Un problème est survenue lors de l\'operation si cela persiste veuillez contacter un administrateur');
            return $this->redirectToRoute('user_profil');
        } else {


            $user->setActivationToken(null);
            //On Genere le token d'activation
            $user->setActivationToken(md5(uniqid()));


            $manager->persist($user);
            $manager->flush();

            // On envoie le mail
            $this->notify_activation->notify($user);
            $this->addFlash('success', ' Un nouveau mail de verification à été envoyé à votre boite mail');

            return $this->redirectToRoute('security_login');
        }


        return $this->redirectToRoute('security_login');
    }


    /**
     * @Route ("/activation/{token}" , name="activation")
     */
    public function activation($token, UserRepository $userRepo)
    {
        // On vérifie si un utilisateur a ce token
        $user = $userRepo->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'existe avec ce token
        if (!$user) {
            //Erreur 404
            throw $this->createNotFoundException('Cet Utilisateur n\'existe pas');
        }

        $em = $this->getDoctrine()->getManager();
        // On supprime le token
        $user->setActivationToken(null);
        $user->setRoles([null]);
        $user->setRoles(['ROLE_USER']);
        $user->setInscriptionDate(new \DateTime('now'));


        $em->persist($user);
        $em->flush();

        // On envoie un message flash
        // $this->addFlash('success', 'Vous avez bien activé votre compte');


        return $this->render('security/activation_confirmed.html.twig');
    }

    /**
     * @Route("/connexion", name ="security_login")
     * 
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/admin_pannel.html.twig');
        }

        return $this->render('security/login.html.twig', [

            'error'         => $error,
        ]);
    }


    /**
     * @Route("/delete_user/{id}" , name="delete_user")
     * 
     */


    public function deleteUser(UserRepository $user, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $em->getRepository(User::class);
        
        // dd($this->getUser()->getId(), $id);
        if ($this->getUser()->getId() == $id) {
            $em = $this->getDoctrine()->getManager();
            
            $reviewRepository = $em->getRepository(Review::class);
    
            $reviews = $reviewRepository->findBy(['user' => $this->getUser()]);
            foreach ($reviews as $review) {
    
                $review->setUser(null);
                // dd($reviews);
            }
            
           
           
            $user =  $this->getUser();
            $user->setEmail('profil supprimé');
            $user->setRoles(null);
            $user->setRoles('profil supprimé');
            $em->persist($user);
            $em->flush();
            $this->container->get('security.token_storage')->setToken(null);

            $session = new Session();

            $this->addFlash('success', 'Votre compte utilisateur a bien été supprimé !');
            $session->invalidate(0);
            return $this->redirectToRoute('security_logout');
        }
        throw $this->createNotFoundException('Oula vous vous êtes perdu ?');
    }

    /**
     * @Route("/mot_de_passe_oublié", name="forgotten_password")
     */
    public function forgottenPassword(Request $request, UserRepository $usersRepo, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // On crée le formulaire
        $form = $this->createForm(ResetPassType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche si un utilisateur a cet email
            $user = $usersRepo->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if (!$user) {
                // On envoie un message flash
                $this->addFlash('danger', 'Cette adresse n\'existe pas');

                return $this->redirectToRoute('forgotten_password');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('forgotten_password');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            // On envoie le message
            $message = (new \Swift_Message('Réinitialisation de mot de passe'))
                ->setFrom('votre@adresse.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    "<p>Bonjour,</p><p>Une demande de réinitialisation de mot de passe a été effectuée pour le site GAMEIFY Veuillez cliquer sur le lien suivant : " . $url . '</p>',
                    'text/html'
                );

            // On envoie l'e-mail
            $mailer->send($message);

            // On crée le message flash
            $this->addFlash('success', 'Un e-mail de réinitialisation de mot de passe vous a été envoyé');

            return $this->redirectToRoute('security_login');
        }

        // On envoie vers la page de demande de l'e-mail
        return $this->render('security/forgotten_password.html.twig', ['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/reinitialisation_mot_de_passe/{token}", name="reset_password")
     */
    public function resetPass($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On cherche l'utilisateur avec le token fourni
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByResetToken($token);

        if (!$user) {
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('forgotten_password');
        }

        // Si le formulaire est envoyé en méthode POST
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setResetToken(null);

            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès');

            return $this->redirectToRoute('security_login');
        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }

    /**
     * @Route("/profil/modification_de_mot_passe", name="edit_password")
     *  @IsGranted("ROLE_USER")
     */

    public function editPassword(Request $request,  UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)

    {

        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($password);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('accueil');
            }
        }

        return $this->render('security/edit_password.html.twig', array(

            'form' => $form->createView(),

        ));
    }





    /**
     * @Route("/deconnexion", name ="security_logout")
     */
    public function logout()
    {

        return $this->render('accueil/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, ContactNotification $notification): Response
    {

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success','Votre email a bien été envoyé, un administrateur va vous répondre au plus vite');
            return $this->redirectToRoute('accueil');
        } else {
            // $this->addFlash('danger','Une erreur est survenue');
        }



        return $this->render('contact/contact.html.twig',[
            'ContactForm'=>$form->createView()
        ]);
    }
}
