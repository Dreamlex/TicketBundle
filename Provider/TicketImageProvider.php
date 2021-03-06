<?php
/**
 * Created by PhpStorm.
 * User: SERGEY
 * Date: 06.04.2016
 * Time: 11:36
 */

namespace Dreamlex\Bundle\TicketBundle\Provider;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilder;

class TicketImageProvider extends ImageProvider
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param FormBuilder $formBuilder
     */
    public function buildMediaType(FormBuilder $formBuilder)
    {
        if ($formBuilder->getOption('context') !== 'api') {
            $formBuilder->add('binaryContent', FileType::class, array(
                'required' => true,
                'label' => false,
            ));
        }
    }

    /**
     * @param MediaInterface $media
     * @param                $format
     *
     * @return string
     */
    public function generateCustomUrl(MediaInterface $media, $format)
    {
        if ($format === 'reference') {
            $path = $this->getReferenceImage($media);
        } else {
            $path = $this->thumbnail->generatePublicUrl($this, $media, $format);

        }

        return '/uploads/media/'.$path;
    }

    /**
     * @param MediaInterface $media
     *
     * @return string
     * @internal param $format
     */
    public function generateProtectedUrl(MediaInterface $media)
    {
        return $this->getCdn()->getPath($media->getId(), $media->getCdnIsFlushable());
    }

    /**
     * @param MediaInterface $media
     * @param string         $format
     *
     * @return string
     * @internal param Router $router
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        return $this->router->generate('ticket_get_big_img', ['format' => $format, 'id' => $media->getId()]);
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }
}