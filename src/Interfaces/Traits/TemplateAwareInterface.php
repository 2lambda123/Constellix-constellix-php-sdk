<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Models\Template;

/**
 * Trait for objects that know about templates.
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read Template $template
 */
interface TemplateAwareInterface
{
    public function setTemplate(Template $template): TemplateAwareInterface;
}
