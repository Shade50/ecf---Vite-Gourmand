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
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        $settings = $entityManager->getRepository(SiteSettings::class)->findOneBy([]);
        if($request->isMethod('POST')){
            $settings = $entityManager->getRepository(SiteSettings::class)->findOneBy([]);
            if (!$settings){
                $settings = new SiteSettings();
            }

            $settings->setSiteName($request->request->get('siteName'));
            $settings->setEmail($request->request->get('siteEmail'));
            $settings->setPhone($request->request->get('sitePhone'));
            $settings->setAddress($request->request->get('siteAddress'));
            $settings->setPostalCode($request->request->get('sitePostalCode'));
            $settings->setCity($request->request->get('siteCity'));
            $settings->setOpeningHoursWeekdays($request->request->get('siteHoursWeekDays'));
            $settings->setOpeningHoursSaturday($request->request->get('siteHoursSaturday'));
            $settings->setOpeningHoursSunday($request->request->get('siteHoursSunday'));

            $entityManager->persist($settings);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_settings');
        }
        return $this->render('admin/settings.html.twig',['settings' => $settings,]);

    }

}
