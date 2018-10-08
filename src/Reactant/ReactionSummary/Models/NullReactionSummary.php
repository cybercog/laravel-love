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

namespace Cog\Laravel\Love\Reactant\ReactionSummary\Models;

use Cog\Contracts\Love\Reactant\ReactionSummary\Models\ReactionSummary as ReactionSummaryContract;

class NullReactionSummary implements ReactionSummaryContract
{
    public function getTotalCount(): int
    {
        return 0;
    }

    public function getTotalWeight(): int
    {
        return 0;
    }
}
