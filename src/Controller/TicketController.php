<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_ticket_list')]
    public function list(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();

        $tickets = $ticketRepository->findBy(
            ['createdBy' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/new', name: 'app_ticket_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticket->setCreatedBy($this->getUser());

            $entityManager->persist($ticket);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Your ticket has been created successfully.'
            );

            return $this->redirectToRoute('app_ticket_list');
        }

        return $this->render('ticket/new.html.twig', [
            'ticketForm' => $form,
        ]);
    }

    #[Route('/ticket/{id}', name: 'app_ticket_show')]
    public function show(Ticket $ticket): Response
    {
        if ($ticket->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'You are not allowed to view this ticket.'
            );
        }

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/{id}/edit', name: 'app_ticket_edit')]
    public function edit(
        Ticket $ticket,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // Customer can edit only their own ticket
        if ($ticket->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'You are not allowed to edit this ticket.'
            );
        }

        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Ticket updated successfully.'
            );

            return $this->redirectToRoute(
                'app_ticket_show',
                ['id' => $ticket->getId()]
            );
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'ticketForm' => $form,
        ]);
    }

    #[Route('/ticket/{id}/delete', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(
        Ticket $ticket,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // User can delete only their own ticket
        if ($ticket->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'You are not allowed to delete this ticket.'
            );
        }

        // Validate CSRF token
        if ($this->isCsrfTokenValid(
            'delete' . $ticket->getId(),
            $request->request->get('_token')
        )) {

            $entityManager->remove($ticket);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Ticket deleted successfully.'
            );
        }

        return $this->redirectToRoute('app_ticket_list');
    }

}
