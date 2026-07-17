<?php

namespace App\Controller;

use App\Repository\SiteSettingsRepository;
use App\Entity\SiteSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminControler extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(SiteSettingsRepository $SiteSettingsRepository): Response
    {
        $settings = $SiteSettingsRepository->findOneBy([]);
        return $this->render('admin/index.html.twig', ['settings' =>$settings,
            'controller_name' => 'AdminControler',
        ]);
    }

    #[Route('/admin/settings', name: 'app_admin_settings')]
public function settings(
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

    $settings = $entityManager
        ->getRepository(SiteSettings::class)
        ->findOneBy([]);

    if (!$settings) {
        $settings = new SiteSettings();
    }

    if ($request->isMethod('POST')) {

        // Informations générales : administrateur uniquement
        if ($this->isGranted('ROLE_ADMIN')) {
            $settings->setSiteName(
                (string) $request->request->get('siteName')
            );

            $settings->setEmail(
                (string) $request->request->get('siteEmail')
            );

            $settings->setPhone(
                (string) $request->request->get('sitePhone')
            );

            $settings->setAddress(
                (string) $request->request->get('siteAddress')
            );

            $settings->setPostalCode(
                (string) $request->request->get('sitePostalCode')
            );

            $settings->setCity(
                (string) $request->request->get('siteCity')
            );
        }

        // Horaires : administrateur et employé
        $settings->setOpeningHoursWeekdays(
            (string) $request->request->get('siteHoursWeekDays')
        );

        $settings->setOpeningHoursSaturday(
            (string) $request->request->get('siteHoursSaturday')
        );

        $settings->setOpeningHoursSunday(
            (string) $request->request->get('siteHoursSunday')
        );

        $entityManager->persist($settings);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Les horaires ont été enregistrés.'
        );

        return $this->redirectToRoute('app_admin_settings');
    }

    return $this->render('admin/settings.html.twig', [
        'settings' => $settings,
    ]);

    
}

    // #[Route('/admin/settings', name: 'app_admin_settings')]
    // public function settings(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $settings = $entityManager->getRepository(SiteSettings::class)->findOneBy([]);
    //     if($request->isMethod('POST')){
    //         $settings = $entityManager->getRepository(SiteSettings::class)->findOneBy([]);
    //         if (!$settings){
    //             $settings = new SiteSettings();
    //         }

    //         $settings->setSiteName($request->request->get('siteName'));
    //         $settings->setEmail($request->request->get('siteEmail'));
    //         $settings->setPhone($request->request->get('sitePhone'));
    //         $settings->setAddress($request->request->get('siteAddress'));
    //         $settings->setPostalCode($request->request->get('sitePostalCode'));
    //         $settings->setCity($request->request->get('siteCity'));
    //         $settings->setOpeningHoursWeekdays($request->request->get('siteHoursWeekDays'));
    //         $settings->setOpeningHoursSaturday($request->request->get('siteHoursSaturday'));
    //         $settings->setOpeningHoursSunday($request->request->get('siteHoursSunday'));

    //         $entityManager->persist($settings);
    //         $entityManager->flush();
    //         return $this->redirectToRoute('app_admin_settings');
    //     }
    //     return $this->render('admin/settings.html.twig',['settings' => $settings,]);

    // }

}
