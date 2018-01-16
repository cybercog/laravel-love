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

namespace Cog\Contracts\Love\Liker\Models;

use Cog\Contracts\Love\Likeable\Models\Likeable;

/**
 * Interface Liker.
 *
 * @package Cog\Contract\Likeable\Liker\Models
 */
interface Liker
{
    /**
     * Add a like for the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function like(Likeable $likeable);

    /**
     * Add a dislike for the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function dislike(Likeable $likeable);

    /**
     * Remove a like from the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function unlike(Likeable $likeable);

    /**
     * Remove a dislike from the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function undislike(Likeable $likeable);

    /**
     * Toggle like of the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function toggleLike(Likeable $likeable);

    /**
     * Toggle dislike of the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function toggleDislike(Likeable $likeable);

    /**
     * Determine if Liker has liked Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    public function hasLiked(Likeable $likeable): bool;

    /**
     * Determine if Liker has disliked Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    public function hasDisliked(Likeable $likeable): bool;
}
