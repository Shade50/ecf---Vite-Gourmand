<?php

namespace App\Controller;

use App\Entity\Allergene;
use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Theme;
use App\Form\AllergeneType;
use App\Form\MenuType;
use App\Form\PlatType;
use App\Form\ThemeType;
use App\Service\PlatPhotoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;




final class CatalogueController extends AbstractController
{
    #[Route('admin/catalogue', name: 'app_admin_catalogue')]
    public function index(Request $request, EntityManagerInterface $entityManager, PlatPhotoUploader $photoUploader): Response
    {
        $allergene = new Allergene();
        $theme = new Theme();
        $plat = new Plat();
        $menu = new Menu();

        $allergeneForm = $this->createForm(AllergeneType::class, $allergene);
        $themeForm = $this->createForm(ThemeType::class, $theme);
        $platForm = $this->createForm(PlatType::class, $plat);
        $menuForm = $this->createForm(MenuType::class, $menu);

        $allergeneForm->handleRequest($request);
        $themeForm->handleRequest($request);
        $platForm->handleRequest($request);
        $menuForm->handleRequest($request);

        if ($allergeneForm->isSubmitted() && $allergeneForm->isValid()) {

            $entityManager->persist($allergene);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_catalogue');
        }

        if ($themeForm->isSubmitted() && $themeForm->isValid()) {

            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_catalogue');
        }

        if ($platForm->isSubmitted() && $platForm->isValid()) {

            $photoFile = $platForm->get('photoFile')->getData();

            if ($photoFile) {
                $plat->setPhoto($photoUploader->upload($photoFile));
            }

            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_catalogue');
        }

        if ($menuForm->isSubmitted() && $menuForm->isValid()) {

            $entityManager->persist($menu);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_catalogue');
        }

        return $this->render('catalogue/index.html.twig', [
            'allergeneForm' => $allergeneForm,
            'themeForm' => $themeForm,
            'platForm' => $platForm,
            'menuForm' => $menuForm,
        ]);
    }
}
