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

namespace Cog\Laravel\Love\Reactable\Observers;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;

final class ReactableObserver
{
    public function created(
        ReactableContract $reactable
    ): void {
        if ($this->shouldRegisterAsReactantOnCreate($reactable)
            && $reactable->isNotRegisteredAsLoveReactant()) {
            $reactable->registerAsLoveReactant();
        }
    }

    private function shouldRegisterAsReactantOnCreate(
        ReactableContract $reactable
    ): bool {
        return !method_exists($reactable, 'shouldRegisterAsLoveReactantOnCreate')
            || $reactable->shouldRegisterAsLoveReactantOnCreate();
    }
}
