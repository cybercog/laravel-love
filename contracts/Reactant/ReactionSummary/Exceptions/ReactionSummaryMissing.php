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

namespace Cog\Contracts\Love\Reactant\ReactionSummary\Exceptions;

use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use OutOfBoundsException;

class ReactionSummaryMissing extends OutOfBoundsException
{
    public static function forReactant(Reactant $reactant): self
    {
        return new static(sprintf(
            'Reactant with ID `%s` missing ReactionSummary.',
            $reactant->getKey()
        ));
    }
}
