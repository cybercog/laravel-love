<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reacterable\Observers;

use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;

final class ReacterableObserver
{
    public function created(
        ReacterableInterface $reacterable
    ): void {
        if ($this->shouldRegisterAsReacterOnCreate($reacterable)
            && $reacterable->isNotRegisteredAsLoveReacter()) {
            $reacterable->registerAsLoveReacter();
        }
    }

    private function shouldRegisterAsReacterOnCreate(
        ReacterableInterface $reacterable
    ): bool {
        return !method_exists($reacterable, 'shouldRegisterAsLoveReacterOnCreate')
            || $reacterable->shouldRegisterAsLoveReacterOnCreate();
    }
}
