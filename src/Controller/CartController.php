<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Repository\CartRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser(), 'status' => 'active']);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    #[Route('/checkout', name: 'checkout')]
    public function checkout(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser(), 'status' => 'active']);

        Stripe::setApiKey('sk_test_51KG0R8B70WhTmRhmtnjAaylND1ngWwYozes0xzcDaTswo3LHbbcFzEqrzNlEiNA8uT15muemkhKGENo1SxUgIMsy00WTJxx7p8');
		
		$session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $cart->getStripeLineItems(),
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // return new JsonResponse(['id' => $session->id]);
        return $this->redirect($session->url, 303);
    }

    #[Route('/success', name: 'success')]
    public function successUrl(): Response
    {
        return $this->render('cart/success.html.twig', []);
    }


    #[Route('/cancel', name: 'cancel')]
    public function cancelUrl(): Response
    {
        return $this->render('cart/cancel.html.twig', []);
    }

}
