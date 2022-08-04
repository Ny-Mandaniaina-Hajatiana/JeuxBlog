<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
   /* public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }*/

    
   /* private $security;

    public function __construct(Security $security)//pour acceder aux user
    {
        $this->security = $security;//si on a pas d'accès au token directement ni au fonction getUser
    }
*/
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'pluralize']),
        ];
    }
// pour la création de nouvelle fonction, ici pour savoir singulier ou pluriel
    public function pluralize(int $count, string $singular, string $plural):string
    {
            $str = $count === 1 ? $singular : $plural;
        return"$count $str";
    }
}
