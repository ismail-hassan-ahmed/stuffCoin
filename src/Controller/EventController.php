<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Flasher\Prime\FlasherInterface;
use Flasher\SweetAlert\Prime\SweetAlertFactory;
use Knp\Component\Pager\PaginatorInterface;


class EventController extends AbstractController
{
    #[Route('/evenements', name: 'app_event')]
    public function index(EventRepository $repository, CategoryRepository $categoryRepository, Request $request): Response
    {   
        
        $events = $repository->findAll();
        $categories = $categoryRepository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/events/create', name: 'app_create')]
    public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, SweetAlertFactory $flasher): Response
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
            $flasher->addSuccess("L'événement a bien été crée avec succés!");

            return $this->redirectToRoute("app_event");
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
