<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;



class EventController extends AbstractController
{
    #[Route('/evenements', name: 'app_event')]
    public function index(EventRepository $repository): Response
    {
        $events = $repository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/create', name: 'app_create')]
    public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $entityManager = $doctrine->getManager();
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $event->setOwner($this->getUser());
            $event->setSlug(strtolower($slugger->slug($event->getTitle())));
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute("app_home");
        }

        return $this->render('event/create.html.twig', [
            'FormEvent' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param EventRepository $repository
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/events/{slug}', name: 'event_show')]
    public function show(Request $request, EventRepository $eventRepository, $slug,ManagerRegistry $doctrine): Response
    {
        $event = $eventRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$event)
        {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/show.html.twig', [

            'event' => $event
        ]);
    }
}
