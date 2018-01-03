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

namespace Cog\Laravel\Love\Likeable\Observers;

use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;

/**
 * Class LikeableObserver.
 *
 * @package Cog\Laravel\Love\Likeable\Observers
 */
class LikeableObserver
{
    /**
     * Handle the deleted event for the model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function deleted(LikeableContract $likeable)
    {
        if (!$this->removeLikesOnDelete($likeable)) {
            return;
        }

        $likeable->removeLikes();
    }

    /**
     * Determine if should remove likes on model delete (defaults to true).
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    private function removeLikesOnDelete(LikeableContract $likeable): bool
    {
        return $likeable->removeLikesOnDelete ?? true;
    }
}
