<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions;

use Cog\Contracts\Love\Reactant\Models\Reactant;
use OutOfBoundsException;

final class ReactionTotalMissing extends OutOfBoundsException
{
    public static function forReactant(Reactant $reactant): self
    {
        return new static(sprintf(
            'Reactant with ID `%s` missing ReactionTotal.',
            $reactant->getKey()
        ));
    }
}
