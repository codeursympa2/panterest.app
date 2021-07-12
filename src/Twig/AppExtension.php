<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security=$security;
     
       // dd($security->isGranted("ROLE_USER"));
    }
    
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
       // $this->security->getUser()->getFullName();
        return [
            new TwigFunction('pluralize', [$this, 'doSomething']),
        ];
    }

    public function doSomething(int $count,string $singulier,string $pluriel):String
    {
        $result=$count===1 ? $singulier: $pluriel;
       return  $result;
    }
}
