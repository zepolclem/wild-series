<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Service\Slugifer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()->getRepository(Program::class)->findAll();
        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    /**
     * The controller for the Program add form
     *
     * @Route("/new", name="new")
     */
    function new (Request $request, Slugifer $slugifer, MailerInterface $mailer): Response {
        // create the Program object
        $program = new Program();
        // create the associated form
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();

            $slug = $slugifer->generate($program->getTitle());
            $program->setSlug($slug);

            // Persist Category Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('lopez.clmnt@gmail.com')
                ->subject('Test')
                ->html('<p>Une nouvelle série vient d\'être publiée sur Wild Séries !</p>');
            $mailer->send($email);
            // Finally redirect to categories list
            return $this->redirectToRoute('category_index');
        }
        // render the form
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Getting a program
     *
     * @Route("/{slug<^[a-z]+(?:-[a-z]+)*$>}", name="show")
     * @return Response
     */
    public function show(program $program): Response
    {
        // $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['slug' => $slug]);
        if (!$program) {
            throw $this->createNotFoundException('No program with slug : ' . $program->getSlug() . ' found in program\'s table.');
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    /**
     * Show season by program id and season id
     * @Route("/{program_id}/seasons/{season_id} ", name="show_season", requirements={"program"="\d+", "season"="\d+"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_id": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_id": "id"}})
     */
    public function showSeason(program $program, season $season)
    {
        // $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['id' => $programId]);
        // $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['id' => $seasonId]);

        if (!$program) {
            throw $this->createNotFoundException('No program with id : ' . $program->getId() . ' found in program\'s table.');
        }

        if (!$season) {
            throw $this->createNotFoundException('No program with id : ' . $season->getId() . ' found in season\'s table.');
        }
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    /**
     * @Route("/programs/{program_id}/seasons/{season_id}/episodes/{episode_id}", name="show_episode")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_id": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_id": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_id": "id"}})
     */
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,

        ]);
    }
}
