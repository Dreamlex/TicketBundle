<?php

namespace Dreamlex\TicketBundle\Controller;

use Dreamlex\CoreBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/media", options={"i18n": false})
 */
class MediaController extends Controller
{
    /**
     * @Route("/get/{format}/{id}", name="ticket_get_big_img")
     * @param $id
     * @param $format
     * @return Response
     */
    public function getTicketBigImage($id, $format)
    {
        $mediaManager = $this->get('sonata.media.manager.media');
        /** @var Media $media */
        $media = $mediaManager->findOneBy(['id' => $id]);
        if ($media) {
            $repository = $this->getDoctrine()->getRepository('DreamlexTicketBundle:Message');
            $message = $repository->findOneBy([
                'media' => $media->getId(),
            ]);
            if ($message->getTicket()->getUser() === $this->getUser() || $this->get('security.authorization_checker')->isGranted('ROLE_TICKET_ADMIN')) {
                $provider = $this->get('sonata.media.provider.ticket_image');
                $file = $provider->generateCustomUrl($media, $format);
                $response = new Response();
                $response->headers->set('X-Accel-Redirect', $file);
                $response->headers->set('Content-type', $media->getContentType());

                return $response->sendHeaders();
            }
        }
        throw $this->createNotFoundException();
    }

}
