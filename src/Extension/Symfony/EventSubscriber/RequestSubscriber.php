<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Extension\Symfony\EventSubscriber;

use Rollerworks\Bundle\DatagridBundle\Extension\Symfony\RequestUriProviderByListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestUriProviderByListener
     */
    private $uriProvider;

    /**
     * @param RequestUriProviderByListener $uriProvider
     */
    public function __construct(RequestUriProviderByListener $uriProvider)
    {
        $this->uriProvider = $uriProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    /**
     * @param KernelEvent $event
     */
    public function onRequest(KernelEvent $event)
    {
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
            $this->uriProvider->setRequest($event->getRequest());
        }
    }
}
