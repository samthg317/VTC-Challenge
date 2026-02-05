<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as PHI;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if ($request->isMethod('POST')) {
            $email = (string) $request->request->get('email');
            $plain = (string) $request->request->get('password');

            if (!$email || !$plain) {
                return $this->render('security/register.html.twig', ['error' => 'Email and password required']);
            }

            $user = new User();
            $user->setEmail($email);
            $hashed = $passwordHasher->hashPassword($user, $plain);
            $user->setPassword($hashed);
            $user->setIsVerified(false);
            $token = bin2hex(random_bytes(16));
            $user->setConfirmationToken($token);

            $em->persist($user);
            try {
                $em->flush();
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                // Duplicate email -> show friendly message and log details
                $logger->warning('Duplicate user registration attempt', ['email' => $email, 'exception' => $e]);
                $msg = 'Email is already registered. Did you forget your password?';
                return $this->render('security/register.html.twig', ['error' => $msg]);
            } catch (\Exception $e) {
                // Generic DB / other error -> log and show friendly message
                $logger->error('Error while registering user', ['email' => $email, 'exception' => $e]);
                $msg = 'An error occurred while creating your account. Please try again later.';
                return $this->render('security/register.html.twig', ['error' => $msg]);
            }

            // persist "email" as file
            $host = $request->getSchemeAndHttpHost();
            $verifyUrl = $host . '/verify/' . $token;
            $body = "To: $email\nSubject: Confirm your account\n\nClick: $verifyUrl\n";
            @mkdir($this->getParameter('kernel.project_dir').'/var/emails', 0777, true);
            $file = $this->getParameter('kernel.project_dir').'/var/emails/'.time()."_".uniqid().'.eml';
            file_put_contents($file, $body);

            return $this->redirectToRoute('app_check_email');
        }

        return $this->render('security/register.html.twig');
    }

    /**
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail()
    {
        return $this->render('security/check_email.html.twig');
    }

    /**
     * @Route("/verify/{token}", name="app_verify")
     */
    public function verify(string $token, UserRepository $users, EntityManagerInterface $em)
    {
        $user = $users->findOneByConfirmationToken($token);
        if (!$user) {
            throw $this->createNotFoundException('Invalid token');
        }

        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $em->flush();

        return $this->render('security/verified.html.twig');
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This should never be reached.');
    }
}
